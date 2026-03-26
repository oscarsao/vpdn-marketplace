import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useLocationStore = defineStore('location', {
  state: () => ({
    provinces: [],
    municipalities: [],
    districts: [],
    neighborhoods: [],
    loading: false,
  }),

  actions: {
    async fetchProvinces() {
      try {
        const { data } = await axios.get('/province')
        this.provinces = data
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
