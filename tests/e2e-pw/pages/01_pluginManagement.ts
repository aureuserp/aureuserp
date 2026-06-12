import { Page, expect } from '@playwright/test';
import { ErpLocators } from '../locator/erp_locator';

type PluginAction = 'install' | 'uninstall';

export class PluginManagementPage {

    /**
     * Page and Locators
     */
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;

        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Navigate to Plugin Management Page
     */
    async gotoPluginManagementPage() {
        await this.page.goto('/admin/plugins');
        await expect(this.page).toHaveURL(/.*admin\/plugins/);
        await expect(this.erpLocators.pluginSyncButton).toBeVisible();
    }

    /**
     * Install every plugin listed on the "Not Installed" tab
     */
    async installAllPlugins() {
        await this.processAllPlugins('install');
    }

    /**
     * Uninstall every plugin listed on the "Installed" tab
     */
    async uninstallAllPlugins() {
        await this.processAllPlugins('uninstall');
    }

    /**
     * Install plugin by name if not installed
     */
    async installPluginByName(pluginName: string) {
        await this.erpLocators.pluginSearchInput.fill(pluginName);
        await this.page.waitForTimeout(1000); // table search debounce
        await this.page.waitForLoadState('networkidle');
        await expect(this.erpLocators.pluginName.first()).toContainText(pluginName, { ignoreCase: true });

        await this.erpLocators.pluginActionsButton.first().click();
        await expect(this.erpLocators.pluginDropdownPanel).toBeVisible();

        if (await this.erpLocators.pluginUninstallOption.isVisible()) {
            console.log(`Plugin "${pluginName}" is already installed, skipping installation`);
            await this.page.keyboard.press('Escape');
            return;
        }

        console.log(`Installing plugin: ${pluginName}`);
        await this.erpLocators.pluginInstallOption.click();
        await expect(this.erpLocators.pluginInstallConfirmButton).toBeVisible();
        await this.erpLocators.pluginInstallConfirmButton.click();
        await this.expectActionSucceeded('install', pluginName);
    }

    /**
     * Open the plugins list filtered to the tab holding the remaining work
     */
    private async gotoPluginTab(tab: 'installed' | 'not_installed') {
        await this.page.goto(`/admin/plugins?tab=${tab}`);
        await expect(this.erpLocators.pluginSyncButton).toBeVisible();
        await this.page.waitForLoadState('networkidle');
    }

    /**
     * Install/uninstall every plugin on the matching tab. The tab always
     * contains exactly the remaining plugins, so acting on the first card
     * until the tab is empty survives pagination, list reordering and the
     * full-page redirect the app performs after every install/uninstall.
     */
    private async processAllPlugins(action: PluginAction) {
        const tab = action === 'install' ? 'not_installed' : 'installed';

        await this.gotoPluginTab(tab);

        const total = await this.erpLocators.pluginName.count();
        console.log(`Plugins to ${action}: ${total}`);

        for (let i = 0; i < total; i++) {
            if (await this.erpLocators.pluginName.count() === 0) {
                break;
            }

            const pluginTitle = (await this.erpLocators.pluginName.first().innerText()).trim();
            console.log(`${action === 'install' ? 'Installing' : 'Uninstalling'} plugin: ${pluginTitle}`);

            await this.erpLocators.pluginActionsButton.first().click();

            const actionOption = action === 'install'
                ? this.erpLocators.pluginInstallOption
                : this.erpLocators.pluginUninstallOption;
            await expect(actionOption).toBeVisible();
            await actionOption.click();

            const confirmButton = action === 'install'
                ? this.erpLocators.pluginInstallConfirmButton
                : this.erpLocators.pluginUninstallConfirmButton;
            await expect(confirmButton).toBeVisible();
            await confirmButton.click();

            await this.expectActionSucceeded(action, pluginTitle);

            await this.gotoPluginTab(tab);
        }

        await expect(this.erpLocators.pluginName).toHaveCount(0);
        console.log(`All plugins ${action === 'install' ? 'installed' : 'uninstalled'}`);
    }

    /**
     * Wait for the action's success notification; surface the plugin name
     * immediately if the app reports a failure instead
     */
    private async expectActionSucceeded(action: PluginAction, pluginTitle: string) {
        const successNotification = action === 'install'
            ? this.erpLocators.pluginInstallSuccessNotification
            : this.erpLocators.pluginUninstallSuccessNotification;
        const failureNotification = this.erpLocators.pluginActionFailedNotification;

        await expect(successNotification.or(failureNotification).first()).toBeVisible({ timeout: 320_000 });

        if (await failureNotification.isVisible()) {
            throw new Error(`Failed to ${action} plugin "${pluginTitle}" - the app reported: ${await failureNotification.innerText()}`);
        }

        console.log(`${action === 'install' ? 'Installed' : 'Uninstalled'} plugin: ${pluginTitle}`);
    }
}
