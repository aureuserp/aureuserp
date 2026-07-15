import { Page, expect } from '@playwright/test';
import { ErpLocators } from '../locator/erp_locator';
import { invalidateSetup, PLUGIN_SETUP_KEYS } from '../utils/setupCache';

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

        for (let installed = 0; installed < pluginCount; installed++) {
            const pending = this.erpLocators.pluginNotInstalledCards;

            if (await pending.count() === 0) {
                break;
            }

            const card = pending.first();
            const pluginTitle = await this.pluginCardTitle(card);
            console.log(`Installing Plugin: ${pluginTitle}`);

            if (!await this.openCardActions(card)) {
                continue;
            }

            await this.erpLocators.pluginInstallButton.first().click({ timeout: 30000 });
            await this.page.waitForTimeout(3000); // Wait for 3 seconds to allow installation to complete
            await this.erpLocators.pluginConfirmButton.click();

            await this.waitForPluginState(pluginTitle, true);
        }

        await expect(this.erpLocators.pluginNotInstalledCards).toHaveCount(0, { timeout: 60000 });
    }

    /**
     * Uninstall all plugins
     */
    async uninstallAllPlugins() {

        invalidateSetup(...PLUGIN_SETUP_KEYS);

        const pluginCount = await this.erpLocators.pluginName.count();

        for (let uninstalled = 0; uninstalled < pluginCount; uninstalled++) {
            const remaining = this.erpLocators.pluginInstalledCards;

            if (await remaining.count() === 0) {
                break;
            }

            const card = remaining.first();
            const pluginTitle = await this.pluginCardTitle(card);
            console.log(`Uninstalling Plugin: ${pluginTitle}`);

            if (!await this.openCardActions(card)) {
                continue;
            }

            await this.erpLocators.pluginUninstallButton.first().click({ timeout: 30000 });
            await this.page.waitForTimeout(3000);
            await this.erpLocators.pluginConfirmButton.click();

            await this.waitForPluginState(pluginTitle, false);
        }

        await expect(this.erpLocators.pluginInstalledCards).toHaveCount(0, { timeout: 60000 });
    }

    /**
     * The plugin name is the card's first line of text.
     */
    private async pluginCardTitle(card: ReturnType<Page['locator']>): Promise<string> {
        return (await card.innerText()).split('\n')[0].trim();
    }

    /**
     * Wait for a plugin to reach its new state, read from its own card's badge. 
     */
    private async waitForPluginState(pluginTitle: string, installed: boolean): Promise<void> {
        await this.page.waitForLoadState('networkidle', { timeout: 300000 });

        const expected = installed ? /^\s*Installed\s*$/ : /^\s*Not Installed\s*$/;

        await expect(this.pluginCardByName(pluginTitle).locator('.fi-badge').filter({ hasText: expected }))
            .toHaveCount(1, { timeout: 300000 });
    }

    /**
     * A card matched on its name element, not on its whole text
     */
    private pluginCardByName(pluginTitle: string) {
        const exactName = new RegExp(`^\\s*${pluginTitle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*$`);

        return this.erpLocators.pluginCards
            .filter({ has: this.erpLocators.pluginName.filter({ hasText: exactName }) })
            .first();
    }

    /**
     * Open a card's actions dropdown, retrying when a re-render lands while it opens and
     * closes it again — leaving neither action on screen.
     */
    private async openCardActions(card: ReturnType<Page['locator']>): Promise<boolean> {
        for (let attempt = 0; attempt < 3; attempt++) {
            const actions = card.locator('button[title="Actions"]');

            if (await actions.count() === 0) {
                return false;
            }

            await expect(actions).toBeEnabled({ timeout: 120000 });
            await actions.click({ timeout: 30000 });

            const opened = await Promise.race([
                this.erpLocators.pluginInstallButton.first().waitFor({ state: 'visible', timeout: 5000 }).then(() => true).catch(() => false),
                this.erpLocators.pluginUninstallButton.first().waitFor({ state: 'visible', timeout: 5000 }).then(() => true).catch(() => false),
            ]);

            if (opened) {
                return true;
            }

            await this.page.keyboard.press('Escape');
            await this.page.waitForLoadState('networkidle');
            await this.page.waitForTimeout(1000);
        }

        throw new Error('The plugin actions dropdown never opened.');
    }

    /**
     * Install plugin by name if not installed
     */
    async installPluginByName(pluginName: string) {
        await this.searchPlugin(pluginName);

        const pluginTitle = await this.pluginCardTitle(this.erpLocators.pluginCards.first());

        if (await this.openPluginActionsAndCheckInstalled()) {
            return;
        }

        await this.page.waitForLoadState('networkidle');
        await this.erpLocators.pluginInstallButton.first().click({ timeout: 30000 });
        await this.page.waitForTimeout(3000);
        await this.erpLocators.pluginConfirmButton.click();

        await this.waitForPluginState(pluginTitle, true);
    }

    /**
     * Filter the list down to one plugin. 
     */
    private async searchPlugin(pluginName: string): Promise<void> {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.pluginSearchInput.fill(pluginName);
            await this.page.waitForLoadState('networkidle');
            await this.page.waitForTimeout(500);

            if (await this.erpLocators.pluginCards.count() === 1) {
                return;
            }
        }

        await expect(this.erpLocators.pluginCards).toHaveCount(1, { timeout: 30000 });
    }

    /**
     * Assert a plugin card's install state from its badge. 
     */
    private async expectPluginCardState(cardIndex: number, installed: boolean): Promise<void> {
        const badges = this.erpLocators.pluginCards.nth(cardIndex).locator('.fi-badge');
        const expected = installed ? /^\s*Installed\s*$/ : /^\s*Not Installed\s*$/;

        await expect(badges.filter({ hasText: expected })).toHaveCount(1, { timeout: 300000 });
    }

    /**
     * Wait for the install/uninstall request itself, which runs artisan on the server and
     * takes tens of seconds. 
     */
    private async waitForPluginActionToFinish() {
        await this.page.waitForLoadState('networkidle', { timeout: 300000 });
        await expect(this.erpLocators.pluginthreeDot.first()).toBeEnabled({ timeout: 300000 });
    }

    /**
     * Open a plugin's actions dropdown and report whether it is already installed.
     */
    private async openPluginActionsAndCheckInstalled(cardIndex = 0): Promise<boolean> {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.pluginthreeDot.nth(cardIndex).click({ timeout: 30000 });

            const uninstall = this.erpLocators.pluginUninstallButton.first();
            const install = this.erpLocators.pluginInstallButton.first();

            const opened = await Promise.race([
                uninstall.waitFor({ state: 'visible', timeout: 5000 }).then(() => 'installed').catch(() => null),
                install.waitFor({ state: 'visible', timeout: 5000 }).then(() => 'not-installed').catch(() => null),
            ]);

            if (opened === 'installed') {
                return true;
            }

            if (opened === 'not-installed') {
                return false;
            }

            await this.page.keyboard.press('Escape');
            await this.page.waitForLoadState('networkidle');
            await this.page.waitForTimeout(1000);
        }

        return false;
    }
}
