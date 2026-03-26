<script setup>
import { ref } from 'vue'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const copied = ref(false)

const shareUrl = () => {
  const base = window.location.origin
  return `${base}/negocio/${props.business.id_code || props.business.id}`
}

const shareText = () => {
  const price = props.business.investment
    ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(props.business.investment)
    : ''
  return `${props.business.name} ${price ? '- ' + price : ''} | COYAG`
}

function shareWhatsApp() {
  const text = encodeURIComponent(`${shareText()}\n${shareUrl()}`)
  window.open(`https://wa.me/?text=${text}`, '_blank')
}

function shareEmail() {
  const subject = encodeURIComponent(shareText())
  const body = encodeURIComponent(`Mira este negocio en COYAG:\n\n${props.business.name}\nPrecio: ${props.business.investment ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(props.business.investment) : 'Consultar'}\n\n${shareUrl()}`)
  window.open(`mailto:?subject=${subject}&body=${body}`)
}

function shareTelegram() {
  const text = encodeURIComponent(shareText())
  const url = encodeURIComponent(shareUrl())
  window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank')
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl())
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    // Fallback
    const input = document.createElement('input')
    input.value = shareUrl()
    document.body.appendChild(input)
    input.select()
    document.execCommand('copy')
    document.body.removeChild(input)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  }
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="show" class="fixed inset-0 z-[1100] flex items-center justify-center p-4" @click.self="emit('close')">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-in">
          <!-- Close -->
          <button @click="emit('close')" class="absolute top-4 right-4 p-1 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer">
            <AppIcon name="x-mark" :size="18" />
          </button>

          <h3 class="text-lg font-bold text-gray-900 mb-1">Compartir negocio</h3>
          <p class="text-xs text-gray-500 mb-5">{{ business.name }}</p>

          <!-- Share buttons -->
          <div class="grid grid-cols-2 gap-3 mb-5">
            <button @click="shareWhatsApp"
              class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 bg-white hover:bg-green-50 hover:border-green-200 transition-colors cursor-pointer">
              <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
                <AppIcon name="chat" :size="18" />
              </div>
              <span class="text-sm font-semibold text-gray-700">WhatsApp</span>
            </button>

            <button @click="shareTelegram"
              class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 bg-white hover:bg-blue-50 hover:border-blue-200 transition-colors cursor-pointer">
              <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white shrink-0">
                <AppIcon name="arrow-right" :size="18" />
              </div>
              <span class="text-sm font-semibold text-gray-700">Telegram</span>
            </button>

            <button @click="shareEmail"
              class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 bg-white hover:bg-amber-50 hover:border-amber-200 transition-colors cursor-pointer">
              <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center text-white shrink-0">
                <AppIcon name="envelope" :size="18" />
              </div>
              <span class="text-sm font-semibold text-gray-700">Email</span>
            </button>

            <button @click="copyLink"
              class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 bg-white transition-colors cursor-pointer"
              :class="copied ? 'bg-green-50 border-green-300' : 'hover:bg-gray-50'">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-white shrink-0" :class="copied ? 'bg-green-500' : 'bg-gray-600'">
                <AppIcon :name="copied ? 'check' : 'external-link'" :size="18" />
              </div>
              <span class="text-sm font-semibold" :class="copied ? 'text-green-700' : 'text-gray-700'">
                {{ copied ? 'Copiado!' : 'Copiar link' }}
              </span>
            </button>
          </div>

          <!-- URL display -->
          <div class="bg-gray-50 rounded-lg px-3 py-2 flex items-center gap-2">
            <AppIcon name="external-link" :size="14" class="text-gray-400 shrink-0" />
            <span class="text-xs text-gray-500 truncate flex-1">{{ shareUrl() }}</span>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
