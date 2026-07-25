<script setup lang="ts">
import type { Picture, TaggingPerson } from '~/types/api'

const props = defineProps<{ open: boolean, picture: Picture, people: TaggingPerson[] }>()
const emit = defineEmits<{ close: [], updated: [Picture] }>()

const { updatePicture } = usePictureMutations()
const { flash } = useFlash()

const title = ref(props.picture.title)
const description = ref(props.picture.description)
const year = ref<string | number>(props.picture.year)
const featured = ref(Boolean(props.picture.featured))
const isPrivate = ref(Boolean(props.picture.private))
const personIds = ref<number[]>(props.picture.person_ids ?? [])
const photoInput = ref<HTMLInputElement | null>(null)
const photoPreview = ref<string | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

watch(() => props.picture, (picture) => {
  title.value = picture.title
  description.value = picture.description
  year.value = picture.year
  featured.value = Boolean(picture.featured)
  isPrivate.value = Boolean(picture.private)
  personIds.value = picture.person_ids ?? []
})

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
    const { picture } = await updatePicture(props.picture.slug, {
      title: title.value,
      description: description.value,
      year: year.value,
      featured: featured.value,
      private: isPrivate.value,
      photo: photoInput.value?.files?.[0] ?? null,
      person_ids: personIds.value,
    })
    flash('Picture successfully updated!')
    emit('updated', picture)
    close()
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}

function close() {
  photoPreview.value = null
  if (photoInput.value) photoInput.value.value = ''
  errors.value = {}
  emit('close')
}
</script>

<template>
  <AppModal :open="open" title="Edit Picture" @close="close">
    <div class="space-y-4">
      <div>
        <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange">
        <label class="block text-sm font-semibold">Photo</label>
        <img v-if="!photoPreview" :src="picture.url" :alt="picture.title" class="mt-2 h-20 w-20 rounded-full object-cover">
        <div v-else class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${photoPreview}')`" />
        <button type="button" class="btn-secondary mt-2" @click="selectPhoto">Select A New Photo</button>
        <p v-if="errors.photo" class="mt-1 text-sm text-red-600">{{ errors.photo }}</p>
      </div>

      <div>
        <label for="title" class="block text-sm font-semibold">Title</label>
        <input id="title" v-model="title" type="text" class="form-input mt-1">
        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Tag People</label>
        <PersonTagSelect v-model="personIds" :options="people" />
        <p v-if="errors.person_ids" class="mt-1 text-sm text-red-600">{{ errors.person_ids }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Description</label>
        <WysiwygEditor v-model="description" />
        <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
      </div>

      <div class="max-w-[8rem]">
        <label for="year" class="block text-sm font-semibold">Year</label>
        <input id="year" v-model="year" type="number" class="form-input mt-1">
        <p v-if="errors.year" class="mt-1 text-sm text-red-600">{{ errors.year }}</p>
      </div>

      <div class="flex gap-8">
        <div>
          <label for="featured" class="block text-sm font-semibold">Featured</label>
          <input id="featured" v-model="featured" type="checkbox" :disabled="isPrivate" class="mt-1">
        </div>
        <div>
          <label for="private" class="block text-sm font-semibold">Private</label>
          <input id="private" v-model="isPrivate" type="checkbox" class="mt-1">
        </div>
      </div>
    </div>

    <template #footer>
      <button type="button" class="btn-secondary" @click="close">Nevermind</button>
      <button type="button" class="btn-primary" :disabled="processing" @click="submit">Update Picture</button>
    </template>
  </AppModal>

</template>
