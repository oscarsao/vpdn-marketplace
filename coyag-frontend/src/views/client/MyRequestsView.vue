<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'
import AppIcon from '../../components/ui/AppIcon.vue'

const requests = ref([])
const loading = ref(true)
const showNewModal = ref(false)
const newRequest = ref({ type: 'info', business_ref: '', message: '' })

onMounted(async () => {
  try {
    const { data } = await axios.get('/client-request/my-request')
    requests.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const statusColors = {
  pendiente: 'bg-yellow-100 text-yellow-700',
  confirmada: 'bg-blue-100 text-blue-700',
  completada: 'bg-green-100 text-green-700',
  cancelada: 'bg-red-100 text-red-700',
}

const typeLabels = {
  info: 'Información',
  videocall: 'Videollamada',
  valoracion: 'Valoración',
  visita: 'Visita presencial',
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function submitRequest() {
  try {
    const { data } = await axios.post('/client-request', newRequest.value)
    requests.value.unshift(data)
    showNewModal.value = false
    newRequest.value = { type: 'info', business_ref: '', message: '' }
  } catch (e) {
    console.error(e)
  }
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">
      <div>
        <h2 class="text-xl font-extrabold text-gray-900">Mis Solicitudes</h2>
        <p class="text-sm text-gray-500">Historial de tus solicitudes y consultas</p>
      </div>
      <button @click="showNewModal = true" class="c-btn c-btn--primary w-full md:w-auto">+ Nueva solicitud</button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="c-card p-5"><div class="skeleton h-20 w-full rounded-lg"></div></div>
    </div>

    <!-- Empty -->
    <div v-else-if="requests.length === 0" class="text-center py-20">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <AppIcon name="inbox" :size="28" class="text-gray-300" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Sin solicitudes</h3>
      <p class="text-gray-500">Aún no has realizado ninguna solicitud.</p>
    </div>

    <!-- Request List -->
    <div v-else class="space-y-4">
      <div v-for="req in requests" :key="req.id" class="c-card p-5">
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2">
              <span :class="['c-badge text-xs', statusColors[req.status] || 'bg-gray-100 text-gray-600']">{{ req.status }}</span>
              <span class="c-badge bg-gray-100 text-gray-600 text-xs">{{ typeLabels[req.type] || req.type }}</span>
              <span v-if="req.business_ref" class="text-xs font-mono text-gray-400">{{ req.business_ref }}</span>
            </div>
            <p class="text-sm text-gray-700 font-medium">{{ req.message }}</p>
            <div class="text-xs text-gray-400 mt-2">{{ formatDate(req.created_at) }}</div>
            <div v-if="req.scheduled_at" class="text-xs text-blue-600 font-semibold mt-1">
              Programada: {{ formatDate(req.scheduled_at) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New Request Modal -->
    <Teleport to="body">
      <div v-if="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="showNewModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-[calc(100vw-2rem)] md:max-w-md p-6">
          <h3 class="text-lg font-extrabold text-gray-900 mb-4">Nueva Solicitud</h3>
          <form @submit.prevent="submitRequest" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tipo</label>
              <select v-model="newRequest.type" class="c-input">
                <option value="info">Solicitar información</option>
                <option value="videocall">Videollamada</option>
                <option value="visita">Visita presencial</option>
                <option value="valoracion">Valoración de negocio</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Referencia (opcional)</label>
              <input v-model="newRequest.business_ref" type="text" class="c-input" placeholder="REF-93203" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Mensaje</label>
              <textarea v-model="newRequest.message" rows="3" class="c-input" placeholder="Describe tu solicitud..." required></textarea>
            </div>
            <div class="flex gap-3 pt-2">
              <button type="submit" class="c-btn c-btn--primary flex-1">Enviar</button>
              <button type="button" @click="showNewModal = false" class="c-btn c-btn--ghost flex-1">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
