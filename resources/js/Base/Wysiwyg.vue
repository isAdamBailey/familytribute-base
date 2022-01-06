<template>
    <div v-if="editor" class="border border-gray-300 rounded-md shadow-sm">
        <wysiwyg-button
            icon="ri-paragraph"
            title="set paragraph"
            :is-active="editor.isActive('paragraph')"
            @click.prevent="editor.chain().focus().setParagraph().run()"
        />

        <span v-if="!isExcerpt">
            <wysiwyg-button
                icon="ri-h-1"
                title="toggle h1"
                :is-active="editor.isActive('heading', { level: 2 })"
                @click.prevent="
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                "
            />

            <wysiwyg-button
                icon="ri-h-2"
                title="toggle h2"
                :is-active="editor.isActive('heading', { level: 3 })"
                @click.prevent="
                    editor.chain().focus().toggleHeading({ level: 3 }).run()
                "
            />

            <wysiwyg-button
                icon="ri-h-3"
                title="toggle h3"
                :is-active="editor.isActive('heading', { level: 4 })"
                @click.prevent="
                    editor.chain().focus().toggleHeading({ level: 4 }).run()
                "
            />

            <wysiwyg-button
                icon="ri-bold"
                title="toggle bold"
                :is-active="editor.isActive('bold')"
                @click.prevent="editor.chain().focus().toggleBold().run()"
            />

            <wysiwyg-button
                icon="ri-italic"
                title="toggle italic"
                :is-active="editor.isActive('italic')"
                @click.prevent="editor.chain().focus().toggleItalic().run()"
            />

            <wysiwyg-button
                icon="ri-link"
                title="add hyperlink"
                :is-active="editor.isActive('link')"
                @click.prevent="setLink"
            />

            <wysiwyg-button
                icon="ri-link-unlink"
                title="remove hyperlink"
                :disabled="!editor.isActive('link')"
                @click.prevent="editor.chain().focus().unsetLink().run()"
            />

            <wysiwyg-button
                icon="ri-strikethrough"
                title="toggle strikethrough"
                :is-active="editor.isActive('strike')"
                @click.prevent="editor.chain().focus().toggleStrike().run()"
            />

            <wysiwyg-button
                icon="ri-list-unordered"
                title="toggle unordered list"
                :is-active="editor.isActive('bulletList')"
                @click.prevent="editor.chain().focus().toggleBulletList().run()"
            />

            <wysiwyg-button
                icon="ri-list-ordered"
                title="toggle ordered list"
                :is-active="editor.isActive('orderedList')"
                @click.prevent="
                    editor.chain().focus().toggleOrderedList().run()
                "
            />

            <wysiwyg-button
                icon="ri-double-quotes-l"
                title="toggle blockquote"
                :is-active="editor.isActive('blockquote')"
                @click.prevent="editor.chain().focus().toggleBlockquote().run()"
            />

            <wysiwyg-button
                icon="ri-bar-chart-horizontal-line"
                title="toggle horizontal rule"
                @click.prevent="
                    editor.chain().focus().setHorizontalRule().run()
                "
            />
        </span>

        <wysiwyg-button
            icon="ri-arrow-go-back-line"
            title="undo last action"
            @click.prevent="editor.chain().focus().undo().run()"
        />

        <wysiwyg-button
            icon="ri-arrow-go-forward-line"
            title="redo last action"
            @click.prevent="editor.chain().focus().redo().run()"
        />

        <editor-content :editor="editor" />

        <div
            :class="`ml-1 text-sm ${
                isClose ? 'text-red-600 font-bold' : 'text-gray-400'
            }`"
        >
            {{ editorCharacterCount }}
        </div>
    </div>
</template>

<script>
import { Editor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Typography from "@tiptap/extension-typography";
import WysiwygButton from "@/Base/WysiwygButton";
import Link from "@tiptap/extension-link";
import { CharacterCount } from "@tiptap/extension-character-count";

export default {
    components: {
        WysiwygButton,
        EditorContent,
    },

    props: {
        modelValue: {
            type: String,
            default: "",
        },
        maxCharacterCount: { type: Number, default: 20000 },
    },
    emits: ["update:modelValue"],

    data() {
        return {
            editor: null,
        };
    },

    computed: {
        isExcerpt() {
            return this.maxCharacterCount <= 250;
        },
        editorCharacterCount() {
            return this.editor.storage.characterCount.characters();
        },
        isClose() {
            return this.editorCharacterCount >= this.maxCharacterCount - 10;
        },
    },

    watch: {
        modelValue(value) {
            const isSame = this.editor.getHTML() === value;

            if (isSame) {
                return;
            }

            this.editor.commands.setContent(value, false);
        },
    },

    mounted() {
        this.editor = new Editor({
            editorProps: {
                attributes: {
                    class: "prose max-w-full h-48 my-2 mx-5 border-t overflow-y-auto focus:outline-none",
                },
            },
            extensions: [
                StarterKit,
                Typography,
                CharacterCount.configure({
                    limit: this.maxCharacterCount,
                }),
                Link.configure({
                    openOnClick: false,
                }),
            ],
            onUpdate: () => {
                this.$emit("update:modelValue", this.editor.getHTML());
            },
            content: this.modelValue,
        });
    },

    beforeUnmount() {
        this.editor.destroy();
    },

    methods: {
        setLink() {
            const previousUrl = this.editor.getAttributes("link").href;
            const url = window.prompt("URL", previousUrl);

            // cancelled
            if (url === null) {
                return;
            }

            // empty
            if (url === "") {
                this.editor
                    .chain()
                    .focus()
                    .extendMarkRange("link")
                    .unsetLink()
                    .run();

                return;
            }

            this.editor
                .chain()
                .focus()
                .extendMarkRange("link")
                .setLink({ href: url })
                .run();
        },
    },
};
</script>
