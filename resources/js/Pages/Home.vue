<template>
    <home-layout title="Home">
        <div class="flex flex-col-reverse mb-10 lg:flex-row lg:mb-24">
            <div>
                <div class="font-bold text-2xl md:text-5xl">
                    Welcome to {{ $inertia.page.props.siteName }}
                </div>
                <div class="mt-6 text-gray-500 text-l md:text-2xl">
                    Hansen Farm provides a beautiful, robust starting point for
                    your next Laravel application. Laravel is designed to help
                    you build your application using a development environment
                    that is simple, powerful, and enjoyable. We believe you
                    should love expressing your creativity through programming,
                    so we have spent time carefully crafting the Laravel
                    ecosystem to be a breath of fresh air. We hope you love it.
                </div>
            </div>
            <application-mark class="w-full md:w-1/2 mx-auto mb-6 lg:mb-0" />
        </div>

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
});
</script>
