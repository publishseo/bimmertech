<script setup lang="ts">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}

const { users } = defineProps<Props>();

const editingId = ref<number | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'client',
});

const startEdit = (user: User) => {
    editingId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.password_confirmation = '';
    form.role = user.role;
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
    form.role = 'client';
};

const submit = () => {
    if (editingId.value) {
        form.put(`/admin/users/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                form.role = 'client';
                editingId.value = null;
            },
        });
    } else {
        form.post('/admin/users', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                form.role = 'client';
            },
        });
    }
};

const remove = (id: number) => {
    if (confirm('Delete this user?')) {
        form.delete(`/admin/users/${id}`, {
            preserveScroll: true,
        });
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

const { flash = {} } = usePage<{ flash?: { success?: string } }>();
</script>

<template>
    <Head title="User Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-6">Manage Users</h2>

            <form @submit.prevent="submit" class="grid grid-cols-2 gap-4 mb-10 bg-gray-900 p-4 border rounded">
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Name</label>
                    <input type="text" v-model="form.name" placeholder="Full Name" class="w-full border rounded p-2 text-white bg-gray-800">
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" v-model="form.email" placeholder="Email Address" class="w-full border rounded p-2 text-white bg-gray-800">
                    <InputError :message="form.errors.email" />
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Password</label>
                    <input type="password" v-model="form.password" :placeholder="editingId ? 'Leave blank to keep current' : 'Password'" class="w-full border rounded p-2 text-white bg-gray-800">
                    <InputError :message="form.errors.password" />
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Confirm Password</label>
                    <input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" class="w-full border rounded p-2 text-white bg-gray-800">
                </div>
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Role</label>
                    <select v-model="form.role" class="w-full border rounded p-2 text-white bg-gray-800">
                        <option value="client">Client</option>
                        <option value="admin">Admin</option>
                    </select>
                    <InputError :message="form.errors.role" />
                </div>
                <div class="flex gap-2 col-span-2">
                    <button type="submit" class="bg-green-600 text-white p-2 rounded flex-1" :disabled="form.processing">
                        {{ editingId ? 'Update User' : 'Add User' }}
                    </button>
                    <button v-if="editingId" type="button" @click="cancelEdit" class="bg-gray-600 text-white p-2 rounded">Cancel</button>
                </div>
            </form>

            <div v-if="flash?.success" class="mb-4 p-3 bg-green-800 text-green-100 rounded">
                {{ flash.success }}
            </div>

            <table class="w-full border text-left">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Role</th>
                        <th class="p-2 border">Created At</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="p-2 border">{{ user.name }}</td>
                        <td class="p-2 border">{{ user.email }}</td>
                        <td class="p-2 border">
                            <span :class="user.role === 'admin' ? 'bg-red-900 text-red-100' : 'bg-blue-900 text-blue-100'" class="px-2 py-1 rounded text-xs">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="p-2 border">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="p-2 border">
                            <button @click="startEdit(user)" class="text-blue-600 mr-2">Edit</button>
                            <button @click="remove(user.id)" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="mt-6 flex justify-center gap-2">
                <template v-for="link in users.links" :key="link.label">
                    <a
                        v-if="link.url"
                        :href="link.url"
                        :class="link.active ? 'bg-blue-600 text-white' : 'bg-gray-700 hover:bg-gray-600'"
                        class="px-3 py-2 rounded text-sm"
                        v-html="link.label"
                    ></a>
                    <span v-else class="px-3 py-2 rounded text-sm bg-gray-800 text-gray-400" v-html="link.label"></span>
                </template>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
input[type="text"],
input[type="email"],
input[type="password"],
select {
    background-color: #1f2937;
    color: #f9fafb;
    border: 1px solid #374151;
    border-radius: 0.375rem;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

select option {
    background-color: #1f2937;
    color: #f9fafb;
}
</style>
