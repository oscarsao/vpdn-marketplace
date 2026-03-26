// ═══════════════════════════════════════════════════════════
// COYAG VPDN — AI Engine
// Pure functions for intelligent analysis using real mock data
// ═══════════════════════════════════════════════════════════

import { computeGroupBySectors, computeQuartiles, computeInvestmentScore } from './statisticsEngine'

// ── Helpers ──────────────────────────────────────────────
function avg(arr) {
  if (!arr.length) return 0
  return arr.reduce((s, v) => s + v, 0) / arr.length
}

function getPrice(b) { return b.investment || b.financials?.price || 0 }
function getRent(b) { return b.rental || b.financials?.rent || 0 }
function getSize(b) { return b.size || b.features?.size || 0 }
function getDays(b) { return b.days_on_market || b.metadata?.daysOnMarket || 0 }
function getViews(b) { return b.times_viewed || b.metadata?.views || 0 }
function getSector(b) { return (b.sectors || [])[0]?.name || (b.sectors || [])[0] || '' }
function getProvince(b) { return b.province?.name || b.location?.province || '' }
function getMunicipality(b) { return b.municipality?.name || b.location?.municipality || '' }
function getType(b) { return b.business_type?.name || b.type || '' }
function hasTerrace(b) { return b.terrace || b.features?.terrace || false }
function hasSmokeOutlet(b) { return b.smoke_outlet || b.features?.smokeOutlet || false }
function getName(b) { return b.name || b.title || '' }
function formatEur(n) { return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n) }

// ── 0. Opportunity Score ─────────────────────────────────
// Detects "bargain" opportunities: low price percentile + fresh listing + high demand
export function computeOpportunityScore(business, allBusinesses) {
  const price = getPrice(business)
  const days = getDays(business)
  const views = getViews(business)
  const sector = getSector(business)

  if (!price || !sector) return { score: 0, label: '', show: false }

  const sectorPeers = allBusinesses.filter(b => getSector(b) === sector && getPrice(b) > 0)
  if (sectorPeers.length < 2) return { score: 0, label: '', show: false }

  const sectorPrices = sectorPeers.map(getPrice).sort((a, b) => a - b)
  const pricePercentile = Math.round((sectorPrices.filter(p => p <= price).length / sectorPrices.length) * 100)

  let score = 0
  // Price below 25th percentile = up to 40pts
  if (pricePercentile <= 15) score += 40
  else if (pricePercentile <= 25) score += 30
  else if (pricePercentile <= 40) score += 15

  // Fresh listing (< 30 days) = up to 25pts
  if (days <= 7) score += 25
  else if (days <= 15) score += 20
  else if (days <= 30) score += 10

  // High demand (views) = up to 20pts
  const avgViews = avg(sectorPeers.map(getViews))
  if (avgViews > 0) {
    const viewRatio = views / avgViews
    if (viewRatio >= 2) score += 20
    else if (viewRatio >= 1.3) score += 12
    else if (viewRatio >= 0.8) score += 5
  }

  // Has key features = up to 15pts
  if (hasTerrace(business)) score += 8
  if (hasSmokeOutlet(business)) score += 7

  const show = score >= 45
  let label = ''
  if (score >= 70) label = 'OPORTUNIDAD'
  else if (score >= 55) label = 'BUEN PRECIO'
  else if (score >= 45) label = 'INTERESANTE'

  return { score, label, show, pricePercentile }
}

