<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useBusinessStore } from '../../stores/business'
import { useCompareStore } from '../../stores/compare'
import { useAiStore } from '../../stores/ai'
import { useInvestmentScore } from '../../composables/useInvestmentScore'
import { computeScatterData, computeQuartiles, computeMarketSummary, computeGroupBySectors } from '../../data/statisticsEngine'
import ROICalculator from '../../components/analytics/ROICalculator.vue'
import FinancingCalculator from '../../components/analytics/FinancingCalculator.vue'
import ScenarioSimulator from '../../components/analytics/ScenarioSimulator.vue'
import MarketKPIs from '../../components/analytics/MarketKPIs.vue'
import BusinessAiPanel from '../../components/ai/BusinessAiPanel.vue'
import BusinessMarketContext from '../../components/ai/BusinessMarketContext.vue'
import SectorComparisonChart from '../../components/analytics/SectorComparisonChart.vue'
import PricePositionGauge from '../../components/analytics/PricePositionGauge.vue'
import AppIcon from '../../components/ui/AppIcon.vue'
import PriceSqmScatterChart from '../../components/charts/PriceSqmScatterChart.vue'
import BusinessCard from '../../components/business/BusinessCard.vue'
import BusinessTimeline from '../../components/business/BusinessTimeline.vue'
import SectorRadarChart from '../../components/charts/SectorRadarChart.vue'
import ShareModal from '../../components/business/ShareModal.vue'
import ContactModal from '../../components/business/ContactModal.vue'
import BusinessReport from '../../components/business/BusinessReport.vue'
import axios from '../../api/axios'

const route = useRoute()
const businessStore = useBusinessStore()
const compareStore = useCompareStore()
const aiStore = useAiStore()
const isLoading = ref(true)

const business = computed(() => businessStore.currentBusiness)
const allBusinesses = ref([])
const similarBusinesses = ref([])
const businessTimeline = ref([])
const showShareModal = ref(false)
const showContactModal = ref(false)
const showReport = ref(false)

// Investment Score
const { score, scoreLabel, scoreBreakdown } = useInvestmentScore(business, allBusinesses)

// Market stats for KPIs
const marketSummary = computed(() => computeMarketSummary(allBusinesses.value))

// Sector radar data
const sectorData = computed(() => computeGroupBySectors(allBusinesses.value))
const businessSectors = computed(() => {
  const sectors = (business.value?.sectors || []).map(s => s.name || s)
  // Include the business's sector plus top 2 other sectors for comparison
  const topOthers = sectorData.value
    .filter(s => !sectors.includes(s.sectors_segment))
    .slice(0, 2)
    .map(s => s.sectors_segment)
  return [...sectors, ...topOthers].slice(0, 4)
})

// Scatter chart data (real, computed from all businesses)
const scatterData = computed(() => computeScatterData(allBusinesses.value, 'investment'))
const scatterQuartiles = computed(() => {
  const sqmValues = scatterData.value.map(d => d.y).sort((a, b) => a - b)
  if (sqmValues.length === 0) return {}
  const pct = (arr, p) => arr[Math.max(0, Math.ceil((p / 100) * arr.length) - 1)]
  return { median: pct(sqmValues, 50), upper_quartile: pct(sqmValues, 75), lower_quartile: pct(sqmValues, 25) }
})

// Current image index for carousel
const currentImg = ref(0)
const images = computed(() => business.value?.multimedia || [])
function nextImg() { currentImg.value = (currentImg.value + 1) % images.value.length }
function prevImg() { currentImg.value = (currentImg.value - 1 + images.value.length) % images.value.length }

// Process description: convert literal \n sequences to real newlines
const processedDescription = computed(() => {
  const raw = business.value?.description || ''
  if (!raw) return 'No hay descripción disponible.'
  return raw.replace(/\\n/g, '\n').replace(/\\r/g, '')
})

const formatMoney = (amount) => {
  if (!amount) return 'Consultar'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(amount)
}

