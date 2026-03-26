<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  collapsed: { type: Boolean, default: false },
  isMobile: { type: Boolean, default: false },
  mobileOpen: { type: Boolean, default: false },
})

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const emit = defineEmits(['collapse', 'close-mobile'])

function toggleCollapse() {
  emit('collapse', !props.collapsed)
}

const publicNavItems = [
  { name: 'Traspasos', route: '/listado-general', icon: 'store' },
  { name: 'Franquicias', route: '/listado-franquicias', icon: 'franchise' },
  { name: 'Inmuebles', route: '/listado-inmuebles', icon: 'real-estate' },
  { name: 'Estadisticas', route: '/estadisticas', icon: 'chart-bar' },
  { name: 'Valoracion', route: '/valoracion', icon: 'calculator' },
  { name: 'Mapa', route: '/mapa', icon: 'map-marker' },
]

const clientNavItems = computed(() => {
  if (!auth.isLoggedIn) return []
  return [
    { name: 'Mi Panel', route: '/dashboard', icon: 'dashboard' },
    { name: 'Mis Favoritos', route: '/favoritos', icon: 'heart' },
    { name: 'Mis Busquedas', route: '/mis-preferencias', icon: 'search' },
    { name: 'Recomendados', route: '/recomendados', icon: 'sparkles' },
    { name: 'Comparador', route: '/comparar', icon: 'scale' },
    { name: 'Mi Asesor', route: '/mi-asesor', icon: 'briefcase' },
    { name: 'Mis Solicitudes', route: '/mis-solicitudes', icon: 'envelope' },
    { name: 'Mi Perfil', route: '/mi-perfil', icon: 'user' },
  ]
})

const adminNavItems = computed(() => {
  if (!auth.isAdmin) return []
  return [
    { name: 'Negocios', route: '/admin/negocios', icon: 'office' },
    { name: 'Clientes', route: '/admin/clientes', icon: 'users' },
    { name: 'Empleados', route: '/admin/empleados', icon: 'computer' },
    { name: 'Leads', route: '/admin/leads', icon: 'inbox' },
    { name: 'Calendario', route: '/admin/calendario', icon: 'calendar' },
    { name: 'Estadisticas', route: '/admin/estadisticas', icon: 'chart-line' },
    { name: 'Configuracion', route: '/admin/configuracion', icon: 'cog' },
  ]
})

function isActive(item) {
  return route.path === item.route || route.path.startsWith(item.route + '/')
}

function navigateTo(item) {
  router.push(item.route)
  emit('close-mobile')
}

function logout() {
  auth.logout()
  router.push('/login')
  emit('close-mobile')
}

const sidebarClass = computed(() => {
  if (props.isMobile) {
    return props.mobileOpen ? 'translate-x-0' : '-translate-x-full'
  }
  return 'translate-x-0'
})

const sidebarWidth = computed(() => {
  if (props.isMobile) return 'w-[280px]'
  return props.collapsed ? 'w-[72px]' : 'w-[260px]'
})
</script>

