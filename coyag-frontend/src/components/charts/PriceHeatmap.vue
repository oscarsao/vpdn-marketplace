<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  businesses: { type: Array, required: true },
})

const { baseChartConfig, formatEUR } = useChartDefaults()

// Group by province x sector
const heatmapData = computed(() => {
  const matrix = {} // province -> sector -> { total, count }
  const sectors = new Set()
  const provinces = new Set()

  props.businesses.forEach(b => {
    const prov = b.province?.name || 'Otra'
    const sector = (b.sectors || [])[0]?.name || 'Sin sector'
    provinces.add(prov)
    sectors.add(sector)
    if (!matrix[prov]) matrix[prov] = {}
    if (!matrix[prov][sector]) matrix[prov][sector] = { total: 0, count: 0 }
    matrix[prov][sector].total += b.investment || 0
    matrix[prov][sector].count++
  })

  const sectorList = [...sectors].slice(0, 8)
  const provList = [...provinces].sort()

  // ApexCharts heatmap: series = rows (provinces), data = columns (sectors)
  return {
    series: provList.map(prov => ({
      name: prov,
      data: sectorList.map(sector => ({
        x: sector.length > 15 ? sector.substring(0, 15) + '...' : sector,
        y: matrix[prov]?.[sector] ? Math.round(matrix[prov][sector].total / matrix[prov][sector].count) : 0,
      })),
    })),
    sectorList,
  }
})

const options = computed(() => ({
  chart: {
    type: 'heatmap',
    height: 350,
    ...baseChartConfig,
    toolbar: { show: false },
  },
  plotOptions: {
    heatmap: {
      radius: 4,
      colorScale: {
        ranges: [
          { from: 0, to: 0, color: '#F3F4F6', name: 'Sin datos' },
          { from: 1, to: 30000, color: '#10B981', name: '<30K €' },
          { from: 30001, to: 60000, color: '#6366F1', name: '30-60K €' },
          { from: 60001, to: 100000, color: '#F59E0B', name: '60-100K €' },
          { from: 100001, to: 200000, color: '#EF4444', name: '100-200K €' },
          { from: 200001, to: 9999999, color: '#991B1B', name: '>200K €' },
        ],
      },
    },
  },
  dataLabels: {
    enabled: true,
    formatter: (val) => val > 0 ? formatEUR(val) + ' €' : '',
    style: { fontSize: '10px', fontWeight: 600 },
  },
  xaxis: {
    labels: { style: { fontSize: '10px' }, rotate: -45, rotateAlways: true },
  },
  yaxis: {
    labels: { style: { fontSize: '11px' } },
  },
  tooltip: {
    y: {
      formatter: (val) => val > 0 ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(val) : 'Sin datos',
    },
  },
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-1">Mapa de calor: Precio medio por zona y sector</h3>
    <p class="text-xs text-gray-400 mb-4">Precio medio de inversion por provincia y tipo de negocio</p>
    <apexchart
      v-if="heatmapData.series.length"
      type="heatmap"
      :options="options"
      :series="heatmapData.series"
      height="350"
    />
    <div v-else class="h-[350px] flex items-center justify-center">
      <span class="text-xs text-gray-400">No hay datos suficientes</span>
    </div>
  </div>
</template>
