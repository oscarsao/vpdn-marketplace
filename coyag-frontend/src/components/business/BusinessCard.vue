<script setup>
import { computed, ref } from 'vue'
import { useBusinessStore } from '../../stores/business'
import { useCompareStore } from '../../stores/compare'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true }
})

const showQuickView = ref(false)
let hoverTimer = null
function onMouseEnter() {
  hoverTimer = setTimeout(() => { showQuickView.value = true }, 600)
}
function onMouseLeave() {
  clearTimeout(hoverTimer)
  showQuickView.value = false
}

// Normalize field names from API (name_business → name, investment_business → investment, etc.)
const b = computed(() => {
  const raw = props.business
  return {
    ...raw,
    id: raw.id_business || raw.id,
    id_code: raw.id_code_business || raw.id_code,
    name: raw.name_business || raw.name,
    investment: raw.investment_business || raw.investment,
    rental: raw.rental || raw.rental_business,
    size: raw.size_business || raw.size,
    times_viewed: raw.times_viewed_business || raw.times_viewed,
    lat: raw.lat_business || raw.lat,
    lng: raw.lng_business || raw.lng,
    flag_outstanding: raw.flag_outstanding,
    flag_sold: raw.sold || raw.flag_sold,
    source_platform: raw.source_platform,
    sector: raw.sector,
    municipality_name: raw.name_municipality || raw.municipality?.name,
    district_name: raw.name_district || raw.district?.name,
    province_name: raw.name_province || raw.province?.name,
    business_type_name: raw.name_business_type || raw.business_type?.name,
  }
})

const eurPerSqm = computed(() => {
  return b.value.size > 0 ? Math.round((b.value.investment || 0) / b.value.size) : 0
})
const roiEstimate = computed(() => {
  if (!b.value.rental || !b.value.investment) return null
  return (((b.value.rental * 12 * 0.6) / b.value.investment) * 100).toFixed(1)
})

const businessStore = useBusinessStore()
const compareStore = useCompareStore()

const isFavorite = computed(() =>
  businessStore.favorites.includes(b.value.id)
)

const formatMoney = (amount) => {
  if (!amount) return 'Consultar'
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0
  }).format(amount)
}

const formattedPrice = computed(() => formatMoney(b.value.investment))
const formattedRent = computed(() => formatMoney(b.value.rental))

const typeLabel = computed(() => {
  const typeStr = (b.value.business_type_name || '').toLowerCase()
  if (typeStr.includes('franquicia')) return 'Franquicia'
  if (typeStr.includes('inmueble')) return 'Inmueble'
  return 'Traspaso'
})

const typeBadgeClass = computed(() => {
  if (typeLabel.value === 'Franquicia') return 'c-badge c-badge--franquicia'
  if (typeLabel.value === 'Inmueble') return 'c-badge c-badge--inmueble'
  return 'c-badge c-badge--traspaso'
})

const imageUrl = computed(() => {
  // Try multimedia array first
  if (props.business.multimedia?.length) return props.business.multimedia[0].url || props.business.multimedia[0].full_path
  // Then try business_images_string (semicolon-separated URLs from API)
  if (props.business.business_images_string) {
    const firstImage = props.business.business_images_string.split(';')[0]
    if (firstImage) return firstImage
  }
  return null
})

const imageCount = computed(() => {
  if (props.business.multimedia?.length) return props.business.multimedia.length
  if (props.business.business_images_string) return props.business.business_images_string.split(';').filter(Boolean).length
  return 0
})

const imgError = ref(false)
const handleImageError = () => { imgError.value = true }
</script>

