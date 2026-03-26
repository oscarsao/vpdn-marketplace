<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  data: { type: Array, required: true }, // [{price_segment, avg_days_on_market}]
})

const { makeBarOptions } = useChartDefaults()

const categories = computed(() => props.data.map(d => d.price_segment))

const series = computed(() => [{
  name: 'Dias en mercado',
  data: props.data.map(d => d.avg_days_on_market),
}])

const options = computed(() => makeBarOptions({
  categories: categories.value,
  yLabel: 'Dias promedio',
  distributed: true,
  height: 320,
  formatY: (v) => Math.round(v) + 'd',
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">Tiempo en mercado por rango de precio</h3>
    <apexchart type="bar" height="320" :options="options" :series="series" />
  </div>
</template>
