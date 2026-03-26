<script setup>
import { ref, computed, watch } from 'vue'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
})

const loanAmount = ref(0)
const years = ref(5)
const interestRate = ref(6)
const downPayment = ref(20) // percentage

// Init loan amount from business price
watch(() => props.business.investment, (val) => {
  if (val) loanAmount.value = Math.round(val * (1 - downPayment.value / 100))
}, { immediate: true })

watch(downPayment, (pct) => {
  loanAmount.value = Math.round((props.business.investment || 0) * (1 - pct / 100))
})

const monthlyPayment = computed(() => {
  const P = loanAmount.value
  const r = interestRate.value / 100 / 12
  const n = years.value * 12
  if (!P || !r || !n) return 0
  return Math.round((P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1))
})

const totalPaid = computed(() => monthlyPayment.value * years.value * 12)
const totalInterest = computed(() => totalPaid.value - loanAmount.value)

const breakEvenMonths = computed(() => {
  const rent = props.business.rental || 0
  if (!rent || !monthlyPayment.value) return null
  // Assume net margin of 30% from the business revenue
  const netMonthly = rent * 0.3
  if (netMonthly <= 0) return null
  return Math.ceil(monthlyPayment.value / netMonthly)
})

const downPaymentAmount = computed(() => Math.round((props.business.investment || 0) * (downPayment.value / 100)))

function formatEur(n) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div>
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
      <AppIcon name="calculator" :size="16" class="text-indigo-500" />
      Simulador de Financiacion
    </h3>

    <!-- Sliders -->
    <div class="space-y-4 mb-5">
      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="text-gray-500 font-medium">Entrada</span>
          <span class="font-bold text-gray-900">{{ downPayment }}% ({{ formatEur(downPaymentAmount) }})</span>
        </div>
        <input v-model.number="downPayment" type="range" min="0" max="80" step="5" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="text-gray-500 font-medium">Plazo</span>
          <span class="font-bold text-gray-900">{{ years }} anos</span>
        </div>
        <input v-model.number="years" type="range" min="1" max="15" step="1" class="w-full" />
      </div>

      <div>
        <div class="flex justify-between text-xs mb-1">
          <span class="text-gray-500 font-medium">Tipo de interes</span>
          <span class="font-bold text-gray-900">{{ interestRate }}%</span>
        </div>
        <input v-model.number="interestRate" type="range" min="1" max="15" step="0.5" class="w-full" />
      </div>
    </div>

    <!-- Results -->
    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
      <!-- Monthly payment highlight -->
      <div class="text-center py-2">
        <div class="text-[10px] text-gray-400 uppercase font-semibold mb-1">Cuota mensual</div>
        <div class="text-3xl font-extrabold text-[var(--color-primary)]">{{ formatEur(monthlyPayment) }}</div>
        <div class="text-[10px] text-gray-400 mt-0.5">/mes durante {{ years }} anos</div>
      </div>

      <hr class="border-gray-200" />

      <!-- Breakdown -->
      <div class="grid grid-cols-2 gap-3 text-center">
        <div>
          <div class="text-xs text-gray-500">Financiado</div>
          <div class="text-sm font-bold text-gray-900">{{ formatEur(loanAmount) }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Total intereses</div>
          <div class="text-sm font-bold text-amber-600">{{ formatEur(totalInterest) }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Total pagado</div>
          <div class="text-sm font-bold text-gray-900">{{ formatEur(totalPaid) }}</div>
        </div>
        <div v-if="breakEvenMonths">
          <div class="text-xs text-gray-500">Break-even</div>
          <div class="text-sm font-bold text-green-600">{{ breakEvenMonths }} meses</div>
        </div>
      </div>

      <!-- Visual bar: principal vs interest -->
      <div class="mt-2">
        <div class="flex h-2 rounded-full overflow-hidden">
          <div class="bg-[var(--color-primary)] transition-all" :style="{ width: (loanAmount / totalPaid * 100) + '%' }"></div>
          <div class="bg-amber-400 transition-all" :style="{ width: (totalInterest / totalPaid * 100) + '%' }"></div>
        </div>
        <div class="flex justify-between text-[9px] text-gray-400 mt-1">
          <span>Capital {{ Math.round(loanAmount / totalPaid * 100) }}%</span>
          <span>Intereses {{ Math.round(totalInterest / totalPaid * 100) }}%</span>
        </div>
      </div>
    </div>
  </div>
</template>
