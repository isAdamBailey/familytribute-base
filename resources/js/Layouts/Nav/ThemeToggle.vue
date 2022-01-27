<template>
    <button
        class="my-5 mx-2 text-indigo-800 transition hover:text-orange-700 dark:text-indigo-400 dark:hover:text-yellow-300 md:mx-5"
        aria-label="Toggle Dark Theme"
        @click="toggle()"
    >
        <svg
            id="light"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        </svg>
        <svg
            id="dark"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class=""
        >
            <circle cx="12" cy="12" r="5" />
            <path
                d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"
            />
        </svg>
    </button>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    data() {
        return {
            light: null,
            dark: null,
        };
    },
    mounted() {
        this.light = document.getElementById("light");
        this.dark = document.getElementById("dark");

        if (document.documentElement.classList.contains("dark")) {
            this.light.classList.add("hidden");
            this.dark.classList.remove("hidden");
        } else {
            this.dark.classList.add("hidden");
            this.light.classList.remove("hidden");
        }
    },
    methods: {
        toggle() {
            document.documentElement.classList.toggle("dark");
            this.light.classList.toggle("hidden");
            this.dark.classList.toggle("hidden");

            const newTheme = localStorage.theme === "light" ? "dark" : "light";
            localStorage.setItem("theme", newTheme);
        },
    },
});
</script>
