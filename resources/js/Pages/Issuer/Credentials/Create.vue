<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps<{
    students: Array<{ id: number; name: string; email: string }>;
    evidence: Array<{
        id: number;
        title: string;
        filename: string;
        student_name: string;
        cid: string | null;
        status: string;
    }>;
}>();

const form = useForm({
    student_id: '',
    title: '',
    description: '',
    evidence_id: '',
});

const submit = () => {
    form.post(route('credentials.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Credential" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Credential
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <p class="text-gray-600">
                                Create a new credential for a student. The credential will be created in "pending" status
                                and must be issued to anchor it to the blockchain.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Student Selection -->
                            <div>
                                <InputLabel for="student_id" value="Student *" />
                                <select
                                    id="student_id"
                                    v-model="form.student_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select a student...</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">
                                        {{ student.name }} ({{ student.email }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.student_id" class="mt-2" />
                            </div>

                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Credential Title *" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="e.g., AI Hackathon Winner, Dean's List Spring 2024"
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
                                    rows="5"
                                    required
                                    placeholder="Provide details about this credential, the achievement, and what it signifies..."
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Evidence Selection (Optional) -->
                            <div>
                                <InputLabel for="evidence_id" value="Supporting Evidence (Optional)" />
                                <select
                                    id="evidence_id"
                                    v-model="form.evidence_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">None (no evidence attached)</option>
                                    <option v-for="item in evidence" :key="item.id" :value="item.id">
                                        {{ item.title }} - by {{ item.student_name }} ({{ item.filename }})
                                        <template v-if="item.status !== 'pinned'"> - [{{ item.status.toUpperCase() }}]</template>
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    Link this credential to supporting documents uploaded by students
                                </p>
                                <InputError :message="form.errors.evidence_id" class="mt-2" />
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-blue-900 mb-2">What happens next?</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>1. Credential will be created with status "Pending"</li>
                                    <li>2. You can review and edit the credential</li>
                                    <li>3. Click "Issue" to approve and anchor it to the blockchain</li>
                                    <li>4. Student will receive the credential in their portfolio</li>
                                </ul>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-between">
                                <Link
                                    :href="route('issuer.dashboard')"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Credential</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Guide -->
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Credential Guidelines</h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>
                            <strong>Title:</strong> Keep it concise and descriptive (e.g., "Outstanding Research Award 2024")
                        </li>
                        <li>
                            <strong>Description:</strong> Include relevant details about the achievement, criteria met, and significance
                        </li>
                        <li>
                            <strong>Evidence:</strong> Attach supporting documents when available (certificates, projects, papers)
                        </li>
                        <li>
                            <strong>Verification:</strong> Once issued, credentials are publicly verifiable via blockchain
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
