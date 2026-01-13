<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps<{
    stats: {
        total_credentials: number;
        issued: number;
        pending: number;
        revoked: number;
    };
    recentCredentials: Array<{
        id: number;
        title: string;
        description: string;
        student_name: string;
        issuer_name: string;
        status: string;
        issued_at: string | null;
        has_blockchain: boolean;
        verification_url: string;
    }>;
}>();

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        issued: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        revoked: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Verifier Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Verifier Dashboard
            </h2>
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

                <!-- Recent Issued Credentials -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b">
                        <h2 class="text-2xl font-bold">Recent Issued Credentials</h2>
                    </div>
                    <div class="p-6">
                        <div v-if="recentCredentials.length === 0" class="text-gray-500 text-center py-8">
                            No issued credentials to verify yet.
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Student
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Issued By
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Issued At
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Blockchain
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="credential in recentCredentials" :key="credential.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ credential.title }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ credential.description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ credential.student_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ credential.issuer_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ credential.issued_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                v-if="credential.has_blockchain"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                            >
                                                Anchored
                                            </span>
                                            <span
                                                v-else
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"
                                            >
                                                Not Anchored
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a
                                                :href="credential.verification_url"
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Verify
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Guide -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Verifier Guide</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• View all issued credentials in the system</li>
                        <li>• Click "Verify" to check credential authenticity and blockchain status</li>
                        <li>• Green "Anchored" status means the credential is secured on the blockchain</li>
                        <li>• Use verification links to share with third parties for validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
