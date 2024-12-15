<template>
    <JetDialogModal :show="open" @close="closeModal()">
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

                        <JetLabel for="photo" value="Photo" />

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

                        <JetSecondaryButton
                            class="mt-2 mr-2"
                            type="button"
                            @click.prevent="selectNewPhoto"
                        >
                            Select A New Photo
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

                        <JetSecondaryButton
                            class="mt-2 mr-2"
                            type="button"
                            @click.prevent="selectNewBackgroundPhoto"
                        >
                            Select A New Background Photo
                        </JetSecondaryButton>

                        <JetInputError
                            :message="form.errors.background_photo"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <div class="col-span-6 mt-3 sm:col-span-4">
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

            <div class="col-span-6 mt-3 sm:col-span-4">
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

            <div class="col-span-6 mt-2 sm:col-span-4">
                <JetLabel for="parent_ids" value="Parents" />
                <Multiselect
                    id="parent_ids"
                    v-model="form.parent_ids"
                    mode="tags"
                    :create-tag="true"
                    :options="peopleOptions"
                    placeholder="Select parents"
                />
                <JetInputError :message="form.errors.parent_ids" class="mt-2" />
            </div>

            <div class="col-span-6 mt-3 sm:col-span-4">
                <JetLabel for="content" value="Obituary" />
                <wysiwyg v-model="form.content" />
                <JetInputError :message="form.errors.content" class="mt-2" />
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
                @click="updateObituary"
            >
                Update Obituary
            </BaseButton>
        </template>
    </JetDialogModal>
</template>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { parseISO } from "date-fns";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import DatePicker from "@/Base/DatePicker.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";

const props = defineProps({
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
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "PUT",
    first_name: props.person.first_name,
    last_name: props.person.last_name,
    content: props.person.obituary.content,
    birth_date: parseISO(props.person.obituary.birth_date),
    death_date: parseISO(props.person.obituary.death_date),
    photo: null,
    background_photo: null,
    parent_ids: props.person.parent_ids,
});

const photo = ref(null);
const bg_photo = ref(null);
const photoPreview = ref(null);
const bgPhotoPreview = ref(null);

const peopleOptions = computed(() => {
    return props.people.map((person) => {
        return {
            value: person.id,
            label: person.full_name,
        };
    });
});

const updateObituary = () => {
    if (photo.value) {
        form.photo = photo.value.files[0];
    }
    if (bg_photo.value) {
        form.background_photo = bg_photo.value.files[0];
    }

    form.post(route("obituaries.update", props.person.obituary.id), {
        errorBag: "updateObituary",
        onSuccess: () => closeModal(),
    });
};

const selectNewPhoto = () => {
    photo.value.click();
};

const selectNewBackgroundPhoto = () => {
    bg_photo.value.click();
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

const updateBackgroundPhotoPreview = () => {
    const photoFile = bg_photo.value.files[0];

    if (!photoFile) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        bgPhotoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photoFile);
};

const clearPhotoFileInput = () => {
    photoPreview.value = null;
    bgPhotoPreview.value = null;

    if (photo.value?.value) {
        photo.value.value = null;
    }

    if (bg_photo.value?.value) {
        bg_photo.value.value = null;
    }
};

const closeModal = () => {
    clearPhotoFileInput();
    form.reset();
    form.clearErrors();
    emit("close", true);
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
