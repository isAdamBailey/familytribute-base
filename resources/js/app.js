import Button from "@/Base/Button";

require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp, Link } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import "remixicon/fonts/remixicon.css";
import VueSocialSharing from "vue-social-sharing";

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(VueSocialSharing)
            .component("Link", Link)
            .component("BaseButton", Button)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
