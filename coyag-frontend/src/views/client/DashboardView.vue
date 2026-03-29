<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../api/axios'
import AppIcon from '../../components/ui/AppIcon.vue'

const auth = useAuthStore()

const loading = ref(true)
const favoritesData = ref([])
const notificationsData = ref([])
const activityFeed = ref([])

onMounted(async () => {
  try {
    const { data: favRaw } = await axios.get('/favorite')
    favoritesData.value = Array.isArray(favRaw) ? favRaw : []
  } catch (e) { console.error('favorites:', e); favoritesData.value = [] }

  try {
    const { data: notifRaw } = await axios.get('/notification')
    notificationsData.value = Array.isArray(notifRaw) ? notifRaw : []
  } catch (e) { console.error('notifications:', e); notificationsData.value = [] }

  try {
    const { data: actRaw } = await axios.get('/activity-feed')
    activityFeed.value = Array.isArray(actRaw) ? actRaw : []
  } catch (e) { console.error('activity-feed:', e); activityFeed.value = [] }

  loading.value = false
})

// KPI stats computed from real data
const stats = computed(() => [
  {
    label: 'Negocios Favoritos',
    value: favoritesData.value.length,
    icon: 'heart',
    bgColor: 'bg-red-50',
    textColor: 'var(--color-primary)',
  },
  {
    label: 'Notificaciones',
    value: notificationsData.value.filter(n => !n.read).length,
    icon: 'bell',
    bgColor: 'bg-yellow-50',
    textColor: '#EAB308',
  },
  {
    label: 'Actividad Reciente',
    value: activityFeed.value.length,
    icon: 'chart-line',
    bgColor: 'bg-indigo-50',
    textColor: 'var(--color-info)',
  },
])

// Donut data: favorites by type
const favByType = computed(() => {
  const counts = { Traspaso: 0, Franquicia: 0, Inmueble: 0 }
  favoritesData.value.forEach(b => {
    const type = b.business_type?.name || 'Traspaso'
    counts[type] = (counts[type] || 0) + 1
  })
  return counts
})

const donutSeries = computed(() => Object.values(favByType.value))
const donutLabels = computed(() => Object.keys(favByType.value))
const donutOptions = computed(() => ({
  chart: { type: 'donut', height: 220 },
  labels: donutLabels.value,
  colors: ['#A40E05', '#7367F0', '#28C76F'],
  legend: { position: 'bottom', fontSize: '12px' },
  dataLabels: { enabled: false },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            fontSize: '14px',
            fontWeight: 700,
          }
        }
      }
    }
  },
}))

// Sparkline: simulated weekly views
const sparkSeries = computed(() => [{
  name: 'Visitas',
  data: [12, 18, 14, 22, 19, 28, 25],
}])
const sparkOptions = {
  chart: { type: 'area', height: 80, sparkline: { enabled: true } },
  stroke: { curve: 'smooth', width: 2 },
  colors: ['#A40E05'],
  fill: {
    type: 'gradient',
    gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
  },
  tooltip: {
    fixed: { enabled: false },
    y: { formatter: (v) => v + ' visitas' }
  },
}

// Activity feed icon mapping
const activityIcons = {
  favorite: 'heart',
  view: 'eye',
  search: 'search',
  login: 'lock',
  profile: 'user',
  request: 'chat',
}

function formatDate(d) {
  const date = new Date(d)
  const now = new Date()
  const diffMs = now - date
  const diffHrs = Math.floor(diffMs / (1000 * 60 * 60))
  if (diffHrs < 1) return 'Hace unos minutos'
  if (diffHrs < 24) return `Hace ${diffHrs}h`
  const diffDays = Math.floor(diffHrs / 24)
  if (diffDays === 1) return 'Ayer'
  if (diffDays < 7) return `Hace ${diffDays} dias`
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}
</script>

<template>
  <div class="animate-fade-in">
    <!-- Header -->
    <header class="mb-8 p-4 md:p-8 bg-gradient-to-r from-red-50 to-white rounded-2xl border border-red-100 shadow-sm relative overflow-hidden">
      <div class="absolute -right-20 -top-20 w-64 h-64 bg-red-100 rounded-full blur-3xl opacity-50 z-0 hidden md:block"></div>
      <div class="relative z-10">
        <h1 class="text-xl md:text-3xl font-extrabold" style="color: var(--color-primary-dark);">
          Hola, {{ auth.fullName || 'Usuario' }}
        </h1>
        <p class="mt-2 text-base font-medium text-gray-600">
          Bienvenido a tu panel. Aqui tienes el resumen de tu actividad.
        </p>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div v-for="i in 3" :key="i" class="c-card p-6"><div class="skeleton h-20 w-full rounded"></div></div>
    </div>

    <template v-else>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div v-for="(stat, index) in stats" :key="index" class="c-card p-6 flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 mb-1">{{ stat.label }}</p>
            <p class="text-xl md:text-3xl font-bold" :style="{ color: stat.textColor }">{{ stat.value }}</p>
          </div>
          <div :class="[stat.bgColor, 'w-12 h-12 rounded-full flex items-center justify-center']">
            <AppIcon :name="stat.icon" :size="20" />
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Sparkline Card -->
        <div class="c-card p-6">
          <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Visitas esta semana</h3>
          <p class="text-2xl font-bold text-gray-900 mb-2">138</p>
          <apexchart type="area" height="80" :options="sparkOptions" :series="sparkSeries" />
        </div>

        <!-- Donut Card -->
        <div class="c-card p-6 lg:col-span-2">
          <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Favoritos por tipo</h3>
          <div v-if="favoritesData.length > 0">
            <apexchart type="donut" height="220" :options="donutOptions" :series="donutSeries" />
          </div>
          <div v-else class="text-center py-8 text-gray-400">
            <p class="text-sm">Agrega negocios a favoritos para ver la distribucion.</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Activity Feed -->
        <div class="lg:col-span-2 c-card p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-6">Actividad Reciente</h2>
          <div v-if="activityFeed.length" class="space-y-4">
            <div v-for="item in activityFeed.slice(0, 8)" :key="item.id" class="flex gap-4 items-start">
              <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                <AppIcon :name="activityIcons[item.type] || 'bell'" :size="18" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ item.action }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(item.created_at) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-400">
            <p class="text-sm">No hay actividad reciente.</p>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="c-card p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-6">Acciones Rapidas</h2>
          <div class="flex flex-col gap-3">
            <router-link to="/favoritos" class="c-btn c-btn--outline w-full justify-start no-underline text-sm">
              Mis Favoritos
            </router-link>
            <router-link to="/mis-preferencias" class="c-btn c-btn--outline w-full justify-start no-underline text-sm">
              Mis Busquedas
            </router-link>
            <router-link to="/mi-perfil" class="c-btn c-btn--outline w-full justify-start no-underline text-sm">
              Editar Perfil
            </router-link>
            <router-link to="/mi-asesor" class="c-btn c-btn--outline w-full justify-start no-underline text-sm">
              Mi Asesor
            </router-link>
            <router-link to="/recomendados" class="c-btn c-btn--outline w-full justify-start no-underline text-sm">
              Recomendados IA
            </router-link>
            <router-link v-if="auth.isAdmin" to="/admin/negocios" class="c-btn c-btn--primary w-full justify-start no-underline text-sm mt-2">
              Panel de Administracion
            </router-link>
          </div>
        </div>
      </div>
    </template>
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
</style>
