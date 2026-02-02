<template>
  <div>
    <!-- Hero Section -->
    <div class="hero-section text-center">
      <div class="hero-content">
        <h1 class="display-4">Koleksi Game Terbaru</h1>
        <p class="lead">Temukan game favoritmu dengan promo terbaik hanya di Pixelzone.</p>
        <button class="btn btn-lg btn-secondary">Jelajahi Promo</button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
      <div class="text-center mb-5">
        <h2>Koleksi Game Unggulan</h2>
        <p class="lead text-muted">Temukan game favorit baru Anda dari koleksi pilihan kami.</p>
      </div>
      
      <div class="row g-4">
        <div v-for="product in products" :key="product.id" class="col-lg-3 col-md-4 col-sm-6">
          <div class="card product-card h-100" :class="{ 'selected': isSelected(product) }">
            <div class="product-image-container">
              <img
                :src="product.image && product.image.startsWith('http') ? product.image : `http://127.0.0.1:8000/storage/${product.image}`"
                class="product-image"
                alt="Gambar Game"
              />
              <span v-if="product.is_new" class="badge-new">BARU</span>
              <div @click="toggleSelect(product)" class="select-checkbox">
                <span v-if="isSelected(product)" class="checkmark">✔</span>
              </div>
            </div>
            <div class="card-body d-flex flex-column">
              <p class="card-category text-muted">{{ product.category?.name || 'Uncategorized' }}</p>
              <h5 class="card-title">{{ product.name }}</h5>
              <p class="card-price fw-bold">Rp {{ new Intl.NumberFormat('id-ID').format(product.price) }}</p>
              <button @click="buyNow(product)" class="btn btn-secondary mt-auto">
                Buy
              </button>
            </div>
          </div>
        </div>
        <div v-if="!products.length" class="col-12">
          <p class="text-center text-muted">Belum ada produk untuk ditampilkan.</p>
        </div>
      </div>

      <!-- Floating Action Button -->
      <div v-if="selectedProducts.length > 0" class="floating-cart-button">
        <button @click="addToCartAndCheckout" class="btn btn-secondary btn-lg">
          Add to Basket ({{ selectedProducts.length }})
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { store } from '../store';
import api from '../axios';

const products = ref([]);
const selectedProducts = ref([]);
const router = useRouter();

const isSelected = (product) => {
  return selectedProducts.value.some(p => p.id === product.id);
};

const toggleSelect = (product) => {
  if (isSelected(product)) {
    selectedProducts.value = selectedProducts.value.filter(p => p.id !== product.id);
  } else {
    if (selectedProducts.value.length < 3) {
      selectedProducts.value.push(product);
    } else {
      store.showNotification('Maksimal 3 produk untuk di tambahkan ke keranjang.');
    }
  }
};

const buyNow = (product) => {
  store.addToCart(product);
  router.push('/checkout');
};

const addToCartAndCheckout = () => {
  const productCount = selectedProducts.value.length;
  if (productCount < 2) {
    store.showNotification('Minimal 2 produk harus dipilih untuk ditambahkan ke keranjang.');
    return;
  }
  if (productCount > 3) {
    store.showNotification('Maksimal 3 produk dapat ditambahkan ke keranjang.');
    return;
  }

  selectedProducts.value.forEach(product => {
    store.addToCart(product);
  });
  selectedProducts.value = []; // Kosongkan pilihan setelah ditambahkan
  store.showNotification(`${productCount} produk berhasil ditambahkan ke keranjang!`);
};

onMounted(async () => {
  try {
    const response = await api.get('/products');
    products.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch products:', error);
  }
});
</script>

<style scoped>
/* Hero Section */
.hero-section {
  padding: 5rem 1rem;
  background-color: #f5f5f7;
  margin-bottom: 2rem;
}

.hero-content h1 {
  font-weight: 700;
  color: #1d1d1f;
}

.hero-content .lead {
  color: #6c757d;
}

.hero-content .btn {
  background-color: #6c757d;
  color: white;
  font-weight: 600;
}

/* New Product Card Design */
.product-card {
  border: 2px solid transparent;
  border-radius: 8px;
  overflow: hidden;
  background-color: #fff;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

.product-card.selected {
  border-color: #6c757d;
  /* box-shadow: 0 8px 20px rgba(108, 117, 125, 0.2); */
}

.product-card:hover {
  transform: translateY(-5px);
  /* box-shadow: 0 8px 20px rgba(0,0,0,0.1); */
}

.product-image-container {
  position: relative;
  width: 100%;
  aspect-ratio: 2 / 3;
  background-color: #f5f5f7;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.badge-new {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: white;
  color: black;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.select-checkbox {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 28px;
  height: 28px;
  background-color: rgba(0, 0, 0, 0.3);
  border: 2px solid white;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 0.2s ease-in-out;
  /* box-shadow: 0 1px 3px rgba(0,0,0,0.1); */
}

.product-card.selected .select-checkbox {
  background-color: #6c757d;
  border-color: #6c757d;
  /* box-shadow: 0 2px 5px rgba(108, 117, 125, 0.3); */
  animation: pop 0.3s ease-in-out;
}

.checkmark {
  color: white;
  font-size: 1rem;
  line-height: 1;
  transition: transform 0.2s;
  opacity: 1;
}

.product-card.selected .checkmark {
  transform: scale(1.2);
}

@keyframes pop {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

.card-body {
  padding: 1rem;
  text-align: left;
}

.card-category {
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1d1d1f;
  margin-bottom: 0.5rem;
}

.card-price {
  color: #1d1d1f;
  font-size: 1.1rem;
}

.floating-cart-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
}
</style>
