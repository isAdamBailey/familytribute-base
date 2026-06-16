<template>
    <div>
        <AppHead />

        <JetBanner />

        <div
            class="min-h-screen bg-gradient-to-br from-amber-50 via-stone-50 to-amber-100 dark:from-stone-950 dark:via-stone-900 dark:to-amber-950"
        >
            <PrimaryNav />

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-white shadow dark:bg-gray-900"
            >
                <div
                    class="mx-auto max-w-7xl py-3 px-4 leading-tight text-gray-800 dark:text-amber-300 sm:px-6 lg:px-8"
                >
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-5 md:py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div
                            class="overflow-hidden rounded-lg bg-white shadow-xl shadow-amber-200/50"
                        >
                            <div
                                class="border-b border-gray-200 bg-white p-6 pb-20 dark:border-stone-700 dark:bg-gray-800 sm:px-20"
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
import JetBanner from "@/Base/Banner.vue";
import PrimaryNav from "@/Layouts/Nav/PrimaryNav.vue";
import PrimaryFooter from "@/Layouts/PrimaryFooter.vue";
import AppHead from "@/Layouts/AppHead.vue";
</script>