// ── 0b. User-Business Compatibility ─────────────────────
// Computes how well a business matches user preferences
export function computeUserCompatibility(business, preferences) {
  if (!preferences) return { score: 0, show: false, reasons: [] }

  let score = 0
  let maxScore = 0
  const reasons = []

  // Price range match (30pts)
  const price = getPrice(business)
  if (preferences.min_investment || preferences.max_investment) {
    maxScore += 30
    const inRange = (!preferences.min_investment || price >= preferences.min_investment) &&
                    (!preferences.max_investment || price <= preferences.max_investment)
    if (inRange) { score += 30; reasons.push('Precio en rango') }
  }

  // Rent range match (15pts)
  const rent = getRent(business)
  if (preferences.min_rental !== undefined || preferences.max_rental) {
    maxScore += 15
    const inRange = (!preferences.min_rental || rent >= preferences.min_rental) &&
                    (!preferences.max_rental || rent <= preferences.max_rental)
    if (inRange) { score += 15; reasons.push('Alquiler en rango') }
  }

  // Sector match (25pts)
  if (preferences.sectors?.length) {
    maxScore += 25
    const bizSectors = (business.sectors || []).map(s => s.id || s)
    const match = bizSectors.some(s => preferences.sectors.includes(s))
    if (match) { score += 25; reasons.push('Sector preferido') }
  }

  // Province match (15pts)
  if (preferences.provinces?.length) {
    maxScore += 15
    const bizProvId = business.province?.id
    if (bizProvId && preferences.provinces.includes(bizProvId)) {
      score += 15; reasons.push('Zona preferida')
    }
  }

  // Type match (10pts)
  if (preferences.operation_types?.length) {
    maxScore += 10
    const bizType = (business.business_type?.name || business.type || '').toLowerCase()
    if (preferences.operation_types.some(t => bizType.includes(t))) {
      score += 10; reasons.push('Tipo de operacion')
    }
  }

  // Features (5pts)
  if (preferences.features) {
    if (preferences.features.terrace) {
      maxScore += 5
      if (business.terrace || business.features?.terrace) { score += 5; reasons.push('Terraza') }
    }
    if (preferences.features.smokeOutlet) {
      maxScore += 5
      if (business.smoke_outlet || business.features?.smokeOutlet) { score += 5; reasons.push('Humos') }
    }
  }

  const pct = maxScore > 0 ? Math.round((score / maxScore) * 100) : 0
  return { score: pct, show: pct >= 40, reasons }
}

// ── 1. Natural Language Query Parser ─────────────────────
const SECTOR_KEYWORDS = {
  'restaurante': 'Restauración y Hostelería', 'restauración': 'Restauración y Hostelería',
  'hostelería': 'Restauración y Hostelería', 'bar': 'Ocio Nocturno',
  'cafetería': 'Cafetería', 'café': 'Cafetería', 'cafe': 'Cafetería',
  'panadería': 'Panadería', 'panaderia': 'Panadería',
  'pizzería': 'Pizzería', 'pizzeria': 'Pizzería', 'pizza': 'Pizzería',
  'peluquería': 'Peluquería', 'peluqueria': 'Peluquería', 'belleza': 'Estética y Belleza',
  'gimnasio': 'Gimnasio', 'deporte': 'Deporte',
  'clínica': 'Clínica', 'clinica': 'Clínica', 'farmacia': 'Farmacia', 'salud': 'Salud',
  'lavandería': 'Lavandería', 'lavanderia': 'Lavandería',
  'helados': 'Helados', 'heladería': 'Helados',
  'coworking': 'Coworking', 'oficina': 'Oficinas',
  'moda': 'Moda', 'ropa': 'Moda',
  'taller': 'Taller', 'automoción': 'Automoción',
  'academia': 'Educación', 'formación': 'Formación', 'educación': 'Educación',
  'cervecería': 'Cervecería', 'cerveceria': 'Cervecería',
  'local comercial': 'Local Comercial', 'nave': 'Logística',
  'industrial': 'Industrial',
}

const PROVINCE_KEYWORDS = ['madrid', 'barcelona', 'valencia', 'sevilla', 'málaga', 'malaga', 'vizcaya', 'alicante', 'guipúzcoa']
const DISTRICT_KEYWORDS = ['centro', 'salamanca', 'chamberí', 'chamberi', 'arganzuela', 'retiro', 'chamartín', 'chamartin', 'tetuán', 'tetuan', 'latina', 'carabanchel', 'usera', 'hortaleza', 'eixample', 'gracia', 'sarrià']

