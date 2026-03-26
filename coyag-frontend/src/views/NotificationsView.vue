<script setup>
import { ref, onMounted } from 'vue'
import axios from '../api/axios'
import AppIcon from '../components/ui/AppIcon.vue'

const notifications = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await axios.get('/notification')
    notifications.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const typeIcons = {
  lead: 'inbox',
  business: 'store',
  calendar: 'calendar',
  client: 'user',
  stats: 'chart-bar',
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

function markRead(notif) {
  notif.read = true
}
</script>

<template>
  <div class="max-w-2xl mx-auto">
    <h2 class="text-xl font-extrabold text-gray-900 mb-6">Notificaciones</h2>

    <div v-if="loading" class="space-y-3">
      <div v-for="i in 4" :key="i" class="c-card p-4"><div class="skeleton h-16 w-full rounded"></div></div>
    </div>

    <div v-else-if="notifications.length === 0" class="text-center py-20">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <AppIcon name="bell" :size="28" class="text-gray-300" />
      </div>
      <h3 class="text-lg font-bold text-gray-900">Sin notificaciones</h3>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="n in notifications"
        :key="n.id"
        @click="markRead(n)"
        :class="['c-card p-4 cursor-pointer transition-colors', n.read ? 'opacity-60' : 'border-l-4 border-l-[var(--color-primary)]']"
      >
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
            <AppIcon :name="typeIcons[n.type] || 'bell'" :size="16" class="text-gray-500" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <h4 class="font-bold text-gray-900 text-sm">{{ n.title }}</h4>
              <span v-if="!n.read" class="w-2 h-2 rounded-full bg-[var(--color-primary)] shrink-0"></span>
            </div>
            <p class="text-sm text-gray-600 mt-0.5">{{ n.message }}</p>
            <span class="text-xs text-gray-400 mt-1 block">{{ formatDate(n.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
