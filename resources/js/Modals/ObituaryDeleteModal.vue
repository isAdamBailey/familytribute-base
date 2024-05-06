<template>
    <jet-confirmation-modal :show="open" @close="closeModal()">
        <template #title> Delete Person </template>

        <template #content>
            Are you sure you want to delete this person? Once the person is
            deleted, their obituary, and all of their connections to stories and
            pictures will also be permanently removed.
        </template>

        <template #footer>
            <jet-secondary-button @click="closeModal()">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteObituary"
            >
                Delete Person
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import { defineComponent } from "vue";
import JetConfirmationModal from "@/Base/ConfirmationModal.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetDangerButton from "@/Base/DangerButton.vue";

export default defineComponent({
    components: { JetConfirmationModal, JetSecondaryButton, JetDangerButton },
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        person: {
            type: Object,
            required: true,
        },
    },
    emits: ["close"],
    data() {
        return {
            form: this.$inertia.form({
                _method: "DELETE",
            }),
        };
    },
    methods: {
        deleteObituary() {
            this.form.delete(
                route("obituaries.destroy", this.person.obituary.id),
                {
                    errorBag: "destroyObituary",
                    onSuccess: () => this.closeModal(),
                }
            );
        },
        closeModal() {
            this.form.clearErrors();
            this.form.reset();
            this.$emit("close", true);
        },
    },
});
</script>
