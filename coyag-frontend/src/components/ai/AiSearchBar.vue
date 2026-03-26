<script setup>
import { ref, computed } from 'vue'
import { parseNaturalLanguageQuery } from '../../data/aiEngine'
import AppIcon from '../ui/AppIcon.vue'

const emit = defineEmits(['search', 'filters-applied'])

const query = ref('')
const parsedFilters = ref(null)
const isAiMode = ref(false)
const showHistory = ref(false)

// Search history (localStorage)
const MAX_HISTORY = 10
function getHistory() {
  try { return JSON.parse(localStorage.getItem('coyag_search_history') || '[]') }
  catch { return [] }
}
function saveToHistory(text) {
  if (!text.trim()) return
  let history = getHistory()
  history = history.filter(h => h.query !== text.trim())
  history.unshift({ query: text.trim(), date: new Date().toISOString() })
  if (history.length > MAX_HISTORY) history = history.slice(0, MAX_HISTORY)
  localStorage.setItem('coyag_search_history', JSON.stringify(history))
}
function removeFromHistory(idx) {
  const history = getHistory()
  history.splice(idx, 1)
  localStorage.setItem('coyag_search_history', JSON.stringify(history))
}
function clearHistory() {
  localStorage.removeItem('coyag_search_history')
  showHistory.value = false
}
const searchHistory = computed(() => getHistory())

function applyHistoryItem(item) {
  query.value = item.query
  showHistory.value = false
  handleSubmit()
}

const filterChips = computed(() => {
  if (!parsedFilters.value) return []
  const f = parsedFilters.value
  const chips = []
  f.sectors.forEach(s => chips.push({ type: 'sector', label: s, key: 'sector-' + s }))
  if (f.province) chips.push({ type: 'location', label: f.province, key: 'prov' })
  if (f.district) chips.push({ type: 'location', label: f.district, key: 'dist' })
  if (f.maxPrice) chips.push({ type: 'price', label: `Hasta ${Math.round(f.maxPrice / 1000)}K €`, key: 'maxp' })
  if (f.minPrice) chips.push({ type: 'price', label: `Desde ${Math.round(f.minPrice / 1000)}K €`, key: 'minp' })
  if (f.features.terrace) chips.push({ type: 'feature', label: 'Terraza', key: 'terrace' })
  if (f.features.smokeOutlet) chips.push({ type: 'feature', label: 'Salida de humos', key: 'smoke' })
  if (f.type) chips.push({ type: 'type', label: f.type.charAt(0).toUpperCase() + f.type.slice(1), key: 'type' })
  return chips
})

function handleSubmit() {
  if (!query.value.trim()) return
  saveToHistory(query.value)
  showHistory.value = false
  const parsed = parseNaturalLanguageQuery(query.value)
  const hasFilters = parsed.sectors.length > 0 || parsed.province || parsed.maxPrice || parsed.minPrice || parsed.features.terrace || parsed.features.smokeOutlet

  if (hasFilters) {
    parsedFilters.value = parsed
    isAiMode.value = true
    emit('filters-applied', parsed)
  } else {
    parsedFilters.value = null
    isAiMode.value = false
    emit('search', query.value.trim())
  }
}

function removeChip(chip) {
  if (!parsedFilters.value) return
  const f = { ...parsedFilters.value }
  if (chip.type === 'sector') f.sectors = f.sectors.filter(s => s !== chip.label)
  if (chip.key === 'prov') f.province = ''
  if (chip.key === 'dist') f.district = ''
  if (chip.key === 'maxp') f.maxPrice = null
  if (chip.key === 'minp') f.minPrice = null
  if (chip.key === 'terrace') f.features = { ...f.features, terrace: false }
  if (chip.key === 'smoke') f.features = { ...f.features, smokeOutlet: false }
  if (chip.key === 'type') f.type = ''
  parsedFilters.value = f

  const stillHasFilters = f.sectors.length > 0 || f.province || f.maxPrice || f.minPrice || f.features.terrace || f.features.smokeOutlet
  if (!stillHasFilters) {
    isAiMode.value = false
    parsedFilters.value = null
    emit('search', query.value.trim())
  } else {
    emit('filters-applied', f)
  }
}

function clearAll() {
  query.value = ''
  parsedFilters.value = null
  isAiMode.value = false
  emit('search', '')
}
</script>

<template>
  <div class="flex-1 w-full max-w-2xl relative">
    <div class="relative">
      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400">
        <AppIcon name="sparkles" :size="16" />
      </span>
      <input
        v-model="query"
        @keyup.enter="handleSubmit"
        @focus="showHistory = true"
        @blur="setTimeout(() => showHistory = false, 200)"
        type="text"
        placeholder="Busca con IA: restaurante en Madrid menos de 100k con terraza"
        class="w-full bg-gray-50 border-none rounded-lg pl-10 pr-20 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-400 outline-none"
      />
      <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
        <span v-if="isAiMode" class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded">IA</span>
        <button v-if="query" @click="clearAll" class="p-1 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer">
          <AppIcon name="x-mark" :size="14" />
        </button>
      </div>
    </div>

    <!-- Search History Dropdown -->
    <div v-if="showHistory && searchHistory.length && !query"
      class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden">
      <div class="flex items-center justify-between px-3 py-2 border-b border-gray-50">
        <span class="text-[10px] font-bold text-gray-400 uppercase">Busquedas recientes</span>
        <button @mousedown.prevent="clearHistory" class="text-[10px] text-red-400 hover:text-red-600 bg-transparent border-none cursor-pointer">Limpiar</button>
      </div>
      <div v-for="(item, idx) in searchHistory" :key="idx"
        class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer group transition-colors"
        @mousedown.prevent="applyHistoryItem(item)">
        <AppIcon name="clock" :size="12" class="text-gray-300 shrink-0" />
        <span class="text-sm text-gray-700 flex-1 truncate">{{ item.query }}</span>
        <button @mousedown.prevent.stop="removeFromHistory(idx)" class="p-0.5 text-gray-300 hover:text-red-500 bg-transparent border-none cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity">
          <AppIcon name="x-mark" :size="10" />
        </button>
      </div>
    </div>

    <!-- Filter Chips -->
    <div v-if="filterChips.length" class="flex flex-wrap gap-1.5 mt-2">
      <span
        v-for="chip in filterChips"
        :key="chip.key"
        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-md cursor-pointer transition-colors"
        :class="{
          'bg-indigo-50 text-indigo-700': chip.type === 'sector',
          'bg-green-50 text-green-700': chip.type === 'location',
          'bg-amber-50 text-amber-700': chip.type === 'price',
          'bg-purple-50 text-purple-700': chip.type === 'feature',
          'bg-gray-100 text-gray-700': chip.type === 'type',
        }"
        @click="removeChip(chip)"
      >
        {{ chip.label }}
        <AppIcon name="x-mark" :size="10" />
      </span>
    </div>
  </div>
</template>
