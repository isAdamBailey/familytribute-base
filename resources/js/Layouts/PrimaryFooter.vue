<template>
    <footer class="bg-white dark:bg-gray-900 border-t relative pt-1">
        <div class="container mx-auto px-6">
            <div
                v-if="!$page.props.user"
                class="flex justify-center items-center mt-8"
            >
                <Link
                    :href="route('login')"
                    class="text-gray-900 dark:text-indigo-300 underline hover:text-gray-500 dark:hover:text-gray-100"
                >
                    Log In
                </Link>

                <Link
                    v-if="$inertia.page.props.settings.registration"
                    :href="route('register')"
                    class="ml-4 text-gray-900 dark:text-indigo-300 underline hover:text-gray-500 dark:hover:text-gray-100"
                >
                    Register
                </Link>
            </div>
            <div
                class="mt-16 border-t-2 border-gray-300 dark:border-indigo-300 flex flex-col items-center"
            >
                <div class="sm:w-2/3 text-center py-6">
                    <p
                        class="text-sm text-indigo-700 dark:text-indigo-300 font-bold mb-2"
                    >
                        © {{ new Date().getFullYear() }} by
                        <a
                            class="text-indigo-700 dark:text-indigo-300 hover:text-indigo-800 dark:hover:text-indigo-500 hover:underline decoration-indigo-800"
                            href="https://familytribute.org"
                            >FamilyTribute</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
