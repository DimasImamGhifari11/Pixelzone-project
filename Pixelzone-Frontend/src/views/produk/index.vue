<template>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="section-title">Daftar Produk</h2>
      <button @click="goToCreateProduct" class="btn btn-primary">Tambah Produk</button>
    </div>
    <div class="row g-4">
      <div v-for="produk in produks" :key="produk.id" class="col-lg-3 col-md-4 col-sm-6">
        <div class="card product-card h-100">
          <div class="product-image-wrapper">
            <img
              :src="produk.image && produk.image.startsWith('http') ? produk.image : `http://127.0.0.1:8000/storage/${produk.image}`"
              class="product-image"
              alt="Gambar Produk"
            />
          </div>
<div class="card-body">
              <h5 class="card-title">{{ produk.name }}</h5>
              <p class="card-price fw-bold">Rp {{ produk.price }}</p>
              <span v-if="produk.is_new" class="badge bg-success">Baru</span>
            </div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <button @click="addToCart(produk)" class="btn btn-primary btn-sm">Beli</button>
            <div>
              <button @click="goToEditProduct(produk.id)" class="btn btn-outline-secondary btn-sm">Edit</button>
              <button @click="deleteProduct(produk.id)" class="btn btn-outline-danger btn-sm ms-2">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../axios';
import { useRouter } from 'vue-router';
import { store } from '../../store'; // Import the store

const router = useRouter();
const produks = ref([]);

// Function to show notification
const showNotification = (message, type = 'success') => {
  store.notification.message = message;
  store.notification.type = type;
  store.notification.visible = true;
  setTimeout(() => {
    store.notification.visible = false;
  }, 3000); // Notification disappears after 3 seconds
};

// Fetch Produk
const fetchProduks = () => {
  api.get('/products').then((response) => {
    produks.value = response.data.data;
  });
};

// Hapus Produk
const deleteProduct = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
    api.delete(`/products/${id}`)
      .then(() => {
        fetchProduks(); // Muat ulang daftar produk
        showNotification('Produk berhasil dihapus!', 'success');
      })
      .catch(error => {
        console.error("Error deleting product:", error);
        showNotification('Gagal menghapus produk.', 'error');
      });
  }
};

// Fungsi untuk menambahkan produk ke keranjang (simulasi)
const addToCart = (product) => {
  console.log('Menambahkan produk ke keranjang:', product.name);
  showNotification(`${product.name} telah ditambahkan ke keranjang!`, 'success');
};

// Navigasi ke halaman create
const goToCreateProduct = () => {
  router.push('/create-product');
};

// Navigasi ke halaman edit
const goToEditProduct = (id) => {
  router.push(`/edit-product/${id}`);
};

onMounted(() => {
  fetchProduks();
});
</script>

<style scoped>
/* Apple-inspired minimalist design V2 */
.product-card {
  background-color: #ffffff;
  border-radius: 18px;
  border: 1px solid #e5e5e5;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.product-image-wrapper {
  width: 100%;
  aspect-ratio: 2 / 3; /* Respect 800x1200 aspect ratio */
  background-color: #f5f5f7;
  padding: 1rem;
  box-sizing: border-box;
}
.product-image {
  width: 100%;
  height: 100%;
  object-fit: contain; /* Fit image without cropping */
}
.card-body {
  padding: 1rem 1.5rem;
  text-align: left;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}
.card-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1d1d1f;
}
.card-price {
    color: #0071e3;
    font-size: 1.2rem;
    font-weight: 600;
}
.card-footer {
    background-color: #ffffff;
    border-top: 1px solid #e5e5e5;
}
</style>
