<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()

const profileForm = ref({
  names: auth.user?.names || '',
  surnames: auth.user?.surnames || '',
  email: auth.user?.email || '',
  phone: auth.user?.phone || ''
})

const loading = ref(false)
const saved = ref(false)

async function saveProfile() {
  loading.value = true
  saved.value = false
  // Basic mock save
  setTimeout(() => {
    loading.value = false
    saved.value = true
    
    // reset notification after 3s
    setTimeout(() => {
      saved.value = false
    }, 3000)
  }, 800)
}
</script>

<template>
  <div class="py-6 max-w-2xl mx-auto animate-fade-in">
    <header class="mb-8 flex items-center gap-4">
      <div class="w-16 h-16 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center text-2xl font-bold uppercase shrink-0">
        {{ profileForm.names.charAt(0) || 'U' }}
      </div>
      <div>
        <h1 class="text-3xl font-bold" style="color: var(--color-text-heading);">Mi Perfil</h1>
        <p style="color: var(--color-text-muted);">Gestiona tu información personal y configuración</p>
      </div>
    </header>

    <div class="c-card p-8">
      <form @submit.prevent="saveProfile" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nombre -->
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Nombre</label>
            <input v-model="profileForm.names" type="text" class="c-input" required />
          </div>

          <!-- Apellidos -->
          <div>
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Apellidos</label>
            <input v-model="profileForm.surnames" type="text" class="c-input" required />
          </div>

          <!-- Email -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Email</label>
            <input v-model="profileForm.email" type="email" class="c-input bg-gray-50 cursor-not-allowed" disabled />
            <p class="text-xs mt-1" style="color: var(--color-text-muted);">No puedes cambiar el correo electrónico asociado.</p>
          </div>

          <!-- Teléfono -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1.5" style="color: var(--color-text-heading);">Teléfono de Contacto</label>
            <input v-model="profileForm.phone" type="tel" class="c-input" placeholder="+34 600 000 000" />
          </div>
        </div>

        <div class="pt-6 border-t" style="border-color: var(--color-border);">
          <div class="flex items-center gap-4 justify-end">
            <span v-if="saved" class="text-sm font-medium text-[var(--color-success)]">
              ¡Cambios guardados!
            </span>
            <button type="submit" class="c-btn c-btn--primary" :disabled="loading">
              <span v-if="loading">Guardando...</span>
              <span v-else>Guardar Cambios</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
