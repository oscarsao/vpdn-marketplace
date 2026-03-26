// ═══════════════════════════════════════════════════════════
// COYAG VPDN — Mock API Interceptor
// Intercepts Axios requests and returns local mock data
// Toggle via VITE_USE_MOCK=true in .env
// ═══════════════════════════════════════════════════════════

import {
  mockUsers,
  mockBusinessesFull,
  mockProvinces,
  mockMunicipalities,
  mockDistricts,
  mockNeighborhoods,
  mockSectors,
  mockBusinessTypes,
  mockClients,
  mockEmployees,
  mockCalendarEvents,
  mockNotifications,
  mockStatistics,
  mockFavoriteIds,
  mockRecommendationIds,
  mockPreferences,
  mockAdvisor,
  mockClientRequests,
  mockActivityFeed,
  mockLeads,
} from '../data/mockData'

import {
  generateContextualChatResponse,
  generateBusinessAnalysis,
  findSimilarBusinesses,
  searchBusinessesWithAI,
  parseNaturalLanguageQuery,
  computeBusinessMarketContext,
  computeOpportunityScore,
  computeUserCompatibility,
} from '../data/aiEngine'

// ── Normalize mock data → Laravel API schema ────────────
// BusinessCard.vue expects: investment, rental, name, multimedia, business_type, id_code, etc.
// Mock data has: financials.price, financials.rent, title, images, type, metadata.reference, etc.
function normalizeBusinessForApi(raw) {
  const sectorMap = {
    'Restauración y Hostelería': 1, 'Restauración': 1, 'Gastronomía': 1, 'Pizzería': 1,
    'Alimentación': 2, 'Panadería': 2, 'Cafetería': 2, 'Helados': 2,
    'Estética y Belleza': 3, 'Peluquería': 3,
    'Gimnasios y Deporte': 4, 'Gimnasio': 4, 'Deporte': 4,
    'Salud y Clínicas': 5, 'Salud': 5, 'Clínica': 5, 'Farmacia': 5,
    'Ocio Nocturno': 6, 'Ocio nocturno': 6, 'Bar': 6, 'Discoteca': 6,
    'Locales Vacíos': 7, 'Local Comercial': 7, 'Retail': 7,
    'Logística e Industrial': 8, 'Logística': 8, 'Industrial': 8,
    'Tecnología': 9, 'Informática': 9,
    'Educación y Formación': 10, 'Educación': 10, 'Formación': 10, 'Idiomas': 10,
    'Moda y Retail': 11, 'Moda': 11,
    'Servicios Profesionales': 12, 'Servicios': 12, 'Lavandería': 12,
    'Automoción': 13, 'Taller': 13,
    'Oficinas': 14, 'Coworking': 14,
    'Franquicia': 15,
  }

  const typeNames = { traspaso: 'Traspaso', franquicia: 'Franquicia', inmueble: 'Inmueble' }

  return {
    id: raw.id,
    id_code: raw.metadata?.reference || `REF-${raw.id}`,
    name: raw.title,
    description: raw.description || '',
    investment: raw.financials?.price || 0,
    rental: raw.financials?.rent || 0,
    size: raw.features?.size || 0,
    bathrooms: raw.features?.bathrooms || 0,
    smoke_outlet: raw.features?.smokeOutlet || false,
    terrace: raw.features?.terrace || false,
    facade: raw.features?.facade || '',
    status_label: raw.features?.status || '',
    flag_active: true,
    flag_sold: false,
    flag_outstanding: (raw.metadata?.favorites || 0) > 20,
    times_viewed: raw.metadata?.views || 0,
    days_on_market: raw.metadata?.daysOnMarket || 0,
    source: raw.metadata?.source || 'VPDN Direct',
    address: raw.location?.fullAddress || '',
    business_type: { id: raw.type === 'franquicia' ? 2 : raw.type === 'inmueble' ? 3 : 1, name: typeNames[raw.type] || 'Traspaso' },
    province: { id: 28, name: raw.location?.province || 'Madrid' },
    municipality: { id: 1, name: raw.location?.municipality || 'Madrid' },
    district: { id: 1, name: raw.location?.district || '' },
    neighborhood: { id: 1, name: raw.location?.neighborhood || '' },
    location: raw.location || {},
    multimedia: (raw.images || []).map((img, i) => ({ id: i + 1, url: img, full_path: img, type: 'image', order: i })),
    sectors: (raw.sectors || []).map(s => ({ id: sectorMap[s] || 99, name: s })),
    employee: raw.agent ? { id: 1, name: raw.agent.name, contact: raw.agent.contact, phone: raw.agent.phone } : null,
    agent: raw.agent || null,
    metadata: raw.metadata || {},
    // Keep raw fields for backwards compat
    financials: raw.financials,
    features: raw.features,
    type: raw.type,
  }
}

