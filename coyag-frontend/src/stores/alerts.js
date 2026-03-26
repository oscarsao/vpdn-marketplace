import { defineStore } from 'pinia'

export const useAlertStore = defineStore('alerts', {
  state: () => ({
    savedAlerts: JSON.parse(localStorage.getItem('coyag_alerts') || '[]'),
  }),

  getters: {
    count: (state) => state.savedAlerts.length,
  },

  actions: {
    addAlert(alert) {
      const newAlert = {
        id: Date.now(),
        createdAt: new Date().toISOString(),
        ...alert,
      }
      this.savedAlerts.unshift(newAlert)
      this._persist()
      return newAlert
    },

    removeAlert(id) {
      this.savedAlerts = this.savedAlerts.filter(a => a.id !== id)
      this._persist()
    },

    _persist() {
      localStorage.setItem('coyag_alerts', JSON.stringify(this.savedAlerts))
    },
  },
})
