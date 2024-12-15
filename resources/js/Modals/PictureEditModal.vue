<template>
    <JetDialogModal :show="open" @close="closeModal()">
        <template #title> Edit Picture </template>

        <template #content>
            <div class="col-span-6 sm:col-span-4">
                <input
                    ref="photo"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <JetLabel for="photo" value="Photo" />

                <div v-show="!photoPreview" class="mt-2">
                    <img
                        :src="picture.url"
                        :alt="picture.title"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                </div>

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
                    Select A New Photo
                </JetSecondaryButton>

                <JetInputError :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="col-span-6 mt-2 sm:col-span-4">
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

            <div class="col-span-6 mt-2 sm:col-span-4">
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

            <div class="col-span-6 mt-2 sm:col-span-4">
                <JetLabel for="description" value="Description" />
                <wysiwyg v-model="form.description" />
                <JetInputError
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 mt-2 sm:col-span-4">
                <JetLabel for="year" value="Year" />
                <JetInput
                    id="year"
                    v-model="form.year"
                    type="number"
                    class="mt-1 block w-full"
                />
                <JetInputError :message="form.errors.year" class="mt-2" />
            </div>

            <div class="mt-2 flex">
                <div class="w-1/2">
                    <JetLabel for="featured" value="Featured" />
                    <InfoText>
                        Featured images are displayed randomly on the home page.
                    </InfoText>
                    <Checkbox
                        id="featured"
                        v-model:checked="form.featured"
                        name="featured"
                        :disabled="form.private"
                    />
                    <JetInputError
                        :message="form.errors.featured"
                        class="mt-2"
                    />
                </div>

                <div class="w-1/2">
                    <JetLabel for="private" value="Private" />
                    <InfoText>
                        Private images will only appear for registered users
                    </InfoText>
                    <Checkbox
                        id="private"
                        v-model:checked="form.private"
                        name="private"
                    />
                    <JetInputError
                        :message="form.errors.private"
                        class="mt-2"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <JetSecondaryButton @click="closeModal()">
                Nevermind
            </JetSecondaryButton>

            <BaseButton
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                @click="updatePicture"
            >
                Update Picture
            </BaseButton>
        </template>
    </JetDialogModal>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import Checkbox from "@/Base/Checkbox.vue";
import InfoText from "@/Base/InfoText.vue";

const props = defineProps({
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
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "PUT",
    title: props.picture.title,
    description: props.picture.description,
    year: props.picture.year,
    featured: Boolean(props.picture.featured),
    private: Boolean(props.picture.private),
    photo: null,
    person_ids: props.picture.person_ids,
});

const photo = ref(null);
const photoPreview = ref(null);

const peopleOptions = computed(() => {
    return props.people.map((person) => {
        return {
            value: person.id,
            label: person.full_name,
        };
    });
});

watch(
    () => form.private,
    (newVal) => {
        if (newVal) {
            form.featured = false;
        }
    },
);

const updatePicture = () => {
    if (photo.value) {
        form.photo = photo.value.files[0];
    }

    form.post(route("pictures.update", props.picture.slug), {
        errorBag: "updatePicture",
        onSuccess: () => closeModal(),
    });
};

const selectNewPhoto = () => {
    photo.value.click();
};

const updatePhotoPreview = () => {
    const photoFile = photo.value.files[0];

    if (!photoFile) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photoFile);
};

const clearPhotoFileInput = () => {
    if (photo.value?.value) {
        photo.value.value = null;
    }
};

const closeModal = () => {
    photoPreview.value = null;
    clearPhotoFileInput();
    form.clearErrors();
    form.reset();
    emit("close", true);
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
