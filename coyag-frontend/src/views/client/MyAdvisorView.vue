<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../api/axios'
import AppIcon from '../../components/ui/AppIcon.vue'

const advisor = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await axios.get('/assigned-advisor')
    advisor.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <div v-if="loading" class="c-card p-8"><div class="skeleton h-48 w-full rounded-lg"></div></div>

    <div v-else-if="!advisor" class="text-center py-20">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <AppIcon name="user" :size="28" class="text-gray-300" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Sin asesor asignado</h3>
      <p class="text-gray-500 mb-6">Aun no tienes un asesor asignado. Contacta con nosotros para mas informacion.</p>
      <router-link to="/" class="c-btn c-btn--primary no-underline">Volver al portal</router-link>
    </div>

    <template v-else>
      <div class="max-w-2xl mx-auto">
        <!-- Advisor Card -->
        <div class="c-card p-8">
          <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 md:gap-6 mb-8">
            <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] text-white flex items-center justify-center text-3xl font-extrabold shadow-lg shrink-0">
              {{ advisor.names?.charAt(0) }}{{ advisor.surnames?.charAt(0) }}
            </div>
            <div class="text-center sm:text-left">
              <h2 class="text-2xl font-extrabold text-gray-900">{{ advisor.names }} {{ advisor.surnames }}</h2>
              <p class="text-sm text-gray-500 font-medium">{{ advisor.department }} · {{ advisor.specialization }}</p>
              <div class="flex items-center gap-0.5 mt-2 justify-center sm:justify-start">
                <AppIcon v-for="i in 5" :key="i" name="star" :size="16"
                  :class="i <= Math.floor(advisor.rating) ? 'text-amber-400' : 'text-gray-200'" />
                <span class="text-sm font-bold text-gray-500 ml-1.5">{{ advisor.rating }}</span>
              </div>
            </div>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
              <div class="text-2xl font-extrabold" style="color: var(--color-primary)">{{ advisor.totalClients }}</div>
              <div class="text-xs font-semibold text-gray-400 mt-1">Clientes activos</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
              <div class="text-2xl font-extrabold text-green-600">{{ advisor.closedDeals }}</div>
              <div class="text-xs font-semibold text-gray-400 mt-1">Operaciones cerradas</div>
            </div>
          </div>

          <!-- Contact Info -->
          <div class="space-y-4">
            <h3 class="font-bold text-gray-900">Contacto</h3>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
              <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                <AppIcon name="envelope" :size="16" class="text-[var(--color-primary)]" />
              </div>
              <div>
                <div class="text-xs font-semibold text-gray-400">Email</div>
                <a :href="'mailto:' + advisor.email" class="text-sm font-bold text-[var(--color-primary)] hover:underline">{{ advisor.email }}</a>
              </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
              <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                <AppIcon name="phone" :size="16" class="text-[var(--color-primary)]" />
              </div>
              <div>
                <div class="text-xs font-semibold text-gray-400">Telefono</div>
                <a :href="'tel:' + advisor.phone" class="text-sm font-bold text-[var(--color-primary)] hover:underline">{{ advisor.phone }}</a>
              </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
              <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                <AppIcon name="clock" :size="16" class="text-[var(--color-primary)]" />
              </div>
              <div>
                <div class="text-xs font-semibold text-gray-400">Disponibilidad</div>
                <div class="text-sm font-bold text-gray-700">{{ advisor.availability }}</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row gap-3 mt-8">
            <a :href="'mailto:' + advisor.email" class="c-btn c-btn--primary flex-1 justify-center no-underline">Enviar email</a>
            <a :href="'tel:' + advisor.phone" class="c-btn c-btn--outline flex-1 justify-center no-underline">Llamar ahora</a>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