export function parseNaturalLanguageQuery(message) {
  const msg = message.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  const msgOriginal = message.toLowerCase()

  const result = {
    sectors: [],
    province: '',
    municipality: '',
    district: '',
    maxPrice: null,
    minPrice: null,
    maxRent: null,
    minRent: null,
    features: { terrace: false, smokeOutlet: false },
    type: '',
    textQuery: '',
  }

  // Extract sectors
  for (const [keyword, sector] of Object.entries(SECTOR_KEYWORDS)) {
    if (msgOriginal.includes(keyword)) {
      if (!result.sectors.includes(sector)) result.sectors.push(sector)
    }
  }

  // Extract province
  for (const prov of PROVINCE_KEYWORDS) {
    const provNorm = prov.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    if (msg.includes(provNorm)) {
      result.province = prov.charAt(0).toUpperCase() + prov.slice(1)
      // Fix accented names
      if (result.province === 'Malaga') result.province = 'Málaga'
      if (result.province === 'Guipuzcoa') result.province = 'Guipúzcoa'
      break
    }
  }

  // Extract district
  for (const dist of DISTRICT_KEYWORDS) {
    const distNorm = dist.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    if (msg.includes(distNorm)) {
      result.district = dist.charAt(0).toUpperCase() + dist.slice(1)
      if (result.district === 'Chamberi') result.district = 'Chamberí'
      if (result.district === 'Chamartin') result.district = 'Chamartín'
      if (result.district === 'Tetuan') result.district = 'Tetuán'
      break
    }
  }

  // Extract price ranges
  const pricePatterns = [
    /menos de (\d+(?:\.\d+)?)\s*k/i,
    /hasta (\d+(?:\.\d+)?)\s*k/i,
    /maximo (\d+(?:\.\d+)?)\s*k/i,
    /max(?:imo)? (\d+(?:\.\d+)?)\s*k/i,
    /por debajo de (\d+(?:\.\d+)?)\s*k/i,
    /menos de (\d+(?:\.\d{3})+)/,
    /hasta (\d+(?:\.\d{3})+)/,
  ]
  for (const pattern of pricePatterns) {
    const match = msgOriginal.match(pattern)
    if (match) {
      let val = match[1].replace(/\./g, '')
      val = parseFloat(val)
      if (val < 10000) val *= 1000 // "100k" = 100000
      result.maxPrice = val
      break
    }
  }

  const minPricePatterns = [
    /mas de (\d+(?:\.\d+)?)\s*k/i,
    /desde (\d+(?:\.\d+)?)\s*k/i,
    /minimo (\d+(?:\.\d+)?)\s*k/i,
  ]
  for (const pattern of minPricePatterns) {
    const match = msgOriginal.match(pattern)
    if (match) {
      let val = parseFloat(match[1].replace(/\./g, ''))
      if (val < 10000) val *= 1000
      result.minPrice = val
      break
    }
  }

  // Extract features
  if (msg.includes('terraza')) result.features.terrace = true
  if (msg.includes('humos') || msg.includes('salida de humos')) result.features.smokeOutlet = true

  // Extract type
  if (msg.includes('franquicia')) result.type = 'franquicia'
  else if (msg.includes('inmueble') || msg.includes('local') || msg.includes('nave')) result.type = 'inmueble'
  else if (msg.includes('traspaso')) result.type = 'traspaso'

  return result
}

