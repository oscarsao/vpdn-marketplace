<script setup>
import { ref } from 'vue'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const activeTab = ref('info')
const form = ref({
  name: '',
  email: '',
  phone: '',
  message: '',
  date: '',
  time: '',
})
const submitted = ref(false)
const submitting = ref(false)

async function handleSubmit() {
  if (!form.value.name || !form.value.phone) return
  submitting.value = true
  // Simulate API call
  await new Promise(r => setTimeout(r, 800))
  submitting.value = false
  submitted.value = true
}

function reset() {
  submitted.value = false
  form.value = { name: '', email: '', phone: '', message: '', date: '', time: '' }
  activeTab.value = 'info'
}

function close() {
  reset()
  emit('close')
}

const advisor = () => props.business.employee || { name: 'Agencia VPDN', phone: '+34 600 000 000' }
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[1100] flex items-center justify-center p-4" @click.self="close">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">

          <!-- Header -->
          <div class="bg-gradient-to-r from-[var(--color-primary)] to-[#CC2B1F] p-5 text-white">
            <button @click="close" class="absolute top-4 right-4 p-1 text-white/80 hover:text-white bg-transparent border-none cursor-pointer">
              <AppIcon name="x-mark" :size="18" />
            </button>
            <h3 class="text-lg font-bold">Contactar Asesor</h3>
            <p class="text-sm text-white/80 mt-1">{{ business.name }}</p>
          </div>

          <!-- Success state -->
          <div v-if="submitted" class="p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
              <AppIcon name="check" :size="32" class="text-green-600" />
            </div>
            <h4 class="text-lg font-bold text-gray-900 mb-2">Solicitud enviada</h4>
            <p class="text-sm text-gray-500 mb-6">{{ advisor().name }} se pondra en contacto contigo pronto.</p>
            <button @click="close" class="c-btn c-btn--primary px-6">Cerrar</button>
          </div>

          <!-- Form -->
          <div v-else>
            <!-- Tabs -->
            <div class="flex border-b border-gray-100">
              <button @click="activeTab = 'info'"
                :class="['flex-1 py-3 text-xs font-semibold border-b-2 bg-transparent cursor-pointer transition-colors',
                  activeTab === 'info' ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-gray-400 border-transparent']">
                <AppIcon name="chat" :size="14" class="inline mr-1" /> Solicitar Info
              </button>
              <button @click="activeTab = 'visit'"
                :class="['flex-1 py-3 text-xs font-semibold border-b-2 bg-transparent cursor-pointer transition-colors',
                  activeTab === 'visit' ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-gray-400 border-transparent']">
                <AppIcon name="calendar" :size="14" class="inline mr-1" /> Agendar Visita
              </button>
              <button @click="activeTab = 'call'"
                :class="['flex-1 py-3 text-xs font-semibold border-b-2 bg-transparent cursor-pointer transition-colors',
                  activeTab === 'call' ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-gray-400 border-transparent']">
                <AppIcon name="phone" :size="14" class="inline mr-1" /> Llamar
              </button>
            </div>

            <!-- Call tab -->
            <div v-if="activeTab === 'call'" class="p-6 text-center">
              <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4 text-2xl font-bold text-indigo-700">
                {{ advisor().name.charAt(0) }}
              </div>
              <h4 class="text-base font-bold text-gray-900">{{ advisor().name }}</h4>
              <p class="text-sm text-gray-500 mb-5">Asesor asignado</p>
              <a :href="`tel:${advisor().phone || '+34600000000'}`" class="c-btn c-btn--primary w-full py-3 text-base no-underline">
                <AppIcon name="phone" :size="18" /> Llamar ahora
              </a>
              <p class="text-xs text-gray-400 mt-3">{{ advisor().phone || '+34 600 000 000' }}</p>
            </div>

            <!-- Info / Visit form -->
            <form v-else @submit.prevent="handleSubmit" class="p-5 space-y-4">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre *</label>
                <input v-model="form.name" type="text" required class="c-input text-sm" placeholder="Tu nombre completo" />
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Telefono *</label>
                  <input v-model="form.phone" type="tel" required class="c-input text-sm" placeholder="+34 600..." />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                  <input v-model="form.email" type="email" class="c-input text-sm" placeholder="tu@email.com" />
                </div>
              </div>

              <!-- Date/time for visit -->
              <div v-if="activeTab === 'visit'" class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fecha preferida</label>
                  <input v-model="form.date" type="date" class="c-input text-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hora</label>
                  <select v-model="form.time" class="c-input text-sm">
                    <option value="">Cualquiera</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="13:00">13:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="18:00">18:00</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mensaje</label>
                <textarea v-model="form.message" rows="3" class="c-input text-sm resize-none"
                  :placeholder="activeTab === 'visit' ? 'Notas adicionales para la visita...' : 'Escribe tu consulta sobre este negocio...'"></textarea>
              </div>

              <button type="submit" class="c-btn c-btn--primary w-full py-3" :disabled="submitting || !form.name || !form.phone">
                <template v-if="submitting">Enviando...</template>
                <template v-else>
                  {{ activeTab === 'visit' ? 'Solicitar Visita' : 'Enviar Consulta' }}
                </template>
              </button>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
