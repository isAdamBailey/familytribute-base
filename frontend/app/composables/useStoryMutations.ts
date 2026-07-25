import type { Story } from '~/types/api'

export interface StoryFormFields {
  title: string
  excerpt: string
  content: string
  year?: string | number | null
  private: boolean
  person_ids: number[]
  media?: File | null
  remove_media?: boolean
}

export function useStoryMutations() {
  const { $api } = useNuxtApp()

  function createStory(fields: StoryFormFields) {
    return $api<{ story: Story }>('/stories', {
      method: 'POST',
      body: toFormData(fields),
    })
  }

  function updateStory(slug: string, fields: Partial<StoryFormFields>) {
    return $api<{ story: Story }>(`/stories/${slug}`, {
      method: 'POST',
      body: toFormData(fields, 'PUT'),
    })
  }

  function deleteStory(slug: string) {
    return $api(`/stories/${slug}`, { method: 'DELETE' })
  }

  return { createStory, updateStory, deleteStory }
}
