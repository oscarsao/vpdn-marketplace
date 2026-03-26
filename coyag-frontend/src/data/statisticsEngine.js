// ═══════════════════════════════════════════════════════════
// COYAG VPDN — Statistics Engine
// Pure functions that compute market analytics from business data
// ═══════════════════════════════════════════════════════════

/**
 * Group businesses by sector and compute aggregates
 * @param {Array} businesses - normalized business array
 * @param {'investment'|'rental'} mode - price mode
 * @returns {Array} [{sectors_segment, avg_investment, avg_rental, avg_sqm, total, businesses}]
 */
export function computeGroupBySectors(businesses, mode = 'investment') {
  const groups = {}

  businesses.forEach(b => {
    const sectorNames = (b.sectors || []).map(s => s.name || s)
    const key = sectorNames[0] || 'Sin sector'

    if (!groups[key]) {
      groups[key] = { prices: [], rents: [], sqms: [], sizes: [], businesses: [] }
    }

    const price = b.investment || b.financials?.price || 0
    const rent = b.rental || b.financials?.rent || 0
    const size = b.size || b.features?.size || 0

    groups[key].prices.push(price)
    groups[key].rents.push(rent)
    if (size > 0) groups[key].sizes.push(size)
    if (size > 0 && price > 0) groups[key].sqms.push(price / size)
    groups[key].businesses.push(b)
  })

  return Object.entries(groups).map(([sector, data]) => ({
    sectors_segment: sector,
    avg_investment: Math.round(avg(data.prices)),
    avg_rental: Math.round(avg(data.rents)),
    avg_sqm: Math.round(avg(data.sqms)),
    avg_size: Math.round(avg(data.sizes)),
    total: data.businesses.length,
    businesses: data.businesses,
  })).sort((a, b) => b.total - a.total)
}

/**
 * Group businesses by price segment
 * @param {Array} businesses
 * @param {'investment'|'rental'} mode
 * @returns {Array} [{price_segment, total, avg_price, avg_days_on_market}]
 */
export function computeGroupByPriceSegment(businesses, mode = 'investment') {
  const segments = mode === 'investment'
    ? [
        { label: '< 50K', min: 0, max: 50000 },
        { label: '50K - 100K', min: 50000, max: 100000 },
        { label: '100K - 200K', min: 100000, max: 200000 },
        { label: '200K - 500K', min: 200000, max: 500000 },
        { label: '500K - 1M', min: 500000, max: 1000000 },
        { label: '> 1M', min: 1000000, max: Infinity },
      ]
    : [
        { label: '< 1.000', min: 0, max: 1000 },
        { label: '1.000 - 2.000', min: 1000, max: 2000 },
        { label: '2.000 - 3.500', min: 2000, max: 3500 },
        { label: '3.500 - 5.000', min: 3500, max: 5000 },
        { label: '> 5.000', min: 5000, max: Infinity },
      ]

  return segments.map(seg => {
    const matching = businesses.filter(b => {
      const val = mode === 'investment'
        ? (b.investment || b.financials?.price || 0)
        : (b.rental || b.financials?.rent || 0)
      return val >= seg.min && val < seg.max
    })

    const prices = matching.map(b =>
      mode === 'investment' ? (b.investment || b.financials?.price || 0) : (b.rental || b.financials?.rent || 0)
    )
    const daysArr = matching.map(b => b.days_on_market || b.metadata?.daysOnMarket || 0)

    return {
      price_segment: seg.label,
      total: matching.length,
      avg_price: Math.round(avg(prices)),
      avg_days_on_market: Math.round(avg(daysArr)),
    }
  })
}

/**
 * Compute quartile statistics for price distribution
 * @param {Array} businesses
 * @param {'investment'|'rental'} mode
 * @returns {Object} {median, avg, upper_quartile, lower_quartile, min, max, count}
 */
export function computeQuartiles(businesses, mode = 'investment') {
  const values = businesses
    .map(b => mode === 'investment' ? (b.investment || b.financials?.price || 0) : (b.rental || b.financials?.rent || 0))
    .filter(v => v > 0)
    .sort((a, b) => a - b)

  if (values.length === 0) return { median: 0, avg: 0, upper_quartile: 0, lower_quartile: 0, min: 0, max: 0, count: 0 }

  return {
    median: percentile(values, 50),
    avg: Math.round(avg(values)),
    upper_quartile: percentile(values, 75),
    lower_quartile: percentile(values, 25),
    min: values[0],
    max: values[values.length - 1],
    count: values.length,
  }
}

/**
 * Compute scatter data: price vs price/sqm for each business
 * @param {Array} businesses
 * @param {'investment'|'rental'} mode
 * @returns {Array} [{x: price, y: pricePerSqm, id, name, sector, size}]
 */
export function computeScatterData(businesses, mode = 'investment') {
  return businesses
    .filter(b => {
      const size = b.size || b.features?.size || 0
      const price = mode === 'investment' ? (b.investment || b.financials?.price || 0) : (b.rental || b.financials?.rent || 0)
      return size > 0 && price > 0
    })
    .map(b => {
      const size = b.size || b.features?.size || 0
      const price = mode === 'investment' ? (b.investment || b.financials?.price || 0) : (b.rental || b.financials?.rent || 0)
      return {
        x: price,
        y: Math.round(price / size),
        id: b.id,
        id_code: b.id_code || b.metadata?.reference || '',
        name: b.name || b.title || '',
        sector: (b.sectors || [])[0]?.name || (b.sectors || [])[0] || 'Sin sector',
        size,
      }
    })
}

