<script setup>
import { ref } from 'vue'

const items = ref([
  { id: 1, name: 'Carlos Díaz', email: 'carlos@coyag.com', phone: '+34 600000003', role: 'Administrador' },
  { id: 2, name: 'Laura Ruiz', email: 'laura@coyag.com', phone: '+34 600000004', role: 'Asesor' },
])
</script>

<template>
  <div class="py-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-lg md:text-2xl font-bold" style="color: var(--color-text-heading);">Gestión de Empleados</h1>
      <button class="c-btn c-btn--primary text-sm">+ Nuevo Empleado</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-sm border-b border-gray-100">
          <tr>
            <th class="py-3 px-4 font-semibold">ID</th>
            <th class="py-3 px-4 font-semibold">Nombre</th>
            <th class="py-3 px-4 font-semibold">Email</th>
            <th class="py-3 px-4 font-semibold">Rol</th>
            <th class="py-3 px-4 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <tr v-for="b in items" :key="b.id" class="border-b border-gray-50 hover:bg-gray-50 transition">
            <td class="py-3 px-4 text-gray-500">#{{ b.id }}</td>
            <td class="py-3 px-4 font-medium text-gray-900">{{ b.name }}</td>
            <td class="py-3 px-4 text-gray-500">{{ b.email }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                {{ b.role }}
              </span>
            </td>
            <td class="py-3 px-4 text-right">
              <button class="text-blue-600 hover:text-blue-800 mr-2 text-xs font-semibold">Editar</button>
              <button class="text-red-600 hover:text-red-800 text-xs font-semibold">Borrar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
