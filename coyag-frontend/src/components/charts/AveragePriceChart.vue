<script setup>
import { computed } from 'vue'
import { useChartDefaults } from '../../composables/useChartDefaults'

const props = defineProps({
  data: { type: Array, required: true }, // [{sectors_segment, avg_investment, avg_rental}]
  mode: { type: String, default: 'investment' },
  highlightSector: { type: String, default: '' },
})

const { makeBarOptions, formatEURFull } = useChartDefaults()

const categories = computed(() => props.data.map(d => d.sectors_segment))
const highlightIdx = computed(() =>
  props.highlightSector ? categories.value.indexOf(props.highlightSector) : -1
)

const series = computed(() => [{
  name: props.mode === 'investment' ? 'Precio medio' : 'Alquiler medio',
  data: props.data.map(d => props.mode === 'investment' ? d.avg_investment : d.avg_rental),
}])

const options = computed(() => makeBarOptions({
  categories: categories.value,
  yLabel: props.mode === 'investment' ? 'Precio (EUR)' : 'Alquiler (EUR/mes)',
  distributed: true,
  highlightIndex: highlightIdx.value,
  height: 320,
}))
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-3">
      {{ mode === 'investment' ? 'Precio medio por sector' : 'Alquiler medio por sector' }}
    </h3>
    <apexchart type="bar" height="320" :options="options" :series="series" />
  </div>
</template>
