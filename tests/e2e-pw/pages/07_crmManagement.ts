import { Locator, Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type CRMLayout = "list" | "kanban" | "graph" | "grid";

const escapeRegExp = (value: string) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

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
     * Fill a field once its form is done hydrating: a value typed before Livewire binds the
     * field stays in the input but never reaches the form, so it is read back and retyped.
     */
    private async fillWhenReady(input: Locator, value: string) {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(input).toBeVisible();

        for (let attempt = 0; attempt < 3; attempt++) {
            await input.fill(value);

            if ((await input.inputValue()) === value) {
                return;
            }

            await this.page.waitForTimeout(500);
        }

        await expect(input).toHaveValue(value);
    }

    async gotoCRMPage(path: string, title: RegExp) {
        await this.safeGoto(path);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).toHaveTitle(title);
    }

    /**
     * Opens a page in one of its view layouts (the switcher keeps the choice in ?layout=) and
     * checks both the selected switcher button and the content that layout renders.
     */
    async expectLayout(path: string, layout: CRMLayout) {
        await this.safeGoto(`${path}${path.includes("?") ? "&" : "?"}layout=${layout}`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await expect(this.erpLocators.crmSelectedLayout).toHaveAccessibleName(new RegExp(`^${layout}$`, "i"));

        const content = {
            list: this.erpLocators.crmListContent,
            kanban: this.erpLocators.crmKanbanContent,
            graph: this.erpLocators.crmGraphContent,
            grid: this.erpLocators.crmGridContent,
        }[layout];

        await expect(content).toBeVisible();
    }

    /**
     * Boards grouped by a field (leads, customers, quotations) open with their default Group by
     * already selected, so the board renders instead of asking for a Group by.
     */
    async expectDefaultGroupBy(path: string, group: string) {
        await this.safeGoto(`${path}${path.includes("?") ? "&" : "?"}layout=kanban`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await expect(this.erpLocators.crmGroupBySelect).toHaveValue(group);
        await expect(this.erpLocators.crmGroupByPrompt).toHaveCount(0);
        await expect(this.erpLocators.crmKanbanContent).toBeVisible();
    }

    tableRow(name: string): Locator {
        return this.page.locator(".fi-ta-row").filter({ hasText: name });
    }

    /**
     * Opens a list filtered by the table search, which Filament keeps in ?search=.
     */
    async searchList(path: string, name: string) {
        const separator = path.includes("?") ? "&" : "?";

        await this.safeGoto(`${path}${separator}search=${encodeURIComponent(name)}`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.erpLocators.crmListContent).toBeVisible();
    }

    async expectListed(path: string, name: string) {
        await this.searchList(path, name);
        await expect(this.tableRow(name).first()).toBeVisible();
    }

    async expectNotListed(path: string, name: string) {
        await this.searchList(path, name);
        await expect(this.tableRow(name)).toHaveCount(0);
    }

    /**
     * Creates a record through its full-page create form and returns the view page it lands on.
     */
    async createRecord(createPath: string, name: string): Promise<string> {
        await this.safeGoto(createPath);
        await this.fillWhenReady(this.erpLocators.crmFormNameInput, name);
        await this.erpLocators.crmFormCreateButton.click();

        await this.page.waitForURL(/\/\d+$/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page.getByText(name).first()).toBeVisible();

        return new URL(this.page.url()).pathname;
    }

    async editRecord(viewPath: string, name: string) {
        await this.safeGoto(`${viewPath}/edit`);
        await this.fillWhenReady(this.erpLocators.crmFormNameInput, name);
        await this.erpLocators.crmFormSaveButton.click();
        await expect(this.page.locator(".fi-no-notification-title").first()).toBeVisible();

        await this.safeGoto(viewPath);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page.getByText(name).first()).toBeVisible();
    }

    async deleteRecord(viewPath: string, indexPath: string) {
        await this.safeGoto(viewPath);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.erpLocators.crmHeaderDeleteButton.click();
        await this.erpLocators.crmConfirmDeleteButton.click();

        await this.page.waitForURL(new RegExp(`${escapeRegExp(indexPath)}(\\?.*)?$`));
    }

    /**
     * Configuration pages (tags, lost reasons) create and edit their records in a modal.
     */
    async createInModal(path: string, newButton: RegExp, name: string) {
        await this.safeGoto(path);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.locator(".fi-header").getByRole("button", { name: newButton }).click();
        await this.fillWhenReady(this.erpLocators.crmModalNameInput, name);
        await this.erpLocators.crmModalCreateButton.click();
        await expect(this.erpLocators.crmModalNameInput).toBeHidden();

        await this.expectListed(path, name);
    }

    async editInModal(path: string, name: string, newName: string) {
        await this.searchList(path, name);
        await this.tableRow(name).first().getByRole("button", { name: /^Edit$/ }).click();
        await this.fillWhenReady(this.erpLocators.crmModalNameInput, newName);
        await this.erpLocators.crmModalSaveButton.click();
        await expect(this.erpLocators.crmModalNameInput).toBeHidden();

        await this.expectListed(path, newName);
    }

    async deleteInModal(path: string, name: string) {
        await this.searchList(path, name);
        await this.tableRow(name).first().getByRole("button", { name: /^Delete$/ }).click();
        await this.erpLocators.crmConfirmDeleteButton.click();

        await expect(this.tableRow(name)).toHaveCount(0);
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