// ── 2. AI-Powered Search ─────────────────────────────────
export function searchBusinessesWithAI(businesses, query, limit = 10) {
  const scored = businesses.map(b => {
    let score = 0
    const reasons = []

    // Sector match (30pts)
    const bSector = getSector(b)
    if (query.sectors.length > 0) {
      const sectorMatch = query.sectors.some(s =>
        bSector.toLowerCase().includes(s.toLowerCase()) || s.toLowerCase().includes(bSector.toLowerCase())
      )
      if (sectorMatch) { score += 30; reasons.push(`Sector: ${bSector}`) }
    }

    // Location match (25pts)
    const bProv = getProvince(b)
    const bDist = b.district?.name || b.location?.district || ''
    if (query.province && bProv.toLowerCase() === query.province.toLowerCase()) {
      score += 15; reasons.push(`Provincia: ${bProv}`)
    }
    if (query.district && bDist.toLowerCase().includes(query.district.toLowerCase())) {
      score += 10; reasons.push(`Distrito: ${bDist}`)
    }

    // Price range (20pts)
    const price = getPrice(b)
    let priceInRange = true
    if (query.maxPrice && price > query.maxPrice) priceInRange = false
    if (query.minPrice && price < query.minPrice) priceInRange = false
    if (priceInRange && (query.maxPrice || query.minPrice)) {
      score += 20; reasons.push(`Precio: ${formatEur(price)}`)
    }

    // Features (15pts)
    if (query.features.terrace && hasTerrace(b)) { score += 8; reasons.push('Terraza') }
    if (query.features.smokeOutlet && hasSmokeOutlet(b)) { score += 7; reasons.push('Salida de humos') }

    // Type match (5pts)
    if (query.type) {
      const bType = (b.type || b.business_type?.name || '').toLowerCase()
      if (bType.includes(query.type)) { score += 5; reasons.push(`Tipo: ${getType(b)}`) }
    }

    // Demand bonus (5pts)
    const views = getViews(b)
    if (views > 100) { score += 3 }
    if (views > 200) { score += 2 }

    return { business: b, score, reasons, matchScore: Math.min(99, Math.round(score * 1.1)) }
  })

  return scored
    .filter(s => s.score > 0)
    .sort((a, b) => b.score - a.score)
    .slice(0, limit)
}

// ── 3. Business Analysis ─────────────────────────────────
export function generateBusinessAnalysis(business, allBusinesses) {
  const price = getPrice(business)
  const rent = getRent(business)
  const size = getSize(business)
  const days = getDays(business)
  const sector = getSector(business)
  const province = getProvince(business)
  const score = computeInvestmentScore(business, allBusinesses)

  // Sector peers
  const sectorPeers = allBusinesses.filter(b => getSector(b) === sector && b.id !== business.id)
  const sectorAvgPrice = sectorPeers.length ? avg(sectorPeers.map(getPrice)) : price
  const sectorAvgRent = sectorPeers.length ? avg(sectorPeers.map(getRent)) : rent
  const sectorAvgDays = sectorPeers.length ? avg(sectorPeers.map(getDays)) : days

  const priceDiffPct = sectorAvgPrice > 0 ? Math.round(((price - sectorAvgPrice) / sectorAvgPrice) * 100) : 0
  const rentDiffPct = sectorAvgRent > 0 ? Math.round(((rent - sectorAvgRent) / sectorAvgRent) * 100) : 0

  // Price percentile
  const allPrices = allBusinesses.map(getPrice).filter(p => p > 0).sort((a, b) => a - b)
  const pricePercentile = Math.round((allPrices.filter(p => p <= price).length / allPrices.length) * 100)

  // Valuation range
  const valuationLow = Math.round(sectorAvgPrice * 0.85)
  const valuationHigh = Math.round(sectorAvgPrice * 1.15)

  // Strengths & Weaknesses
  const strengths = []
  const weaknesses = []

  if (priceDiffPct < -10) strengths.push(`Precio ${Math.abs(priceDiffPct)}% por debajo de la media del sector`)
  else if (priceDiffPct > 10) weaknesses.push(`Precio ${priceDiffPct}% por encima de la media del sector`)

  if (rentDiffPct < -5) strengths.push(`Alquiler ${Math.abs(rentDiffPct)}% inferior a la media`)
  else if (rentDiffPct > 10) weaknesses.push(`Alquiler ${rentDiffPct}% superior a la media`)

  if (hasTerrace(business)) strengths.push('Dispone de terraza (premium para hosteleria)')
  if (hasSmokeOutlet(business)) strengths.push('Salida de humos instalada')
  if (!hasSmokeOutlet(business) && ['Restauración y Hostelería', 'Pizzería', 'Cafetería'].includes(sector)) {
    weaknesses.push('Sin salida de humos (limita opciones de cocina)')
  }

  if (days < 30) strengths.push('Recien publicado, alta frescura')
  else if (days > 90) weaknesses.push(`${days} dias en mercado (puede indicar precio elevado)`)

  if (size > 100) strengths.push(`Superficie amplia: ${size}m2`)
  if (getViews(business) > 150) strengths.push('Alta demanda: muchas visitas al anuncio')

  if (province === 'Madrid' || province === 'Barcelona') strengths.push(`Ubicacion en ${province} (alta demanda)`)

  if (rent > 0 && price > 0) {
    const ratio = price / (rent * 12)
    if (ratio < 10) strengths.push(`Ratio precio/alquiler favorable: ${ratio.toFixed(1)}x`)
    else if (ratio > 20) weaknesses.push(`Ratio precio/alquiler alto: ${ratio.toFixed(1)}x`)
  }

  // Risk level
  let riskLevel = 'Medio'
  if (score >= 70 && priceDiffPct <= 0 && days < 60) riskLevel = 'Bajo'
  else if (score < 40 || priceDiffPct > 20 || days > 120) riskLevel = 'Alto'

  // Summary
  const summary = `${getName(business)} es un negocio de ${sector.toLowerCase()} en ${province} con un precio de ${formatEur(price)}. ` +
    `${priceDiffPct < 0 ? `Se situa un ${Math.abs(priceDiffPct)}% por debajo` : `Se situa un ${priceDiffPct}% por encima`} de la media del sector (${formatEur(sectorAvgPrice)}). ` +
    `Con una puntuacion de inversion de ${score}/100, el nivel de riesgo es ${riskLevel.toLowerCase()}.`

  return {
    summary,
    strengths,
    weaknesses,
    riskLevel,
    score,
    valuation: { low: valuationLow, high: valuationHigh, sectorAvg: Math.round(sectorAvgPrice) },
    metrics: {
      priceDiffPct,
      rentDiffPct,
      pricePercentile,
      sectorPeerCount: sectorPeers.length,
      sectorAvgDays: Math.round(sectorAvgDays),
    },
  }
}

