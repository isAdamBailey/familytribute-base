<template>
    <jet-form-section :full="true" @submitted="storeStory">
        <template #title> New Story </template>

        <template #description>
            Add a new story to the "Stories" Page
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="title" value="Title" />
                <jet-input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="title"
                />
                <jet-input-error :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="excerpt" value="Excerpt" />
                <wysiwyg v-model="form.excerpt" :excerpt="true" />
                <jet-input-error :message="form.errors.excerpt" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="content" value="Story" />
                <wysiwyg v-model="form.content" />
                <jet-input-error :message="form.errors.content" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="person_ids" value="Tag People" />
                <Multiselect
                    id="person_ids"
                    v-model="form.person_ids"
                    mode="tags"
                    :create-tag="true"
                    :options="peopleOptions"
                />
                <jet-input-error
                    :message="form.errors.person_ids"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <base-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </base-button>

            <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                Saved.
            </jet-action-message>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import Multiselect from "@vueform/multiselect";
import Wysiwyg from "@/Base/Wysiwyg";

export default defineComponent({
    components: {
        JetActionMessage,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        Wysiwyg,
        Multiselect,
    },

    props: {
        peopleOptions: Array,
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: "POST",
                title: null,
                excerpt: null,
                content: null,
                person_ids: null,
            }),
        };
    },

    methods: {
        storeStory() {
            this.form.post(route("stories.store"), {
                errorBag: "storeStory",
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
            });
        },
    },
});
</script>
