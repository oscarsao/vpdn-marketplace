<script setup>
import { ref, nextTick, watch } from 'vue'
import { useAiStore } from '../../stores/ai'
import AppIcon from '../ui/AppIcon.vue'

defineProps({
  isMobile: { type: Boolean, default: false }
})

const ai = useAiStore()
const message = ref('')
const chatContainer = ref(null)

watch(() => ai.chatMessages.length, async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
})

function send() {
  if (!message.value.trim() || ai.loading) return
  ai.sendMessage(message.value.trim())
  message.value = ''
}
</script>

<template>
  <!-- FAB Button -->
  <button
    v-if="!ai.chatOpen"
    class="c-ai-fab"
    :class="isMobile ? 'bottom-[80px]' : 'bottom-6'"
    @click="ai.toggleChat()"
    aria-label="Abrir asistente IA"
  >
    <AppIcon name="sparkles" :size="24" />
  </button>

  <!-- Chat Panel -->
  <Teleport to="body">
    <transition name="slide-up">
      <div
        v-if="ai.chatOpen"
        class="fixed z-[1001] flex flex-col bg-white overflow-hidden"
        :class="isMobile
          ? 'inset-x-0 bottom-[80px] top-0 rounded-none'
          : 'inset-0 lg:inset-auto lg:bottom-6 lg:right-6 lg:w-[400px] lg:h-[560px] lg:rounded-2xl'"
        style="box-shadow: 0 12px 48px rgba(0,0,0,0.15);"
      >
        <!-- Header -->
        <div
          class="flex items-center justify-between px-5 py-4 text-white"
          style="background: linear-gradient(135deg, #7367F0, #CE9FFC);"
        >
          <div class="flex items-center gap-3">
            <AppIcon name="sparkles" :size="20" />
            <div>
              <h3 class="text-sm font-bold text-white m-0">Asistente COYAG</h3>
              <span class="text-xs opacity-80">Powered by AI</span>
            </div>
          </div>
          <button
            @click="ai.toggleChat()"
            class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center cursor-pointer border-none hover:bg-white/30 transition"
          >
            <AppIcon name="x-mark" :size="18" />
          </button>
        </div>

        <!-- Messages -->
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3">
          <!-- Welcome -->
          <div v-if="!ai.chatMessages.length" class="text-center py-8">
            <span class="block mb-3"><AppIcon name="robot" :size="40" /></span>
            <p class="text-sm" style="color: var(--color-text-muted);">
              Soy tu asistente de COYAG.<br />
              Preguntame sobre negocios, inversiones o inmuebles.
            </p>
          </div>

          <!-- Chat bubbles -->
          <div
            v-for="(msg, i) in ai.chatMessages"
            :key="i"
            class="flex"
            :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-[80%] px-4 py-2.5 rounded-2xl text-sm leading-relaxed"
              :class="msg.role === 'user'
                ? 'bg-[var(--color-primary)] text-white rounded-br-sm'
                : 'bg-gray-100 text-[var(--color-text-body)] rounded-bl-sm'"
            >
              {{ msg.content }}
            </div>
          </div>

          <!-- Loading dots -->
          <div v-if="ai.loading" class="flex justify-start">
            <div class="bg-gray-100 px-4 py-3 rounded-2xl rounded-bl-sm flex gap-1">
              <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
              <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
              <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
            </div>
          </div>
        </div>

        <!-- Input -->
        <div class="p-3 border-t border-gray-100">
          <form @submit.prevent="send" class="flex gap-2">
            <input
              v-model="message"
              type="text"
              placeholder="Escribe tu pregunta..."
              class="c-input flex-1"
              :disabled="ai.loading"
            />
            <button
              type="submit"
              class="c-btn c-btn--primary px-4"
              :disabled="!message.trim() || ai.loading"
            >
              Enviar
            </button>
          </form>
        </div>
      </div>
    </transition>
  </Teleport>
</template>
