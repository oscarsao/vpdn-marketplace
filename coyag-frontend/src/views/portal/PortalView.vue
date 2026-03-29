<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBusinessStore } from '../../stores/business'
import { useLocationStore } from '../../stores/location'
import { useAiStore } from '../../stores/ai'
import BusinessCard from '../../components/business/BusinessCard.vue'
import AiSearchBar from '../../components/ai/AiSearchBar.vue'
import axios from '../../api/axios'
import AppIcon from '../../components/ui/AppIcon.vue'
import SavedAlerts from '../../components/business/SavedAlerts.vue'
import { useAlertStore } from '../../stores/alerts'
import { useSearchStore } from '../../stores/searches'

const route = useRoute()
const router = useRouter()
const businessStore = useBusinessStore()
const locationStore = useLocationStore()
const aiStore = useAiStore()

// Collapsible filters: open on desktop, closed on mobile
const isDesktop = ref(window.innerWidth >= 1024)
const filtersOpen = ref(isDesktop.value)
function onResize() {
  isDesktop.value = window.innerWidth >= 1024
  if (isDesktop.value) filtersOpen.value = true
}
onMounted(() => window.addEventListener('resize', onResize))
onBeforeUnmount(() => window.removeEventListener('resize', onResize))

// AI search results mode
const aiSearchResults = ref([])
const isAiSearchMode = ref(false)

// Local filter state
const searchQuery = ref('')
const sortOrder = ref('most_relevant')
const selectedProvince = ref(null)
const selectedMunicipality = ref(null)
const selectedDistrict = ref(null)
const selectedNeighborhood = ref(null)
const minInvestment = ref(null)
const maxInvestment = ref(null)
const minRental = ref(null)
const maxRental = ref(null)
const smokeOutlet = ref(false)
const terrace = ref(false)
const outstanding = ref(false)
const selectedSectors = ref([])
const sectors = ref([])

// Active filter count for badge
const activeFilterCount = computed(() => {
  let count = 0
  if (selectedProvince.value) count++
  if (selectedMunicipality.value) count++
  if (selectedDistrict.value) count++
  if (selectedNeighborhood.value) count++
  if (minInvestment.value) count++
  if (maxInvestment.value) count++
  if (minRental.value) count++
  if (maxRental.value) count++
  if (smokeOutlet.value) count++
  if (terrace.value) count++
  if (outstanding.value) count++
  if (selectedSectors.value.length) count++
  return count
})

// Debounce timer
let searchTimer = null

const searchStore = useSearchStore()

// Load sectors and provinces on mount
onMounted(async () => {
  // Set condition from route meta (franquicias, inmuebles, general)
  const condition = route.meta.condition || 'general'
  businessStore.setFilter('condition', condition)

  // Load supporting data
  const [sectorsRes] = await Promise.all([
    axios.get('/sector'),
    locationStore.fetchProvinces(),
  ])
  sectors.value = sectorsRes.data

  // Apply pending saved search filters if any
  if (searchStore.pendingFilters) {
    const pf = searchStore.pendingFilters
    if (pf.condition) businessStore.setFilter('condition', pf.condition)
    if (pf.province_id) {
      selectedProvince.value = pf.province_id
      businessStore.setFilter('province_id', pf.province_id)
      locationStore.fetchMunicipalities(pf.province_id)
    }
    if (pf.municipality_id) {
      selectedMunicipality.value = pf.municipality_id
      businessStore.setFilter('municipality_id', pf.municipality_id)
    }
    if (pf.district_id) {
      selectedDistrict.value = pf.district_id
      businessStore.setFilter('district_id', pf.district_id)
    }
    if (pf.neighborhood_id) {
      selectedNeighborhood.value = pf.neighborhood_id
      businessStore.setFilter('neighborhood_id', pf.neighborhood_id)
    }
    if (pf.min_investment) {
      minInvestment.value = pf.min_investment
      businessStore.setFilter('min_investment', pf.min_investment)
    }
    if (pf.max_investment && pf.max_investment < 2000000) {
      maxInvestment.value = pf.max_investment
      businessStore.setFilter('max_investment', pf.max_investment)
    }
    if (pf.min_rental) {
      minRental.value = pf.min_rental
      businessStore.setFilter('min_rental', pf.min_rental)
    }
    if (pf.max_rental && pf.max_rental < 10000) {
      maxRental.value = pf.max_rental
      businessStore.setFilter('max_rental', pf.max_rental)
    }
    if (pf.smoke_outlet) {
      smokeOutlet.value = true
      businessStore.setFilter('smoke_outlet', '1')
    }
    if (pf.terrace) {
      terrace.value = true
      businessStore.setFilter('terrace', '1')
    }
    if (pf.sectors && pf.sectors.length) {
      selectedSectors.value = [...pf.sectors]
      businessStore.setFilter('sectors', pf.sectors.join(','))
    }
    searchStore.clearPending()
  }

  // Read global search query from URL (?q=...)
  if (route.query.q) {
    searchQuery.value = route.query.q
    businessStore.setFilter('name', route.query.q)
  }

  // Initial load
  businessStore.fetchBusinesses()
})

