<template>
    <div>
        <app-head :title="title" />

        <div
            class="min-h-screen bg-gradient-to-r from-indigo-100 to-gray-100 to-indigo-300 dark:from-indigo-900"
        >
            <primary-nav />
            <!-- Page Content -->
            <main>
                <div class="md:py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div
                            class="overflow-hidden rounded-lg shadow-xl shadow-indigo-200/50"
                        >
                            <div
                                class="border-b border-gray-200 bg-white p-2 pb-20 dark:border-indigo-900 dark:bg-gray-800 sm:px-20"
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
import PrimaryFooter from "@/Layouts/PrimaryFooter";
import PrimaryNav from "@/Layouts/Nav/PrimaryNav";
import AppHead from "@/Layouts/AppHead";

export default defineComponent({
    components: {
        AppHead,
        PrimaryNav,
        PrimaryFooter,
    },
    props: {
        title: String,
    },
});
</script>
