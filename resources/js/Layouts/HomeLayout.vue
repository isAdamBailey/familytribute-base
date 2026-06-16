<template>
    <div>
        <AppHead :title="title" />

        <div
            class="min-h-screen bg-gradient-to-br from-amber-50 via-stone-50 to-amber-100 dark:from-stone-950 dark:via-stone-900 dark:to-amber-950"
        >
            <PrimaryNav />
            <!-- Page Content -->
            <main>
                <div class="md:py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div
                            class="overflow-hidden rounded-lg shadow-xl shadow-amber-200/50"
                        >
                            <div
                                class="border-b border-gray-200 bg-white p-2 pb-20 dark:border-stone-700 dark:bg-gray-800 sm:px-20"
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
