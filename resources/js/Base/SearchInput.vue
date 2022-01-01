<template>
    <div class="w-1/2 bg-white dark:bg-gray-800 px-4">
        <label for="search" class="hidden">Search</label>
        <input
            id="search"
            ref="search"
            v-model="search"
            class="transition h-10 w-full bg-gray-100 border border-gray-500 rounded-full focus:border-purple-400 outline-none cursor-pointer text-gray-700 px-4 pb-0 pt-px"
            :class="{ 'transition-border': search }"
            autocomplete="off"
            name="search"
            placeholder="Search"
            type="search"
            @keyup.esc="search = null"
            @blur="search = null"
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
                { search: this.search },
                { preserveState: true }
            );
        }, 500),
    },
});
</script>
