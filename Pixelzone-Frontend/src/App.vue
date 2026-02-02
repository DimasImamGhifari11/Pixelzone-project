<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container-fluid">
        <router-link to="/" class="navbar-brand fw-bold text-uppercase">Pixelzone</router-link>
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown me-3" v-if="isLoggedIn" ref="dropdownRef">
              <a @click.prevent="toggleDropdown" class="nav-link" href="#" role="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" :class="{ show: showDropdown }">
                <li><router-link to="/categories" class="dropdown-item" @click="showDropdown = false">Kategori</router-link></li>
                <li><router-link to="/products" class="dropdown-item" @click="showDropdown = false">Produk</router-link></li>
                <li><router-link to="/checkout" class="dropdown-item" @click="showDropdown = false">Checkout</router-link></li>
                <li><hr class="dropdown-divider"></li>
                <li><a @click.prevent="handleLogout" class="dropdown-item" href="#">Logout</a></li>
              </ul>
            </li>
            <li class="nav-item" v-if="!isLoggedIn">
              <router-link to="/login" class="btn btn-primary me-3">Login</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <main class="main-content">
      <div class="container-fluid mt-5">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" @update-login-status="updateLoginStatus" />
          </transition>
        </router-view>
      </div>
    </main>
    <footer class="bg-dark text-white text-center p-3">
      &copy; 2025 Pixelzone. All Rights Reserved.
    </footer>
    <Notification />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import Notification from './components/Notification.vue';
import { useRouter } from 'vue-router';
import { store } from './store'; // Import the store

const router = useRouter();
const isLoggedIn = computed(() => {
  console.log('App.vue - isLoggedIn:', store.isAuthenticated);
  return store.isAuthenticated;
});
const showDropdown = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

const handleLogout = async () => {
  try {
    await api.post('/logout');
    store.clearAuth(); // Clear auth state in the store
    showDropdown.value = false;
    router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
    // Even if API fails, force clear auth on client-side
    store.clearAuth();
    router.push('/login');
  }
};

onMounted(async () => {
  console.log('App.vue - onMounted: Checking auth status');
  await store.checkAuth(); // Check authentication status when the app loads
  document.addEventListener('click', handleClickOutside, true);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside, true);
});
</script>

<style>
#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  overflow-y: auto; /* Allow vertical scroll */
  overflow-x: hidden; /* Prevent horizontal scroll */
}

/* Styling Global */
body {
  font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  margin: 0;
  background-color: #f5f5f7; /* Apple-like light gray */
  color: #1d1d1f; /* Apple-like black text */
  min-height: 100vh;
}

/* Improved Dropdown Styles */
.navbar-nav .dropdown-menu {
  position: absolute;
  border: 1px solid rgba(0,0,0,0.1);
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}

.navbar-nav .dropdown-menu-end {
  right: 0;
  left: auto;
}

/* Page Transition Animation */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
