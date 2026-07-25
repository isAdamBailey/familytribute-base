<script setup lang="ts">
import type { SiteSettings } from '~/types/api'

const props = defineProps<{ settings: SiteSettings }>()

const { updateSiteSettings } = useSiteSettingsMutation()
const { flash } = useFlash()
// useSiteSettings() shares its state with layouts/default.vue's own call,
// which drives the header/footer site title — refreshing it here is what
// makes a save show up outside this form too, not just in its own fields.
const { refresh: refreshSettings } = await useSiteSettings()

const title = ref(props.settings.title)
const description = ref(props.settings.description)
const registration = ref(Boolean(props.settings.registration))
const registrationSecret = ref('')
const processing = ref(false)
const errors = ref<Record<string, string>>({})

const isDirty = computed(() =>
  title.value !== props.settings.title
  || description.value !== props.settings.description
  || registration.value !== Boolean(props.settings.registration)
  || registrationSecret.value !== '',
)

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    const { settings } = await updateSiteSettings(props.settings.id, {
      title: title.value,
      description: description.value,
      registration: registration.value,
      registration_secret: registrationSecret.value || undefined,
    })
    title.value = settings.title
    description.value = settings.description
    registration.value = Boolean(settings.registration)
    registrationSecret.value = ''
    await refreshSettings()
    flash('Settings successfully updated!')
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form id="site-settings" data-testid="site-settings-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Site Settings</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">Settings that apply to the entire site.</p>

    <div>
      <label for="title" class="block text-sm font-semibold">Title</label>
      <p class="text-xs text-faded-ink dark:text-old-binding">
        Shown at the top of every page, in emails, and when sharing pages to social media.
      </p>
      <input id="title" v-model="title" type="text" class="form-input mt-1">
      <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
    </div>

    <div>
      <label class="block text-sm font-semibold">Description</label>
      <p class="text-xs text-faded-ink dark:text-old-binding">Shown on the home page and when sharing to social media.</p>
      <WysiwygEditor v-model="description" test-id="site-settings-description" />
      <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
    </div>

    <div>
      <label for="registration" class="block text-sm font-semibold">Registration Enabled</label>
      <p class="text-xs text-faded-ink dark:text-old-binding">Turning this off removes the ability for anyone to register.</p>
      <input id="registration" v-model="registration" data-testid="registration-enabled" type="checkbox" class="mt-1">
      <p v-if="errors.registration" class="mt-1 text-sm text-red-600">{{ errors.registration }}</p>
    </div>

    <div v-if="registration">
      <label for="registration_secret" class="block text-sm font-semibold">Registration Secret</label>
      <p class="text-xs text-faded-ink dark:text-old-binding">New users must enter this secret to register.</p>
      <input id="registration_secret" v-model="registrationSecret" type="text" class="form-input mt-1" placeholder="Unchanged">
      <p v-if="errors.registration_secret" class="mt-1 text-sm text-red-600">{{ errors.registration_secret }}</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" data-testid="site-settings-submit" class="btn-primary" :disabled="processing || !isDirty">
        Save
      </button>
    </div>
  </form>
</template>
