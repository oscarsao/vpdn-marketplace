<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import axios from '../../api/axios'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  isMobile: { type: Boolean, default: false },
  sidebarWidth: { type: String, default: '260px' },
})

const emit = defineEmits(['toggle-menu'])

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const pageTitle = computed(() => {
  return route.meta?.pageTitle || 'Portal de Negocios'
})

// Global search
const globalSearch = ref('')
let searchDebounce = null

function handleGlobalSearch() {
  const q = globalSearch.value.trim()
  if (!q) return
  router.push({ path: '/listado-general', query: { q } })
}

function onSearchInput() {
  clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    handleGlobalSearch()
  }, 400)
}

const notifCount = ref(0)

onMounted(async () => {
  try {
    const { data } = await axios.get('/notification')
    const notifs = Array.isArray(data) ? data : []
    notifCount.value = notifs.filter(n => !n.read).length
  } catch (e) {
    // Silently fail — badge stays at 0
  }
})
</script>

<template>
  <header
    class="fixed top-0 right-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-100 flex items-center px-4 md:px-6 transition-all duration-300"
    :style="{ height: '64px', left: isMobile ? '0' : sidebarWidth }"
  >
    <!-- Mobile hamburger -->
    <button
      v-if="isMobile"
      @click="emit('toggle-menu')"
      class="mr-3 w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
    >
      <AppIcon name="menu" :size="18" />
    </button>

    <!-- Page Title -->
    <div class="flex-1 min-w-0">
      <h1 class="text-sm md:text-base font-bold truncate text-gray-900">{{ pageTitle }}</h1>
    </div>

    <!-- Right Tools -->
    <div class="flex items-center gap-1.5 md:gap-3">
      <!-- Global Search (desktop only) -->
      <div class="relative hidden md:flex items-center">
        <span class="absolute left-3 text-gray-400">
          <AppIcon name="search" :size="16" />
        </span>
        <input
          v-model="globalSearch"
          @keyup.enter="handleGlobalSearch"
          @input="onSearchInput"
          type="text"
          placeholder="Buscar negocios..."
          class="pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 w-52 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-colors"
        />
      </div>

      <!-- Notifications Bell -->
      <button class="relative p-2 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors cursor-pointer border-none bg-transparent">
        <AppIcon name="bell" :size="20" />
        <span
          v-if="notifCount > 0"
          class="absolute top-1 right-1 w-4 h-4 rounded-full bg-red-600 text-white text-[8px] font-bold flex items-center justify-center"
        >{{ notifCount }}</span>
      </button>

      <!-- User Profile Bubble -->
      <template v-if="auth.isLoggedIn">
        <router-link
          to="/mi-perfil"
          class="flex items-center gap-2 pl-1.5 pr-3 py-1 rounded-lg hover:bg-gray-50 transition-colors no-underline"
        >
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-800 to-slate-900 text-white flex items-center justify-center text-xs font-bold">
            {{ auth.fullName ? auth.fullName.charAt(0).toUpperCase() : 'U' }}
          </div>
          <div class="hidden md:block">
            <div class="text-sm font-semibold leading-tight text-gray-900">{{ auth.fullName || 'Usuario' }}</div>
            <div class="text-[10px] text-gray-400 leading-tight">{{ auth.isAdmin ? 'Admin' : 'Cliente' }}</div>
          </div>
        </router-link>
      </template>
      <template v-else>
        <router-link to="/login" class="c-btn c-btn--primary text-sm py-2">
          Ingresar
        </router-link>
      </template>
    </div>
  </header>
</template>