/**
 * Filter businesses by geography (cascading)
 */
export function filterByGeography(businesses, filters = {}) {
  let result = [...businesses]
  if (filters.province) {
    result = result.filter(b => {
      const prov = b.province?.name || b.location?.province || ''
      return prov === filters.province
    })
  }
  if (filters.municipality) {
    result = result.filter(b => {
      const mun = b.municipality?.name || b.location?.municipality || ''
      return mun === filters.municipality
    })
  }
  if (filters.district) {
    result = result.filter(b => {
      const dis = b.district?.name || b.location?.district || ''
      return dis === filters.district
    })
  }
  if (filters.neighborhood) {
    result = result.filter(b => {
      const nb = b.neighborhood?.name || b.location?.neighborhood || ''
      return nb === filters.neighborhood
    })
  }
  return result
}

/**
 * Compute investment score for a single business (0-100)
 */
export function computeInvestmentScore(business, allBusinesses) {
  let score = 0

  const price = business.investment || business.financials?.price || 0
  const size = business.size || business.features?.size || 0
  const rent = business.rental || business.financials?.rent || 0
  const days = business.days_on_market || business.metadata?.daysOnMarket || 0
  const views = business.times_viewed || business.metadata?.views || 0
  const favs = business.metadata?.favorites || 0
  const smokeOutlet = business.smoke_outlet || business.features?.smokeOutlet || false
  const terrace = business.terrace || business.features?.terrace || false
  const sector = (business.sectors || [])[0]?.name || (business.sectors || [])[0] || ''

  // 1. Price vs sector avg (25 pts) — lower is better
  const sectorBiz = allBusinesses.filter(b => {
    const s = (b.sectors || [])[0]?.name || (b.sectors || [])[0] || ''
    return s === sector
  })
  if (sectorBiz.length > 1 && price > 0) {
    const sectorAvg = avg(sectorBiz.map(b => b.investment || b.financials?.price || 0))
    const ratio = price / sectorAvg
    score += Math.min(25, Math.max(0, Math.round(25 * (1.5 - ratio))))
  } else {
    score += 12 // neutral
  }

  // 2. EUR/m2 vs zone avg (20 pts)
  if (size > 0 && price > 0) {
    const allSqm = allBusinesses
      .filter(b => (b.size || b.features?.size || 0) > 0 && (b.investment || b.financials?.price || 0) > 0)
      .map(b => (b.investment || b.financials?.price || 0) / (b.size || b.features?.size || 0))
    const avgSqm = avg(allSqm)
    const mySqm = price / size
    const ratio = mySqm / avgSqm
    score += Math.min(20, Math.max(0, Math.round(20 * (1.4 - ratio))))
  } else {
    score += 10
  }

  // 3. Freshness — days on market (15 pts) — fewer days = better
  if (days > 0) {
    score += Math.min(15, Math.max(0, Math.round(15 * (1 - days / 180))))
  } else {
    score += 15 // brand new
  }

  // 4. Features value (15 pts)
  if (smokeOutlet) score += 8
  if (terrace) score += 7

  // 5. Location premium index (10 pts) — proxy via views
  if (views > 0) {
    const maxViews = Math.max(...allBusinesses.map(b => b.times_viewed || b.metadata?.views || 0))
    score += Math.round(10 * (views / maxViews))
  }

  // 6. Demand ratio — views/favorites (15 pts)
  if (views > 0 && favs > 0) {
    const demandRatio = favs / views
    score += Math.min(15, Math.round(demandRatio * 150))
  }

  return Math.min(100, Math.max(0, score))
}

/**
 * Compute market summary KPIs
 */
export function computeMarketSummary(businesses) {
  const prices = businesses.map(b => b.investment || b.financials?.price || 0).filter(v => v > 0)
  const rents = businesses.map(b => b.rental || b.financials?.rent || 0).filter(v => v > 0)
  const sizes = businesses.map(b => b.size || b.features?.size || 0).filter(v => v > 0)
  const days = businesses.map(b => b.days_on_market || b.metadata?.daysOnMarket || 0).filter(v => v > 0)

  return {
    totalBusinesses: businesses.length,
    avgPrice: Math.round(avg(prices)),
    medianPrice: percentile(prices.sort((a, b) => a - b), 50),
    avgRent: Math.round(avg(rents)),
    avgSize: Math.round(avg(sizes)),
    avgDaysOnMarket: Math.round(avg(days)),
    avgPricePerSqm: sizes.length > 0 ? Math.round(avg(prices) / avg(sizes)) : 0,
    typeDistribution: countBy(businesses, b => b.business_type?.name || b.type || 'Traspaso'),
  }
}

// ── Utility functions ────────────────────────────────────
function avg(arr) {
  if (arr.length === 0) return 0
  return arr.reduce((sum, v) => sum + v, 0) / arr.length
}

function percentile(sortedArr, p) {
  if (sortedArr.length === 0) return 0
  const idx = Math.ceil((p / 100) * sortedArr.length) - 1
  return sortedArr[Math.max(0, idx)]
}

function countBy(arr, fn) {
  const counts = {}
  arr.forEach(item => {
    const key = fn(item)
    counts[key] = (counts[key] || 0) + 1
  })
  return counts
}
