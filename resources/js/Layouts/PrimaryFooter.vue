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
                  <base-button> Log In </base-button>
                </Link>

                <Link :href="route('register')" class="ml-4">
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
                            class="text-indigo-700 hover:text-indigo-800 hover:underline decoration-indigo-800"
                            href="https://familytribute.org"
                            >FamilyTribute</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
