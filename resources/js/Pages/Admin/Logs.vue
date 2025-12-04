<script setup>
defineProps({ logs: Array })
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold">Activity Logs</h1>

    <table class="min-w-full mt-4 bg-white shadow rounded">
      <thead>
        <tr class="border-b">
          <th class="p-2 text-left">Time</th>
          <th class="p-2 text-left">Actor</th>
          <th class="p-2 text-left">Action</th>
          <th class="p-2 text-left">Meta</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id" class="border-b">
          <td class="p-2">{{ new Date(log.created_at).toLocaleString() }}</td>
          <td class="p-2">{{ log.actor?.name ?? 'Public Visitor' }}</td>
          <td class="p-2">{{ log.action }}</td>
          <td class="p-2 text-xs text-gray-500">{{ log.meta }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
