<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

defineProps<{
    stats: {
        total_issued: number;
        pending: number;
        issued: number;
        revoked: number;
    };
    recentCredentials: Array<{
        id: number;
        title: string;
        student_name: string;
        status: string;
        created_at: string;
        issued_at: string | null;
    }>;
    students: Array<{
        id: number;
        name: string;
        email: string;
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

const showRevokeModal = ref(false);
const credentialToRevoke = ref<number | null>(null);
const revokeForm = useForm({
    reason: '',
});

const openRevokeModal = (credentialId: number) => {
    credentialToRevoke.value = credentialId;
    showRevokeModal.value = true;
    revokeForm.reason = '';
};

const closeRevokeModal = () => {
    showRevokeModal.value = false;
    credentialToRevoke.value = null;
    revokeForm.reason = '';
};

const submitRevoke = () => {
    if (!credentialToRevoke.value) return;

    revokeForm.patch(route('credentials.revoke', credentialToRevoke.value), {
        onSuccess: () => {
            closeRevokeModal();
        },
    });
};
</script>

<template>
    <Head title="Issuer Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Issuer Dashboard
                </h2>
                <Link :href="route('credentials.create')" as="a">
                    <PrimaryButton>
                        Create New Credential
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
                        <div class="text-3xl font-bold">{{ stats.total_issued }}</div>
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

                <!-- Recent Credentials -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Recent Credentials</h2>
                    </div>
                    <div class="p-6">
                        <div v-if="recentCredentials.length === 0" class="text-gray-500 text-center py-8">
                            <p class="mb-4">No credentials created yet.</p>
                            <Link :href="route('credentials.create')" as="a">
                                <PrimaryButton>
                                    Create Your First Credential
                                </PrimaryButton>
                            </Link>
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
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Issued
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="credential in recentCredentials" :key="credential.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ credential.title }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ credential.student_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="getStatusColor(credential.status)"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ credential.status.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ credential.created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ credential.issued_at || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                v-if="credential.status === 'pending'"
                                                :href="route('credentials.issue', credential.id)"
                                                method="patch"
                                                as="button"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Issue
                                            </Link>
                                            <button
                                                v-if="credential.status === 'issued'"
                                                @click="openRevokeModal(credential.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Revoke
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Help -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Quick Guide</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• <strong>Create Credential:</strong> Click the button above to create a new credential for a student</li>
                        <li>• <strong>Issue:</strong> Click "Issue" to approve a pending credential and anchor it to the blockchain</li>
                        <li>• <strong>Revoke:</strong> Click "Revoke" to invalidate an issued credential</li>
                        <li>• Students can view their credentials and generate QR codes for verification</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Revoke Modal -->
        <div v-if="showRevokeModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Revoke Credential</h3>
                <p class="text-sm text-gray-500 mb-4">Please provide a reason for revoking this credential. This action cannot be undone.</p>

                <form @submit.prevent="submitRevoke">
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <textarea
                            id="reason"
                            v-model="revokeForm.reason"
                            rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter reason for revocation..."
                            required
                        ></textarea>
                        <div v-if="revokeForm.errors.reason" class="text-red-600 text-sm mt-1">
                            {{ revokeForm.errors.reason }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeRevokeModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            :disabled="revokeForm.processing"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 disabled:opacity-50"
                            :disabled="revokeForm.processing || !revokeForm.reason"
                        >
                            {{ revokeForm.processing ? 'Revoking...' : 'Revoke Credential' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
