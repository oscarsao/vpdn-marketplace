<script setup>
import { computed, onMounted } from 'vue'
import { useAiStore } from '../../stores/ai'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
})

const ai = useAiStore()

onMounted(async () => {
  if (!ai.marketContext) {
    await ai.fetchMarketContext(props.business.id_code || props.business.id)
  }
})

const ctx = computed(() => ai.marketContext)

const percentileColor = computed(() => {
  if (!ctx.value) return '#94A3B8'
  const p = ctx.value.pricePercentile
  if (p <= 25) return '#10B981'
  if (p <= 50) return '#6366F1'
  if (p <= 75) return '#F59E0B'
  return '#EF4444'
})

const demandColor = computed(() => {
  if (!ctx.value) return '#94A3B8'
  const d = ctx.value.demandIndex
  if (d >= 120) return '#10B981'
  if (d >= 80) return '#6366F1'
  return '#EF4444'
})

const healthColor = computed(() => {
  if (!ctx.value) return '#94A3B8'
  if (ctx.value.sectorHealth === 'Activo') return '#10B981'
  if (ctx.value.sectorHealth === 'Limitado') return '#EF4444'
  return '#6366F1'
})

function formatEur(n) {
  if (!n) return '0 €'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
      <AppIcon name="chart-bar" :size="16" class="text-indigo-500" />
      Contexto de Mercado
    </h3>

    <!-- Loading -->
    <div v-if="!ctx" class="py-6 text-center">
      <div class="flex justify-center gap-1 mb-2">
        <span class="w-1.5 h-1.5 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
        <span class="w-1.5 h-1.5 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
        <span class="w-1.5 h-1.5 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
      </div>
      <p class="text-[10px] text-gray-400">Calculando posición...</p>
    </div>

    <div v-else class="space-y-4">
      <!-- Price Percentile -->
      <div>
        <div class="flex justify-between items-center mb-1.5">
          <span class="text-xs text-gray-500 font-medium">Percentil de precio</span>
          <span class="text-xs font-bold" :style="{ color: percentileColor }">{{ ctx.pricePercentileLabel }}</span>
        </div>
        <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full transition-all duration-700" :style="{ width: (ctx.pricePercentile || 0) + '%', backgroundColor: percentileColor }"></div>
        </div>
        <p class="text-[10px] text-gray-400 mt-1">Más barato que el {{ isFinite(ctx.pricePercentile) ? (100 - ctx.pricePercentile) : 'N/A' }}% del sector</p>
      </div>

      <!-- EUR/m2 comparison -->
      <div>
        <div class="flex justify-between items-center mb-1.5">
          <span class="text-xs text-gray-500 font-medium">EUR/m2</span>
          <span class="text-xs font-bold" :class="(ctx.sqmDiffPct || 0) <= 0 ? 'text-green-600' : 'text-amber-600'">
            {{ isFinite(ctx.sqmDiffPct) ? ((ctx.sqmDiffPct > 0 ? '+' : '') + ctx.sqmDiffPct + '%') : 'N/A' }}
          </span>
        </div>
        <div class="flex gap-2 items-end">
          <div class="flex-1">
            <div class="h-6 bg-gray-900 rounded" :style="{ width: Math.min(100, ((ctx.myPricePerSqm || 0) / Math.max(ctx.myPricePerSqm || 1, ctx.sectorMedianSqm || 1)) * 100) + '%' }"></div>
            <span class="text-[10px] text-gray-500 mt-0.5 block">Este negocio: {{ ctx.myPricePerSqm ? formatEur(ctx.myPricePerSqm) : '0 €' }}/m2</span>
          </div>
          <div class="flex-1">
            <div class="h-6 bg-gray-300 rounded" :style="{ width: Math.min(100, ((ctx.sectorMedianSqm || 0) / Math.max(ctx.myPricePerSqm || 1, ctx.sectorMedianSqm || 1)) * 100) + '%' }"></div>
            <span class="text-[10px] text-gray-500 mt-0.5 block">Sector: {{ formatEur(ctx.sectorMedianSqm) }}/m2</span>
          </div>
        </div>
      </div>

      <!-- Demand Index -->
      <div class="flex items-center justify-between py-2 border-t border-gray-50">
        <div>
          <span class="text-xs text-gray-500 font-medium block">Índice de demanda</span>
          <span class="text-[10px] text-gray-400">{{ ctx.demandLabel }}</span>
        </div>
        <div class="text-right">
          <span class="text-lg font-bold" :style="{ color: demandColor }">{{ ctx.demandIndex || 0 }}%</span>
        </div>
      </div>

      <!-- Days on Market -->
      <div class="flex items-center justify-between py-2 border-t border-gray-50">
        <div>
          <span class="text-xs text-gray-500 font-medium block">Dias en mercado</span>
          <span class="text-[10px] text-gray-400">vs media sector: {{ ctx.avgSectorDays }}d</span>
        </div>
        <div class="text-right">
          <span class="text-lg font-bold" :class="(ctx.daysDiff || 0) <= 0 ? 'text-green-600' : 'text-amber-600'">
            {{ ctx.daysDiff != null ? ((ctx.daysDiff > 0 ? '+' : '') + ctx.daysDiff + 'd') : 'N/A' }}
          </span>
        </div>
      </div>

      <!-- Sector Health -->
      <div class="flex items-center justify-between py-2 border-t border-gray-50">
        <span class="text-xs text-gray-500 font-medium">Salud del sector</span>
        <span class="text-xs font-bold px-2 py-0.5 rounded-full" :style="{ color: healthColor, backgroundColor: healthColor + '15' }">
          {{ ctx.sectorHealth }} ({{ ctx.sectorPeerCount }} anuncios)
        </span>
      </div>
    </div>
  </div>
</template>
