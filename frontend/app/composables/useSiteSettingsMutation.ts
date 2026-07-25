import type { SiteSettings } from '~/types/api'

export interface SiteSettingsFormFields {
  title: string
  description: string
  registration: boolean
  registration_secret?: string
}

export function useSiteSettingsMutation() {
  const { $api } = useNuxtApp()

  function updateSiteSettings(id: number, fields: Partial<SiteSettingsFormFields>) {
    return $api<{ settings: SiteSettings }>(`/site-settings/${id}`, {
      method: 'PUT',
      body: fields,
    })
  }

  return { updateSiteSettings }
}
