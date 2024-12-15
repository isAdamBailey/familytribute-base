<template>
    <JetConfirmationModal :show="open" @close="closeModal()">
        <template #title> Delete Picture </template>

        <template #content>
            Are you sure you want to delete this picture? Once the picture is
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
                @click="deletePicture"
            >
                Delete Picture
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
    picture: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "DELETE",
});

const deletePicture = () => {
    form.delete(route("pictures.destroy", props.picture.slug), {
        errorBag: "destroyPicture",
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    emit("close", true);
};
</script>
