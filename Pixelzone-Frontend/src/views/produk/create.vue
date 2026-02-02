<template>
  <div class="container mt-5">
    <h2>Tambah Produk</h2>
    <form @submit.prevent="storeProduk">
      <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" v-model="produk.name" class="form-control" required minlength="3" maxlength="50" />
        <div v-if="validationErrors.name" class="text-danger mt-1">{{ validationErrors.name[0] }}</div>
      </div>
      <div class="mb-3">
        <label>Deskripsi</label>
        <textarea v-model="produk.description" class="form-control" required></textarea>
        <div v-if="validationErrors.description" class="text-danger mt-1">{{ validationErrors.description[0] }}</div>
      </div>
      <div class="mb-3">
        <label>Harga</label>
        <input type="number" v-model="produk.price" class="form-control" required min="1" max="5000000" />
        <div v-if="validationErrors.price" class="text-danger mt-1">{{ validationErrors.price[0] }}</div>
      </div>
      <div class="mb-3">
        <label>Link Gambar</label>
        <div class="input-group">
          <input
            type="url"
            v-model="produk.image"
            class="form-control"
            placeholder="https://example.com/image.jpg"
            required
          />
          <button type="button" class="btn btn-outline-secondary" @click="pasteLink">Paste Link</button>
        </div>
        <div v-if="validationErrors.image" class="text-danger mt-1">{{ validationErrors.image[0] }}</div>
      </div>
      <div class="mb-3">
        <label>Kategori</label>
        <select v-model="produk.category_id" class="form-control" required>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <div v-if="validationErrors.category_id" class="text-danger mt-1">{{ validationErrors.category_id[0] }}</div>
      </div>
      <div v-if="saveError" class="alert alert-danger">
        {{ saveError }}
      </div>
      <button type="submit" class="btn btn-primary">Simpan</button>
      <router-link to="/" class="btn btn-secondary ms-2">Kembali</router-link>
    </form>

    <!-- Tombol Tambah Kategori -->
    <div class="mt-4">
      <router-link to="/create-category" class="btn btn-success">+ Tambah Kategori</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const produk = ref({
  name: '',
  description: '',
  price: '',
  category_id: null,
  image: '',
});

const categories = ref([]);
const fetchError = ref(null);
const validationErrors = ref({});
const saveError = ref(null);
const debugTokenStatus = ref('Mengecek status token...');

onMounted(() => {
  const token = sessionStorage.getItem('token'); // Corrected: read from sessionStorage
  if (token) {
    debugTokenStatus.value = 'Token DITEMUKAN. Mencoba mengambil kategori...';
  } else {
    debugTokenStatus.value = 'Token TIDAK DITEMUKAN. Pastikan Anda sudah login.';
    fetchError.value = "Token otentikasi tidak ditemukan.";
    return;
  }

  // api.get already has an interceptor that adds the token.
  // The explicit header is not necessary if the global axios instance is used correctly.
  api.get('/categories').then(response => {
    console.log('Server response for categories:', response.data);
    categories.value = response.data.data;
    fetchError.value = null;
    debugTokenStatus.value = 'Token DITEMUKAN. Kategori berhasil diambil!';
  })
  .catch(error => {
      console.error("Failed to fetch categories for product form:", error);
      debugTokenStatus.value = 'Token DITEMUKAN. Pengambilan kategori GAGAL!';
      if (error.response) {
        fetchError.value = `Error ${error.response.status}: ${JSON.stringify(error.response.data)}`;
      } else {
        fetchError.value = `Network Error: ${error.message}`;
      }
    });
});

// Fungsi untuk menempelkan link dari clipboard
const pasteLink = async () => {
  try {
    const text = await navigator.clipboard.readText(); // Membaca teks dari clipboard
    produk.value.image = text; // Menyimpan teks ke v-model image
  } catch (error) {
    console.error("Gagal menempelkan link dari clipboard:", error);
  }
};

const storeProduk = () => {
  console.log('Attempting to POST to /products with data:', produk.value);
  validationErrors.value = {};
  saveError.value = null;

  if (isNaN(parseFloat(produk.value.price)) || !isFinite(produk.value.price)) {
    validationErrors.value.price = ['Harga harus berupa angka.'];
    saveError.value = 'Data yang dimasukkan tidak valid. Mohon periksa kembali isian Anda.';
    return;
  }

  if (produk.value.price < 1) {
    validationErrors.value.price = ['Value must be greater than or equal to 1'];
    saveError.value = 'Data yang dimasukkan tidak valid. Mohon periksa kembali isian Anda.';
    return;
  }

  if (produk.value.price > 5000000) {
    validationErrors.value.price = ['Value must be less than or equal to 5000000'];
    saveError.value = 'Data yang dimasukkan tidak valid. Mohon periksa kembali isian Anda.';
    return;
  }

  if (produk.value.name.length < 3 || produk.value.name.length > 50) {
    validationErrors.value.name = ['Nama produk harus memiliki minimal 3 karakter dan maksimal 50 karakter.'];
    saveError.value = 'Data yang dimasukkan tidak valid. Mohon periksa kembali isian Anda.';
    return;
  }

  api.post('/products', produk.value).then(() => {
    router.push('/');
  }).catch(error => {
    if (error.response) {
      if (error.response.status === 422) {
        validationErrors.value = error.response.data.errors;
        saveError.value = 'Data yang dimasukkan tidak valid. Mohon periksa kembali isian Anda.';
      } else {
        saveError.value = `Terjadi error tak terduga dari server (Status: ${error.response.status}).`;
      }
    } else {
      saveError.value = `Terjadi error jaringan: ${error.message}`;
    }
    console.error("Error saving product:", error);
  });
};
</script>
