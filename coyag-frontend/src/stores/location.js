import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useLocationStore = defineStore('location', {
  state: () => ({
    provinces: [],
    municipalities: [],
    districts: [],
    neighborhoods: [],
    sectors: [],
    loading: false,
  }),

  actions: {
    async fetchSectors() {
      if (this.sectors.length > 0) return // Cache
      try {
        const { data } = await axios.get('/sector')
        this.sectors = Array.isArray(data) ? data : (data?.data || [])
      } catch (e) {
        console.error('Error fetching sectors:', e)
      }
    },

    async fetchProvinces() {
      if (this.provinces.length > 0) return // Cache: don't re-fetch
      try {
        const { data } = await axios.get('/province')
        this.provinces = Array.isArray(data) ? data : (data?.data || [])
      } catch (e) {
        console.error('Error fetching provinces:', e)
      }
    },

    async fetchMunicipalities(provinceId) {
      try {
        const { data } = await axios.get(`/province/${provinceId}/municipalities`)
        this.municipalities = data
        // Reset dependent levels
        this.districts = []
        this.neighborhoods = []
      } catch (e) {
        console.error('Error fetching municipalities:', e)
      }
    },

    async fetchDistricts(municipalityId) {
      try {
        const { data } = await axios.get(`/municipality/${municipalityId}/districts`)
        this.districts = data
        this.neighborhoods = []
      } catch (e) {
        console.error('Error fetching districts:', e)
      }
    },

    async fetchNeighborhoods(districtId) {
      try {
        const { data } = await axios.get(`/neighborhood`, { params: { district_id: districtId } })
        this.neighborhoods = data
      } catch (e) {
        console.error('Error fetching neighborhoods:', e)
      }
    },
  }
})
