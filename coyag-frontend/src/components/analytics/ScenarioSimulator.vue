<script setup>
import { ref, computed, watch } from 'vue'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
})

const basePrice = computed(() => props.business.investment || 0)
const baseRent = computed(() => props.business.rental || 0)

// Scenario sliders
const priceAdjust = ref(0)       // -30% to +30%
const rentAdjust = ref(0)        // -30% to +30%
const occupancy = ref(90)        // 50-100%
const monthlyExpenses = ref(500) // 0-5000
const interestRate = ref(5)      // 1-12%

// Derived values
const adjustedPrice = computed(() => Math.round(basePrice.value * (1 + priceAdjust.value / 100)))
const adjustedRent = computed(() => Math.round(baseRent.value * (1 + rentAdjust.value / 100)))

const monthlyGross = computed(() => Math.round(adjustedRent.value * (occupancy.value / 100)))
const monthlyNet = computed(() => Math.max(0, monthlyGross.value - monthlyExpenses.value))
const annualNet = computed(() => monthlyNet.value * 12)

const roiPercent = computed(() => {
  if (!adjustedPrice.value || adjustedPrice.value <= 0) return 0
  return ((annualNet.value / adjustedPrice.value) * 100).toFixed(1)
})

const breakEvenMonths = computed(() => {
  if (monthlyNet.value <= 0) return null
  return Math.ceil(adjustedPrice.value / monthlyNet.value)
})

const breakEvenYears = computed(() => {
  if (!breakEvenMonths.value) return null
  const y = Math.floor(breakEvenMonths.value / 12)
  const m = breakEvenMonths.value % 12
  return y > 0 ? `${y}a ${m}m` : `${m} meses`
})

// Monthly loan payment if financed
const monthlyLoan = computed(() => {
  const P = adjustedPrice.value
  const r = interestRate.value / 100 / 12
  const n = 10 * 12 // 10 years
  if (r === 0 || P === 0) return 0
  return Math.round((P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1))
})

const cashFlowWithLoan = computed(() => monthlyNet.value - monthlyLoan.value)

// Scenario presets
function applyOptimistic() {
  priceAdjust.value = -10
  rentAdjust.value = 10
  occupancy.value = 95
  monthlyExpenses.value = 400
}
function applyPessimistic() {
  priceAdjust.value = 10
  rentAdjust.value = -15
  occupancy.value = 70
  monthlyExpenses.value = 800
}
function applyReset() {
  priceAdjust.value = 0
  rentAdjust.value = 0
  occupancy.value = 90
  monthlyExpenses.value = 500
  interestRate.value = 5
}

function formatEur(n) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}

// ROI color
const roiColor = computed(() => {
  const r = parseFloat(roiPercent.value)
  if (r >= 10) return '#10B981'
  if (r >= 5) return '#6366F1'
  if (r >= 2) return '#F59E0B'
  return '#EF4444'
})
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
        <AppIcon name="adjustments" :size="16" class="text-indigo-500" />
        Simulador de Escenarios
      </h3>
    </div>

    <!-- Presets -->
    <div class="flex gap-2 mb-5">
      <button @click="applyOptimistic" class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 hover:bg-green-100 transition-colors cursor-pointer border-none">
        Optimista
      </button>
      <button @click="applyPessimistic" class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-700 hover:bg-red-100 transition-colors cursor-pointer border-none">
        Pesimista
      </button>
      <button @click="applyReset" class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors cursor-pointer border-none">
        Reiniciar
      </button>
    </div>

    <!-- Sliders -->
    <div class="space-y-4">
      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="font-semibold text-gray-600">Precio de compra</span>
          <span class="font-bold" :class="priceAdjust < 0 ? 'text-green-600' : priceAdjust > 0 ? 'text-red-600' : 'text-gray-900'">
            {{ priceAdjust > 0 ? '+' : '' }}{{ priceAdjust }}% → {{ formatEur(adjustedPrice) }}
          </span>
        </div>
        <input type="range" v-model.number="priceAdjust" min="-30" max="30" step="5" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="font-semibold text-gray-600">Alquiler mensual</span>
          <span class="font-bold" :class="rentAdjust > 0 ? 'text-green-600' : rentAdjust < 0 ? 'text-red-600' : 'text-gray-900'">
            {{ rentAdjust > 0 ? '+' : '' }}{{ rentAdjust }}% → {{ formatEur(adjustedRent) }}
          </span>
        </div>
        <input type="range" v-model.number="rentAdjust" min="-30" max="30" step="5" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="font-semibold text-gray-600">Ocupacion</span>
          <span class="font-bold text-gray-900">{{ occupancy }}%</span>
        </div>
        <input type="range" v-model.number="occupancy" min="50" max="100" step="5" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="font-semibold text-gray-600">Gastos mensuales</span>
          <span class="font-bold text-gray-900">{{ formatEur(monthlyExpenses) }}</span>
        </div>
        <input type="range" v-model.number="monthlyExpenses" min="0" max="5000" step="100" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="font-semibold text-gray-600">Interes financiacion</span>
          <span class="font-bold text-gray-900">{{ interestRate }}%</span>
        </div>
        <input type="range" v-model.number="interestRate" min="1" max="12" step="0.5" class="w-full" />
      </div>
    </div>

    <!-- Results -->
    <div class="mt-5 pt-4 border-t border-gray-100 space-y-3">
      <!-- ROI -->
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500">ROI anual</span>
        <span class="text-lg font-extrabold" :style="{ color: roiColor }">{{ roiPercent }}%</span>
      </div>

      <!-- Monthly income -->
      <div class="bg-gray-50 rounded-xl p-3 space-y-2">
        <div class="flex justify-between text-xs">
          <span class="text-gray-500">Ingreso bruto/mes</span>
          <span class="font-bold text-gray-900">{{ formatEur(monthlyGross) }}</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-gray-500">Gastos/mes</span>
          <span class="font-bold text-red-500">-{{ formatEur(monthlyExpenses) }}</span>
        </div>
        <div class="flex justify-between text-xs border-t border-gray-200 pt-2">
          <span class="font-semibold text-gray-700">Neto/mes</span>
          <span class="font-bold" :class="monthlyNet > 0 ? 'text-green-600' : 'text-red-600'">{{ formatEur(monthlyNet) }}</span>
        </div>
      </div>

      <!-- Break even -->
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500">Break-even</span>
        <span class="text-sm font-bold" :class="breakEvenMonths ? 'text-indigo-600' : 'text-gray-400'">
          {{ breakEvenYears || 'N/A' }}
        </span>
      </div>

      <!-- Financed scenario -->
      <div class="bg-indigo-50 rounded-xl p-3 space-y-2">
        <p class="text-[10px] font-bold text-indigo-500 uppercase">Si financias (10 anos)</p>
        <div class="flex justify-between text-xs">
          <span class="text-gray-600">Cuota mensual</span>
          <span class="font-bold text-gray-900">{{ formatEur(monthlyLoan) }}</span>
        </div>
        <div class="flex justify-between text-xs border-t border-indigo-100 pt-2">
          <span class="font-semibold text-gray-700">Cash flow/mes</span>
          <span class="font-bold" :class="cashFlowWithLoan >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ formatEur(cashFlowWithLoan) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
