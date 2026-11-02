import { Locator, Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export class CRMFlowPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    async ensureCRMPluginInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("CRM");
    }

    /**
     * Navigate, retrying when a redirect still in flight from the previous page aborts or
     * interrupts this one.
     */
    private async safeGoto(url: string) {
        await this.page.waitForLoadState("domcontentloaded").catch(() => undefined);

        for (let attempt = 0; attempt < 3; attempt++) {
            try {
                await this.page.goto(url);
                return;
            } catch (error) {
                if (!/ERR_ABORTED|interrupted by another navigation/.test((error as Error).message)) {
                    throw error;
                }
                await this.page.waitForTimeout(500);
            }
        }

        await this.page.goto(url);
    }

    /**
     * Opens the pipeline board on the "Default" tab, so cards of every salesperson show
     * (quick-created opportunities have no salesperson, and "My Pipeline" would hide them).
     */
    async gotoPipeline() {
        await this.safeGoto("/admin/crm/sales/pipeline?activeTableView=default");
        await expect(this.page).toHaveURL(/crm\/sales\/pipeline/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.erpLocators.crmKanbanBoard).toBeVisible();
        await expect(this.erpLocators.crmKanbanColumns.nth(1)).toBeVisible();
    }

    column(index: number): Locator {
        return this.erpLocators.crmKanbanColumns.nth(index);
    }

    card(name: string): Locator {
        return this.page.locator("[data-kanban-card]").filter({ hasText: name });
    }

    async columnCount(index: number): Promise<number> {
        return Number(await this.column(index).locator("[data-kanban-count]").getAttribute("data-kanban-count"));
    }

    async quickCreateOpportunity(columnIndex: number, name: string) {
        await this.column(columnIndex).locator('button[wire\\:click^="mountAction(\'kanbanColumn\'"]').click();

        await expect(this.erpLocators.crmQuickCreateNameInput).toBeVisible();
        await this.erpLocators.crmQuickCreateNameInput.fill(name);
        await this.erpLocators.crmQuickCreateSubmitButton.click();

        await expect(this.column(columnIndex).locator("[data-kanban-card]").filter({ hasText: name })).toBeVisible();
    }

    /**
     * Drags with real pointer steps: the board runs SortableJS in pointer (fallback) mode,
     * so a single jump or an HTML5 drag event would not move the card. The board saves
     * moves in one batch a moment after the last drop, so this waits for that save.
     */
    async dragCardToColumn(name: string, targetIndex: number) {
        const saved = this.page.waitForResponse(
            (response) => response.url().includes("/update") && (response.request().postData() ?? "").includes("moveKanbanRecords")
        );

        const from = await this.card(name).boundingBox();
        const to = await this.column(targetIndex).locator("[data-kanban-column]").boundingBox();

        expect(from).not.toBeNull();
        expect(to).not.toBeNull();

        const startX = from!.x + 40;
        const startY = from!.y + 20;
        const endX = to!.x + to!.width / 2;
        const endY = to!.y + 30;

        await this.page.mouse.move(startX, startY);
        await this.page.mouse.down();
        await this.page.mouse.move(startX + 10, startY + 10, { steps: 3 });
        await this.page.mouse.move(endX, endY, { steps: 20 });
        await this.page.mouse.up();
        await saved;
    }

    async expectCardInColumn(name: string, index: number) {
        await expect(this.column(index).locator("[data-kanban-card]").filter({ hasText: name })).toBeVisible();
    }

    async deleteOpportunity(name: string) {
        const card = this.card(name);

        await this.page.mouse.move(0, 0);
        await card.hover();
        await card.locator(".fi-dropdown-trigger button").click();
        await this.erpLocators.crmCardMenuDeleteOption.click();
        await this.erpLocators.crmConfirmDeleteButton.click();

        await expect(card).toHaveCount(0);
    }
}
