<script setup>
import AppIcon from '../components/ui/AppIcon.vue'
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[var(--color-bg-page)] px-4">
    <div class="text-center">
      <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <AppIcon name="search" :size="36" class="text-gray-300" />
      </div>
      <h1 class="text-3xl font-bold mb-2">404</h1>
      <p class="text-lg mb-6" style="color: var(--color-text-muted);">Pagina no encontrada</p>
      <router-link to="/" class="c-btn c-btn--primary no-underline">Volver al inicio</router-link>
    </div>
  </div>
</template>
