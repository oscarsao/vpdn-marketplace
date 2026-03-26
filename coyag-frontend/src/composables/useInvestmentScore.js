// ═══════════════════════════════════════════════════════════
// COYAG VPDN — Investment Score Composable
// Computes a 0-100 investment score for each business
// ═══════════════════════════════════════════════════════════

import { computed } from 'vue'
import { computeInvestmentScore } from '../data/statisticsEngine'

export function useInvestmentScore(business, allBusinesses) {
  const score = computed(() => {
    if (!business.value || !allBusinesses.value?.length) return 0
    return computeInvestmentScore(business.value, allBusinesses.value)
  })

  const scoreLabel = computed(() => {
    if (score.value >= 80) return { text: 'Excelente', color: '#28C76F' }
    if (score.value >= 60) return { text: 'Bueno', color: '#7367F0' }
    if (score.value >= 40) return { text: 'Regular', color: '#FF9F43' }
    return { text: 'Bajo', color: '#EA5455' }
  })

  const scoreBreakdown = computed(() => {
    if (!business.value) return []
    const b = business.value
    const price = b.investment || b.financials?.price || 0
    const size = b.size || b.features?.size || 0
    const smoke = b.smoke_outlet || b.features?.smokeOutlet || false
    const terrace = b.terrace || b.features?.terrace || false
    const days = b.days_on_market || b.metadata?.daysOnMarket || 0
    const views = b.times_viewed || b.metadata?.views || 0

    return [
      { label: 'Precio vs sector', value: price > 0 ? 'Competitivo' : 'Sin datos', weight: 25 },
      { label: 'EUR/m2 vs zona', value: size > 0 ? Math.round(price / size) + '/m2' : 'N/A', weight: 20 },
      { label: 'Frescura', value: days > 0 ? days + ' dias' : 'Nuevo', weight: 15 },
      { label: 'Equipamiento', value: [smoke && 'Humos', terrace && 'Terraza'].filter(Boolean).join(', ') || 'Basico', weight: 15 },
      { label: 'Ubicacion', value: views > 50 ? 'Alta demanda' : 'Normal', weight: 10 },
      { label: 'Demanda', value: views > 0 ? views + ' visitas' : 'Sin datos', weight: 15 },
    ]
  })

  return { score, scoreLabel, scoreBreakdown }
}
