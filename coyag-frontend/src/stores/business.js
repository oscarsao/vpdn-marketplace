import { defineStore } from 'pinia'
import axios from '../api/axios'

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
        this.businesses = data.businesses || data.data || data
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
        this.currentBusiness = data
        return data
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
