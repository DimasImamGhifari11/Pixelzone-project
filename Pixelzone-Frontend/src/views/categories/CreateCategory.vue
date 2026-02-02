<template>
  <div class="container mt-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2>Tambah Kategori</h2>
      <p class="lead">Masukkan nama kategori baru di bawah ini</p>
    </div>

    <!-- Form Tambah Kategori -->
    <form @submit.prevent="storeCategory" class="shadow-lg p-4 rounded-lg bg-white">
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
        <div v-if="validationErrors.name" class="text-danger mt-1">
          {{ validationErrors.name[0] }}
        </div>
        <div v-if="validationErrors.name" class="text-danger mt-1">
          {{ validationErrors.name[0] }}
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <router-link to="/categories" class="btn btn-secondary">Kembali</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const category = ref({
  name: '',
});
const validationErrors = ref({});

const storeCategory = () => {
  validationErrors.value = {}; // Bersihkan error sebelumnya
  api.post('/categories', category.value)
    .then(response => {
      router.push('/categories');
    })
    .catch(error => {
      if (error.response && error.response.status === 422) {
        validationErrors.value = error.response.data.errors;
      } else {
        console.error("An unexpected error occurred:", error);
      }
    });
};
</script>

<style scoped>
/* Styling untuk form tambah kategori */
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 15px;
}

.form-control {
  margin-bottom: 15px;
}

h2 {
  font-weight: bold;
  color: #333;
}

.lead {
  color: #666;
}

.btn-primary {
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

/* Add responsive design for small screens */
@media (max-width: 767px) {
  .form-control {
    font-size: 14px;
  }

  .btn-primary, .btn-secondary {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>
