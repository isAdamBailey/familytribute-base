import type { Picture } from '~/types/api'

export interface PictureFormFields {
  title: string
  description: string
  year: string | number
  featured: boolean
  private: boolean
  photo?: File | null
  person_ids: number[]
}

export function usePictureMutations() {
  const { $api } = useNuxtApp()

  function createPicture(fields: PictureFormFields) {
    return $api<{ picture: Picture }>('/pictures', {
      method: 'POST',
      body: toFormData(fields),
    })
  }

  function updatePicture(slug: string, fields: Partial<PictureFormFields>) {
    // PUT with a multipart body doesn't parse in PHP, so spoof via POST + _method.
    return $api<{ picture: Picture }>(`/pictures/${slug}`, {
      method: 'POST',
      body: toFormData(fields, 'PUT'),
    })
  }

  function deletePicture(slug: string) {
    return $api(`/pictures/${slug}`, { method: 'DELETE' })
  }

  return { createPicture, updatePicture, deletePicture }
}
