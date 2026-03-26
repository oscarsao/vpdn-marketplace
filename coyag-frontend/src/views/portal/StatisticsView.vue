<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../api/axios'
import { useLocationStore } from '../../stores/location'
import {
  computeGroupBySectors,
  computeGroupByPriceSegment,
  computeQuartiles,
  computeScatterData,
  filterByGeography,
  computeMarketSummary,
} from '../../data/statisticsEngine'
import AveragePriceChart from '../../components/charts/AveragePriceChart.vue'
import AveragePriceSqmChart from '../../components/charts/AveragePriceSqmChart.vue'
import MarketStockChart from '../../components/charts/MarketStockChart.vue'
import MarketTimeChart from '../../components/charts/MarketTimeChart.vue'
import PriceDistributionChart from '../../components/charts/PriceDistributionChart.vue'
import PriceSqmScatterChart from '../../components/charts/PriceSqmScatterChart.vue'
import PriceHeatmap from '../../components/charts/PriceHeatmap.vue'
import DemandTrendsChart from '../../components/charts/DemandTrendsChart.vue'

const locationStore = useLocationStore()

const allBusinesses = ref([])
const loading = ref(true)
const mode = ref('investment') // 'investment' | 'rental'
const groupBy = ref('sector') // 'sector' | 'price'

// Geographic filters
const selectedProvince = ref('')
const selectedMunicipality = ref('')

