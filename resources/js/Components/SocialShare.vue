<template>
    <div class="flex flex-col items-center justify-between">
        <span class="text-sm text-gray-500">Share by clicking the links below</span>
        <div class="flex items-center justify-between space-x-3 md:w-60">
        <ShareNetwork
            network="facebook"
            :quote="title"
            :title="title"
            :description="description"
            :url="currentPage"
        >
            <i
                role="button"
                title="Share on Facebook"
                aria-label="Share on Facebook"
                class="ri-facebook-circle-fill text-3xl text-indigo-500 hover:text-indigo-700 md:text-4xl"
            ></i>
        </ShareNetwork>
        <ShareNetwork
            network="messenger"
            :url="currentPage"
        >
            <i
                role="button"
                title="Share on Facebook Messenger"
                aria-label="Share on Facebook Messenger"
                class="ri-messenger-fill text-3xl text-indigo-500 hover:text-indigo-700 md:text-4xl"
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
                title="Share on Twitter"
                aria-label="Share on Twitter"
                class="ri-twitter-fill text-3xl text-indigo-500 hover:text-indigo-700 md:text-4xl"
            ></i>
        </ShareNetwork>
        <ShareNetwork
            network="email"
            :title="title"
            :url="currentPage"
            :description="description"
        >
            <i
                role="button"
                title="Share on email"
                aria-label="Share on email"
                class="ri-mail-fill text-3xl text-indigo-500 hover:text-indigo-700 md:text-4xl"
            ></i>
        </ShareNetwork>
        <ShareNetwork
            network="sms"
            :title="title"
            :url="currentPage"
            :description="description"
        >
            <i
                role="button"
                title="Share SMS message"
                aria-label="Share SMS message"
                class="ri-message-2-fill text-3xl text-indigo-500 hover:text-indigo-700 md:text-4xl"
            ></i>
        </ShareNetwork>
    </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    title: String,
});

const currentPage = computed(() => window.location.href);

const description = computed(
    () =>
        `See ${props.title} on ${usePage().props.settings.title} FamilyTribute`,
);
</script>
