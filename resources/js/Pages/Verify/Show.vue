<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps<{
    credential: {
        id: number;
        title: string;
        description: string;
        status: string;
        student_name: string;
        issuer_name: string;
        issued_at: string;
        revoked_at?: string;
        revocation_reason?: string;
    };
    blockchain?: {
        verified: boolean;
        tx_hash?: string;
        status?: string;
        explorer_url?: string;
        error?: string;
    };
    ipfs?: {
        cid: string;
        url: string;
        gateway: string;
        integrity_check?: boolean;
    };
}>();

const getStatusIcon = (status: string) => {
    if (status === 'issued') return '✓';
    if (status === 'revoked') return '✗';
    return '⏳';
};

const getStatusColor = (status: string) => {
    if (status === 'issued') return 'text-green-600';
    if (status === 'revoked') return 'text-red-600';
    return 'text-yellow-600';
};
</script>

<template>
    <GuestLayout>
        <Head title="Verify Credential" />

        <div class="min-h-screen bg-gray-100 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Status Banner -->
                <div
                    :class="[
                        'rounded-lg p-6 mb-8',
                        credential.status === 'issued' ? 'bg-green-50 border-green-200' :
                        credential.status === 'revoked' ? 'bg-red-50 border-red-200' :
                        'bg-yellow-50 border-yellow-200'
                    ]"
                    class="border-2"
                >
                    <div class="flex items-center">
                        <div
                            :class="getStatusColor(credential.status)"
                            class="text-6xl mr-6"
                        >
                            {{ getStatusIcon(credential.status) }}
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold" :class="getStatusColor(credential.status)">
                                {{ credential.status.toUpperCase() }}
                            </h1>
                            <p class="text-gray-600 mt-1">This credential has been verified</p>
                        </div>
                    </div>
                </div>

                <!-- Credential Details -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="bg-gray-800 text-white p-6">
                        <h2 class="text-2xl font-bold">{{ credential.title }}</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <p class="text-gray-800">{{ credential.description || 'No description provided' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-500">Student</label>
                                <p class="font-semibold">{{ credential.student_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Issued By</label>
                                <p class="font-semibold">{{ credential.issuer_name }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Issued Date</label>
                            <p class="font-semibold">{{ credential.issued_at }}</p>
                        </div>
                        <div v-if="credential.revoked_at">
                            <label class="text-sm text-gray-500">Revoked Date</label>
                            <p class="font-semibold text-red-600">{{ credential.revoked_at }}</p>
                        </div>
                        <div v-if="credential.revocation_reason">
                            <label class="text-sm text-gray-500">Revocation Reason</label>
                            <p class="text-red-600">{{ credential.revocation_reason }}</p>
                        </div>
                    </div>
                </div>

                <!-- Blockchain Verification -->
                <div v-if="blockchain" class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="bg-blue-800 text-white p-6">
                        <h3 class="text-xl font-bold">🔗 Blockchain Verification</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="blockchain.verified" class="space-y-3">
                            <div class="flex items-center text-green-600">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                </svg>
                                <span class="font-semibold">Verified on Ethereum Sepolia</span>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Transaction Hash</label>
                                <p class="font-mono text-xs break-all">{{ blockchain.tx_hash }}</p>
                            </div>
                            <div>
                                <a
                                    :href="blockchain.explorer_url"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                >
                                    View on Etherscan →
                                </a>
                            </div>
                        </div>
                        <div v-else class="text-red-600">
                            <p>{{ blockchain.error }}</p>
                        </div>
                    </div>
                </div>

                <!-- IPFS Verification -->
                <div v-if="ipfs" class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-purple-800 text-white p-6">
                        <h3 class="text-xl font-bold">📦 IPFS Storage Verification</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <label class="text-sm text-gray-500">IPFS CID</label>
                            <p class="font-mono text-xs break-all">{{ ipfs.cid }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Gateway</label>
                            <p>{{ ipfs.gateway }}</p>
                        </div>
                        <div v-if="ipfs.integrity_check !== undefined">
                            <label class="text-sm text-gray-500">File Integrity</label>
                            <p :class="ipfs.integrity_check ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                                {{ ipfs.integrity_check ? '✓ Hash Verified' : '✗ Hash Mismatch' }}
                            </p>
                        </div>
                        <div>
                            <a
                                :href="ipfs.url"
                                target="_blank"
                                class="text-blue-600 hover:underline"
                            >
                                View File on IPFS →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
