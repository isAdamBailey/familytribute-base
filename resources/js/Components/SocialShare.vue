<template>
    <div class="my-5 md:w-96 flex justify-between items-center">
        <ShareNetwork
            network="facebook"
            :quote="title"
            :title="title"
            :description="description"
            :url="currentPage"
        >
            <i
                role="button"
                aria-label="Share on Facebook"
                class="ri-facebook-circle-fill text-3xl md:text-4xl text-indigo-500 hover:text-indigo-700"
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
                class="ri-twitter-fill text-3xl md:text-4xl text-indigo-500 hover:text-indigo-700"
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
                aria-label="Share on email"
                class="ri-mail-fill text-3xl md:text-4xl text-indigo-500 hover:text-indigo-700"
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
                aria-label="Share SMS message"
                class="ri-message-2-fill text-3xl md:text-4xl text-indigo-500 hover:text-indigo-700"
            ></i>
        </ShareNetwork>
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
        description() {
            return `See ${this.title} on ${this.$inertia.page.props.siteName} FamilyTribute`;
        },
    },
});
</script>
