<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../api/axios'
import { computeGroupBySectors, computeMarketSummary, computeGroupByPriceSegment } from '../../data/statisticsEngine'
import { computeOpportunityScore } from '../../data/aiEngine'
import BusinessCard from '../../components/business/BusinessCard.vue'
import AppIcon from '../../components/ui/AppIcon.vue'
import ActivityFeed from '../../components/business/ActivityFeed.vue'

const router = useRouter()
const loading = ref(true)
const allBusinesses = ref([])

// Normalize API field names so statisticsEngine functions work correctly
function normalizeBiz(b) {
  if (!b || typeof b !== 'object') return b
  return {
    ...b,
    id: b.id_business || b.id,
    id_code: b.id_code_business || b.id_code,
    name: b.name_business || b.name,
    investment: b.investment_business || b.investment,
    rental: b.rental_business || b.rental,
    size: b.size_business || b.size,
    days_on_market: b.days_on_market || 0,
    times_viewed: b.times_viewed_business || b.times_viewed,
    province: b.province || { name: b.name_province },
    municipality: b.municipality || { name: b.name_municipality },
    business_type: b.business_type || { name: b.name_business_type },
    sectors: b.sectors || (b.sector ? b.sector.split(', ').map(s => ({ name: s })) : []),
    multimedia: b.multimedia || (b.business_images_string
      ? b.business_images_string.split(';').filter(Boolean).map((url, i) => ({ url, id: i }))
      : []),
  }
}

