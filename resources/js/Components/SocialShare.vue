<template>
    <div class="my-3 w-60 flex justify-between items-center">
        <div class="font-semibold text-xl">Share:</div>
        <ShareNetwork network="facebook" :title="title" :url="currentPage">
            <i
                role="button"
                aria-label="Share on Facebook"
                class="ri-facebook-circle-fill text-5xl text-indigo-500 hover:text-indigo-700"
            ></i>
        </ShareNetwork>
        <ShareNetwork
            network="twitter"
            twitter-user="FamilyTribute"
            :title="title"
            :url="currentPage"
        >
            <i
                role="button"
                aria-label="Share on Twitter"
                class="ri-twitter-fill text-5xl text-indigo-500 hover:text-indigo-700"
            ></i>
        </ShareNetwork>
        <i
            role="button"
            aria-label="Share on email"
            class="ri-mail-fill text-5xl text-indigo-500 hover:text-indigo-700"
        ></i>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        title: String,
    },
    computed: {
        currentPage() {
            return window.location.href;
        },
    },
});
</script>
