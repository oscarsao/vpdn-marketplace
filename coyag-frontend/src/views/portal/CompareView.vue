<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCompareStore } from '../../stores/compare'
import { computeInvestmentScore } from '../../data/statisticsEngine'
import { useChartDefaults } from '../../composables/useChartDefaults'
import AppIcon from '../../components/ui/AppIcon.vue'

const compareStore = useCompareStore()
const router = useRouter()
const { CHART_COLORS, formatEUR, formatEURFull, baseChartConfig, gridConfig } = useChartDefaults()

const items = computed(() => compareStore.items)
const activeChartTab = ref('financial')

function formatPrice(value) {
  if (!value) return 'Consultar'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value)
}

function getSectors(sectors) {
  if (!sectors || !sectors.length) return '--'
  return sectors.map(s => s.name).join(', ')
}

function getPhoto(item) {
  return item.multimedia?.[0]?.url || item.multimedia?.[0]?.full_path || null
}

// Investment scores
function getScore(item) {
  return computeInvestmentScore(item, items.value)
}

function scoreColor(score) {
  if (score >= 80) return '#28C76F'
  if (score >= 60) return '#7367F0'
  if (score >= 40) return '#FF9F43'
  return '#EA5455'
}

// ── Financial Bar Chart ──
const barSeries = computed(() => {
  if (items.value.length === 0) return []
  return [
    { name: 'Inversión', data: items.value.map(b => b.investment || 0) },
    { name: 'Alquiler x12', data: items.value.map(b => (b.rental || 0) * 12) },
  ]
})

const barOptions = computed(() => ({
  chart: { type: 'bar', height: 280, ...baseChartConfig, toolbar: { show: false } },
  colors: [CHART_COLORS[0], CHART_COLORS[1]],
  plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
  dataLabels: { enabled: false },
  xaxis: { categories: items.value.map(b => (b.name || '').substring(0, 20)) },
  yaxis: { labels: { formatter: (v) => formatEUR(v) + ' €' } },
  grid: gridConfig,
  legend: { position: 'top' },
  tooltip: { y: { formatter: (v) => formatEURFull(v) } },
}))

// ── Radar Chart (normalized 0-100) ──
const radarCategories = ['Precio', 'Superficie', 'EUR/m2', 'Demanda', 'Inv. Score']

const radarSeries = computed(() => {
  if (items.value.length === 0) return []
  const maxInv = Math.max(...items.value.map(b => b.investment || 0)) || 1
  const maxSize = Math.max(...items.value.map(b => b.size || 0)) || 1
  const maxSqm = Math.max(...items.value.map(b => b.size > 0 ? (b.investment || 0) / b.size : 0)) || 1
  const maxViews = Math.max(...items.value.map(b => b.times_viewed || 0)) || 1

  return items.value.map((b, i) => ({
    name: (b.name || '').substring(0, 25),
    data: [
      Math.round(((b.investment || 0) / maxInv) * 100),
      Math.round(((b.size || 0) / maxSize) * 100),
      Math.round(((b.size > 0 ? (b.investment || 0) / b.size : 0) / maxSqm) * 100),
      Math.round(((b.times_viewed || 0) / maxViews) * 100),
      getScore(b),
    ],
  }))
})

const radarOptions = computed(() => ({
  chart: { type: 'radar', height: 320, ...baseChartConfig, toolbar: { show: false } },
  colors: CHART_COLORS.slice(0, items.value.length),
  xaxis: { categories: radarCategories },
  yaxis: { show: false, max: 100 },
  stroke: { width: 2 },
  fill: { opacity: 0.15 },
  markers: { size: 4 },
  legend: { position: 'bottom', fontSize: '12px' },
  dataLabels: { enabled: false },
}))

// ── Profitability Chart (ROI estimation) ──
const roiSeries = computed(() => {
  if (items.value.length === 0) return []
  return [{
    name: 'Meses break-even',
    data: items.value.map(b => {
      if (!b.rental || b.rental <= 0 || !b.investment) return 0
      const monthlyNet = b.rental * 0.6  // rough estimate: 60% net margin
      return monthlyNet > 0 ? Math.round(b.investment / monthlyNet) : 0
    }),
  }]
})

const roiOptions = computed(() => ({
  chart: { type: 'bar', height: 280, ...baseChartConfig, toolbar: { show: false } },
  colors: ['#28C76F'],
  plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
  colors: items.value.map((_, i) => CHART_COLORS[i % CHART_COLORS.length]),
  dataLabels: {
    enabled: true,
    formatter: (v) => v > 0 ? v + ' meses' : 'N/A',
    style: { fontSize: '11px', fontWeight: 700 },
  },
  xaxis: { categories: items.value.map(b => (b.name || '').substring(0, 20)) },
  yaxis: {
    title: { text: 'Meses', style: { fontSize: '12px' } },
    labels: { formatter: (v) => Math.round(v) },
  },
  grid: gridConfig,
  legend: { show: false },
  tooltip: {
    y: { formatter: (v) => v > 0 ? v + ' meses para recuperar inversion' : 'No disponible' },
  },
}))

