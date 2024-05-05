<template>
    <div class="w-1/2 bg-white px-4 dark:bg-gray-800">
        <label for="search" class="hidden">Search</label>
        <input
            id="search"
            ref="search"
            v-model="search"
            class="h-10 w-full cursor-pointer rounded-full border border-gray-500 bg-gray-100 px-4 pb-0 pt-px text-gray-700 outline-none transition focus:border-purple-400"
            :class="{ 'transition-border': search }"
            autocomplete="off"
            name="search"
            placeholder="Search"
            type="search"
            @keyup.esc="search = null"
        />
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        routeName: String,
    },

    data() {
        return {
            search: this.$inertia.page.props.search || null,
            sort: this.$inertia.page.props.sort || null,
        };
    },

    watch: {
        search() {
            if (this.search) {
                this.searchMethod();
            } else {
                this.$inertia.get(route(this.routeName));
            }
        },
    },

    methods: {
        searchMethod: _.debounce(function () {
            this.$inertia.get(
                route(this.routeName),
                { search: this.search, sort: this.sort },
                { preserveState: true }
            );
        }, 2000),
    },
});
</script>
