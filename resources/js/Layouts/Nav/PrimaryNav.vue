<template>
    <nav
        class="border-b border-gray-100 bg-white dark:border-indigo-900 dark:bg-gray-900"
    >
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <!-- Logo -->
                    <Link class="flex items-center" :href="route('home')">
                        <jet-application-mark class="block h-9 w-auto" />
                        <div
                            class="font-header text-3xl font-bold dark:text-indigo-500 md:text-5xl"
                        >
                            {{ page.props.settings.title }}
                        </div>
                    </Link>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <jet-nav-link
                            :href="route('people.index')"
                            :active="route().current('people.*')"
                        >
                            People
                        </jet-nav-link>
                        <jet-nav-link
                            :href="route('pictures.index')"
                            :active="route().current('pictures.*')"
                        >
                            Pictures
                        </jet-nav-link>
                        <jet-nav-link
                            :href="route('stories.index')"
                            :active="route().current('stories.*')"
                        >
                            Stories
                        </jet-nav-link>
                    </div>
                </div>

                <theme-toggle class="" />

                <div
                    v-if="page.props.auth.user"
                    class="hidden sm:ml-6 sm:flex sm:items-center"
                >
                    <!-- Settings Dropdown -->
                    <div class="relative ml-3">
                        <jet-dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    v-if="
                                        page.props.jetstream
                                            .managesProfilePhotos
                                    "
                                    class="flex rounded-full border-2 border-transparent text-sm transition focus:border-gray-300 focus:outline-none"
                                >
                                    <img
                                        class="h-8 w-8 rounded-full object-cover"
                                        :src="
                                            page.props.auth.user
                                                .profile_photo_url
                                        "
                                        :alt="page.props.auth.user.name"
                                    />
                                </button>

                                <span v-else class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition hover:text-gray-700 focus:outline-none"
                                    >
                                        {{ page.props.auth.user.name }}

                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <!-- Site Management -->
                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Manage Site
                                </div>

                                <jet-dropdown-link :href="route('dashboard')">
                                    Dashboard
                                </jet-dropdown-link>

                                <div v-if="page.props.auth.user.current_team">
                                    <div
                                        class="block px-4 py-2 text-xs text-gray-400"
                                    >
                                        Manage Team
                                    </div>

                                    <!-- Team Settings -->
                                    <jet-dropdown-link
                                        :href="
                                            route(
                                                'teams.show',
                                                page.props.auth.user
                                                    .current_team,
                                            )
                                        "
                                    >
                                        Team Settings
                                    </jet-dropdown-link>
                                </div>

                                <!-- Account Management -->
                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Manage Account
                                </div>

                                <jet-dropdown-link
                                    :href="route('profile.show')"
                                >
                                    Profile
                                </jet-dropdown-link>

                                <jet-dropdown-link
                                    v-if="page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')"
                                >
                                    API Tokens
                                </jet-dropdown-link>

                                <div class="border-t border-gray-100"></div>

                                <!-- Authentication -->
                                <form @submit.prevent="logout">
                                    <jet-dropdown-link as="button">
                                        Log Out
                                    </jet-dropdown-link>
                                </form>
                            </template>
                        </jet-dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-500 focus:text-gray-900 focus:outline-none dark:hover:bg-indigo-300 dark:hover:text-gray-900 dark:focus:bg-indigo-300"
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="sm:hidden"
        >
            <div class="space-y-1 pt-2 pb-3">
                <jet-responsive-nav-link
                    :href="route('people.index')"
                    :active="route().current('people.*')"
                >
                    People
                </jet-responsive-nav-link>
                <jet-responsive-nav-link
                    :href="route('pictures.index')"
                    :active="route().current('pictures.*')"
                >
                    Pictures
                </jet-responsive-nav-link>
                <jet-responsive-nav-link
                    :href="route('stories.index')"
                    :active="route().current('stories.*')"
                >
                    Stories
                </jet-responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div
                v-if="page.props.auth.user"
                class="border-t border-gray-200 pt-4 pb-1"
            >
                <div class="flex items-center px-4">
                    <div
                        v-if="page.props.jetstream.managesProfilePhotos"
                        class="mr-3 flex-shrink-0"
                    >
                        <img
                            class="h-10 w-10 rounded-full object-cover"
                            :src="page.props.auth.user.profile_photo_url"
                            :alt="page.props.auth.user.name"
                        />
                    </div>

                    <div>
                        <div class="text-base font-medium text-gray-800">
                            {{ page.props.auth.user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">
                            {{ page.props.auth.user.email }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <jet-responsive-nav-link
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        Dashboard
                    </jet-responsive-nav-link>
                    <jet-responsive-nav-link
                        :href="route('profile.show')"
                        :active="route().current('profile.show')"
                    >
                        Profile
                    </jet-responsive-nav-link>

                    <jet-responsive-nav-link
                        v-if="page.props.jetstream.hasApiFeatures"
                        :href="route('api-tokens.index')"
                        :active="route().current('api-tokens.index')"
                    >
                        API Tokens
                    </jet-responsive-nav-link>

                    <jet-responsive-nav-link
                        v-if="page.props.auth.user.current_team"
                        :href="
                            route(
                                'teams.show',
                                page.props.auth.user.current_team,
                            )
                        "
                        :active="route().current('teams.show')"
                    >
                        Team Settings
                    </jet-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" @submit.prevent="logout">
                        <jet-responsive-nav-link as="button">
                            Log Out
                        </jet-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import JetApplicationMark from "@/Base/ApplicationMark.vue";
import JetDropdown from "@/Base/Dropdown.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import JetNavLink from "@/Base/NavLink.vue";
import JetResponsiveNavLink from "@/Base/ResponsiveNavLink.vue";
import ThemeToggle from "@/Layouts/Nav/ThemeToggle.vue";

const page = usePage();
const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        },
    );
};
</script>
