import {  Page, expect } from '@playwright/test';
import { ErpLocators } from '../locator/erp_locator';

export class PluginManagementPage {

    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page

        this.erpLocators = new ErpLocators(page);
    }

    async gotoPluginManagementPage() {
        await this.page.goto('/admin/plugins');
        await expect(this.page).toHaveURL(/.*admin/);
        await expect(this.erpLocators.pluginSyncButton).toBeVisible();
    }

    async installPlugin() {
        const pluginCount = await this.erpLocators.pluginName.count();
        for (let i = 0; i < pluginCount; i++) {

            await this.erpLocators.pluginthreeDot.nth(i).click();
            const checkInstalled = await this.erpLocators.pluginUninstallButton.nth(i).isVisible();

            if (!checkInstalled) {
                await this.page.waitForLoadState('networkidle');
                await this.erpLocators.pluginInstallButton.nth(0).click();
                await this.page.waitForTimeout(3000); // Wait for 3 seconds to allow installation to complete
                await this.erpLocators.pluginConfirmButton.click();
                const pluginTitle = await this.erpLocators.pluginName.nth(i).innerText();
                console.log(`Installing Plugin: ${pluginTitle}`);
                await expect(this.erpLocators.pluginSuccessMessage).toBeVisible();
            }
        }
    }

    async uninstallPlugin() {
        const pluginCount = await this.erpLocators.pluginName.count();
        for (let i = 0; i < pluginCount; i++) {

            await this.erpLocators.pluginthreeDot.nth(i).click();
            const checkInstalled = await this.erpLocators.pluginUninstallButton.nth(0).isVisible();

            if (checkInstalled) {
                await this.page.waitForLoadState('networkidle');
                await this.page.waitForTimeout(2000);
                await this.erpLocators.pluginUninstallButton.nth(0).click();
                await this.page.waitForTimeout(5000);
                await this.erpLocators.pluginConfirmButton.click();
                const pluginTitle = await this.erpLocators.pluginName.nth(i).innerText();
                console.log(`Uninstalling Plugin: ${pluginTitle}`);
                await expect(this.erpLocators.pluginSuccessMessage).toBeVisible();
            }
        }
    }
}