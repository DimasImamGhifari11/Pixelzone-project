import { reactive, watch } from 'vue';
import api from './axios'; // Import axios instance

// Helper function to save cart to localStorage
const saveCart = (cart) => {
  localStorage.setItem('cart', JSON.stringify(cart));
};

// Load cart from localStorage or initialize as empty array
const initialCart = JSON.parse(localStorage.getItem('cart')) || [];

// Initial authentication state
const initialToken = sessionStorage.getItem('token');
const initialUser = JSON.parse(sessionStorage.getItem('user'));

export const store = reactive({
  // Notification state
  notification: {
    visible: false,
    message: '',
    type: 'success', // 'success' or 'error'
  },

  // Cart state
  cart: initialCart,

  // Auth state
  isAuthenticated: !!initialToken,
  user: initialUser,
  token: initialToken,

  // Debugging
  get isAuthenticatedDebug() {
    console.log('Store - isAuthenticated:', this.isAuthenticated);
    return this.isAuthenticated;
  },

  // Cart methods
  addToCart(product) {
    const existingProduct = this.cart.find(item => item.id === product.id);
    if (!existingProduct) {
      this.cart.push(product);
      saveCart(this.cart);
    } else {
      console.log('Product already in cart');
    }
  },

  removeFromCart(productId) {
    this.cart = this.cart.filter(item => item.id !== productId);
    saveCart(this.cart);
  },

  clearCart() {
    this.cart = [];
    saveCart(this.cart);
  },

  removeItemsFromCart(itemIds) {
    this.cart = this.cart.filter(item => !itemIds.includes(item.id));
    saveCart(this.cart);
  },

  // Auth methods
  setAuth(token, user) {
    this.isAuthenticated = true;
    this.user = user;
    this.token = token;
    sessionStorage.setItem('token', token);
    sessionStorage.setItem('user', JSON.stringify(user));
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    console.log('Store - setAuth: isAuthenticated set to true');
  },

  clearAuth() {
    this.isAuthenticated = false;
    this.user = null;
    this.token = null;
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('user');
    delete api.defaults.headers.common['Authorization'];
    console.log('Store - clearAuth: isAuthenticated set to false');
  },

  // Check auth status with API
  async checkAuth() {
    if (this.token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      try {
        const response = await api.get('/user');
        this.setAuth(this.token, response.data);
        console.log('Store - checkAuth: User data fetched successfully');
      } catch (error) {
        console.error('Store - checkAuth: Failed to fetch user data', error);
        this.clearAuth();
      }
    } else {
      console.log('Store - checkAuth: No token found in sessionStorage');
      this.clearAuth();
    }
  },

  // Notification methods
  showNotification(message, type = 'success', duration = 3000) {
    this.notification.message = message;
    this.notification.type = type;
    this.notification.visible = true;

    setTimeout(() => {
      this.notification.visible = false;
    }, duration);
  }
});

// Watch for cart changes and save to localStorage
watch(() => store.cart, (newCart) => {
  saveCart(newCart);
}, { deep: true });
