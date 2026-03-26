<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AppIcon from '../../components/ui/AppIcon.vue'

const auth = useAuthStore()
const router = useRouter()

const names = ref('')
const surnames = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const passwordConfirm = ref('')

const showPass = ref(false)
const showPassConfirm = ref(false)
const loading = ref(false)
const error = ref('')

async function register() {
  if (password.value !== passwordConfirm.value) {
    error.value = 'Las contraseñas no coinciden.'
    return
  }
  
  loading.value = true
  error.value = ''

  try {
    const success = await auth.register({
      names: names.value,
      surnames: surnames.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
      password_confirmation: passwordConfirm.value
    })
    
    if (success) {
      router.push('/')
    } else {
      error.value = 'Error al registrar. Por favor, intenta de nuevo.'
    }
  } catch (err) {
    error.value = err.response?.data?.error || err.response?.data?.message || 'Error al completar el registro.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[var(--color-bg-page)] py-12 px-4">
    <div class="c-card w-full max-w-lg p-0 overflow-hidden">
      <!-- Header -->
      <div class="text-center px-8 pt-10 pb-6">
        <h1 class="text-3xl font-bold mb-1" style="color: var(--color-primary);">COYAG</h1>
        <p class="text-xs font-medium tracking-widest mb-6" style="color: var(--color-text-muted);">VIDEOPORTAL DE NEGOCIOS</p>
        <h2 class="text-lg font-semibold mb-1">Crear Cuenta</h2>
        <p class="text-sm" style="color: var(--color-text-muted);">
          Comleta tus datos para registrarte
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="register" class="px-8 pb-8 space-y-4">
        <!-- Error -->
        <div v-if="error" class="bg-red-50 text-[var(--color-danger)] px-4 py-3 rounded-lg text-sm">
          {{ error }}
        </div>

        <div class="grid grid-cols-2 gap-4">
          <!-- Names -->
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Nombres</label>
            <input v-model="names" type="text" placeholder="Tus nombres" class="c-input" required />
          </div>
          
          <!-- Surnames -->
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Apellidos</label>
            <input v-model="surnames" type="text" placeholder="Tus apellidos" class="c-input" required />
          </div>
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Teléfono</label>
          <input v-model="phone" type="tel" placeholder="+34 600..." class="c-input" required />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Email</label>
          <input v-model="email" type="email" placeholder="tu@email.com" class="c-input" required />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Contraseña</label>
          <div class="relative">
            <input 
              v-model="password" 
              :type="showPass ? 'text' : 'password'" 
              placeholder="••••••••" 
              class="c-input pr-10" 
              required minlength="8" 
            />
            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer text-sm" style="color: var(--color-text-muted);">
              <AppIcon :name="showPass ? 'eye-off' : 'eye'" :size="16" />
            </button>
          </div>
        </div>
        
        <!-- Password Confirm -->
        <div>
          <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Confirmar Contraseña</label>
          <div class="relative">
            <input 
              v-model="passwordConfirm" 
              :type="showPassConfirm ? 'text' : 'password'" 
              placeholder="••••••••" 
              class="c-input pr-10" 
              required minlength="8" 
            />
            <button type="button" @click="showPassConfirm = !showPassConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer text-sm" style="color: var(--color-text-muted);">
              <AppIcon :name="showPassConfirm ? 'eye-off' : 'eye'" :size="16" />
            </button>
          </div>
        </div>

        <!-- Links -->
        <div class="flex justify-center text-sm pt-2">
          <router-link to="/login" class="text-[var(--color-primary)] font-medium no-underline hover:underline">
            ¿Ya tienes cuenta? Inicia sesión
          </router-link>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-2">
          <button type="submit" class="c-btn c-btn--primary w-full" :disabled="loading">
            {{ loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
