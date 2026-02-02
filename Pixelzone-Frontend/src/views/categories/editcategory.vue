<template>
  <div class="container mt-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2>Edit Kategori</h2>
      <p class="lead">Perbarui nama kategori di bawah ini</p>
    </div>

    <!-- Form Edit Kategori -->
    <form @submit.prevent="updateCategory" class="shadow-lg p-4 rounded-lg bg-white">
      <div class="mb-3">
        <label for="name" class="form-label">Nama Kategori</label>
        <input
          id="name"
          type="text"
          v-model="category.name"
          class="form-control"
          placeholder="Masukkan nama kategori"
          required
        />
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-success">Update</button>
        <router-link to="/categories" class="btn btn-secondary">Kembali</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const category = ref({
  name: '',
});

onMounted(() => {
  // Mengambil kategori berdasarkan ID
  axios.get(`http://127.0.0.1:8000/api/categories/${route.params.id}`)
    .then(response => {
      category.value = response.data.data;  // Menyimpan data kategori yang akan diedit
    })
    .catch(error => {
      console.error("Error fetching category for editing:", error);
    });
});

/*************  ✨ Windsurf Command ⭐  *************/
/*******  f78a7a80-576a-4dae-aafa-9e39c0c1f056  *******/
const updateCategory = () => {
  // Mengirim data kategori yang sudah diperbarui
  axios.put(`http://127.0.0.1:8000/api/categories/${route.params.id}`, category.value)
    .then(response => {
      router.push('/categories');  // Redirect setelah sukses mengupdate kategori
    })
    .catch(error => {
      console.error("Error updating category:", error);
    });
};
</script>

<style scoped>
/* Styling untuk form edit kategori */
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 15px;
}

h2 {
  font-weight: bold;
  color: #333;
}

.lead {
  color: #666;
}

.form-control {
  margin-bottom: 15px;
}

.btn-success {
  width: 48%;
}

.btn-secondary {
  width: 48%;
}

.shadow-lg {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.bg-white {
  background-color: #ffffff;
}

.text-center {
  margin-bottom: 20px;
}

.d-flex {
  justify-content: space-between;
}

 /* Add responsive design for small screens */
@media (max-width: 767px) {
  .form-control {
    font-size: 14px;
  }

  .btn-success, .btn-secondary {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>