onMounted(async () => {
  window.scrollTo(0, 0)
  const idCode = route.params.id_code_business || route.params.idCode || route.params.id
  const [bizData, allBizRes] = await Promise.all([
    businessStore.fetchBusiness(idCode),
    axios.get('/business/index', { params: { page: 1 } }),
  ])
  const rawAll = allBizRes.data?.businesses || allBizRes.data?.data || allBizRes.data
  allBusinesses.value = Array.isArray(rawAll) ? rawAll.map(b => ({
    ...b,
    id: b.id_business || b.id,
    id_code: b.id_code_business || b.id_code,
    name: b.name_business || b.name,
    investment: b.investment_business || b.investment,
    rental: b.rental_business || b.rental,
    size: b.size_business || b.size,
    days_on_market: b.days_on_market || 0,
    times_viewed: b.times_viewed_business || b.times_viewed,
    province: b.province || { name: b.name_province },
    municipality: b.municipality || { name: b.name_municipality },
    business_type: b.business_type || { name: b.name_business_type },
    sectors: b.sectors || (b.sector ? b.sector.split(', ').map(s => ({ name: s })) : []),
  })) : []
  similarBusinesses.value = bizData?.similarBusinesses || []
  businessTimeline.value = bizData?.timeline || []
  isLoading.value = false

  // Init AI context
  const bizId = business.value?.id_code || business.value?.id
  if (bizId) {
    aiStore.setBusinessContext(bizId)
    aiStore.fetchMarketContext(bizId)
    aiStore.fetchSimilarBusinesses(bizId)
  }
})
</script>

