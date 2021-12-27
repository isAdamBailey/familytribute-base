<template>
    <jet-form-section :full="true" @submitted="storePicture">
        <template #title> New Picture </template>

        <template #description>
            Add a new picture to the "Pictures" Page
        </template>

        <template #form>
            <div class="col-span-6">
                <input
                    ref="photo"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Photo" />

                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="
                            'background-image: url(\'' + photoPreview + '\');'
                        "
                    >
                    </span>
                </div>

                <jet-secondary-button
                    class="mt-2 mr-2"
                    type="button"
                    @click.prevent="selectNewPhoto"
                >
                    Select A Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="col-span-6">
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

            <div class="col-span-6">
                <jet-label for="description" value="Description" />
                <wysiwyg v-model="form.description" />
                <jet-input-error
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-2">
                <jet-label for="year" value="Year" />
                <jet-input
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1 block w-full"
                />
                <jet-input-error :message="form.errors.year" class="mt-2" />
            </div>

            <div class="col-span-6">
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

            <div class="mt-2 col-span-6 md:col-span-2">
                <jet-label for="featured" value="Featured" />
                <info-text>
                    Featured images are displayed randomly on the home page, and
                    cannot be private.
                </info-text>
                <checkbox
                    id="featured"
                    v-model:checked="form.featured"
                    name="featured"
                    :disabled="form.private"
                />
                <jet-input-error :message="form.errors.featured" class="mt-2" />
            </div>
            <div class="mt-2 col-span-6 md:col-span-2">
                <jet-label for="private" value="Private" />
                <info-text
                    >Private images will only appear for registered
                    users</info-text
                >
                <checkbox
                    id="private"
                    v-model:checked="form.private"
                    name="private"
                />
                <jet-input-error :message="form.errors.private" class="mt-2" />
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
import Multiselect from "@vueform/multiselect";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import Wysiwyg from "@/Base/Wysiwyg";
import Checkbox from "@/Base/Checkbox";
import InfoText from "../../../Base/InfoText";

export default defineComponent({
    components: {
        InfoText,
        Checkbox,
        JetActionMessage,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
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
                description: null,
                year: null,
                photo: null,
                person_ids: null,
                featured: false,
                private: false,
            }),

            photoPreview: null,
        };
    },

    watch: {
        "form.private"() {
            if (this.form.private) {
                this.form.featured = false;
            }
        },
    },

    methods: {
        storePicture() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0];
            }

            this.form.post(route("pictures.store"), {
                errorBag: "storePicture",
                preserveScroll: true,
                onSuccess: () => {
                    this.clearPhotoFileInput();
                    this.form.reset();
                },
            });
        },

        selectNewPhoto() {
            this.$refs.photo.click();
        },

        updatePhotoPreview() {
            const photo = this.$refs.photo.files[0];

            if (!photo) return;

            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
        },

        clearPhotoFileInput() {
            if (this.$refs.photo?.value) {
                this.$refs.photo.value = null;
            }
        },
    },
});
</script>
