<template>
    <JetConfirmationModal :show="open" @close="closeModal()">
        <template #title> Delete Story </template>

        <template #content>
            Are you sure you want to delete this story? Once the story is
            deleted, all of its connections to people will also be permanently
            deleted.
        </template>

        <template #footer>
            <JetSecondaryButton @click="closeModal()">
                Nevermind
            </JetSecondaryButton>

            <JetDangerButton
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteStory"
            >
                Delete Story
            </JetDangerButton>
        </template>
    </JetConfirmationModal>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import JetConfirmationModal from "@/Base/ConfirmationModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetDangerButton from "@/Base/DangerButton.vue";

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    story: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "DELETE",
});

const deleteStory = () => {
    form.delete(route("stories.destroy", props.story.slug), {
        errorBag: "destroyStory",
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    emit("close", true);
};
</script>