// ── 4. Similar Businesses Finder ─────────────────────────
export function findSimilarBusinesses(business, allBusinesses, limit = 6) {
  const bPrice = getPrice(business)
  const bSize = getSize(business)
  const bSector = getSector(business)
  const bProvince = getProvince(business)
  const bTerrace = hasTerrace(business)
  const bSmoke = hasSmokeOutlet(business)

  const scored = allBusinesses
    .filter(b => b.id !== business.id)
    .map(b => {
      let score = 0
      const reasons = []

      // Same sector (40pts)
      if (getSector(b) === bSector) {
        score += 40
        reasons.push('Mismo sector')
      }

      // Price proximity (25pts) - closer = higher
      const pPrice = getPrice(b)
      if (bPrice > 0 && pPrice > 0) {
        const priceDiff = Math.abs(pPrice - bPrice) / bPrice
        const priceScore = Math.max(0, 25 * (1 - priceDiff))
        score += priceScore
        if (priceDiff < 0.2) reasons.push('Precio similar')
      }

      // Size proximity (15pts)
      const pSize = getSize(b)
      if (bSize > 0 && pSize > 0) {
        const sizeDiff = Math.abs(pSize - bSize) / bSize
        const sizeScore = Math.max(0, 15 * (1 - sizeDiff))
        score += sizeScore
        if (sizeDiff < 0.3) reasons.push('Tamano similar')
      }

      // Same province (10pts)
      if (getProvince(b) === bProvince) {
        score += 10
        reasons.push('Misma zona')
      }

      // Shared features (10pts)
      if (hasTerrace(b) === bTerrace) score += 5
      if (hasSmokeOutlet(b) === bSmoke) score += 5

      return {
        business: b,
        score: Math.round(score),
        matchScore: Math.min(99, Math.round(score)),
        reasons,
      }
    })

  return scored
    .sort((a, b) => b.score - a.score)
    .slice(0, limit)
}

