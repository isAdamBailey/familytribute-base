<template>
    <app-head title="Email Verification" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-green-600"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <base-button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </base-button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-gray-600 underline hover:text-gray-900"
                    >Log Out</Link
                >
            </div>
        </form>
    </jet-authentication-card>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

import JetAuthenticationCard from "@/Base/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Base/AuthenticationCardLogo.vue";
import AppHead from "@/Layouts/AppHead.vue";
import { computed } from "vue";

const props = defineProps({
    status: { type: String, default: null },
});

const form = useForm();

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);

const submit = () => {
    form.post(route("verification.send"));
};
</script>
