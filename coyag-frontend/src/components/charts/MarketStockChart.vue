<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  data: { type: Array, required: true }, // [{price_segment, total}] or [{sectors_segment, total}]
  groupBy: { type: String, default: 'sector' }, // 'sector' | 'price'
})

const { makeBarOptions, CHART_COLORS } = useChartDefaults()

const labelKey = computed(() => props.groupBy === 'price' ? 'price_segment' : 'sectors_segment')
const categories = computed(() => props.data.map(d => d[labelKey.value]))

const series = computed(() => [{
  name: 'Negocios',
  data: props.data.map(d => d.total),
}])

const options = computed(() => makeBarOptions({
  categories: categories.value,
  yLabel: 'Cantidad de negocios',
  distributed: true,
  height: 320,
  formatY: (v) => Math.round(v).toString(),
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">
      Stock de mercado por {{ groupBy === 'price' ? 'rango de precio' : 'sector' }}
    </h3>
    <apexchart type="bar" height="320" :options="options" :series="series" />
  </div>
</template>
