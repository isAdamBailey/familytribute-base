<template>
    <jet-form-section :full="true" @submitted="storeObituary">
        <template #title> New Obituary</template>

        <template #description>
            Add a new obituary to the "People" Page
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <input
                    ref="photo"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Headstone Photo" />

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
                    Select A Headstone Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <jet-label for="first_name" value="First Name" />
                        <jet-input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            class="mt-1 w-full"
                            autocomplete="first_name"
                        />
                        <jet-input-error
                            :message="form.errors.first_name"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <jet-label for="last_name" value="Last Name" />
                        <jet-input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            class="mt-1 w-full"
                            autocomplete="last_name"
                        />
                        <jet-input-error
                            :message="form.errors.last_name"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <jet-label for="birth_date" value="Birth Date" />
                        <date-picker v-model="form.birth_date" />
                        <jet-input-error
                            :message="form.errors.birth_date"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <jet-label for="death_date" value="Death Date" />
                        <date-picker v-model="form.death_date" />
                        <jet-input-error
                            :message="form.errors.death_date"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="content" value="Obituary" />
                <wysiwyg v-model="form.content" />
                <jet-input-error :message="form.errors.content" class="mt-2" />
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
import JetButton from "@/Base/Button.vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import DatePicker from "@/Base/DatePicker";
import Wysiwyg from "@/Base/Wysiwyg";

export default defineComponent({
    components: {
        Wysiwyg,
        DatePicker,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: "POST",
                first_name: null,
                last_name: null,
                content: null,
                birth_date: null,
                death_date: null,
                photo: null,
            }),

            photoPreview: null,
        };
    },

    methods: {
        storeObituary() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0];
            }

            this.form.post(route("obituaries.store"), {
                errorBag: "storeObituary",
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
