<template>
  <div>
    <h2>Manajemen Kategori</h2>

    <!-- Form Tambah Kategori -->
    <form @submit.prevent="addCategory">
      <input v-model="name" type="text" placeholder="Nama kategori" required />
      <button type="submit">Tambah</button>
    </form>

    <hr />

    <!-- Daftar Kategori -->
    <ul>
      <li v-for="category in categories" :key="category.id">
        {{ category.name }}
        <button @click="editCategory(category)">Edit</button>
        <button @click="deleteCategory(category.id)">Hapus</button>
      </li>
    </ul>

    <!-- Form Edit -->
    <div v-if="editMode">
      <h3>Edit Kategori</h3>
      <form @submit.prevent="updateCategory">
        <input v-model="editName" type="text" required />
        <button type="submit">Update</button>
        <button type="button" @click="cancelEdit">Batal</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      categories: [],
      name: "",
      editMode: false,
      editId: null,
      editName: "",
    };
  },
  async mounted() {
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      const res = await api.get("/categories");
      this.categories = res.data;
    },
    async addCategory() {
      await api.post("/categories", { name: this.name });
      this.name = "";
      this.loadCategories();
    },
    editCategory(category) {
      this.editMode = true;
      this.editId = category.id;
      this.editName = category.name;
    },
    async updateCategory() {
      await api.put(`/categories/${this.editId}`, { name: this.editName });
      this.editMode = false;
      this.editId = null;
      this.editName = "";
      this.loadCategories();
    },
    cancelEdit() {
      this.editMode = false;
      this.editId = null;
      this.editName = "";
    },
    async deleteCategory(id) {
      await api.delete(`/categories/${id}`);
      this.loadCategories();
    },
  },
};
</script>
