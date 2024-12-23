<template>
    <JetFormSection :full="true" @submitted="storePicture">
        <template #title> New Picture </template>

        <template #description>
            Add a new picture to the "Pictures" Page
        </template>

        <template #form>
            <div class="col-span-6">
                <input
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <JetLabel for="photo" value="Photo" />

                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                        :style="
                            'background-image: url(\'' + photoPreview + '\');'
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

                <JetInputError :message="form.errors.photo" class="mt-2" />
            </div>

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
                <JetLabel for="description" value="Description" />
                <wysiwyg v-model="form.description" />
                <JetInputError
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-2">
                <JetLabel for="year" value="Year" />
                <JetInput
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1 block w-full"
                />
                <JetInputError :message="form.errors.year" class="mt-2" />
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

            <div class="col-span-6 mt-2 md:col-span-2">
                <JetLabel for="featured" value="Featured" />
                <InfoText>
                    Featured images are displayed randomly on the home page, and
                    cannot be private.
                </InfoText>
                <Checkbox
                    id="featured"
                    v-model:checked="form.featured"
                    name="featured"
                    :disabled="form.private"
                />
                <JetInputError :message="form.errors.featured" class="mt-2" />
            </div>
            <div class="col-span-6 mt-2 md:col-span-2">
                <JetLabel for="private" value="Private" />
                <InfoText
                    >Private images will only appear for registered
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
import JetActionMessage from "@/Base/ActionMessage.vue";
import Checkbox from "@/Base/Checkbox.vue";
import JetFormSection from "@/Base/FormSection.vue";
import InfoText from "@/Base/InfoText.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import { ref, watch } from "vue";

const props = defineProps({
    peopleOptions: { type: Array, required: true },
});

const form = useForm({
    _method: "POST",
    title: null,
    description: null,
    year: null,
    photo: null,
    person_ids: null,
    featured: false,
    private: false,
});

const photoPreview = ref(null);
const photoInput = ref(null);

watch(
    () => form.private,
    (newVal) => {
        if (newVal) {
            form.featured = false;
        }
    },
);

const storePicture = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route("pictures.store"), {
        errorBag: "storePicture",
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

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>
