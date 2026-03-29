<script setup>
import { computed } from 'vue'

const props = defineProps({
  business: { type: Object, required: true },
  marketContext: { type: Object, default: null },
})

function formatEur(n) {
  if (!n) return '0 €'
  if (n >= 1000) return Math.round(n / 1000) + 'K €'
  return n + ' €'
}

const price = computed(() => props.business.investment || 0)

const range = computed(() => {
  if (!props.marketContext) return { min: 0, max: 0 }
  return props.marketContext.sectorPriceRange || { min: 0, max: 0 }
})

const position = computed(() => {
  if (!range.value.max || !range.value.min || range.value.max === range.value.min) return 50
  const pct = ((price.value - range.value.min) / (range.value.max - range.value.min)) * 100
  return Math.max(2, Math.min(98, pct))
})

const percentile = computed(() => props.marketContext?.pricePercentile || 50)

const indicatorColor = computed(() => {
  if (percentile.value <= 25) return '#10B981'
  if (percentile.value <= 50) return '#6366F1'
  if (percentile.value <= 75) return '#F59E0B'
  return '#EF4444'
})
</script>

<template>
  <div v-if="marketContext">
    <h3 class="text-sm font-bold text-gray-900 mb-3">Posición de Precio en el Sector</h3>

    <!-- Gauge bar -->
    <div class="relative mt-6 mb-2">
      <!-- Background gradient bar -->
      <div class="h-3 rounded-full w-full" style="background: linear-gradient(to right, #10B981, #6366F1, #F59E0B, #EF4444)"></div>

      <!-- Position indicator -->
      <div class="absolute -top-5 transition-all duration-700" :style="{ left: position + '%', transform: 'translateX(-50%)' }">
        <div class="flex flex-col items-center">
          <span class="text-[10px] font-bold whitespace-nowrap" :style="{ color: indicatorColor }">
            {{ formatEur(price) }}
          </span>
          <div class="w-0 h-0 border-l-[5px] border-r-[5px] border-t-[6px] border-transparent" :style="{ borderTopColor: indicatorColor }"></div>
        </div>
      </div>

      <!-- Quartile markers -->
      <div class="absolute top-0 left-1/4 w-px h-3 bg-white/60"></div>
      <div class="absolute top-0 left-1/2 w-px h-3 bg-white/80"></div>
      <div class="absolute top-0 left-3/4 w-px h-3 bg-white/60"></div>
    </div>

    <!-- Labels -->
    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
      <span>{{ formatEur(range.min) }}</span>
      <span>Mediana</span>
      <span>{{ formatEur(range.max) }}</span>
    </div>

    <!-- Percentile label -->
    <div class="text-center mt-3">
      <span class="text-xs font-bold px-3 py-1 rounded-full" :style="{ color: indicatorColor, backgroundColor: indicatorColor + '15' }">
        Percentil {{ percentile }}
      </span>
    </div>
  </div>
</template>
