<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '../../api/axios'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const loading = ref(false)
const saving = ref(false)
const saved = ref(false)

const sectors = ref([])
const provinces = ref([])

const form = ref({
  title: '',
  type: 'traspaso',
  description: '',
  price: null,
  rent: null,
  size: null,
  bathrooms: null,
  smokeOutlet: false,
  terrace: false,
  facade: '',
  status: 'En funcionamiento',
  province: '',
  municipality: '',
  district: '',
  neighborhood: '',
  fullAddress: '',
  sectors: [],
  agentName: '',
  agentContact: '',
  agentPhone: '',
})

onMounted(async () => {
  // Load supporting data
  const [sectorsRes, provRes] = await Promise.all([
    axios.get('/sector'),
    axios.get('/province'),
  ])
  sectors.value = sectorsRes.data
  provinces.value = provRes.data

  // If editing, load business data
  if (isEdit.value) {
    loading.value = true
    try {
      const { data } = await axios.get(`/business/show/${route.params.id}`)
      if (data) {
        form.value = {
          title: data.title || '',
          type: data.type || 'traspaso',
          description: data.description || '',
          price: data.financials?.price || null,
          rent: data.financials?.rent || null,
          size: data.features?.size || null,
          bathrooms: data.features?.bathrooms || null,
          smokeOutlet: data.features?.smokeOutlet || false,
          terrace: data.features?.terrace || false,
          facade: data.features?.facade || '',
          status: data.features?.status || 'En funcionamiento',
          province: data.location?.province || '',
          municipality: data.location?.municipality || '',
          district: data.location?.district || '',
          neighborhood: data.location?.neighborhood || '',
          fullAddress: data.location?.fullAddress || '',
          sectors: data.sectors || [],
          agentName: data.agent?.name || '',
          agentContact: data.agent?.contact || '',
          agentPhone: data.agent?.phone || '',
        }
      }
    } catch (e) {
      console.error(e)
    } finally {
      loading.value = false
    }
  }
})

async function save() {
  saving.value = true
  // In mock mode this won't persist, but simulates the flow
  await new Promise(r => setTimeout(r, 600))
  saved.value = true
  saving.value = false
  setTimeout(() => {
    router.push('/admin/negocios')
  }, 1000)
}
</script>

<template>
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <button @click="router.back()" class="c-btn c-btn--ghost text-sm">← Volver</button>
      <h2 class="text-xl font-extrabold text-gray-900">{{ isEdit ? 'Editar Negocio' : 'Nuevo Negocio' }}</h2>
    </div>

    <div v-if="loading" class="c-card p-8"><div class="skeleton h-96 w-full rounded-lg"></div></div>

    <form v-else @submit.prevent="save" class="space-y-6">
      <!-- Basic Info -->
      <div class="c-card p-6">
        <h3 class="font-bold text-gray-900 mb-4">Información Básica</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Título</label>
            <input v-model="form.title" type="text" class="c-input" placeholder="Nombre del negocio" required />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tipo</label>
            <select v-model="form.type" class="c-input">
              <option value="traspaso">Traspaso</option>
              <option value="franquicia">Franquicia</option>
              <option value="inmueble">Inmueble</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Estado</label>
            <select v-model="form.status" class="c-input">
              <option>En funcionamiento</option>
              <option>Recién Reformado</option>
              <option>Vacío</option>
              <option>Llave en mano</option>
              <option>Equipado</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Descripción</label>
            <textarea v-model="form.description" rows="4" class="c-input" placeholder="Descripción detallada del negocio"></textarea>
          </div>
        </div>
      </div>

      <!-- Financial -->
      <div class="c-card p-6">
        <h3 class="font-bold text-gray-900 mb-4">Datos Financieros</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Precio (€)</label>
            <input v-model.number="form.price" type="number" class="c-input" placeholder="150000" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alquiler mensual (€)</label>
            <input v-model.number="form.rent" type="number" class="c-input" placeholder="2000" />
          </div>
        </div>
      </div>

      <!-- Features -->
      <div class="c-card p-6">
        <h3 class="font-bold text-gray-900 mb-4">Características</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Superficie (m²)</label>
            <input v-model.number="form.size" type="number" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Baños</label>
            <input v-model.number="form.bathrooms" type="number" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Fachada</label>
            <input v-model="form.facade" type="text" class="c-input" placeholder="8 m" />
          </div>
          <div class="flex flex-col gap-3 pt-5">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.smokeOutlet" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)]" />
              <span class="text-sm font-medium">Salida de Humos</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.terrace" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)]" />
              <span class="text-sm font-medium">Terraza</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Location -->
      <div class="c-card p-6">
        <h3 class="font-bold text-gray-900 mb-4">Ubicación</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Provincia</label>
            <select v-model="form.province" class="c-input">
              <option value="">Seleccionar</option>
              <option v-for="p in provinces" :key="p.id" :value="p.name">{{ p.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Municipio</label>
            <input v-model="form.municipality" type="text" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Distrito</label>
            <input v-model="form.district" type="text" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Barrio</label>
            <input v-model="form.neighborhood" type="text" class="c-input" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Dirección completa</label>
            <input v-model="form.fullAddress" type="text" class="c-input" placeholder="Calle, número, ciudad" />
          </div>
        </div>
      </div>

      <!-- Agent -->
      <div class="c-card p-6">
        <h3 class="font-bold text-gray-900 mb-4">Agente / Captador</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Empresa</label>
            <input v-model="form.agentName" type="text" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Contacto</label>
            <input v-model="form.agentContact" type="text" class="c-input" />
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Teléfono</label>
            <input v-model="form.agentPhone" type="text" class="c-input" placeholder="+34 600 000 000" />
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex items-center gap-4">
        <button type="submit" class="c-btn c-btn--primary" :disabled="saving">
          {{ saving ? 'Guardando...' : (isEdit ? 'Actualizar Negocio' : 'Crear Negocio') }}
        </button>
        <span v-if="saved" class="text-green-600 font-bold text-sm">Guardado correctamente</span>
      </div>
    </form>
  </div>
</template>