<template>
  <div v-if="isLoading" class="animate-fade-in pb-20">
    <!-- Skeleton Image Carousel -->
    <div class="h-[35vh] md:h-[50vh] w-full bg-gray-200 skeleton"></div>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      <!-- Main Column Skeleton -->
      <div class="lg:col-span-2 space-y-6">
        <div class="space-y-3">
          <div class="flex gap-2">
            <div class="h-6 w-20 bg-gray-200 rounded skeleton"></div>
            <div class="h-6 w-24 bg-gray-200 rounded skeleton"></div>
          </div>
          <div class="h-8 bg-gray-200 rounded skeleton w-3/4"></div>
          <div class="h-5 bg-gray-200 rounded skeleton w-1/2"></div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="i in 4" :key="i" class="space-y-2">
              <div class="h-3 bg-gray-200 rounded skeleton w-16"></div>
              <div class="h-6 bg-gray-200 rounded skeleton w-20"></div>
            </div>
          </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-3">
          <div class="h-6 bg-gray-200 rounded skeleton w-48"></div>
          <div class="h-4 bg-gray-200 rounded skeleton w-full"></div>
          <div class="h-4 bg-gray-200 rounded skeleton w-full"></div>
          <div class="h-4 bg-gray-200 rounded skeleton w-3/4"></div>
          <div class="h-4 bg-gray-200 rounded skeleton w-5/6"></div>
        </div>
      </div>
      <!-- Right Sidebar Skeleton -->
      <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
          <div class="h-5 bg-gray-200 rounded skeleton w-40"></div>
          <div class="h-8 bg-gray-200 rounded skeleton w-full"></div>
          <div class="h-12 bg-gray-200 rounded-xl skeleton w-full"></div>
          <div class="h-10 bg-gray-200 rounded skeleton w-full"></div>
        </div>
      </div>
    </div>
  </div>

  <div v-else-if="business" class="animate-fade-in pb-20">

    <!-- Image Carousel -->
    <section class="relative h-[35vh] md:h-[50vh] w-full bg-gray-900 overflow-hidden">
      <div v-if="images.length" class="flex h-full transition-transform duration-500" :style="{ transform: `translateX(-${currentImg * 100}%)` }">
        <div v-for="(img, i) in images" :key="i" class="w-full h-full shrink-0">
          <img :src="img.url || img.full_path" class="w-full h-full object-cover" :alt="business.name" />
        </div>
      </div>
      <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
        <AppIcon name="building" :size="64" class="opacity-30" />
      </div>

      <!-- Carousel controls -->
      <template v-if="images.length > 1">
        <button @click="prevImg" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors">&lsaquo;</button>
        <button @click="nextImg" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors">&rsaquo;</button>
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5">
          <button v-for="(_, i) in images" :key="i" @click="currentImg = i"
            :class="['w-2 h-2 rounded-full transition-colors', i === currentImg ? 'bg-white' : 'bg-white/40']" />
        </div>
      </template>

      <!-- Photo count badge -->
      <div class="absolute top-4 right-4 bg-black/60 text-white text-xs font-bold px-3 py-1.5 rounded-full">
        {{ currentImg + 1 }} / {{ images.length || 1 }}
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 mt-6 md:mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 relative items-start">

      <!-- Main Left Column -->
      <div class="lg:col-span-2 space-y-5 md:space-y-8 pb-12">

        <!-- Header Info + Score Badge -->
        <div>
          <div class="flex flex-wrap gap-2 mb-3">
            <span class="px-3 py-1 bg-red-100 text-[var(--color-primary)] font-bold text-xs uppercase rounded tracking-wider">
              {{ business.business_type?.name || 'Traspaso' }}
            </span>
            <span class="px-3 py-1 bg-gray-200 text-gray-700 font-bold text-xs uppercase rounded tracking-wider">
              Ref: {{ business.id_code || business.id }}
            </span>
            <!-- Investment Score Badge -->
            <span v-if="score > 0" class="px-3 py-1 rounded font-bold text-xs text-white" :style="{ backgroundColor: scoreLabel.color }">
              Score: {{ score }}/100 — {{ scoreLabel.text }}
            </span>
          </div>
          <h1 class="text-xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight mb-2">{{ business.name }}</h1>
          <p class="text-lg text-gray-600">{{ business.address || business.neighborhood?.name || business.municipality?.name || '' }}</p>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 bg-white p-4 md:p-6 rounded-xl md:rounded-2xl shadow-sm border border-gray-100">
          <div class="flex flex-col gap-1">
            <span class="text-xs font-bold text-gray-400 uppercase">Superficie</span>
            <span class="text-lg font-bold text-gray-900">{{ business.size || '--' }} m2</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-xs font-bold text-gray-400 uppercase">Estado</span>
            <span class="text-lg font-bold" :class="business.flag_active ? 'text-green-600' : 'text-gray-900'">
              {{ business.status_label || (business.flag_active ? 'Activo' : 'Cerrado') }}
            </span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-xs font-bold text-gray-400 uppercase">Visitas</span>
            <span class="text-lg font-bold text-gray-900">{{ business.times_viewed || 0 }}</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="text-xs font-bold text-gray-400 uppercase">Dias en mercado</span>
            <span class="text-lg font-bold text-gray-900">{{ business.days_on_market || 0 }}d</span>
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white p-4 md:p-8 rounded-2xl shadow-sm border border-gray-100">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Descripción del Negocio</h2>
          <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ processedDescription }}</p>
        </div>

        <!-- Investment Score Breakdown -->
        <div v-if="score > 0" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Índice de Inversión</h2>
          <div class="flex items-center gap-4 mb-6">
            <div class="relative w-20 h-20">
              <svg viewBox="0 0 36 36" class="w-20 h-20">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F3F4F6" stroke-width="3" />
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none" :stroke="scoreLabel.color" stroke-width="3" stroke-linecap="round"
                  :stroke-dasharray="`${score}, 100`" />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-xl font-extrabold" :style="{ color: scoreLabel.color }">{{ score }}</span>
              </div>
            </div>
            <div>
              <p class="text-lg font-bold" :style="{ color: scoreLabel.color }">{{ scoreLabel.text }}</p>
              <p class="text-sm text-gray-500">Puntuación basada en 6 factores clave del mercado</p>
            </div>
          </div>
          <div class="space-y-2">
            <div v-for="item in scoreBreakdown" :key="item.label" class="flex items-center gap-3 text-sm">
              <span class="w-32 text-gray-500 shrink-0">{{ item.label }}</span>
              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-[var(--color-primary)] rounded-full" :style="{ width: item.weight + '%' }"></div>
              </div>
              <span class="w-24 text-right text-gray-600 font-medium shrink-0">{{ item.value }}</span>
            </div>
          </div>
        </div>

        <!-- AI Panel -->
        <BusinessAiPanel :business="business" :allBusinesses="allBusinesses" />

        <!-- Sector Comparison Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <SectorComparisonChart :business="business" :marketContext="aiStore.marketContext" />
        </div>

        <!-- Price Position Gauge -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <PricePositionGauge :business="business" :marketContext="aiStore.marketContext" />
        </div>

        <!-- Scatter Chart — Real data -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
          <PriceSqmScatterChart
            :scatterData="scatterData"
            :quartiles="scatterQuartiles"
            :highlightId="business.id"
            mode="investment"
          />
        </div>

        <!-- Sector Radar Chart -->
        <div v-if="sectorData.length >= 2" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <SectorRadarChart :sectorData="sectorData" :selectedSectors="businessSectors" />
        </div>

        <!-- Business Timeline -->
        <div v-if="businessTimeline.length" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <BusinessTimeline :timeline="businessTimeline" />
        </div>

        <!-- Similar Businesses (AI-scored) -->
        <div v-if="aiStore.similarBusinesses.length || similarBusinesses.length" class="space-y-4">
          <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <AppIcon name="sparkles" :size="20" class="text-indigo-500" />
            Negocios similares
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="sim in (aiStore.similarBusinesses.length ? aiStore.similarBusinesses.slice(0, 6) : similarBusinesses.slice(0, 6))"
              :key="sim.business?.id || sim.id" class="relative">
              <!-- Match badge -->
              <div v-if="sim.matchScore" class="absolute top-3 right-3 z-10 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                {{ sim.matchScore }}% similar
              </div>
              <BusinessCard :business="sim.business || sim" />
              <!-- Reasons -->
              <div v-if="sim.reasons?.length" class="px-3 pb-2 -mt-1">
                <p class="text-[10px] text-gray-400 truncate">{{ sim.reasons.join(' · ') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Sticky Sidebar -->
      <aside class="lg:sticky lg:top-24 space-y-6">

        <!-- Financial Card -->
        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-xl border-t-4 border-[var(--color-primary)]">
          <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-3">Información Financiera</h3>
          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-600 font-semibold">Alquiler / Mes</span>
            <span class="text-xl font-extrabold text-gray-900">{{ formatMoney(business.rental) }}</span>
          </div>
          <div class="flex justify-between items-center bg-red-50 p-4 rounded-xl mb-4">
            <span class="text-[var(--color-primary)] font-bold">Inversión Total</span>
            <span class="text-2xl font-extrabold text-[var(--color-primary)]">{{ formatMoney(business.investment) }}</span>
          </div>
          <button @click="showContactModal = true" class="w-full c-btn c-btn--primary py-3 text-base shadow-lg shadow-red-500/30 mb-3">
            <AppIcon name="phone" :size="18" /> Contactar al Asesor
          </button>
          <div class="grid grid-cols-4 gap-2">
            <button @click.prevent="businessStore.toggleFavorite(business.id)" class="c-btn c-btn--outline py-2 text-xs">
              <AppIcon name="heart" :size="14" />
            </button>
            <button @click.prevent="compareStore.toggle(business)" class="c-btn c-btn--outline py-2 text-xs">
              <AppIcon name="scale" :size="14" />
            </button>
            <button @click.prevent="showShareModal = true" class="c-btn c-btn--outline py-2 text-xs">
              <AppIcon name="share" :size="14" />
            </button>
            <button @click.prevent="showReport = true" class="c-btn c-btn--outline py-2 text-xs">
              <AppIcon name="download" :size="14" />
            </button>
          </div>
        </div>

        <!-- Market KPIs -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <MarketKPIs :business="business" :marketAvg="marketSummary" />
        </div>

        <!-- AI Market Context -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <BusinessMarketContext :business="business" />
        </div>

        <!-- ROI Calculator -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <ROICalculator :business="business" />
        </div>

        <!-- Financing Calculator -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <FinancingCalculator :business="business" />
        </div>

        <!-- Scenario Simulator -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
          <ScenarioSimulator :business="business" />
        </div>

        <!-- Agency Info -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-xl font-bold text-indigo-700">
            {{ business.employee ? business.employee.name.charAt(0) : 'V' }}
          </div>
          <div>
            <span class="text-xs font-bold text-gray-400 uppercase block mb-0.5">Asesor asignado</span>
            <h4 class="text-base font-bold text-gray-900">{{ business.employee?.name || 'Agencia VPDN' }}</h4>
          </div>
        </div>
      </aside>
    </div>
  </div>

  <div v-else class="text-center py-20 text-gray-500">
    No se ha encontrado el negocio solicitado.
  </div>

  <!-- Share Modal -->
  <ShareModal v-if="business" :business="business" :show="showShareModal" @close="showShareModal = false" />
  <!-- Contact Modal -->
  <ContactModal v-if="business" :business="business" :show="showContactModal" @close="showContactModal = false" />
  <!-- Business Report -->
  <BusinessReport v-if="business" :business="business" :allBusinesses="allBusinesses" :show="showReport" @close="showReport = false" />
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
}
@keyframes skeletonPulse {
  0% { background-color: #E2E8F0; }
  50% { background-color: #F1F5F9; }
  100% { background-color: #E2E8F0; }
}
</style>
