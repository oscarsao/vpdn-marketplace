<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAiStore } from '../../stores/ai'
import axios from '../../api/axios'
import BusinessCard from '../../components/business/BusinessCard.vue'
import AppIcon from '../../components/ui/AppIcon.vue'

const route = useRoute()
const aiStore = useAiStore()
const recommendations = ref([])
const loading = ref(true)
const similarToName = ref('')

const isSimilarMode = computed(() => !!route.query.similar_to)

onMounted(async () => {
  try {
    if (route.query.similar_to) {
      // Similar-to mode: find businesses similar to a specific one
      const results = await aiStore.fetchSimilarBusinesses(route.query.similar_to, 12)
      recommendations.value = results || []
      // Try to get the business name
      const { data: bizData } = await axios.get(`/business/show/${route.query.similar_to}`)
      similarToName.value = bizData?.data?.name || bizData?.name || route.query.similar_to
    } else {
      // Default: AI recommendations based on preferences
      const { data } = await axios.post('/ai/recommendations')
      const recs = data.data || data
      recommendations.value = Array.isArray(recs) ? recs.map((b, i) => ({
        business: b,
        matchScore: b.matchScore || Math.round(95 - i * 7),
        reasons: b.reasons || [],
      })) : []
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="animate-fade-in">
    <header class="mb-6 md:mb-8 p-4 md:p-6 bg-gradient-to-r from-[rgba(115,103,240,0.08)] to-[rgba(206,159,252,0.08)] rounded-2xl border border-[rgba(115,103,240,0.15)]">
      <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
        <AppIcon name="sparkles" :size="24" class="text-indigo-500" />
        {{ isSimilarMode ? `Similares a ${similarToName}` : 'Recomendados por IA' }}
      </h1>
      <p class="mt-1 text-sm text-gray-600">
        {{ isSimilarMode
          ? 'Negocios con caracteristicas similares encontrados por nuestro algoritmo de IA.'
          : 'Basado en tu actividad y preferencias, nuestro asistente ha seleccionado estos negocios para ti.'
        }}
      </p>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <div v-for="i in 6" :key="i" class="c-card p-4">
        <div class="skeleton h-48 w-full rounded-xl mb-4"></div>
        <div class="skeleton h-6 w-3/4 mb-2"></div>
        <div class="skeleton h-4 w-1/2"></div>
      </div>
    </div>

    <!-- Results -->
    <div v-else-if="recommendations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <div v-for="rec in recommendations" :key="rec.business?.id || rec.id" class="relative">
        <div class="absolute -top-2 -left-2 z-10 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md"
          :class="isSimilarMode ? 'bg-indigo-600' : 'bg-[var(--color-primary)]'">
          {{ rec.matchScore || 0 }}% {{ isSimilarMode ? 'similar' : 'match' }}
        </div>
        <BusinessCard :business="rec.business || rec" class="h-full" />
        <!-- Reasons -->
        <div v-if="rec.reasons?.length" class="px-3 pb-2 -mt-1">
          <p class="text-[10px] text-gray-400 truncate">{{ rec.reasons.join(' · ') }}</p>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else class="c-card p-12 text-center">
      <div class="mb-4"><AppIcon name="sparkles" :size="48" /></div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">
        {{ isSimilarMode ? 'No se encontraron negocios similares' : 'La IA esta aprendiendo de ti' }}
      </h3>
      <p class="text-gray-500">
        {{ isSimilarMode ? 'Intenta con otro negocio de referencia.' : 'Navega el portal para que nuestros algoritmos puedan entender mejor tus preferencias.' }}
      </p>
    </div>
  </div>
</template>