// Simulate network delay
const delay = (ms = 300) => new Promise(resolve => setTimeout(resolve, ms + Math.random() * 200))

// In-memory state for mutations
let favorites = [...mockFavoriteIds]
let calendarEvents = [...mockCalendarEvents]
let notifications = [...mockNotifications]
let currentUser = null
let currentToken = null

// ── Route Matcher ────────────────────────────────────────
function matchRoute(url, method) {
  const u = url.replace(/^\//, '') // strip leading slash

  // Auth
  if (u === 'auth/login' && method === 'post') return 'auth.login'
  if (u === 'auth/register' && method === 'post') return 'auth.register'
  if (u === 'auth/user' && method === 'get') return 'auth.user'
  if (u === 'auth/logout' && method === 'post') return 'auth.logout'

  // Business
  if (u === 'business/index' && method === 'get') return 'business.index'
  if (u.match(/^business\/show\//) && method === 'get') return 'business.show'

  // Favorites
  if (u === 'favorite' && method === 'get') return 'favorite.list'
  if (u === 'favorite' && method === 'post') return 'favorite.toggle'

  // Geographic
  if (u === 'province' && method === 'get') return 'province.list'
  if (u.match(/^province\/\d+\/municipalities/) && method === 'get') return 'province.municipalities'
  if (u.match(/^municipality\/\d+\/districts/) && method === 'get') return 'municipality.districts'
  if (u === 'municipality' && method === 'get') return 'municipality.list'
  if (u === 'district' && method === 'get') return 'district.list'
  if (u === 'neighborhood' && method === 'get') return 'neighborhood.list'

  // Sectors & Types
  if (u === 'sector' && method === 'get') return 'sector.list'
  if (u === 'business-type' && method === 'get') return 'businessType.list'

  // Clients
  if (u === 'client' && method === 'get') return 'client.list'
  if (u.match(/^client\/\d+$/) && method === 'get') return 'client.show'

  // Employees
  if ((u === 'employee' || u === 'employees') && method === 'get') return 'employee.list'

  // Calendar
  if (u === 'calendar' && method === 'get') return 'calendar.list'
  if (u === 'calendar' && method === 'post') return 'calendar.create'

  // Notifications
  if (u === 'notification' && method === 'get') return 'notification.list'

  // Statistics
  if (u === 'statistics' && method === 'get') return 'statistics.general'
  if (u.match(/^statistics\//) && method === 'get') return 'statistics.business'
  if (u === 'statistics/total-clients-by-plan') return 'statistics.clientsByPlan'
  if (u === 'statistics/most-visited-businesses') return 'statistics.mostVisited'

  // AI
  if (u === 'ai/status' && method === 'get') return 'ai.status'
  if (u === 'ai/chat' && method === 'post') return 'ai.chat'
  if (u === 'ai/business-analysis' && method === 'post') return 'ai.analysis'
  if (u === 'ai/recommendations' && method === 'post') return 'ai.recommendations'
  if (u === 'ai/generate-description' && method === 'post') return 'ai.description'
  if (u === 'ai/similar' && method === 'post') return 'ai.similar'
  if (u === 'ai/search' && method === 'post') return 'ai.search'
  if (u === 'ai/market-context' && method === 'post') return 'ai.marketContext'

  // Advisor
  if (u === 'assigned-advisor' && method === 'get') return 'advisor.mine'

  // Client requests
  if (u === 'client-request/my-request' && method === 'get') return 'clientRequest.mine'
  if (u === 'client-request' && method === 'post') return 'clientRequest.create'

  // Leads
  if (u === 'leads' && method === 'get') return 'leads.list'

  // Activity Feed
  if (u === 'activity-feed' && method === 'get') return 'activityFeed.list'

  // Preferences
  if (u === 'preferences' && method === 'get') return 'preferences.get'
  if (u === 'preferences' && method === 'put') return 'preferences.update'

  return null
}

// ── Handler ──────────────────────────────────────────────
async function handleMockRequest(route, config) {
  await delay()

  const url = config.url || ''
  const data = config.data ? (typeof config.data === 'string' ? JSON.parse(config.data) : config.data) : {}
  const params = config.params || {}

  switch (route) {
    // ── Auth ──
    case 'auth.login': {
      // Accept any credentials for dev
      currentUser = data.email?.includes('admin') ? mockUsers.admin : mockUsers.client
      currentToken = 'mock-jwt-token-' + Date.now()
      return { Authorization: currentToken, user: currentUser }
    }
    case 'auth.register': {
      currentUser = {
        id: 100,
        names: data.names || 'Nuevo',
        surnames: data.surnames || 'Usuario',
        email: data.email,
        phone: data.phone || '',
        roles: [{ id_role: 50, name: 'Cliente' }],
        created_at: new Date().toISOString(),
      }
      currentToken = 'mock-jwt-token-' + Date.now()
      return { Authorization: currentToken, user: currentUser }
    }
    case 'auth.user': {
      return currentUser || mockUsers.client
    }
    case 'auth.logout': {
      currentUser = null
      currentToken = null
      return { message: 'Sesión cerrada' }
    }

    // ── Business ──
    case 'business.index': {
      let results = [...mockBusinessesFull]

      // Filter by condition/type
      if (params.condition === 'franchise') results = results.filter(b => b.type === 'franquicia')
      else if (params.condition === 'inmuebles') results = results.filter(b => b.type === 'inmueble')

      // Filter by name
      if (params.name) {
        const q = params.name.toLowerCase()
        results = results.filter(b => b.title.toLowerCase().includes(q) || b.description.toLowerCase().includes(q))
      }

      // Filter by province
      if (params.province_id) {
        const prov = mockProvinces.find(p => p.id === Number(params.province_id))
        if (prov) results = results.filter(b => b.location.province === prov.name)
      }

      // Filter by investment range
      if (params.min_investment) results = results.filter(b => b.financials.price >= Number(params.min_investment))
      if (params.max_investment) results = results.filter(b => b.financials.price <= Number(params.max_investment))

      // Filter by rental range
      if (params.min_rental) results = results.filter(b => b.financials.rent >= Number(params.min_rental))
      if (params.max_rental) results = results.filter(b => b.financials.rent <= Number(params.max_rental))

      // Filter by sectors (comma-separated IDs)
      if (params.sectors) {
        const sectorIds = params.sectors.split(',').map(Number)
        const sectorNames = Object.entries({
          1: ['Restauración y Hostelería', 'Restauración', 'Gastronomía', 'Pizzería'],
          2: ['Alimentación', 'Panadería', 'Cafetería', 'Helados'],
          3: ['Estética y Belleza', 'Peluquería'],
          4: ['Gimnasios y Deporte', 'Gimnasio', 'Deporte'],
          5: ['Salud y Clínicas', 'Salud', 'Clínica', 'Farmacia'],
          6: ['Ocio Nocturno', 'Ocio nocturno', 'Bar', 'Discoteca'],
          7: ['Locales Vacíos', 'Local Comercial', 'Retail'],
          8: ['Logística e Industrial', 'Logística', 'Industrial'],
          9: ['Tecnología', 'Informática'],
          10: ['Educación y Formación', 'Educación', 'Formación', 'Idiomas'],
          11: ['Moda y Retail', 'Moda'],
          12: ['Servicios Profesionales', 'Servicios', 'Lavandería'],
        }).filter(([id]) => sectorIds.includes(Number(id))).flatMap(([, names]) => names)
        results = results.filter(b => b.sectors.some(s => sectorNames.includes(s)))
      }

      // Filter characteristics
      if (params.smoke_outlet) results = results.filter(b => b.features.smokeOutlet)
      if (params.terrace) results = results.filter(b => b.features.terrace)

      // Sort
      if (params.order_by === 'price_asc') results.sort((a, b) => a.financials.price - b.financials.price)
      else if (params.order_by === 'price_desc') results.sort((a, b) => b.financials.price - a.financials.price)
      else if (params.order_by === 'newest') results.sort((a, b) => a.metadata.daysOnMarket - b.metadata.daysOnMarket)
      else if (params.order_by === 'most_viewed') results.sort((a, b) => b.metadata.views - a.metadata.views)

      // Pagination
      const page = Number(params.page) || 1
      const perPage = 12
      const start = (page - 1) * perPage
      const paged = results.slice(start, start + perPage)

      const allNormalized = mockBusinessesFull.map(normalizeBusinessForApi)
      return {
        data: paged.map(b => {
          const norm = normalizeBusinessForApi(b)
          const opp = computeOpportunityScore(norm, allNormalized)
          const compat = computeUserCompatibility(norm, mockPreferences)
          return { ...norm, opportunityScore: opp.score, opportunityLabel: opp.label, showOpportunity: opp.show, userCompatibility: compat.score, userCompatibilityReasons: compat.reasons, showCompatibility: compat.show }
        }),
        total: results.length,
        current_page: page,
        per_page: perPage,
        last_page: Math.ceil(results.length / perPage),
      }
    }
    case 'business.show': {
      const idMatch = url.match(/show\/(.+)$/)
      const id = idMatch ? idMatch[1] : null
      const biz = mockBusinessesFull.find(b => String(b.id) === id || b.metadata.reference === id)
      if (!biz) return { status: 404, message: 'Negocio no encontrado' }

      const normalized = normalizeBusinessForApi(biz)
      // Generate realistic timeline from business data
      const daysOnMarket = biz.metadata.daysOnMarket || 30
      const pubDate = new Date(Date.now() - daysOnMarket * 86400000)
      const timeline = [
        { date: pubDate.toISOString().slice(0, 10), type: 'publish', action: 'Publicado en portal', detail: `Precio inicial: ${new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(biz.financials.price)}`, user: 'Sistema' },
      ]
      // Price update ~7 days after publish
      if (daysOnMarket > 10) {
        const priceUpdateDate = new Date(pubDate.getTime() + 7 * 86400000)
        const oldPrice = Math.round(biz.financials.price * 1.08)
        timeline.push({ date: priceUpdateDate.toISOString().slice(0, 10), type: 'price', action: 'Ajuste de precio', detail: `${new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(oldPrice)} → ${new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(biz.financials.price)} (-8%)`, user: biz.agent.contact })
      }
      // Views milestone
      if (biz.metadata.views > 50) {
        const viewsDate = new Date(pubDate.getTime() + 14 * 86400000)
        timeline.push({ date: viewsDate.toISOString().slice(0, 10), type: 'views', action: `${biz.metadata.views} visitas alcanzadas`, detail: 'Alta demanda detectada', user: 'Sistema' })
      }
      // Favorites milestone
      if (biz.metadata.favorites > 5) {
        const favDate = new Date(pubDate.getTime() + 18 * 86400000)
        timeline.push({ date: favDate.toISOString().slice(0, 10), type: 'favorite', action: `${biz.metadata.favorites} usuarios lo marcaron favorito`, detail: '', user: 'Sistema' })
      }
      // Info requests
      if (daysOnMarket > 15) {
        const reqDate = new Date(pubDate.getTime() + 20 * 86400000)
        const reqCount = Math.max(1, Math.floor(biz.metadata.views / 30))
        timeline.push({ date: reqDate.toISOString().slice(0, 10), type: 'request', action: `${reqCount} solicitudes de informacion`, detail: 'Contactos recibidos via portal', user: biz.agent.contact })
      }
      // Feature update
      if (biz.features.terrace || biz.features.smokeOutlet) {
        const featDate = new Date(pubDate.getTime() + 12 * 86400000)
        const feats = [biz.features.terrace && 'terraza', biz.features.smokeOutlet && 'salida de humos'].filter(Boolean).join(', ')
        timeline.push({ date: featDate.toISOString().slice(0, 10), type: 'update', action: 'Datos actualizados', detail: `Confirmado: ${feats}`, user: biz.agent.contact })
      }
      // Sort by date
      timeline.sort((a, b) => new Date(b.date) - new Date(a.date))

      return {
        ...normalized,
        timeline,
        similarBusinesses: findSimilarBusinesses(normalizeBusinessForApi(biz), mockBusinessesFull.map(normalizeBusinessForApi), 6).map(s => ({ ...normalizeBusinessForApi(s.business), matchScore: s.matchScore, matchReasons: s.reasons })),
      }
    }

    // ── Favorites ──
    case 'favorite.list': {
      const favBiz = mockBusinessesFull.filter(b => favorites.includes(b.id))
      return favBiz.map(normalizeBusinessForApi)
    }
    case 'favorite.toggle': {
      const bizId = data.business_id
      const idx = favorites.indexOf(bizId)
      if (idx > -1) {
        favorites.splice(idx, 1)
        return { action: 'removed', business_id: bizId }
      } else {
        favorites.push(bizId)
        return { action: 'added', business_id: bizId }
      }
    }

    // ── Geographic ──
    case 'province.list':
      return mockProvinces
    case 'province.municipalities': {
      const provId = Number(url.match(/province\/(\d+)/)[1])
      return mockMunicipalities.filter(m => m.province_id === provId)
    }
    case 'municipality.list':
      return mockMunicipalities
    case 'municipality.districts': {
      const munId = Number(url.match(/municipality\/(\d+)/)[1])
      return mockDistricts.filter(d => d.municipality_id === munId)
    }
    case 'district.list':
      return mockDistricts
    case 'neighborhood.list':
      return mockNeighborhoods

    // ── Sectors & Types ──
    case 'sector.list':
      return mockSectors
    case 'businessType.list':
      return mockBusinessTypes

    // ── Clients ──
    case 'client.list':
      return mockClients
    case 'client.show': {
      const clientId = Number(url.match(/client\/(\d+)/)[1])
      return mockClients.find(c => c.id === clientId) || { status: 404 }
    }

    // ── Employees ──
    case 'employee.list':
      return mockEmployees

    // ── Calendar ──
    case 'calendar.list':
      return calendarEvents
    case 'calendar.create': {
      const newEvent = { id: calendarEvents.length + 1, ...data }
      calendarEvents.push(newEvent)
      return newEvent
    }

    // ── Notifications ──
    case 'notification.list':
      return notifications

    // ── Statistics ──
    case 'statistics.general':
      return mockStatistics
    case 'statistics.business': {
      const bizId = url.match(/statistics\/(.+)/)[1]
      const biz = mockBusinessesFull.find(b => String(b.id) === bizId)
      return {
        views_last_30_days: biz?.metadata?.views || 0,
        favorites_count: biz?.metadata?.favorites || 0,
        contact_requests: Math.floor(Math.random() * 10),
        avg_time_on_page: '2:45',
      }
    }
    case 'statistics.clientsByPlan':
      return mockStatistics.clientsByPlan
    case 'statistics.mostVisited':
      return [...mockBusinessesFull].sort((a, b) => b.metadata.views - a.metadata.views).slice(0, 10).map(normalizeBusinessForApi)

    // ── AI ──
    case 'ai.status':
      return { provider: 'Claude', model: 'claude-haiku-4-5-20251001', status: 'active' }
    case 'ai.chat': {
      await delay(600)
      const userMsg = data.message || data.content || ''
      const ctxBiz = data.business_id ? mockBusinessesFull.find(b => b.id === data.business_id || b.metadata?.reference === data.business_id) : null
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      const ctxNorm = ctxBiz ? normalizeBusinessForApi(ctxBiz) : null
      return {
        role: 'assistant',
        content: generateContextualChatResponse(userMsg, allNorm, ctxNorm),
      }
    }
    case 'ai.analysis': {
      await delay(800)
      const biz = mockBusinessesFull.find(b => b.id === data.business_id || b.metadata?.reference === data.business_id)
      if (!biz) return { error: 'Negocio no encontrado' }
      const normalized = normalizeBusinessForApi(biz)
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      return generateBusinessAnalysis(normalized, allNorm)
    }
    case 'ai.recommendations': {
      await delay(500)
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      const query = { sectors: [], province: '', maxPrice: null, minPrice: null, features: { terrace: false, smokeOutlet: false }, type: '' }
      if (mockPreferences.sectors?.length) query.sectors = mockPreferences.sectors
      if (mockPreferences.province) query.province = mockPreferences.province
      if (mockPreferences.maxBudget) query.maxPrice = mockPreferences.maxBudget
      const results = searchBusinessesWithAI(allNorm, query, 9)
      if (results.length === 0) {
        const fallback = mockBusinessesFull.filter(b => mockRecommendationIds.includes(b.id))
        return fallback.map(b => ({ ...normalizeBusinessForApi(b), matchScore: 75 }))
      }
      return results.map(r => ({ ...normalizeBusinessForApi(r.business), matchScore: r.matchScore, matchReasons: r.reasons }))
    }
    case 'ai.description': {
      await delay(600)
      return {
        description: 'Excepcional oportunidad de inversión en un negocio consolidado con amplia cartera de clientes. Ubicado en una zona de alto tránsito peatonal con excelente visibilidad y accesibilidad. Instalaciones modernas y completamente equipadas, listas para operar desde el primer día.'
      }
    }
    case 'ai.similar': {
      await delay(500)
      const biz = mockBusinessesFull.find(b => b.id === data.business_id || b.metadata?.reference === data.business_id)
      if (!biz) return []
      const normalized = normalizeBusinessForApi(biz)
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      const similar = findSimilarBusinesses(normalized, allNorm, data.limit || 6)
      return similar.map(s => ({ ...normalizeBusinessForApi(s.business), matchScore: s.matchScore, matchReasons: s.reasons }))
    }
    case 'ai.search': {
      await delay(400)
      const query = parseNaturalLanguageQuery(data.query || '')
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      const results = searchBusinessesWithAI(allNorm, query, data.limit || 12)
      return { query, results: results.map(r => ({ ...normalizeBusinessForApi(r.business), matchScore: r.matchScore, matchReasons: r.reasons })), total: results.length }
    }
    case 'ai.marketContext': {
      await delay(400)
      const biz = mockBusinessesFull.find(b => b.id === data.business_id || b.metadata?.reference === data.business_id)
      if (!biz) return { error: 'Negocio no encontrado' }
      const normalized = normalizeBusinessForApi(biz)
      const allNorm = mockBusinessesFull.map(normalizeBusinessForApi)
      return computeBusinessMarketContext(normalized, allNorm)
    }

    // ── Advisor ──
    case 'advisor.mine':
      return mockAdvisor

    // ── Client Requests ──
    case 'clientRequest.mine':
      return mockClientRequests
    case 'clientRequest.create': {
      const newReq = { id: mockClientRequests.length + 1, ...data, status: 'pendiente', created_at: new Date().toISOString() }
      mockClientRequests.push(newReq)
      return newReq
    }

    // ── Leads ──
    case 'leads.list':
      return mockLeads

    // ── Activity Feed ──
    case 'activityFeed.list':
      return mockActivityFeed

    // ── Preferences ──
    case 'preferences.get':
      return mockPreferences
    case 'preferences.update':
      Object.assign(mockPreferences, data)
      return mockPreferences

    default:
      console.warn(`[MockAPI] Unhandled route: ${route} (${config.method} ${config.url})`)
      return { status: 404, message: 'Mock endpoint not implemented' }
  }
}

// ── Install Mock Interceptor ─────────────────────────────
export function installMockApi(axiosInstance) {
  axiosInstance.interceptors.request.use(async (config) => {
    const method = (config.method || 'get').toLowerCase()
    const route = matchRoute(config.url, method)

    if (route) {
      // Cancel real request and return mock data
      const mockData = await handleMockRequest(route, config)

      // Create an axios-like response by throwing a "fulfilled" cancel
      const response = {
        data: mockData,
        status: 200,
        statusText: 'OK (Mock)',
        headers: {},
        config,
      }

      // Use adapter override to return mock response
      config.adapter = () => Promise.resolve(response)
    }

    return config
  })

  console.log('%c[COYAG] 🧪 Mock API activo — datos locales', 'color: #7367F0; font-weight: bold; font-size: 12px')
}
