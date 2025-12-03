<script setup>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const form = ref({
  title: '',
  description: '',
  visibility: 'private',
})

function submit() {
  const data = new FormData()
  data.append('file', document.querySelector('#file').files[0])
  data.append('title', form.value.title)
  data.append('description', form.value.description)
  data.append('visibility', form.value.visibility)

  Inertia.post(route('evidence.store'), data)
}
</script>

<template>
  <div class="p-6 space-y-4">
    <input v-model="form.title" type="text" placeholder="Title" />
    <textarea v-model="form.description" placeholder="Description"></textarea>
    <select v-model="form.visibility">
      <option value="private">Private</option>
      <option value="public">Public</option>
    </select>
    <input id="file" type="file" />

    <button @click="submit">Upload</button>
  </div>
</template>
