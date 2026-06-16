<template>
    <footer class="relative border-t bg-white pt-1 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <div v-if="!user" class="mt-8 flex items-center justify-center">
                <Link
                    :href="route('login')"
                    class="text-gray-900 underline hover:text-gray-500 dark:text-amber-300 dark:hover:text-gray-100"
                >
                    Log In
                </Link>

                <Link
                    v-if="settings.registration"
                    :href="route('register')"
                    class="ml-4 text-gray-900 underline hover:text-gray-500 dark:text-amber-300 dark:hover:text-gray-100"
                >
                    Register
                </Link>
            </div>
            <div
                class="mt-16 flex flex-col items-center border-t-2 border-gray-300 dark:border-amber-900"
            >
                <div class="py-6 text-center sm:w-2/3">
                    <p
                        class="mb-2 text-sm font-bold text-hearthlight-deep dark:text-amber-300"
                    >
                        © {{ currentYear }} by
                        <a
                            class="text-hearthlight-deep decoration-hearthlight-deep hover:text-hearthlight hover:underline dark:text-amber-300 dark:hover:text-amber-400"
                            href="https://adambailey.io"
                            >Adam Bailey</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const settings = computed(() => page.props.settings);
const currentYear = computed(() => new Date().getFullYear());
</script>
