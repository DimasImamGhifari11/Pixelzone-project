<template>
  <div class="container mt-5">
    <h2 class="section-title text-center">Daftar Kategori</h2>
    <button class="btn btn-primary mb-3" @click="goToCreateCategory">Tambah Kategori</button>
    <div class="list-group">
      <div v-for="category in categories" :key="category.id" class="list-group-item d-flex justify-content-between align-items-center">
        <span class="category-name">{{ category.name }}</span>
        <div>
          <button @click="goToEditCategory(category.id)" class="btn btn-warning btn-sm">Edit</button>
          <button @click="deleteCategory(category.id)" class="btn btn-danger btn-sm">Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const categories = ref([]);

// Ambil data kategori
const fetchCategories = () => {
  api.get('/categories')
    .then(response => {
      categories.value = response.data.data;
    })
    .catch(error => {
      console.error("Error fetching categories:", error);
    });
};

// Fungsi untuk menghapus kategori
const deleteCategory = (id) => {
  api.delete(`/categories/${id}`)
    .then(() => {
      fetchCategories();
    })
    .catch(error => {
      console.error("Error deleting category:", error);
    });
};

// Arahkan ke halaman Create Kategori
const goToCreateCategory = () => {
  router.push('/create-category');
};

// Arahkan ke halaman Edit Kategori
const goToEditCategory = (id) => {
  router.push(`/edit-category/${id}`);
};

// Ambil data kategori saat komponen dipasang
onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
/* Styling untuk halaman kategori */
.list-group-item {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px 20px;
  margin-bottom: 10px;
}

.category-name {
  font-weight: 600;
}

.list-group-item:hover {
  background-color: #f1f1f1;
}

/* Styling tombol */
.btn-sm {
  font-size: 0.875rem;
  padding: 0.375rem 0.75rem;
  margin-left: 10px;
}
</style>
