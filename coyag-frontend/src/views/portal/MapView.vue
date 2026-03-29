<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../api/axios'
import AppIcon from '../../components/ui/AppIcon.vue'

const router = useRouter()
const loading = ref(true)
const allBusinesses = ref([])
const selectedProvince = ref(null)

// Normalize API field names
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
    province: b.province || { name: b.name_province },
    municipality: b.municipality || { name: b.name_municipality },
    business_type: b.business_type || { name: b.name_business_type },
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

// Province coordinates (approximate positions on a 500x400 Spain map)
const provinceCoords = {
  'Madrid': { x: 240, y: 200 },
  'Barcelona': { x: 400, y: 130 },
  'Valencia': { x: 350, y: 230 },
  'Sevilla': { x: 150, y: 310 },
  'Malaga': { x: 200, y: 340 },
  'Alicante': { x: 360, y: 260 },
  'Zaragoza': { x: 310, y: 140 },
  'Bilbao': { x: 210, y: 70 },
  'Murcia': { x: 340, y: 290 },
  'Palma de Mallorca': { x: 430, y: 230 },
  'Las Palmas': { x: 60, y: 380 },
  'Granada': { x: 210, y: 320 },
  'Cordoba': { x: 180, y: 290 },
  'A Coruna': { x: 60, y: 80 },
  'San Sebastian': { x: 240, y: 60 },
  'Valladolid': { x: 190, y: 140 },
  'Salamanca': { x: 140, y: 170 },
  'Toledo': { x: 210, y: 220 },
}

// Group businesses by province
const provinceData = computed(() => {
  const groups = {}
  allBusinesses.value.forEach(b => {
    const prov = b.province?.name || 'Desconocida'
    if (!groups[prov]) groups[prov] = { name: prov, businesses: [], totalInv: 0 }
    groups[prov].businesses.push(b)
    groups[prov].totalInv += b.investment || 0
  })

  return Object.values(groups).map(g => ({
    ...g,
    count: g.businesses.length,
    avgPrice: Math.round(g.totalInv / g.businesses.length),
    coords: provinceCoords[g.name] || null,
  })).sort((a, b) => b.count - a.count)
})

const maxCount = computed(() => Math.max(...provinceData.value.map(p => p.count), 1))

function markerSize(count) {
  const min = 20, max = 50
  return min + (count / maxCount.value) * (max - min)
}

function markerColor(avgPrice) {
  if (avgPrice <= 50000) return '#10B981'
  if (avgPrice <= 100000) return '#6366F1'
  if (avgPrice <= 200000) return '#F59E0B'
  return '#EF4444'
}

function selectProvince(prov) {
  selectedProvince.value = selectedProvince.value?.name === prov.name ? null : prov
}

function formatEur(n) {
  if (!n) return '0 €'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M €'
  if (n >= 1000) return Math.round(n / 1000) + 'K €'
  return n + ' €'
}

const selectedBusinesses = computed(() => {
  if (!selectedProvince.value) return []
  return selectedProvince.value.businesses.slice(0, 6)
})
</script>

