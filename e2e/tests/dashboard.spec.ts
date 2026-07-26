import path from 'node:path';
import { test, expect } from '@playwright/test';
import { FIXTURES, SEED } from '../constants';
import { expectFlash, fillDate, fillWysiwyg, logout } from '../helpers/auth';
import { gotoHydrated, loginViaApi } from '../helpers/nuxtAuth';

// Dashboard CRUD coverage (issue #19, Phase 4). loginViaApi() establishes a
// session directly rather than driving the real login UI — nuxt-auth.spec.ts
// covers the login page itself, so this test only needs to exercise the
// dashboard forms, edit/delete modals, and flash banner.

const photo = path.resolve(FIXTURES.photo);
const audio = path.resolve(FIXTURES.audio);

test.describe('Nuxt dashboard CRUD', () => {
  test.beforeEach(async ({ page }) => {
    await loginViaApi(page);
  });

  test('create, edit, and delete a person/obituary', async ({ page }) => {
    const first = `E2ENuxt${Date.now()}`;
    const last = 'Person';
    const fullName = `${first} ${last}`;
    const slug = `${first.toLowerCase()}-person`;

    await gotoHydrated(page, '/dashboard');
    await expect(page.getByText(/Welcome to .*dashboard/i)).toBeVisible();

    const form = page.getByTestId('person-create-form');
    await form.locator('#first_name').fill(first);
    await form.locator('#last_name').fill(last);
    await fillDate(page, 'person-birth-date', '1901-01-15');
    await fillDate(page, 'person-death-date', '1980-06-01');
    await fillWysiwyg(page, 'person-obituary', 'An e2e obituary for a memorable life.');
    await form.getByTestId('person-photo-input').setInputFiles(photo);
    await form.getByTestId('person-create-submit').click();
    await expectFlash(page, 'Obituary successfully created!');

    await page.goto(`/${slug}`);
    await expect(page.getByRole('heading', { name: fullName })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Person' }).click();
    await page.locator('#first_name').fill(`${first} Updated`);
    const editEditor = page.locator('.ProseMirror').last();
    await editEditor.click();
    await page.keyboard.press(process.platform === 'darwin' ? 'Meta+A' : 'Control+A');
    await page.keyboard.insertText('Updated e2e obituary content.');
    await page.getByRole('button', { name: 'Update Obituary' }).click();
    await expectFlash(page, 'Obituary successfully updated!');
    await expect(page.getByRole('heading', { name: `${first} Updated ${last}` })).toBeVisible();

    await page.getByLabel('Delete Person').click();
    await page.getByTestId('confirm-delete-person').click();
    await expectFlash(page, 'Obituary successfully deleted!');
    await expect(page).toHaveURL('/people');
  });

  test('create, edit, and delete a picture', async ({ page }) => {
    const title = `E2E Nuxt Picnic ${Date.now()}`;
    const slug = title.toLowerCase().replace(/\s+/g, '-');

    await gotoHydrated(page, '/dashboard');
    const form = page.getByTestId('picture-create-form');
    await form.getByTestId('picture-photo-input').setInputFiles(photo);
    await form.locator('#title').fill(title);
    await fillWysiwyg(page, 'picture-description', 'An e2e picture description.');
    await form.locator('#year').fill('1977');
    await form.getByTestId('picture-create-submit').click();
    await expectFlash(page, 'Picture successfully created!');

    await page.goto(`/pictures/${slug}`);
    await expect(page.getByRole('heading', { name: title })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Picture' }).click();
    await page.locator('#title').fill(`${title} Edited`);
    await page.getByRole('button', { name: 'Update Picture' }).click();
    await expectFlash(page, 'Picture successfully updated!');
    // Editing the title regenerates the slug, so this triggers a client-side
    // navigateTo() to the new URL (see useSlugFollow) — wait for it to settle
    // before continuing, or the next click can land mid-transition on an
    // element that's about to be torn down.
    await expect(page.getByRole('heading', { name: `${title} Edited` })).toBeVisible();

    await page.getByLabel('Delete Picture').click();
    await page.getByTestId('confirm-delete-picture').click();
    await expectFlash(page, 'Picture successfully deleted!');
    await expect(page).toHaveURL('/pictures');
  });

  test('create, edit, and delete a story with media', async ({ page }) => {
    const title = `E2E Nuxt Story ${Date.now()}`;
    const slug = title.toLowerCase().replace(/\s+/g, '-');

    await gotoHydrated(page, '/dashboard');
    const form = page.getByTestId('story-create-form');
    await form.locator('#title').fill(title);
    await fillWysiwyg(page, 'story-excerpt', 'Short e2e excerpt.');
    await fillWysiwyg(page, 'story-content', 'Full e2e story content.');
    await form.locator('#year').fill('1988');
    await form.getByTestId('story-media-input').setInputFiles(audio);
    await form.getByTestId('story-create-submit').click();
    await expectFlash(page, 'Story successfully created!');

    await page.goto(`/stories/${slug}`);
    await expect(page.getByRole('heading', { name: title })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Story' }).click();
    await page.locator('#title').fill(`${title} Edited`);
    await page.getByRole('button', { name: 'Update Story' }).click();
    await expectFlash(page, 'Story successfully updated!');
    // Editing the title regenerates the slug, so this triggers a client-side
    // navigateTo() to the new URL (see useSlugFollow) — wait for it to settle
    // before continuing, or the next click can land mid-transition on an
    // element that's about to be torn down.
    await expect(page.getByRole('heading', { name: `${title} Edited` })).toBeVisible();

    await page.getByLabel('Delete Story').click();
    await page.getByTestId('confirm-delete-story').click();
    await expectFlash(page, 'Story successfully deleted!');
    await expect(page).toHaveURL('/stories');
  });

  test('update site settings and log out', async ({ page }) => {
    await gotoHydrated(page, '/dashboard');
    const form = page.getByTestId('site-settings-form');

    try {
      await form.locator('#title').fill('Family Tribute Nuxt E2E');
      await expect(form.getByTestId('site-settings-submit')).toBeEnabled();
      await form.getByTestId('site-settings-submit').click();
      await expectFlash(page, 'Settings successfully updated!');
      await expect(page.getByText('Family Tribute Nuxt E2E').first()).toBeVisible();
    } finally {
      await gotoHydrated(page, '/dashboard');
      const restore = page.getByTestId('site-settings-form');
      await restore.locator('#title').fill(SEED.siteTitle);
      await expect(restore.getByTestId('site-settings-submit')).toBeEnabled();
      await restore.getByTestId('site-settings-submit').click();
      await expectFlash(page, 'Settings successfully updated!');
    }

    await page.goto('/');
    await logout(page);
  });
});
