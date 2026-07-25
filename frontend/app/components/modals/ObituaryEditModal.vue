<script setup lang="ts">
import type { Person, TaggingPerson } from '~/types/api'

const props = defineProps<{ open: boolean, person: Person, people: TaggingPerson[] }>()
const emit = defineEmits<{ close: [], updated: [Person] }>()

const { updateObituary } = useObituaryMutations()
const { flash } = useFlash()

const firstName = ref(props.person.first_name)
const lastName = ref(props.person.last_name ?? '')
const content = ref(props.person.obituary?.content ?? '')
const birthDate = ref(props.person.obituary?.birth_date ?? '')
const deathDate = ref(props.person.obituary?.death_date ?? '')
const parentIds = ref<number[]>(props.person.parent_ids ?? [])
const photoInput = ref<HTMLInputElement | null>(null)
const bgPhotoInput = ref<HTMLInputElement | null>(null)
const photoPreview = ref<string | null>(null)
const bgPhotoPreview = ref<string | null>(null)
const processing = ref(false)
const errors = ref<Record<string, string>>({})

watch(() => props.person, (person) => {
  firstName.value = person.first_name
  lastName.value = person.last_name ?? ''
  content.value = person.obituary?.content ?? ''
  birthDate.value = person.obituary?.birth_date ?? ''
  deathDate.value = person.obituary?.death_date ?? ''
  parentIds.value = person.parent_ids ?? []
})

function onPhotoChange() {
  readFileAsPreview(photoInput.value?.files?.[0], photoPreview)
}

function onBgPhotoChange() {
  readFileAsPreview(bgPhotoInput.value?.files?.[0], bgPhotoPreview)
}

async function submit() {
  if (!props.person.obituary) return
  processing.value = true
  errors.value = {}
  try {
    const { person } = await updateObituary(props.person.obituary.id, {
      first_name: firstName.value,
      last_name: lastName.value,
      content: content.value,
      birth_date: birthDate.value,
      death_date: deathDate.value,
      photo: photoInput.value?.files?.[0] ?? null,
      background_photo: bgPhotoInput.value?.files?.[0] ?? null,
      parent_ids: parentIds.value,
    })
    flash('Obituary successfully updated!')
    emit('updated', person)
    close()
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}

function close() {
  photoPreview.value = null
  bgPhotoPreview.value = null
  if (photoInput.value) photoInput.value.value = ''
  if (bgPhotoInput.value) bgPhotoInput.value.value = ''
  errors.value = {}
  emit('close')
}
</script>

<template>
  <AppModal :open="open" title="Edit Obituary" @close="close">
    <div class="space-y-4">
      <div class="flex flex-wrap gap-6">
        <div>
          <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange">
          <label class="block text-sm font-semibold">Photo</label>
          <img v-if="!photoPreview" :src="person.photo_url" :alt="person.full_name" class="mt-2 h-20 w-20 rounded-full object-cover">
          <div v-else class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${photoPreview}')`" />
          <button type="button" class="btn-secondary mt-2" @click="photoInput?.click()">Select A New Photo</button>
          <p v-if="errors.photo" class="mt-1 text-sm text-red-600">{{ errors.photo }}</p>
        </div>
        <div>
          <input ref="bgPhotoInput" type="file" accept="image/*" class="hidden" @change="onBgPhotoChange">
          <label class="block text-sm font-semibold">Background Photo</label>
          <img
            v-if="!bgPhotoPreview && person.obituary?.background_photo_url"
            :src="person.obituary.background_photo_url"
            :alt="person.full_name"
            class="mt-2 h-20 w-20 rounded-full object-cover"
          >
          <div v-else-if="bgPhotoPreview" class="mt-2 h-20 w-20 rounded-full bg-cover bg-center" :style="`background-image: url('${bgPhotoPreview}')`" />
          <button type="button" class="btn-secondary mt-2" @click="bgPhotoInput?.click()">Select A New Background Photo</button>
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
        <PersonTagSelect v-model="parentIds" :options="people" />
        <p v-if="errors.parent_ids" class="mt-1 text-sm text-red-600">{{ errors.parent_ids }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold">Obituary</label>
        <WysiwygEditor v-model="content" />
        <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
      </div>
    </div>

    <template #footer>
      <button type="button" class="btn-secondary" @click="close">Nevermind</button>
      <button type="button" class="btn-primary" :disabled="processing" @click="submit">Update Obituary</button>
    </template>
  </AppModal>

</template>
