import {  Page, expect } from '@playwright/test';
import { ErpLocators } from '../locator/erp_locator';

export class PluginManagementPage {

    /**
     * Page and Locators
     */
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page

        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Navigate to Plugin Management Page
     */
    async gotoPluginManagementPage() {
        await this.page.goto('/admin/plugins');
        await expect(this.page).toHaveURL(/.*admin/);
        await expect(this.erpLocators.pluginSyncButton).toBeVisible();
    }

    /**
     * Install all plugins
     */
    async installAllPlugins() {
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

    /**
     * Uninstall all plugins
     */
    async uninstallAllPlugins() {
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

    /**
     * Install plugin by name if not installed
     */
    async installPluginByName(pluginName: string) {
        await this.erpLocators.pluginSearchInput.fill(pluginName);
        await this.page.waitForTimeout(1000);
        await this.erpLocators.pluginthreeDot.first().click();

        if (await this.erpLocators.pluginUninstallButton.first().isVisible()) {
            return;
        }

        await this.page.waitForLoadState('networkidle');
        await this.erpLocators.pluginInstallButton.first().click();
        await this.page.waitForTimeout(3000);
        await this.erpLocators.pluginConfirmButton.click();
        await expect(this.erpLocators.pluginSuccessMessage).toBeVisible();
    }

    // /**
    //  * Install Accounting Plugin
    //  */
    // async AccountingInstall() {
    //     await this.erpLocators.pluginSearchInput.fill('Accounting');
    //     await this.page.waitForTimeout(2000);
    //     await this.erpLocators.pluginthreeDot.click();
    //     if (await this.erpLocators.pluginUninstallButton.isVisible()) {
    //         console.log('Accounting Plugin is already installed.');
    //         return;
    //     }else {
    //     await this.erpLocators.pluginInstallButton.click();
    //     await this.page.waitForTimeout(3000); // Wait for 3 seconds to allow installation to complete
    //     await this.erpLocators.pluginConfirmButton.click();
    //     console.log(`Installing Plugin: Accounting`);
    //     await expect(this.erpLocators.pluginSuccessMessage).toBeVisible();
    //     }
    // }

}
