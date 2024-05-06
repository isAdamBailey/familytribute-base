<template>
    <app-head title="Secure Area" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="password" value="Password" />
                <jet-input
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
                <base-button
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm
                </base-button>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
import { defineComponent } from "vue";
import JetAuthenticationCard from "@/Base/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Base/AuthenticationCardLogo.vue";
import JetInput from "@/Base/Input.vue";
import JetLabel from "@/Base/Label.vue";
import JetValidationErrors from "@/Base/ValidationErrors.vue";
import AppHead from "@/Layouts/AppHead.vue";

export default defineComponent({
    components: {
        AppHead,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetInput,
        JetLabel,
        JetValidationErrors,
    },

    data() {
        return {
            form: this.$inertia.form({
                password: "",
            }),
        };
    },

    methods: {
        submit() {
            this.form.post(this.route("password.confirm"), {
                onFinish: () => this.form.reset(),
            });
        },
    },
});
</script>
