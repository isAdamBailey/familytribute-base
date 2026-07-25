import type { Person } from '~/types/api'

export interface ObituaryFormFields {
  first_name: string
  last_name: string
  content: string
  birth_date: string
  death_date: string
  photo?: File | null
  background_photo?: File | null
  parent_ids: number[]
  child_ids?: number[]
}

export function useObituaryMutations() {
  const { $api } = useNuxtApp()

  function createObituary(fields: ObituaryFormFields) {
    return $api<{ person: Person }>('/obituaries', {
      method: 'POST',
      body: toFormData(fields),
    })
  }

  function updateObituary(obituaryId: number, fields: Partial<ObituaryFormFields>) {
    return $api<{ person: Person }>(`/obituaries/${obituaryId}`, {
      method: 'POST',
      body: toFormData(fields, 'PUT'),
    })
  }

  function deleteObituary(obituaryId: number) {
    return $api(`/obituaries/${obituaryId}`, { method: 'DELETE' })
  }

  return { createObituary, updateObituary, deleteObituary }
}
