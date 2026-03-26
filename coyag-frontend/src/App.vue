<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { useAuthStore } from './stores/auth'
import AppSidebar from './components/layout/AppSidebar.vue'
import AppHeader from './components/layout/AppHeader.vue'
import MobileBottomNav from './components/layout/MobileBottomNav.vue'
import AiChatWidget from './components/ai/AiChatWidget.vue'
import CompareWidget from './components/business/CompareWidget.vue'
import { useRoute } from 'vue-router'

const auth = useAuthStore()
const route = useRoute()
const sidebarCollapsed = ref(false)
const mobileMenuOpen = ref(false)
const isMobile = ref(false)

function checkMobile() {
  isMobile.value = window.innerWidth < 1024
  if (!isMobile.value) {
    mobileMenuOpen.value = false
  }
}

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

function closeMobileMenu() {
  mobileMenuOpen.value = false
}

function onSidebarCollapse(val) {
  sidebarCollapsed.value = val
}

// Close mobile menu on route change
watch(() => route.path, () => {
  if (isMobile.value) closeMobileMenu()
})

const sidebarWidth = computed(() => {
  if (isMobile.value) return '0px'
  return sidebarCollapsed.value ? '72px' : '260px'
})

onMounted(() => {
  auth.restoreSession()
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<template>
  <div id="app" class="min-h-screen bg-[var(--color-bg-page)]">
    <!-- Blank layout for auth pages -->
    <template v-if="route.meta.layout === 'blank'">
      <router-view />
    </template>

    <!-- Main Dashboard Layout -->
    <template v-else>
      <!-- Mobile overlay -->
      <Transition name="fade">
        <div
          v-if="mobileMenuOpen && isMobile"
          class="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm"
          @click="closeMobileMenu"
        />
      </Transition>

      <!-- Left Sidebar -->
      <AppSidebar
        :collapsed="sidebarCollapsed"
        :is-mobile="isMobile"
        :mobile-open="mobileMenuOpen"
        @collapse="onSidebarCollapse"
        @close-mobile="closeMobileMenu"
      />

      <!-- App Header -->
      <AppHeader
        :is-mobile="isMobile"
        :sidebar-width="sidebarWidth"
        @toggle-menu="toggleMobileMenu"
      />

      <!-- Page Content -->
      <div
        class="transition-all duration-300 min-h-screen pt-[64px]"
        :style="{ marginLeft: sidebarWidth }"
      >
        <main class="p-4 md:p-6 lg:p-8 pb-20 lg:pb-8">
          <router-view v-slot="{ Component }">
            <transition name="page-fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </main>
      </div>

      <!-- Mobile Bottom Nav -->
      <MobileBottomNav v-if="isMobile" />
      <!-- Global Widgets -->
      <AiChatWidget v-if="auth.isLoggedIn" :is-mobile="isMobile" />
      <CompareWidget :is-mobile="isMobile" />
    </template>
  </div>
</template>

<style>
.page-fade-enter-active,
.page-fade-leave-active {
  transition: all 0.25s ease;
}
.page-fade-enter-from {
  opacity: 0;
  transform: translateY(12px);
}
.page-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
