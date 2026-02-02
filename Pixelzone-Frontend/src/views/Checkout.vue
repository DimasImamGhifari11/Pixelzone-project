<template>
  <div class="checkout-container">
    <h1>Keranjang Belanja</h1>
    <div v-if="cartItems.length === 0" class="empty-cart">
      <p>Keranjang belanja Anda kosong.</p>
      <router-link to="/" class="btn btn-secondary">Mulai Belanja</router-link>
    </div>
    <div v-else>
      <div v-for="item in cartItems" :key="item.id" class="cart-item">
        <input type="checkbox" v-model="item.selected" @change="updateTotal" class="form-check-input">
        <img :src="item.image && item.image.startsWith('http') ? item.image : `http://127.0.0.1:8000/storage/${item.image}`" alt="Gambar Game" class="item-image">
        <div class="item-details">
          <h5>{{ item.name }}</h5>
          <p>Rp {{ new Intl.NumberFormat('id-ID').format(item.price) }}</p>
        </div>
        <button @click="removeFromCart(item.id)" class="btn btn-danger btn-sm">Hapus</button>
      </div>
      <div class="checkout-summary">
        <h3>Total: Rp {{ new Intl.NumberFormat('id-ID').format(totalPrice) }}</h3>
        <button @click="proceedToPayment" class="btn btn-success btn-lg">Beli</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { store } from '../store';

const router = useRouter();
const cartItems = ref(store.cart.map(item => ({ ...item, selected: true })));

const totalPrice = computed(() => {
  return cartItems.value
    .filter(item => item.selected)
    .reduce((total, item) => total + item.price, 0);
});

const updateTotal = () => {
  // This function is implicitly handled by the computed property 'totalPrice'
};

const removeFromCart = (productId) => {
  store.removeFromCart(productId);
  cartItems.value = cartItems.value.filter(item => item.id !== productId);
};

const proceedToPayment = () => {
  const selectedItems = cartItems.value.filter(item => item.selected);
  sessionStorage.setItem('processedItems', JSON.stringify(selectedItems));
  router.push('/payment-success');
};
</script>

<style scoped>
.checkout-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  /* box-shadow: 0 4px 12px rgba(0,0,0,0.1); */
}

h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: #1d1d1f;
}

.empty-cart {
  text-align: center;
  padding: 2rem;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e8e8e8;
}

.form-check-input {
  margin-right: 1rem;
  width: 1.5rem;
  height: 1.5rem;
  box-shadow: none;
}

.item-image {
  width: 80px;
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
  margin-right: 1rem;
}

.item-details {
  flex-grow: 1;
}

.item-details h5 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
  font-weight: 600;
}

.item-details p {
  margin: 0;
  font-size: 1rem;
  color: #6c757d;
}

.checkout-summary {
  margin-top: 2rem;
  text-align: right;
}

.checkout-summary h3 {
  font-weight: 700;
  margin-bottom: 1rem;
}

.form-check-input:checked {
  background-color: #007bff;
  border-color: #007bff;
}

</style>
