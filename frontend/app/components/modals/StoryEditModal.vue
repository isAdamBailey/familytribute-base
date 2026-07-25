<script setup lang="ts">
import type { Story, TaggingPerson } from '~/types/api'

const props = defineProps<{ open: boolean, story: Story, people: TaggingPerson[] }>()
const emit = defineEmits<{ close: [], updated: [Story] }>()

const { updateStory } = useStoryMutations()
const { flash } = useFlash()

const title = ref(props.story.title)
const excerpt = ref(props.story.excerpt)
const content = ref(props.story.content)
const year = ref<string | number | null>(props.story.year)
const isPrivate = ref(Boolean(props.story.private))
const personIds = ref<number[]>(props.story.person_ids ?? [])
const mediaInput = ref<HTMLInputElement | null>(null)
const removeMedia = ref(false)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

watch(() => props.story, (story) => {
  title.value = story.title
  excerpt.value = story.excerpt
  content.value = story.content
  year.value = story.year
  isPrivate.value = Boolean(story.private)
  personIds.value = story.person_ids ?? []
  removeMedia.value = false
})

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    const { story } = await updateStory(props.story.slug, {
      title: title.value,
      excerpt: excerpt.value,
      content: content.value,
      year: year.value,
      private: isPrivate.value,
      person_ids: personIds.value,
      media: mediaInput.value?.files?.[0] ?? null,
      remove_media: removeMedia.value,
    })
    flash('Story successfully updated!')
    emit('updated', story)
    close()
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}

function close() {
  if (mediaInput.value) mediaInput.value.value = ''
  errors.value = {}
  emit('close')
}
</script>

<template>
  <AppModal :open="open" title="Edit Story" @close="close">
    <div class="space-y-4">
      <div>
        <label for="title" class="block text-sm font-semibold">Title</label>
        <input id="title" v-model="title" type="text" class="form-input mt-1">
        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Excerpt</label>
        <WysiwygEditor v-model="excerpt" :max-character-count="250" />
        <p v-if="errors.excerpt" class="mt-1 text-sm text-red-600">{{ errors.excerpt }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Story</label>
        <WysiwygEditor v-model="content" />
        <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Tag People</label>
        <PersonTagSelect v-model="personIds" :options="people" />
        <p v-if="errors.person_ids" class="mt-1 text-sm text-red-600">{{ errors.person_ids }}</p>
      </div>

      <div class="flex flex-wrap items-start gap-8">
        <div class="max-w-[8rem]">
          <label for="year" class="block text-sm font-semibold">Year</label>
          <input id="year" v-model="year" type="number" class="form-input mt-1">
          <p v-if="errors.year" class="mt-1 text-sm text-red-600">{{ errors.year }}</p>
        </div>
        <div>
          <label for="private" class="block text-sm font-semibold">Private</label>
          <input id="private" v-model="isPrivate" type="checkbox" class="mt-1">
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold">Audio / Video</label>
        <p v-if="story.media_url" class="text-xs text-faded-ink dark:text-old-binding">
          A recording is already attached.
          <label class="ml-1 inline-flex items-center gap-1">
            <input v-model="removeMedia" type="checkbox"> Remove it
          </label>
        </p>
        <input ref="mediaInput" type="file" accept="audio/*,video/*" class="mt-1 block w-full text-sm">
        <p v-if="errors.media" class="mt-1 text-sm text-red-600">{{ errors.media }}</p>
      </div>
    </div>

    <template #footer>
      <button type="button" class="btn-secondary" @click="close">Nevermind</button>
      <button type="button" class="btn-primary" :disabled="processing" @click="submit">Update Story</button>
    </template>
  </AppModal>

</template>
