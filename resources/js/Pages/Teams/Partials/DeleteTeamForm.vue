<template>
    <jet-action-section>
        <template #title> Delete Team </template>

        <template #description> Permanently delete this team. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once a team is deleted, all of its resources and data will be
                permanently deleted. Before deleting this team, please download
                any data or information regarding this team that you wish to
                retain.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmTeamDeletion">
                    Delete Team
                </jet-danger-button>
            </div>

            <!-- Delete Team Confirmation Modal -->
            <jet-confirmation-modal
                :show="confirmingTeamDeletion"
                @close="confirmingTeamDeletion = false"
            >
                <template #title> Delete Team </template>

                <template #content>
                    Are you sure you want to delete this team? Once a team is
                    deleted, all of its resources and data will be permanently
                    deleted.
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click="confirmingTeamDeletion = false"
                    >
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteTeam"
                    >
                        Delete Team
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </jet-action-section>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import JetActionSection from "@/Base/ActionSection.vue";
import JetConfirmationModal from "@/Base/ConfirmationModal.vue";
import JetDangerButton from "@/Base/DangerButton.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";

const props = defineProps({
    team: Object,
});

const form = useForm({});
const confirmingTeamDeletion = ref(false);

const confirmTeamDeletion = () => {
    confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
    form.delete(route("teams.destroy", props.team), {
        errorBag: "deleteTeam",
    });
};
</script>
