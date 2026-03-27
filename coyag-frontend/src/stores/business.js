import { defineStore } from 'pinia'
import axios from '../api/axios'

// Normalize API response fields (name_business → name, etc.)
function normalizeBusiness(raw) {
  if (!raw || typeof raw !== 'object') return raw
  return {
    ...raw,
    id: raw.id_business || raw.id,
    id_code: raw.id_code_business || raw.id_code,
    name: raw.name_business || raw.name,
    description: raw.description_business || raw.description,
    investment: raw.investment_business || raw.investment,
    rental: raw.rental_business || raw.rental,
    size: raw.size_business || raw.size,
    age: raw.age_business || raw.age,
    times_viewed: raw.times_viewed_business || raw.times_viewed,
    lat: raw.lat_business || raw.lat,
    lng: raw.lng_business || raw.lng,
    flag_outstanding: raw.flag_outstanding,
    flag_sold: raw.sold || raw.flag_sold,
    source_platform: raw.source_platform,
    sector: raw.sector,
    data_of_interest: raw.data_of_interest_business || raw.data_of_interest,
    relevant_advantages: raw.relevant_advantages_business || raw.relevant_advantages,
    address: raw.address_business || raw.address,
    contact_name: raw.contact_name_business || raw.contact_name,
    contact_email: raw.contact_email_business || raw.contact_email,
    contact_mobile_phone: raw.contact_mobile_phone_business || raw.contact_mobile_phone,
    // Location
    municipality: raw.municipality || { id: raw.id_municipality, name: raw.name_municipality },
    district: raw.district || { id: raw.id_district, name: raw.name_district },
    neighborhood: raw.neighborhood || { id: raw.id_neighborhood, name: raw.name_neighborhood },
    province: raw.province || { id: raw.id_province, name: raw.name_province },
    autonomous_community: raw.autonomous_community || { id: raw.id_autonomous_community, name: raw.name_autonomous_community },
    business_type: raw.business_type || { id: raw.id_business_type, name: raw.name_business_type },
    // Images: parse semicolon-separated string into array of objects
    multimedia: raw.multimedia || (raw.business_images_string
      ? raw.business_images_string.split(';').filter(Boolean).map((url, i) => ({ url, id: i }))
      : []),
    // Videos
    videos: raw.videos || (raw.business_videos_string
      ? raw.business_videos_string.split(';').filter(Boolean).map((url, i) => ({ url, id: i }))
      : []),
  }
}

export const useBusinessStore = defineStore('business', {
  state: () => ({
    businesses: [],
    currentBusiness: null,
    totalResults: 0,
    loading: false,
    filters: {
      condition: 'general',
      name: '',
      province_id: null,
      municipality_id: null,
      district_id: null,
      neighborhood_id: null,
      min_investment: null,
      max_investment: null,
      min_rental: null,
      max_rental: null,
      sectors: null,
      flag_sold: '0',
      flag_outstanding: null,
      order_by: 'most_relevant',
      filterby: null,
      page: 1,
    },
    favorites: [],
  }),

  getters: {
    activeFiltersCount: (state) => {
      let count = 0
      const f = state.filters
      if (f.name) count++
      if (f.province_id) count++
      if (f.municipality_id) count++
      if (f.min_investment || f.max_investment) count++
      if (f.min_rental || f.max_rental) count++
      if (f.sectors) count++
      if (f.flag_outstanding) count++
      if (f.filterby) count++
      return count
    },
  },

  actions: {
    async fetchBusinesses() {
      this.loading = true
      try {
        const params = {}
        Object.entries(this.filters).forEach(([key, val]) => {
          if (val !== null && val !== '' && val !== undefined) {
            params[key] = val
          }
        })

        const { data } = await axios.get('/business/index', { params })
        const rawList = data.businesses || data.data || data
        this.businesses = Array.isArray(rawList) ? rawList.map(normalizeBusiness) : rawList
        this.totalResults = data.total || data.length || 0
      } catch (error) {
        console.error('Error fetching businesses:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchBusiness(idCode) {
      this.loading = true
      try {
        const { data } = await axios.get(`/business/show/${idCode}`)
        const raw = data.business || data
        this.currentBusiness = normalizeBusiness(raw)
        return this.currentBusiness
      } catch (error) {
        console.error('Error fetching business:', error)
        return null
      } finally {
        this.loading = false
      }
    },

    setFilter(key, value) {
      this.filters[key] = value
      this.filters.page = 1
    },

    resetFilters() {
      this.filters = {
        condition: 'general',
        name: '',
        province_id: null,
        municipality_id: null,
        district_id: null,
        neighborhood_id: null,
        min_investment: null,
        max_investment: null,
        min_rental: null,
        max_rental: null,
        sectors: null,
        flag_sold: '0',
        flag_outstanding: null,
        order_by: 'most_relevant',
        filterby: null,
        page: 1,
      }
    },

    async toggleFavorite(businessId) {
      try {
        await axios.post('/favorite', { business_id: businessId })
        const idx = this.favorites.indexOf(businessId)
        if (idx > -1) {
          this.favorites.splice(idx, 1)
        } else {
          this.favorites.push(businessId)
        }
      } catch (error) {
        console.error('Error toggling favorite:', error)
      }
    },
  }
})
