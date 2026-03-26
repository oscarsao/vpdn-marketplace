<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AppIcon from '../../components/ui/AppIcon.vue'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const showPass = ref(false)
const loading = ref(false)
const error = ref('')

async function login() {
  if (!email.value || !password.value) return
  loading.value = true
  error.value = ''

  try {
    const success = await auth.login(email.value, password.value)
    if (success) {
      if (auth.isAdmin) {
        router.push('/dashboard')
      } else {
        router.push('/')
      }
    } else {
      error.value = 'Credenciales incorrectas. Verifica tu email y contraseña.'
    }
  } catch (err) {
    error.value = err.response?.data?.error || 'Error al iniciar sesión.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[var(--color-bg-page)] px-4">
    <div class="c-card w-full max-w-md p-0 overflow-hidden">
      <!-- Header -->
      <div class="text-center px-8 pt-10 pb-6">
        <h1 class="text-3xl font-bold mb-1" style="color: var(--color-primary);">COYAG</h1>
        <p class="text-xs font-medium tracking-widest mb-6" style="color: var(--color-text-muted);">VIDEOPORTAL DE NEGOCIOS</p>
        <h2 class="text-lg font-semibold mb-1">Bienvenido</h2>
        <p class="text-sm" style="color: var(--color-text-muted);">
          Inicia sesión para acceder a la plataforma
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="login" class="px-8 pb-8 space-y-4">
        <!-- Error -->
        <div
          v-if="error"
          class="bg-red-50 text-[var(--color-danger)] px-4 py-3 rounded-lg text-sm"
        >
          {{ error }}
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">
            Email
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="tu@email.com"
            class="c-input"
            required
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">
            Contraseña
          </label>
          <div class="relative">
            <input
              v-model="password"
              :type="showPass ? 'text' : 'password'"
              placeholder="••••••••"
              class="c-input pr-10"
              required
              minlength="8"
            />
            <button
              type="button"
              @click="showPass = !showPass"
              class="absolute right-3 top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer text-sm"
              style="color: var(--color-text-muted);"
            >
              <AppIcon :name="showPass ? 'eye-off' : 'eye'" :size="16" />
            </button>
          </div>
        </div>

        <!-- Links -->
        <div class="flex justify-between text-sm">
          <router-link to="/register" class="text-[var(--color-primary)] font-medium no-underline hover:underline">
            ¿Aún no tienes cuenta?
          </router-link>
          <router-link to="/forgot-password" class="font-medium no-underline hover:underline" style="color: var(--color-text-muted);">
            ¿Olvidaste tu contraseña?
          </router-link>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-2">
          <router-link to="/register" class="c-btn c-btn--outline flex-1 text-center no-underline">
            Regístrate
          </router-link>
          <button
            type="submit"
            class="c-btn c-btn--primary flex-1"
            :disabled="loading"
          >
            {{ loading ? 'Ingresando...' : 'Ingresar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
