import axios from 'axios'

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1/',
  validateStatus: (status) => {
    if (status === 403) {
      localStorage.removeItem('userInfo')
      localStorage.removeItem('userRoles')
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return status < 500
  },
})

// Request interceptor — attach token
instance.interceptors.request.use((config) => {
  const token = JSON.parse(localStorage.getItem('token') || 'null')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default instance