// Watch route changes for condition switching
watch(() => route.meta.condition, (newCondition) => {
  businessStore.setFilter('condition', newCondition || 'general')
  businessStore.fetchBusinesses()
})

// Debounced search
watch(searchQuery, (val) => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    businessStore.setFilter('name', val)
    businessStore.fetchBusinesses()
  }, 400)
})

// Sort handler
function handleSort(e) {
  sortOrder.value = e.target.value
  businessStore.setFilter('order_by', e.target.value)
  businessStore.fetchBusinesses()
}

// Province change → load municipalities
async function handleProvinceChange(e) {
  const provId = Number(e.target.value) || null
  selectedProvince.value = provId
  selectedMunicipality.value = null
  selectedDistrict.value = null
  selectedNeighborhood.value = null
  businessStore.setFilter('province_id', provId)
  businessStore.setFilter('municipality_id', null)
  businessStore.setFilter('district_id', null)
  businessStore.setFilter('neighborhood_id', null)
  if (provId) {
    await locationStore.fetchMunicipalities(provId)
  } else {
    locationStore.municipalities = []
  }
  locationStore.districts = []
  locationStore.neighborhoods = []
  businessStore.fetchBusinesses()
}

// Municipality change
function handleMunicipalityChange(e) {
  const munId = Number(e.target.value) || null
  selectedMunicipality.value = munId
  selectedDistrict.value = null
  selectedNeighborhood.value = null
  businessStore.setFilter('municipality_id', munId)
  businessStore.setFilter('district_id', null)
  businessStore.setFilter('neighborhood_id', null)
  if (munId) {
    locationStore.fetchDistricts(munId)
  } else {
    locationStore.districts = []
    locationStore.neighborhoods = []
  }
  businessStore.fetchBusinesses()
}

// District change
function handleDistrictChange(e) {
  const distId = Number(e.target.value) || null
  selectedDistrict.value = distId
  selectedNeighborhood.value = null
  businessStore.setFilter('district_id', distId)
  businessStore.setFilter('neighborhood_id', null)
  if (distId) {
    locationStore.fetchNeighborhoods(distId)
  } else {
    locationStore.neighborhoods = []
  }
  businessStore.fetchBusinesses()
}

// Neighborhood change
function handleNeighborhoodChange(e) {
  const nbId = Number(e.target.value) || null
  selectedNeighborhood.value = nbId
  businessStore.setFilter('neighborhood_id', nbId)
  businessStore.fetchBusinesses()
}

// Price/Rent filters — apply on blur or Enter
function applyPriceFilter() {
  businessStore.setFilter('min_investment', minInvestment.value || null)
  businessStore.setFilter('max_investment', maxInvestment.value || null)
  businessStore.setFilter('min_rental', minRental.value || null)
  businessStore.setFilter('max_rental', maxRental.value || null)
  businessStore.fetchBusinesses()
}

