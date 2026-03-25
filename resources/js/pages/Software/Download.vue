<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const version = ref('');
const hwVersion = ref('');
const response = ref(null);
const loading = ref(false);

const checkSoftware = async () => {
    loading.value = true;
    try {
        const res = await axios.post('/api/check-version', {
            version: version.value,
            hwVersion: hwVersion.value
        });
        response.value = res.data;
    } catch (e) {
        alert('Error checking version');
    } finally {
        loading.value = false;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },

    {
        title: 'Software Download',
        href: '/software-download',
    },
];
</script>

<template>

    <Head title="Dashboard" />



    <AppLayout :breadcrumbs="breadcrumbs">


        <div class="max-w-xl mx-auto py-12 px-4">
            <h1 class="text-3xl font-bold mb-4">Update the software for your<br />
                CarPlay / Android Auto MMI

            </h1>

            <div class="mb-4 text-gray-400 text-sm">
                <div><strong>Demo Software Version: : </strong> v3.1.1.2.mmi.c</div>
                <div><strong>Demo Hardware Version: </strong> CPAA_2023.01.01</div>
            </div>

            <form @submit.prevent="checkSoftware" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Software Version</label>
                    <input v-model="version" type="text" placeholder="e.g. v3.1.1.2.mmi.c" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Hardware Version</label>
                    <input v-model="hwVersion" type="text" placeholder="e.g. CPAA_1234.56.78" class="w-full border rounded p-2" required>
                </div>
                <button :disabled="loading" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 disabled:opacity-50">
                    {{ loading ? 'Checking...' : 'Check for Updates' }}
                </button>
            </form>

            <div v-if="response" class="mt-8 p-6 border rounded-lg shadow-sm" :class="response.versionExist ? 'bg-gray-800' : 'bg-gray-800'">
                <p class="text-lg">{{ response.msg }}</p>
                <div v-if="response.versionExist" class="mt-4 flex flex-wrap gap-4">
                    <a v-if="response.link" :href="response.link" target="_blank" class="text-blue-600 underline font-bold">Download Update</a>
                    <a v-if="response.st" :href="response.st" target="_blank" class="text-blue-600 underline font-bold">Standard File</a>
                    <a v-if="response.gd" :href="response.gd" target="_blank" class="text-blue-600 underline font-bold">Mirror File</a>
                </div>
            </div>
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