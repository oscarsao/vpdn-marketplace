import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  // ── Auth (Full Page) ────────────────────────────────
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/LoginView.vue'),
    meta: { requiresAuth: false, layout: 'blank' }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/auth/RegisterView.vue'),
    meta: { requiresAuth: false, layout: 'blank' }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('../views/auth/ForgotPasswordView.vue'),
    meta: { requiresAuth: false, layout: 'blank' }
  },

  // ── Portal (Main Layout) ───────────────────────────
  {
    path: '/',
    name: 'inicio',
    component: () => import('../views/portal/MarketDashboardView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Dashboard del Mercado' }
  },
  {
    path: '/listado-general',
    name: 'listado-general',
    component: () => import('../views/portal/PortalView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Traspasos', condition: 'general' }
  },
  {
    path: '/listado-franquicias',
    name: 'listado-franquicias',
    component: () => import('../views/portal/PortalView.vue'),
    meta: { pageTitle: 'Franquicias', condition: 'franchise' }
  },
  {
    path: '/listado-inmuebles',
    name: 'listado-inmuebles',
    component: () => import('../views/portal/PortalView.vue'),
    meta: { pageTitle: 'Inmuebles', condition: 'inmuebles' }
  },
  {
    path: '/negocio/:id_code_business',
    name: 'item-portal-negocios',
    component: () => import('../views/portal/BusinessDetailView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Detalles' }
  },
  {
    path: '/favoritos',
    name: 'favoritos',
    component: () => import('../views/portal/FavoritesView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Favoritos' }
  },
  {
    path: '/recomendados',
    name: 'recomendados',
    component: () => import('../views/portal/RecommendedView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Recomendados' }
  },
  {
    path: '/comparar',
    name: 'comparar',
    component: () => import('../views/portal/CompareView.vue'),
    meta: { requiresAuth: false, pageTitle: 'Comparador de Negocios' }
  },
  {
    path: '/estadisticas',
    name: 'estadisticas',
    component: () => import('../views/portal/StatisticsView.vue'),
    meta: { pageTitle: 'Estadísticas del Mercado' }
  },
  {
    path: '/valoracion',
    name: 'valoracion',
    component: () => import('../views/portal/ValuationView.vue'),
    meta: { pageTitle: 'Valoracion de tu Negocio' }
  },
  {
    path: '/mapa',
    name: 'mapa',
    component: () => import('../views/portal/MapView.vue'),
    meta: { pageTitle: 'Mapa de Negocios' }
  },

  // ── Client ─────────────────────────────────────────
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/client/DashboardView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Mi Panel' }
  },
  {
    path: '/mis-preferencias',
    name: 'preferencias',
    component: () => import('../views/client/PreferencesView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Mis Criterios de Inversión' }
  },
  {
    path: '/mi-perfil',
    name: 'mi-perfil',
    component: () => import('../views/client/ProfileView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Mi Perfil' }
  },
  {
    path: '/mi-asesor',
    name: 'mi-asesor',
    component: () => import('../views/client/MyAdvisorView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Mi Asesor' }
  },
  {
    path: '/mis-solicitudes',
    name: 'mis-solicitudes',
    component: () => import('../views/client/MyRequestsView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Mis Solicitudes' }
  },
  {
    path: '/notificaciones',
    name: 'notificaciones',
    component: () => import('../views/NotificationsView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Notificaciones' }
  },

  // ── Admin ──────────────────────────────────────────
  {
    path: '/admin/negocios',
    name: 'admin.negocios',
    component: () => import('../views/admin/BusinessManagement.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Gestión de Negocios' }
  },
  {
    path: '/admin/agregar-negocio',
    name: 'admin.agregar-negocio',
    component: () => import('../views/admin/BusinessFormView.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Nuevo Negocio' }
  },
  {
    path: '/admin/editar-negocio/:id',
    name: 'admin.editar-negocio',
    component: () => import('../views/admin/BusinessFormView.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Editar Negocio' }
  },
  {
    path: '/admin/clientes',
    name: 'admin.clientes',
    component: () => import('../views/admin/ClientManagement.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Gestión de Clientes' }
  },
  {
    path: '/admin/empleados',
    name: 'admin.empleados',
    component: () => import('../views/admin/EmployeeManagement.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Gestión de Empleados' }
  },
  {
    path: '/admin/leads',
    name: 'admin.leads',
    component: () => import('../views/admin/LeadsManagement.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Gestión de Leads' }
  },
  {
    path: '/admin/calendario',
    name: 'admin.calendario',
    component: () => import('../views/admin/CalendarManagement.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Calendario de Citas' }
  },
  {
    path: '/admin/estadisticas',
    name: 'admin.estadisticas',
    component: () => import('../views/admin/StatisticsDashboard.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Estadísticas y Rendimiento' }
  },
  {
    path: '/admin/configuracion',
    name: 'admin.configuracion',
    component: () => import('../views/admin/AdminSettingsView.vue'),
    meta: { requiresAuth: true, rule: 'admin', pageTitle: 'Ajustes Globales' }
  },

  // ── AI ─────────────────────────────────────────────
  {
    path: '/ai',
    name: 'ai-chat',
    component: () => import('../views/ai/AiChatView.vue'),
    meta: { requiresAuth: true, pageTitle: 'Asistente IA' }
  },

  // ── 404 ────────────────────────────────────────────
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFoundView.vue'),
    meta: { layout: 'blank' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// Auth guard
router.beforeEach((to, from, next) => {
  const userInfo = localStorage.getItem('userInfo')
  const userRoles = JSON.parse(localStorage.getItem('userRoles') || 'null')

  if (to.meta.requiresAuth && !userInfo) {
    next('/login')
    return
  }

  // Admin check
  if (to.meta.rule === 'admin') {
    const isAdmin = userRoles && userRoles.some(r => r.id_role < 40)
    if (!isAdmin) {
      next('/')
      return
    }
  }

  next()
})

export default router
