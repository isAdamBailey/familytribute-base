<template>
    <Link :href="'/'">
        <div
            class="font-header text-3xl font-bold text-hearthlight md:text-5xl"
        >
            {{ page.props.settings.title }}
        </div>
    </Link>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
</script>
