import Button from "@/Base/Button";
import ScrollTop from "@/Base/ScrollTop";

require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp, Link } from "@inertiajs/vue3";
import "remixicon/fonts/remixicon.css";
import VueSocialSharing from "vue-social-sharing";

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}.vue`),
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
