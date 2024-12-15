<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <JetSectionBorder />

            <!-- Add Team Member -->
            <JetFormSection @submitted="addTeamMember">
                <template #title> Add Team Member </template>

                <template #description>
                    Add a new team member to your team, allowing them to
                    collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the email address of the person you
                            would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <JetLabel for="email" value="Email" />
                        <JetInput
                            id="email"
                            v-model="addTeamMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <JetInputError
                            :message="addTeamMemberForm.errors.email"
                            class="mt-2"
                        />
                    </div>

                    <!-- Role -->
                    <div
                        v-if="
                            availableRoles.length > 0 &&
                            userPermissions.canRemoveTeamMembers
                        "
                        class="col-span-6 lg:col-span-4"
                    >
                        <JetLabel for="roles" value="Role" />
                        <JetInputError
                            :message="addTeamMemberForm.errors.role"
                            class="mt-2"
                        />

                        <div
                            class="relative z-0 mt-1 cursor-pointer rounded-lg border border-gray-200"
                        >
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                                type="button"
                                class="relative inline-flex w-full rounded-lg px-4 py-3 focus:z-10 focus:border-blue-300 focus:outline-none focus:ring focus:ring-blue-200"
                                :class="{
                                    'rounded-t-none border-t border-gray-200':
                                        i > 0,
                                    'rounded-b-none':
                                        i !==
                                        Object.keys(availableRoles).length - 1,
                                }"
                                @click="addTeamMemberForm.role = role.key"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            addTeamMemberForm.role &&
                                            addTeamMemberForm.role !== role.key,
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-gray-600"
                                            :class="{
                                                'font-semibold':
                                                    addTeamMemberForm.role ===
                                                    role.key,
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <svg
                                            v-if="
                                                addTeamMemberForm.role ===
                                                role.key
                                            "
                                            class="ml-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div
                                        class="mt-2 text-left text-xs text-gray-600"
                                    >
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <JetActionMessage
                        :on="addTeamMemberForm.recentlySuccessful"
                        class="mr-3"
                    >
                        Invite sent.
                    </JetActionMessage>

                    <JetButton
                        :class="{ 'opacity-25': addTeamMemberForm.processing }"
                        :disabled="addTeamMemberForm.processing"
                    >
                        Invite
                    </JetButton>
                </template>
            </JetFormSection>
        </div>

        <div
            v-if="
                team.team_invitations.length > 0 &&
                userPermissions.canAddTeamMembers
            "
        >
            <JetSectionBorder />

            <!-- Team Member Invitations -->
            <JetActionSection class="mt-10 sm:mt-0">
                <template #title> Pending Team Invitations </template>

                <template #description>
                    These people have been invited to your team and have been
                    sent an invitation email. They may join the team by
                    accepting the email invitation.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            v-for="invitation in team.team_invitations"
                            :key="invitation.id"
                            class="flex items-center justify-between"
                        >
                            <div class="text-gray-600">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="ml-6 cursor-pointer text-sm text-red-500 focus:outline-none"
                                    @click="cancelTeamInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </JetActionSection>
        </div>

        <div v-if="team.users.length > 0">
            <JetSectionBorder />

            <!-- Manage Team Members -->
            <JetActionSection class="mt-10 sm:mt-0">
                <template #title> Team Members </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            v-for="user in team.users"
                            :key="user.id"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center">
                                <img
                                    class="h-8 w-8 rounded-full"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <div class="ml-4">{{ user.name }}</div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button
                                    v-if="
                                        userPermissions.canAddTeamMembers &&
                                        userPermissions.canRemoveTeamMembers &&
                                        availableRoles.length
                                    "
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div
                                    v-else-if="availableRoles.length"
                                    class="ml-2 text-sm text-gray-400"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Team -->
                                <button
                                    v-if="page.props.auth.user.id === user.id"
                                    class="ml-6 cursor-pointer text-sm text-red-500"
                                    @click="confirmLeavingTeam"
                                >
                                    Leave
                                </button>

                                <!-- Remove Team Member -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="ml-6 cursor-pointer text-sm text-red-500"
                                    @click="confirmTeamMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </JetActionSection>
        </div>

        <!-- Role Management Modal -->
        <JetDialogModal
            :show="currentlyManagingRole"
            @close="currentlyManagingRole = false"
        >
            <template #title> Manage Role </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div
                        class="relative z-0 mt-1 cursor-pointer rounded-lg border border-gray-200"
                    >
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative inline-flex w-full rounded-lg px-4 py-3 focus:z-10 focus:border-blue-300 focus:outline-none focus:ring focus:ring-blue-200"
                            :class="{
                                'rounded-t-none border-t border-gray-200':
                                    i > 0,
                                'rounded-b-none':
                                    i !==
                                    Object.keys(availableRoles).length - 1,
                            }"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div
                                :class="{
                                    'opacity-50':
                                        updateRoleForm.role &&
                                        updateRoleForm.role !== role.key,
                                }"
                            >
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        :class="{
                                            'font-semibold':
                                                updateRoleForm.role ===
                                                role.key,
                                        }"
                                    >
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="updateRoleForm.role === role.key"
                                        class="ml-2 h-5 w-5 text-green-400"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <JetSecondaryButton @click="currentlyManagingRole = false">
                    Cancel
                </JetSecondaryButton>

                <JetButton
                    class="ml-2"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </JetButton>
            </template>
        </JetDialogModal>

        <!-- Leave Team Confirmation Modal -->
        <JetConfirmationModal
            :show="confirmingLeavingTeam"
            @close="confirmingLeavingTeam = false"
        >
            <template #title> Leave Team </template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <JetSecondaryButton @click="confirmingLeavingTeam = false">
                    Cancel
                </JetSecondaryButton>

                <JetDangerButton
                    class="ml-2"
                    :class="{ 'opacity-25': leaveTeamForm.processing }"
                    :disabled="leaveTeamForm.processing"
                    @click="leaveTeam"
                >
                    Leave
                </JetDangerButton>
            </template>
        </JetConfirmationModal>

        <!-- Remove Team Member Confirmation Modal -->
        <JetConfirmationModal
            :show="teamMemberBeingRemoved"
            @close="teamMemberBeingRemoved = null"
        >
            <template #title> Remove Team Member </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <JetSecondaryButton @click="teamMemberBeingRemoved = null">
                    Cancel
                </JetSecondaryButton>

                <JetDangerButton
                    class="ml-2"
                    :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                    :disabled="removeTeamMemberForm.processing"
                    @click="removeTeamMember"
                >
                    Remove
                </JetDangerButton>
            </template>
        </JetConfirmationModal>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import JetActionMessage from "@/Base/ActionMessage.vue";
