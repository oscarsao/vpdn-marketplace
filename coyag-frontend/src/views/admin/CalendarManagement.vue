<script setup>
import { ref, reactive } from 'vue'
import AppIcon from '../../components/ui/AppIcon.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

// Estado del calendario y los eventos
const events = ref([
  { id: '1', title: 'Visita Local Madrid', start: new Date().toISOString().split('T')[0] + 'T10:00:00', end: new Date().toISOString().split('T')[0] + 'T11:30:00', backgroundColor: 'var(--color-primary)', borderColor: 'var(--color-primary)' },
  { id: '2', title: 'Firma Contrato Franquicia', start: new Date().toISOString().split('T')[0] + 'T16:00:00', backgroundColor: 'var(--color-success)', borderColor: 'var(--color-success)' }
])

const showModal = ref(false)
const modalMode = ref('add') // 'add' or 'edit'
const selectedEvent = ref(null)

const formData = reactive({
  title: '',
  date: '',
  time: '',
  color: 'var(--color-primary)'
})

const calendarOptions = reactive({
  plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  initialView: 'dayGridMonth',
  editable: true,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true,
  events: events.value,
  
  // Handlers
  select: handleDateSelect,
  eventClick: handleEventClick,
  eventChange: handleEventChange,
  
  // Idioma
  locale: 'es',
  buttonText: {
    today: 'Hoy',
    month: 'Mes',
    week: 'Semana',
    day: 'Día',
    list: 'Agenda'
  }
})

function handleDateSelect(selectInfo) {
  modalMode.value = 'add'
  selectedEvent.value = null
  
  // Pre-fill form
  formData.title = ''
  formData.date = selectInfo.startStr.split('T')[0]
  formData.time = selectInfo.startStr.includes('T') ? selectInfo.startStr.split('T')[1].substring(0,5) : '10:00'
  formData.color = 'var(--color-primary)'
  
  showModal.value = true
  calendarOptions.events = events.value // reactividad
  
  // Clear selection
  let calendarApi = selectInfo.view.calendar
  calendarApi.unselect()
}

function handleEventClick(clickInfo) {
  modalMode.value = 'edit'
  selectedEvent.value = clickInfo.event
  
  formData.title = clickInfo.event.title
  formData.date = clickInfo.event.startStr.split('T')[0]
  formData.time = clickInfo.event.startStr.includes('T') ? clickInfo.event.startStr.split('T')[1].substring(0,5) : ''
  formData.color = clickInfo.event.backgroundColor || 'var(--color-primary)'
  
  showModal.value = true
}

function handleEventChange(changeInfo) {
  // Update internal state when dragged/resized
  const index = events.value.findIndex(e => e.id === changeInfo.event.id)
  if (index !== -1) {
    events.value[index].start = changeInfo.event.startStr
    events.value[index].end = changeInfo.event.endStr
  }
}

function saveEvent() {
  if (!formData.title) return
  
  const startStr = formData.time ? `${formData.date}T${formData.time}:00` : formData.date
  
  if (modalMode.value === 'add') {
    const newEvent = {
      id: String(Date.now()),
      title: formData.title,
      start: startStr,
      backgroundColor: formData.color,
      borderColor: formData.color
    }
    events.value.push(newEvent)
  } else if (modalMode.value === 'edit' && selectedEvent.value) {
    const index = events.value.findIndex(e => e.id === selectedEvent.value.id)
    if (index !== -1) {
      events.value[index].title = formData.title
      events.value[index].start = startStr
      events.value[index].backgroundColor = formData.color
      events.value[index].borderColor = formData.color
    }
  }
  
  // Force calendar refresh by re-assigning
  calendarOptions.events = [...events.value]
  showModal.value = false
}

function deleteEvent() {
  if (selectedEvent.value && confirm(`¿Estás seguro de eliminar el evento "${selectedEvent.value.title}"?`)) {
    events.value = events.value.filter(e => e.id !== selectedEvent.value.id)
    calendarOptions.events = [...events.value]
    showModal.value = false
  }
}
</script>

