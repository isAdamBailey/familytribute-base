<template>
    <footer class="relative border-t bg-white pt-1 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div v-if="!user" class="mt-8 flex items-center justify-center">
                <Link
                    :href="route('login')"
                    class="text-gray-900 underline hover:text-gray-500 dark:text-indigo-300 dark:hover:text-gray-100"
                >
                    Log In
                </Link>

                <Link
                    v-if="settings.registration"
                    :href="route('register')"
                    class="ml-4 text-gray-900 underline hover:text-gray-500 dark:text-indigo-300 dark:hover:text-gray-100"
                >
                    Register
                </Link>
            </div>
            <div
                class="mt-16 flex flex-col items-center border-t-2 border-gray-300 dark:border-indigo-300"
            >
                <div class="py-6 text-center sm:w-2/3">
                    <p
                        class="mb-2 text-sm font-bold text-indigo-700 dark:text-indigo-300"
                    >
                        © {{ currentYear }} by
                        <a
                            class="text-indigo-700 decoration-indigo-800 hover:text-indigo-800 hover:underline dark:text-indigo-300 dark:hover:text-indigo-500"
                            href="https://familytribute.org"
                            >FamilyTribute</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth.user);
const settings = computed(() => page.props.settings);
const currentYear = computed(() => new Date().getFullYear());
</script>
