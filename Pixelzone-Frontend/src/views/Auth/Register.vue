<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow-lg">
          <h2 class="text-center mb-4">Daftar</h2>
          <form @submit.prevent="handleRegister">
            <div class="mb-3">
              <label for="name" class="form-label">Nama</label>
              <input type="text" v-model="name" class="form-control" id="name" required />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" v-model="email" class="form-control" id="email" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Kata Sandi</label>
              <input type="password" v-model="password" class="form-control" id="password" required />
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
              <input type="password" v-model="password_confirmation" class="form-control" id="password_confirmation" required />
            </div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
          </form>
          <div class="text-center mt-3">
            <p>Sudah punya akun? <router-link to="/login">Masuk di sini</router-link></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const error = ref(null);

const handleRegister = async () => {
  error.value = null;
  try {
    await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    router.push('/login');
  } catch (err) {
    error.value = 'Pendaftaran gagal. Silakan periksa kembali data Anda.';
    console.error('Registration failed:', err);
  }
};
</script>
