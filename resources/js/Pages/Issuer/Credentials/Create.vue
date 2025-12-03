<script setup>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const form = ref({
    student_id: '',
    title: '',
    description: '',
    evidence_id: '',
})

function submit() {
    const data = new FormData()
    Object.keys(form.value).forEach(k => data.append(k, form.value[k]))
    Inertia.post(route('credentials.store'), data)
}
</script>

<template>
    <div class="p-6 space-y-4">
        <h1 class="text-xl font-bold">Create Credential</h1>

        <input v-model="form.student_id" placeholder="Student ID" />
        <input v-model="form.title" placeholder="Credential Title" />
        <textarea v-model="form.description" placeholder="Description"></textarea>

        <input v-model="form.evidence_id" placeholder="Evidence ID (optional)" />

        <button @click="submit">Create</button>
    </div>
</template>
