<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  business: { type: Object, default: null },
})

// Inputs with defaults from business
const purchasePrice = ref(props.business?.investment || props.business?.financials?.price || 150000)
const monthlyRent = ref(props.business?.rental || props.business?.financials?.rent || 2000)
const monthlyRevenue = ref(8000)
const monthlyExpenses = ref(3500)
const financingPct = ref(0)
const interestRate = ref(4.5)

// Computed metrics
const monthlyMortgage = computed(() => {
  if (financingPct.value <= 0) return 0
  const principal = purchasePrice.value * (financingPct.value / 100)
  const monthlyRate = interestRate.value / 100 / 12
  const months = 240 // 20 years
  if (monthlyRate === 0) return principal / months
  return (principal * monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1)
})

const totalMonthlyCost = computed(() => monthlyRent.value + monthlyExpenses.value + monthlyMortgage.value)
const monthlyCashflow = computed(() => monthlyRevenue.value - totalMonthlyCost.value)
const annualCashflow = computed(() => monthlyCashflow.value * 12)
const totalInvestment = computed(() => purchasePrice.value * (1 - financingPct.value / 100))
const monthsBreakeven = computed(() => {
  if (monthlyCashflow.value <= 0) return Infinity
  return Math.ceil(totalInvestment.value / monthlyCashflow.value)
})
const roiAnnual = computed(() => {
  if (totalInvestment.value <= 0) return 0
  return ((annualCashflow.value / totalInvestment.value) * 100).toFixed(1)
})
const capRate = computed(() => {
  if (purchasePrice.value <= 0) return 0
  const noi = (monthlyRevenue.value - monthlyExpenses.value - monthlyRent.value) * 12
  return ((noi / purchasePrice.value) * 100).toFixed(1)
})
const priceRentRatio = computed(() => {
  if (monthlyRent.value <= 0) return 0
  return (purchasePrice.value / (monthlyRent.value * 12)).toFixed(1)
})

// ROI Rating
const roiRating = computed(() => {
  const roi = Number(roiAnnual.value)
  if (roi >= 20) return { label: 'Excelente', color: '#28C76F', pct: 100 }
  if (roi >= 12) return { label: 'Bueno', color: '#7367F0', pct: 75 }
  if (roi >= 5) return { label: 'Aceptable', color: '#FF9F43', pct: 50 }
  return { label: 'Deficiente', color: '#EA5455', pct: 25 }
})

function formatCurrency(n) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="space-y-6">
    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Calculadora ROI</h3>

    <!-- Inputs -->
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Precio compra</label>
        <input v-model.number="purchasePrice" type="number" class="c-input text-sm w-full" />
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Alquiler/mes</label>
        <input v-model.number="monthlyRent" type="number" class="c-input text-sm w-full" />
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Ingresos/mes</label>
        <input v-model.number="monthlyRevenue" type="number" class="c-input text-sm w-full" />
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Gastos/mes</label>
        <input v-model.number="monthlyExpenses" type="number" class="c-input text-sm w-full" />
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">% Financiacion</label>
        <input v-model.number="financingPct" type="number" min="0" max="100" class="c-input text-sm w-full" />
      </div>
      <div v-if="financingPct > 0">
        <label class="block text-xs font-bold text-gray-400 mb-1">Interes (%)</label>
        <input v-model.number="interestRate" type="number" step="0.1" class="c-input text-sm w-full" />
      </div>
    </div>

    <!-- ROI Gauge -->
    <div class="text-center py-4">
      <div class="relative inline-block">
        <svg width="140" height="80" viewBox="0 0 140 80">
          <!-- Background arc -->
          <path d="M 10 70 A 60 60 0 0 1 130 70" fill="none" stroke="#F3F4F6" stroke-width="10" stroke-linecap="round" />
          <!-- Value arc -->
          <path
            d="M 10 70 A 60 60 0 0 1 130 70"
            fill="none"
            :stroke="roiRating.color"
            stroke-width="10"
            stroke-linecap="round"
            :stroke-dasharray="`${roiRating.pct * 1.88} 188`"
          />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-end pb-1">
          <span class="text-2xl font-extrabold" :style="{ color: roiRating.color }">{{ roiAnnual }}%</span>
          <span class="text-xs font-bold" :style="{ color: roiRating.color }">{{ roiRating.label }}</span>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div class="space-y-2">
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Cashflow mensual</span>
        <span :class="['font-bold', monthlyCashflow >= 0 ? 'text-green-600' : 'text-red-600']">{{ formatCurrency(monthlyCashflow) }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Break-even</span>
        <span class="font-bold text-gray-900">{{ monthsBreakeven === Infinity ? 'N/A' : monthsBreakeven + ' meses' }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">ROI anual</span>
        <span class="font-bold" :style="{ color: roiRating.color }">{{ roiAnnual }}%</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Cap Rate</span>
        <span class="font-bold text-gray-900">{{ capRate }}%</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Ratio Precio/Alquiler</span>
        <span class="font-bold text-gray-900">{{ priceRentRatio }}x</span>
      </div>
      <div v-if="financingPct > 0" class="flex justify-between text-sm">
        <span class="text-gray-500">Cuota hipoteca/mes</span>
        <span class="font-bold text-gray-900">{{ formatCurrency(monthlyMortgage) }}</span>
      </div>
    </div>
  </div>
</template>
