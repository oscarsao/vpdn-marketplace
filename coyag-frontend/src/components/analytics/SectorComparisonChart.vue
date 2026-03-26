<script setup>
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  business: { type: Object, required: true },
  marketContext: { type: Object, default: null },
})

const { formatEUR, baseChartConfig, gridConfig, apexLocaleES, COYAG_RED } = useChartDefaults()

const categories = ['Precio', 'EUR/m2', 'Alquiler/mes', 'Superficie']

const series = computed(() => {
  if (!props.marketContext) return []
  const ctx = props.marketContext
  const b = props.business
  return [
    {
      name: 'Este negocio',
      data: [
        b.investment || 0,
        ctx.myPricePerSqm || 0,
        b.rental || 0,
        b.size || 0,
      ],
    },
    {
      name: 'Media sector',
      data: [
        ctx.sectorAvgPrice || 0,
        ctx.sectorMedianSqm || 0,
        ctx.sectorAvgRent || 0,
        ctx.sectorAvgSize || 0,
      ],
    },
  ]
})

const options = computed(() => ({
  chart: {
    type: 'bar',
    height: 280,
    ...baseChartConfig,
    toolbar: { show: false },
    locales: [apexLocaleES],
    defaultLocale: 'es',
  },
  plotOptions: {
    bar: {
      horizontal: true,
      borderRadius: 4,
      barHeight: '65%',
    },
  },
  colors: [COYAG_RED, '#D1D5DB'],
  dataLabels: { enabled: false },
  grid: { ...gridConfig, padding: { left: 0, right: 16 } },
  xaxis: {
    categories,
    labels: { show: false },
  },
  yaxis: {
    labels: { style: { fontSize: '11px', fontWeight: 600 } },
  },
  tooltip: {
    y: {
      formatter: (val, { dataPointIndex }) => {
        if (dataPointIndex === 3) return `${Math.round(val)} m2`
        return formatEUR(val)
      },
    },
  },
  legend: {
    position: 'top',
    horizontalAlign: 'left',
    fontSize: '11px',
    markers: { size: 6, shape: 'circle' },
  },
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">Negocio vs Sector</h3>
    <VueApexCharts
      v-if="marketContext"
      type="bar"
      :options="options"
      :series="series"
      height="280"
    />
    <div v-else class="h-[280px] flex items-center justify-center">
      <span class="text-xs text-gray-400">Cargando datos del sector...</span>
    </div>
  </div>
</template>
