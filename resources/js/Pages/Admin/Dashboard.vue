<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    stats: {
        total_users: number;
        total_credentials: number;
        issued_credentials: number;
        pending_credentials: number;
        revoked_credentials: number;
        total_evidence: number;
        blockchain_anchored: number;
    };
    recentActivity: Array<any>;
    credentialsByMonth: Array<{ month: string; count: number }>;
    topIssuers: Array<{ name: string; count: number }>;
}>();
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Total Users</div>
                        <!-- <div class="text-3xl font-bold text-blue-600">{{ stats.total_users }}</div> -->
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Total Credentials</div>
                        <div class="text-3xl font-bold text-purple-600">{{ stats.total_credentials }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Issued</div>
                        <div class="text-3xl font-bold text-green-600">{{ stats.issued_credentials }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Blockchain Anchored</div>
                        <div class="text-3xl font-bold text-indigo-600">{{ stats.blockchain_anchored }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Pending</div>
                        <div class="text-3xl font-bold text-yellow-600">{{ stats.pending_credentials }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Revoked</div>
                        <div class="text-3xl font-bold text-red-600">{{ stats.revoked_credentials }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="text-gray-500 text-sm">Evidence Files</div>
                        <div class="text-3xl font-bold text-teal-600">{{ stats.total_evidence }}</div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
                        <div class="space-y-3">
                            <div
                                v-for="activity in recentActivity"
                                :key="activity.id"
                                class="border-l-4 border-blue-500 pl-4 py-2"
                            >
                                <div class="font-semibold">{{ activity.actor }}</div>
                                <div class="text-sm text-gray-600">{{ activity.action }}</div>
                                <div class="text-xs text-gray-400">{{ activity.time }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Issuers -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold mb-4">Top Issuers</h2>
                        <div class="space-y-3">
                            <div
                                v-for="(issuer, index) in topIssuers"
                                :key="index"
                                class="flex justify-between items-center"
                            >
                                <span class="font-medium">{{ issuer.name }}</span>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ issuer.count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credentials by Month Chart -->
                <div class="bg-white rounded-lg shadow p-6 mt-8">
                    <h2 class="text-xl font-bold mb-4">Credentials Issued (Last 6 Months)</h2>
                    <div v-if="credentialsByMonth.length > 0" class="flex items-end justify-around h-64">
                        <div
                            v-for="month in credentialsByMonth"
                            :key="month.month"
                            class="flex flex-col items-center"
                        >
                            <div
                                :style="{ height: `${(month.count / Math.max(...credentialsByMonth.map(m => m.count))) * 200}px` }"
                                class="bg-blue-500 w-16 rounded-t"
                            ></div>
                            <div class="text-xs text-gray-600 mt-2">{{ month.month }}</div>
                            <div class="text-sm font-semibold">{{ month.count }}</div>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 text-center py-8">
                        No credentials issued in the last 6 months
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
