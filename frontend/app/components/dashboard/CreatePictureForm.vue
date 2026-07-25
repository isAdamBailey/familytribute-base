<script setup lang="ts">
import type { TaggingPerson } from '~/types/api'

const props = defineProps<{ peopleOptions: TaggingPerson[] }>()

const { createPicture } = usePictureMutations()
const { flash } = useFlash()

const title = ref('')
const description = ref('')
const year = ref('')
const featured = ref(false)
const isPrivate = ref(false)
const personIds = ref<number[]>([])
const photoInput = ref<HTMLInputElement | null>(null)
const photoPreview = ref<string | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

watch(isPrivate, (value) => { if (value) featured.value = false })

function selectPhoto() {
  photoInput.value?.click()
}

function onPhotoChange() {
  readFileAsPreview(photoInput.value?.files?.[0], photoPreview)
}

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    await createPicture({
      title: title.value,
      description: description.value,
      year: year.value,
      featured: featured.value,
      private: isPrivate.value,
      photo: photoInput.value?.files?.[0] ?? null,
      person_ids: personIds.value,
    })
    flash('Picture successfully created!')
    title.value = ''
    description.value = ''
    year.value = ''
    featured.value = false
    isPrivate.value = false
    personIds.value = []
    photoPreview.value = null
    if (photoInput.value) photoInput.value.value = ''
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form data-testid="picture-create-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">New Picture</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">Add a new picture to the "Pictures" page.</p>

    <div>
      <input
        ref="photoInput"
        type="file"
        accept="image/*"
        data-testid="picture-photo-input"
        class="hidden"
        @change="onPhotoChange"
      >
      <label for="photo" class="block text-sm font-semibold">Photo</label>
      <div v-if="photoPreview" class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${photoPreview}')`" />
      <button type="button" class="btn-secondary mt-2" @click="selectPhoto">Select A Photo</button>
      <p v-if="errors.photo" class="mt-1 text-sm text-red-600">{{ errors.photo }}</p>
    </div>

    <div>
      <label for="title" class="block text-sm font-semibold">Title</label>
      <input id="title" v-model="title" type="text" class="form-input mt-1">
      <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Description</label>
      <WysiwygEditor v-model="description" test-id="picture-description" />
      <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
    </div>

    <div class="max-w-[8rem]">
      <label for="year" class="block text-sm font-semibold">Year</label>
      <input id="year" v-model="year" type="number" class="form-input mt-1">
      <p v-if="errors.year" class="mt-1 text-sm text-red-600">{{ errors.year }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Tag People</label>
      <PersonTagSelect v-model="personIds" :options="props.peopleOptions" test-id="picture-person-ids" />
      <p v-if="errors.person_ids" class="mt-1 text-sm text-red-600">{{ errors.person_ids }}</p>
    </div>

    <div class="flex gap-8">
      <div>
        <label for="featured" class="block text-sm font-semibold">Featured</label>
        <p class="text-xs text-faded-ink dark:text-old-binding">Featured images are displayed randomly on the home page, and cannot be private.</p>
        <input id="featured" v-model="featured" type="checkbox" :disabled="isPrivate" class="mt-1">
      </div>
      <div>
        <label for="private" class="block text-sm font-semibold">Private</label>
        <p class="text-xs text-faded-ink dark:text-old-binding">Private images will only appear for registered users.</p>
        <input id="private" v-model="isPrivate" type="checkbox" class="mt-1">
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" data-testid="picture-create-submit" class="btn-primary" :disabled="processing">
        Save
      </button>
    </div>
  </form>
</template>