// ── 5. Market Context for a Business ─────────────────────
export function computeBusinessMarketContext(business, allBusinesses) {
  const price = getPrice(business)
  const rent = getRent(business)
  const size = getSize(business)
  const days = getDays(business)
  const sector = getSector(business)

  // Sector peers
  const sectorPeers = allBusinesses.filter(b => getSector(b) === sector)
  const sectorPrices = sectorPeers.map(getPrice).filter(p => p > 0).sort((a, b) => a - b)
  const sectorRents = sectorPeers.map(getRent).filter(r => r > 0)
  const sectorDays = sectorPeers.map(getDays).filter(d => d > 0)
  const sectorSizes = sectorPeers.map(getSize).filter(s => s > 0)

  // Price percentile within sector
  const pricePercentile = sectorPrices.length > 0
    ? Math.round((sectorPrices.filter(p => p <= price).length / sectorPrices.length) * 100)
    : 50

  // EUR/m2 comparison
  const myPricePerSqm = size > 0 ? Math.round(price / size) : 0
  const sectorSqmValues = sectorPeers
    .filter(b => getSize(b) > 0 && getPrice(b) > 0)
    .map(b => getPrice(b) / getSize(b))
  const sectorMedianSqm = sectorSqmValues.length > 0
    ? Math.round(sectorSqmValues.sort((a, b) => a - b)[Math.floor(sectorSqmValues.length / 2)])
    : 0
  const sqmDiffPct = sectorMedianSqm > 0 ? Math.round(((myPricePerSqm - sectorMedianSqm) / sectorMedianSqm) * 100) : 0

  // Demand vs average
  const myViews = getViews(business)
  const sectorViews = sectorPeers.map(getViews).filter(v => v > 0)
  const avgSectorViews = avg(sectorViews)
  const demandIndex = avgSectorViews > 0 ? Math.round((myViews / avgSectorViews) * 100) : 100

  // Days on market vs sector
  const avgSectorDays = Math.round(avg(sectorDays))
  const daysDiff = days - avgSectorDays

  // Sector health (more listings = active market)
  const totalListings = allBusinesses.length
  const sectorShare = totalListings > 0 ? Math.round((sectorPeers.length / totalListings) * 100) : 0
  let sectorHealth = 'Normal'
  if (sectorPeers.length >= 5 && sectorShare > 15) sectorHealth = 'Activo'
  else if (sectorPeers.length <= 2) sectorHealth = 'Limitado'

  return {
    pricePercentile,
    pricePercentileLabel: pricePercentile <= 25 ? 'Muy competitivo' : pricePercentile <= 50 ? 'Competitivo' : pricePercentile <= 75 ? 'En rango' : 'Premium',
    myPricePerSqm,
    sectorMedianSqm,
    sqmDiffPct,
    demandIndex,
    demandLabel: demandIndex >= 120 ? 'Alta demanda' : demandIndex >= 80 ? 'Demanda normal' : 'Demanda baja',
    avgSectorDays,
    daysDiff,
    sectorHealth,
    sectorPeerCount: sectorPeers.length,
    sectorName: sector,
    sectorAvgPrice: Math.round(avg(sectorPrices)),
    sectorAvgRent: Math.round(avg(sectorRents)),
    sectorAvgSize: Math.round(avg(sectorSizes)),
    sectorPriceRange: { min: sectorPrices[0] || 0, max: sectorPrices[sectorPrices.length - 1] || 0 },
  }
}

