import './bootstrap';
import "../css/app.css";

import Button from "@/Base/Button.vue";
import ScrollTop from "@/Base/ScrollTop.vue";

import { createApp, h } from "vue";
import { createInertiaApp, Link } from "@inertiajs/vue3";
import "remixicon/fonts/remixicon.css";
import VueSocialSharing from "vue-social-sharing";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueSocialSharing)
            .component("Link", Link)
            .component("BaseButton", Button)
            .component("ScrollTop", ScrollTop)
            .mixin({ methods: { route } })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
