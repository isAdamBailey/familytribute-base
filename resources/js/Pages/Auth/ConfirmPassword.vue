<template>
    <AppHead title="Secure Area" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <JetValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="password" value="Password" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                />
            </div>

            <div class="mt-4 flex justify-end">
                <BaseButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm
                </BaseButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import JetAuthenticationCard from "@/Base/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Base/AuthenticationCardLogo.vue";
import JetInput from "@/Base/Input.vue";
import JetLabel from "@/Base/Label.vue";
import JetValidationErrors from "@/Base/ValidationErrors.vue";
import AppHead from "@/Layouts/AppHead.vue";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>
