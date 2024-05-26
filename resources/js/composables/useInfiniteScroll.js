import { onMounted, reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";

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

    function fetchUploads() {
        if (reactiveItems.links.next) {
            router.get(
                reactiveItems.links.next,
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
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
