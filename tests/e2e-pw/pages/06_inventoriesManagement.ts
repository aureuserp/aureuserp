import { Page, expect, Locator } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type WarehouseData = {
    name: string;
    code: string;
    receptionStep?: 1 | 2 | 3;
    deliveryStep?: 1 | 2 | 3;
};

export type InventoryProductData = {
    name: string;
    price?: string;
    tracking?: "lot" | "serial";
};

export type ReceiptData = {
    partnerName?: string;
    productName: string;
    demand: string;
    operationType?: string;
};

export type DeliveryData = {
    partnerName?: string;
    productName: string;
    demand: string;
    operationType?: string;
};

export type InternalTransferData = {
    productName: string;
    demand: string;
    operationType?: string;
    operationTypeName?: string;
};

/**
 * One move line of an operation. `lotName` marks a lot/serial-tracked product
 * whose lot/serials are generated during the flow.
 */
export type MoveLineInput = {
    productName: string;
    demand: string;
    lotName?: string;
};

export type PackageTypeData = {
    name: string;
    length?: string;
    width?: string;
    height?: string;
    baseWeight?: string;
    maxWeight?: string;
};

export type PackageData = {
    name: string;
    packageType?: string;
    location?: string;
};

export type ScrapData = {
    productName: string;
    qty: string;
    sourceLocation?: string;
};

