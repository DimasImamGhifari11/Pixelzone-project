<template>
  <div class="payment-success-container">
    <div class="card text-center">
      <div class="card-body">
        <h1 class="card-title">Pembayaran Berhasil!</h1>
        <p class="card-text">Terima kasih telah berbelanja di Pixelzone.</p>
        <router-link to="/" class="btn btn-secondary">Kembali ke Dashboard</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { store } from '../store';

onMounted(() => {
  const processedItems = JSON.parse(sessionStorage.getItem('processedItems'));
  if (processedItems && processedItems.length > 0) {
    const itemIdsToRemove = processedItems.map(item => item.id);
    store.removeItemsFromCart(itemIdsToRemove);
    sessionStorage.removeItem('processedItems');
  }
});
</script>

<style scoped>
.payment-success-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 80vh;
}

.card {
  width: 100%;
  max-width: 400px;
  padding: 2rem;
  border: none;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-title {
  font-size: 2rem;
  font-weight: 700;
  color: #28a745;
  margin-bottom: 1rem;
}

.card-text {
  font-size: 1.1rem;
  color: #6c757d;
  margin-bottom: 2rem;
}

.btn-secondary {
  background-color: #8e8e93;
  border-color: #8e8e93;
}
</style>
