<template>
    <datepicker
        :model-value="modelValue"
        model-type="yyyy-MM-dd"
        format="yyyy-MM-dd"
        text-input
        :enable-time-picker="false"
        :year-range="[1850, 2040]"
        auto-apply
        @update:model-value="onUpdateDate"
    />
</template>

<script>
import { defineComponent } from "vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default defineComponent({
    components: { Datepicker },

    props: {
        modelValue: {
            type: [String, null],
            default: null,
        },
    },

    emits: ["update:modelValue"],

    methods: {
        onUpdateDate(event) {
            this.$emit("update:modelValue", event || null);
        },
    },
});
</script>
