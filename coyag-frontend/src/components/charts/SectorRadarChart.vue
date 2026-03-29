<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  sectorData: { type: Array, required: true }, // from computeGroupBySectors
  selectedSectors: { type: Array, default: () => [] }, // sector names to compare
})

const { CHART_COLORS } = useChartDefaults()

const categories = ['Precio medio', 'Tamano medio', 'EUR/m2', 'Num. negocios', 'Alquiler medio']

// Normalize values to 0-100 scale for radar
const series = computed(() => {
  const sectors = props.selectedSectors.length
    ? props.sectorData.filter(s => props.selectedSectors.includes(s.sectors_segment))
    : props.sectorData.slice(0, 3)

  if (sectors.length === 0) return []

  // Find max values for normalization
  const maxPrice = Math.max(...props.sectorData.map(s => s.avg_investment)) || 1
  const maxSize = Math.max(...props.sectorData.map(s => s.avg_size)) || 1
  const maxSqm = Math.max(...props.sectorData.map(s => s.avg_sqm)) || 1
  const maxTotal = Math.max(...props.sectorData.map(s => s.total)) || 1
  const maxRent = Math.max(...props.sectorData.map(s => s.avg_rental)) || 1

  return sectors.map(s => ({
    name: s.sectors_segment,
    data: [
      Math.round((s.avg_investment / maxPrice) * 100),
      Math.round((s.avg_size / maxSize) * 100),
      Math.round((s.avg_sqm / maxSqm) * 100),
      Math.round((s.total / maxTotal) * 100),
      Math.round((s.avg_rental / maxRent) * 100),
    ],
  }))
})

const options = computed(() => ({
  chart: {
    type: 'radar',
    height: 350,
    fontFamily: "'Inter', sans-serif",
    toolbar: { show: false },
  },
  colors: CHART_COLORS,
  xaxis: { categories },
  yaxis: { show: false },
  stroke: { width: 2 },
  fill: { opacity: 0.15 },
  markers: { size: 4 },
  legend: { position: 'bottom', fontSize: '12px' },
  dataLabels: { enabled: false },
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">Comparación de sectores</h3>
    <apexchart v-if="series.length" type="radar" height="350" :options="options" :series="series" />
    <p v-else class="text-sm text-gray-400 text-center py-8">Selecciona sectores para comparar</p>
  </div>
</template>
