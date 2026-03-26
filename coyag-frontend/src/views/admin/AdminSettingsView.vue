<script setup>
import { ref } from 'vue'
import AppIcon from '../../components/ui/AppIcon.vue'

const activeTab = ref('geo') // 'geo', 'types', 'ops'

// Sub-tabs for Geography
const geoSubTab = ref('municipios')
const geoData = ref([
  { id: 1, name: 'Madrid', prefix: 'MAD', count: 145 },
  { id: 2, name: 'Barcelona', prefix: 'BCN', count: 98 },
  { id: 3, name: 'Valencia', prefix: 'VAL', count: 42 }
])

// Types Data
const typesData = ref([
  { id: 1, name: 'Traspaso', active: true },
  { id: 2, name: 'Franquicia', active: true },
  { id: 3, name: 'Inmueble Libre', active: true }
])

// Ops Data
const advisorsData = ref([
  { id: 1, name: 'Agente Zona Centro', clients: 45, status: 'Activo' },
  { id: 2, name: 'Agente Levante', clients: 12, status: 'Activo' }
])

function deleteItem(type, id) {
  if (confirm('¿Seguro que deseas eliminar este registro?')) {
    if (type === 'geo') geoData.value = geoData.value.filter(i => i.id !== id)
    if (type === 'types') typesData.value = typesData.value.filter(i => i.id !== id)
    if (type === 'ops') advisorsData.value = advisorsData.value.filter(i => i.id !== id)
  }
}
</script>

