<template>
    <div class="my-3 w-60 flex justify-between items-center">
        <div class="font-semibold text-xl">Share:</div>
        <i
            role="button"
            aria-label="Share on Facebook"
            class="ri-facebook-circle-fill text-5xl text-indigo-500 hover:text-indigo-700"
        ></i>
        <i
            role="button"
            aria-label="Share on Twitter"
            class="ri-twitter-fill text-5xl text-indigo-500 hover:text-indigo-700"
        ></i>
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
    props: {},
});
</script>