// ── 6. Contextual Chat Response ──────────────────────────
export function generateContextualChatResponse(message, businesses, currentBusiness = null) {
  const msg = message.toLowerCase()

  // If scoped to a business
  if (currentBusiness) {
    return generateBusinessScopedResponse(msg, currentBusiness, businesses)
  }

  // Greeting
  if (msg.match(/^(hola|buenos|buenas|hey|hi)/)) {
    const total = businesses.length
    const sectors = [...new Set(businesses.map(getSector))].length
    return `Hola! Soy el asistente IA de COYAG. Tengo acceso a ${total} negocios en ${sectors} sectores diferentes. Puedo ayudarte a:\n\n` +
      `- **Buscar negocios**: "restaurante en Madrid menos de 100k"\n` +
      `- **Analizar mercado**: "como esta el sector de hosteleria?"\n` +
      `- **Comparar opciones**: "que es mejor, traspaso o franquicia?"\n\n` +
      `Que necesitas?`
  }

  // Search intent
  const parsed = parseNaturalLanguageQuery(message)
  const hasFilters = parsed.sectors.length > 0 || parsed.province || parsed.maxPrice || parsed.minPrice || parsed.features.terrace || parsed.features.smokeOutlet

  if (hasFilters) {
    const results = searchBusinessesWithAI(businesses, parsed, 5)
    if (results.length === 0) {
      return `No he encontrado negocios que coincidan exactamente con tu busqueda. Prueba ampliando el rango de precio o cambiando la zona.`
    }

    let response = `He encontrado **${results.length} negocios** que coinciden:\n\n`
    results.forEach((r, i) => {
      const b = r.business
      response += `${i + 1}. **${getName(b)}** — ${formatEur(getPrice(b))} | ${getSector(b)} | ${getProvince(b)} (${r.matchScore}% match)\n`
    })
    response += `\nQuieres que analice alguno en detalle?`
    return response
  }

  // Sector analysis
  if (msg.includes('sector') || msg.includes('mercado') || msg.includes('estadistica')) {
    const groups = computeGroupBySectors(businesses)
    let response = `**Resumen del mercado** (${businesses.length} negocios):\n\n`
    groups.slice(0, 5).forEach(g => {
      response += `- **${g.sectors_segment}**: ${g.total} negocios, precio medio ${formatEur(g.avg_investment)}, alquiler medio ${formatEur(g.avg_rental)}\n`
    })
    return response
  }

  // Comparison: traspaso vs franquicia
  if (msg.includes('traspaso') && msg.includes('franquicia')) {
    const traspasos = businesses.filter(b => (b.type || '').includes('traspaso'))
    const franquicias = businesses.filter(b => (b.type || '').includes('franquicia'))
    return `**Comparacion Traspaso vs Franquicia:**\n\n` +
      `| | Traspasos | Franquicias |\n|---|---|---|\n` +
      `| Total | ${traspasos.length} | ${franquicias.length} |\n` +
      `| Precio medio | ${formatEur(avg(traspasos.map(getPrice)))} | ${formatEur(avg(franquicias.map(getPrice)))} |\n` +
      `| Alquiler medio | ${formatEur(avg(traspasos.map(getRent)))} | ${formatEur(avg(franquicias.map(getRent)))} |\n\n` +
      `Los traspasos ofrecen mas flexibilidad pero mas riesgo. Las franquicias incluyen modelo probado y soporte.`
  }

  // Investment advice
  if (msg.includes('inversión') || msg.includes('invertir') || msg.includes('presupuesto') || msg.includes('recomienda')) {
    const quartiles = computeQuartiles(businesses, 'investment')
    return `**Guia de inversion en el mercado actual:**\n\n` +
      `- Rango de precios: ${formatEur(quartiles.min)} — ${formatEur(quartiles.max)}\n` +
      `- Precio medio: ${formatEur(quartiles.avg)}\n` +
      `- Precio mediano: ${formatEur(quartiles.median)}\n` +
      `- Q1 (25%): ${formatEur(quartiles.lower_quartile)} — Q3 (75%): ${formatEur(quartiles.upper_quartile)}\n\n` +
      `Para invertir, te recomiendo definir tu presupuesto y sector de interes. Puedes preguntarme algo como "restaurante en Madrid menos de 150k".`
  }

  // Rentability
  if (msg.includes('rentab') || msg.includes('roi') || msg.includes('rendimiento')) {
    const withRent = businesses.filter(b => getRent(b) > 0 && getPrice(b) > 0)
    const yields = withRent.map(b => (getRent(b) * 12 / getPrice(b)) * 100)
    const avgYield = avg(yields)
    return `**Rentabilidad del mercado:**\n\n` +
      `- Rentabilidad bruta media: **${avgYield.toFixed(1)}%** anual\n` +
      `- Negocios analizados: ${withRent.length}\n` +
      `- Rango: ${Math.min(...yields).toFixed(1)}% — ${Math.max(...yields).toFixed(1)}%\n\n` +
      `Una rentabilidad bruta superior al 8% se considera buena en el mercado de traspasos.`
  }

  // Default
  return `Puedo ayudarte con:\n\n` +
    `- **Buscar negocios**: describe lo que buscas (sector, zona, precio)\n` +
    `- **Analizar mercado**: "como esta el mercado de hosteleria?"\n` +
    `- **Comparar**: "traspaso vs franquicia"\n` +
    `- **Inversion**: "que presupuesto necesito?"\n` +
    `- **Rentabilidad**: "cual es la rentabilidad media?"\n\n` +
    `Preguntame lo que necesites!`
}

