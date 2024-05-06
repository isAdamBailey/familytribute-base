<template>
    <home-layout :title="title">
        <h2 class="text-3xl text-gray-700 dark:text-indigo-500">{{ title }}</h2>
        <div class="text-xl text-gray-700 dark:text-indigo-300">
            {{ description }}
        </div>
        <div class="flex items-baseline justify-center">
            <img class="w-3/4" :src="image" :alt="title" />
        </div>

        <div class="mt-10 flex justify-center">
            <Link :href="route('home')">
                <attention-button>Back Home?</attention-button>
            </Link>
        </div>
    </home-layout>
</template>

<script>
import { defineComponent } from "vue";
import HomeLayout from "@/Layouts/HomeLayout.vue";
import AttentionButton from "@/Base/AttentionButton.vue";

export default defineComponent({
    components: {
        AttentionButton,
        HomeLayout,
    },
    props: {
        status: Number,
    },
    computed: {
        title() {
            return {
                503: "503: Service Unavailable",
                500: "500: Server Error",
                404: "404: Page Not Found",
                403: "403: Forbidden",
            }[this.status];
        },
        description() {
            return {
                503: "Sorry, we are doing some maintenance. Please check back soon.",
                500: "Whoops, something went wrong on our servers.",
                404: "Sorry, the page you are looking for could not be found.",
                403: "Sorry, you are forbidden from accessing this page.",
            }[this.status];
        },
        image() {
            return {
                503: "/images/undraw_server_down_s-4-lk.svg",
                500: "/images/undraw_cancel_u-1-it.svg",
                404: "/images/undraw_page_not_found_re_e9o6.svg",
                403: "/images/undraw_access_denied_re_awnf.svg",
            }[this.status];
        },
    },
});
</script>
