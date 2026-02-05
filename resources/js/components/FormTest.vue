<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Forms CRUD Test (Bypassing Auth)</h1>

    <div class="bg-gray-100 p-4 rounded mb-8">
      <h2 class="font-bold mb-2">{{ isEditing ? 'Edit Form' : 'Create New Form' }}</h2>
      <form @submit.prevent="submitForm" class="grid grid-cols-2 gap-4">
        
        <input v-model="form.title" placeholder="Title (e.g. SEO Audit)" class="border p-2 rounded" required />
        <input v-model="form.client_name" placeholder="Client Name" class="border p-2 rounded" required />
        <input v-model="form.date" type="date" class="border p-2 rounded" required />
        <input v-model="form.payment_method" placeholder="Payment Method" class="border p-2 rounded" />
        
        <input v-model.number="form.amount_in" type="number" step="0.01" placeholder="Amount In" class="border p-2 rounded" />
        <input v-model.number="form.fees" type="number" step="0.01" placeholder="Fees" class="border p-2 rounded" />
        <input v-model.number="form.amount_out" type="number" step="0.01" placeholder="Amount Out" class="border p-2 rounded" />
        
        <textarea v-model="form.description" placeholder="Description" class="border p-2 rounded col-span-2"></textarea>

        <div class="col-span-2 flex gap-2">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
            {{ isEditing ? 'Update' : 'Save' }}
          </button>
          <button type="button" v-if="isEditing" @click="resetForm" class="bg-gray-500 text-white px-4 py-2 rounded">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <h2 class="font-bold mb-2">Existing Forms</h2>
    <ul>
      <li v-for="item in forms" :key="item.id" class="border-b p-4 flex justify-between items-center bg-white">
        <div>
          <strong>{{ item.title }}</strong> <span class="text-gray-500">- {{ item.client_name }}</span>
          <br>
          <small class="text-green-600">In: ${{ item.amount_in }}</small> | 
          <small class="text-red-600">Out: ${{ item.amount_out }}</small>
        </div>
        <div>
          <button @click="editForm(item)" class="text-blue-500 mr-4">Edit</button>
          <button @click="deleteForm(item.id)" class="text-red-500">Delete</button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      forms: [],
      isEditing: false,
      currentId: null,
      form: {
        user_id: 1, // <--- BYPASS: Hardcoding the ID so Auth isn't needed
        title: '',
        client_name: '',
        date: new Date().toISOString().split('T')[0],
        payment_method: '',
        amount_in: 0,
        fees: 0,
        amount_out: 0,
        description: ''
      }
    };
  },
  mounted() {
    this.fetchForms();
  },
  methods: {
    async fetchForms() {
      try {
        const res = await axios.get('/api/forms');
        this.forms = res.data;
      } catch (e) {
        console.error("Error fetching:", e);
      }
    },
    async submitForm() {
      try {
        if (this.isEditing) {
          await axios.put(`/api/forms/${this.currentId}`, this.form);
        } else {
          await axios.post('/api/forms', this.form);
        }
        this.resetForm();
        this.fetchForms();
      } catch (e) {
        alert("Error saving form. Check console.");
        console.error(e);
      }
    },
    async deleteForm(id) {
      if (!confirm('Are you sure?')) return;
      try {
        await axios.delete(`/api/forms/${id}`);
        this.fetchForms();
      } catch (e) {
        console.error(e);
      }
    },
    editForm(item) {
      this.isEditing = true;
      this.currentId = item.id;
      this.form = { ...item }; // Copy data to form
      this.form.user_id = 1;   // Ensure user_id stays attached
    },
    resetForm() {
      this.isEditing = false;
      this.currentId = null;
      this.form = {
        user_id: 1,
        title: '',
        client_name: '',
        date: new Date().toISOString().split('T')[0],
        payment_method: '',
        amount_in: 0,
        fees: 0,
        amount_out: 0,
        description: ''
      };
    }
  }
};
</script>