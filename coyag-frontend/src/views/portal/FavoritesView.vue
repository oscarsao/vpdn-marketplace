<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'
import BusinessCard from '../../components/business/BusinessCard.vue'
import AppIcon from '../../components/ui/AppIcon.vue'

const favorites = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await axios.get('/favorite')
    favorites.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

async function removeFavorite(business) {
  try {
    await axios.post('/favorite', { business_id: business.id })
    favorites.value = favorites.value.filter(b => b.id !== business.id)
  } catch (e) {
    console.error(e)
  }
}
</script>

<template>
  <div class="animate-fade-in">
    <header class="mb-8">
      <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
        Mis Favoritos
      </h1>
      <p class="mt-1 text-sm text-gray-500">
        {{ favorites.length }} negocios guardados para revisar.
      </p>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <div v-for="i in 3" :key="i" class="c-card p-4">
        <div class="skeleton h-48 w-full rounded-xl mb-4"></div>
        <div class="skeleton h-6 w-3/4 mb-2"></div>
        <div class="skeleton h-4 w-1/2"></div>
      </div>
    </div>

    <!-- Results -->
    <div v-else-if="favorites.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <div v-for="business in favorites" :key="business.id" class="relative group">
        <BusinessCard :business="business" class="h-full" />
        <button
          @click="removeFavorite(business)"
          class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 shadow-sm flex items-center justify-center text-red-500 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity"
          title="Quitar de favoritos"
        ><AppIcon name="x-mark" :size="14" /></button>
      </div>
    </div>

    <!-- Empty -->
    <div v-else class="c-card p-12 text-center text-gray-500">
      <div class="mb-4"><AppIcon name="heart" :size="48" /></div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Aun no tienes favoritos</h3>
      <p class="text-gray-500">Explora el catalogo y marca los negocios que te interesen.</p>
      <router-link to="/listado-general" class="c-btn c-btn--outline mt-6 no-underline inline-block">
        Explorar Negocios
      </router-link>
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
.skeleton {
  background-color: #E2E8F0;
  animation: skeletonPulse 1.5s ease-in-out infinite;
  border-radius: 4px;
}
@keyframes skeletonPulse {
  0% { background-color: #E2E8F0; }
  50% { background-color: #F1F5F9; }
  100% { background-color: #E2E8F0; }
}
</style>
