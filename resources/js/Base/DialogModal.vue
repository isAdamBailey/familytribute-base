<template>
    <modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4">
            <div class="text-lg">
                <slot name="title"> </slot>
            </div>

            <div class="mt-4">
                <slot name="content"> </slot>
            </div>
        </div>

        <div class="bg-gray-100 px-6 py-4 text-right">
            <slot name="footer"> </slot>
        </div>
    </modal>
</template>

<script>
import { defineComponent } from "vue";
import Modal from "@/Base/Modal.vue";

export default defineComponent({
    components: {
        Modal,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        maxWidth: {
            type: String,
            default: "2xl",
        },
        closeable: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["close"],

    methods: {
        close() {
            this.$emit("close");
        },
    },
});
</script>
