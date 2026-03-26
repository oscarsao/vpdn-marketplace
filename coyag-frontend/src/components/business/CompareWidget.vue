<script setup>
import { useCompareStore } from '../../stores/compare'
import { useRouter } from 'vue-router'
import AppIcon from '../ui/AppIcon.vue'

defineProps({
  isMobile: { type: Boolean, default: false }
})

const compareStore = useCompareStore()
const router = useRouter()

function goToCompare() {
  router.push('/comparar')
}
</script>

<template>
  <transition name="slide-up">
    <div
      v-if="compareStore.hasItems"
      class="fixed right-4 md:right-8 glass-panel rounded-2xl p-4 shadow-2xl flex items-center gap-4 border border-[var(--color-primary)] max-w-sm w-[calc(100%-2rem)] md:w-auto"
      :class="isMobile ? 'bottom-[80px] z-[999]' : 'bottom-8 z-50'"
      style="box-shadow: 0 10px 40px rgba(164, 14, 5, 0.2);"
    >
      <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center shrink-0 border border-red-100 relative">
        <AppIcon name="scale" :size="24" />
        <div class="absolute -top-1 -right-1 w-5 h-5 bg-[var(--color-primary)] text-white text-xs font-bold rounded-full flex items-center justify-center">
          {{ compareStore.count }}
        </div>
      </div>

      <div class="flex-1">
        <p class="font-bold text-sm" style="color: var(--color-text-heading);">
          Comparador Activo
        </p>
        <p class="text-xs mt-0.5" style="color: var(--color-text-body);">
          Has seleccionado {{ compareStore.count }} negocio{{ compareStore.count > 1 ? 's' : '' }}
        </p>
      </div>

      <div class="flex flex-col gap-2">
        <button
          v-if="compareStore.canCompare"
          @click="goToCompare"
          class="c-btn c-btn--primary px-3 py-1.5 text-xs rounded-lg"
        >
          Comparar
        </button>
        <button
          @click="compareStore.clear()"
          class="text-xs text-[var(--color-danger)] font-medium hover:underline bg-transparent border-none cursor-pointer flex items-center gap-1"
        >
          <AppIcon name="trash" :size="12" />
          Limpiar
        </button>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>