// Checkbox filters
function handleCheckbox(type) {
  if (type === 'smoke') {
    businessStore.setFilter('smoke_outlet', smokeOutlet.value ? '1' : null)
  } else if (type === 'terrace') {
    businessStore.setFilter('terrace', terrace.value ? '1' : null)
  } else if (type === 'outstanding') {
    businessStore.setFilter('flag_outstanding', outstanding.value ? '1' : null)
  }
  businessStore.fetchBusinesses()
}

// Sector toggle
function toggleSector(sectorId) {
  const idx = selectedSectors.value.indexOf(sectorId)
  if (idx > -1) selectedSectors.value.splice(idx, 1)
  else selectedSectors.value.push(sectorId)
  businessStore.setFilter('sectors', selectedSectors.value.length ? selectedSectors.value.join(',') : null)
  businessStore.fetchBusinesses()
}

// AI Search handlers
function handleAiSearch(text) {
  isAiSearchMode.value = false
  aiSearchResults.value = []
  searchQuery.value = text
  businessStore.setFilter('name', text)
  businessStore.fetchBusinesses()
}

async function handleAiFilters(parsed) {
  isAiSearchMode.value = true
  // Reconstruct a text query from parsed filters for the API
  const parts = []
  parsed.sectors.forEach(s => parts.push(s))
  if (parsed.province) parts.push(parsed.province)
  if (parsed.maxPrice) parts.push(`menos de ${Math.round(parsed.maxPrice / 1000)}k`)
  if (parsed.minPrice) parts.push(`mas de ${Math.round(parsed.minPrice / 1000)}k`)
  if (parsed.features.terrace) parts.push('terraza')
  if (parsed.features.smokeOutlet) parts.push('salida de humos')
  if (parsed.type) parts.push(parsed.type)
  const results = await aiStore.searchWithAI(parts.join(' '))
  aiSearchResults.value = results.results || results || []
}

// Clear all filters
function clearFilters() {
  searchQuery.value = ''
  sortOrder.value = 'most_relevant'
  isAiSearchMode.value = false
  aiSearchResults.value = []
  selectedProvince.value = null
  selectedMunicipality.value = null
  selectedDistrict.value = null
  selectedNeighborhood.value = null
  minInvestment.value = null
  maxInvestment.value = null
  minRental.value = null
  maxRental.value = null
  smokeOutlet.value = false
  terrace.value = false
  outstanding.value = false
  selectedSectors.value = []
  locationStore.municipalities = []
  locationStore.districts = []
  locationStore.neighborhoods = []
  businessStore.resetFilters()
  businessStore.setFilter('condition', route.meta.condition || 'general')
  businessStore.fetchBusinesses()
}

// Alerts
const alertStore = useAlertStore()
const currentFiltersSnapshot = computed(() => ({
  name: searchQuery.value || null,
  province_id: selectedProvince.value,
  municipality_id: selectedMunicipality.value,
  district_id: selectedDistrict.value,
  neighborhood_id: selectedNeighborhood.value,
  min_investment: minInvestment.value,
  max_investment: maxInvestment.value,
  min_rental: minRental.value,
  max_rental: maxRental.value,
  smoke_outlet: smokeOutlet.value ? '1' : null,
  terrace: terrace.value ? '1' : null,
  flag_outstanding: outstanding.value ? '1' : null,
  sectors: selectedSectors.value.length ? selectedSectors.value.join(',') : null,
}))

function applyAlertFilters(filters) {
  searchQuery.value = filters.name || ''
  selectedProvince.value = filters.province_id || null
  selectedMunicipality.value = filters.municipality_id || null
  minInvestment.value = filters.min_investment || null
  maxInvestment.value = filters.max_investment || null
  minRental.value = filters.min_rental || null
  maxRental.value = filters.max_rental || null
  smokeOutlet.value = filters.smoke_outlet === '1'
  terrace.value = filters.terrace === '1'
  outstanding.value = filters.flag_outstanding === '1'
  selectedSectors.value = filters.sectors ? filters.sectors.split(',').map(Number) : []

  // Apply to store
  Object.entries(filters).forEach(([key, val]) => {
    businessStore.setFilter(key, val)
  })
  if (filters.province_id) locationStore.fetchMunicipalities(filters.province_id)
  businessStore.fetchBusinesses()
}