// ── Business-Scoped Chat ─────────────────────────────────
function generateBusinessScopedResponse(msg, business, allBusinesses) {
  const analysis = generateBusinessAnalysis(business, allBusinesses)
  const context = computeBusinessMarketContext(business, allBusinesses)
  const name = getName(business)
  const price = getPrice(business)
  const rent = getRent(business)

  // Price question
  if (msg.includes('precio') || msg.includes('caro') || msg.includes('barato') || msg.includes('buen precio')) {
    return `**Analisis de precio de ${name}:**\n\n` +
      `- Precio: ${formatEur(price)} (percentil ${context.pricePercentile} del sector)\n` +
      `- Media del sector (${context.sectorName}): ${formatEur(context.sectorAvgPrice)}\n` +
      `- Diferencia: ${analysis.metrics.priceDiffPct > 0 ? '+' : ''}${analysis.metrics.priceDiffPct}%\n` +
      `- Rango en el sector: ${formatEur(context.sectorPriceRange.min)} — ${formatEur(context.sectorPriceRange.max)}\n\n` +
      `${context.pricePercentile <= 40 ? 'El precio esta en un rango competitivo para el sector.' : 'El precio esta por encima de la mediana del sector.'}`
  }

  // Risk question
  if (msg.includes('riesgo') || msg.includes('debilidad') || msg.includes('problema')) {
    let response = `**Analisis de riesgos de ${name}:**\n\nNivel de riesgo: **${analysis.riskLevel}**\n\n`
    if (analysis.weaknesses.length) {
      response += `Puntos de atencion:\n`
      analysis.weaknesses.forEach(w => { response += `- ${w}\n` })
    } else {
      response += `No se han identificado debilidades significativas.\n`
    }
    response += `\nFortalezas que compensan:\n`
    analysis.strengths.slice(0, 3).forEach(s => { response += `- ${s}\n` })
    return response
  }

  // Comparison question
  if (msg.includes('compara') || msg.includes('similar') || msg.includes('parecido')) {
    const similar = findSimilarBusinesses(business, allBusinesses, 3)
    let response = `**Negocios similares a ${name}:**\n\n`
    similar.forEach((s, i) => {
      const sb = s.business
      response += `${i + 1}. **${getName(sb)}** — ${formatEur(getPrice(sb))} | ${s.matchScore}% similar\n   ${s.reasons.join(', ')}\n\n`
    })
    return response
  }

  // ROI / investment
  if (msg.includes('roi') || msg.includes('rentab') || msg.includes('inversion')) {
    if (rent > 0 && price > 0) {
      const grossYield = ((rent * 12) / price * 100).toFixed(1)
      const ratio = (price / (rent * 12)).toFixed(1)
      return `**Metricas de inversion de ${name}:**\n\n` +
        `- Rentabilidad bruta: **${grossYield}%** anual\n` +
        `- Ratio precio/alquiler: **${ratio}x**\n` +
        `- Alquiler mensual: ${formatEur(rent)}\n` +
        `- Break-even estimado: **${Math.round(price / (rent * 0.3))} meses** (asumiendo margen 30%)\n\n` +
        `${parseFloat(grossYield) >= 8 ? 'La rentabilidad es buena para el mercado actual.' : 'La rentabilidad esta por debajo de la media del mercado (8%).'}`
    }
    return `No dispongo de datos suficientes de alquiler para calcular la rentabilidad de este negocio.`
  }

  // General question about the business
  return `**Resumen de ${name}:**\n\n${analysis.summary}\n\n` +
    `**Fortalezas:**\n${analysis.strengths.map(s => `- ${s}`).join('\n')}\n\n` +
    `**Puntuacion de inversion:** ${analysis.score}/100\n` +
    `**Valoracion estimada:** ${formatEur(analysis.valuation.low)} — ${formatEur(analysis.valuation.high)}\n\n` +
    `Puedes preguntarme sobre el precio, riesgos, comparacion con similares o rentabilidad.`
}
