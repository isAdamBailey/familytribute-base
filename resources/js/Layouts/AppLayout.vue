<template>
    <div>
        <app-head />

        <jet-banner />

        <div
            class="min-h-screen bg-gradient-to-r from-indigo-100 to-gray-100 to-indigo-300 dark:from-indigo-900"
        >
            <primary-nav />

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-white shadow dark:bg-gray-900"
            >
                <div
                    class="mx-auto max-w-7xl py-3 px-4 leading-tight text-gray-800 dark:text-indigo-300 sm:px-6 lg:px-8"
                >
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-5 md:py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div
                            class="overflow-hidden rounded-lg bg-white shadow-xl shadow-indigo-200/50"
                        >
                            <div
                                class="border-b border-gray-200 bg-white p-6 pb-20 dark:border-indigo-300 dark:bg-gray-800 sm:px-20"
                            >
                                <slot></slot>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <primary-footer />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetBanner from "@/Base/Banner.vue";
import PrimaryNav from "@/Layouts/Nav/PrimaryNav";
import PrimaryFooter from "@/Layouts/PrimaryFooter";
import AppHead from "@/Layouts/AppHead";

export default defineComponent({
    components: {
        AppHead,
        PrimaryNav,
        PrimaryFooter,
        JetBanner,
    },
});
</script>
