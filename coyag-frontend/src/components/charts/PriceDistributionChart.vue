<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  data: { type: Array, required: true }, // [{price_segment, total, avg_price}]
  mode: { type: String, default: 'investment' },
})

const { makeBarOptions } = useChartDefaults()

const categories = computed(() => props.data.map(d => d.price_segment))

const series = computed(() => [{
  name: 'Negocios',
  data: props.data.map(d => d.total),
}])

const options = computed(() => makeBarOptions({
  categories: categories.value,
  yLabel: 'Numero de negocios',
  xLabel: props.mode === 'investment' ? 'Rango de inversion (EUR)' : 'Rango de alquiler (EUR)',
  distributed: true,
  height: 320,
  formatY: (v) => Math.round(v).toString(),
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">
      Distribución de {{ mode === 'investment' ? 'precios' : 'alquileres' }}
    </h3>
    <apexchart type="bar" height="320" :options="options" :series="series" />
  </div>
</template>
