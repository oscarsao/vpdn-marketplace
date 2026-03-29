<script setup>
import { ref, computed } from 'vue'
import { computeGroupBySectors } from '../../data/statisticsEngine'
import AppIcon from '../../components/ui/AppIcon.vue'
import axios from '../../api/axios'

const step = ref(1)
const loading = ref(false)
const result = ref(null)
const allBusinesses = ref([])

// Form data
const form = ref({
  sector: '',
  province: '',
  size: null,
  rental: null,
  hasTerraza: false,
  hasSalidaHumos: false,
  yearsOperating: null,
  monthlyRevenue: null,
})

const sectors = ref([])
const provinces = ref([])

// Load data on mount
import { onMounted } from 'vue'
onMounted(async () => {
  try {
    const { data: sectorsRaw } = await axios.get('/sector')
    sectors.value = Array.isArray(sectorsRaw) ? sectorsRaw : []
  } catch (e) { console.error('sectors:', e); sectors.value = [] }

  try {
    const { data: provsRaw } = await axios.get('/province')
    provinces.value = Array.isArray(provsRaw) ? provsRaw : []
  } catch (e) { console.error('provinces:', e); provinces.value = [] }

  try {
    const bizRes = await axios.get('/business/index', { params: { page: 1 } })
    const bizRaw = bizRes.data?.data || bizRes.data
    allBusinesses.value = Array.isArray(bizRaw) ? bizRaw : []
  } catch (e) { console.error('businesses:', e); allBusinesses.value = [] }
})

function nextStep() {
  if (step.value < 3) step.value++
  else runValuation()
}
function prevStep() {
  if (step.value > 1) step.value--
}

function runValuation() {
  loading.value = true

  setTimeout(() => {
    const f = form.value
    const sectorGroups = computeGroupBySectors(allBusinesses.value)
    const mySectorGroup = sectorGroups.find(s =>
      s.sectors_segment.toLowerCase().includes(f.sector.toLowerCase())
    ) || sectorGroups[0]

    // Filter comparables
    let comparables = allBusinesses.value
    if (f.sector) {
      comparables = comparables.filter(b =>
        (b.sectors || []).some(s => (s.name || s).toLowerCase().includes(f.sector.toLowerCase()))
      )
    }
    if (f.province) {
      comparables = comparables.filter(b =>
        (b.province?.name || '').toLowerCase().includes(f.province.toLowerCase())
      )
    }
    if (comparables.length < 3) comparables = allBusinesses.value

    // Calculate valuation using multiple methods
    const prices = comparables.map(b => b.investment || 0).filter(v => v > 0).sort((a, b) => a - b)
    const sqmPrices = comparables.filter(b => b.size > 0).map(b => (b.investment || 0) / b.size).sort((a, b) => a - b)

    const median = (arr) => {
      if (arr.length === 0) return 0
      const mid = Math.floor(arr.length / 2)
      return arr.length % 2 === 0 ? (arr[mid - 1] + arr[mid]) / 2 : arr[mid]
    }

    // Method 1: EUR/m2 based
    const medianSqm = median(sqmPrices)
    const sqmValuation = f.size ? Math.round(medianSqm * f.size) : 0

    // Method 2: Revenue multiplier (common: 1.5-3x annual)
    const revenueValuation = f.monthlyRevenue ? Math.round(f.monthlyRevenue * 12 * 2) : 0

    // Method 3: Market comparison (median of comparable sector)
    const marketValuation = Math.round(median(prices))

    // Adjustments
    let adjustmentFactor = 1.0
    const adjustments = []
    if (f.hasTerraza) { adjustmentFactor += 0.08; adjustments.push({ label: 'Terraza', pct: '+8%', positive: true }) }
    if (f.hasSalidaHumos) { adjustmentFactor += 0.05; adjustments.push({ label: 'Salida de humos', pct: '+5%', positive: true }) }
    if (f.yearsOperating && f.yearsOperating > 5) { adjustmentFactor += 0.1; adjustments.push({ label: 'Negocio establecido (+5 anos)', pct: '+10%', positive: true }) }
    else if (f.yearsOperating && f.yearsOperating < 2) { adjustmentFactor -= 0.1; adjustments.push({ label: 'Negocio joven (<2 anos)', pct: '-10%', positive: false }) }

    // Weighted average of methods
    const methodValues = [sqmValuation, revenueValuation, marketValuation].filter(v => v > 0)
    const baseVal = methodValues.length > 0 ? methodValues.reduce((a, b) => a + b, 0) / methodValues.length : marketValuation

    const estimatedValue = Math.round(baseVal * adjustmentFactor)
    const rangeLow = Math.round(estimatedValue * 0.85)
    const rangeHigh = Math.round(estimatedValue * 1.15)

    result.value = {
      estimated: estimatedValue,
      rangeLow,
      rangeHigh,
      methods: [
        sqmValuation > 0 ? { name: 'Valoracion por m2', value: sqmValuation, desc: `${Math.round(medianSqm)} €/m2 × ${f.size} m2` } : null,
        revenueValuation > 0 ? { name: 'Multiplicador ingresos', value: revenueValuation, desc: `${formatEur(f.monthlyRevenue)}/mes × 24 meses` } : null,
        { name: 'Comparacion de mercado', value: marketValuation, desc: `Mediana de ${comparables.length} negocios comparables` },
      ].filter(Boolean),
      adjustments,
      comparablesCount: comparables.length,
      sectorAvg: mySectorGroup?.avg_investment || 0,
      percentile: prices.length > 0
        ? Math.round((prices.filter(p => p <= estimatedValue).length / prices.length) * 100)
        : 50,
    }

    loading.value = false
    step.value = 4
  }, 1200)
}

