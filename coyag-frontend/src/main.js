import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './main.css'

// Mock API for local development (activate via VITE_USE_MOCK=true or when no backend)
import axiosInstance from './api/axios'
import { installMockApi } from './api/mockApi'

const useMock = import.meta.env.VITE_USE_MOCK !== 'false'
if (useMock) {
  installMockApi(axiosInstance)
}

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
