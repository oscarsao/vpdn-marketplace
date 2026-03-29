<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useLocationStore } from '../../stores/location'
import { mockSectors } from '../../data/mockData'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  editSearch: { type: Object, default: null },
})

const emit = defineEmits(['saved', 'close'])

const locationStore = useLocationStore()

const step = ref(1)
const totalSteps = 3

// Step 1: Type + Sectors
const condition = ref(null)
const selectedSectors = ref([])

// Step 2: Location
const provinceId = ref(null)
const municipalityId = ref(null)
const districtId = ref(null)
const neighborhoodId = ref(null)

// Step 3: Budget + Features + Name
const minInvestment = ref(0)
const maxInvestment = ref(2000000)
const minRental = ref(0)
const maxRental = ref(10000)
const smokeOutlet = ref(false)
const terrace = ref(false)
const searchName = ref('')

const businessTypes = [
  { value: null, label: 'Todos', icon: 'globe' },
  { value: 'traspaso', label: 'Traspaso', icon: 'store' },
  { value: 'franquicia', label: 'Franquicia', icon: 'briefcase' },
  { value: 'inmueble', label: 'Inmueble', icon: 'building' },
]

// Populate from editSearch if provided
onMounted(() => {
  locationStore.fetchProvinces()

  if (props.editSearch) {
    const f = props.editSearch.filters
    searchName.value = props.editSearch.name || ''
    condition.value = f.condition || null
    selectedSectors.value = f.sectors ? [...f.sectors] : []
    provinceId.value = f.province_id || null
    municipalityId.value = f.municipality_id || null
    districtId.value = f.district_id || null
    neighborhoodId.value = f.neighborhood_id || null
    minInvestment.value = f.min_investment || 0
    maxInvestment.value = f.max_investment || 2000000
    minRental.value = f.min_rental || 0
    maxRental.value = f.max_rental || 10000
    smokeOutlet.value = f.smoke_outlet || false
    terrace.value = f.terrace || false

    // Load cascading selects
    if (f.province_id) locationStore.fetchMunicipalities(f.province_id)
    if (f.municipality_id) locationStore.fetchDistricts(f.municipality_id)
    if (f.district_id) locationStore.fetchNeighborhoods(f.district_id)
  }
})

// Cascading location watchers
watch(provinceId, (val) => {
  if (val) {
    locationStore.fetchMunicipalities(val)
  } else {
    locationStore.municipalities = []
  }
  municipalityId.value = null
  districtId.value = null
  neighborhoodId.value = null
})

watch(municipalityId, (val) => {
  if (val) {
    locationStore.fetchDistricts(val)
  } else {
    locationStore.districts = []
  }
  districtId.value = null
  neighborhoodId.value = null
})

watch(districtId, (val) => {
  if (val) {
    locationStore.fetchNeighborhoods(val)
  } else {
    locationStore.neighborhoods = []
  }
  neighborhoodId.value = null
})

function toggleSector(id) {
  const idx = selectedSectors.value.indexOf(id)
  if (idx >= 0) {
    selectedSectors.value.splice(idx, 1)
  } else {
    selectedSectors.value.push(id)
  }
}

function formatEur(n) {
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M €'
  if (n >= 1000) return Math.round(n / 1000) + 'K €'
  return n + ' €'
}

const isEditing = computed(() => !!props.editSearch)

const summaryChips = computed(() => {
  const chips = []
  if (condition.value) {
    const t = businessTypes.find(b => b.value === condition.value)
    if (t) chips.push(t.label)
  }
  selectedSectors.value.forEach(id => {
    const s = mockSectors.find(s => s.id === id)
    if (s) chips.push(s.name)
  })
  if (provinceId.value) {
    const p = locationStore.provinces.find(p => p.id === provinceId.value)
    if (p) chips.push(p.name)
  }
  if (minInvestment.value > 0 || maxInvestment.value < 2000000) {
    chips.push(`${formatEur(minInvestment.value)} - ${formatEur(maxInvestment.value)}`)
  }
  if (terrace.value) chips.push('Terraza')
  if (smokeOutlet.value) chips.push('Salida de humos')
  return chips
})

