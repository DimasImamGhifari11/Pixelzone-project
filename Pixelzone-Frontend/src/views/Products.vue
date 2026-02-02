<template>
  <div>
    <h2>Manajemen Produk</h2>

    <!-- Form Tambah Produk -->
    <form @submit.prevent="addProduct">
      <input v-model="name" type="text" placeholder="Nama produk" required />
      <textarea v-model="description" placeholder="Deskripsi"></textarea>
      <input v-model.number="price" type="number" placeholder="Harga" required />
      <select v-model="category_id" required>
        <option disabled value="">Pilih kategori</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>
      <input type="file" @change="handleFileUpload" />
      <button type="submit">Tambah</button>
    </form>

    <hr />

    <!-- Daftar Produk -->
    <ul>
      <li v-for="product in products" :key="product.id">
        <strong>{{ product.name }}</strong> - {{ product.price }}
        <br />
        {{ product.description }}
        <br />
        Kategori: {{ product.category?.name }}
        <br />
        <img v-if="product.image" :src="`http://localhost:8000/storage/${product.image}`" width="100" />
        <br />
        <button @click="editProduct(product)">Edit</button>
        <button @click="deleteProduct(product.id)">Hapus</button>
      </li>
    </ul>

    <!-- Form Edit Produk -->
    <div v-if="editMode">
      <h3>Edit Produk</h3>
      <form @submit.prevent="updateProduct">
        <input v-model="editName" type="text" required />
        <textarea v-model="editDescription"></textarea>
        <input v-model.number="editPrice" type="number" required />
        <select v-model="editCategoryId" required>
          <option disabled value="">Pilih kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <input type="file" @change="handleEditFileUpload" />
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
      products: [],
      categories: [],
      name: "",
      description: "",
      price: "",
      category_id: "",
      image: null,

      editMode: false,
      editId: null,
      editName: "",
      editDescription: "",
      editPrice: "",
      editCategoryId: "",
      editImage: null,
    };
  },
  async mounted() {
    this.loadProducts();
    this.loadCategories();
  },
  methods: {
    async loadProducts() {
      const res = await api.get("/products");
      this.products = res.data;
    },
    async loadCategories() {
      const res = await api.get("/categories");
      this.categories = res.data;
    },
    handleFileUpload(e) {
      this.image = e.target.files[0];
    },
    async addProduct() {
      const formData = new FormData();
      formData.append("name", this.name);
      formData.append("description", this.description);
      formData.append("price", this.price);
      formData.append("category_id", this.category_id);
      if (this.image) {
        formData.append("image", this.image);
      }
      await api.post("/products", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      this.name = "";
      this.description = "";
      this.price = "";
      this.category_id = "";
      this.image = null;
      this.loadProducts();
    },
    editProduct(product) {
      this.editMode = true;
      this.editId = product.id;
      this.editName = product.name;
      this.editDescription = product.description;
      this.editPrice = product.price;
      this.editCategoryId = product.category_id;
    },
    handleEditFileUpload(e) {
      this.editImage = e.target.files[0];
    },
    async updateProduct() {
      const formData = new FormData();
      formData.append("name", this.editName);
      formData.append("description", this.editDescription);
      formData.append("price", this.editPrice);
      formData.append("category_id", this.editCategoryId);
      if (this.editImage) {
        formData.append("image", this.editImage);
      }
      await api.post(`/products/${this.editId}?_method=PUT`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      this.editMode = false;
      this.editId = null;
      this.loadProducts();
    },
    cancelEdit() {
      this.editMode = false;
      this.editId = null;
    },
    async deleteProduct(id) {
      await api.delete(`/products/${id}`);
      this.loadProducts();
    },
  },
};
</script>
