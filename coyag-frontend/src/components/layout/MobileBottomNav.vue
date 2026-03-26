<script setup>
import { useRoute } from 'vue-router'
import AppIcon from '../ui/AppIcon.vue'

const route = useRoute()

const tabs = [
  { icon: 'home', label: 'Inicio', to: '/' },
  { icon: 'search', label: 'Buscar', to: '/listado-general' },
  { icon: 'heart', label: 'Favoritos', to: '/favoritos' },
  { icon: 'sparkles', label: 'IA', to: '/ai' },
  { icon: 'user', label: 'Perfil', to: '/mi-perfil' },
]
</script>

<template>
  <nav class="c-bottom-nav">
    <router-link
      v-for="tab in tabs"
      :key="tab.to"
      :to="tab.to"
      class="c-bottom-nav__item"
      :class="{ 'c-bottom-nav__item--active': route.path === tab.to }"
    >
      <AppIcon :name="tab.icon" :size="20" />
      <span>{{ tab.label }}</span>
    </router-link>
  </nav>
</template>
