<template>
    <JetDialogModal :show="open" @close="closeModal()">
        <template #title> Edit Story </template>

        <template #content>
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
                <JetLabel for="excerpt" value="Excerpt" />
                <wysiwyg v-model="form.excerpt" :excerpt="true" />
                <JetInputError :message="form.errors.excerpt" class="mt-2" />
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
                <JetLabel for="content" value="Story" />
                <wysiwyg v-model="form.content" />
                <JetInputError :message="form.errors.content" class="mt-2" />
            </div>

            <div class="-mx-3 flex flex-wrap">
                <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                    <JetLabel for="private" value="Private" />
                    <InfoText>
                        Private stories will only appear for registered users
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
                <div class="mb-6 w-full px-3 md:mb-0 md:w-1/2">
                    <JetLabel for="year" value="Year" />
                    <JetInput
                        id="year"
                        v-model="form.year"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <JetInputError :message="form.errors.year" class="mt-2" />
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
                @click="updateStory"
            >
                Update Story
            </BaseButton>
        </template>
    </JetDialogModal>
</template>

<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import Wysiwyg from "@/Base/Wysiwyg.vue";
import InfoText from "@/Base/InfoText.vue";
import Checkbox from "@/Base/Checkbox.vue";

const props = defineProps({
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
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "PUT",
    title: props.story.title,
    excerpt: props.story.excerpt,
    content: props.story.content,
    year: props.story.year,
    private: Boolean(props.story.private),
    person_ids: props.story.person_ids,
});

const peopleOptions = computed(() => {
    return props.people.map((person) => {
        return {
            value: person.id,
            label: person.full_name,
        };
    });
});

const updateStory = () => {
    form.post(route("stories.update", props.story.slug), {
        errorBag: "updateStory",
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit("close", true);
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
