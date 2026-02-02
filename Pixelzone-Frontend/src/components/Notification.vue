<template>
  <transition name="fade">
    <div v-if="notification.visible" :class="['notification', notification.type]">
      {{ notification.message }}
    </div>
  </transition>
</template>

<script setup>
import { computed } from 'vue';
import { store } from '../store';

const notification = computed(() => store.notification);
</script>

<style scoped>
.notification {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 15px 25px;
  border-radius: 8px;
  color: white;
  font-weight: 600;
  z-index: 1001;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.notification.success {
  background-color: #28a745;
}

.notification.error {
  background-color: #dc3545;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>
