import { defineStore } from 'pinia'

export const useSearchStore = defineStore('searches', {
  state: () => ({
    savedSearches: JSON.parse(localStorage.getItem('coyag_saved_searches') || '[]'),
    pendingFilters: null,
  }),

  getters: {
    searchCount: (state) => state.savedSearches.length,
    hasSearches: (state) => state.savedSearches.length > 0,
  },

  actions: {
    addSearch({ name, filters }) {
      const search = {
        id: Date.now(),
        createdAt: new Date().toISOString(),
        name,
        filters: { ...filters },
      }
      this.savedSearches.unshift(search)
      this._persist()
      return search
    },

    updateSearch(id, { name, filters }) {
      const idx = this.savedSearches.findIndex(s => s.id === id)
      if (idx === -1) return
      this.savedSearches[idx] = {
        ...this.savedSearches[idx],
        name,
        filters: { ...filters },
      }
      this._persist()
    },

    removeSearch(id) {
      this.savedSearches = this.savedSearches.filter(s => s.id !== id)
      this._persist()
    },

    applySearch(id) {
      const search = this.savedSearches.find(s => s.id === id)
      if (search) {
        this.pendingFilters = { ...search.filters }
      }
    },

    clearPending() {
      this.pendingFilters = null
    },

    _persist() {
      localStorage.setItem('coyag_saved_searches', JSON.stringify(this.savedSearches))
    },
  },
})
