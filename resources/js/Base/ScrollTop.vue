<template>
    <div
        ref="scrollTopButton"
        class="invisible sticky w-full flex justify-end bottom-0 pb-3 pr-5 lg:pr-16 transition"
    >
        <div
            class="rounded-full cursor-pointer text-gray-700 dark:text-orange-600 hover:text-indigo-600 dark:hover:text-orange-500 transition"
        >
            <button
                role="button"
                aria-label="scroll to top of the page"
                @click="scrollToTop"
            >
                <i class="text-5xl ri-arrow-up-circle-line"></i>
            </button>
        </div>
    </div>
</template>

<script>
import debounce from "lodash/debounce";
import { defineComponent } from "vue";

export default defineComponent({
    data() {
        return {
            handleDebouncedScroll: null,
        };
    },
    mounted() {
        this.handleDebouncedScroll = debounce(this.handleScroll, 100);
        window.addEventListener("scroll", this.handleDebouncedScroll);
    },

    beforeUnmount() {
        window.removeEventListener("scroll", this.handleDebouncedScroll);
    },
    methods: {
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
        handleScroll() {
            const scrollBtn = this.$refs.scrollTopButton;

            if (window.scrollY > 0) {
                scrollBtn.classList.remove("invisible");
            } else {
                scrollBtn.classList.add("invisible");
            }
        },
    },
});
</script>
