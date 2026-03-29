<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useAiStore } from '../../stores/ai'
import AppIcon from '../ui/AppIcon.vue'

const props = defineProps({
  business: { type: Object, required: true },
  allBusinesses: { type: Array, default: () => [] },
})

const ai = useAiStore()
const activeTab = ref('analysis')
const input = ref('')
const chatContainer = ref(null)

const suggestedQuestions = [
  'Es buen precio para el sector?',
  'Cuales son los riesgos?',
  'Como se compara con similares?',
  'Cual es la rentabilidad?',
]

onMounted(async () => {
  ai.setBusinessContext(props.business.id_code || props.business.id)
  if (!ai.businessAnalysis) {
    await ai.analyzeBusiness(props.business.id_code || props.business.id)
  }
})

watch(() => ai.chatMessages.length, async () => {
  await nextTick()
  if (chatContainer.value) chatContainer.value.scrollTop = chatContainer.value.scrollHeight
})

function send() {
  if (!input.value.trim() || ai.loading) return
  ai.sendMessage(input.value.trim())
  input.value = ''
}

function askSuggestion(q) {
  input.value = q
  send()
}

function formatMarkdown(text) {
  if (!text) return ''
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\n/g, '<br>')
}
</script>

<template>
  <div class="c-card overflow-hidden">
    <!-- Tabs -->
    <div class="flex border-b border-gray-100">
      <button
        @click="activeTab = 'analysis'"
        :class="['flex-1 py-3 text-xs font-semibold transition-colors border-b-2 cursor-pointer bg-transparent',
          activeTab === 'analysis' ? 'text-indigo-600 border-indigo-500' : 'text-gray-400 border-transparent hover:text-gray-600']"
      >
        <AppIcon name="chart-bar" :size="14" class="inline mr-1" /> Análisis IA
      </button>
      <button
        @click="activeTab = 'chat'"
        :class="['flex-1 py-3 text-xs font-semibold transition-colors border-b-2 cursor-pointer bg-transparent',
          activeTab === 'chat' ? 'text-indigo-600 border-indigo-500' : 'text-gray-400 border-transparent hover:text-gray-600']"
      >
        <AppIcon name="chat" :size="14" class="inline mr-1" /> Pregunta a la IA
      </button>
    </div>

    <!-- Analysis Tab -->
    <div v-if="activeTab === 'analysis'" class="p-5">
      <template v-if="ai.businessAnalysis">
        <!-- Summary -->
        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ ai.businessAnalysis.summary }}</p>

        <!-- Score + Risk -->
        <div class="flex gap-3 mb-4">
          <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ ai.businessAnalysis.score }}</div>
            <div class="text-[10px] text-gray-400 uppercase font-semibold">Score /100</div>
          </div>
          <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
            <div :class="['text-lg font-bold', ai.businessAnalysis.riskLevel === 'Bajo' ? 'text-green-600' : ai.businessAnalysis.riskLevel === 'Alto' ? 'text-red-600' : 'text-amber-600']">
              {{ ai.businessAnalysis.riskLevel }}
            </div>
            <div class="text-[10px] text-gray-400 uppercase font-semibold">Riesgo</div>
          </div>
          <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
            <div class="text-xs font-bold text-gray-700">
              {{ new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(ai.businessAnalysis.valuation?.sectorAvg || 0) }}
            </div>
            <div class="text-[10px] text-gray-400 uppercase font-semibold">Media sector</div>
          </div>
        </div>

        <!-- Strengths -->
        <div v-if="ai.businessAnalysis.strengths?.length" class="mb-3">
          <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Fortalezas</div>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="s in ai.businessAnalysis.strengths" :key="s" class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-md font-medium">
              {{ s }}
            </span>
          </div>
        </div>

        <!-- Weaknesses -->
        <div v-if="ai.businessAnalysis.weaknesses?.length">
          <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Puntos de atencion</div>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="w in ai.businessAnalysis.weaknesses" :key="w" class="text-xs bg-amber-50 text-amber-700 px-2 py-1 rounded-md font-medium">
              {{ w }}
            </span>
          </div>
        </div>
      </template>

      <!-- Loading -->
      <div v-else class="py-8 text-center">
        <div class="flex justify-center gap-1 mb-2">
          <span class="w-2 h-2 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
          <span class="w-2 h-2 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
          <span class="w-2 h-2 bg-indigo-300 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
        </div>
        <p class="text-xs text-gray-400">Analizando negocio...</p>
      </div>
    </div>

    <!-- Chat Tab -->
    <div v-if="activeTab === 'chat'" class="flex flex-col" style="height: 380px">
      <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50">
        <!-- Suggestions when empty -->
        <div v-if="ai.chatMessages.length === 0" class="space-y-2 pt-4">
          <p class="text-xs text-gray-400 text-center mb-3">Pregunta sobre este negocio:</p>
          <button
            v-for="q in suggestedQuestions"
            :key="q"
            @click="askSuggestion(q)"
            class="w-full text-left px-3 py-2 text-xs text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors border-none cursor-pointer"
          >
            {{ q }}
          </button>
        </div>

        <!-- Messages -->
        <div
          v-for="(msg, i) in ai.chatMessages"
          :key="i"
          :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']"
        >
          <div
            :class="[
              'p-2.5 rounded-lg text-xs leading-relaxed break-words max-w-[90%]',
              msg.role === 'user' ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 shadow-sm'
            ]"
            v-html="msg.role === 'assistant' ? formatMarkdown(msg.content) : msg.content"
          />
        </div>

        <!-- Loading -->
        <div v-if="ai.loading" class="flex gap-1 px-3">
          <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
          <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
          <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
        </div>
      </div>

      <!-- Input -->
      <div class="p-3 border-t border-gray-100 flex gap-2">
        <input
          v-model="input"
          @keyup.enter="send"
          type="text"
          placeholder="Pregunta sobre este negocio..."
          class="c-input flex-1 text-xs py-2 bg-gray-50"
          :disabled="ai.loading"
        />
        <button @click="send" class="c-btn c-btn--ai px-3 py-2 rounded-lg text-xs" :disabled="ai.loading || !input.trim()">
          <AppIcon name="arrow-right" :size="14" />
        </button>
      </div>
    </div>
  </div>
</template>
