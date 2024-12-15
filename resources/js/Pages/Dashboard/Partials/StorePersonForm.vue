<template>
    <JetFormSection :full="true" @submitted="storeObituary">
        <template #title> New Person</template>

        <template #description>
            Add a new person to the site. This allows you to "tag" them in
            photos and stories.
        </template>

        <template #form>
            <div class="col-span-6">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-5 w-full md:w-1/2">
                        <input
                            ref="photo"
                            type="file"
                            class="hidden"
                            @change="updatePhotoPreview"
                        />

                        <JetLabel for="photo" value="Photo" />

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

                        <JetSecondaryButton
                            class="mt-2 mr-2"
                            type="button"
                            @click.prevent="selectNewPhoto"
                        >
                            Select A Photo
                        </JetSecondaryButton>

                        <JetInputError
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

                        <JetLabel for="bg_photo" value="Background Photo" />

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

                        <JetSecondaryButton
                            class="mt-2 mr-2"
                            type="button"
                            @click.prevent="selectNewBackgroundPhoto"
                        >
                            Select A Background Photo
                        </JetSecondaryButton>

                        <JetInputError
                            :message="form.errors.background_photo"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                        <JetLabel for="first_name" value="First Name" />
                        <JetInput
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            class="mt-1 w-full"
                            autocomplete="first_name"
                        />
                        <JetInputError
                            :message="form.errors.first_name"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full px-3 md:w-1/2">
                        <JetLabel for="last_name" value="Last Name" />
                        <JetInput
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            class="mt-1 w-full"
                            autocomplete="last_name"
                        />
                        <JetInputError
                            :message="form.errors.last_name"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6">
                <div class="-mx-3 flex flex-wrap">
                    <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                        <JetLabel for="birth_date" value="Birth Date" />
                        <DatePicker v-model="form.birth_date" />
                        <JetInputError
                            :message="form.errors.birth_date"
                            class="mt-2"
                        />
                    </div>
                    <div class="w-full px-3 md:w-1/2">
                        <JetLabel for="death_date" value="Death Date" />
                        <DatePicker v-model="form.death_date" />
                        <JetInputError
                            :message="form.errors.death_date"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6">
                <JetLabel for="parent_ids" value="Parents" />
                <Multiselect
                    id="parent_ids"
                    v-model="form.parent_ids"
                    mode="tags"
                    :create-tag="true"
                    :options="peopleOptions"
                    placeholder="Select parents (you can do this later)"
                />
                <JetInputError :message="form.errors.parent_ids" class="mt-2" />
            </div>

            <div class="col-span-6">
                <JetLabel for="content" value="Obituary" />
                <wysiwyg v-model="form.content" />
                <JetInputError :message="form.errors.content" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <base-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </base-button>

            <JetActionMessage :on="form.recentlySuccessful" class="ml-3">
                Saved.
            </JetActionMessage>
        </template>
    </JetFormSection>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetActionMessage from "@/Base/ActionMessage.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import DatePicker from "@/Base/DatePicker.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";

const props = defineProps({
    peopleOptions: { type: Array, required: true },
});

const form = useForm({
    _method: "POST",
    first_name: null,
    last_name: null,
    content: null,
    birth_date: null,
    death_date: null,
    photo: null,
    background_photo: null,
    parent_ids: null,
});

const photoPreview = ref(null);
const bgPhotoPreview = ref(null);

const storeObituary = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    if (bgPhotoInput.value) {
        form.background_photo = bgPhotoInput.value.files[0];
    }

    form.post(route("obituaries.store"), {
        errorBag: "storeObituary",
        preserveScroll: true,
        onSuccess: () => {
            clearPhotoFileInput();
            form.reset();
        },
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const selectNewBackgroundPhoto = () => {
    bgPhotoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const updateBackgroundPhotoPreview = () => {
    const photo = bgPhotoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        bgPhotoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }

    if (bgPhotoInput.value?.value) {
        bgPhotoInput.value.value = null;
    }
};

const photoInput = ref(null);
const bgPhotoInput = ref(null);
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
