<template>
    <Link :href="'/'">
        <application-mark class="h-20 w-20" />
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/vue3";
import ApplicationMark from "@/Base/ApplicationMark";

export default defineComponent({
    components: {
        ApplicationMark,
        Link,
    },
});
</script>