<template>
  <router-link
    :to="`/negocio/${b.id_code || b.id}`"
    class="bg-white rounded-2xl shadow-sm border border-gray-100 block no-underline group hover:shadow-xl transition-all duration-300 relative overflow-hidden flex flex-col h-full"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
  >
    <!-- Top Image Area -->
    <div class="relative h-40 md:h-52 bg-gray-100 overflow-hidden shrink-0">
      <img
        v-if="imageUrl && !imgError"
        :src="imageUrl"
        :alt="b.name"
        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
        loading="lazy"
        @error="handleImageError"
      />
      <div v-else class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="text-center">
          <AppIcon name="building" :size="48" class="text-gray-300" />
          <p class="text-xs text-gray-400 mt-2">Sin imagen</p>
        </div>
      </div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/20 to-transparent"></div>

      <!-- Top Badges -->
      <div class="absolute top-3 left-3 md:top-4 md:left-4 flex gap-1.5 md:gap-2">
        <span :class="[typeBadgeClass, 'shadow-md backdrop-blur-md bg-opacity-95 font-bold tracking-wide']">
          {{ typeLabel }}
        </span>
        <!-- Opportunity Badge -->
        <span v-if="business.showOpportunity" class="c-badge shadow-md backdrop-blur-md font-bold tracking-wide"
          :class="business.opportunityLabel === 'OPORTUNIDAD' ? 'bg-green-500 text-white' : business.opportunityLabel === 'BUEN PRECIO' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'">
          <AppIcon name="lightning" :size="10" class="mr-0.5" />
          {{ business.opportunityLabel }}
        </span>
      </div>

      <!-- Action Buttons Top Right -->
      <div class="absolute top-3 right-3 md:top-4 md:right-4 flex flex-col gap-1.5 md:gap-2">
        <button
          @click.prevent="businessStore.toggleFavorite(b.id)"
          class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/95 flex items-center justify-center border-none cursor-pointer transition shadow-md hover:scale-110 z-10"
          title="Guardar Favorito"
        >
          <AppIcon name="heart" :size="18" :class="isFavorite ? 'text-red-500' : 'text-gray-300'" />
        </button>
        <button
          @click.prevent="compareStore.toggle(b)"
          class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-none cursor-pointer transition shadow-md hover:scale-110 z-10"
          :class="compareStore.isInCompare(b.id) ? 'bg-red-50 text-[var(--color-primary)] ring-2 ring-[var(--color-primary)]' : 'bg-white/95 text-gray-400 hover:text-gray-600'"
          title="Comparar"
        >
          <AppIcon name="scale" :size="18" />
        </button>
      </div>

      <!-- Bottom Image Stats -->
      <div class="absolute bottom-3 left-3 right-3 md:bottom-4 md:left-4 md:right-4 flex justify-between items-end text-white">
        <div>
          <span class="text-xl md:text-2xl font-extrabold shadow-sm tracking-tight">{{ formattedPrice }}</span>
          <p v-if="b.rental > 0" class="text-sm font-medium text-gray-200 mt-1 flex items-center gap-1">
            <span class="text-gray-400">Alquiler:</span> {{ formattedRent }} /mes
          </p>
        </div>
        <div class="text-right">
          <span class="bg-black/40 backdrop-blur-md px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
            <AppIcon name="photo" :size="12" /> {{ imageCount }}
          </span>
        </div>
      </div>
    </div>

    <!-- Data Content Area -->
    <div class="p-3.5 md:p-5 flex flex-col flex-1 bg-white relative z-10">

      <!-- Title & Location -->
      <div class="mb-3 md:mb-4">
        <h3 class="text-base md:text-lg font-bold mb-1.5 md:mb-2 leading-tight text-gray-900 group-hover:text-[var(--color-primary)] transition-colors line-clamp-2">
          {{ b.name }}
        </h3>
        <p class="text-sm font-medium text-gray-500 flex items-start gap-1.5">
          <AppIcon name="map-pin" :size="14" class="mt-0.5 shrink-0 text-gray-400" />
          <span class="line-clamp-1">
            {{ b.district_name || b.municipality_name || b.province_name || 'Ubicación no disponible' }}
          </span>
        </p>
      </div>

      <!-- Metrics Grid -->
      <div class="flex gap-3 md:gap-4 mb-3 md:mb-5 pt-3 md:pt-4 border-t border-gray-100">
        <div class="flex items-center gap-1.5 text-sm text-gray-700">
          <AppIcon name="building" :size="14" class="text-gray-400" />
          <span class="font-semibold">{{ b.size || '--' }} m2</span>
        </div>
        <div v-if="b.source_platform" class="flex items-center gap-1.5 text-sm text-gray-500">
          <AppIcon name="globe" :size="14" class="text-gray-400" />
          <span class="font-medium">{{ b.source_platform }}</span>
        </div>
        <div v-if="b.times_viewed" class="flex items-center gap-1.5 text-sm text-gray-500">
          <AppIcon name="eye" :size="14" class="text-gray-400" />
          <span class="font-medium">{{ b.times_viewed }}</span>
        </div>
        <div v-if="b.flag_outstanding" class="flex items-center gap-1.5 text-sm text-amber-600 ml-auto">
          <AppIcon name="fire" :size="14" />
          <span class="font-semibold">Destacado</span>
        </div>
      </div>

      <!-- User Compatibility -->
      <!-- User Compatibility (only shown if data available) -->
      <div v-if="props.business.showCompatibility" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-indigo-50/70 border border-indigo-100">
        <div class="relative w-7 h-7 shrink-0">
          <svg viewBox="0 0 36 36" class="w-7 h-7 -rotate-90">
            <circle cx="18" cy="18" r="14" fill="none" stroke="#E0E7FF" stroke-width="3" />
            <circle cx="18" cy="18" r="14" fill="none" stroke="#6366F1" stroke-width="3" stroke-linecap="round"
              :stroke-dasharray="`${props.business.userCompatibility * 0.88} 88`" />
          </svg>
          <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold text-indigo-700">{{ props.business.userCompatibility }}%</span>
        </div>
        <div class="flex-1 min-w-0">
          <span class="text-[10px] font-bold text-indigo-700 block">Compatible contigo</span>
        </div>
      </div>

      <!-- Footer Area -->
      <div class="mt-auto pt-4 border-t border-gray-100 flex flex-col gap-3">
        <!-- Sectors Tags -->
        <div class="flex flex-wrap gap-1.5" v-if="b.sector">
          <span
            v-for="(sectorName, idx) in b.sector.split(', ').slice(0, 3)"
            :key="idx"
            class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-gray-100 text-gray-600 tracking-wider line-clamp-1"
          >
            {{ sectorName }}
          </span>
        </div>

        <!-- Bottom Row -->
        <div class="flex items-center justify-between mt-2">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-bold">
              V
            </span>
            <span class="text-xs font-semibold text-gray-500 truncate max-w-[100px]">
              VPDN
            </span>
          </div>
          <span class="px-2 py-1 bg-red-50 text-[var(--color-primary)] rounded text-xs font-bold whitespace-nowrap">
            Ver Detalle &rarr;
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Compare Overlay (hover) -->
    <Transition name="slide-up">
      <div v-if="showQuickView" class="absolute bottom-0 left-0 right-0 bg-gray-900/95 backdrop-blur-sm text-white p-4 z-20 rounded-b-2xl">
        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Comparación rápida</p>
        <div class="grid grid-cols-3 gap-2 text-center">
          <div>
            <p class="text-[10px] text-gray-400">EUR/m2</p>
            <p class="text-sm font-bold">{{ eurPerSqm ? eurPerSqm.toLocaleString('es-ES') + ' €' : '--' }}</p>
          </div>
          <div>
            <p class="text-[10px] text-gray-400">ROI est.</p>
            <p class="text-sm font-bold" :class="roiEstimate && roiEstimate > 5 ? 'text-green-400' : 'text-gray-200'">{{ roiEstimate ? roiEstimate + '%' : '--' }}</p>
          </div>
          <div>
            <p class="text-[10px] text-gray-400">Fuente</p>
            <p class="text-sm font-bold">{{ b.source_platform || '--' }}</p>
          </div>
        </div>
      </div>
    </Transition>
  </router-link>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.25s ease, opacity 0.25s ease;
}
.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
