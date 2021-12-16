<template>
    <home-layout title="Home">
        <div class="flex flex-col-reverse mb-5 md:mb-7 lg:flex-row">
            <div>
                <div class="font-bold text-2xl md:text-5xl">
                    Welcome to {{ $inertia.page.props.siteName }}
                </div>
                <div class="mt-6 text-gray-500 text-l md:text-2xl">
                    Hansen Farm provides a beautiful, robust starting point for
                    your next Laravel application. Laravel is designed to help
                    you build your application using a development environment
                </div>
            </div>
            <application-mark class="w-full md:w-1/2 mx-auto mb-6 lg:mb-0" />
        </div>

        <section class="grid lg:grid-cols-4 gap-1">
            <div v-if="pictures[0]" class="relative lg:col-span-2">
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[0].url"
                    :alt="pictures[0].title"
                />
                <div
                    class="absolute w-full py-2.5 bottom-0 inset-x-0 backdrop-blur-sm bg-white/30 text-white text-sm text-center leading-4"
                >
                    {{ pictures[0].description }}
                </div>
            </div>
            <div v-if="pictures[1]" class="relative">
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[1].url"
                    :alt="pictures[1].title"
                />
                <div
                    class="absolute w-full py-2.5 bottom-0 inset-x-0 backdrop-blur-sm bg-white/30 text-white text-sm text-center leading-4"
                >
                    {{ pictures[1].description }}
                </div>
            </div>
            <div
                v-if="pictures[2]"
                class="relative lg:row-start-2"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[2].url"
                    :alt="pictures[2].title"
                />
                <div
                    class="absolute w-full py-2.5 bottom-0 inset-x-0 backdrop-blur-sm bg-white/30 text-white text-sm text-center leading-4"
                >
                    {{ pictures[2].description }}
                </div>
            </div>
            <div
                v-if="pictures[3]"
                class="relative lg:col-span-2"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[3].url"
                    :alt="pictures[3].title"
                />
                <div
                    class="absolute w-full py-2.5 bottom-0 inset-x-0 backdrop-blur-sm bg-white/30 text-white text-sm text-center leading-4"
                >
                    {{ pictures[3].description }}
                </div>
            </div>
            <div
                v-if="pictures[4]"
                class="relative lg:row-start-1 lg:row-span-2 lg:col-start-4"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[4].url"
                    :alt="pictures[4].title"
                />
                <div
                    class="absolute w-full py-2.5 bottom-0 inset-x-0 backdrop-blur-sm bg-white/30 text-white text-sm text-center leading-4"
                >
                    {{ pictures[4].description }}
                </div>
            </div>
        </section>

        <div class="flex justify-center flex-wrap mt-10">
            <Link :href="route('people.index')">
                <base-button
                    class="justify-center w-48 sm:mr-5 mb-5"
                    type="button"
                    >People</base-button
                >
            </Link>
            <Link :href="route('pictures.index')">
                <base-button class="justify-center w-48" type="button"
                    >Pictures</base-button
                >
            </Link>
        </div>
    </home-layout>
</template>

<script>
import { defineComponent } from "vue";
import HomeLayout from "@/Layouts/HomeLayout";
import ApplicationMark from "@/Base/ApplicationMark";

export default defineComponent({
    components: {
        ApplicationMark,
        HomeLayout,
    },
    props: {
        pictures: Array,
    },
});
</script>
