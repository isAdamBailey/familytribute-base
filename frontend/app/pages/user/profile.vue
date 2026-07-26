<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'verified'] })

const { data: settings } = await useSiteSettings()
const siteTitle = computed(() => settings.value?.settings?.title ?? 'Family Tribute')

useSeoMeta({ title: () => `Profile | ${siteTitle.value}` })
</script>

<template>
  <div class="space-y-14">
    <h1 class="font-header text-4xl text-hearthlight-deep dark:text-hearthlight">Profile</h1>

    <UpdateProfileInformationForm />
    <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

    <UpdatePasswordForm />
    <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

    <TwoFactorAuthenticationForm />
    <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

    <LogoutOtherBrowserSessionsForm />
    <div class="h-px bg-hearthlight-subtle dark:bg-old-binding/30" />

    <DeleteUserForm />
  </div>
</template>
