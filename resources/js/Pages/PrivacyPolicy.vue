<template>
    <Head title="Privacy Policy" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="bg-gray-100 pt-4">
            <div class="flex min-h-screen flex-col items-center pt-6 sm:pt-0">
                <div>
                    <JetAuthenticationCardLogo />
                </div>

                <div
                    class="prose mt-6 w-full overflow-hidden bg-white p-6 shadow-md sm:max-w-2xl sm:rounded-lg"
                    v-html="policy"
                ></div>
            </div>
        </div>
    </div>
</template>

<script>
import JetAuthenticationCardLogo from "@/Base/AuthenticationCardLogo.vue";
import { Head } from "@inertiajs/vue3";
import { defineComponent } from "vue";

export default defineComponent({
    components: {
        Head,
        JetAuthenticationCardLogo,
    },
    props: {
        policy: String,
    },
});
</script>
