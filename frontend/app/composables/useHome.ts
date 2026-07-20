import type { HomePicture } from '~/types/api'

/**
 * GET /api/home → { pictures: HomePicture[] } (up to 5 random featured).
 */
export function useHome() {
  return useApiFetch<{ pictures: HomePicture[] }>('/home', {
    key: 'home',
  })
}
