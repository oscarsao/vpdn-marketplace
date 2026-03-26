<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useAiStore } from '../../stores/ai'
import AppIcon from '../../components/ui/AppIcon.vue'

const ai = useAiStore()
const input = ref('')
const chatContainer = ref(null)

const suggestedTopics = [
  'Buscar restaurante en Madrid menos de 100k',
  'Comparar traspaso vs franquicia',
  'Que sectores son mas rentables?',
  'Cual es el precio medio del mercado?',
]

onMounted(() => {
  ai.clearBusinessContext()
  if (ai.chatMessages.length === 0) {
    ai.chatMessages.push({
      role: 'assistant',
      content: 'Hola! Soy el asistente IA de COYAG. Puedo ayudarte a buscar negocios, analizar el mercado, comparar opciones o resolver cualquier duda sobre traspasos, franquicias e inmuebles. Que necesitas?',
      timestamp: new Date().toISOString(),
    })
  }
})

watch(() => ai.chatMessages.length, async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
})

function send() {
  if (!input.value.trim() || ai.loading) return
  ai.sendMessage(input.value.trim())
  input.value = ''
}

function useSuggestion(topic) {
  input.value = topic
  send()
}

function formatMarkdown(text) {
  if (!text) return ''
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\n/g, '<br>')
    .replace(/- (.*?)(?=<br>|$)/g, '<span class="block pl-3 text-gray-600">- $1</span>')
}
</script>

<template>
  <div class="h-[calc(100vh-100px)] flex flex-col animate-fade-in max-w-4xl mx-auto">
    <header class="mb-4">
      <h1 class="text-lg md:text-xl font-bold flex items-center gap-2 text-gray-900">
        <AppIcon name="sparkles" :size="22" class="text-indigo-500" /> Asistente IA
      </h1>
      <p class="text-sm text-gray-400">Conversa con la IA para encontrar y analizar negocios</p>
    </header>

    <div class="flex-1 c-card flex flex-col overflow-hidden">
      <!-- Messages -->
      <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 bg-gray-50/50">
        <div
          v-for="(msg, i) in ai.chatMessages"
          :key="i"
          :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']"
        >
          <!-- Bot avatar -->
          <div v-if="msg.role === 'assistant'" class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mr-2 bg-gradient-to-br from-indigo-500 to-purple-500 text-white">
            <AppIcon name="sparkles" :size="16" />
          </div>

          <div
            :class="[
              'p-3 rounded-xl text-sm leading-relaxed break-words max-w-[85%] md:max-w-[75%] whitespace-pre-line',
              msg.role === 'user'
                ? 'bg-gray-900 text-white'
                : 'bg-white border border-gray-200 text-gray-700 shadow-sm'
            ]"
            v-html="msg.role === 'assistant' ? formatMarkdown(msg.content) : msg.content"
          />
        </div>

        <!-- Suggested topics when empty -->
        <div v-if="ai.chatMessages.length <= 1 && !ai.loading" class="mt-6">
          <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Sugerencias</p>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="topic in suggestedTopics"
              :key="topic"
              @click="useSuggestion(topic)"
              class="px-3 py-2 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors border-none cursor-pointer"
            >
              {{ topic }}
            </button>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="ai.loading" class="flex items-start gap-2">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-gradient-to-br from-indigo-500 to-purple-500 text-white">
            <AppIcon name="sparkles" :size="16" />
          </div>
          <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
            <div class="flex gap-1">
              <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
              <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
              <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="p-3 md:p-4 bg-white border-t border-gray-100 flex gap-2">
        <input
          v-model="input"
          @keyup.enter="send"
          type="text"
          placeholder="Escribe tu consulta..."
          class="c-input flex-1 bg-gray-50 border-transparent focus:border-indigo-400 focus:bg-white"
          :disabled="ai.loading"
        />
        <button @click="send" class="c-btn c-btn--ai px-5 rounded-lg" :disabled="ai.loading || !input.trim()">
          <AppIcon name="arrow-right" :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
