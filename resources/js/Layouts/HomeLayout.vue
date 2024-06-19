<template>
    <div>
        <AppHead :title="title" />

        <div
            class="min-h-screen bg-gradient-to-r from-indigo-100 to-gray-100 to-indigo-300 dark:from-indigo-900"
        >
            <PrimaryNav />
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

            <PrimaryFooter />
        </div>
    </div>
</template>

<script setup>
import PrimaryFooter from "@/Layouts/PrimaryFooter.vue";
import PrimaryNav from "@/Layouts/Nav/PrimaryNav.vue";
import AppHead from "@/Layouts/AppHead.vue";

defineProps({
    title: String,
});
</script>
