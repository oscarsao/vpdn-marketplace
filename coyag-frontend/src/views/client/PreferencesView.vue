<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useSearchStore } from '../../stores/searches'
import { mockSectors } from '../../data/mockData'
import SearchWizard from '../../components/search/SearchWizard.vue'
import AppIcon from '../../components/ui/AppIcon.vue'

const router = useRouter()
const searchStore = useSearchStore()

const showWizard = ref(false)
const editingSearch = ref(null)
const confirmDeleteId = ref(null)

function openNew() {
  editingSearch.value = null
  showWizard.value = true
}

function openEdit(search) {
  editingSearch.value = search
  showWizard.value = true
}

function handleSaved({ name, filters }) {
  if (editingSearch.value) {
    searchStore.updateSearch(editingSearch.value.id, { name, filters })
  } else {
    searchStore.addSearch({ name, filters })
  }
  showWizard.value = false
  editingSearch.value = null
}

function handleDelete(id) {
  searchStore.removeSearch(id)
  confirmDeleteId.value = null
}

function handleApply(id) {
  searchStore.applySearch(id)
  router.push('/listado-general')
}

function getSectorName(id) {
  const s = mockSectors.find(s => s.id === id)
  return s ? s.name : ''
}

function formatEur(n) {
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M €'
  if (n >= 1000) return Math.round(n / 1000) + 'K €'
  return n + ' €'
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })
}

function getFilterChips(search) {
  const chips = []
  const f = search.filters
  if (f.condition) {
    const labels = { traspaso: 'Traspaso', franquicia: 'Franquicia', inmueble: 'Inmueble' }
    chips.push(labels[f.condition] || f.condition)
  }
  if (f.sectors && f.sectors.length) {
    f.sectors.slice(0, 3).forEach(id => {
      const name = getSectorName(id)
      if (name) chips.push(name)
    })
    if (f.sectors.length > 3) chips.push(`+${f.sectors.length - 3} mas`)
  }
  if (f.min_investment > 0 || f.max_investment < 2000000) {
    chips.push(`${formatEur(f.min_investment)} - ${formatEur(f.max_investment)}`)
  }
  if (f.terrace) chips.push('Terraza')
  if (f.smoke_outlet) chips.push('Humos')
  return chips
}
</script>

<template>
  <div class="py-4 md:py-6 animate-fade-in max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 md:mb-8">
      <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-gray-900">Mis Busquedas Guardadas</h1>
        <p class="text-xs md:text-sm text-gray-400 mt-1">Crea y gestiona tus criterios de busqueda personalizados</p>
      </div>
      <button @click="openNew" class="c-btn c-btn--primary text-xs md:text-sm py-2 px-4 flex items-center gap-1.5">
        <AppIcon name="plus" :size="16" />
        Nueva busqueda
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="!searchStore.hasSearches" class="c-card p-8 md:p-16 text-center">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <AppIcon name="search" :size="28" class="text-gray-300" />
      </div>
      <h2 class="text-base md:text-lg font-bold text-gray-900 mb-2">Aun no tienes busquedas guardadas</h2>
      <p class="text-xs md:text-sm text-gray-400 mb-5 max-w-md mx-auto">
        Crea tu primera busqueda con los criterios que te interesan y aplicalos con un clic.
      </p>
      <button @click="openNew" class="c-btn c-btn--primary text-sm py-2.5 px-6">
        Crear mi primera busqueda
      </button>
    </div>

    <!-- Saved searches grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
      <div
        v-for="search in searchStore.savedSearches"
        :key="search.id"
        class="c-card p-4 md:p-5 flex flex-col"
      >
        <!-- Name + date -->
        <div class="mb-3">
          <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ search.name }}</h3>
          <p class="text-[10px] text-gray-400 mt-0.5">{{ formatDate(search.createdAt) }}</p>
        </div>

        <!-- Filter chips -->
        <div class="flex flex-wrap gap-1.5 mb-4 flex-1">
          <span
            v-for="(chip, i) in getFilterChips(search)"
            :key="i"
            class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600"
          >
            {{ chip }}
          </span>
          <span v-if="!getFilterChips(search).length" class="text-[10px] text-gray-300 italic">Sin filtros especificos</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
          <button @click="handleApply(search.id)" class="c-btn c-btn--primary text-[11px] py-1.5 px-3 flex-1">
            Aplicar
          </button>
          <button @click="openEdit(search)" class="c-btn c-btn--outline text-[11px] py-1.5 px-3">
            Editar
          </button>
          <button
            v-if="confirmDeleteId !== search.id"
            @click="confirmDeleteId = search.id"
            class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-red-50 transition-colors cursor-pointer border-none"
          >
            <AppIcon name="trash" :size="14" class="text-gray-400 hover:text-red-500" />
          </button>
          <button
            v-else
            @click="handleDelete(search.id)"
            class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1.5 rounded-lg cursor-pointer border-none hover:bg-red-100 transition-colors"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>

    <!-- Wizard modal -->
    <SearchWizard
      v-if="showWizard"
      :editSearch="editingSearch"
      @saved="handleSaved"
      @close="showWizard = false"
    />
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
</style>
