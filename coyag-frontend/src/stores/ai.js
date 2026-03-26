import { defineStore } from 'pinia'
import axios from '../api/axios'

export const useAiStore = defineStore('ai', {
  state: () => ({
    chatMessages: [],
    chatOpen: false,
    loading: false,
    providerInfo: null,
    currentBusinessContext: null, // business ID for scoped chat
    businessAnalysis: null,
    marketContext: null,
    similarBusinesses: [],
  }),

  actions: {
    toggleChat() {
      this.chatOpen = !this.chatOpen
    },

    setBusinessContext(businessId) {
      this.currentBusinessContext = businessId
    },

    clearBusinessContext() {
      this.currentBusinessContext = null
      this.businessAnalysis = null
      this.marketContext = null
      this.similarBusinesses = []
    },

    async sendMessage(message) {
      this.chatMessages.push({
        role: 'user',
        content: message,
        timestamp: new Date().toISOString()
      })

      this.loading = true
      try {
        const payload = { message }
        if (this.currentBusinessContext) {
          payload.business_id = this.currentBusinessContext
        }
        const { data } = await axios.post('/ai/chat', payload)
        this.chatMessages.push({
          role: 'assistant',
          content: data.data?.content || data.content || 'No pude procesar tu mensaje.',
          timestamp: new Date().toISOString()
        })
      } catch (error) {
        this.chatMessages.push({
          role: 'assistant',
          content: 'Lo siento, hubo un error al procesar tu mensaje.',
          timestamp: new Date().toISOString()
        })
      } finally {
        this.loading = false
      }
    },

    async analyzeBusiness(businessId) {
      this.loading = true
      try {
        const { data } = await axios.post('/ai/business-analysis', {
          business_id: businessId
        })
        this.businessAnalysis = data.data || data
        return this.businessAnalysis
      } catch (error) {
        console.error('AI analysis error:', error)
        return null
      } finally {
        this.loading = false
      }
    },

    async fetchSimilarBusinesses(businessId, limit = 6) {
      try {
        const { data } = await axios.post('/ai/similar', {
          business_id: businessId,
          limit,
        })
        this.similarBusinesses = data.data || data
        return this.similarBusinesses
      } catch (error) {
        console.error('AI similar error:', error)
        return []
      }
    },

    async fetchMarketContext(businessId) {
      try {
        const { data } = await axios.post('/ai/market-context', {
          business_id: businessId
        })
        this.marketContext = data.data || data
        return this.marketContext
      } catch (error) {
        console.error('AI market context error:', error)
        return null
      }
    },

    async searchWithAI(query) {
      this.loading = true
      try {
        const { data } = await axios.post('/ai/search', { query })
        return data.data || data
      } catch (error) {
        console.error('AI search error:', error)
        return { results: [], total: 0 }
      } finally {
        this.loading = false
      }
    },

    async generateDescription(businessId) {
      this.loading = true
      try {
        const { data } = await axios.post('/ai/generate-description', {
          business_id: businessId
        })
        return data.data?.content || data.description || null
      } catch (error) {
        console.error('AI description error:', error)
        return null
      } finally {
        this.loading = false
      }
    },

    async fetchStatus() {
      try {
        const { data } = await axios.get('/ai/status')
        this.providerInfo = data.data || data
      } catch (error) {
        console.error('AI status error:', error)
      }
    },

    clearChat() {
      this.chatMessages = []
    }
  }
})
