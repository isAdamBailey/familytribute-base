<template>
    <JetFormSection :full="true" @submitted="storeStory">
        <template #title> New Story </template>

        <template #description>
            Add a new story to the "Stories" Page
        </template>

        <template #form>
            <div class="col-span-6">
                <JetLabel for="title" value="Title" />
                <JetInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="title"
                />
                <JetInputError :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6">
                <JetLabel for="excerpt" value="Excerpt" />
                <wysiwyg v-model="form.excerpt" :max-character-count="250" />
                <JetInputError :message="form.errors.excerpt" class="mt-2" />
            </div>

            <div class="col-span-6">
                <JetLabel for="content" value="Story" />
                <wysiwyg v-model="form.content" />
                <JetInputError :message="form.errors.content" class="mt-2" />
            </div>

            <div class="col-span-6">
                <JetLabel for="person_ids" value="Tag People" />
                <Multiselect
                    id="person_ids"
                    v-model="form.person_ids"
                    mode="tags"
                    :create-tag="true"
                    :options="peopleOptions"
                />
                <JetInputError :message="form.errors.person_ids" class="mt-2" />
            </div>

            <div class="col-span-6 md:col-span-2">
                <JetLabel for="year" value="Year" />
                <JetInput
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1 block w-full"
                />
                <JetInputError :message="form.errors.year" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-2">
                <JetLabel for="private" value="Private" />
                <InfoText
                    >Private stories will only appear for registered
                    users</InfoText
                >
                <Checkbox
                    id="private"
                    v-model:checked="form.private"
                    name="private"
                />
                <JetInputError :message="form.errors.private" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <BaseButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </BaseButton>

            <JetActionMessage :on="form.recentlySuccessful" class="ml-3">
                Saved.
            </JetActionMessage>
        </template>
    </JetFormSection>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import Multiselect from "@vueform/multiselect";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import InfoText from "@/Base/InfoText.vue";
import Checkbox from "@/Base/Checkbox.vue";

const props = defineProps({
    peopleOptions: Array,
});

const form = useForm({
    _method: "POST",
    title: null,
    excerpt: null,
    content: null,
    year: null,
    private: false,
    person_ids: null,
});

const storeStory = () => {
    form.post(route("stories.store"), {
        errorBag: "storeStory",
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>
