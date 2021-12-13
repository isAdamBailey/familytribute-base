<template>
    <Link :href="'/'">
        <application-mark class="w-20 h-20" />
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import ApplicationMark from "@/Base/ApplicationMark";

export default defineComponent({
    components: {
        ApplicationMark,
        Link,
    },
});
</script>