<template>
  <div class="py-6 animate-fade-in max-w-7xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-xl md:text-3xl font-extrabold flex items-center gap-3" style="color: var(--color-text-heading);">
          <AppIcon name="calendar" :size="20" class="text-[var(--color-primary)]" /> Calendario de Citas
        </h1>
        <p class="mt-2 text-base text-gray-500">
          Gestiona las visitas a inmuebles, firmas y reuniones con clientes. Haz click en cualquier día para añadir eventos.
        </p>
      </div>
      <button @click="handleDateSelect({startStr: new Date().toISOString()})" class="c-btn c-btn--primary">
        + Nueva Cita
      </button>
    </header>

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
      <FullCalendar class="fc-custom" :options="calendarOptions" />
    </div>

    <!-- Event Modal (Simulated dialog overlay) -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-[calc(100vw-2rem)] md:max-w-md m-4 transform animate-scale-up">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">
          {{ modalMode === 'add' ? 'Añadir Nueva Cita' : 'Editar Cita' }}
        </h2>
        
        <form @submit.prevent="saveEvent" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Título de la cita</label>
            <input v-model="formData.title" type="text" class="c-input w-full" placeholder="Ej: Visita Local Comercial" required />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
              <input v-model="formData.date" type="date" class="c-input w-full" required />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Hora (Opcional)</label>
              <input v-model="formData.time" type="time" class="c-input w-full" />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Color del evento</label>
            <div class="flex gap-3">
              <button 
                type="button"
                @click="formData.color = 'var(--color-primary)'"
                class="w-8 h-8 rounded-full bg-[var(--color-primary)] border-2 transition-all"
                :class="formData.color === 'var(--color-primary)' ? 'border-gray-900 scale-110 shadow-md' : 'border-transparent'"
              ></button>
              <button 
                type="button"
                @click="formData.color = 'var(--color-success)'"
                class="w-8 h-8 rounded-full bg-[var(--color-success)] border-2 transition-all"
                :class="formData.color === 'var(--color-success)' ? 'border-gray-900 scale-110 shadow-md' : 'border-transparent'"
              ></button>
              <button 
                type="button"
                @click="formData.color = 'var(--color-info)'"
                class="w-8 h-8 rounded-full bg-[var(--color-info)] border-2 transition-all"
                :class="formData.color === 'var(--color-info)' ? 'border-gray-900 scale-110 shadow-md' : 'border-transparent'"
              ></button>
              <button 
                type="button"
                @click="formData.color = 'var(--color-warning)'"
                class="w-8 h-8 rounded-full bg-[var(--color-warning)] border-2 transition-all"
                :class="formData.color === 'var(--color-warning)' ? 'border-gray-900 scale-110 shadow-md' : 'border-transparent'"
              ></button>
            </div>
          </div>
          
          <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between">
            <button 
              v-if="modalMode === 'edit'" 
              type="button" 
              @click="deleteEvent" 
              class="text-red-500 font-medium hover:bg-red-50 px-3 py-2 rounded-lg transition"
            >
              <AppIcon name="trash" :size="14" class="inline mr-1" /> Eliminar
            </button>
            <div v-else></div> <!-- Spacer -->
            
            <div class="flex gap-3">
              <button type="button" @click="showModal = false" class="c-btn c-btn--ghost text-gray-500">
                Cancelar
              </button>
              <button type="submit" class="c-btn c-btn--primary px-6">
                Guardar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style>
/* FullCalendar custom overrides */
.fc-custom {
  --fc-border-color: #f1f5f9;
  --fc-button-text-color: #475569;
  --fc-button-bg-color: #ffffff;
  --fc-button-border-color: #cbd5e1;
  --fc-button-hover-bg-color: #f8fafc;
  --fc-button-hover-border-color: #94a3b8;
  --fc-button-active-bg-color: #e2e8f0;
  --fc-button-active-border-color: #64748b;
  --fc-event-bg-color: var(--color-primary);
  --fc-event-border-color: var(--color-primary);
  --fc-today-bg-color: rgba(164, 14, 5, 0.05); /* Rojo muuuuy suave de highlight */
  font-family: var(--font-sans);
}

.fc-toolbar-title {
  font-weight: 800 !important;
  color: var(--color-text-heading);
  text-transform: capitalize;
}

.fc .fc-button-primary {
  font-weight: 600;
  border-radius: 8px;
  text-transform: capitalize;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.fc .fc-button-primary:not(:disabled).fc-button-active, 
.fc .fc-button-primary:not(:disabled):active {
  background-color: var(--color-primary);
  border-color: var(--color-primary-dark);
  color: white;
}

.fc-daygrid-day-number {
  font-weight: 600;
  color: #64748b;
  padding: 8px !important;
}

.fc-event {
  border-radius: 4px;
  padding: 2px 4px;
  font-size: 0.75rem;
  font-weight: 600;
  border: none !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.fc-event:hover {
  transform: translateY(-1px);
  cursor: pointer;
}

/* Modal Animations */
.animate-scale-up {
  animation: scaleUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes scaleUp {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
