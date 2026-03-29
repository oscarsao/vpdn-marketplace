<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../api/axios'
import { computeGroupBySectors, computeMarketSummary } from '../../data/statisticsEngine'
import { useChartDefaults } from '../../composables/useChartDefaults'

const { formatEURFull, COYAG_RED, CHART_COLORS } = useChartDefaults()

const loading = ref(true)
const stats = ref(null)
const businesses = ref([])
const leads = ref([])
const clients = ref([])

onMounted(async () => {
  try {
    const { data: statsRaw } = await axios.get('/statistics')
    stats.value = statsRaw || null
  } catch (e) { console.error('statistics:', e) }

  try {
    const bizRes = await axios.get('/business/index', { params: { page: 1 } })
    const bizRaw = bizRes.data?.data || bizRes.data
    businesses.value = Array.isArray(bizRaw) ? bizRaw : []
  } catch (e) { console.error('businesses:', e); businesses.value = [] }

  try {
    const { data: leadsRaw } = await axios.get('/leads')
    leads.value = Array.isArray(leadsRaw) ? leadsRaw : []
  } catch (e) { console.error('leads:', e); leads.value = [] }

  try {
    const { data: clientsRaw } = await axios.get('/client')
    clients.value = Array.isArray(clientsRaw) ? clientsRaw : []
  } catch (e) { console.error('clients:', e); clients.value = [] }

  loading.value = false
})

const summary = computed(() => computeMarketSummary(businesses.value))