// ── Winner Summary ──
const winnerMetrics = computed(() => {
  if (items.value.length < 2) return []
  const results = []
  const m = items.value

  // Best price (lowest)
  const bestPrice = m.reduce((a, b) => (a.investment || Infinity) < (b.investment || Infinity) ? a : b)
  results.push({ label: 'Mejor precio', winner: bestPrice.name, value: formatPrice(bestPrice.investment), icon: 'currency', color: '#28C76F' })

  // Biggest space
  const bestSize = m.reduce((a, b) => (a.size || 0) > (b.size || 0) ? a : b)
  if (bestSize.size) results.push({ label: 'Mayor superficie', winner: bestSize.name, value: bestSize.size + ' m2', icon: 'building', color: '#7367F0' })

  // Best EUR/m2
  const withSqm = m.filter(b => b.size > 0)
  if (withSqm.length >= 2) {
    const bestSqm = withSqm.reduce((a, b) => (a.investment / a.size) < (b.investment / b.size) ? a : b)
    results.push({ label: 'Mejor EUR/m2', winner: bestSqm.name, value: formatPrice(Math.round(bestSqm.investment / bestSqm.size)) + '/m2', icon: 'tag', color: '#FF9F43' })
  }

  // Most demand
  const bestDemand = m.reduce((a, b) => (a.times_viewed || 0) > (b.times_viewed || 0) ? a : b)
  if (bestDemand.times_viewed) results.push({ label: 'Mayor demanda', winner: bestDemand.name, value: bestDemand.times_viewed + ' visitas', icon: 'eye', color: '#00CFE8' })

  // Best score
  const bestScoreItem = m.reduce((a, b) => getScore(a) > getScore(b) ? a : b)
  results.push({ label: 'Mejor score', winner: bestScoreItem.name, value: getScore(bestScoreItem) + '/100', icon: 'star', color: '#A40E05' })

  return results
})

// Metrics rows for comparison table
const metrics = computed(() => [
  { label: 'Inversión', key: 'investment', format: formatPrice, highlight: 'lowest' },
  { label: 'Alquiler/mes', key: 'rental', format: (v) => v ? formatPrice(v) + '/mes' : '--', highlight: 'lowest' },
  { label: 'Superficie', key: 'size', format: (v) => v ? v + ' m2' : '--', highlight: 'highest' },
  { label: 'EUR/m2', key: null, compute: (b) => b.size > 0 ? Math.round((b.investment || 0) / b.size) : 0, format: (v) => v ? formatPrice(v) + '/m2' : '--', highlight: 'lowest' },
  { label: 'Dias en mercado', key: 'days_on_market', format: (v) => v ? v + 'd' : '--', highlight: 'lowest' },
  { label: 'Visitas', key: 'times_viewed', format: (v) => v || 0, highlight: 'highest' },
  { label: 'Score inversión', key: null, compute: (b) => getScore(b), format: (v) => v + '/100', highlight: 'highest' },
  { label: 'Tipo', key: null, compute: (b) => b.business_type?.name || 'Traspaso', format: (v) => v },
  { label: 'Ubicación', key: null, compute: (b) => [b.municipality?.name, b.province?.name].filter(Boolean).join(', ') || '--', format: (v) => v },
  { label: 'Sectores', key: null, compute: (b) => getSectors(b.sectors), format: (v) => v },
])

// Find best value for highlighting
function isBest(metric, item) {
  if (!metric.highlight || items.value.length < 2) return false
  const values = items.value.map(b => {
    if (metric.compute) return metric.compute(b)
    return b[metric.key] || 0
  }).filter(v => typeof v === 'number' && v > 0)
  if (values.length === 0) return false
  const val = metric.compute ? metric.compute(item) : (item[metric.key] || 0)
  if (typeof val !== 'number') return false
  if (metric.highlight === 'lowest') return val === Math.min(...values)
  return val === Math.max(...values)
}
</script>

