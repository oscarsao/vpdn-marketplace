import { defineStore } from 'pinia'

export const useCompareStore = defineStore('compare', {
  state: () => ({
    items: [], // Array of business objects
    maxItems: 4
  }),
  
  getters: {
    count: (state) => state.items.length,
    isFull: (state) => state.items.length >= state.maxItems,
    hasItems: (state) => state.items.length > 0,
    canCompare: (state) => state.items.length > 1
  },
  
  actions: {
    add(business) {
      if (this.isFull) {
        // Podríamos lanzar una notificación aquí o un error
        console.warn('Alcanzado límite máximo de comparación')
        return false
      }
      
      const exists = this.items.find(item => item.id === business.id)
      if (exists) return true
      
      this.items.push(business)
      return true
    },
    
    remove(businessId) {
      this.items = this.items.filter(item => item.id !== businessId)
    },
    
    toggle(business) {
      const exists = this.items.find(item => item.id === business.id)
      if (exists) {
        this.remove(business.id)
      } else {
        this.add(business)
      }
    },
    
    clear() {
      this.items = []
    },
    
    isInCompare(businessId) {
      return this.items.some(item => item.id === businessId)
    }
  }
})
