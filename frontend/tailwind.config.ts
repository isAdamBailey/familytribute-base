import type { Config } from 'tailwindcss'
import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import scrollbar from 'tailwind-scrollbar'

// Ported from the Inertia app's tailwind.config.js (issue #19, Phase 2).
// Keep the token values in sync with that file / DESIGN.md until the Inertia
// frontend is retired at cutover (Phase 6).
export default <Partial<Config>>{
  darkMode: 'class',
  content: [
    './app/components/**/*.{vue,js,ts}',
    './app/layouts/**/*.vue',
    './app/pages/**/*.vue',
    './app/composables/**/*.{js,ts}',
    './app/plugins/**/*.{js,ts}',
    './app/app.vue',
    './app/error.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        // Fixes the "OpenSans" (no space) mismatch in the Inertia config —
        // Google serves the family as "Open Sans".
        sans: ['Open Sans', ...defaultTheme.fontFamily.sans],
        header: ['Gwendolyn', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        hearthlight: {
          DEFAULT: '#bf8028',
          deep: '#8b5c18',
          subtle: '#fdf3e0',
        },
        'album-rose': '#c47070',
        inkwell: '#1c1410',
        'faded-ink': '#584038',
        'old-binding': '#8c7468',
        'aged-edge': '#d4c2b4',
      },
      // Amber-tinted shadow tokens from DESIGN.md — never cool gray. These are
      // spec-only in the Inertia config; promoted to real utilities here so
      // pages ported in Phase 3+ can use them directly.
      boxShadow: {
        card: '0 4px 24px rgba(140, 80, 30, 0.10)',
        'card-hover': '0 8px 40px rgba(140, 80, 30, 0.18)',
        surface: '0 2px 12px rgba(140, 80, 30, 0.08)',
        modal: '0 20px 60px rgba(28, 20, 16, 0.22)',
        'button-hover': '0 4px 20px rgba(140, 80, 30, 0.30)',
        'input-focus': '0 0 0 3px rgba(191, 128, 40, 0.15)',
      },
    },
  },
  plugins: [forms, typography, scrollbar],
}