<template>
  <div class="animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-4 mb-5 md:mb-8">
      <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-gray-900">Comparador de Negocios</h1>
        <p class="text-xs md:text-sm text-gray-500 mt-1">{{ items.length }} negocios seleccionados</p>
      </div>
      <div class="flex gap-3">
        <button @click="router.push('/listado-general')" class="c-btn c-btn--outline text-sm">Agregar mas</button>
        <button v-if="items.length" @click="compareStore.clear()" class="c-btn c-btn--ghost text-red-600 text-sm">Limpiar</button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="items.length === 0" class="c-card p-8 md:p-16 text-center border-2 border-dashed border-gray-200">
      <div class="mb-4 opacity-30"><AppIcon name="scale" :size="48" /></div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">El comparador esta vacio</h2>
      <p class="text-gray-500 mb-6">Agrega negocios desde el listado pulsando el icono de comparar.</p>
      <button @click="router.push('/listado-general')" class="c-btn c-btn--primary">Explorar Negocios</button>
    </div>

    <template v-else>
      <!-- Winner Summary -->
      <div v-if="winnerMetrics.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 md:gap-3 mb-4 md:mb-6">
        <div v-for="w in winnerMetrics" :key="w.label"
          class="c-card p-3 md:p-4 text-center border-l-3"
          :style="{ borderLeftColor: w.color }">
          <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto mb-2" :style="{ backgroundColor: w.color + '15' }">
            <AppIcon :name="w.icon" :size="16" :style="{ color: w.color }" />
          </div>
          <p class="text-[10px] font-bold text-gray-400 uppercase">{{ w.label }}</p>
          <p class="text-sm font-bold text-gray-900 mt-0.5 line-clamp-1">{{ w.winner }}</p>
          <p class="text-xs font-semibold mt-0.5" :style="{ color: w.color }">{{ w.value }}</p>
        </div>
      </div>

      <!-- Chart Tabs -->
      <div class="c-card mb-4 md:mb-6 overflow-hidden">
        <div class="flex border-b border-gray-100">
          <button v-for="tab in [
            { id: 'financial', label: 'Financiera', icon: 'currency' },
            { id: 'radar', label: 'Perfil', icon: 'adjustments' },
            { id: 'roi', label: 'Rentabilidad', icon: 'calculator' },
          ]" :key="tab.id"
            @click="activeChartTab = tab.id"
            :class="['flex-1 py-3 text-xs font-semibold border-b-2 bg-transparent cursor-pointer transition-colors flex items-center justify-center gap-1.5',
              activeChartTab === tab.id ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-gray-400 border-transparent hover:text-gray-600']">
            <AppIcon :name="tab.icon" :size="14" />
            {{ tab.label }}
          </button>
        </div>

        <div class="p-3 md:p-6">
          <!-- Financial Bar Chart -->
          <div v-if="activeChartTab === 'financial'">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Comparativa financiera</h3>
            <p class="text-xs text-gray-400 mb-4">Inversión total vs coste anual de alquiler</p>
            <apexchart type="bar" height="280" :options="barOptions" :series="barSeries" />
          </div>

          <!-- Radar Chart -->
          <div v-else-if="activeChartTab === 'radar'">
            <h3 class="text-sm font-bold text-gray-900 mb-1">Perfil comparativo</h3>
            <p class="text-xs text-gray-400 mb-4">Metricas normalizadas (0-100) para comparacion directa</p>
            <apexchart type="radar" height="320" :options="radarOptions" :series="radarSeries" />
          </div>

          <!-- ROI Chart -->
          <div v-else>
            <h3 class="text-sm font-bold text-gray-900 mb-1">Estimacion de rentabilidad</h3>
            <p class="text-xs text-gray-400 mb-4">Meses estimados para recuperar la inversion (margen neto 60%)</p>
            <apexchart type="bar" height="280" :options="roiOptions" :series="roiSeries" />
          </div>
        </div>
      </div>

      <!-- Comparison Table -->
      <div class="overflow-x-auto pb-6">
        <div class="min-w-[700px] bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr>
                <th class="p-5 border-b border-gray-200 bg-gray-50 w-40 sticky left-0 z-10 text-xs font-bold text-gray-400 uppercase">
                  Metrica
                </th>
                <th v-for="item in items" :key="'h-'+item.id" class="p-5 border-b border-gray-200 bg-white min-w-[250px] relative">
                  <button @click="compareStore.remove(item.id)"
                    class="absolute top-3 right-3 w-7 h-7 rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 flex items-center justify-center text-xs transition-colors">
                    <AppIcon name="x-mark" :size="14" />
                  </button>
                  <div class="aspect-video w-full rounded-lg bg-gray-100 mb-3 overflow-hidden">
                    <img v-if="getPhoto(item)" :src="getPhoto(item)" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center opacity-20"><AppIcon name="building" :size="24" /></div>
                  </div>
                  <h3 class="text-base font-bold text-gray-900 mb-1 line-clamp-2">{{ item.name }}</h3>
                  <p class="text-xs text-gray-400 mb-2">Ref: {{ item.id_code }}</p>
                  <!-- Score badge -->
                  <span class="inline-block px-2 py-0.5 rounded text-xs font-bold text-white" :style="{ backgroundColor: scoreColor(getScore(item)) }">
                    Score: {{ getScore(item) }}/100
                  </span>
                  <router-link :to="`/negocio/${item.id_code}`" class="block mt-3 c-btn c-btn--outline py-1.5 text-xs text-center no-underline">
                    Ver detalles
                  </router-link>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="m in metrics" :key="m.label" class="hover:bg-gray-50/50 transition-colors">
                <td class="p-4 pl-5 font-semibold text-gray-500 text-sm bg-gray-50/30 sticky left-0 z-10 border-r border-gray-100">
                  {{ m.label }}
                </td>
                <td v-for="item in items" :key="m.label+'-'+item.id" class="p-4 px-5">
                  <span
                    :class="['font-medium', isBest(m, item) ? 'text-green-600 font-bold' : 'text-gray-800']"
                  >
                    {{ m.format(m.compute ? m.compute(item) : (item[m.key] || 0)) }}
                    <AppIcon v-if="isBest(m, item)" name="check" :size="14" class="ml-1 inline-block" />
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
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
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>
