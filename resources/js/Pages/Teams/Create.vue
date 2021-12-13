<template>
    <app-layout title="Create Team">
        <template #header> Create Team </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <create-team-form />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CreateTeamForm from "@/Pages/Teams/Partials/CreateTeamForm.vue";

export default defineComponent({
    components: {
        AppLayout,
        CreateTeamForm,
    },
});
</script>