<template>
  <div class="py-6 animate-fade-in max-w-7xl mx-auto">
    <!-- Header -->
    <header class="mb-8">
      <h1 class="text-xl md:text-3xl font-extrabold flex items-center gap-3 text-gray-900">
        <AppIcon name="cog" :size="20" class="text-[var(--color-primary)]" /> Configuracion Global
      </h1>
      <p class="mt-2 text-base text-gray-500 max-w-2xl">
        Administra las tablas maestras del sistema: Ubicaciones geográficas, tipologías de negocio y operativa de asesores y pagos.
      </p>
    </header>

    <div class="flex flex-col md:flex-row gap-8">
      
      <!-- General Sidebar Tabs -->
      <aside class="w-full md:w-64 shrink-0">
        <nav class="flex flex-col gap-2">
          <button 
            @click="activeTab = 'geo'"
            class="px-4 py-3 text-left rounded-xl transition-all font-semibold"
            :class="activeTab === 'geo' ? 'bg-red-50 text-[var(--color-primary)] shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
          >
            <AppIcon name="map-pin" :size="16" class="inline mr-1" /> Geografia y Zonas
          </button>
          <button
            @click="activeTab = 'types'"
            class="px-4 py-3 text-left rounded-xl transition-all font-semibold"
            :class="activeTab === 'types' ? 'bg-red-50 text-[var(--color-primary)] shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
          >
            <AppIcon name="tag" :size="16" class="inline mr-1" /> Tipologias y Sectores
          </button>
          <button
            @click="activeTab = 'ops'"
            class="px-4 py-3 text-left rounded-xl transition-all font-semibold"
            :class="activeTab === 'ops' ? 'bg-red-50 text-[var(--color-primary)] shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
          >
            <AppIcon name="briefcase" :size="16" class="inline mr-1" /> Operativa y Asesores
          </button>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1">
        
        <!-- Tab 1: Geo Configurations -->
        <div v-if="activeTab === 'geo'" class="animate-fade-in">
          <div class="c-card p-0 overflow-hidden shadow-sm border border-gray-100">
            <!-- Sub tabs -->
            <div class="flex border-b border-gray-100 bg-gray-50/50">
              <button @click="geoSubTab = 'municipios'" class="px-6 py-4 font-semibold text-sm border-b-2 transition-colors" :class="geoSubTab === 'municipios' ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-gray-500 hover:text-gray-700'">Municipios</button>
              <button @click="geoSubTab = 'distritos'" class="px-6 py-4 font-semibold text-sm border-b-2 transition-colors" :class="geoSubTab === 'distritos' ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-gray-500 hover:text-gray-700'">Distritos</button>
              <button @click="geoSubTab = 'barrios'" class="px-6 py-4 font-semibold text-sm border-b-2 transition-colors" :class="geoSubTab === 'barrios' ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-gray-500 hover:text-gray-700'">Barrios</button>
            </div>
            
            <div class="p-6">
              <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold capitalize">Gestión de {{ geoSubTab }}</h2>
                <button class="c-btn c-btn--primary py-2 px-4 text-sm">+ Añadir {{ geoSubTab.slice(0,-1) }}</button>
              </div>
              
              <div class="overflow-x-auto">
              <table class="w-full text-left">
                <thead>
                  <tr class="bg-gray-50 rounded-lg text-gray-500 text-xs uppercase tracking-wider">
                    <th class="p-3 font-semibold rounded-l-lg">ID</th>
                    <th class="p-3 font-semibold">Nombre</th>
                    <th class="p-3 font-semibold">Prefijo / Código</th>
                    <th class="p-3 font-semibold text-right">Negocios Vinculados</th>
                    <th class="p-3 font-semibold text-center rounded-r-lg">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="item in geoData" :key="item.id" class="hover:bg-gray-50/50">
                    <td class="p-3 text-sm text-gray-500">{{ item.id }}</td>
                    <td class="p-3 font-bold text-gray-900">{{ item.name }}</td>
                    <td class="p-3 text-sm text-gray-500"><span class="bg-gray-100 px-2 py-1 rounded">{{ item.prefix }}</span></td>
                    <td class="p-3 text-sm text-gray-600 text-right">{{ item.count }}</td>
                    <td class="p-3 text-center">
                      <button class="text-indigo-600 hover:text-indigo-900 mx-2 text-sm font-semibold">Editar</button>
                      <button @click="deleteItem('geo', item.id)" class="text-red-500 hover:text-red-700 mx-2 text-sm font-semibold">Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab 2: Typologies -->
        <div v-if="activeTab === 'types'" class="animate-fade-in">
          <div class="c-card p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-xl font-bold">Tipos de Operación</h2>
              <button class="c-btn c-btn--primary py-2 px-4 text-sm">+ Añadir Tipo</button>
            </div>
            
            <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-gray-50 rounded-lg text-gray-500 text-xs uppercase tracking-wider">
                  <th class="p-3 font-semibold rounded-l-lg">Nombre del Tipo</th>
                  <th class="p-3 font-semibold">Estado</th>
                  <th class="p-3 font-semibold text-center rounded-r-lg">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in typesData" :key="item.id" class="hover:bg-gray-50/50">
                  <td class="p-3 font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
                    {{ item.name }}
                  </td>
                  <td class="p-3 text-sm">
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Activo</span>
                  </td>
                  <td class="p-3 text-center">
                    <button class="text-indigo-600 hover:text-indigo-900 mx-2 text-sm font-semibold">Editar</button>
                    <button @click="deleteItem('types', item.id)" class="text-red-500 hover:text-red-700 mx-2 text-sm font-semibold">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>
        </div>

        <!-- Tab 3: Ops -->
        <div v-if="activeTab === 'ops'" class="animate-fade-in">
          <div class="c-card p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-xl font-bold">Asesores Asignados</h2>
              <div class="flex gap-2">
                <button class="c-btn c-btn--outline py-2 px-4 text-sm">Configurar Pasarelas</button>
                <button class="c-btn c-btn--primary py-2 px-4 text-sm">+ Nuevo Asesor</button>
              </div>
            </div>
            
            <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-gray-50 rounded-lg text-gray-500 text-xs uppercase tracking-wider">
                  <th class="p-3 font-semibold rounded-l-lg">Nombre Asesor</th>
                  <th class="p-3 font-semibold text-center">Clientes Carterizados</th>
                  <th class="p-3 font-semibold">Estado de Cuenta</th>
                  <th class="p-3 font-semibold text-center rounded-r-lg">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in advisorsData" :key="item.id" class="hover:bg-gray-50/50">
                  <td class="p-3 font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex justify-center items-center">
                      <AppIcon name="user" :size="14" />
                    </div>
                    {{ item.name }}
                  </td>
                  <td class="p-3 text-center font-semibold text-gray-700">{{ item.clients }}</td>
                  <td class="p-3 text-sm">
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">{{ item.status }}</span>
                  </td>
                  <td class="p-3 text-center">
                    <button class="text-indigo-600 hover:text-indigo-900 mx-2 text-sm font-semibold">Ajustes</button>
                    <button @click="deleteItem('ops', item.id)" class="text-red-500 hover:text-red-700 mx-2 text-sm font-semibold">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>
        </div>

      </main>
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
