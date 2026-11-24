import { test, expect } from '@playwright/test';

test.describe('Authentification', () => {
    test('redirige les invités vers la page de connexion', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveURL(/login/);
    });

    test('connexion avec identifiants valides', async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@sugupro.test');
        await page.fill('input[name="password"]', 'MotDePasse!2026');
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL(/home|dashboard/);
    });
});