function formatEur(n) {
  if (!n) return '0 €'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}

function restart() {
  step.value = 1
  result.value = null
}
</script>

<template>
  <div class="animate-fade-in max-w-2xl mx-auto px-1 md:px-0 pb-20">
    <div class="text-center mb-6 md:mb-8">
      <h1 class="text-xl md:text-2xl font-extrabold text-gray-900">Valoracion de tu Negocio</h1>
      <p class="text-xs md:text-sm text-gray-500 mt-1">Descubre cuanto vale tu negocio basado en datos de mercado reales</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex items-center justify-center gap-1.5 md:gap-2 mb-6 md:mb-8">
      <div v-for="s in 4" :key="s" class="flex items-center">
        <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors',
          step >= s ? 'bg-[var(--color-primary)] text-white' : 'bg-gray-200 text-gray-400']">
          <AppIcon v-if="step > s" name="check" :size="14" />
          <span v-else>{{ s }}</span>
        </div>
        <div v-if="s < 4" :class="['w-8 h-0.5 transition-colors', step > s ? 'bg-[var(--color-primary)]' : 'bg-gray-200']"></div>
      </div>
    </div>

    <!-- Step 1: Basic Info -->
    <div v-if="step === 1" class="c-card p-4 md:p-6 space-y-4 md:space-y-5">
      <h2 class="text-lg font-bold text-gray-900">Informacion basica</h2>

      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Sector del negocio *</label>
        <select v-model="form.sector" class="w-full c-input text-sm">
          <option value="">Seleccionar sector</option>
          <option v-for="s in sectors" :key="s.id" :value="s.name">{{ s.name }}</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Provincia *</label>
        <select v-model="form.province" class="w-full c-input text-sm">
          <option value="">Seleccionar provincia</option>
          <option v-for="p in provinces" :key="p.id" :value="p.name">{{ p.name }}</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Superficie (m2)</label>
          <input v-model.number="form.size" type="number" class="c-input text-sm w-full" placeholder="ej: 120" />
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alquiler mensual (€)</label>
          <input v-model.number="form.rental" type="number" class="c-input text-sm w-full" placeholder="ej: 2000" />
        </div>
      </div>

      <button @click="nextStep" :disabled="!form.sector" class="c-btn c-btn--primary w-full py-3">
        Siguiente
      </button>
    </div>

    <!-- Step 2: Characteristics -->
    <div v-if="step === 2" class="c-card p-4 md:p-6 space-y-4 md:space-y-5">
      <h2 class="text-lg font-bold text-gray-900">Caracteristicas del local</h2>

      <div class="space-y-3">
        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
          <input v-model="form.hasTerraza" type="checkbox" class="w-4 h-4 text-[var(--color-primary)] rounded" />
          <div>
            <p class="text-sm font-semibold text-gray-900">Terraza</p>
            <p class="text-xs text-gray-400">El local dispone de terraza exterior</p>
          </div>
        </label>
        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
          <input v-model="form.hasSalidaHumos" type="checkbox" class="w-4 h-4 text-[var(--color-primary)] rounded" />
          <div>
            <p class="text-sm font-semibold text-gray-900">Salida de humos</p>
            <p class="text-xs text-gray-400">Dispone de instalacion de extraccion de humos</p>
          </div>
        </label>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Anos operando</label>
        <input v-model.number="form.yearsOperating" type="number" class="c-input text-sm w-full" placeholder="ej: 5" />
      </div>

      <div class="flex gap-3">
        <button @click="prevStep" class="c-btn c-btn--outline flex-1 py-3">Atras</button>
        <button @click="nextStep" class="c-btn c-btn--primary flex-1 py-3">Siguiente</button>
      </div>
    </div>

    <!-- Step 3: Financials -->
    <div v-if="step === 3" class="c-card p-4 md:p-6 space-y-4 md:space-y-5">
      <h2 class="text-lg font-bold text-gray-900">Datos financieros (opcional)</h2>
      <p class="text-xs text-gray-400 -mt-3">Estos datos mejoran la precision de la valoracion</p>

      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Facturacion mensual (€)</label>
        <input v-model.number="form.monthlyRevenue" type="number" class="c-input text-sm w-full" placeholder="ej: 15000" />
      </div>

      <div class="flex gap-3">
        <button @click="prevStep" class="c-btn c-btn--outline flex-1 py-3">Atras</button>
        <button @click="nextStep" class="c-btn c-btn--primary flex-1 py-3">Calcular Valoracion</button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="c-card p-12 text-center">
      <div class="h-12 w-12 border-4 border-gray-200 border-t-[var(--color-primary)] rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-sm font-semibold text-gray-600">Analizando datos de mercado...</p>
      <p class="text-xs text-gray-400 mt-1">Comparando con {{ allBusinesses.length }} negocios</p>
    </div>

    <!-- Step 4: Results -->
    <div v-if="step === 4 && result" class="space-y-4 md:space-y-6">
      <!-- Main value -->
      <div class="c-card p-5 md:p-8 text-center bg-gradient-to-br from-white to-gray-50">
        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Valoracion estimada</p>
        <p class="text-3xl md:text-4xl font-extrabold text-[var(--color-primary)]">{{ formatEur(result.estimated) }}</p>
        <p class="text-xs md:text-sm text-gray-500 mt-2">
          Rango: <span class="font-bold">{{ formatEur(result.rangeLow) }}</span> — <span class="font-bold">{{ formatEur(result.rangeHigh) }}</span>
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 mt-4">
          <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-50 text-indigo-600">
            Percentil {{ result.percentile }}
          </span>
          <span class="text-xs text-gray-400">
            Basado en {{ result.comparablesCount }} negocios comparables
          </span>
        </div>
      </div>

      <!-- Methods breakdown -->
      <div class="c-card p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Metodos de valoracion</h3>
        <div class="space-y-3">
          <div v-for="m in result.methods" :key="m.name" class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
            <div>
              <p class="text-sm font-semibold text-gray-900">{{ m.name }}</p>
              <p class="text-[10px] text-gray-400">{{ m.desc }}</p>
            </div>
            <span class="text-sm font-bold text-gray-900">{{ formatEur(m.value) }}</span>
          </div>
        </div>
      </div>

      <!-- Adjustments -->
      <div v-if="result.adjustments.length" class="c-card p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Ajustes aplicados</h3>
        <div class="space-y-2">
          <div v-for="adj in result.adjustments" :key="adj.label"
            class="flex items-center justify-between py-2 px-3 rounded-lg"
            :class="adj.positive ? 'bg-green-50' : 'bg-red-50'">
            <span class="text-sm text-gray-700">{{ adj.label }}</span>
            <span class="text-sm font-bold" :class="adj.positive ? 'text-green-600' : 'text-red-600'">{{ adj.pct }}</span>
          </div>
        </div>
      </div>

      <!-- Context -->
      <div class="c-card p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Contexto de mercado</h3>
        <div class="grid grid-cols-2 gap-4">
          <div class="text-center p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-400 mb-1">Media del sector</p>
            <p class="text-lg font-bold text-gray-900">{{ formatEur(result.sectorAvg) }}</p>
          </div>
          <div class="text-center p-3 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-400 mb-1">Tu valoracion</p>
            <p class="text-lg font-bold text-[var(--color-primary)]">{{ formatEur(result.estimated) }}</p>
          </div>
        </div>
      </div>

      <div class="flex gap-3">
        <button @click="restart" class="c-btn c-btn--outline flex-1 py-3">Nueva valoracion</button>
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
