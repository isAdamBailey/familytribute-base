<template>
    <input
        v-model="proxyChecked"
        type="checkbox"
        :value="value"
        class="rounded border-gray-300 text-hearthlight shadow-sm focus:border-hearthlight focus:ring focus:ring-amber-200 focus:ring-opacity-50"
    />
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        checked: {
            type: [Array, Boolean],
            default: false,
        },
        value: {
            type: [String, Number],
            default: null,
        },
    },
    emits: ["update:checked"],

    computed: {
        proxyChecked: {
            get() {
                return this.checked;
            },

            set(val) {
                this.$emit("update:checked", val);
            },
        },
    },
});
</script>
