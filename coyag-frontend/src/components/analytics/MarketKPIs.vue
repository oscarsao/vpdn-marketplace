<script setup>
import { computed } from 'vue'

const props = defineProps({
  business: { type: Object, required: true },
  marketAvg: { type: Object, default: () => ({}) },
})

const price = computed(() => props.business.investment || props.business.financials?.price || 0)
const rent = computed(() => props.business.rental || props.business.financials?.rent || 0)
const size = computed(() => props.business.size || props.business.features?.size || 0)

// KPIs
const priceRentRatio = computed(() => {
  if (rent.value <= 0) return 'N/A'
  return (price.value / (rent.value * 12)).toFixed(1)
})

const grossYield = computed(() => {
  if (price.value <= 0) return 'N/A'
  return ((rent.value * 12 / price.value) * 100).toFixed(1)
})

const pricePerSqm = computed(() => {
  if (size.value <= 0) return 'N/A'
  return Math.round(price.value / size.value)
})

const pricePerSqmVsAvg = computed(() => {
  if (!props.marketAvg.avgPricePerSqm || size.value <= 0) return null
  const diff = ((pricePerSqm.value - props.marketAvg.avgPricePerSqm) / props.marketAvg.avgPricePerSqm * 100).toFixed(0)
  return Number(diff)
})

const views = computed(() => props.business.times_viewed || props.business.metadata?.views || 0)
const favs = computed(() => props.business.metadata?.favorites || 0)
const demandIndex = computed(() => {
  if (views.value <= 0) return 'N/A'
  return ((favs.value / views.value) * 100).toFixed(1)
})

function formatEUR(n) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="space-y-4">
    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">KPIs de mercado</h3>

    <div class="space-y-3">
      <!-- Price/Rent Ratio -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-gray-400">Ratio Precio/Alquiler</div>
          <div class="text-xs text-gray-500">Inversión / alquiler anual</div>
        </div>
        <span class="text-lg font-extrabold text-gray-900">{{ priceRentRatio }}x</span>
      </div>

      <!-- Gross Yield -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-gray-400">Rentabilidad Bruta</div>
          <div class="text-xs text-gray-500">(Alquiler anual / Precio) x 100</div>
        </div>
        <span class="text-lg font-extrabold" :class="Number(grossYield) >= 8 ? 'text-green-600' : Number(grossYield) >= 5 ? 'text-orange-500' : 'text-red-600'">
          {{ grossYield }}%
        </span>
      </div>

      <!-- EUR/m2 -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-gray-400">EUR/m2</div>
          <div v-if="pricePerSqmVsAvg !== null" class="text-xs" :class="pricePerSqmVsAvg <= 0 ? 'text-green-600' : 'text-red-500'">
            {{ pricePerSqmVsAvg > 0 ? '+' : '' }}{{ pricePerSqmVsAvg }}% vs media zona
          </div>
        </div>
        <span class="text-lg font-extrabold text-gray-900">{{ formatEUR(pricePerSqm) }}/m2</span>
      </div>

      <!-- Demand Index -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-gray-400">Indice de Demanda</div>
          <div class="text-xs text-gray-500">Ratio favoritos/visitas</div>
        </div>
        <span class="text-lg font-extrabold text-gray-900">{{ demandIndex }}%</span>
      </div>

      <!-- Views -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-gray-400">Visitas totales</div>
        </div>
        <span class="text-lg font-extrabold text-gray-900">{{ views }}</span>
      </div>
    </div>
  </div>
</template>
