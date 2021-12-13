<template>
    <jet-confirmation-modal :show="open" @close="closeModal()">
        <template #title> Delete Story </template>

        <template #content>
            Are you sure you want to delete this story? Once the story is
            deleted, all of its connections to people will also be permanently
            deleted.
        </template>

        <template #footer>
            <jet-secondary-button @click="closeModal()">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button
                class="ml-2"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteStory"
            >
                Delete Story
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import { defineComponent } from "vue";
import JetConfirmationModal from "@/Base/ConfirmationModal";
import JetSecondaryButton from "@/Base/SecondaryButton";
import JetDangerButton from "@/Base/DangerButton";

export default defineComponent({
    components: { JetConfirmationModal, JetSecondaryButton, JetDangerButton },
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        story: {
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
        deleteStory() {
            this.form.delete(route("stories.destroy", this.story.slug), {
                errorBag: "destroyStory",
                onSuccess: () => this.closeModal(),
            });
        },
        closeModal() {
            this.form.clearErrors();
            this.form.reset();
            this.$emit("close", true);
        },
    },
});
</script>
