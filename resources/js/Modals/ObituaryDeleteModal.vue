<template>
    <JetConfirmationModal :show="open" @close="closeModal()">
        <template #title> Delete Person </template>

        <template #content>
            Are you sure you want to delete this person? Once the person is
            deleted, their obituary, and all of their connections to stories and
            pictures will also be permanently removed.
        </template>

        <template #footer>
            <JetSecondaryButton @click="closeModal()">
                Nevermind
            </JetSecondaryButton>

            <JetDangerButton
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteObituary"
            >
                Delete Person
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
    person: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    _method: "DELETE",
});

const deleteObituary = () => {
    form.delete(route("obituaries.destroy", props.person.obituary.id), {
        errorBag: "destroyObituary",
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    emit("close", true);
};
</script>
