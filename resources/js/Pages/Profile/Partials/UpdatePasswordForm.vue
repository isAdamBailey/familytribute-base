<template>
    <JetFormSection @submitted="updatePassword">
        <template #title> Update Password </template>

        <template #description>
            Ensure your account is using a long, random password to stay secure.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="current_password" value="Current Password" />
                <JetInput
                    id="current_password"
                    ref="current_password"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <JetInputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="password" value="New Password" />
                <JetInput
                    id="password"
                    ref="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <JetInputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <JetLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <JetInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <JetInputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <JetActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </JetActionMessage>

            <BaseButton
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </BaseButton>
        </template>
    </JetFormSection>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import JetActionMessage from "@/Base/ActionMessage.vue";
import JetFormSection from "@/Base/FormSection.vue";
import JetInput from "@/Base/Input.vue";
import JetInputError from "@/Base/InputError.vue";
import JetLabel from "@/Base/Label.vue";

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("user-password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            console.log(form.errors);
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                form.refs.password.focus();
            }

            if (form.errors.current_password) {
                form.reset("current_password");
                form.refs.current_password.focus();
            }
        },
    });
};
</script>
