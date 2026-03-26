<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const navItems = computed(() => {
  const items = [
    { name: 'Traspasos', route: '/listado-general', show: true },
    { name: 'Franquicias', route: '/listado-franquicias', show: true },
    { name: 'Inmuebles', route: '/listado-inmuebles', show: true },
    { name: 'Favoritos', route: '/favoritos', show: auth.isLoggedIn },
  ]
  if (auth.isAdmin) {
    items.push({ name: 'Admin', route: '/admin/negocios', show: true })
  }
  return items.filter(i => i.show)
})

function logout() {
  auth.logout()
  router.push('/login')
}
</script>

<template>
  <header class="fixed top-0 left-0 right-0 z-50 glass-panel transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 h-16 flex items-center justify-between">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2 no-underline">
        <span class="text-xl font-bold" style="color: var(--color-primary);">COYAG</span>
        <span class="text-xs font-medium hidden sm:inline" style="color: var(--color-text-muted);">VPDN</span>
      </router-link>

      <!-- Desktop Nav -->
      <nav class="hidden lg:flex items-center gap-1">
        <router-link
          v-for="item in navItems"
          :key="item.route"
          :to="item.route"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors no-underline"
          :class="$route.path === item.route
            ? 'bg-[var(--color-primary)] text-white'
            : 'text-[var(--color-text-body)] hover:bg-gray-100'"
        >
          {{ item.name }}
        </router-link>
      </nav>

      <!-- Right Side -->
      <div class="flex items-center gap-3">
        <template v-if="auth.isLoggedIn">
          <!-- User avatar / menu -->
          <div class="relative">
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center text-sm font-bold cursor-pointer border-none"
            >
              {{ auth.fullName ? auth.fullName.charAt(0).toUpperCase() : 'U' }}
            </button>
            <!-- Dropdown -->
            <div
              v-if="mobileMenuOpen"
              class="absolute right-0 top-12 w-48 bg-white rounded-xl py-2 z-50"
              style="box-shadow: var(--shadow-card-hover);"
            >
              <router-link
                to="/mi-perfil"
                class="block px-4 py-2 text-sm hover:bg-gray-50 no-underline text-[var(--color-text-body)]"
                @click="mobileMenuOpen = false"
              >Mi Perfil</router-link>
              <router-link
                to="/dashboard"
                class="block px-4 py-2 text-sm hover:bg-gray-50 no-underline text-[var(--color-text-body)]"
                @click="mobileMenuOpen = false"
              >Dashboard</router-link>
              <template v-if="auth.isAdmin">
                <hr class="my-1 border-gray-100" />
                <router-link
                  to="/admin/calendario"
                  class="block px-4 py-2 text-sm hover:bg-red-50 text-[var(--color-primary)] font-semibold no-underline"
                  @click="mobileMenuOpen = false"
                >📅 Calendario Admin</router-link>
                <router-link
                  to="/admin/estadisticas"
                  class="block px-4 py-2 text-sm hover:bg-red-50 text-[var(--color-primary)] font-semibold no-underline"
                  @click="mobileMenuOpen = false"
                >📊 Estadísticas</router-link>
                <router-link
                  to="/admin/configuracion"
                  class="block px-4 py-2 text-sm hover:bg-red-50 text-[var(--color-primary)] font-semibold no-underline"
                  @click="mobileMenuOpen = false"
                >⚙️ Ajustes Globales</router-link>
              </template>
              <hr class="my-1 border-gray-100" />
              <button
                @click="logout"
                class="w-full text-left px-4 py-2 text-sm text-[var(--color-danger)] hover:bg-red-50 cursor-pointer border-none bg-transparent"
              >Cerrar sesión</button>
            </div>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="c-btn c-btn--primary text-sm">Ingresar</router-link>
        </template>
      </div>
    </div>
  </header>
</template>
