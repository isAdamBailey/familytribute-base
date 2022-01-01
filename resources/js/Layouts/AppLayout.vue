<template>
    <div>
        <app-head />

        <jet-banner />

        <div
            class="min-h-screen bg-gradient-to-r from-indigo-100 to-gray-100 dark:from-indigo-900 to-indigo-300"
        >
            <primary-nav />

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-white dark:bg-gray-900 shadow"
            >
                <div
                    class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 text-gray-800 dark:text-indigo-300 leading-tight"
                >
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-5 md:py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div
                            class="bg-white overflow-hidden shadow-xl shadow-indigo-200/50 rounded-lg"
                        >
                            <div
                                class="p-6 pb-20 sm:px-20 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-indigo-300"
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
