<template>
    <jet-dialog-modal :show="open" @close="closeModal()">
        <template #title> Edit Picture </template>

        <template #content>
            <div class="col-span-6 sm:col-span-4">
                <input
                    ref="photo"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Photo" />

                <div v-show="!photoPreview" class="mt-2">
                    <img
                        :src="picture.url"
                        :alt="picture.title"
                        class="rounded-full h-20 w-20 object-cover"
                    />
                </div>

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
                    Select A New Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="mt-2 col-span-6 sm:col-span-4">
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

            <div class="mt-2 col-span-6 sm:col-span-4">
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

            <div class="mt-2 col-span-6 sm:col-span-4">
                <jet-label for="description" value="Description" />
                <wysiwyg v-model="form.description" />
                <jet-input-error
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="mt-2 col-span-6 sm:col-span-4">
                <jet-label for="year" value="Year" />
                <jet-input
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1 block w-full"
                />
                <jet-input-error :message="form.errors.year" class="mt-2" />
            </div>

          <div class="mt-2 col-span-6 sm:col-span-4">
            <jet-label for="featured" value="Featured" />
            <div class="flex items-center text-sm text-indigo-600">
              <i class="ri-information-line mr-1"></i>
              Featured images are displayed on the home page
            </div>
            <checkbox
                id="featured"
                v-model:checked="form.featured"
                name="featured"
            />
            <jet-input-error
                :message="form.errors.featured"
                class="mt-2"
            />
          </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="closeModal()">
                Nevermind
            </jet-secondary-button>

            <base-button
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="updatePicture"
            >
                Update Picture
            </base-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import { defineComponent } from "vue";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal";
import JetSecondaryButton from "@/Base/SecondaryButton";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import Wysiwyg from "@/Base/Wysiwyg";
import Checkbox from "../Base/Checkbox";

export default defineComponent({
    components: {
      Checkbox,
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
        picture: {
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
                title: this.picture.title,
                description: this.picture.description,
                year: this.picture.year,
                featured: Boolean(this.picture.featured),
                photo: null,
                person_ids: this.picture.person_ids,
            }),

            photoPreview: null,
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
        updatePicture() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0];
            }

            this.form.post(route("pictures.update", this.picture.slug), {
                errorBag: "updatePicture",
                onSuccess: () => this.closeModal(),
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

        closeModal() {
            this.photoPreview = null;
            this.clearPhotoFileInput();
            this.form.clearErrors();
            this.form.reset();
            this.$emit("close", true);
        },
    },
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
