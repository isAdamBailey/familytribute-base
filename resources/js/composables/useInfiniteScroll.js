import { router, usePage } from "@inertiajs/vue3";
import { onMounted, reactive, ref } from "vue";

export default function useInfiniteScroll(items, type = "") {
    const reactiveItems = reactive(items);
    const infiniteScroll = ref(null);
    let observer = null;

    onMounted(async () => {
        observer = new IntersectionObserver((entries) =>
            entries.forEach((entry) => entry.isIntersecting && fetchUploads(), {
                rootMargin: "-150px 0px 0px 0px",
            }),
        );
        observer.observe(infiniteScroll.value);
    });

    const initialUrl = usePage().url;

    function fetchUploads() {
        if (reactiveItems.links.next) {
            router.get(
                reactiveItems.links.next,
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        window.history.replaceState({}, "", initialUrl);
                        reactiveItems.data = [
                            ...reactiveItems.data,
                            ...page.props[type].data,
                        ];

                        reactiveItems.links = page.props[type].links;
                    },
                },
            );
        }
    }
    return { reactiveItems, infiniteScroll };
}