import JetActionSection from "@/Base/ActionSection.vue";
import JetButton from "@/Base/Button.vue";
import JetConfirmationModal from "@/Base/ConfirmationModal.vue";
import JetDangerButton from "@/Base/DangerButton.vue";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";
import JetSectionBorder from "@/Base/SectionBorder.vue";

defineProps({
    team: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const page = usePage();
const addTeamMemberForm = useForm({
    email: "",
    role: "editor",
});
const updateRoleForm = useForm({
    role: null,
});
const leaveTeamForm = useForm({});
const removeTeamMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref(null);

const addTeamMember = () => {
    addTeamMemberForm.post(route("team-members.store", page.props.team), {
        errorBag: "addTeamMember",
        preserveScroll: true,
        onSuccess: () => addTeamMemberForm.reset(),
    });
};

const cancelTeamInvitation = (invitation) => {
    router.delete(route("team-invitations.destroy", invitation), {
        preserveScroll: true,
    });
};

const manageRole = (teamMember) => {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(
        route("team-members.update", [page.props.team, managingRoleFor.value]),
        {
            preserveScroll: true,
            onSuccess: () => (currentlyManagingRole.value = false),
        },
    );
};

const confirmLeavingTeam = () => {
    confirmingLeavingTeam.value = true;
};

const leaveTeam = () => {
    leaveTeamForm.delete(
        route("team-members.destroy", [page.props.team, page.props.auth.user]),
    );
};

const confirmTeamMemberRemoval = (teamMember) => {
    teamMemberBeingRemoved.value = teamMember;
};

const removeTeamMember = () => {
    removeTeamMemberForm.delete(
        route("team-members.destroy", [
            page.props.team,
            teamMemberBeingRemoved.value,
        ]),
        {
            errorBag: "removeTeamMember",
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => (teamMemberBeingRemoved.value = null),
        },
    );
};

const displayableRole = (role) => {
    return page.props.availableRoles.find((r) => r.key === role).name;
};
</script>
