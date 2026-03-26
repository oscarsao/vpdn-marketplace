import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('userInfo') || 'null'),
    token: JSON.parse(localStorage.getItem('token') || 'null'),
    roles: JSON.parse(localStorage.getItem('userRoles') || '[]'),
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.roles.some(r => r.id_role < 40),
    isClient: (state) => state.roles.some(r => r.id_role >= 40),
    fullName: (state) => {
      if (!state.user) return ''
      return `${state.user.names || ''} ${state.user.surnames || ''}`.trim()
    },
  },

  actions: {
    async login(email, password) {
      const { data } = await axios.post('/auth/login', {
        email,
        password,
        frontend: 'videoportal'
      })

      if (data.Authorization) {
        this.token = data.Authorization
        localStorage.setItem('token', JSON.stringify(data.Authorization))
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.Authorization}`

        await this.fetchUser()
        return true
      }
      return false
    },

    async register(userData) {
      // Basic registration logic
      userData.frontend = 'videoportal'
      const { data } = await axios.post('/auth/register', userData)
      
      if (data.Authorization || data.token) {
        const token = data.Authorization || data.token
        this.token = token
        localStorage.setItem('token', JSON.stringify(token))
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        await this.fetchUser()
        return true
      }
      return false
    },

    async fetchUser() {
      try {
        const { data } = await axios.get('/auth/user')
        this.user = data
        localStorage.setItem('userInfo', JSON.stringify(data))

        // Fetch roles
        if (data.roles) {
          this.roles = data.roles
          localStorage.setItem('userRoles', JSON.stringify(data.roles))
        }
      } catch (error) {
        console.error('Error fetching user:', error)
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.roles = []
      localStorage.removeItem('userInfo')
      localStorage.removeItem('token')
      localStorage.removeItem('userRoles')
      delete axios.defaults.headers.common['Authorization']
    },

    restoreSession() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    }
  }
})