function handleSave() {
  const filters = {
    condition: condition.value,
    sectors: [...selectedSectors.value],
    province_id: provinceId.value,
    municipality_id: municipalityId.value,
    district_id: districtId.value,
    neighborhood_id: neighborhoodId.value,
    min_investment: minInvestment.value,
    max_investment: maxInvestment.value,
    min_rental: minRental.value,
    max_rental: maxRental.value,
    smoke_outlet: smokeOutlet.value,
    terrace: terrace.value,
  }
  const name = searchName.value.trim() || 'Mi busqueda'
  emit('saved', { name, filters })
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="emit('close')">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50" @click="emit('close')"></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-xl md:rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
      <!-- Header -->
      <div class="sticky top-0 bg-white z-10 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h2 class="text-base md:text-lg font-extrabold text-gray-900">
            {{ isEditing ? 'Editar busqueda' : 'Nueva busqueda' }}
          </h2>
          <p class="text-xs text-gray-400 mt-0.5">Paso {{ step }} de {{ totalSteps }}</p>
        </div>
        <button @click="emit('close')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors cursor-pointer border-none">
          <AppIcon name="x-mark" :size="16" class="text-gray-500" />
        </button>
      </div>

      <!-- Progress bar -->
      <div class="h-1 bg-gray-100">
        <div class="h-full bg-[var(--color-primary)] transition-all duration-300" :style="{ width: (step / totalSteps * 100) + '%' }"></div>
      </div>

      <!-- Step content -->
      <div class="p-5">

        <!-- STEP 1: Que buscas -->
        <div v-if="step === 1">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Tipo de negocio</h3>
          <div class="grid grid-cols-2 gap-2.5 mb-6">
            <button
              v-for="t in businessTypes"
              :key="t.value"
              @click="condition = condition === t.value ? null : t.value"
              class="p-3 rounded-xl border-2 transition-all cursor-pointer text-left"
              :class="condition === t.value
                ? 'border-[var(--color-primary)] bg-red-50'
                : 'border-gray-200 bg-white hover:border-gray-300'"
            >
              <div class="w-8 h-8 rounded-full flex items-center justify-center mb-2"
                :class="condition === t.value ? 'bg-[var(--color-primary)]/10' : 'bg-gray-100'">
                <AppIcon :name="t.icon" :size="16"
                  :class="condition === t.value ? 'text-[var(--color-primary)]' : 'text-gray-400'" />
              </div>
              <p class="text-xs font-bold" :class="condition === t.value ? 'text-[var(--color-primary)]' : 'text-gray-700'">
                {{ t.label }}
              </p>
            </button>
          </div>

          <h3 class="text-sm font-bold text-gray-900 mb-3">Sectores de interes</h3>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="s in mockSectors"
              :key="s.id"
              @click="toggleSector(s.id)"
              class="text-[11px] font-semibold px-3 py-1.5 rounded-full transition-all cursor-pointer border"
              :class="selectedSectors.includes(s.id)
                ? 'bg-[var(--color-primary)] text-white border-[var(--color-primary)]'
                : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'"
            >
              {{ s.name }}
            </button>
          </div>
        </div>

        <!-- STEP 2: Donde -->
        <div v-if="step === 2">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Ubicacion</h3>
          <p class="text-xs text-gray-400 mb-5">Deja en blanco para buscar en toda Espana</p>

          <div class="space-y-4">
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Provincia</label>
              <select v-model="provinceId" class="c-input w-full">
                <option :value="null">Toda Espana</option>
                <option v-for="p in locationStore.provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>

            <div v-if="provinceId && locationStore.municipalities.length">
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Municipio</label>
              <select v-model="municipalityId" class="c-input w-full">
                <option :value="null">Todos los municipios</option>
                <option v-for="m in locationStore.municipalities" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
            </div>

            <div v-if="municipalityId && locationStore.districts.length">
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Distrito</label>
              <select v-model="districtId" class="c-input w-full">
                <option :value="null">Todos los distritos</option>
                <option v-for="d in locationStore.districts" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>

            <div v-if="districtId && locationStore.neighborhoods.length">
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Barrio</label>
              <select v-model="neighborhoodId" class="c-input w-full">
                <option :value="null">Todos los barrios</option>
                <option v-for="n in locationStore.neighborhoods" :key="n.id" :value="n.id">{{ n.name }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- STEP 3: Presupuesto y detalles -->
        <div v-if="step === 3">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Presupuesto</h3>

          <div class="space-y-5 mb-6">
            <div>
              <div class="flex justify-between text-xs mb-1.5">
                <span class="font-semibold text-gray-600">Inversión mínima</span>
                <span class="font-bold text-gray-900">{{ formatEur(minInvestment) }}</span>
              </div>
              <input type="range" v-model.number="minInvestment" min="0" :max="maxInvestment" step="10000" class="w-full" />
            </div>

            <div>
              <div class="flex justify-between text-xs mb-1.5">
                <span class="font-semibold text-gray-600">Inversión máxima</span>
                <span class="font-bold text-gray-900">{{ formatEur(maxInvestment) }}</span>
              </div>
              <input type="range" v-model.number="maxInvestment" :min="minInvestment" max="2000000" step="10000" class="w-full" />
            </div>

            <div>
              <div class="flex justify-between text-xs mb-1.5">
                <span class="font-semibold text-gray-600">Alquiler min/mes</span>
                <span class="font-bold text-gray-900">{{ formatEur(minRental) }}</span>
              </div>
              <input type="range" v-model.number="minRental" min="0" :max="maxRental" step="100" class="w-full" />
            </div>

            <div>
              <div class="flex justify-between text-xs mb-1.5">
                <span class="font-semibold text-gray-600">Alquiler max/mes</span>
                <span class="font-bold text-gray-900">{{ formatEur(maxRental) }}</span>
              </div>
              <input type="range" v-model.number="maxRental" :min="minRental" max="10000" step="100" class="w-full" />
            </div>
          </div>

          <h3 class="text-sm font-bold text-gray-900 mb-3">Características</h3>
          <div class="flex gap-4 mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="terrace" />
              <span class="text-xs font-semibold text-gray-700">Terraza</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="smokeOutlet" />
              <span class="text-xs font-semibold text-gray-700">Salida de humos</span>
            </label>
          </div>

          <h3 class="text-sm font-bold text-gray-900 mb-3">Nombre de la busqueda</h3>
          <input
            v-model="searchName"
            type="text"
            class="c-input w-full"
            placeholder="Ej: Mi busqueda de restaurantes"
          />

          <!-- Summary -->
          <div v-if="summaryChips.length" class="mt-5 pt-4 border-t border-gray-100">
            <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Resumen de criterios</p>
            <div class="flex flex-wrap gap-1.5">
              <span
                v-for="(chip, i) in summaryChips"
                :key="i"
                class="text-[10px] font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600"
              >
                {{ chip }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="sticky bottom-0 bg-white px-5 py-4 border-t border-gray-100 flex justify-between">
        <button
          v-if="step > 1"
          @click="step--"
          class="c-btn c-btn--outline text-xs py-2 px-4"
        >
          Anterior
        </button>
        <div v-else></div>

        <button
          v-if="step < totalSteps"
          @click="step++"
          class="c-btn c-btn--primary text-xs py-2 px-5"
        >
          Siguiente
        </button>
        <button
          v-else
          @click="handleSave"
          class="c-btn c-btn--primary text-xs py-2 px-5"
        >
          {{ isEditing ? 'Actualizar' : 'Guardar busqueda' }}
        </button>
      </div>
    </div>
  </div>
</template>
