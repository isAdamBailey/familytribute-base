<script setup lang="ts">
import type { TaggingPerson } from '~/types/api'

const props = defineProps<{ peopleOptions: TaggingPerson[] }>()

const { createStory } = useStoryMutations()
const { flash } = useFlash()

const title = ref('')
const excerpt = ref('')
const content = ref('')
const year = ref('')
const isPrivate = ref(false)
const personIds = ref<number[]>([])
const mediaInput = ref<HTMLInputElement | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    await createStory({
      title: title.value,
      excerpt: excerpt.value,
      content: content.value,
      year: year.value || null,
      private: isPrivate.value,
      person_ids: personIds.value,
      media: mediaInput.value?.files?.[0] ?? null,
    })
    flash('Story successfully created!')
    title.value = ''
    excerpt.value = ''
    content.value = ''
    year.value = ''
    isPrivate.value = false
    personIds.value = []
    if (mediaInput.value) mediaInput.value.value = ''
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form data-testid="story-create-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">New Story</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">Add a new story to the "Stories" page.</p>

    <div>
      <label for="title" class="block text-sm font-semibold">Title</label>
      <input id="title" v-model="title" type="text" class="form-input mt-1">
      <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Excerpt</label>
      <WysiwygEditor v-model="excerpt" test-id="story-excerpt" :max-character-count="250" />
      <p v-if="errors.excerpt" class="mt-1 text-sm text-red-600">{{ errors.excerpt }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Story</label>
      <WysiwygEditor v-model="content" test-id="story-content" />
      <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Tag People</label>
      <PersonTagSelect v-model="personIds" :options="props.peopleOptions" test-id="story-person-ids" />
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
        <p class="text-xs text-faded-ink dark:text-old-binding">Private stories will only appear for registered users.</p>
        <input id="private" v-model="isPrivate" type="checkbox" class="mt-1">
      </div>
    </div>

    <div>
      <label for="media" class="block text-sm font-semibold">Audio / Video (optional)</label>
      <p class="text-xs text-faded-ink dark:text-old-binding">Upload a recording of this story being spoken.</p>
      <input
        id="media"
        ref="mediaInput"
        data-testid="story-media-input"
        type="file"
        accept="audio/*,video/*"
        class="mt-1 block w-full text-sm"
      >
      <p v-if="errors.media" class="mt-1 text-sm text-red-600">{{ errors.media }}</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" data-testid="story-create-submit" class="btn-primary" :disabled="processing">
        Save
      </button>
    </div>
  </form>
</template>