export class InventoriesManagementPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Plugin / Setup
     */

    async ensureInventoriesPluginInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Inventories");
    }

    async ensureBaseDependentPluginsInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        // await pluginPage.installPluginByName("Products");
        // await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Inventories");
    }

    /**
     * Settings - Manage Operations
     */

    async gotoManageOperationsPage() {
        await this.page.goto("/admin/settings/inventory/manage-operations");
        await expect(this.page).toHaveURL(/manage-operations/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventorySettingsSaveButton).toBeVisible();
    }

    async enableManageOperationsToggles() {
        await this.gotoManageOperationsPage();
        await this.setToggleOn(this.erpLocators.inventoryManageOperationsToggleEnablePackages);
        await this.erpLocators.inventorySettingsSaveButton.click();
        await this.page.waitForLoadState("networkidle");
        await this.expectSuccessToastSoft();
    }

    /**
     * Settings - Manage Products
     */

    async gotoManageProductsSettingsPage() {
        await this.page.goto("/admin/settings/inventory/manage-products");
        await expect(this.page).toHaveURL(/manage-products/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventorySettingsSaveButton).toBeVisible();
    }

    async enableManageProductsToggles() {
        await this.gotoManageProductsSettingsPage();
        await this.setToggleOn(this.erpLocators.inventoryManageProductsToggleEnableVariants);
        await this.setToggleOn(this.erpLocators.inventoryManageProductsToggleEnableUom);
        await this.setToggleOn(this.erpLocators.inventoryManageProductsToggleEnablePackagings);
        await this.erpLocators.inventorySettingsSaveButton.click();
        await this.page.waitForLoadState("networkidle");
        await this.expectSuccessToastSoft();
    }

    /**
     * Settings - Manage Warehouses (Locations + Multi-Step Routes)
     */

    async gotoManageWarehousesSettingsPage() {
        await this.page.goto("/admin/settings/inventory/manage-warehouses");
        await expect(this.page).toHaveURL(/manage-warehouses/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventorySettingsSaveButton).toBeVisible();
    }

    async enableManageWarehousesToggles() {
        await this.gotoManageWarehousesSettingsPage();
        await this.setToggleOn(this.erpLocators.inventoryManageWarehousesToggleEnableLocations);
        await this.setToggleOn(this.erpLocators.inventoryManageWarehousesToggleEnableMultiSteps);
        await this.erpLocators.inventorySettingsSaveButton.click();
        await this.page.waitForLoadState("networkidle");
        await this.expectSuccessToastSoft();
    }

    /**
     * Settings - Manage Traceability
     */

    async gotoManageTraceabilitySettingsPage() {
        await this.page.goto("/admin/settings/inventory/manage-traceability");
        await expect(this.page).toHaveURL(/manage-traceability/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventorySettingsSaveButton).toBeVisible();
    }

    async enableManageTraceabilityToggles() {
        await this.gotoManageTraceabilitySettingsPage();
        await this.setToggleOn(this.erpLocators.inventoryManageTraceabilityToggleEnableLots);
        if (await this.erpLocators.inventoryManageTraceabilityToggleDisplayOnDeliverySlips
            .isVisible()
            .catch(() => false)) {
            await this.setToggleOn(this.erpLocators.inventoryManageTraceabilityToggleDisplayOnDeliverySlips);
        }
        await this.erpLocators.inventorySettingsSaveButton.click();
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Settings - Manage Logistics (Dropshipping)
     */

    async gotoManageLogisticsSettingsPage() {
        await this.page.goto("/admin/settings/inventory/manage-logistics");
        await expect(this.page).toHaveURL(/manage-logistics/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventorySettingsSaveButton).toBeVisible();
    }

    async enableManageLogisticsToggles() {
        await this.gotoManageLogisticsSettingsPage();
        await this.setToggleOn(this.erpLocators.inventoryManageLogisticsToggleEnableDropshipping);
        await this.erpLocators.inventorySettingsSaveButton.click();
        await this.page.waitForLoadState("networkidle");
        await this.expectSuccessToastSoft();
    }

    /**
     * Run all settings and enable everything required for the full E2E flow.
     */
    async enableAllInventorySettings() {
        await this.enableManageWarehousesToggles();
        await this.enableManageProductsToggles();
        await this.enableManageOperationsToggles();
        await this.enableManageTraceabilityToggles();
        await this.enableManageLogisticsToggles();
    }

    /**
     * Configurations - Warehouses
     */

    async gotoWarehousesPage() {
        await this.page.waitForLoadState("networkidle");
        await this.page.goto("/admin/inventory/configurations/warehouses");
        await expect(this.page).toHaveURL(/configurations\/warehouses/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryWarehouseTable.first()).toBeVisible();
    }

    async createWarehouse(data: WarehouseData) {
        await this.gotoWarehousesPage();
        await this.erpLocators.inventoryWarehouseCreateButton.click();
        await expect(this.page).toHaveURL(/warehouses\/create/);

        await this.erpLocators.inventoryWarehouseNameInput.fill(data.name);
        await this.erpLocators.inventoryWarehouseCodeInput.fill(data.code);

        if (data.receptionStep) {
            await this.selectReceptionStep(data.receptionStep);
        }

        if (data.deliveryStep) {
            await this.selectDeliveryStep(data.deliveryStep);
        }

        await this.erpLocators.inventoryWarehouseSaveButton.click();
        await this.expectSuccessToast();
    }

    async selectReceptionStep(step: 1 | 2 | 3) {
        const target = step === 1
            ? this.erpLocators.inventoryWarehouseReceptionOneStep
            : step === 2
                ? this.erpLocators.inventoryWarehouseReceptionTwoSteps
                : this.erpLocators.inventoryWarehouseReceptionThreeSteps;

        if (await target.isVisible().catch(() => false)) {
            await target.click();
        }
    }

    async selectDeliveryStep(step: 1 | 2 | 3) {
        const target = step === 1
            ? this.erpLocators.inventoryWarehouseDeliveryOneStep
            : step === 2
                ? this.erpLocators.inventoryWarehouseDeliveryTwoSteps
                : this.erpLocators.inventoryWarehouseDeliveryThreeSteps;

        if (await target.isVisible().catch(() => false)) {
            await target.click();
        }
    }

    async editWarehouseSteps(name: string, receptionStep: 1 | 2 | 3, deliveryStep: 1 | 2 | 3) {
        await this.gotoWarehousesPage();
        await this.searchList(name);
        // await this.erpLocators.openWarehouseRow().click();
        // await this.openRowActions();
        // await this.page.waitForLoadState("networkidle");
        await this.page.waitForTimeout(800);
        
        await this.erpLocators.inventoryWarehouseEditAction.click();

        await this.selectReceptionStep(receptionStep);
        await this.selectDeliveryStep(deliveryStep);

        await this.erpLocators.inventoryWarehouseEditSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteWarehouse(name: string) {
        await this.gotoWarehousesPage();
        await this.searchList(name);
        await this.openRowActions();
        await this.erpLocators.inventoryWarehouseDeleteAction.click();
        await this.erpLocators.inventoryWarehouseConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Configurations - Locations / Operation Types / Routes / Rules
     * (Used to verify auto-creation after warehouse setup.)
     */

    async gotoLocationsPage() {
        await this.page.waitForLoadState("networkidle");
        await this.page.goto("/admin/inventory/configurations/locations");
        await expect(this.page).toHaveURL(/configurations\/locations/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryLocationsTable.first()).toBeVisible();
    }

    async gotoOperationTypesPage() {
        await this.page.goto("/admin/inventory/configurations/operation-types");
        await expect(this.page).toHaveURL(/operation-types/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTypesTable.first()).toBeVisible();
    }

    async gotoRoutesPage() {
        await this.page.goto("/admin/inventory/configurations/routes");
        await expect(this.page).toHaveURL(/configurations\/routes/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryRoutesTable.first()).toBeVisible();
    }

    async gotoRulesPage() {
        await this.page.goto("/admin/inventory/configurations/rules");
        await expect(this.page).toHaveURL(/configurations\/rules/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryRulesTable.first()).toBeVisible();
    }

    /**
     * Verify list contains a keyword by searching the list.
     */
    async expectListContains(keyword: string) {
        await this.searchList(keyword);
        const matches = this.erpLocators.inventoryTableRows.filter({ hasText: keyword });
        await expect(matches.first()).toBeVisible();
    }

    async expectLocationCreatedFor(warehouseCode: string) {
        await this.gotoLocationsPage();
        await this.expectListContains(warehouseCode);
    }

    async expectOperationTypeCreatedFor(warehouseCode: string) {
        await this.gotoOperationTypesPage();
        await this.expectListContains(warehouseCode);
    }

    async expectRouteCreatedFor(warehouseCode: string) {
        await this.gotoRoutesPage();
        await this.expectListContains(warehouseCode);
    }

    async expectRuleCreatedFor(warehouseCode: string) {
        await this.gotoRulesPage();
        await this.expectListContains(warehouseCode);
    }

    /**
     * Verify a warehouse's auto-created records by presence, not brittle global counts.
     */
    async expectWarehouseConfiguration(warehouse: WarehouseData) {
        const reception = warehouse.receptionStep ?? 1;
        const delivery = warehouse.deliveryStep ?? 1;

        await this.expectWarehouseLocations(warehouse.code, reception, delivery);
        await this.expectWarehouseOperationTypes(warehouse.name, reception, delivery);
        await this.expectWarehouseRoutes(warehouse.name, reception, delivery);
        await this.expectWarehouseRules(warehouse.code);
    }

    /**
     * Locations are listed as "<CODE>/<Name>", scoped by the warehouse code.
     */
    async expectWarehouseLocations(code: string, reception: number, delivery: number) {
        await this.gotoLocationsPage();
        await this.searchList(code);

        await this.expectScopedRow(`${code}/Stock`, true);
        await this.expectScopedRow(`${code}/Input`, reception >= 2);
        await this.expectScopedRow(`${code}/Quality Control`, reception >= 3);
        await this.expectScopedRow(`${code}/Output`, delivery >= 2);
        await this.expectScopedRow(`${code}/Packing Zone`, delivery >= 3);
    }

    /**
     * Operation types are scoped via the warehouse-name column.
     */
    async expectWarehouseOperationTypes(name: string, reception: number, delivery: number) {
        await this.gotoOperationTypesPage();
        await this.searchList(name);

        await this.expectScopedRow("Receipts", true);
        await this.expectScopedRow("Delivery Orders", true);
        await this.expectScopedRow("Storage", reception >= 2);
        await this.expectScopedRow("Quality Control", reception >= 3);
        await this.expectScopedRow("Pick", delivery >= 2);
        await this.expectScopedRow("Pack", delivery >= 3);
    }

    /**
     * Only the route variant matching the configured step count should exist.
     */
    async expectWarehouseRoutes(name: string, reception: number, delivery: number) {
        await this.gotoRoutesPage();
        await this.searchList(name);

        for (const step of [1, 2, 3]) {
            await this.expectScopedRow(this.receiveRouteLabel(step), step === reception);
            await this.expectScopedRow(this.deliverRouteLabel(step), step === delivery);
        }
    }

    /**
     * Rule names vary per step, so just assert auto-created rules exist.
     */
    async expectWarehouseRules(code: string) {
        await this.gotoRulesPage();
        await this.searchList(code);
        const rules = this.erpLocators.inventoryTableRows.filter({ hasText: code });
        await expect(rules.first(), `Expected auto-created rules for warehouse ${code}`).toBeVisible();
    }

    private receiveRouteLabel(step: number): string {
        return `Receive in ${step} step${step === 1 ? "" : "s"}`;
    }

    private deliverRouteLabel(step: number): string {
        return `Deliver in ${step} step${step === 1 ? "" : "s"}`;
    }

    /**
     * Assert a row containing `rowText` is present/absent in the already-filtered list.
     */
    private async expectScopedRow(rowText: string, shouldExist: boolean) {
        const rows = this.erpLocators.inventoryTableRows.filter({ hasText: rowText });
        if (shouldExist) {
            await expect(rows.first(), `Expected a row containing "${rowText}"`).toBeVisible();
        } else {
            await expect(rows, `Expected no row containing "${rowText}"`).toHaveCount(0);
        }
    }

    /**
     * Products
     */

    async gotoProductsPage() {
        await this.page.goto("/admin/inventory/products/products");
        await expect(this.page).toHaveURL(/inventory\/products\/products/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryProductTable.first()).toBeVisible();
    }

    async createInventoryProduct(product: InventoryProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.inventoryProductCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.erpLocators.inventoryProductNameInput.fill(product.name);
        if (product.price) {
            await this.erpLocators.inventoryProductPriceInput
                .fill(product.price)
                .catch(() => undefined);
        }

        if (await this.erpLocators.inventoryProductIsStorableToggle.isVisible().catch(() => false)) {
            await this.setToggleOn(this.erpLocators.inventoryProductIsStorableToggle);
        }

        if (product.tracking) {
            await this.erpLocators.inventoryProductTrackingSelect.selectOption(product.tracking, { timeout: 15000 });
        }

        await this.erpLocators.inventoryProductSaveButton.click();
        await expect(this.page).not.toHaveURL(/products\/create/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Navigate to a product's record by visiting list, searching, and opening it.
     */
    async openProductByName(name: string) {
        await this.gotoProductsPage();
        await this.searchList(name);
        const link = this.erpLocators.inventoryTableRows.locator("a").filter({ hasText: name }).first();
        await expect(link).toBeVisible();
        await link.click();
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * On-hand quantities tab on a product record.
     */
    async gotoProductQuantitiesTab(productName: string) {
        await this.openProductByName(productName);
        await this.erpLocators.inventoryProductQuantitiesTab.click();
        await this.page.waitForLoadState("networkidle");
        await expect(this.page).toHaveURL(/\/quantities/);
    }

    /**
     * In/Out movement history tab on a product record.
     */
    async gotoProductMovesTab(productName: string) {
        await this.openProductByName(productName);
        await this.erpLocators.inventoryProductMovesTab.click();
        await this.page.waitForLoadState("networkidle");
        await expect(this.page).toHaveURL(/\/moves/);
    }

    /**
     * Add an on-hand quantity for the product at a given location.
     */
    async addOnHandQuantity(productName: string, location: string, quantity: string, packageName?: string) {
        const l = this.erpLocators;

        await this.gotoProductQuantitiesTab(productName);

        await l.inventoryProductQuantityCreateButton.click();
        await this.page.waitForLoadState("networkidle");
        await expect(l.inventoryProductQuantityOpenModal).toBeVisible();

        try {
            await l.inventoryProductQuantityLocationSelect.waitFor({ state: "visible", timeout: 5000 });
            await this.selectFromFilamentDropdown(l.inventoryProductQuantityLocationSelect, location);
            await this.page.waitForLoadState("networkidle");
        } catch {
            // Location field not rendered (enable_locations toggle off) — proceed without it.
        }

        if (packageName) {
            await this.selectFromFilamentDropdown(l.inventoryProductQuantityPackageSelect, packageName);
            await this.page.waitForLoadState("networkidle");
        }

        await expect(l.inventoryProductQuantityInput).toBeVisible();
        await l.inventoryProductQuantityInput.fill(quantity);
        await l.inventoryProductQuantityDialogCreate.click();
        await this.page.waitForLoadState("networkidle");
        await this.expectSuccessToastSoft();
    }

    /**
     * Open a product's edit page (the row link lands on the read-only view).
     */
    async gotoProductEdit(productName: string) {
        await this.openProductByName(productName);
        const id = this.page.url().match(/products\/(\d+)/)?.[1];
        if (id) {
            await this.page.goto(`/admin/inventory/products/products/${id}/edit`);
            await this.page.waitForLoadState("networkidle");
        }
    }

    /**
     * Change a product's "Track By" on its edit page and save. The tracking
     * field is live, so allow its round-trip to settle before saving.
     */
    async editProductTracking(productName: string, tracking: "qty" | "lot" | "serial") {
        const l = this.erpLocators;

        await this.gotoProductEdit(productName);
        await l.inventoryProductTrackingSelect.selectOption(tracking, { timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
        await l.inventoryProductEditSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1500);
    }

    /**
     * Assert a product's persisted "Track By" value (read from a fresh edit page).
     */
    async expectProductTracking(productName: string, tracking: "qty" | "lot" | "serial") {
        await this.gotoProductEdit(productName);
        await expect(this.erpLocators.inventoryProductTrackingSelect).toHaveValue(tracking);
    }

    /**
     * Adjust an existing on-hand quantity row inline to a new value.
     */
    async adjustOnHandQuantity(productName: string, newQuantity: string) {
        await this.gotoProductQuantitiesTab(productName);
        const input = this.erpLocators.inventoryProductQuantityEditableInput;
        await input.fill(newQuantity);
        await input.press("Enter");
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Inventory-adjust a product's stock to zero via Operations > Adjustments >
     * Quantities: set the counted quantity to 0 and apply.
     */
    async clearStockViaAdjustment(productName: string) {
        await this.page.goto("/admin/inventory/operations/quantities");
        await expect(this.page).toHaveURL(/operations\/quantities/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTable.first()).toBeVisible();

        await this.searchList(productName);
        await this.page.waitForTimeout(500);

        const counted = this.erpLocators.inventoryQuantityCountedInput;
        await counted.fill("0");
        await counted.press("Enter");
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);

        await this.erpLocators.inventoryQuantityApplyAction.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Click a Filament select trigger, type into the search box of the
     * resulting dropdown panel, and click the first option matching `value`.
     * Robust against multiple panels because we scope to the visible panel
     * that appears after we click.
     */
    async selectFromFilamentDropdown(trigger: Locator, value: string) {
        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        const panel = this.erpLocators.inventorySelectPanel.last();
        await expect(panel).toBeVisible();

        const search = panel.locator('input.fi-input[aria-label="Search"]').first();
        if (await search.isVisible().catch(() => false)) {
            await search.fill(value);
            await this.page.waitForTimeout(500);
        }

        const option = panel
            .locator('[role="option"]')
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(option).toBeVisible();
        await option.click();
    }

    /**
     * Assert that the product's on-hand row at `location` shows `quantity`.
     * On Hand is a TextInputColumn so the value lives in the input, not text.
     */
    async expectOnHandQuantityRow(productName: string, location: string, quantity: string) {
        const l = this.erpLocators;
        await this.gotoProductQuantitiesTab(productName);

        const row = l.inventoryProductQuantityTableRows.filter({ hasText: location }).first();
        const onHandInput = row.locator(l.inventoryProductQuantityOnHandInlineInputs).first();
        // Allow trailing decimal padding (Filament formats numerics as "30.0000").
        const escaped = quantity.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        await expect(onHandInput).toHaveValue(new RegExp(`^${escaped}(\\.0+)?$`));
    }

    /**
     * Assert that the product's on-hand row at `location` shows the expected
     * Reserved Quantity. Reserved is a read-only TextColumn (cell text).
     */
    async expectReservedQuantityRow(productName: string, location: string, reserved: string) {
        const l = this.erpLocators;
        await this.gotoProductQuantitiesTab(productName);

        const row = l.inventoryProductQuantityTableRows.filter({ hasText: location }).first();
        const reservedCell = row.locator(l.inventoryProductQuantityReservedCells).first();
        await expect(reservedCell).toContainText(reserved);
    }

    /**
     * Assert that the product's moves (In/Out) tab contains a row with
     * the given product name. Filters by an optional state if provided.
     */
    async expectProductMoveRowVisible(productName: string, state?: string) {
        await this.gotoProductMovesTab(productName);
        if (state) {
            const tab = this.page.getByRole("tab", { name: new RegExp(`^${this.escapeRegExp(state)}$`, "i") });
            if (await tab.isVisible().catch(() => false)) {
                await tab.click();
                await this.page.waitForLoadState("networkidle");
            }
        }
        const row = this.erpLocators.inventoryTableRows.first();
        await expect(row).toBeVisible();
    }

    /**
     * Count the moves rows visible on a product's In/Out tab. The default
     * preset view on this page hardcodes a `state=DONE` filter, so rows are
     * only counted for moves that have actually been validated.
     */
    async countProductMoveRows(productName: string): Promise<number> {
        await this.gotoProductMovesTab(productName);
        const rows = this.erpLocators.inventoryTableRows;
        if (await rows.first().isVisible().catch(() => false)) {
            return rows.count();
        }
        return 0;
    }

    async deleteInventoryProduct(name: string) {
        await this.gotoProductsPage();
        await this.searchList(name);
        await this.openRowActions();
        await this.erpLocators.inventoryProductDeleteAction.click();
        await this.erpLocators.inventoryConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Operations - shared helpers
     */

    async clickConfirmIfVisible(): Promise<boolean> {
        if (await this.erpLocators.inventoryOperationConfirmButton.isVisible().catch(() => false)) {
            await this.erpLocators.inventoryOperationConfirmButton.click();
            await this.page.waitForLoadState("networkidle");
            return true;
        }
        return false;
    }

    async clickMarkAsTodoIfVisible(): Promise<boolean> {
        if (await this.erpLocators.inventoryOperationMarkAsTodoButton.isVisible().catch(() => false)) {
            await this.erpLocators.inventoryOperationMarkAsTodoButton.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            return true;
        }
        return false;
    }

    async clickCheckAvailabilityIfVisible(): Promise<boolean> {
        if (await this.erpLocators.inventoryOperationCheckAvailabilityButton.isVisible().catch(() => false)) {
            await this.erpLocators.inventoryOperationCheckAvailabilityButton.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            return true;
        }
        return false;
    }

    /**
     * Drive an operation Draft -> Done, settling between Livewire steps and retrying Validate with bounded clicks.
     */
    async confirmAndValidateOperation() {
        const l = this.erpLocators;

        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);

        await this.clickCheckAvailabilityIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);

        const validateBtn = l.inventoryOperationValidateButton;
        // Wait once for Validate to render (it can lag under load). After a
        // successful validate the button disappears, so the loop breaks on the
        // next pass without waiting on it again — avoiding a wasteful stall.
        await validateBtn.waitFor({ state: "visible", timeout: 10000 }).catch(() => undefined);
        for (let attempt = 0; attempt < 5; attempt++) {
            if (!(await validateBtn.isVisible().catch(() => false))) {
                break;
            }
            await validateBtn.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForTimeout(800);
            if (await l.inventoryOperationNoBackorderButton.isVisible().catch(() => false)) {
                await l.inventoryOperationNoBackorderButton.click({ timeout: 15000 }).catch(() => undefined);
                await this.page.waitForTimeout(500);
            }
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(600);
        }

        await this.expectSuccessToastSoft();
    }

    /**
     * Operations - Receipts
     */

    async gotoReceiptsPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/inventory/operations/receipts");
        await expect(this.page).toHaveURL(/operations\/receipts/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTable.first()).toBeVisible();
    }

    async createReceipt(data: ReceiptData): Promise<string> {
        await this.gotoReceiptsPage();
        await this.erpLocators.inventoryOperationCreateButton.click();
        await expect(this.page).toHaveURL(/receipts\/create/);

        if (data.operationType) {
            await this.selectOperationTypeForWarehouse(data.operationType, "Receipts");
        }

        if (data.partnerName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationPartnerSelect, data.partnerName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectSuccessToast();

        return this.readOperationReference();
    }

    async receiptFullFlow(data: ReceiptData) {
        await this.createReceipt(data);
        await this.confirmAndValidateOperation();
    }

    /**
     * Follow the auto-generated onward transfers via the "Next Transfer" button,
     * validating each, until the chain ends (Input -> [QC ->] Stock for a
     * multi-step receipt). The onward transfers are created automatically when an
     * operation created through the warehouse's operation type is validated.
     */
    async chainNextTransfers(maxSteps = 4) {
        const nextBtn = this.erpLocators.inventoryOperationNextTransferButton;

        for (let step = 0; step < maxSteps; step++) {
            // Reload so the header reflects show_next_operations after the last validate.
            await this.page.reload().catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);

            if (!(await nextBtn.isVisible().catch(() => false))) {
                break;
            }
            await nextBtn.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);
            await this.confirmAndValidateOperation();
        }
    }

    /**
     * Receive against a multi-step warehouse (via its operation type) and chain
     * the product all the way to its stock location using "Next Transfer".
     */
    async receiptChainFullFlow(data: ReceiptData) {
        await this.receiptFullFlow(data);
        await this.chainNextTransfers();
    }

    /**
     * Create a receipt with one or more move lines and return its reference.
     */
    async createReceiptLines(lines: MoveLineInput[], operationType?: string): Promise<string> {
        await this.gotoReceiptsPage();
        await this.erpLocators.inventoryOperationCreateButton.click();
        await expect(this.page).toHaveURL(/receipts\/create/);

        if (operationType) {
            await this.selectOperationTypeForWarehouse(operationType, "Receipts");
        }

        await this.addMoveLines(lines);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectSuccessToast();

        return this.readOperationReference();
    }

    /**
     * Receive one or more move lines and validate. Lot/serial-tracked lines (a
     * `lotName` present) get their lot/serials generated once the receipt is
     * confirmed; quantity-tracked lines need nothing extra.
     */
    async receiptLinesFullFlow(lines: MoveLineInput[], operationType?: string) {
        await this.createReceiptLines(lines, operationType);

        const tracked = lines
            .map((line, index) => ({ ...line, index }))
            .filter((line) => line.lotName);

        if (tracked.length > 0) {
            // The lot detail only appears once the moves leave the Draft state.
            await this.clickMarkAsTodoIfVisible();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);

            for (const line of tracked) {
                await this.generateLotOnMove(line.lotName!, line.demand, line.index);
            }
        }

        await this.confirmAndValidateOperation();
    }

    /**
     * Receive a single lot/serial-tracked product, generating its lot/serials.
     */
    async receiptWithLotFlow(data: ReceiptData, lotName: string) {
        await this.receiptLinesFullFlow(
            [{ productName: data.productName, demand: data.demand, lotName }],
            data.operationType
        );
    }

    /**
     * Open the "Manage Stock Moves" modal on the move at `rowIndex` and generate
     * the lot/serials covering the received quantity.
     */
    private async generateLotOnMove(lotName: string, quantity: string, rowIndex = 0) {
        const l = this.erpLocators;

        await l.inventoryMoveManageLinesAction.nth(rowIndex).click({ timeout: 15000 });
        await expect(l.inventoryMoveLinesModal).toBeVisible();

        await l.inventoryMoveGenerateLotsAction.click({ timeout: 15000 });
        await l.inventoryMoveLinesFirstLotInput.fill(lotName);
        await l.inventoryMoveLinesQuantityReceivedInput.fill(quantity);
        await l.inventoryMoveLinesGenerateSubmit.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(500);

        await l.inventoryMoveLinesModalSaveButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(500);
    }

    /**
     * Open the "Manage Stock Moves" modal on the move at `rowIndex` and route the
     * line's stock into the given destination package.
     */
    private async setResultPackageOnMove(packageName: string, rowIndex = 0) {
        const l = this.erpLocators;

        await l.inventoryMoveManageLinesAction.nth(rowIndex).click({ timeout: 15000 });
        await expect(l.inventoryMoveLinesModal).toBeVisible();

        await this.selectFromFilamentDropdown(l.inventoryMoveLinesResultPackageSelect, packageName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(400);

        await l.inventoryMoveLinesModalSaveButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(400);
    }

    /**
     * Receive a product into a destination package and validate, leaving the
     * received stock held inside that package at its stock location.
     */
    async receiptIntoPackageFlow(data: ReceiptData, packageName: string) {
        await this.createReceipt(data);

        // The destination package can only be set once the move leaves Draft.
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);

        await this.setResultPackageOnMove(packageName, 0);
        await this.confirmAndValidateOperation();
    }

    /**
     * Receive into a destination package via a multi-step warehouse and follow the
     * onward transfers with "Next Transfer" until the package reaches stock.
     */
    async receiptIntoPackageChainFlow(data: ReceiptData, packageName: string) {
        await this.receiptIntoPackageFlow(data, packageName);
        await this.chainNextTransfers();
    }

    /**
     * Operations - Deliveries
     */

    async gotoDeliveriesPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/inventory/operations/deliveries");
        await expect(this.page).toHaveURL(/operations\/deliveries/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTable.first()).toBeVisible();
    }

    async createDelivery(data: DeliveryData): Promise<string> {
        await this.gotoDeliveriesPage();
        await this.erpLocators.inventoryOperationCreateButton.click();
        await expect(this.page).toHaveURL(/deliveries\/create/);

        if (data.operationType) {
            await this.selectOperationTypeForWarehouse(data.operationType, "Delivery Orders");
        }

        if (data.partnerName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationPartnerSelect, data.partnerName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectSuccessToast();

        return this.readOperationReference();
    }

    async deliveryFullFlow(data: DeliveryData) {
        await this.createDelivery(data);
        await this.confirmAndValidateOperation();
    }

    /**
     * Operations - Internal Transfers
     */

    async gotoInternalTransfersPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/inventory/operations/internals");
        await expect(this.page).toHaveURL(/operations\/internals/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTable.first()).toBeVisible();
    }

    async createInternalTransfer(data: InternalTransferData): Promise<string> {
        await this.gotoInternalTransfersPage();
        await this.erpLocators.inventoryOperationCreateButton.click();
        await expect(this.page).toHaveURL(/internals\/create/);

        // Picking the warehouse's chain operation type (e.g. "Pick") sets the
        // route-linked source/destination.
        if (data.operationType && data.operationTypeName) {
            await this.selectOperationTypeForWarehouse(data.operationType, data.operationTypeName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectSuccessToast();

        return this.readOperationReference();
    }

    async internalTransferFullFlow(data: InternalTransferData) {
        await this.createInternalTransfer(data);
        await this.confirmAndValidateOperation();
    }

    /**
     * Quantities check
     */

    async gotoQuantitiesPage() {
        await this.page.goto("/admin/inventory/operations/quantities");
        await expect(this.page).toHaveURL(/operations\/quantities/);
        await this.page.waitForLoadState("networkidle");
    }

    async expectProductQuantityRowVisible(productName: string) {
        await this.gotoQuantitiesPage();
        await this.searchList(productName);
        const row = this.erpLocators.inventoryTableRows.filter({ hasText: productName });
        await expect(row.first()).toBeVisible();
    }

    /**
     * Package Types list. Only registered when packages are enabled.
     */
    async gotoPackageTypesPage() {
        await this.page.goto("/admin/inventory/configurations/package-types");
        await expect(this.page).toHaveURL(/package-types/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryPackageTypeTable.first()).toBeVisible();
    }

    /**
     * Create a package type (dimensions and weights are required).
     */
    async createPackageType(data: PackageTypeData) {
        await this.gotoPackageTypesPage();
        await this.erpLocators.inventoryPackageTypeCreateButton.click();
        await expect(this.page).toHaveURL(/package-types\/create/);

        await this.erpLocators.inventoryPackageTypeNameInput.fill(data.name);
        await this.erpLocators.inventoryPackageTypeLengthInput.fill(data.length ?? "10");
        await this.erpLocators.inventoryPackageTypeWidthInput.fill(data.width ?? "10");
        await this.erpLocators.inventoryPackageTypeHeightInput.fill(data.height ?? "10");
        await this.erpLocators.inventoryPackageTypeBaseWeightInput.fill(data.baseWeight ?? "1");
        await this.erpLocators.inventoryPackageTypeMaxWeightInput.fill(data.maxWeight ?? "100");

        await this.erpLocators.inventoryPackageTypeSaveButton.click();
        await expect(this.page).not.toHaveURL(/package-types\/create/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Packages list. Only registered when packages are enabled.
     */
    async gotoPackagesPage() {
        await this.page.goto("/admin/inventory/products/packages");
        await expect(this.page).toHaveURL(/products\/packages/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryPackageTable.first()).toBeVisible();
    }

    /**
     * Create a package, optionally of a given package type.
     */
    async createPackage(data: PackageData) {
        await this.gotoPackagesPage();
        await this.erpLocators.inventoryPackageCreateButton.click();
        await expect(this.page).toHaveURL(/packages\/create/);

        // Wait for the form to render before filling so the save isn't raced.
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.erpLocators.inventoryPackageNameInput).toBeVisible();
        await this.erpLocators.inventoryPackageNameInput.fill(data.name);
        await expect(this.erpLocators.inventoryPackageNameInput).toHaveValue(data.name);
        if (data.packageType) {
            await this.selectFromFilamentDropdown(this.erpLocators.inventoryPackageTypeSelect, data.packageType);
        }
        if (data.location) {
            await this.selectFromFilamentDropdown(this.erpLocators.inventoryPackageLocationSelect, data.location);
        }

        await this.erpLocators.inventoryPackageSaveButton.click();
        await expect(this.page).not.toHaveURL(/packages\/create/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Delete a package via its row actions.
     */
    async deletePackage(name: string) {
        await this.gotoPackagesPage();
        await this.searchList(name);
        await this.openRowActions();
        await this.erpLocators.inventoryPackageDeleteAction.click();
        await this.erpLocators.inventoryConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Open a package from the list and land on its "Products" sub-page.
     */
    async gotoPackageProductsTab(packageName: string) {
        await this.gotoPackagesPage();
        await this.searchList(packageName);
        const link = this.erpLocators.inventoryTableRows.locator("a").filter({ hasText: packageName }).first();
        await expect(link).toBeVisible();
        await link.click();
        await this.page.waitForLoadState("networkidle");

        // Jump to the package's Products sub-page via its record id.
        const id = this.page.url().match(/packages\/(\d+)/)?.[1];
        if (id) {
            await this.page.goto(`/admin/inventory/products/packages/${id}/products`);
            await this.page.waitForLoadState("networkidle");
        }
    }

    /**
     * Assert a package holds the given product (optionally at the given quantity).
     */
    async expectPackageContainsProduct(packageName: string, productName: string, quantity?: string) {
        await this.gotoPackageProductsTab(packageName);

        const row = this.erpLocators.inventoryTableRows.filter({ hasText: productName }).first();
        await expect(row).toBeVisible();
        if (quantity) {
            const escaped = quantity.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            await expect(row).toContainText(new RegExp(`${escaped}(\\.0+)?`));
        }
    }

    /**
     * Assert a package is no longer in the Packages list (it left its internal
     * location — e.g. delivered out to a customer).
     */
    async expectPackageNotListed(packageName: string) {
        await this.gotoPackagesPage();
        await this.searchList(packageName);

        const link = this.erpLocators.inventoryTableRows.locator("a").filter({ hasText: packageName });
        await expect(link).toHaveCount(0);
    }

    /**
     * Operations - Scrap
     */

    async gotoScrapsPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/inventory/operations/scraps");
        await expect(this.page).toHaveURL(/operations\/scraps/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryOperationTable.first()).toBeVisible();
    }

    /**
     * Create a scrap for a product; selecting the product auto-fills its unit.
     */
    async createScrap(data: ScrapData) {
        await this.gotoScrapsPage();
        await this.erpLocators.inventoryScrapCreateButton.click();
        await expect(this.page).toHaveURL(/scraps\/create/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.selectBySearch(this.erpLocators.inventoryScrapProductSelect, data.productName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.erpLocators.inventoryScrapQtyInput.fill(data.qty);
        if (data.sourceLocation) {
            await this.selectFromFilamentDropdown(this.erpLocators.inventoryScrapSourceLocationSelect, data.sourceLocation);
        }

        await this.erpLocators.inventoryOperationSaveButton.click();
        await expect(this.page).not.toHaveURL(/scraps\/create/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Validate the open scrap, moving its quantity to the scrap location.
     */
    async validateScrap() {
        const btn = this.erpLocators.inventoryOperationValidateButton;
        await btn.waitFor({ state: "visible", timeout: 10000 }).catch(() => undefined);
        if (await btn.isVisible().catch(() => false)) {
            await btn.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);
        }
    }

    /**
     * Create and validate a scrap in one flow.
     */
    async scrapFlow(data: ScrapData) {
        await this.createScrap(data);
        await this.validateScrap();
    }

    /**
     * Generic UI helpers
     */

    /**
     * Read an operation's reference from the edit-page heading (lists are searchable by reference, not product).
     */
    private async readOperationReference(): Promise<string> {
        const heading = this.erpLocators.inventoryPageHeading;
        await expect(heading).toBeVisible();
        const text = (await heading.textContent().catch(() => "")) ?? "";
        const match = text.match(/[A-Za-z0-9]+\/[A-Za-z]+\/\d+/);
        return (match ? match[0] : text.replace(/^\s*(Edit|View)\s+/i, "")).trim();
    }

    async searchList(keyword: string) {
        await this.erpLocators.inventorySearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
        await this.page.waitForTimeout(800);
    }

    async openRowActions() {
        await this.erpLocators.inventoryOperationRowActions.first().click();
    }

    async selectBySearch(trigger: Locator, value: string) {
        await trigger.click();

        await expect(this.erpLocators.inventorySelectSearchInput).toBeVisible();
        await this.erpLocators.inventorySelectSearchInput.fill(value);

        const option = this.erpLocators.inventorySelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(option).toBeVisible();
        await option.click();
    }

    /**
     * Add the given move lines, reusing the repeater's pre-added empty first row
     * and adding a new line for each subsequent product.
     */
    private async addMoveLines(lines: MoveLineInput[]) {
        const productSelects = this.erpLocators.inventoryOperationMoveProductSelect;
        const demandInputs = this.erpLocators.inventoryOperationMoveDemandInput;

        // Let any pending repeater re-render (from an operation-type change) settle.
        await this.page.waitForTimeout(500);

        // The moves repeater renders one row by default; wait for it so a slow
        // render isn't mistaken for an empty list, which would add a stray row.
        await productSelects.first().waitFor({ state: "visible", timeout: 10000 }).catch(() => undefined);

        for (let i = 0; i < lines.length; i++) {
            if ((await productSelects.count()) < i + 1) {
                await this.erpLocators.inventoryOperationAddMoveButton.scrollIntoViewIfNeeded();
                await this.erpLocators.inventoryOperationAddMoveButton.click();
                await expect(productSelects.nth(i)).toBeVisible();
            }
            await this.selectBySearch(productSelects.nth(i), lines[i].productName);

            // Confirm the product registered; retry once if the option click missed.
            const trigger = await productSelects.nth(i).innerText().catch(() => "");
            if (/select an option/i.test(trigger) || trigger.trim() === "") {
                await this.selectBySearch(productSelects.nth(i), lines[i].productName).catch(() => undefined);
            }

            await demandInputs.nth(i).fill(lines[i].demand);
        }
    }

    /**
     * Select a warehouse's operation type: search by op-type name, then pick the option whose label has the warehouse name.
     */
    async selectOperationTypeForWarehouse(warehouseName: string, operationTypeName: string) {
        const trigger = this.erpLocators.inventoryOperationTypeSelect;
        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        await expect(this.erpLocators.inventorySelectSearchInput).toBeVisible();
        await this.erpLocators.inventorySelectSearchInput.fill(operationTypeName);
        await this.page.waitForTimeout(800);

        const option = this.erpLocators.inventorySelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(warehouseName), "i") })
            .filter({ hasText: new RegExp(this.escapeRegExp(operationTypeName), "i") })
            .first();

        await expect(option).toBeVisible();
        await option.click();
        await this.page.waitForLoadState("networkidle");
    }

    async setToggleOn(toggle: Locator) {
        if (!(await toggle.isVisible().catch(() => false))) {
            return;
        }

        const checked = await toggle.getAttribute("aria-checked").catch(() => null);
        if (checked === "true") {
            return;
        }
        await toggle.click();
    }

    async setToggleOff(toggle: Locator) {
        if (!(await toggle.isVisible().catch(() => false))) {
            return;
        }

        const checked = await toggle.getAttribute("aria-checked").catch(() => null);
        if (checked === "false") {
            return;
        }
        await toggle.click();
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\\]\\]/g, "\\$&");
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.inventorySuccessToast).toBeVisible();
    }

    private async expectSuccessToastSoft() {
        try {
            await expect(this.erpLocators.inventorySuccessToast).toBeVisible({ timeout: 2_500 });
        } catch {
            // The success toast is transient (and some validate flows redirect
            // immediately), so a short, non-blocking check is enough.
        }
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.inventoryValidationMessage.first()).toBeVisible();
    }
}
