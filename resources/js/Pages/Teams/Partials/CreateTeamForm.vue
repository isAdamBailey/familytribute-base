<template>
    <jet-form-section @submitted="createTeam">
        <template #title> Team Details </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Team Owner" />

                <div class="mt-2 flex items-center">
                    <img
                        class="h-12 w-12 rounded-full object-cover"
                        :src="authenticated.profile_photo_url"
                        :alt="authenticated.name"
                    />

                    <div class="ml-4 leading-tight">
                        <div>{{ authenticated.name }}</div>
                        <div class="text-sm text-gray-700">
                            {{ authenticated.email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />
                <jet-input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { computed } from "vue";
import JetButton from "@/Base/Button.vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const form = useForm({
    name: "",
});

const authenticated = computed(() => usePage().props.auth.user);

const createTeam = () => {
    form.post(route("teams.store"), {
        errorBag: "createTeam",
        preserveScroll: true,
    });
};
</script>
