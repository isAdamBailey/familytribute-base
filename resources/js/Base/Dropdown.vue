<template>
    <div class="relative">
        <div @click="open = !open">
            <span v-if="arrowTriggerTitle" class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-indigo-300 dark:hover:text-indigo-500"
                >
                    {{ arrowTriggerTitle }}
                    <svg
                        class="ml-2 -mr-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
            <div v-else>
                <slot name="trigger"></slot>
            </div>
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-40"
            @click="open = false"
        ></div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5 dark:bg-gray-900"
                    :class="contentClasses"
                >
                    <slot name="content"></slot>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { defineComponent, onMounted, onUnmounted, ref } from "vue";

export default defineComponent({
    props: {
        align: {
            type: String,
            default: "right",
        },
        width: {
            type: String,
            default: "48",
        },
        contentClasses: {
            type: Array,
            default: () => ["py-1", "bg-white"],
        },
        arrowTriggerTitle: {
            type: String,
            default: null,
        },
    },

    setup() {
        let open = ref(false);

        const closeOnEscape = (e) => {
            if (open.value && e.keyCode === 27) {
                open.value = false;
            }
        };

        onMounted(() => document.addEventListener("keydown", closeOnEscape));
        onUnmounted(() =>
            document.removeEventListener("keydown", closeOnEscape)
        );

        return {
            open,
        };
    },

    computed: {
        widthClass() {
            return {
                48: "w-48",
            }[this.width.toString()];
        },

        alignmentClasses() {
            if (this.align === "left") {
                return "origin-top-left left-0";
            } else if (this.align === "right") {
                return "origin-top-right right-0";
            } else {
                return "origin-top";
            }
        },
    },
});
</script>
