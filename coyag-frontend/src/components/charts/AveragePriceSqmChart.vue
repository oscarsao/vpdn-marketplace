<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  data: { type: Array, required: true }, // [{sectors_segment, avg_sqm}]
  mode: { type: String, default: 'investment' },
})

const { makeBarOptions } = useChartDefaults()

const categories = computed(() => props.data.map(d => d.sectors_segment))

const series = computed(() => [{
  name: 'EUR/m2 medio',
  data: props.data.map(d => d.avg_sqm),
}])

const options = computed(() => makeBarOptions({
  categories: categories.value,
  yLabel: 'EUR/m2',
  distributed: true,
  height: 320,
  formatY: (v) => Math.round(v).toLocaleString('es-ES') + '/m2',
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">EUR/m2 medio por sector</h3>
    <apexchart type="bar" height="320" :options="options" :series="series" />
  </div>
</template>
