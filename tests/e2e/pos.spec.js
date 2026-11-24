import { test, expect } from '@playwright/test';

test.describe('POS - Caisse', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@sugupro.test');
        await page.fill('input[name="password"]', 'MotDePasse!2026');
        await page.click('button[type="submit"]');
        await page.waitForURL(/home|dashboard/);
    });

    test('page caisse accessible', async ({ page }) => {
        await page.goto('/pos');
        await expect(page.locator('body')).toContainText(/Caisse|caisse|produit|Produit/i);
    });

    test('ouverture de caisse', async ({ page }) => {
        await page.goto('/pos/open');
        await page.fill('input[name="opening_amount"]', '10000');
        const tenantSelect = page.locator('select[name="tenant_id"]');
        if (await tenantSelect.isVisible()) {
            await tenantSelect.selectOption({ index: 1 });
        }
        await page.getByRole('button', { name: 'Ouvrir' }).click();
        await expect(page).toHaveURL(/pos/);
    });
});