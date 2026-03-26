<script setup>
import { ref } from 'vue'
import { useAlertStore } from '../../stores/alerts'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  currentFilters: { type: Object, default: () => ({}) },
  sectors: { type: Array, default: () => [] },
  provinces: { type: Array, default: () => [] },
})

const emit = defineEmits(['apply-alert'])

const alertStore = useAlertStore()
const showPanel = ref(false)
const alertName = ref('')
const showSaveForm = ref(false)
const justSaved = ref(false)

function getFilterSummary(filters) {
  const parts = []
  if (filters.name) parts.push(`"${filters.name}"`)
  if (filters.province_id) {
    const prov = props.provinces.find(p => p.id === filters.province_id)
    if (prov) parts.push(prov.name)
  }
  if (filters.min_investment || filters.max_investment) {
    const min = filters.min_investment ? Math.round(filters.min_investment / 1000) + 'K' : '0'
    const max = filters.max_investment ? Math.round(filters.max_investment / 1000) + 'K' : '...'
    parts.push(`${min}-${max} €`)
  }
  if (filters.sectors) {
    const sectorIds = filters.sectors.split(',').map(Number)
    const names = sectorIds
      .map(id => props.sectors.find(s => s.id === id)?.name)
      .filter(Boolean)
    if (names.length) parts.push(names.join(', '))
  }
  if (filters.smoke_outlet) parts.push('Humos')
  if (filters.terrace) parts.push('Terraza')
  if (filters.flag_outstanding) parts.push('Destacados')
  return parts.length ? parts.join(' · ') : 'Sin filtros'
}

function hasActiveFilters() {
  const f = props.currentFilters
  return f.name || f.province_id || f.municipality_id || f.min_investment || f.max_investment ||
    f.min_rental || f.max_rental || f.smoke_outlet || f.terrace || f.flag_outstanding || f.sectors
}

function saveCurrentFilters() {
  if (!alertName.value.trim()) return
  alertStore.addAlert({
    name: alertName.value.trim(),
    filters: { ...props.currentFilters },
  })
  alertName.value = ''
  showSaveForm.value = false
  justSaved.value = true
  setTimeout(() => { justSaved.value = false }, 2000)
}

function applyAlert(alert) {
  emit('apply-alert', alert.filters)
  showPanel.value = false
}

function deleteAlert(id) {
  alertStore.removeAlert(id)
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}
</script>

<template>
  <div class="relative">
    <!-- Toggle button -->
    <button @click="showPanel = !showPanel"
      class="c-btn c-btn--outline text-sm flex items-center gap-1.5 relative">
      <AppIcon name="bell" :size="14" />
      Alertas
      <span v-if="alertStore.count" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-[var(--color-primary)] text-white text-[10px] font-bold rounded-full flex items-center justify-center">
        {{ alertStore.count }}
      </span>
    </button>

    <!-- Panel dropdown -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showPanel" class="fixed inset-0 z-[1050]" @click.self="showPanel = false">
          <div class="absolute inset-0 bg-black/20" @click="showPanel = false"></div>
          <div class="absolute top-20 right-4 md:right-8 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in">
            <!-- Header -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
              <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <AppIcon name="bell" :size="16" class="text-[var(--color-primary)]" />
                Mis Alertas
              </h3>
              <button @click="showPanel = false" class="p-1 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer">
                <AppIcon name="x-mark" :size="16" />
              </button>
            </div>

            <!-- Save current filters -->
            <div class="p-3 border-b border-gray-50 bg-gray-50/50">
              <div v-if="!showSaveForm">
                <button @click="showSaveForm = true" :disabled="!hasActiveFilters()"
                  :class="['w-full text-xs font-semibold py-2 px-3 rounded-lg border-none cursor-pointer transition-colors',
                    hasActiveFilters() ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' : 'bg-gray-100 text-gray-400 cursor-not-allowed']">
                  <AppIcon name="plus" :size="12" class="inline mr-1" />
                  Guardar filtros actuales como alerta
                </button>
              </div>
              <div v-else class="space-y-2">
                <input v-model="alertName" type="text" placeholder="Nombre de la alerta..."
                  class="c-input text-sm w-full" @keyup.enter="saveCurrentFilters" />
                <p class="text-[10px] text-gray-400">{{ getFilterSummary(currentFilters) }}</p>
                <div class="flex gap-2">
                  <button @click="saveCurrentFilters" :disabled="!alertName.trim()"
                    class="flex-1 c-btn c-btn--primary py-1.5 text-xs">Guardar</button>
                  <button @click="showSaveForm = false" class="c-btn c-btn--ghost py-1.5 text-xs">Cancelar</button>
                </div>
              </div>
            </div>

            <!-- Saved alert notification -->
            <div v-if="justSaved" class="p-3 bg-green-50 text-green-700 text-xs font-semibold flex items-center gap-1.5">
              <AppIcon name="check" :size="14" /> Alerta guardada
            </div>

            <!-- Alert list -->
            <div class="max-h-64 overflow-y-auto">
              <div v-if="alertStore.count === 0" class="p-6 text-center">
                <div class="opacity-30 mb-2"><AppIcon name="bell" :size="32" /></div>
                <p class="text-xs text-gray-400">No tienes alertas guardadas</p>
                <p class="text-[10px] text-gray-300 mt-1">Aplica filtros y guardalos para recibir notificaciones</p>
              </div>

              <div v-for="alert in alertStore.savedAlerts" :key="alert.id"
                class="p-3 border-b border-gray-50 hover:bg-gray-50/50 transition-colors group">
                <div class="flex items-start justify-between gap-2">
                  <div class="flex-1 min-w-0 cursor-pointer" @click="applyAlert(alert)">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ alert.name }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ getFilterSummary(alert.filters) }}</p>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ formatDate(alert.createdAt) }}</p>
                  </div>
                  <div class="flex items-center gap-1 shrink-0">
                    <button @click="applyAlert(alert)" class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded bg-transparent border-none cursor-pointer" title="Aplicar">
                      <AppIcon name="search" :size="12" />
                    </button>
                    <button @click="deleteAlert(alert.id)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded bg-transparent border-none cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity" title="Eliminar">
                      <AppIcon name="x-mark" :size="12" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>
