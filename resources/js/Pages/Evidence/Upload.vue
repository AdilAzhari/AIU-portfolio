<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);

const form = useForm({
    title: '',
    description: '',
    visibility: 'private',
    file: null as File | null,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
        form.file = target.files[0];
    }
};

const submit = () => {
    form.post(route('evidence.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedFile.value = null;
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
};
</script>

<template>
    <Head title="Upload Evidence" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload Evidence
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <p class="text-gray-600">
                                Upload supporting documents for your credentials. Files will be stored securely on IPFS
                                and can be referenced when credentials are issued.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Title *" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., Hackathon Winner Certificate"
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description *" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                    required
                                    placeholder="Describe this evidence and its significance..."
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Visibility -->
                            <div>
                                <InputLabel for="visibility" value="Visibility" />
                                <select
                                    id="visibility"
                                    v-model="form.visibility"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="private">Private (Only you and issuers can see)</option>
                                    <option value="public">Public (Visible on verification page)</option>
                                </select>
                                <InputError :message="form.errors.visibility" class="mt-2" />
                            </div>

                            <!-- File Upload -->
                            <div>
                                <InputLabel for="file" value="File *" />
                                <div class="mt-1">
                                    <input
                                        id="file"
                                        ref="fileInput"
                                        type="file"
                                        @change="handleFileChange"
                                        class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100
                                            cursor-pointer"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Supported formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB)
                                    </p>
                                    <InputError :message="form.errors.file" class="mt-2" />
                                </div>
                                <div v-if="selectedFile" class="mt-2 text-sm text-gray-600">
                                    Selected: <span class="font-medium">{{ selectedFile.name }}</span>
                                    ({{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB)
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between">
                                <a
                                    :href="route('student.dashboard')"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                                >
                                    Back to Dashboard
                                </a>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Uploading to IPFS...</span>
                                    <span v-else>Upload Evidence</span>
                                </PrimaryButton>
                            </div>

                            <!-- Progress Indicator -->
                            <div v-if="form.processing" class="mt-4">
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex items-center">
                                        <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">
                                                Uploading file to IPFS...
                                            </p>
                                            <p class="text-xs text-blue-700 mt-1">
                                                This may take a few moments depending on file size
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
