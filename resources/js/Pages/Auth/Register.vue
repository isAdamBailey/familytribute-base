<template>
    <AppHead title="Register" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <JetValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="name" value="Name" />
                <JetInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
            </div>

            <div class="mt-4">
                <JetLabel for="email" value="Email" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                />
            </div>

            <div class="mt-4">
                <JetLabel for="password" value="Password" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4">
                <JetLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <JetInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4">
                <JetLabel
                    for="registration_secret"
                    value="Registration Secret"
                />
                <JetInput
                    id="registration_secret"
                    v-model="form.registration_secret"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
            </div>

            <div v-if="jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <JetLabel for="terms">
                    <div class="flex items-center">
                        <JetCheckbox
                            id="terms"
                            v-model:checked="form.terms"
                            name="terms"
                        />

                        <div class="ml-2">
                            I agree to the
                            <a
                                target="_blank"
                                :href="route('terms.show')"
                                class="text-sm text-gray-600 underline hover:text-gray-900"
                                >Terms of Service</a
                            >
                            and
                            <a
                                target="_blank"
                                :href="route('policy.show')"
                                class="text-sm text-gray-600 underline hover:text-gray-900"
                                >Privacy Policy</a
                            >
                        </div>
                    </div>
                </JetLabel>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Already registered?
                </Link>

                <BaseButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </BaseButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>

<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import JetAuthenticationCard from "@/Base/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Base/AuthenticationCardLogo.vue";
import JetInput from "@/Base/Input.vue";
import JetCheckbox from "@/Base/Checkbox.vue";
import JetLabel from "@/Base/Label.vue";
import JetValidationErrors from "@/Base/ValidationErrors.vue";
import AppHead from "@/Layouts/AppHead.vue";

const jetstream = usePage().props.jetstream;

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    registration_secret: "",
    terms: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>
