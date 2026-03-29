<script setup>
import { ref, computed } from 'vue'
import { useAiStore } from '../../stores/ai'
import { computeInvestmentScore } from '../../data/statisticsEngine'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
  allBusinesses: { type: Array, default: () => [] },
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const aiStore = useAiStore()
const printing = ref(false)

const score = computed(() => computeInvestmentScore(props.business, props.allBusinesses))
const mc = computed(() => aiStore.marketContext)

function formatEur(n) {
  if (!n) return 'Consultar'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n)
}

function formatDate() {
  return new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function printReport() {
  printing.value = true
  await new Promise(r => setTimeout(r, 100))
  window.print()
  printing.value = false
}

const b = computed(() => props.business)
const sectors = computed(() => (b.value.sectors || []).map(s => s.name || s).join(', ') || 'Sin sector')
const location = computed(() => [b.value.municipality?.name, b.value.province?.name].filter(Boolean).join(', ') || 'No especificada')
const eurPerSqm = computed(() => b.value.size > 0 ? Math.round((b.value.investment || 0) / b.value.size) : 0)
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[1200] flex items-center justify-center p-4 print:relative print:p-0" @click.self="emit('close')">
        <div class="absolute inset-0 bg-black/50 print:hidden" @click="emit('close')"></div>

        <div class="relative bg-white w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl print:max-w-none print:max-h-none print:shadow-none print:rounded-none" id="business-report">

          <!-- Print controls (hidden in print) -->
          <div class="sticky top-0 z-10 bg-white border-b border-gray-100 p-4 flex justify-between items-center print:hidden">
            <h3 class="text-sm font-bold text-gray-900">Vista previa del informe</h3>
            <div class="flex gap-2">
              <button @click="printReport" :disabled="printing" class="c-btn c-btn--primary text-sm px-4">
                <AppIcon name="download" :size="14" />
                {{ printing ? 'Preparando...' : 'Imprimir / PDF' }}
              </button>
              <button @click="emit('close')" class="c-btn c-btn--ghost text-sm">Cerrar</button>
            </div>
          </div>

          <!-- Report Content -->
          <div class="p-8 space-y-6 text-gray-800 print:p-12">

            <!-- Header -->
            <div class="flex items-start justify-between border-b-2 border-[var(--color-primary)] pb-4">
              <div>
                <p class="text-xs font-bold text-[var(--color-primary)] uppercase tracking-wider mb-1">Informe de Negocio</p>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ b.name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Ref: {{ b.id_code || b.id }} · {{ formatDate() }}</p>
              </div>
              <div class="text-right">
                <p class="text-2xl font-extrabold text-[var(--color-primary)]">{{ formatEur(b.investment) }}</p>
                <p class="text-xs text-gray-400">Inversión requerida</p>
              </div>
            </div>

            <!-- Summary Grid -->
            <div class="grid grid-cols-4 gap-4">
              <div class="border border-gray-200 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">Superficie</p>
                <p class="text-lg font-bold">{{ b.size || '--' }} m2</p>
              </div>
              <div class="border border-gray-200 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">Alquiler/mes</p>
                <p class="text-lg font-bold">{{ formatEur(b.rental) }}</p>
              </div>
              <div class="border border-gray-200 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">EUR/m2</p>
                <p class="text-lg font-bold">{{ eurPerSqm ? formatEur(eurPerSqm) : '--' }}</p>
              </div>
              <div class="border border-gray-200 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">Score Inversión</p>
                <p class="text-lg font-bold">{{ score }}/100</p>
              </div>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-2 gap-6">
              <div>
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Datos del negocio</h3>
                <table class="w-full text-sm">
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Tipo</td><td class="py-2 font-semibold text-right">{{ b.business_type?.name || 'Traspaso' }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Sector</td><td class="py-2 font-semibold text-right">{{ sectors }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Ubicación</td><td class="py-2 font-semibold text-right">{{ location }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Dias en mercado</td><td class="py-2 font-semibold text-right">{{ b.days_on_market || 0 }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Visitas</td><td class="py-2 font-semibold text-right">{{ b.times_viewed || 0 }}</td></tr>
                  <tr><td class="py-2 text-gray-500">Estado</td><td class="py-2 font-semibold text-right">{{ b.flag_active ? 'Activo' : 'Cerrado' }}</td></tr>
                </table>
              </div>

              <div>
                <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Características</h3>
                <table class="w-full text-sm">
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Terraza</td><td class="py-2 font-semibold text-right">{{ b.terrace ? 'Si' : 'No' }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Salida de humos</td><td class="py-2 font-semibold text-right">{{ b.smoke_outlet ? 'Si' : 'No' }}</td></tr>
                  <tr class="border-b border-gray-100"><td class="py-2 text-gray-500">Aire acondicionado</td><td class="py-2 font-semibold text-right">{{ b.air_conditioning ? 'Si' : 'No' }}</td></tr>
                  <tr><td class="py-2 text-gray-500">Almacen</td><td class="py-2 font-semibold text-right">{{ b.warehouse ? 'Si' : 'No' }}</td></tr>
                </table>
              </div>
            </div>

            <!-- Market Context -->
            <div v-if="mc">
              <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Contexto de mercado</h3>
              <div class="grid grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                  <p class="text-xs text-gray-400 mb-1">Percentil precio</p>
                  <p class="text-lg font-bold text-indigo-600">{{ mc.pricePercentile || '--' }}</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                  <p class="text-xs text-gray-400 mb-1">Media sector</p>
                  <p class="text-lg font-bold">{{ formatEur(mc.sectorAvgPrice) }}</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                  <p class="text-xs text-gray-400 mb-1">Indice demanda</p>
                  <p class="text-lg font-bold">{{ mc.demandIndex ? mc.demandIndex.toFixed(1) + 'x' : '--' }}</p>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div>
              <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Descripción</h3>
              <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                {{ b.description || 'No hay descripción disponible.' }}
              </p>
            </div>

            <!-- ROI estimate -->
            <div class="bg-gray-50 rounded-lg p-4 print:border print:border-gray-200">
              <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Estimacion financiera</h3>
              <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                  <p class="text-xs text-gray-400 mb-1">Coste anual alquiler</p>
                  <p class="text-lg font-bold">{{ formatEur((b.rental || 0) * 12) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-400 mb-1">Break-even (60% margen)</p>
                  <p class="text-lg font-bold text-indigo-600">
                    {{ b.rental > 0 ? Math.ceil(b.investment / (b.rental * 0.6)) + ' meses' : 'N/A' }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-400 mb-1">ROI anual estimado</p>
                  <p class="text-lg font-bold text-green-600">
                    {{ b.rental > 0 && b.investment > 0 ? (((b.rental * 12 * 0.6) / b.investment) * 100).toFixed(1) + '%' : 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="text-center border-t border-gray-200 pt-4">
              <p class="text-xs text-gray-400">
                Informe generado por COYAG VPDN · {{ formatDate() }} · Este informe es orientativo y no constituye una oferta vinculante.
              </p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style>
@media print {
  body > *:not(#business-report) {
    display: none !important;
  }
  #business-report {
    position: static !important;
    width: 100% !important;
    max-width: none !important;
    box-shadow: none !important;
  }
}
</style>
