<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../api/axios'

const leads = ref([])
const loading = ref(true)
const filterStatus = ref('all')

onMounted(async () => {
  try {
    const { data } = await axios.get('/leads')
    leads.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const filteredLeads = computed(() => {
  if (filterStatus.value === 'all') return leads.value
  return leads.value.filter(l => l.status === filterStatus.value)
})

const statusColors = {
  nuevo: 'bg-blue-100 text-blue-700',
  contactado: 'bg-yellow-100 text-yellow-700',
  cualificado: 'bg-purple-100 text-purple-700',
  negociando: 'bg-orange-100 text-orange-700',
  cerrado: 'bg-green-100 text-green-700',
  perdido: 'bg-red-100 text-red-700',
}

const statuses = ['all', 'nuevo', 'contactado', 'cualificado', 'negociando', 'cerrado']

function formatDate(d) {
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-extrabold text-gray-900">Gestión de Leads</h2>
        <p class="text-sm text-gray-500">{{ leads.length }} leads en total</p>
      </div>
    </div>

    <!-- Status Filters -->
    <div class="c-pills mb-6">
      <button
        v-for="s in statuses"
        :key="s"
        @click="filterStatus = s"
        :class="['c-pill', { 'c-pill--active': filterStatus === s }]"
      >
        {{ s === 'all' ? 'Todos' : s.charAt(0).toUpperCase() + s.slice(1) }}
      </button>
    </div>

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 4" :key="i" class="c-card p-5"><div class="skeleton h-20 w-full rounded"></div></div>
    </div>

    <!-- Lead Cards -->
    <div v-else class="space-y-4">
      <div v-for="lead in filteredLeads" :key="lead.id" class="c-card p-5 hover:border-[var(--color-primary)]/20">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h4 class="font-bold text-gray-900">{{ lead.name }}</h4>
              <span :class="['c-badge text-xs', statusColors[lead.status] || 'bg-gray-100']">{{ lead.status }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
              <span>{{ lead.email }}</span>
              <span>{{ lead.phone }}</span>
              <span v-if="lead.business_ref" class="font-mono text-xs">{{ lead.business_ref }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ lead.notes }}</p>
          </div>
          <div class="shrink-0 text-right">
            <div class="c-badge bg-gray-100 text-gray-600 text-xs mb-2">{{ lead.source }}</div>
            <div class="text-xs text-gray-400">{{ formatDate(lead.created_at) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