// Revenue chart — simulated 12 months
const revenueSeries = ref([{
  name: 'Ingresos',
  data: [12400, 15800, 14200, 18600, 22100, 19500, 24800, 28300, 26700, 31200, 35600, 45200],
}])
const revenueOptions = {
  chart: { type: 'area', height: 320, fontFamily: "'Inter', sans-serif", toolbar: { show: false } },
  colors: [COYAG_RED],
  stroke: { curve: 'smooth', width: 2 },
  dataLabels: { enabled: false },
  xaxis: { categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'] },
  yaxis: { labels: { formatter: (v) => Math.round(v / 1000) + 'K' } },
  fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
  tooltip: { y: { formatter: (v) => formatEURFull(v) } },
  grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
}

// Donut — by business type
const donutSeries = computed(() => {
  const dist = summary.value.typeDistribution || {}
  return Object.values(dist)
})
const donutOptions = computed(() => ({
  chart: { type: 'donut', height: 280 },
  labels: Object.keys(summary.value.typeDistribution || {}),
  colors: [COYAG_RED, '#7367F0', '#28C76F'],
  legend: { position: 'bottom', fontSize: '12px' },
  dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
  plotOptions: { pie: { donut: { size: '55%' } } },
}))

// Bar — business registrations (simulated monthly)
const regSeries = ref([{ name: 'Altas', data: [8, 12, 10, 18, 15, 22, 20, 25, 19, 28, 24, 30] }])
const regOptions = {
  chart: { type: 'bar', height: 280, fontFamily: "'Inter', sans-serif", toolbar: { show: false } },
  colors: ['#28C76F'],
  plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
  dataLabels: { enabled: false },
  xaxis: { categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'] },
  grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
}

// Lead status colors
const statusColors = {
  nuevo: 'bg-blue-100 text-blue-700',
  contactado: 'bg-yellow-100 text-yellow-700',
  cualificado: 'bg-purple-100 text-purple-700',
  negociando: 'bg-orange-100 text-orange-700',
  cerrado: 'bg-green-100 text-green-700',
  perdido: 'bg-red-100 text-red-700',
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <div class="animate-fade-in">
    <!-- Header -->
    <div class="mb-8 p-4 md:p-8 bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-lg relative overflow-hidden">
      <div class="absolute -right-20 -top-20 w-64 h-64 bg-slate-700 rounded-full blur-3xl opacity-50 z-0 hidden md:block"></div>
      <div class="relative z-10">
        <h1 class="text-lg md:text-2xl font-extrabold text-white">Centro de Inteligencia</h1>
        <p class="mt-1 text-sm text-slate-300">Rendimiento de la plataforma y analisis de mercado en tiempo real.</p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="i in 4" :key="i" class="c-card p-6"><div class="skeleton h-20 w-full rounded"></div></div>
    </div>

    <template v-else>
      <!-- KPIs -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="c-card p-5 border-l-4 border-l-[var(--color-primary)]">
          <p class="text-xs font-bold text-gray-400 uppercase">Total Negocios</p>
          <p class="text-xl md:text-3xl font-extrabold text-gray-900 mt-1">{{ stats?.totalBusinesses || summary.totalBusinesses }}</p>
          <p class="text-xs text-green-500 font-bold mt-1">{{ stats?.activeBusinesses || summary.totalBusinesses }} activos</p>
        </div>
        <div class="c-card p-5 border-l-4 border-l-green-500">
          <p class="text-xs font-bold text-gray-400 uppercase">Clientes</p>
          <p class="text-xl md:text-3xl font-extrabold text-gray-900 mt-1">{{ clients.length || stats?.activeClients }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ stats?.premiumClients || 0 }} premium</p>
        </div>
        <div class="c-card p-5 border-l-4 border-l-blue-500">
          <p class="text-xs font-bold text-gray-400 uppercase">Leads</p>
          <p class="text-xl md:text-3xl font-extrabold text-gray-900 mt-1">{{ leads.length || stats?.totalLeads }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ stats?.conversionRate || 0 }}% conversion</p>
        </div>
        <div class="c-card p-5 border-l-4 border-l-orange-500">
          <p class="text-xs font-bold text-gray-400 uppercase">Precio Medio</p>
          <p class="text-xl md:text-3xl font-extrabold text-gray-900 mt-1">{{ formatEURFull(summary.avgPrice) }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ summary.avgDaysOnMarket }}d en mercado</p>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Revenue Line -->
        <div class="c-card p-6 lg:col-span-2">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Ingresos mensuales estimados</h3>
          <apexchart type="area" height="320" :options="revenueOptions" :series="revenueSeries" />
        </div>

        <!-- Type Donut -->
        <div class="c-card p-6">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Distribucion por tipo</h3>
          <apexchart v-if="donutSeries.length" type="donut" height="280" :options="donutOptions" :series="donutSeries" />
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Registration Bar -->
        <div class="c-card p-6">
          <h3 class="text-sm font-bold text-gray-900 mb-4">Altas de negocios por mes</h3>
          <apexchart type="bar" height="280" :options="regOptions" :series="regSeries" />
        </div>

        <!-- Recent Leads Table -->
        <div class="c-card p-0 lg:col-span-2 overflow-hidden flex flex-col">
          <div class="p-5 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Ultimos leads</h3>
          </div>
          <div class="overflow-x-auto flex-1">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-gray-50 text-xs text-gray-400 uppercase">
                  <th class="py-3 px-5 font-bold">Nombre</th>
                  <th class="py-3 px-5 font-bold">Email</th>
                  <th class="py-3 px-5 font-bold">Estado</th>
                  <th class="py-3 px-5 font-bold">Fecha</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="lead in leads.slice(0, 6)" :key="lead.id" class="hover:bg-gray-50 transition-colors">
                  <td class="py-3 px-5 text-xs md:text-sm font-medium text-gray-900">{{ lead.name }}</td>
                  <td class="py-3 px-5 text-xs md:text-sm text-gray-500">{{ lead.email }}</td>
                  <td class="py-3 px-5">
                    <span :class="['text-xs font-bold px-2 py-0.5 rounded-full', statusColors[lead.status] || 'bg-gray-100']">
                      {{ lead.status }}
                    </span>
                  </td>
                  <td class="py-3 px-5 text-xs md:text-sm text-gray-400">{{ formatDate(lead.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="p-3 border-t border-gray-100 bg-gray-50 text-center">
            <router-link to="/admin/leads" class="text-xs font-bold text-[var(--color-primary)] hover:underline no-underline">
              Ver todos los leads
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