<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Mapa de Negocios</h1>
        <p class="text-sm text-gray-500 mt-1">{{ allBusinesses.length }} negocios en {{ provinceData.length }} provincias</p>
      </div>
      <button @click="router.push('/listado-general')" class="c-btn c-btn--outline text-sm">
        <AppIcon name="list" :size="14" /> Vista listado
      </button>
    </div>

    <div v-if="loading" class="h-96 flex items-center justify-center">
      <div class="h-12 w-12 border-4 border-gray-200 border-t-[var(--color-primary)] rounded-full animate-spin"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Map Area -->
      <div class="lg:col-span-2 c-card p-6 relative">
        <!-- Interactive Map -->
        <div class="relative bg-gray-50 rounded-xl overflow-hidden" style="height: 420px">
          <!-- Spain outline (simplified) -->
          <svg viewBox="0 0 500 420" class="w-full h-full">
            <!-- Simplified Spain shape -->
            <path d="M60,60 L80,40 L160,30 L220,50 L280,40 L320,55 L370,50 L420,60 L450,100 L460,130 L440,160 L430,200 L450,240 L420,280 L380,300 L350,310 L300,330 L250,350 L200,360 L170,340 L130,330 L100,300 L80,260 L60,220 L50,180 L40,140 L50,100 Z"
              fill="#F8FAFC" stroke="#E2E8F0" stroke-width="2" />

            <!-- Province markers -->
            <g v-for="prov in provinceData.filter(p => p.coords)" :key="prov.name"
              class="cursor-pointer" @click="selectProvince(prov)">
              <circle
                :cx="prov.coords.x" :cy="prov.coords.y"
                :r="markerSize(prov.count) / 2"
                :fill="markerColor(prov.avgPrice)"
                :opacity="selectedProvince?.name === prov.name ? 1 : 0.7"
                :stroke="selectedProvince?.name === prov.name ? '#1E1E2D' : 'white'"
                :stroke-width="selectedProvince?.name === prov.name ? 3 : 2"
                class="transition-all duration-300 hover:opacity-100"
              />
              <text :x="prov.coords.x" :y="prov.coords.y + 1" text-anchor="middle" dominant-baseline="middle"
                fill="white" font-size="10" font-weight="700">
                {{ prov.count }}
              </text>
            </g>
          </svg>

          <!-- Legend -->
          <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur rounded-lg p-2.5 text-[10px] space-y-1">
            <p class="font-bold text-gray-600 mb-1">Precio medio</p>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span> &lt;50K €</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#6366F1]"></span> 50-100K €</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span> 100-200K €</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span> &gt;200K €</div>
          </div>
        </div>

        <!-- Selected Province Detail -->
        <div v-if="selectedProvince" class="mt-4 p-4 bg-gray-50 rounded-xl">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-900">{{ selectedProvince.name }}</h3>
            <button @click="selectedProvince = null" class="p-1 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer">
              <AppIcon name="x-mark" :size="14" />
            </button>
          </div>
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="text-center">
              <p class="text-xs text-gray-400">Negocios</p>
              <p class="text-lg font-bold text-gray-900">{{ selectedProvince.count }}</p>
            </div>
            <div class="text-center">
              <p class="text-xs text-gray-400">Precio medio</p>
              <p class="text-lg font-bold text-gray-900">{{ formatEur(selectedProvince.avgPrice) }}</p>
            </div>
            <div class="text-center">
              <p class="text-xs text-gray-400">% del total</p>
              <p class="text-lg font-bold text-indigo-600">{{ Math.round((selectedProvince.count / allBusinesses.length) * 100) }}%</p>
            </div>
          </div>
          <!-- Business preview cards -->
          <div v-if="selectedBusinesses.length" class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <router-link v-for="b in selectedBusinesses" :key="b.id"
              :to="`/negocio/${b.id_code || b.id}`"
              class="block p-3 bg-white rounded-lg hover:shadow-md transition-shadow no-underline">
              <p class="text-xs font-bold text-gray-900 line-clamp-1">{{ b.name }}</p>
              <p class="text-xs text-[var(--color-primary)] font-bold mt-1">{{ formatEur(b.investment) }}</p>
              <p class="text-[10px] text-gray-400">{{ b.size || '--' }} m2</p>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Province List Sidebar -->
      <div class="c-card p-4">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Provincias</h3>
        <div class="space-y-1 max-h-[500px] overflow-y-auto">
          <button v-for="prov in provinceData" :key="prov.name"
            @click="selectProvince(prov)"
            :class="['w-full text-left p-3 rounded-lg flex items-center justify-between transition-colors border-none cursor-pointer',
              selectedProvince?.name === prov.name ? 'bg-indigo-50' : 'bg-white hover:bg-gray-50']">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full shrink-0" :style="{ backgroundColor: markerColor(prov.avgPrice) }"></span>
              <div>
                <p class="text-sm font-semibold text-gray-900">{{ prov.name }}</p>
                <p class="text-[10px] text-gray-400">{{ formatEur(prov.avgPrice) }} medio</p>
              </div>
            </div>
            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ prov.count }}</span>
          </button>
        </div>
      </div>
    </div>
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
