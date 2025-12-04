<script setup>
defineProps({
  credential: Object,
  verification: Object,
})
</script>

<template>
  <div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold">Credential Verification</h1>

    <div class="bg-white shadow p-4 rounded">
      <h2 class="text-lg font-semibold">{{ credential.title }}</h2>
      <p class="text-gray-600">{{ credential.description }}</p>

      <div class="mt-3">
        <p><strong>Status:</strong> {{ credential.status }}</p>
        <p v-if="credential.revocation_reason">
          <strong>Revoked Reason:</strong> {{ credential.revocation_reason }}
        </p>
      </div>
    </div>

    <div class="bg-white shadow p-4 rounded">
      <h2 class="font-semibold text-lg">Integrity Check</h2>

      <p><strong>Pinned:</strong> {{ verification.pinned ? 'Yes' : 'No' }}</p>
      <p><strong>CID:</strong> {{ verification.cid || 'N/A' }}</p>

      <div v-if="verification.matches_sha === true" class="text-green-600 font-bold">
        SHA-256 Integrity: Valid ✔
      </div>

      <div v-else-if="verification.matches_sha === false" class="text-red-600 font-bold">
        SHA-256 Integrity: Invalid ✘
      </div>

      <div v-else class="text-gray-500">
        SHA-256 Integrity: Not Checked
      </div>
    </div>

  </div>
</template>
