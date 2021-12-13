<template>
    <home-layout title="Home">
        <h2 class="text-2xl text-gray-700">Hmmmm...</h2>
        <div class="flex justify-center items-baseline">
            <img
                class="w-3/4"
                src="/images/undraw_page_not_found_re_e9o6.svg"
                alt="404 - Page Not Found"
            />
        </div>

        <div class="flex justify-center mt-10">
            <Link :href="route('home')">
                <base-button
                    class="justify-center w-48 mr-5 mb-5"
                    type="button"
                >
                    Back Home?
                </base-button>
            </Link>
        </div>
    </home-layout>
</template>

<script>
import { defineComponent } from "vue";
import HomeLayout from "@/Layouts/HomeLayout";

export default defineComponent({
    components: {
        HomeLayout,
    },
});
</script>