// Pagination
const totalPages = computed(() => Math.ceil(businessStore.totalResults / 12) || 1)
const currentPage = computed(() => businessStore.filters.page)
const resultsCount = computed(() => businessStore.totalResults)

function goToPage(page) {
  if (page < 1 || page > totalPages.value) return
  businessStore.setFilter('page', page)
  businessStore.fetchBusinesses()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const curr = currentPage.value
  const maxVisible = 5
  let start = Math.max(1, curr - Math.floor(maxVisible / 2))
  let end = Math.min(total, start + maxVisible - 1)
  if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

// Page title from route
const pageTitle = computed(() => route.meta.pageTitle || 'Portal de Negocios')

// Watch for ?q= changes in the route
watch(() => route.query.q, (newQ) => {
  if (newQ && newQ !== searchQuery.value) {
    searchQuery.value = newQ
    businessStore.setFilter('name', newQ)
    businessStore.fetchBusinesses()
  }
})
</script>

<template>
  <div class="animate-fade-in">
    <div class="max-w-[1600px] mx-auto px-4 md:px-6 lg:px-8 pb-12">

      <!-- Top Actions Bar -->
      <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 mb-4 md:mb-6 flex flex-col md:flex-row gap-3 md:gap-4 items-center justify-between">
        <AiSearchBar @search="handleAiSearch" @filters-applied="handleAiFilters" />

        <div class="flex items-center gap-4 w-full md:w-auto">
          <SavedAlerts
            :current-filters="currentFiltersSnapshot"
            :sectors="sectors"
            :provinces="locationStore.provinces"
            @apply-alert="applyAlertFilters"
          />
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-500 whitespace-nowrap">Ordenar por:</span>
            <select :value="sortOrder" @change="handleSort" class="c-input py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg">
              <option value="most_relevant">Mas Relevantes</option>
              <option value="price_asc">Precio: Menor a Mayor</option>
              <option value="price_desc">Precio: Mayor a Menor</option>
              <option value="newest">Mas Recientes</option>
              <option value="most_viewed">Mas Vistos</option>
            </select>
          </div>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

        <!-- Filter Toggle Button (always visible) -->
        <button
          @click="filtersOpen = !filtersOpen"
          class="w-full lg:hidden flex items-center justify-center gap-2 bg-white rounded-xl shadow-sm border border-gray-100 p-3 font-bold text-gray-700 hover:bg-gray-50 transition-colors"
        >
          <AppIcon name="filter" :size="18" />
          Filtros
          <svg :class="['w-4 h-4 transition-transform duration-200', filtersOpen ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
          <span v-if="activeFilterCount > 0" class="text-xs bg-[var(--color-primary)] text-white rounded-full w-5 h-5 flex items-center justify-center ml-1">{{ activeFilterCount }}</span>
        </button>

        <!-- Mobile Filter Overlay Backdrop -->
        <Transition name="fade">
          <div
            v-if="filtersOpen && !isDesktop"
            class="fixed inset-0 z-40 bg-black/50"
            @click="filtersOpen = false"
          ></div>
        </Transition>

        <!-- Left Sidebar - Filters (collapsible) -->
        <aside
          class="w-full lg:w-72 shrink-0 space-y-4 lg:sticky lg:top-24 transition-all duration-300"
          :class="{
            'fixed inset-x-0 top-0 bottom-0 z-50 overflow-y-auto bg-gray-50 p-4': filtersOpen && !isDesktop,
            'hidden': !filtersOpen && !isDesktop,
            'lg:block': true
          }"
          v-show="filtersOpen || isDesktop"
        >
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-6">
              <h2 class="font-extrabold text-gray-900 flex items-center gap-2">
                <AppIcon name="filter" :size="18" /> Filtros
                <span v-if="activeFilterCount > 0" class="text-xs bg-[var(--color-primary)] text-white rounded-full w-5 h-5 flex items-center justify-center">{{ activeFilterCount }}</span>
              </h2>
              <div class="flex items-center gap-2">
                <button @click="clearFilters" class="text-xs font-bold text-red-600 hover:text-red-800 bg-red-50 px-2 py-1 rounded">Limpiar</button>
                <button @click="filtersOpen = false" class="lg:hidden text-xs font-bold text-gray-500 hover:text-gray-800 bg-gray-100 px-2 py-1 rounded">Cerrar</button>
              </div>
            </div>

            <div class="space-y-6">
              <!-- Province -->
              <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Provincia</label>
                <select :value="selectedProvince || ''" @change="handleProvinceChange" class="w-full c-input text-sm">
                  <option value="">Todas</option>
                  <option v-for="p in locationStore.provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </div>

              <!-- Municipality -->
              <div v-if="locationStore.municipalities.length">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Municipio</label>
                <select :value="selectedMunicipality || ''" @change="handleMunicipalityChange" class="w-full c-input text-sm">
                  <option value="">Todos</option>
                  <option v-for="m in locationStore.municipalities" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
              </div>

              <!-- District -->
              <div v-if="locationStore.districts.length">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Distrito</label>
                <select :value="selectedDistrict || ''" @change="handleDistrictChange" class="w-full c-input text-sm">
                  <option value="">Todos</option>
                  <option v-for="d in locationStore.districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>

              <!-- Neighborhood -->
              <div v-if="locationStore.neighborhoods.length">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Barrio</label>
                <select :value="selectedNeighborhood || ''" @change="handleNeighborhoodChange" class="w-full c-input text-sm">
                  <option value="">Todos</option>
                  <option v-for="n in locationStore.neighborhoods" :key="n.id" :value="n.id">{{ n.name }}</option>
                </select>
              </div>

              <!-- Price -->
              <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Inversión (Precio)</label>
                <div class="flex items-center gap-2">
                  <input v-model.number="minInvestment" @blur="applyPriceFilter" @keyup.enter="applyPriceFilter" type="number" placeholder="Min" class="w-full c-input px-3 py-2 text-sm" />
                  <span class="text-gray-300">-</span>
                  <input v-model.number="maxInvestment" @blur="applyPriceFilter" @keyup.enter="applyPriceFilter" type="number" placeholder="Max" class="w-full c-input px-3 py-2 text-sm" />
                </div>
              </div>

              <!-- Rent -->
              <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alquiler Mensual</label>
                <div class="flex items-center gap-2">
                  <input v-model.number="minRental" @blur="applyPriceFilter" @keyup.enter="applyPriceFilter" type="number" placeholder="Min" class="w-full c-input px-3 py-2 text-sm" />
                  <span class="text-gray-300">-</span>
                  <input v-model.number="maxRental" @blur="applyPriceFilter" @keyup.enter="applyPriceFilter" type="number" placeholder="Max" class="w-full c-input px-3 py-2 text-sm" />
                </div>
              </div>

              <hr class="border-gray-100" />

              <!-- Characteristics -->
              <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Características</label>
                <div class="space-y-2">
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input v-model="smokeOutlet" @change="handleCheckbox('smoke')" type="checkbox" class="w-4 h-4 text-[var(--color-primary)] rounded border-gray-300 focus:ring-[var(--color-primary)]">
                    <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">Salida de Humos</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input v-model="terrace" @change="handleCheckbox('terrace')" type="checkbox" class="w-4 h-4 text-[var(--color-primary)] rounded border-gray-300 focus:ring-[var(--color-primary)]">
                    <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">Terraza</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer group">
                    <input v-model="outstanding" @change="handleCheckbox('outstanding')" type="checkbox" class="w-4 h-4 text-[var(--color-primary)] rounded border-gray-300 focus:ring-[var(--color-primary)]">
                    <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">Negocios Destacados</span>
                  </label>
                </div>
              </div>

              <hr class="border-gray-100" />

              <!-- Sectors -->
              <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sectores</label>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                  <label v-for="s in sectors" :key="s.id" class="flex items-center gap-2 cursor-pointer group">
                    <input
                      type="checkbox"
                      :checked="selectedSectors.includes(s.id)"
                      @change="toggleSector(s.id)"
                      class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                    >
                    <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">{{ s.name }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Main Grid Area -->
        <main class="flex-1">
          <div class="mb-4">
            <h2 class="text-2xl font-extrabold text-gray-900">{{ pageTitle }}</h2>
            <p class="text-sm text-gray-500 font-medium">
              <template v-if="isAiSearchMode">
                <AppIcon name="sparkles" :size="14" class="inline text-indigo-500 mr-1" />
                {{ aiSearchResults.length }} resultados con IA
              </template>
              <template v-else>{{ resultsCount }} negocios encontrados</template>
            </p>
          </div>

          <!-- Skeleton Loading -->
          <div v-if="businessStore.loading || aiStore.loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div v-for="i in 12" :key="i" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
               <div class="h-48 md:h-56 bg-gray-200 skeleton"></div>
               <div class="p-4 space-y-3">
                 <div class="h-5 bg-gray-200 rounded skeleton w-3/4"></div>
                 <div class="h-4 bg-gray-200 rounded skeleton w-1/2"></div>
                 <div class="h-3 bg-gray-200 rounded skeleton w-1/3"></div>
                 <div class="flex gap-2 pt-2">
                   <div class="h-6 bg-gray-200 rounded-full skeleton w-16"></div>
                   <div class="h-6 bg-gray-200 rounded-full skeleton w-20"></div>
                 </div>
               </div>
            </div>
          </div>

          <!-- AI Search Results -->
          <template v-else-if="isAiSearchMode">
            <div v-if="aiSearchResults.length === 0" class="text-center py-20">
              <div class="mb-4"><AppIcon name="sparkles" :size="48" class="text-indigo-300" /></div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">No se encontraron resultados</h3>
              <p class="text-gray-500">Intenta con otros terminos de busqueda o amplia los filtros.</p>
              <button @click="clearFilters" class="mt-6 c-btn c-btn--outline">Limpiar busqueda</button>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <div v-for="result in aiSearchResults" :key="result.id" class="relative">
                <div v-if="result.matchScore" class="absolute -top-2 -left-2 z-10 bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md">
                  {{ result.matchScore }}% match
                </div>
                <BusinessCard :business="result" class="h-full" />
                <div v-if="result.matchReasons?.length" class="px-3 pb-2 -mt-1">
                  <p class="text-[10px] text-gray-400 truncate">{{ result.matchReasons.join(' · ') }}</p>
                </div>
              </div>
            </div>
          </template>

          <!-- Normal Results -->
          <div v-else-if="resultsCount === 0" class="text-center py-20">
            <div class="mb-4"><AppIcon name="building" :size="48" /></div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No se encontraron negocios</h3>
            <p class="text-gray-500">Prueba ajustando los filtros o ampliando las zonas de busqueda.</p>
            <button @click="clearFilters" class="mt-6 c-btn c-btn--outline">Limpiar filtros y recargar</button>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <BusinessCard
              v-for="business in businessStore.businesses"
              :key="business.id"
              :business="business"
              class="h-full"
            />
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-12 flex justify-center pb-8">
            <div class="flex gap-2">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="w-10 h-10 rounded-xl bg-white border border-gray-200 shadow-sm font-bold transition-colors"
                :class="currentPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-400 hover:bg-gray-50'"
              >&laquo;</button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                class="w-10 h-10 rounded-xl shadow-sm font-bold transition-colors"
                :class="page === currentPage ? 'bg-[var(--color-primary)] text-white shadow-md' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
              >{{ page }}</button>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="w-10 h-10 rounded-xl bg-white border border-gray-200 shadow-sm font-bold transition-colors"
                :class="currentPage === totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-400 hover:bg-gray-50'"
              >&raquo;</button>
            </div>
          </div>
        </main>

      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.skeleton {
  background-color: #E2E8F0;
  animation: skeletonPulse 1.5s ease-in-out infinite;
  border-radius: 4px;
}
@keyframes skeletonPulse {
  0% { background-color: #E2E8F0; }
  50% { background-color: #F1F5F9; }
  100% { background-color: #E2E8F0; }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
