<script setup lang="ts">
// Ported from resources/js/Base/Wysiwyg.vue (issue #19, Phase 2), TS + <script
// setup>. Client-only (.client) so Tiptap never runs during SSR. Delivered now
// so Phase 4 CRUD forms can use it; not mounted by any page yet in Phase 2.
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Typography from '@tiptap/extension-typography'
import Link from '@tiptap/extension-link'
import { CharacterCount } from '@tiptap/extension-character-count'

const props = withDefaults(
  defineProps<{
    modelValue?: string
    maxCharacterCount?: number
    testId?: string | null
  }>(),
  {
    modelValue: '',
    maxCharacterCount: 20000,
    testId: null,
  },
)

const emit = defineEmits<{ 'update:modelValue': [value: string] }>()

// A short limit means the excerpt field — hide the block/format toolbar.
const isExcerpt = computed(() => props.maxCharacterCount <= 250)

const editor = useEditor({
  editorProps: {
    attributes: {
      class:
        'prose max-w-full h-48 my-2 mx-5 border-t border-gray-200 overflow-y-auto focus:outline-none dark:border-stone-600 dark:prose-invert',
    },
  },
  extensions: [
    StarterKit,
    Typography,
    CharacterCount.configure({ limit: props.maxCharacterCount }),
    Link.configure({ openOnClick: false }),
  ],
  content: props.modelValue,
  onUpdate: () => {
    emit('update:modelValue', editor.value?.getHTML() ?? '')
  },
})

const editorCharacterCount = computed(
  () => editor.value?.storage.characterCount.characters() ?? 0,
)
const isClose = computed(
  () => editorCharacterCount.value >= props.maxCharacterCount - 10,
)

// Keep the editor in sync when the bound value changes externally; guard
// against the feedback loop (setContent with emitUpdate=false).
watch(
  () => props.modelValue,
  (value) => {
    if (!editor.value) return
    if (editor.value.getHTML() === value) return
    editor.value.commands.setContent(value, false)
  },
)

function setLink() {
  if (!editor.value) return
  const previousUrl = editor.value.getAttributes('link').href
  const url = window.prompt('URL', previousUrl)

  // cancelled
  if (url === null) return

  // empty — remove the link
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }

  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}
</script>

<template>
  <div
    v-if="editor"
    :data-testid="testId"
    class="rounded-md border border-gray-200 bg-white shadow-sm focus-within:border-hearthlight/50 focus-within:ring-1 focus-within:ring-hearthlight/20 dark:border-stone-600 dark:bg-gray-800"
  >
    <WysiwygButton
      icon="ri-paragraph"
      title="set paragraph"
      :is-active="editor.isActive('paragraph')"
      @click.prevent="editor.chain().focus().setParagraph().run()"
    />

    <span v-if="!isExcerpt">
      <WysiwygButton
        icon="ri-h-1"
        title="toggle h1"
        :is-active="editor.isActive('heading', { level: 2 })"
        @click.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()"
      />
      <WysiwygButton
        icon="ri-h-2"
        title="toggle h2"
        :is-active="editor.isActive('heading', { level: 3 })"
        @click.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()"
      />
      <WysiwygButton
        icon="ri-h-3"
        title="toggle h3"
        :is-active="editor.isActive('heading', { level: 4 })"
        @click.prevent="editor.chain().focus().toggleHeading({ level: 4 }).run()"
      />
      <WysiwygButton
        icon="ri-bold"
        title="toggle bold"
        :is-active="editor.isActive('bold')"
        @click.prevent="editor.chain().focus().toggleBold().run()"
      />
      <WysiwygButton
        icon="ri-italic"
        title="toggle italic"
        :is-active="editor.isActive('italic')"
        @click.prevent="editor.chain().focus().toggleItalic().run()"
      />
      <WysiwygButton
        icon="ri-link"
        title="add hyperlink"
        :is-active="editor.isActive('link')"
        @click.prevent="setLink"
      />
      <WysiwygButton
        icon="ri-link-unlink"
        title="remove hyperlink"
        :disabled="!editor.isActive('link')"
        @click.prevent="editor.chain().focus().unsetLink().run()"
      />
      <WysiwygButton
        icon="ri-strikethrough"
        title="toggle strikethrough"
        :is-active="editor.isActive('strike')"
        @click.prevent="editor.chain().focus().toggleStrike().run()"
      />
      <WysiwygButton
        icon="ri-list-unordered"
        title="toggle unordered list"
        :is-active="editor.isActive('bulletList')"
        @click.prevent="editor.chain().focus().toggleBulletList().run()"
      />
      <WysiwygButton
        icon="ri-list-ordered"
        title="toggle ordered list"
        :is-active="editor.isActive('orderedList')"
        @click.prevent="editor.chain().focus().toggleOrderedList().run()"
      />
      <WysiwygButton
        icon="ri-double-quotes-l"
        title="toggle blockquote"
        :is-active="editor.isActive('blockquote')"
        @click.prevent="editor.chain().focus().toggleBlockquote().run()"
      />
      <WysiwygButton
        icon="ri-bar-chart-horizontal-line"
        title="toggle horizontal rule"
        @click.prevent="editor.chain().focus().setHorizontalRule().run()"
      />
    </span>

    <WysiwygButton
      icon="ri-arrow-go-back-line"
      title="undo last action"
      @click.prevent="editor.chain().focus().undo().run()"
    />
    <WysiwygButton
      icon="ri-arrow-go-forward-line"
      title="redo last action"
      @click.prevent="editor.chain().focus().redo().run()"
    />

    <EditorContent :editor="editor" />

    <div
      :class="`ml-1 text-sm ${isClose ? 'font-bold text-red-600' : 'text-gray-400'}`"
    >
      {{ editorCharacterCount }}
    </div>
  </div>
</template>
