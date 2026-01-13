<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps<{
    credentials: Array<any>;
    evidence: Array<any>;
    stats: {
        total_credentials: number;
        issued: number;
        pending: number;
        revoked: number;
    };
}>();

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        issued: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        revoked: 'bg-red-100 text-red-800',
        pinned: 'bg-green-100 text-green-800',
        pinning: 'bg-blue-100 text-blue-800',
        uploaded: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="My Portfolio" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Portfolio
                </h2>
                <Link :href="route('evidence.create')" as="a">
                    <PrimaryButton>
                        Upload Evidence
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-gray-500 text-sm">Total Credentials</div>
                        <div class="text-3xl font-bold">{{ stats.total_credentials }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-gray-500 text-sm">Issued</div>
                        <div class="text-3xl font-bold text-green-600">{{ stats.issued }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-gray-500 text-sm">Pending</div>
                        <div class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-gray-500 text-sm">Revoked</div>
                        <div class="text-3xl font-bold text-red-600">{{ stats.revoked }}</div>
                    </div>
                </div>

                <!-- Credentials List -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
                    <div class="p-6 border-b">
                        <h2 class="text-2xl font-bold">My Credentials</h2>
                    </div>
                    <div class="p-6">
                        <div v-if="credentials.length === 0" class="text-gray-500 text-center py-8">
                            No credentials yet. Your achievements will appear here once issued by faculty.
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="credential in credentials"
                                :key="credential.id"
                                class="border rounded-lg p-4 hover:shadow-md transition"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold">{{ credential.title }}</h3>
                                        <p class="text-gray-600 text-sm mt-1">{{ credential.description }}</p>
                                        <div class="mt-2 text-sm text-gray-500">
                                            Issued by: <span class="font-medium">{{ credential.issuer_name }}</span> on {{ credential.issued_at }}
                                        </div>
                                        <div v-if="credential.anchor_hash" class="mt-1 text-xs text-gray-400">
                                            Blockchain: {{ credential.anchor_hash.substring(0, 20) }}...
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <span
                                            :class="getStatusColor(credential.status)"
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                        >
                                            {{ credential.status.toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="credential.status === 'issued'" class="mt-4 flex space-x-2">
                                    <Link
                                        :href="credential.verification_url"
                                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                                    >
                                        View Verification
                                    </Link>
                                    <a
                                        :href="`/credentials/${credential.id}/qr`"
                                        target="_blank"
                                        class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-blue-900"
                                    >
                                        Download QR Code
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evidence Files -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">My Evidence Files</h2>
                        <Link :href="route('evidence.create')" as="a">
                            <PrimaryButton class="bg-green-600 hover:bg-green-700">
                                Upload New Evidence
                            </PrimaryButton>
                        </Link>
                    </div>
                    <div class="p-6">
                        <div v-if="evidence.length === 0" class="text-gray-500 text-center py-8">
                            <p class="mb-4">No evidence files uploaded yet.</p>
                            <Link :href="route('evidence.create')" as="a">
                                <PrimaryButton>
                                    Upload Your First Evidence
                                </PrimaryButton>
                            </Link>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Filename</th>
                                        <th class="text-left py-2">Uploaded</th>
                                        <th class="text-left py-2">Status</th>
                                        <th class="text-left py-2">IPFS CID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in evidence" :key="item.id" class="border-b">
                                        <td class="py-3">{{ item.filename }}</td>
                                        <td class="py-3">{{ item.uploaded_at }}</td>
                                        <td class="py-3">
                                            <span :class="getStatusColor(item.status)" class="px-2 py-1 rounded text-xs">
                                                {{ item.status }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-xs text-gray-500">{{ item.cid || 'Pending...' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
