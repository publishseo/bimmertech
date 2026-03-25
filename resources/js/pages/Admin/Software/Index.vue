<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem, type SoftwareVersion } from '@/types';
import { Head } from '@inertiajs/vue3';


const props = defineProps<{ versions: SoftwareVersion[] }>();

const editingId = ref<number | null>(null);

const form = useForm({
    name: '',
    system_version: '',
    system_version_alt: '',
    link: '',
    st: '',
    gd: '',
    latest: false
});

const startEdit = (version) => {
    editingId.value = version.id;
    form.name = version.name;
    form.system_version = version.system_version;
    form.system_version_alt = version.system_version_alt;
    form.link = version.link || '';
    form.st = version.st || '';
    form.gd = version.gd || '';
    form.latest = Boolean(version.latest);
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
};

const submit = () => {
    if (editingId.value) {
        form.put(`/admin/software-versions/${editingId.value}`, {
            onSuccess: () => {
                form.reset();
                editingId.value = null;
            }
        });
    } else {
        form.post('/admin/software-versions', {
            onSuccess: () => form.reset()
        });
    }
};

const remove = (id) => {
    if (confirm('Delete this version?')) {
        form.delete(`/admin/software-versions/${id}`);
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Software Management',
        href: '/admin/software-versions',
    },
];
</script>

<template>

    <Head title="Software Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-6">Manage Software</h2>

            <form @submit.prevent="submit" class="grid grid-cols-2 gap-4 mb-10 bg-gray-900 p-4 border rounded">
                <div class="grid gap-2">
                    <input type="text" v-model="form.name" placeholder="Name (e.g. MMI Prime CIC)" class="w-full border rounded p-2">
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <input type="text" v-model="form.system_version" placeholder="System Version" class="w-full border rounded p-2">
                    <InputError :message="form.errors.system_version" />
                </div>
                <div class="grid gap-2">
                    <input type="text" v-model="form.system_version_alt" placeholder="Alt Version (no 'v')" class="w-full border rounded p-2">
                    <InputError :message="form.errors.system_version_alt" />
                </div>
                <div class="grid gap-2">
                    <input type="text" v-model="form.link" placeholder="Main Link" class="w-full border rounded p-2">
                    <InputError :message="form.errors.link" />
                </div>
                <div class="grid gap-2">
                    <input type="text" v-model="form.st" placeholder="ST Link" class="w-full border rounded p-2">
                    <InputError :message="form.errors.st" />
                </div>
                <div class="grid gap-2">
                    <input type="text" v-model="form.gd" placeholder="GD Link" class="w-full border rounded p-2">
                    <InputError :message="form.errors.gd" />
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.latest"> Is Latest Version?
                </label>
                <div class="flex gap-2 col-span-2">
                    <button type="submit" class="bg-green-600 text-white p-2 rounded flex-1" :disabled="form.processing">
                        {{ editingId ? 'Update Version' : 'Add Version' }}
                    </button>
                    <button v-if="editingId" type="button" @click="cancelEdit" class="bg-gray-600 text-white p-2 rounded">Cancel</button>
                </div>
            </form>

            <table class="w-full border text-left">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Version</th>
                        <th class="p-2 border">Latest</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="v in versions" :key="v.id">
                        <td class="p-2 border">{{ v.name }}</td>
                        <td class="p-2 border">{{ v.system_version }}</td>
                        <td class="p-2 border">{{ v.latest ? '✅' : '❌' }}</td>
                        <td class="p-2 border">
                            <button @click="startEdit(v)" class="text-blue-600 mr-2">Edit</button>
                            <button @click="remove(v.id)" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>


<style scoped>
/* Dark theme input fields */
input[type="text"] {
    background-color: #1f2937;
    /* dark gray background */
    color: #f9fafb;
    /* light text */
    border: 1px solid #374151;
    /* slightly lighter border */
    padding: 0.5rem;
    /* match your p-2 spacing */
    border-radius: 0.375rem;
    /* match rounded corners */
    caret-color: #f9fafb;
    /* cursor color */
}

input[type="text"]::placeholder {
    color: #9ca3af;
    /* placeholder gray for dark theme */
}

/* Optional: focus style */
input[type="text"]:focus {
    outline: none;
    border-color: #3b82f6;
    /* blue highlight on focus */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}
</style>