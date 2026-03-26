<script setup>
import AppIcon from '../ui/AppIcon.vue'

defineProps({
  timeline: { type: Array, default: () => [] },
})

const iconMap = {
  publish: 'megaphone',
  price: 'currency',
  views: 'eye',
  favorite: 'heart',
  request: 'chat',
  update: 'check',
}

const colorMap = {
  publish: 'bg-green-100 text-green-600',
  price: 'bg-amber-100 text-amber-600',
  views: 'bg-blue-100 text-blue-600',
  favorite: 'bg-red-100 text-red-500',
  request: 'bg-indigo-100 text-indigo-600',
  update: 'bg-gray-100 text-gray-600',
}

function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })
}

function daysAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 86400000)
  if (diff === 0) return 'Hoy'
  if (diff === 1) return 'Ayer'
  return `Hace ${diff} dias`
}
</script>

<template>
  <div v-if="timeline.length">
    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
      <AppIcon name="clock" :size="20" class="text-gray-400" />
      Historial del Negocio
    </h2>

    <div class="relative">
      <!-- Vertical line -->
      <div class="absolute left-[19px] top-2 bottom-2 w-px bg-gray-200"></div>

      <div class="space-y-4">
        <div v-for="(event, i) in timeline" :key="i" class="flex gap-4 relative">
          <!-- Icon circle -->
          <div :class="['w-10 h-10 rounded-full flex items-center justify-center shrink-0 z-10', colorMap[event.type] || 'bg-gray-100 text-gray-500']">
            <AppIcon :name="iconMap[event.type] || 'info'" :size="16" />
          </div>

          <!-- Content -->
          <div class="flex-1 pb-1">
            <div class="flex items-baseline justify-between gap-2">
              <p class="text-sm font-semibold text-gray-900">{{ event.action }}</p>
              <span class="text-[10px] text-gray-400 whitespace-nowrap shrink-0">{{ daysAgo(event.date) }}</span>
            </div>
            <p v-if="event.detail" class="text-xs text-gray-500 mt-0.5">{{ event.detail }}</p>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[10px] text-gray-400">{{ formatDate(event.date) }}</span>
              <span v-if="event.user && event.user !== 'Sistema'" class="text-[10px] text-gray-400">· {{ event.user }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
