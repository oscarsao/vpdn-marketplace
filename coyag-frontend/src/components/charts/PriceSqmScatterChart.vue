<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  scatterData: { type: Array, required: true }, // [{x, y, id, name, sector, size, id_code}]
  quartiles: { type: Object, default: () => ({}) }, // {median, avg, upper_quartile, lower_quartile}
  highlightId: { type: [Number, String], default: null },
  mode: { type: String, default: 'investment' },
})

const router = useRouter()
const { makeScatterOptions, COYAG_RED, formatEURFull } = useChartDefaults()

// Group scatter data by sector for multi-series coloring
const series = computed(() => {
  const groups = {}
  props.scatterData.forEach(point => {
    const key = point.sector || 'Otros'
    if (!groups[key]) groups[key] = []
    groups[key].push(point)
  })
  return Object.entries(groups).map(([name, data]) => ({ name, data }))
})

// Annotation lines for quartiles
const annotations = computed(() => {
  const q = props.quartiles
  if (!q || !q.median) return {}

  const label = props.mode === 'investment' ? 'EUR' : 'EUR/mes'

  return {
    yaxis: [
      {
        y: q.median,
        borderColor: COYAG_RED,
        strokeDashArray: 4,
        label: {
          text: `Mediana: ${formatEURFull(q.median)}/m2`,
          style: { color: '#fff', background: COYAG_RED, fontSize: '11px', padding: { left: 6, right: 6, top: 2, bottom: 2 } },
          position: 'front',
        },
      },
      {
        y: q.upper_quartile,
        borderColor: '#9CA3AF',
        strokeDashArray: 2,
        label: {
          text: `Q3: ${formatEURFull(q.upper_quartile)}/m2`,
          style: { color: '#6B7280', background: '#F3F4F6', fontSize: '10px', padding: { left: 4, right: 4, top: 1, bottom: 1 } },
        },
      },
      {
        y: q.lower_quartile,
        borderColor: '#9CA3AF',
        strokeDashArray: 2,
        label: {
          text: `Q1: ${formatEURFull(q.lower_quartile)}/m2`,
          style: { color: '#6B7280', background: '#F3F4F6', fontSize: '10px', padding: { left: 4, right: 4, top: 1, bottom: 1 } },
        },
      },
    ],
  }
})

const options = computed(() => ({
  ...makeScatterOptions({
    xLabel: props.mode === 'investment' ? 'Precio de inversion (EUR)' : 'Alquiler mensual (EUR)',
    yLabel: 'EUR/m2',
    height: 420,
    annotations: annotations.value,
  }),
  chart: {
    ...makeScatterOptions({}).chart,
    events: {
      dataPointSelection(event, chartContext, config) {
        const point = series.value[config.seriesIndex]?.data[config.dataPointIndex]
        if (point?.id_code) {
          router.push(`/negocio/${point.id_code}`)
        }
      },
    },
  },
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">
      Precio vs EUR/m2 — Análisis de mercado
    </h3>
    <p class="text-xs text-gray-500 mb-3">
      Haz clic en un punto para ver los detalles del negocio. Las lineas indican mediana y cuartiles del mercado.
    </p>
    <apexchart type="scatter" height="420" :options="options" :series="series" />
  </div>
</template>
