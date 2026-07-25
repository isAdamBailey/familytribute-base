<script setup lang="ts">
import type { TaggingPerson } from '~/types/api'

const props = defineProps<{ peopleOptions: TaggingPerson[] }>()

const { createObituary } = useObituaryMutations()
const { flash } = useFlash()

const firstName = ref('')
const lastName = ref('')
const content = ref('')
const birthDate = ref('')
const deathDate = ref('')
const parentIds = ref<number[]>([])
const photoInput = ref<HTMLInputElement | null>(null)
const bgPhotoInput = ref<HTMLInputElement | null>(null)
const photoPreview = ref<string | null>(null)
const bgPhotoPreview = ref<string | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

function selectPhoto() {
  photoInput.value?.click()
}
function selectBgPhoto() {
  bgPhotoInput.value?.click()
}

function onPhotoChange() {
  readFileAsPreview(photoInput.value?.files?.[0], photoPreview)
}

function onBgPhotoChange() {
  readFileAsPreview(bgPhotoInput.value?.files?.[0], bgPhotoPreview)
}

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    await createObituary({
      first_name: firstName.value,
      last_name: lastName.value,
      content: content.value,
      birth_date: birthDate.value,
      death_date: deathDate.value,
      photo: photoInput.value?.files?.[0] ?? null,
      background_photo: bgPhotoInput.value?.files?.[0] ?? null,
      parent_ids: parentIds.value,
    })
    flash('Obituary successfully created!')
    firstName.value = ''
    lastName.value = ''
    content.value = ''
    birthDate.value = ''
    deathDate.value = ''
    parentIds.value = []
    photoPreview.value = null
    bgPhotoPreview.value = null
    if (photoInput.value) photoInput.value.value = ''
    if (bgPhotoInput.value) bgPhotoInput.value.value = ''
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form id="new-person" data-testid="person-create-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">New Person</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Add a new person to the site. This allows you to "tag" them in photos and stories.
    </p>

    <div class="flex flex-wrap gap-6">
      <div>
        <input
          ref="photoInput"
          type="file"
          accept="image/*"
          data-testid="person-photo-input"
          class="hidden"
          @change="onPhotoChange"
        >
        <label for="photo" class="block text-sm font-semibold">Photo</label>
        <div v-if="photoPreview" class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${photoPreview}')`" />
        <button type="button" class="btn-secondary mt-2" @click="selectPhoto">Select A Photo</button>
        <p v-if="errors.photo" class="mt-1 text-sm text-red-600">{{ errors.photo }}</p>
      </div>

      <div>
        <input
          ref="bgPhotoInput"
          type="file"
          accept="image/*"
          data-testid="person-background-photo-input"
          class="hidden"
          @change="onBgPhotoChange"
        >
        <label for="bg_photo" class="block text-sm font-semibold">Background Photo</label>
        <div v-if="bgPhotoPreview" class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${bgPhotoPreview}')`" />
        <button type="button" class="btn-secondary mt-2" @click="selectBgPhoto">Select A Background Photo</button>
        <p v-if="errors.background_photo" class="mt-1 text-sm text-red-600">{{ errors.background_photo }}</p>
      </div>
    </div>

    <div class="flex flex-wrap gap-6">
      <div class="min-w-[12rem] flex-1">
        <label for="first_name" class="block text-sm font-semibold">First Name</label>
        <input id="first_name" v-model="firstName" type="text" class="form-input mt-1">
        <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
      </div>
      <div class="min-w-[12rem] flex-1">
        <label for="last_name" class="block text-sm font-semibold">Last Name</label>
        <input id="last_name" v-model="lastName" type="text" class="form-input mt-1">
        <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
      </div>
    </div>

    <div class="flex flex-wrap gap-6">
      <div class="min-w-[12rem] flex-1">
        <label class="block text-sm font-semibold">Birth Date</label>
        <div data-testid="person-birth-date">
          <input v-model="birthDate" type="date" class="form-input mt-1">
        </div>
        <p v-if="errors.birth_date" class="mt-1 text-sm text-red-600">{{ errors.birth_date }}</p>
      </div>
      <div class="min-w-[12rem] flex-1">
        <label class="block text-sm font-semibold">Death Date</label>
        <div data-testid="person-death-date">
          <input v-model="deathDate" type="date" class="form-input mt-1">
        </div>
        <p v-if="errors.death_date" class="mt-1 text-sm text-red-600">{{ errors.death_date }}</p>
      </div>
    </div>

    <div>
      <label class="block text-sm font-semibold">Parents</label>
      <PersonTagSelect v-model="parentIds" :options="props.peopleOptions" test-id="person-parent-ids" />
      <p v-if="errors.parent_ids" class="mt-1 text-sm text-red-600">{{ errors.parent_ids }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Obituary</label>
      <WysiwygEditor v-model="content" test-id="person-obituary" />
      <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" data-testid="person-create-submit" class="btn-primary" :disabled="processing">
        Save
      </button>
    </div>
  </form>
</template>
