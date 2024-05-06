<template>
    <jet-dialog-modal :show="open" @close="closeModal()">
        <template #title> Edit Obituary</template>

        <template #content>
            <div class="col-span-6 sm:col-span-4">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-5 w-full md:w-1/2">
                        <input
                            ref="photo"
                            type="file"
                            class="hidden"
                            @change="updatePhotoPreview"
                        />

                        <jet-label for="photo" value="Photo" />

                        <div v-show="!photoPreview" class="mt-2">
                            <img
                                :src="person.photo_url"
                                :alt="person.full_name"
                                class="h-20 w-20 rounded-full object-cover"
                            />
                        </div>

                        <div v-show="photoPreview" class="mt-2">
                            <span
                                class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                                :style="
                                    'background-image: url(\'' +
                                    photoPreview +
                                    '\');'
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

                        <jet-input-error
                            :message="form.errors.photo"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full md:w-1/2">
                        <input
                            ref="bg_photo"
                            type="file"
                            class="hidden"
                            @change="updateBackgroundPhotoPreview"
                        />

                        <jet-label for="bg_photo" value="Background Photo" />

                        <div
                            v-show="
                                person.obituary.background_photo_url &&
                                !bgPhotoPreview
                            "
                            class="mt-2"
                        >
                            <img
                                :src="person.obituary.background_photo_url"
                                :alt="person.full_name"
                                class="h-20 w-20 rounded-full object-cover"
                            />
                        </div>

                        <div v-show="bgPhotoPreview" class="mt-2">
                            <span
                                class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                                :style="
                                    'background-image: url(\'' +
                                    bgPhotoPreview +
                                    '\');'
                                "
                            >
                            </span>
                        </div>

                        <jet-secondary-button
                            class="mt-2 mr-2"
                            type="button"
                            @click.prevent="selectNewBackgroundPhoto"
                        >
                            Select A New Background Photo
                        </jet-secondary-button>

                        <jet-input-error
                            :message="form.errors.background_photo"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6 mt-3 sm:col-span-4">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
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
                    <div class="w-full px-3 md:w-1/2">
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

            <div class="col-span-6 mt-3 sm:col-span-4">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                        <jet-label for="birth_date" value="Birth Date" />
                        <date-picker v-model="form.birth_date" />
                        <jet-input-error
                            :message="form.errors.birth_date"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full px-3 md:w-1/2">
                        <jet-label for="death_date" value="Death Date" />
                        <date-picker v-model="form.death_date" />
                        <jet-input-error
                            :message="form.errors.death_date"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6 mt-2 sm:col-span-4">
                <jet-label for="parent_ids" value="Parents" />
                <Multiselect
                    id="parent_ids"
                    v-model="form.parent_ids"
                    mode="tags"
                    :create-tag="true"
                    :options="peopleOptions"
                    placeholder="Select parents"
                />
                <jet-input-error
                    :message="form.errors.parent_ids"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 mt-3 sm:col-span-4">
                <jet-label for="content" value="Obituary" />
                <wysiwyg v-model="form.content" />
                <jet-input-error :message="form.errors.content" class="mt-2" />
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
                @click="updateObituary"
            >
                Update Obituary
            </base-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import { defineComponent } from "vue";
import { parseISO } from "date-fns";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import DatePicker from "@/Base/DatePicker.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";

export default defineComponent({
    components: {
        Multiselect,
        Wysiwyg,
        JetDialogModal,
        JetSecondaryButton,
        JetInput,
        JetInputError,
        JetLabel,
        DatePicker,
    },

    props: {
        open: {
            type: Boolean,
            default: false,
        },
        person: {
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
                first_name: this.person.first_name,
                last_name: this.person.last_name,
                content: this.person.obituary.content,
                birth_date: parseISO(this.person.obituary.birth_date),
                death_date: parseISO(this.person.obituary.death_date),
                photo: null,
                background_photo: null,
                parent_ids: this.person.parent_ids,
            }),
            photoPreview: null,
            bgPhotoPreview: null,
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
        updateObituary() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0];
            }
            if (this.$refs.bg_photo) {
                this.form.background_photo = this.$refs.bg_photo.files[0];
            }

            this.form.post(
                route("obituaries.update", this.person.obituary.id),
                {
                    errorBag: "updateObituary",
                    onSuccess: () => this.closeModal(),
                }
            );
        },

        selectNewPhoto() {
            this.$refs.photo.click();
        },

        selectNewBackgroundPhoto() {
            this.$refs.bg_photo.click();
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

        updateBackgroundPhotoPreview() {
            const photo = this.$refs.bg_photo.files[0];

            if (!photo) return;

            const reader = new FileReader();

            reader.onload = (e) => {
                this.bgPhotoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
        },

        clearPhotoFileInput() {
            this.photoPreview = null;
            this.bgPhotoPreview = null;

            if (this.$refs.photo?.value) {
                this.$refs.photo.value = null;
            }

            if (this.$refs.bg_photo?.value) {
                this.$refs.bg_photo.value = null;
            }
        },

        closeModal() {
            this.clearPhotoFileInput();
            this.form.reset();
            this.form.clearErrors();
            this.$emit("close", true);
        },
    },
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