onMounted(async () => {
  try {
    const [bizRes] = await Promise.all([
      axios.get('/business/index', { params: { page: 1 } }),
      locationStore.fetchProvinces(),
    ])
    // Get all businesses (use raw data for stats)
    allBusinesses.value = bizRes.data.data || bizRes.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

// Filtered businesses based on geography
const filteredBusinesses = computed(() => {
  return filterByGeography(allBusinesses.value, {
    province: selectedProvince.value || undefined,
    municipality: selectedMunicipality.value || undefined,
  })
})

// Available provinces/municipalities from data
const availableProvinces = computed(() => {
  const names = new Set(allBusinesses.value.map(b => b.province?.name || b.location?.province || '').filter(Boolean))
  return [...names].sort()
})
const availableMunicipalities = computed(() => {
  if (!selectedProvince.value) return []
  const names = new Set(
    allBusinesses.value
      .filter(b => (b.province?.name || b.location?.province) === selectedProvince.value)
      .map(b => b.municipality?.name || b.location?.municipality || '')
      .filter(Boolean)
  )
  return [...names].sort()
})

// Reset municipality when province changes
watch(selectedProvince, () => { selectedMunicipality.value = '' })

// Computed stats
const summary = computed(() => computeMarketSummary(filteredBusinesses.value))
const sectorData = computed(() => computeGroupBySectors(filteredBusinesses.value, mode.value))
const priceSegmentData = computed(() => computeGroupByPriceSegment(filteredBusinesses.value, mode.value))
const quartiles = computed(() => computeQuartiles(filteredBusinesses.value, mode.value))
const scatterData = computed(() => computeScatterData(filteredBusinesses.value, mode.value))

// Scatter quartiles (per sqm)
const scatterQuartiles = computed(() => {
  const sqmValues = scatterData.value.map(d => d.y).sort((a, b) => a - b)
  if (sqmValues.length === 0) return {}
  const pct = (arr, p) => arr[Math.max(0, Math.ceil((p / 100) * arr.length) - 1)]
  return {
    median: pct(sqmValues, 50),
    upper_quartile: pct(sqmValues, 75),
    lower_quartile: pct(sqmValues, 25),
  }
})

function formatCurrency(n) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="animate-fade-in">
    <div class="max-w-[1600px] mx-auto">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4 mb-5 md:mb-8">
        <div>
          <h1 class="text-xl md:text-2xl font-extrabold text-gray-900">Estadisticas del Mercado</h1>
          <p class="text-xs md:text-sm text-gray-500 mt-1">Analisis en tiempo real de {{ summary.totalBusinesses }} negocios</p>
        </div>

        <!-- Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <!-- Mode Toggle -->
          <div class="flex bg-gray-100 rounded-lg p-0.5">
            <button
              @click="mode = 'investment'"
              :class="['px-3 py-1.5 text-xs font-bold rounded-md transition-colors', mode === 'investment' ? 'bg-white shadow text-gray-900' : 'text-gray-500']"
            >Inversion</button>
            <button
              @click="mode = 'rental'"
              :class="['px-3 py-1.5 text-xs font-bold rounded-md transition-colors', mode === 'rental' ? 'bg-white shadow text-gray-900' : 'text-gray-500']"
            >Alquiler</button>
          </div>

          <!-- Province -->
          <select v-model="selectedProvince" class="c-input py-1.5 text-sm min-w-[140px]">
            <option value="">Todas las provincias</option>
            <option v-for="p in availableProvinces" :key="p" :value="p">{{ p }}</option>
          </select>

          <!-- Municipality -->
          <select v-if="availableMunicipalities.length" v-model="selectedMunicipality" class="c-input py-1.5 text-sm min-w-[140px]">
            <option value="">Todos los municipios</option>
            <option v-for="m in availableMunicipalities" :key="m" :value="m">{{ m }}</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div v-for="i in 4" :key="i" class="c-card p-5"><div class="skeleton h-16 w-full rounded"></div></div>
      </div>

      <template v-else>
        <!-- Summary KPIs -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-5 md:mb-8">
          <div class="c-card p-3 md:p-5">
            <div class="text-xs font-bold text-gray-400 uppercase">Negocios</div>
            <div class="text-lg md:text-2xl font-extrabold text-gray-900 mt-1">{{ summary.totalBusinesses }}</div>
          </div>
          <div class="c-card p-3 md:p-5">
            <div class="text-xs font-bold text-gray-400 uppercase">Precio medio</div>
            <div class="text-lg md:text-2xl font-extrabold mt-1" style="color: var(--color-primary)">{{ formatCurrency(summary.avgPrice) }}</div>
          </div>
          <div class="c-card p-3 md:p-5">
            <div class="text-xs font-bold text-gray-400 uppercase">Alquiler medio</div>
            <div class="text-lg md:text-2xl font-extrabold text-blue-600 mt-1">{{ formatCurrency(summary.avgRent) }}</div>
          </div>
          <div class="c-card p-3 md:p-5">
            <div class="text-xs font-bold text-gray-400 uppercase">EUR/m2 medio</div>
            <div class="text-lg md:text-2xl font-extrabold text-green-600 mt-1">{{ formatCurrency(summary.avgPricePerSqm) }}</div>
          </div>
          <div class="c-card p-3 md:p-5">
            <div class="text-xs font-bold text-gray-400 uppercase">Dias en mercado</div>
            <div class="text-lg md:text-2xl font-extrabold text-orange-500 mt-1">{{ summary.avgDaysOnMarket }}d</div>
          </div>
        </div>

        <!-- Charts Grid 2x3 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-5 md:mb-8">
          <!-- 1. Average Price by Sector -->
          <div class="c-card p-3 md:p-6">
            <AveragePriceChart :data="sectorData" :mode="mode" />
          </div>

          <!-- 2. EUR/m2 by Sector -->
          <div class="c-card p-3 md:p-6">
            <AveragePriceSqmChart :data="sectorData" :mode="mode" />
          </div>

          <!-- 3. Market Stock -->
          <div class="c-card p-3 md:p-6">
            <MarketStockChart :data="sectorData" groupBy="sector" />
          </div>

          <!-- 4. Market Time -->
          <div class="c-card p-3 md:p-6">
            <MarketTimeChart :data="priceSegmentData" />
          </div>

          <!-- 5. Price Distribution -->
          <div class="c-card p-3 md:p-6">
            <PriceDistributionChart :data="priceSegmentData" :mode="mode" />
          </div>

          <!-- 6. Scatter - Price vs EUR/m2 -->
          <div class="c-card p-3 md:p-6">
            <PriceSqmScatterChart :scatterData="scatterData" :quartiles="scatterQuartiles" :mode="mode" />
          </div>
        </div>

        <!-- Price Heatmap -->
        <div class="c-card p-3 md:p-6">
          <PriceHeatmap :businesses="filteredBusinesses" />
        </div>

        <!-- Demand Trends -->
        <div class="c-card p-3 md:p-6 mt-4 md:mt-6">
          <DemandTrendsChart :businesses="filteredBusinesses" />
        </div>

        <!-- Quartile Summary -->
        <div class="c-card p-4 md:p-6 mt-4 md:mt-6">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Resumen estadistico — {{ mode === 'investment' ? 'Inversion' : 'Alquiler' }}</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Minimo</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ formatCurrency(quartiles.min) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Q1 (25%)</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ formatCurrency(quartiles.lower_quartile) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Mediana</div>
              <div class="text-lg font-bold mt-1" style="color: var(--color-primary)">{{ formatCurrency(quartiles.median) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Media</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ formatCurrency(quartiles.avg) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Q3 (75%)</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ formatCurrency(quartiles.upper_quartile) }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs font-bold text-gray-400 uppercase">Maximo</div>
              <div class="text-lg font-bold text-gray-900 mt-1">{{ formatCurrency(quartiles.max) }}</div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.skeleton {
  background-color: #E2E8F0;
  animation: skeletonPulse 1.5s ease-in-out infinite;
  border-radius: 4px;
}
@keyframes skeletonPulse {
  0% { background-color: #E2E8F0; }
  50% { background-color: #F1F5F9; }
  100% { background-color: #E2E8F0; }
}
</style>
