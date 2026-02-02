<template>
  <div class="container mt-5">
    <h2>Edit Produk</h2>
    <form @submit.prevent="updateProduk">
      <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input
          v-model="produk.name"
          type="text"
          class="form-control"
          id="name"
          placeholder="Masukkan Nama Produk"
          required
        />
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea
          v-model="produk.description"
          class="form-control"
          id="description"
          rows="3"
          placeholder="Masukkan Deskripsi Produk"
          required
        ></textarea>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input
          v-model="produk.price"
          type="number"
          class="form-control"
          id="price"
          placeholder="Masukkan Harga Produk"
          required
          @blur="validatePrice" 
        />
        <div v-if="validationErrors.price" class="text-danger mt-1">{{ validationErrors.price[0] }}</div>
      </div>
      <div class="mb-3">
        <label for="category" class="form-label">Kategori</label>
        <select v-model="produk.category_id" class="form-control" id="category" required>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../axios'; // Use the configured axios instance
import { store } from '../../store'; // Import the store for notifications

const route = useRoute();
const router = useRouter();

const produk = ref({
  name: '',
  description: '',
  price: '',
  category_id: '',
});

const categories = ref([]);
const validationErrors = ref({}); // For inline validation

// Fungsi validasi harga yang bisa dipanggil ulang
const validatePrice = () => {
  validationErrors.value.price = null; // Reset error
  const price = parseFloat(produk.value.price);
  if (isNaN(price)) {
    validationErrors.value.price = ['Harga harus berupa angka.'];
    return false;
  }
  if (price < 1) {
    validationErrors.value.price = ['Harga tidak boleh kurang dari 1.'];
    return false;
  }
  if (price > 5000000) {
    validationErrors.value.price = ['Harga tidak boleh melebihi 5.000.000.'];
    return false;
  }
  return true; // Valid
};

// Mengambil data kategori
const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch categories:', error);
    store.showNotification('Gagal memuat kategori.', 'error');
  }
};

// Mengambil data produk untuk edit
const fetchProduk = async (id) => {
  try {
    const response = await api.get(`/products/${id}`);
    produk.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch product:', error);
    store.showNotification('Gagal memuat data produk.', 'error');
    router.push('/products'); // Redirect if product not found or error
  }
};

// Mengupdate produk
const updateProduk = async () => {
  // Panggil validasi sebelum submit
  if (!validatePrice()) {
    return; // Hentikan jika validasi gagal
  }

  try {
    await api.put(`/products/${route.params.id}`, produk.value);
    store.showNotification('Produk berhasil diperbarui!', 'success');
    router.push('/products'); // Arahkan ke halaman produk setelah berhasil
  } catch (error) {
    console.error('Failed to update product:', error);
    if (error.response && error.response.status === 422) {
      // Handle validation errors from the backend
      validationErrors.value = error.response.data.errors;
    } else {
      store.showNotification('Gagal memperbarui produk.', 'error');
    }
  }
};

// Ambil data produk dan kategori saat halaman dimuat
onMounted(() => {
  fetchCategories();
  fetchProduk(route.params.id); // Ambil data produk berdasarkan id
});
</script>
