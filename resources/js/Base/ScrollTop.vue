<template>
    <div
        ref="scrollTopButton"
        class="invisible sticky bottom-0 flex w-full justify-end pb-3 pr-5 transition lg:pr-16"
    >
        <div
            class="cursor-pointer rounded-full text-gray-700 transition hover:text-indigo-600 dark:text-orange-600 dark:hover:text-orange-500"
        >
            <button
                role="button"
                aria-label="scroll to top of the page"
                @click="scrollToTop"
            >
                <i class="ri-arrow-up-circle-line text-5xl"></i>
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
