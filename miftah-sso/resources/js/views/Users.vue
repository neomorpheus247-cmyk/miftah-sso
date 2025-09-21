<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 mb-6">User Management</h1>

      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="p-4">
          <div class="flex justify-between items-center mb-4">
            <input
              v-model="search"
              type="text"
              placeholder="Search users..."
              class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in filteredUsers" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <select
                    v-model="user.role"
                    @change="updateUserRole(user)"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  >
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                  </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button
                    @click="deleteUser(user.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const search = ref('');

const filteredUsers = computed(() => {
  const searchTerm = search.value.toLowerCase();
  return users.value.filter(user => 
    user.name.toLowerCase().includes(searchTerm) ||
    user.email.toLowerCase().includes(searchTerm)
  );
});

onMounted(fetchUsers);

async function fetchUsers() {
  try {
    const { data } = await axios.get('/api/users');
    users.value = data.map(user => ({
      ...user,
      role: user.roles[0]?.name || 'student'
    }));
  } catch (error) {
    console.error('Failed to fetch users:', error);
  }
}

async function updateUserRole(user) {
  try {
    await axios.put(`/api/users/${user.id}/role`, {
      role: user.role
    });
  } catch (error) {
    console.error('Failed to update user role:', error);
    // Reset role on error
    user.role = user.roles[0]?.name || 'student';
  }
}

async function deleteUser(id) {
  if (!confirm('Are you sure you want to delete this user?')) return;
  
  try {
    await axios.delete(`/api/users/${id}`);
    users.value = users.value.filter(user => user.id !== id);
  } catch (error) {
    console.error('Failed to delete user:', error);
  }
}
</script>