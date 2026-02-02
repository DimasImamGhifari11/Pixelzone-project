<template>
  <div>
    <h2>Daftar Kategori</h2>
    <ul>
      <li v-for="category in categories" :key="category.id">
        {{ category.name }}
      </li>
    </ul>

    <h2 class="mt-5">Daftar Produk</h2>
    <ul>
      <li v-for="product in products" :key="product.id">
        {{ product.name }} - {{ product.price }} - {{ product.category.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);
const products = ref([]);

// Mengambil data kategori saat komponen dipasang
onMounted(() => {
  axios.get('http://127.0.0.1:8000/api/categories')
    .then(response => {
      categories.value = response.data.data; // Menyimpan data kategori
    })
    .catch(error => {
      console.error("Error fetching categories:", error);
    });

  // Mengambil data produk saat komponen dipasang
  axios.get('http://127.0.0.1:8000/api/products')
    .then(response => {
      products.value = response.data.data; // Menyimpan data produk
    })
    .catch(error => {
      console.error("Error fetching products:", error);
    });
});
</script>
