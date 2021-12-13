<template>
    <footer class="bg-white border-t relative pt-1">
        <div class="container mx-auto px-6">
            <div
                v-if="!$page.props.user"
                class="flex justify-center items-center mt-8"
            >
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-700 underline"
                >
                    Log in
                </Link>

                <Link
                    :href="route('register')"
                    class="ml-4 text-sm text-gray-700 underline"
                >
                    Register
                </Link>
            </div>
            <div
                class="mt-16 border-t-2 border-gray-300 flex flex-col items-center"
            >
                <div class="sm:w-2/3 text-center py-6">
                    <p class="text-sm font-bold mb-2">
                        © {{ new Date().getFullYear() }} by
                        <a
                            class="text-purple-700 hover:text-purple-800"
                            href="https://historyfam.com"
                            >HistoryFam</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
