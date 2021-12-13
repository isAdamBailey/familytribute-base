<template>
    <div>
        <app-head :title="title" :description="description" />

        <div class="min-h-screen bg-gradient-to-r from-indigo-100 to-gray-100">
            <primary-nav />
            <!-- Page Content -->
            <main>
                <div class="py-5 md:py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div
                            class="bg-white shadow-xl shadow-indigo-200/50 rounded-lg overflow-hidden"
                        >
                            <div
                                class="p-6 pb-20 sm:px-20 bg-white border-b border-gray-200"
                            >
                                <slot></slot>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <primary-footer />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PrimaryFooter from "@/Layouts/PrimaryFooter";
import AppHead from "@/Layouts/AppHead";
import PrimaryNav from "@/Layouts/Nav/PrimaryNav";

export default defineComponent({
    components: {
        PrimaryNav,
        AppHead,
        PrimaryFooter,
    },
    props: {
        title: String,
        description: String,
    },
});
</script>