onMounted(async () => {
  try {
    const res = await axios.get('/business/index', { params: { page: 1 } })
    const raw = res.data?.businesses || res.data?.data || res.data
    allBusinesses.value = Array.isArray(raw) ? raw.map(normalizeBiz) : []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const marketSummary = computed(() => computeMarketSummary(allBusinesses.value))
const sectorGroups = computed(() => computeGroupBySectors(allBusinesses.value).slice(0, 6))

// Featured: top opportunity scores
const featuredBusinesses = computed(() => {
  return allBusinesses.value
    .map(b => {
      const opp = computeOpportunityScore(b, allBusinesses.value)
      return { ...b, oppScore: opp.score, oppLabel: opp.label, showOpp: opp.show }
    })
    .filter(b => b.showOpp)
    .sort((a, b) => b.oppScore - a.oppScore)
    .slice(0, 4)
})

// Latest businesses
const latestBusinesses = computed(() => {
  return [...allBusinesses.value]
    .sort((a, b) => (b.days_on_market || 999) - (a.days_on_market || 999))
    .sort((a, b) => (a.days_on_market || 999) - (b.days_on_market || 999))
    .slice(0, 4)
})

function formatEur(n) {
  if (!n) return '0 €'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M €'
  if (n >= 1000) return Math.round(n / 1000) + 'K €'
  return n + ' €'
}
</script>

<template>
  <div class="animate-fade-in">
    <div v-if="loading" class="min-h-[60vh] flex items-center justify-center">
      <div class="h-12 w-12 border-4 border-gray-200 border-t-[var(--color-primary)] rounded-full animate-spin"></div>
    </div>

    <template v-else>
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl md:rounded-2xl p-5 md:p-8 lg:p-12 mb-5 md:mb-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
          <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(255,255,255,0.05) 20px, rgba(255,255,255,0.05) 40px)"></div>
        </div>
        <div class="relative z-10">
          <h1 class="text-2xl md:text-3xl font-extrabold mb-2">Dashboard del Mercado</h1>
          <p class="text-gray-300 text-sm md:text-base mb-6">Visión general del mercado de traspasos, franquicias e inmuebles</p>

          <!-- KPI Cards -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 md:gap-4">
            <div class="bg-white/10 backdrop-blur rounded-lg md:rounded-xl p-3 md:p-4">
              <p class="text-[10px] md:text-xs text-gray-300 mb-0.5 md:mb-1">Total negocios</p>
              <p class="text-lg md:text-2xl font-extrabold">{{ allBusinesses.length }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg md:rounded-xl p-3 md:p-4">
              <p class="text-[10px] md:text-xs text-gray-300 mb-0.5 md:mb-1">Precio medio</p>
              <p class="text-lg md:text-2xl font-extrabold">{{ formatEur(marketSummary.avgPrice) }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg md:rounded-xl p-3 md:p-4">
              <p class="text-[10px] md:text-xs text-gray-300 mb-0.5 md:mb-1">Alquiler medio</p>
              <p class="text-lg md:text-2xl font-extrabold">{{ formatEur(marketSummary.avgRent) }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg md:rounded-xl p-3 md:p-4">
              <p class="text-[10px] md:text-xs text-gray-300 mb-0.5 md:mb-1">Sectores activos</p>
              <p class="text-lg md:text-2xl font-extrabold">{{ sectorGroups.length }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 md:gap-4 mb-5 md:mb-8">
        <button @click="router.push('/listado-general')" class="c-card p-3 md:p-4 text-center hover:shadow-md transition-shadow cursor-pointer border-none bg-white">
          <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-1.5 md:mb-2">
            <AppIcon name="store" :size="18" class="text-[var(--color-primary)]" />
          </div>
          <p class="text-xs md:text-sm font-bold text-gray-900">Explorar Negocios</p>
          <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">{{ allBusinesses.length }} disponibles</p>
        </button>
        <button @click="router.push('/estadisticas')" class="c-card p-3 md:p-4 text-center hover:shadow-md transition-shadow cursor-pointer border-none bg-white">
          <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-1.5 md:mb-2">
            <AppIcon name="chart-bar" :size="18" class="text-indigo-600" />
          </div>
          <p class="text-xs md:text-sm font-bold text-gray-900">Estadísticas</p>
          <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">Datos de mercado</p>
        </button>
        <button @click="router.push('/valoracion')" class="c-card p-3 md:p-4 text-center hover:shadow-md transition-shadow cursor-pointer border-none bg-white">
          <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-1.5 md:mb-2">
            <AppIcon name="calculator" :size="18" class="text-green-600" />
          </div>
          <p class="text-xs md:text-sm font-bold text-gray-900">Valorar mi negocio</p>
          <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">Valoración gratuita</p>
        </button>
        <button @click="router.push('/ai')" class="c-card p-3 md:p-4 text-center hover:shadow-md transition-shadow cursor-pointer border-none bg-white">
          <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-purple-50 flex items-center justify-center mx-auto mb-1.5 md:mb-2">
            <AppIcon name="sparkles" :size="18" class="text-purple-600" />
          </div>
          <p class="text-xs md:text-sm font-bold text-gray-900">Asistente IA</p>
          <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">Busca con IA</p>
        </button>
      </div>

      <!-- Opportunities -->
      <div v-if="featuredBusinesses.length" class="mb-5 md:mb-8">
        <div class="flex items-center justify-between mb-3 md:mb-4">
          <div>
            <h2 class="text-base md:text-lg font-extrabold text-gray-900">Oportunidades destacadas</h2>
            <p class="text-[10px] md:text-xs text-gray-400">Negocios con mejor relación calidad-precio</p>
          </div>
          <button @click="router.push('/listado-general')" class="c-btn c-btn--outline text-xs py-1.5 px-3">Ver todos</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
          <BusinessCard v-for="b in featuredBusinesses" :key="b.id" :business="b" />
        </div>
      </div>

      <!-- Sectors Overview -->
      <div class="mb-5 md:mb-8">
        <div class="flex items-center justify-between mb-3 md:mb-4">
          <div>
            <h2 class="text-base md:text-lg font-extrabold text-gray-900">Sectores del mercado</h2>
            <p class="text-[10px] md:text-xs text-gray-400">Distribución por tipo de negocio</p>
          </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5 md:gap-3">
          <div v-for="s in sectorGroups" :key="s.sectors_segment"
            class="c-card p-3 md:p-4 hover:shadow-md transition-shadow cursor-pointer"
            @click="router.push('/listado-general')">
            <p class="text-xs md:text-sm font-bold text-gray-900 mb-1.5 md:mb-2 line-clamp-1">{{ s.sectors_segment }}</p>
            <div class="space-y-0.5 md:space-y-1">
              <div class="flex justify-between text-[10px] md:text-xs">
                <span class="text-gray-400">Negocios</span>
                <span class="font-bold text-gray-900">{{ s.total }}</span>
              </div>
              <div class="flex justify-between text-[10px] md:text-xs">
                <span class="text-gray-400">Precio medio</span>
                <span class="font-bold text-gray-900">{{ formatEur(s.avg_investment) }}</span>
              </div>
              <div class="flex justify-between text-[10px] md:text-xs">
                <span class="text-gray-400">m2 medio</span>
                <span class="font-bold text-gray-900">{{ s.avg_size || '--' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Latest Listings -->
      <div class="mb-5 md:mb-8">
        <div class="flex items-center justify-between mb-3 md:mb-4">
          <div>
            <h2 class="text-base md:text-lg font-extrabold text-gray-900">Ultimas incorporaciones</h2>
            <p class="text-[10px] md:text-xs text-gray-400">Los negocios mas recientes en la plataforma</p>
          </div>
          <button @click="router.push('/listado-general')" class="c-btn c-btn--outline text-xs py-1.5 px-3">Ver todos</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
          <BusinessCard v-for="b in latestBusinesses" :key="b.id" :business="b" />
        </div>
      </div>

      <!-- Activity Feed -->
      <div class="c-card p-4 md:p-6">
        <ActivityFeed :businesses="allBusinesses" />
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
</style>
