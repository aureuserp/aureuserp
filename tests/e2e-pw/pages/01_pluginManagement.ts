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
     * Show every plugin on a single page. 
     */
    private async showAllPluginsOnOnePage() {
        if (await this.erpLocators.pluginsPerPageSelect.count() === 0) {
            return;
        }

        await this.erpLocators.pluginsPerPageSelect.selectOption('32');
        await this.page.waitForLoadState('networkidle');
    }

    /**
     * Install/uninstall every plugin on the matching tab.
     * Installs are independent: installing a plugin pulls in its dependencies,
     * Uninstalls are dependency-ordered: a plugin whose dependents are still installed cannot be removed
     */
    private async processAllPlugins(action: PluginAction) {
        const tab = action === 'install' ? 'not_installed' : 'installed';

        await this.gotoPluginTab(tab);
        await this.showAllPluginsOnOnePage();

        const total = await this.erpLocators.pluginName.count();
        console.log(`Plugins to ${action}: ${total}`);

        let guard = total + 5;

        while (await this.erpLocators.pluginName.count() > 0 && guard-- > 0) {
            const acted = action === 'install'
                ? await this.installFirstPlugin()
                : await this.uninstallFirstAvailablePlugin();

            if (!acted) {
                throw new Error(`No "${tab}" plugin could be ${action}ed; the remaining plugins may form an unresolved dependency order.`);
            }

            await this.gotoPluginTab(tab);
        }

        await expect(this.erpLocators.pluginName).toHaveCount(0);
        console.log(`All plugins ${action === 'install' ? 'installed' : 'uninstalled'}`);
    }

    /**
     * Install the first plugin listed on the current tab
     */
    private async installFirstPlugin(): Promise<boolean> {
        if (await this.erpLocators.pluginName.count() === 0) {
            return false;
        }

        const pluginTitle = (await this.erpLocators.pluginName.first().innerText()).trim();
        console.log(`Installing plugin: ${pluginTitle}`);

        await this.erpLocators.pluginActionsButton.first().click();
        await expect(this.erpLocators.pluginInstallOption).toBeVisible();
        await this.erpLocators.pluginInstallOption.click();

        await expect(this.erpLocators.pluginInstallConfirmButton).toBeVisible();
        await this.erpLocators.pluginInstallConfirmButton.click();

        await this.expectActionSucceeded('install', pluginTitle);
        return true;
    }

    /**
     * Uninstall the first plugin on the tab that has no installed dependents.
     */
    private async uninstallFirstAvailablePlugin(): Promise<boolean> {
        const count = await this.erpLocators.pluginName.count();

        for (let i = 0; i < count; i++) {
            const pluginTitle = (await this.erpLocators.pluginName.nth(i).innerText()).trim();

            await this.erpLocators.pluginActionsButton.nth(i).click();
            await expect(this.erpLocators.pluginUninstallOption).toBeVisible();
            await this.erpLocators.pluginUninstallOption.click();
            await expect(this.erpLocators.pluginUninstallModalReady).toBeVisible();

            if (!await this.erpLocators.pluginUninstallConfirmButton.isVisible()) {
                console.log(`Skipping "${pluginTitle}" - dependent plugins still installed`);
                await this.closeOpenModal();
                continue;
            }

            console.log(`Uninstalling plugin: ${pluginTitle}`);
            await this.erpLocators.pluginUninstallConfirmButton.click();
            await this.expectActionSucceeded('uninstall', pluginTitle);
            return true;
        }

        return false;
    }

    /**
     * Dismiss the open modal and wait for it to disappear
     */
    private async closeOpenModal() {
        await this.page.keyboard.press('Escape');
        await expect(this.erpLocators.pluginUninstallModalReady).toBeHidden();
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
