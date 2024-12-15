<template>
    <JetActionSection>
        <template #title> Delete Account </template>

        <template #description> Permanently delete your account. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <JetDangerButton @click="confirmUserDeletion">
                    Delete Account
                </JetDangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <JetDialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title> Delete Account </template>

                <template #content>
                    Are you sure you want to delete your account? Once your
                    account is deleted, all of its resources and data will be
                    permanently deleted. Please enter your password to confirm
                    you would like to permanently delete your account.

                    <div class="mt-4">
                        <JetInput
                            ref="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            @keyup.enter="deleteUser"
                        />

                        <JetInputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #footer>
                    <JetSecondaryButton @click="closeModal">
                        Cancel
                    </JetSecondaryButton>

                    <JetDangerButton
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </JetDangerButton>
                </template>
            </JetDialogModal>
        </template>
    </JetActionSection>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import JetActionSection from "@/Base/ActionSection.vue";
import JetDialogModal from "@/Base/DialogModal.vue";
import JetDangerButton from "@/Base/DangerButton.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetSecondaryButton from "@/Base/SecondaryButton.vue";

const confirmingUserDeletion = ref(false);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => form.refs.password.focus(), 250);
};

const deleteUser = () => {
    form.delete(route("current-user.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => form.refs.password.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>
