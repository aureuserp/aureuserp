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

        await expect(this.erpLocators.pluginName.first()).toBeVisible();

        const pluginTitle = (await this.erpLocators.pluginName.first().innerText()).trim();

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
        await this.expectActionSucceeded('install', pluginTitle);
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
        await this.gotoPluginTab('installed');

        const names = (await this.erpLocators.pluginName.allInnerTexts()).map(name => name.trim());

        for (let i = 0; i < names.length; i++) {
            const pluginTitle = names[i];

            await this.erpLocators.pluginActionsButton.nth(i).click();
            await expect(this.erpLocators.pluginUninstallOption).toBeVisible();
            await this.erpLocators.pluginUninstallOption.click();
            await expect(this.erpLocators.pluginUninstallModalReady).toBeVisible();

            if (await this.erpLocators.pluginUninstallDependencyWarning.isVisible()) {
                console.log(`Skipping "${pluginTitle}" - dependent plugins still installed`);
                await this.page.keyboard.press('Escape');
                await expect(this.erpLocators.pluginUninstallModalReady).toBeHidden();
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
     * Wait for the action to finish and verify its outcome from the plugin lists.
     * The toast is not a completion signal: it is flashed to the page the action
     * redirects to and auto-dismisses, so it can be gone before it is observed.
     */
    private async expectActionSucceeded(action: PluginAction, pluginTitle: string) {
        const confirmButton = action === 'install'
            ? this.erpLocators.pluginInstallConfirmButton
            : this.erpLocators.pluginUninstallConfirmButton;
        const tab = action === 'install' ? 'not_installed' : 'installed';

        await expect(confirmButton).toBeHidden({ timeout: 320_000 });
        await this.page.waitForLoadState('networkidle');

        const failureNotification = this.erpLocators.pluginActionFailedNotification;
        const failureMessage = await failureNotification.isVisible()
            ? await failureNotification.innerText()
            : null;

        await this.gotoPluginTab(tab);
        await this.showAllPluginsOnOnePage();

        const stillListed = await this.erpLocators.pluginName
            .filter({ hasText: new RegExp(`^\\s*${pluginTitle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*$`) })
            .count();

        if (stillListed > 0) {
            throw new Error(`Failed to ${action} plugin "${pluginTitle}"${failureMessage ? ` - the app reported: ${failureMessage}` : ' - it is still listed on the "' + tab + '" tab'}`);
        }

        console.log(`${action === 'install' ? 'Installed' : 'Uninstalled'} plugin: ${pluginTitle}`);
    }
}
