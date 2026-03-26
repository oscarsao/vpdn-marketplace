<script setup>
import { computed } from 'vue'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  businesses: { type: Array, required: true },
})

// Generate realistic activity events from business data
const activities = computed(() => {
  const events = []
  const now = Date.now()

  props.businesses.forEach(b => {
    const daysAgo = b.days_on_market || 30

    // New listing event
    if (daysAgo < 15) {
      events.push({
        type: 'new',
        icon: 'megaphone',
        color: '#10B981',
        title: 'Nuevo negocio publicado',
        business: b.name,
        detail: formatEur(b.investment),
        time: daysAgo === 0 ? 'Hoy' : daysAgo === 1 ? 'Ayer' : `Hace ${daysAgo} dias`,
        timestamp: now - daysAgo * 86400000,
        businessId: b.id_code || b.id,
      })
    }

    // High demand (popular)
    if ((b.times_viewed || 0) > 100) {
      events.push({
        type: 'trending',
        icon: 'fire',
        color: '#F59E0B',
        title: 'Alta demanda',
        business: b.name,
        detail: `${b.times_viewed} visitas`,
        time: `Hace ${Math.max(1, Math.floor(daysAgo / 3))} dias`,
        timestamp: now - Math.floor(daysAgo / 3) * 86400000,
        businessId: b.id_code || b.id,
      })
    }

    // Price below average (deal alert)
    if (b.opportunityLabel === 'OPORTUNIDAD') {
      events.push({
        type: 'deal',
        icon: 'lightning',
        color: '#6366F1',
        title: 'Oportunidad detectada',
        business: b.name,
        detail: `Score: ${b.opportunityScore || '--'}`,
        time: `Hace ${Math.max(1, Math.floor(daysAgo / 2))} dias`,
        timestamp: now - Math.floor(daysAgo / 2) * 86400000,
        businessId: b.id_code || b.id,
      })
    }

    // Favorited a lot
    const favCount = b.times_favorited || b.metadata?.favorites || 0
    if (favCount > 20) {
      events.push({
        type: 'favorite',
        icon: 'heart',
        color: '#EF4444',
        title: 'Muy favoriteado',
        business: b.name,
        detail: `${favCount} usuarios`,
        time: `Hace ${Math.max(2, Math.floor(daysAgo / 4))} dias`,
        timestamp: now - Math.floor(daysAgo / 4) * 86400000,
        businessId: b.id_code || b.id,
      })
    }
  })

  // Sort by timestamp (most recent first) and limit
  return events.sort((a, b) => b.timestamp - a.timestamp).slice(0, 12)
})

function formatEur(n) {
  if (!n) return 'Consultar'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
      <AppIcon name="clock" :size="16" class="text-indigo-500" />
      Actividad reciente del mercado
    </h3>

    <div v-if="activities.length" class="space-y-1">
      <router-link
        v-for="(act, idx) in activities" :key="idx"
        :to="`/negocio/${act.businessId}`"
        class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors no-underline group"
      >
        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5"
          :style="{ backgroundColor: act.color + '15' }">
          <AppIcon :name="act.icon" :size="14" :style="{ color: act.color }" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-bold" :style="{ color: act.color }">{{ act.title }}</p>
          <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-[var(--color-primary)] transition-colors">{{ act.business }}</p>
          <div class="flex items-center justify-between mt-0.5">
            <span class="text-[10px] text-gray-400">{{ act.detail }}</span>
            <span class="text-[10px] text-gray-300">{{ act.time }}</span>
          </div>
        </div>
      </router-link>
    </div>

    <div v-else class="py-8 text-center">
      <p class="text-xs text-gray-400">No hay actividad reciente</p>
    </div>
  </div>
</template>
