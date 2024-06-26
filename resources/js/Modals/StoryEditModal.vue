<template>
    <jet-dialog-modal :show="open" @close="closeModal()">
        <template #title> Edit Story </template>

        <template #content>
            <div class="col-span-6 mt-2 sm:col-span-4">
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

            <div class="col-span-6 mt-2 sm:col-span-4">
                <jet-label for="excerpt" value="Excerpt" />
                <wysiwyg v-model="form.excerpt" :excerpt="true" />
                <jet-input-error :message="form.errors.excerpt" class="mt-2" />
            </div>

            <div class="col-span-6 mt-2 sm:col-span-4">
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

            <div class="col-span-6 mt-2 sm:col-span-4">
                <jet-label for="content" value="Story" />
                <wysiwyg v-model="form.content" />
                <jet-input-error :message="form.errors.content" class="mt-2" />
            </div>

            <div class="-mx-3 flex flex-wrap">
                <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                    <jet-label for="private" value="Private" />
                    <info-text>
                        Private stories will only appear for registered users
                    </info-text>
                    <checkbox
                        id="private"
                        v-model:checked="form.private"
                        name="private"
                    />
                    <jet-input-error
                        :message="form.errors.private"
                        class="mt-2"
                    />
                </div>
                <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                    <jet-label for="year" value="Year" />
                    <jet-input
                        id="year"
                        v-model="form.year"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <jet-input-error :message="form.errors.year" class="mt-2" />
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="closeModal()">
                Nevermind
            </jet-secondary-button>

            <base-button
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                @click="updateStory"
            >
                Update Story
            </base-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import { defineComponent } from "vue";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import InfoText from "@/Base/InfoText.vue";
import Checkbox from "@/Base/Checkbox.vue";

export default defineComponent({
    components: {
        Checkbox,
        InfoText,
        Wysiwyg,
        JetDialogModal,
        JetSecondaryButton,
        JetInput,
        JetInputError,
        JetLabel,
        Multiselect,
    },

    props: {
        open: {
            type: Boolean,
            default: false,
        },
        story: {
            type: Object,
            required: true,
        },
        people: {
            type: Array,
            required: true,
        },
    },
    emits: ["close"],

    data() {
        return {
            form: this.$inertia.form({
                _method: "PUT",
                title: this.story.title,
                excerpt: this.story.excerpt,
                content: this.story.content,
                year: this.story.year,
                private: Boolean(this.story.private),
                person_ids: this.story.person_ids,
            }),
        };
    },

    computed: {
        peopleOptions() {
            return this.people.map((person) => {
                return {
                    value: person.id,
                    label: person.full_name,
                };
            });
        },
    },

    methods: {
        updateStory() {
            this.form.post(route("stories.update", this.story.slug), {
                errorBag: "updateStory",
                onSuccess: () => this.closeModal(),
            });
        },

        closeModal() {
            this.form.reset();
            this.form.clearErrors();
            this.$emit("close", true);
        },
    },
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