<template>
  <aside
    :class="[
      'sidebar fixed left-0 top-0 h-full z-50 transition-all duration-300 flex flex-col',
      sidebarWidth,
      sidebarClass
    ]"
  >
    <!-- Logo Area -->
    <div class="sidebar-logo h-[64px] flex items-center px-4 gap-3 shrink-0">
      <router-link to="/" class="flex items-center gap-3 no-underline min-w-0" @click="$emit('close-mobile')">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center text-white font-extrabold text-sm shrink-0 shadow-sm">
          V
        </div>
        <div v-if="!collapsed || isMobile" class="overflow-hidden">
          <div class="font-extrabold text-sm leading-tight text-gray-900 tracking-wide">COYAG</div>
          <div class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">VPDN</div>
        </div>
      </router-link>

      <!-- Mobile close button -->
      <button
        v-if="isMobile"
        class="ml-auto w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition-colors"
        @click="$emit('close-mobile')"
      >
        <AppIcon name="x-mark" :size="16" />
      </button>
    </div>

    <!-- Nav Menu -->
    <nav class="flex flex-col gap-0.5 px-3 py-4 overflow-y-auto flex-1 sidebar-scroll">

      <!-- Public / Portal -->
      <div class="nav-section">
        <div v-if="!collapsed || isMobile" class="nav-group-label">Portal</div>
        <a
          v-for="item in publicNavItems"
          :key="item.route"
          @click.prevent="navigateTo(item)"
          class="nav-item"
          :class="{ 'nav-item--active': isActive(item) }"
          :title="collapsed && !isMobile ? item.name : ''"
          href="#"
        >
          <AppIcon :name="item.icon" :size="20" class="nav-icon" />
          <span v-if="!collapsed || isMobile" class="nav-label">{{ item.name }}</span>
        </a>
      </div>

      <!-- Client -->
      <template v-if="auth.isLoggedIn">
        <div class="nav-divider"></div>
        <div class="nav-section">
          <div v-if="!collapsed || isMobile" class="nav-group-label">Mi Area</div>
          <a
            v-for="item in clientNavItems"
            :key="item.route"
            @click.prevent="navigateTo(item)"
            class="nav-item"
            :class="{ 'nav-item--active': isActive(item) }"
            :title="collapsed && !isMobile ? item.name : ''"
            href="#"
          >
            <AppIcon :name="item.icon" :size="20" class="nav-icon" />
            <span v-if="!collapsed || isMobile" class="nav-label">{{ item.name }}</span>
          </a>
        </div>
      </template>

      <!-- Admin -->
      <template v-if="auth.isAdmin">
        <div class="nav-divider"></div>
        <div class="nav-section">
          <div v-if="!collapsed || isMobile" class="nav-group-label">Admin</div>
          <a
            v-for="item in adminNavItems"
            :key="item.route"
            @click.prevent="navigateTo(item)"
            class="nav-item"
            :class="{ 'nav-item--active': isActive(item) }"
            :title="collapsed && !isMobile ? item.name : ''"
            href="#"
          >
            <AppIcon :name="item.icon" :size="20" class="nav-icon" />
            <span v-if="!collapsed || isMobile" class="nav-label">{{ item.name }}</span>
          </a>
        </div>
      </template>
    </nav>

    <!-- Bottom: User + Logout -->
    <div class="px-3 py-3 border-t border-gray-100 shrink-0">
      <template v-if="auth.isLoggedIn">
        <div v-if="!collapsed || isMobile" class="flex items-center gap-3 px-3 py-2 mb-1">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-600 to-red-700 text-white flex items-center justify-center text-xs font-bold shrink-0">
            {{ auth.fullName ? auth.fullName.charAt(0).toUpperCase() : 'U' }}
          </div>
          <div class="overflow-hidden">
            <div class="text-sm font-semibold truncate text-gray-900">{{ auth.fullName }}</div>
            <div class="text-[10px] text-gray-400 truncate">{{ auth.isAdmin ? 'Administrador' : 'Cliente' }}</div>
          </div>
        </div>
        <button
          @click="logout"
          class="nav-item nav-item--logout w-full text-left cursor-pointer bg-transparent border-none"
        >
          <AppIcon name="logout" :size="20" class="nav-icon" />
          <span v-if="!collapsed || isMobile" class="nav-label">Cerrar sesion</span>
        </button>
      </template>
      <template v-else>
        <a href="#" @click.prevent="navigateTo({ route: '/login' })" class="nav-item no-underline" :class="{ 'nav-item--active': route.path === '/login' }">
          <AppIcon name="login" :size="20" class="nav-icon" />
          <span v-if="!collapsed || isMobile" class="nav-label">Ingresar</span>
        </a>
      </template>
    </div>
  </aside>

  <!-- Collapse Toggle Button (desktop only) -->
  <button
    v-if="!isMobile"
    @click="toggleCollapse"
    class="fixed z-50 transition-all duration-300 w-6 h-6 rounded-full bg-white border border-gray-200 shadow-md flex items-center justify-center text-gray-400 hover:text-red-600 hover:border-red-300 cursor-pointer"
    :style="{ left: collapsed ? '60px' : '248px', top: '32px', transform: 'translateY(-50%)' }"
    aria-label="Toggle sidebar"
  >
    <AppIcon :name="collapsed ? 'chevron-right' : 'chevron-left'" :size="12" />
  </button>
</template>

<style scoped>
.sidebar {
  background: #FFFFFF;
  border-right: 1px solid #E2E8F0;
  box-shadow: 1px 0 4px rgba(0, 0, 0, 0.03);
}

.sidebar-scroll::-webkit-scrollbar { width: 4px; }
.sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
.sidebar-scroll::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 4px; }

.nav-group-label {
  padding: 6px 12px 4px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #94A3B8;
}

.nav-divider {
  height: 1px;
  background: #F1F5F9;
  margin: 8px 0;
}

.nav-section {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 9px 12px;
  border-radius: 8px;
  text-decoration: none;
  color: #64748B;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.15s ease;
  cursor: pointer;
}

.nav-item:hover {
  background: #F8FAFC;
  color: #334155;
}

.nav-item--active {
  background: #FEF2F2;
  color: #A40E05;
  font-weight: 600;
  box-shadow: inset 3px 0 0 #A40E05;
}

.nav-item--active .nav-icon {
  color: #A40E05;
}

.nav-item--logout {
  color: #94A3B8;
}
.nav-item--logout:hover {
  background: #FEF2F2;
  color: #EF4444;
}

.nav-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  opacity: 0.6;
}
.nav-item:hover .nav-icon,
.nav-item--active .nav-icon {
  opacity: 1;
}

.nav-label {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
