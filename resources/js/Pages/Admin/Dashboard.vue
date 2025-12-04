<script setup>
defineProps({ stats: Object })
</script>

<template>
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>

    <div class="grid grid-cols-3 gap-4">
      <div class="shadow rounded p-4 bg-white">
        <p class="text-gray-600">Total Credentials</p>
        <p class="text-2xl font-bold">{{ stats.credentials }}</p>
      </div>

      <div class="shadow rounded p-4 bg-white">
        <p class="text-gray-600">Issued</p>
        <p class="text-2xl font-bold">{{ stats.issued }}</p>
      </div>

      <div class="shadow rounded p-4 bg-white">
        <p class="text-gray-600">Revoked</p>
        <p class="text-2xl font-bold">{{ stats.revoked }}</p>
      </div>
    </div>
  </div>
</template>
