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
    operationTypeName?: string;
    origin?: string;
};

export type DeliveryData = {
    partnerName?: string;
    productName: string;
    demand: string;
    operationType?: string;
    operationTypeName?: string;
    origin?: string;
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

export type LocationTypeValue =
    | "supplier"
    | "view"
    | "internal"
    | "customer"
    | "inventory"
    | "production"
    | "transit";

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
     * Turn the dropshipping feature off.
     */
    async disableDropshipping() {
        await this.gotoManageLogisticsSettingsPage();
        await this.setToggleOff(this.erpLocators.inventoryManageLogisticsToggleEnableDropshipping);
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

        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.inventoryWarehouseSaveButton.click().catch(() => undefined);
            await this.page
                .waitForURL((url) => !/warehouses\/create/.test(url.toString()), { timeout: 20000 })
                .catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (!/warehouses\/create/.test(this.page.url())) {
                return;
            }
        }

        await expect(this.page).not.toHaveURL(/warehouses\/create/);
    }

    async selectReceptionStep(step: 1 | 2 | 3) {
        const target = step === 1
            ? this.erpLocators.inventoryWarehouseReceptionOneStep
            : step === 2
                ? this.erpLocators.inventoryWarehouseReceptionTwoSteps
                : this.erpLocators.inventoryWarehouseReceptionThreeSteps;

        if (await target.isVisible().catch(() => false)) {
            await target.click();
            await this.settleForm();
        }
    }

    /**
     * Let the Livewire round-trip a live field triggers land before the next interaction,
     * so the submit that follows is not clicked while the form is still disabled.
     */
    private async settleForm() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async selectDeliveryStep(step: 1 | 2 | 3) {
        const target = step === 1
            ? this.erpLocators.inventoryWarehouseDeliveryOneStep
            : step === 2
                ? this.erpLocators.inventoryWarehouseDeliveryTwoSteps
                : this.erpLocators.inventoryWarehouseDeliveryThreeSteps;

        if (await target.isVisible().catch(() => false)) {
            await target.click();
            await this.settleForm();
        }
    }

    async editWarehouseSteps(name: string, receptionStep: 1 | 2 | 3, deliveryStep: 1 | 2 | 3) {
        await this.gotoWarehousesPage();
        await this.searchList(name);
        await this.page.waitForTimeout(800);
        
        await this.erpLocators.inventoryWarehouseEditAction.click();

        // The list opens the edit form as an SPA navigation: a radio picked before the form
        // has hydrated is discarded along with the markup it was clicked in.
        await this.page.waitForURL(/warehouses\/\d+\/edit/, { timeout: 30000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        const editUrl = this.page.url();

        // The success toast cannot tell a real save from a click Filament swallowed while
        // the step radios were still recomputing, so the saved steps are read back instead.
        // A reload resets the form, hence the steps are re-picked on every attempt.
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.selectReceptionStep(receptionStep);
            await this.selectDeliveryStep(deliveryStep);

            await this.erpLocators.inventoryWarehouseEditSaveButton.click().catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(1000);

            await this.page.goto(editUrl);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (await this.areWarehouseStepsSelected(receptionStep, deliveryStep)) {
                return;
            }
        }

        throw new Error(`The warehouse steps were never saved (reception ${receptionStep}, delivery ${deliveryStep}).`);
    }

    private async areWarehouseStepsSelected(receptionStep: 1 | 2 | 3, deliveryStep: 1 | 2 | 3): Promise<boolean> {
        const reception = receptionStep === 1
            ? this.erpLocators.inventoryWarehouseReceptionOneStep
            : receptionStep === 2
                ? this.erpLocators.inventoryWarehouseReceptionTwoSteps
                : this.erpLocators.inventoryWarehouseReceptionThreeSteps;

        const delivery = deliveryStep === 1
            ? this.erpLocators.inventoryWarehouseDeliveryOneStep
            : deliveryStep === 2
                ? this.erpLocators.inventoryWarehouseDeliveryTwoSteps
                : this.erpLocators.inventoryWarehouseDeliveryThreeSteps;

        return (await reception.isChecked().catch(() => false))
            && (await delivery.isChecked().catch(() => false));
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

    async gotoStorageCategoriesPage() {
        await this.page.goto("/admin/inventory/configurations/storage-categories");
        await expect(this.page).toHaveURL(/storage-categories/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryStorageCategoriesTable.first()).toBeVisible();
    }

    /**
     * Create a storage category with just a name (its selects default to Mixed / current company).
     */
    async createStorageCategory(name: string) {
        await this.gotoStorageCategoriesPage();
        await this.erpLocators.inventoryStorageCategoryCreateButton.click();
        await expect(this.page).toHaveURL(/storage-categories\/create/);
        await this.fillConfigNameAndSave(name);
    }

    /**
     * Create a location with just a name (its type defaults to Internal).
     */
    async createLocation(name: string) {
        await this.gotoLocationsPage();
        await this.erpLocators.inventoryLocationCreateButton.click();
        await expect(this.page).toHaveURL(/locations\/create/);
        await this.fillConfigNameAndSave(name);
    }

    /**
     * Open the Location create form and fill its required name.
     */
    private async openLocationCreateForm(name: string) {
        await this.gotoLocationsPage();
        await this.erpLocators.inventoryLocationCreateButton.click();
        await expect(this.page).toHaveURL(/locations\/create/);
        await expect(this.erpLocators.inventoryConfigNameInput).toBeVisible();
        await this.erpLocators.inventoryConfigNameInput.fill(name);
    }

    /**
     * Save the open Location form and wait to land on the record's view page.
     */
    private async saveLocationForm() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(500);
        await this.erpLocators.inventoryConfigSaveButton.click();
        await this.page
            .waitForURL((url) => !/locations\/create/.test(url.toString()), { timeout: 20000 })
            .catch(() => undefined);
        await this.expectSuccessToast();
    }

    /**
     * Create a location of the given type (the type select is native and live).
     */
    async createLocationOfType(name: string, type: LocationTypeValue) {
        await this.openLocationCreateForm(name);
        await this.erpLocators.inventoryLocationTypeSelect.selectOption(type);
        await this.saveLocationForm();
    }

    /**
     * Create a location nested under an existing parent location.
     */
    async createLocationWithParent(name: string, parentName: string) {
        await this.openLocationCreateForm(name);
        await this.selectFromFilamentDropdown(this.erpLocators.inventoryLocationParentSelect, parentName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.saveLocationForm();
    }

    /**
     * Create a sub-location nested under a parent, optionally assigning a storage
     * category (used by putaway to know where to store arriving product).
     */
    async createSubLocation(name: string, parentName: string, storageCategory?: string) {
        await this.openLocationCreateForm(name);
        await this.selectFromFilamentDropdown(this.erpLocators.inventoryLocationParentSelect, parentName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        if (storageCategory) {
            await this.selectFromFilamentDropdown(this.erpLocators.inventoryLocationStorageCategorySelect, storageCategory);
            await this.page.waitForTimeout(400);
        }
        await this.saveLocationForm();
    }

    /**
     * Configurations - Putaway Rules (redirect arriving product to a sub-location).
     */
    async gotoPutawayRulesPage() {
        await this.page.goto("/admin/inventory/configurations/putaway-rules");
        await expect(this.page).toHaveURL(/putaway-rules/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Create a putaway rule: product arriving in `inLocation` (a parent stock
     * location) is redirected on validation to the `outLocation` sub-location.
     */
    async createPutawayRule(data: { inLocation: string; outLocation: string; productName: string; storageCategory?: string }) {
        const l = this.erpLocators;
        await this.gotoPutawayRulesPage();
        await l.inventoryPutawayRuleCreateButton.click();

        const modal = this.page.locator(".fi-modal-window:visible").last();
        await expect(modal).toBeVisible();

        await this.selectFromFilamentDropdown(
            modal.locator('[wire\\:key$="in_location_id"] button.fi-select-input-btn').first(),
            data.inLocation,
        );
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(700);
        await this.selectFromFilamentDropdown(
            modal.locator('[wire\\:key$="out_location_id"] button.fi-select-input-btn').first(),
            data.outLocation,
        );
        await this.page.waitForTimeout(500);
        await this.selectFromFilamentDropdown(
            modal.locator('[wire\\:key$="product_id"] button.fi-select-input-btn').first(),
            data.productName,
        );
        await this.page.waitForTimeout(500);
        if (data.storageCategory) {
            await this.selectFromFilamentDropdown(
                modal.locator('[wire\\:key$="storage_category_id"] button.fi-select-input-btn').first(),
                data.storageCategory,
            );
            await this.page.waitForTimeout(500);
        }

        await modal.getByRole("button", { name: /^Create$/i }).first().click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    /**
     * Create an internal scrap location (Is a Scrap Location? = on).
     */
    async createScrapLocation(name: string) {
        await this.openLocationCreateForm(name);
        await this.setToggleOn(this.erpLocators.inventoryLocationIsScrapToggle);
        await this.saveLocationForm();
    }

    /**
     * Create a route with just a name.
     */
    async createRoute(name: string) {
        await this.gotoRoutesPage();
        await this.erpLocators.inventoryRouteCreateButton.click();
        await expect(this.page).toHaveURL(/routes\/create/);
        await this.fillConfigNameAndSave(name);
    }

    /**
     * Create an operation type with a name and sequence prefix (type defaults to
     * Incoming, so its source/destination locations and backorder policy default).
     */
    async createOperationType(name: string, sequenceCode: string) {
        await this.gotoOperationTypesPage();
        await this.erpLocators.inventoryOperationTypeCreateButton.click();
        await expect(this.page).toHaveURL(/operation-types\/create/);
        await expect(this.erpLocators.inventoryConfigNameInput).toBeVisible();
        await this.erpLocators.inventoryConfigNameInput.fill(name);
        await this.erpLocators.inventoryConfigSequenceCodeInput.fill(sequenceCode);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);
        await this.erpLocators.inventoryConfigSaveButton.click();
        await this.page
            .waitForURL((url) => !/operation-types\/create/.test(url.toString()), { timeout: 20000 })
            .catch(() => undefined);
        await this.expectSuccessToast();
    }

    async deleteOperationType(name: string) {
        await this.gotoOperationTypesPage();
        await this.deleteRecordInList(name);
    }

    /**
     * Open the operation-type create form by direct navigation (a reliable
     * landing that doesn't depend on the list's "New" button).
     */
    async gotoOperationTypeCreatePage() {
        await this.page.goto("/admin/inventory/configurations/operation-types/create");
        await expect(this.page).toHaveURL(/operation-types\/create/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.inventoryConfigNameInput).toBeVisible();
    }

    /**
     * Assert whether the type select offers the given option as selectable. The
     * form gates options with Filament's disableOptionWhen, which keeps the
     * option in the DOM and marks it disabled, so assert on that rather than on
     * the option's presence.
     */
    async expectOperationTypeOptionDisabled(value: string, disabled = true) {
        await expect(this.erpLocators.inventoryOperationTypeTypeSelect).toBeVisible();
        const option = this.erpLocators.inventoryOperationTypeTypeSelect.locator(`option[value="${value}"]`);
        await expect(option).toHaveCount(1);
        await (disabled ? expect(option).toBeDisabled() : expect(option).toBeEnabled());
    }

    /**
     * Create an operation type driving the type-dependent fields: `type` and
     * `warehouse` are live and recompute the source/destination locations (allow
     * that to settle before saving), `returnType` picks another operation type of
     * the same warehouse, `createBackorder` is a native select, and `reservation`
     * (Manual) is a radio set by clicking its visible label so Livewire records it.
     */
    async createOperationTypeWithFlow(data: {
        name: string;
        sequenceCode: string;
        type?: "incoming" | "outgoing" | "internal" | "dropship" | "manufacture";
        warehouseName?: string;
        sourceLocation?: string;
        destinationLocation?: string;
        returnTypeName?: string;
        createBackorder?: "ask" | "always" | "never";
        reservation?: "manual";
        lots?: "create" | "existing" | "both";
    }) {
        const l = this.erpLocators;
        await this.gotoOperationTypesPage();
        await l.inventoryOperationTypeCreateButton.click();
        await expect(this.page).toHaveURL(/operation-types\/create/);
        await expect(l.inventoryConfigNameInput).toBeVisible();

        await l.inventoryConfigNameInput.fill(data.name);
        await l.inventoryConfigSequenceCodeInput.fill(data.sequenceCode);

        if (data.type) {
            await l.inventoryOperationTypeTypeSelect.selectOption(data.type);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(1500);
        }
        if (data.warehouseName) {
            await this.selectFromFilamentDropdown(l.inventoryOperationTypeWarehouseSelect, data.warehouseName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(1200);
        }
        if (data.sourceLocation) {
            await this.selectLocationOverride(l.inventoryOperationSourceLocationSelect, data.sourceLocation);
        }
        if (data.destinationLocation) {
            await this.selectLocationOverride(l.inventoryOperationDestinationLocationSelect, data.destinationLocation);
        }
        if (data.returnTypeName) {
            await this.selectReturnOperationType(data.returnTypeName, data.warehouseName);
            await this.page.waitForTimeout(400);
        }
        if (data.createBackorder) {
            await l.inventoryOperationTypeBackorderSelect.selectOption(data.createBackorder);
            await this.page.waitForTimeout(400);
        }
        if (data.reservation === "manual") {
            await l.inventoryOperationTypeReservationGroup.getByText("Manual", { exact: true }).click();
            await this.page.waitForTimeout(400);
        }
        if (data.lots === "create" || data.lots === "both") {
            await this.setToggleOn(l.inventoryOperationTypeUseCreateLotsToggle);
            await this.page.waitForTimeout(300);
        }
        if (data.lots === "existing" || data.lots === "both") {
            await this.setToggleOn(l.inventoryOperationTypeUseExistingLotsToggle);
            await this.page.waitForTimeout(300);
        }

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(600);
        await l.inventoryConfigSaveButton.click();
        await this.page
            .waitForURL((url) => !/operation-types\/create/.test(url.toString()), { timeout: 20000 })
            .catch(() => undefined);
        // The redirect off /create already confirms the save; the toast can vanish
        // before it's asserted, so don't hard-fail on it.
        await this.expectSuccessToastSoft();
    }

    /**
     * Select a source/destination location on the (live) operation-type form,
     * verifying the choice stuck — the type's live recompute can otherwise reset
     * it, so re-select until the trigger shows the picked location.
     */
    private async selectLocationOverride(trigger: Locator, name: string) {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.selectFromFilamentDropdown(trigger, name);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(600);
            const text = (await trigger.innerText().catch(() => "")) ?? "";
            if (text.includes(name)) {
                return;
            }
        }
    }

    /**
     * Select the Return Type by op-type name (optionally disambiguated by the
     * warehouse shown in the "<warehouse>: <op-type>" option label).
     */
    private async selectReturnOperationType(opTypeName: string, warehouseName?: string) {
        const trigger = this.erpLocators.inventoryOperationTypeReturnSelect;
        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        const panel = this.erpLocators.inventorySelectPanel.last();
        await expect(panel).toBeVisible();

        const search = panel.locator('input.fi-input[aria-label="Search"]').first();
        if (await search.isVisible().catch(() => false)) {
            await search.fill(opTypeName);
            await this.page.waitForTimeout(600);
        }

        let option = panel
            .locator('[role="option"]')
            .filter({ hasText: new RegExp(this.escapeRegExp(opTypeName), "i") });
        if (warehouseName) {
            option = option.filter({ hasText: new RegExp(this.escapeRegExp(warehouseName), "i") });
        }

        await expect(option.first()).toBeVisible();
        await option.first().click();
    }

    /**
     * Assert an operation type's saved value on its view page, matching the
     * infolist entry whose label equals `label` exactly (so "Operation Type"
     * doesn't collide with "Return Operation Type").
     */
    async expectOperationTypeField(label: string, value: string) {
        const entry = this.erpLocators.inventoryInfolistEntries
            .filter({ has: this.page.getByText(label, { exact: true }) })
            .first();
        await expect(entry).toBeVisible({ timeout: 15000 });
        await expect(entry).toContainText(value);
    }

    /**
     * Assert an infolist entry (matched by its exact label) shows the given
     * value on a record's view page.
     */
    async expectInfolistField(label: string, value: string, timeout = 15000) {
        const entry = this.erpLocators.inventoryInfolistEntries
            .filter({ has: this.page.getByText(label, { exact: true }) })
            .first();
        await expect(entry).toBeVisible({ timeout: 15000 });
        await expect(entry).toContainText(value, { timeout });
    }

    /**
     * From the just-created operation type's view page, open its edit form and
     * assert the Lots/Serial toggles: `which` is on, the other is off.
     */
    async expectOperationTypeLots(which: "create" | "existing") {
        const l = this.erpLocators;
        const editUrl = `${this.page.url().replace(/\/$/, "")}/edit`;
        await this.page.goto(editUrl);
        await expect(l.inventoryConfigNameInput).toBeVisible({ timeout: 15000 });
        await expect(l.inventoryOperationTypeUseCreateLotsToggle).toHaveAttribute(
            "aria-checked",
            which === "create" ? "true" : "false",
            { timeout: 10000 },
        );
        await expect(l.inventoryOperationTypeUseExistingLotsToggle).toHaveAttribute(
            "aria-checked",
            which === "existing" ? "true" : "false",
            { timeout: 10000 },
        );
    }

    async deleteStorageCategory(name: string) {
        await this.gotoStorageCategoriesPage();
        await this.deleteRecordInList(name);
    }

    async deleteLocation(name: string) {
        await this.gotoLocationsPage();
        await this.deleteRecordInList(name);
    }

    /**
     * Attempt to delete a location that still holds stock and assert the app
     * blocks it with a "still contain products" validation (record remains).
     */
    async deleteLocationExpectingBlocked(name: string) {
        await this.gotoLocationsPage();
        await this.searchList(name);
        const row = this.erpLocators.inventoryTableRows.filter({ hasText: name }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: /^Delete$/i }).first().click();
        await this.erpLocators.inventoryConfirmDialogButton.click();
        await expect(this.page.getByText(/still contain products/i)).toBeVisible({ timeout: 15000 });
    }

    async deleteRoute(name: string) {
        await this.gotoRoutesPage();
        await this.deleteRecordInList(name);
    }

    /**
     * Fill a config resource's name field and save, expecting a success toast.
     */
    private async fillConfigNameAndSave(name: string) {
        await expect(this.erpLocators.inventoryConfigNameInput).toBeVisible();
        await this.erpLocators.inventoryConfigNameInput.fill(name);
        await this.erpLocators.inventoryConfigSaveButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Delete a configuration record by name via its row's inline Delete action.
     * Assumes the caller is already on the relevant list page.
     */
    private async deleteRecordInList(name: string) {
        await this.searchList(name);
        const row = this.erpLocators.inventoryTableRows.filter({ hasText: name }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: /^Delete$/i }).first().click();
        await this.erpLocators.inventoryConfirmDialogButton.click();
        await this.expectSuccessToast();
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

    /**
     * Fill a field once its form has finished hydrating. Livewire swaps the markup after the
     * page settles, discarding a value typed into the pre-swap DOM — the product is then
     * submitted with an empty name, fails validation, and the form never leaves /create.
     */
    private async fillWhenReady(input: ReturnType<Page["locator"]>, value: string) {
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

    async createInventoryProduct(product: InventoryProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.inventoryProductCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.fillWhenReady(this.erpLocators.inventoryProductNameInput, product.name);
        if (product.price) {
            await this.fillWhenReady(this.erpLocators.inventoryProductPriceInput, product.price)
                .catch(() => undefined);
        }

        if (await this.erpLocators.inventoryProductIsStorableToggle.isVisible().catch(() => false)) {
            await this.setToggleOn(this.erpLocators.inventoryProductIsStorableToggle);
        }

        if (product.tracking) {
            await this.erpLocators.inventoryProductTrackingSelect.selectOption(product.tracking, { timeout: 15000 });

            // Track By recomputes the form; Filament disables the submit while that request
            // is in flight and a click landing then is swallowed, leaving the form open.
            await this.settleForm();
        }

        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.inventoryProductSaveButton.click().catch(() => undefined);
            await this.page
                .waitForURL((url) => !/products\/create/.test(url.toString()), { timeout: 60000 })
                .catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (!/products\/create/.test(this.page.url())) {
                return;
            }
        }

        await expect(this.page).not.toHaveURL(/products\/create/);
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

            if (await l.inventoryOperationReturnButton.isVisible().catch(() => false)) {
                break;
            }
        }

        await this.settleAfterValidation();
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

        if (data.operationTypeName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationTypeSelect, data.operationTypeName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);
        } else if (data.operationType) {
            await this.selectOperationTypeForWarehouse(data.operationType, "Receipts");
        }

        if (data.partnerName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationPartnerSelect, data.partnerName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        if (data.origin) {
            await this.erpLocators.inventoryOperationAdditionalTab.click();
            await this.erpLocators.inventoryOperationOriginInput.fill(data.origin);
        }

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectOperationCreated();

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
        await this.expectOperationCreated();

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
     * Receive a lot/serial-tracked product through a multi-step warehouse
     * (generating its lot/serials at the receipt) and chain every onward transfer
     * to the stock location, where a putaway rule can redirect it.
     */
    async receiptWithLotChainFlow(data: ReceiptData, lotName: string) {
        await this.receiptWithLotFlow(data, lotName);
        await this.chainNextTransfers();
    }

    /**
     * Receive several move lines (mixing quantity/lot/serial-tracked products,
     * lot/serials generated for tracked lines) through a multi-step warehouse and
     * chain every onward transfer to stock, where putaway rules redirect each.
     */
    async receiptLinesChainFlow(lines: MoveLineInput[], operationType?: string) {
        await this.receiptLinesFullFlow(lines, operationType);
        await this.chainNextTransfers();
    }

    /**
     * Lots / Serial Numbers listing (Products cluster).
     */
    async gotoLotsPage() {
        await this.page.goto("/admin/inventory/products/lots");
        await expect(this.page).toHaveURL(/products\/lots/);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Assert a lot/serial with the given name exists in the Lots listing.
     */
    async expectLotListed(name: string) {
        await this.gotoLotsPage();
        await this.searchList(name);
        await expect(
            this.erpLocators.inventoryTableRows.filter({ hasText: name }).first(),
        ).toBeVisible({ timeout: 10000 });
    }

    /**
     * Create a receipt for a serial-tracked product via the op-type, mark it
     * Todo, and open the "Manage Stock Moves" modal (left open so the caller can
     * assert which lot options the op-type's Create New / Use Existing toggles
     * expose on the move line).
     */
    async openSerialReceiptLinesModal(opTypeName: string, productName: string, demand = "1") {
        await this.createReceipt({ operationTypeName: opTypeName, productName, demand });
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
        await this.erpLocators.inventoryMoveManageLinesAction.first().click({ timeout: 15000 });
        await expect(this.erpLocators.inventoryMoveLinesModal).toBeVisible();
    }

    /**
     * Assert whether the "Generate Serials/Lots" action (the Create New path) is
     * offered on the open Manage Stock Moves modal.
     */
    async expectGenerateSerialAction(present: boolean) {
        const action = this.erpLocators.inventoryMoveGenerateLotsAction;
        if (present) {
            await expect(action).toBeVisible({ timeout: 10000 });
        } else {
            await expect(action).toBeHidden({ timeout: 10000 });
        }
    }

    /**
     * On the open modal, open the line's Lot/Serial dropdown and assert an option
     * matching `serial` is offered (the Use Existing path lists existing serials).
     */
    async expectExistingSerialOption(serial: string) {
        const modal = this.page.locator(".fi-modal-window:visible").last();
        const trigger = modal.locator("table tbody tr").first().locator("button.fi-select-input-btn").first();
        await trigger.click();

        const panel = this.erpLocators.inventorySelectPanel.last();
        await expect(panel).toBeVisible();
        const search = panel.locator('input.fi-input[aria-label="Search"]').first();
        if (await search.isVisible().catch(() => false)) {
            await search.fill(serial);
            await this.page.waitForTimeout(500);
        }
        await expect(
            panel.locator('[role="option"]').filter({ hasText: serial }).first(),
        ).toBeVisible({ timeout: 8000 });
        await this.page.keyboard.press("Escape").catch(() => undefined);
    }

    /**
     * Deliver a serial-tracked product through a custom outgoing operation type
     * and validate. The serial is drawn from existing stock (the op-type's lot
     * toggles do not apply to deliveries), so reservation assigns it on confirm.
     */
    async deliverSerialFullFlow(opTypeName: string, productName: string, demand = "1") {
        await this.createDelivery({ operationTypeName: opTypeName, productName, demand });
        await this.confirmAndValidateOperation();
    }

    /**
     * On the open modal, assert the Lot/Serial column is absent (neither lot
     * toggle on, so no serial can be assigned on the move line).
     */
    async expectSerialLotColumnAbsent() {
        const modal = this.page.locator(".fi-modal-window:visible").last();
        await expect(
            modal.locator("thead").getByText(/Lot\/Serial Number/i),
        ).toHaveCount(0, { timeout: 8000 });
    }

    /**
     * Assert no lot/serial is recorded for the given product in the Lots listing.
     */
    async expectNoLotForProduct(productName: string) {
        await this.gotoLotsPage();
        await this.searchList(productName);
        await expect(
            this.erpLocators.inventoryTableRows.filter({ hasText: productName }),
        ).toHaveCount(0, { timeout: 10000 });
    }

    /**
     * Receive a serial/lot-tracked product through a custom operation type,
     * generating a brand-new lot/serial via the "Generate Serials/Lots" action.
     * Requires the op-type's "Create New" lots toggle on and a vendor source.
     */
    async receiptSerialGenerateNew(opTypeName: string, productName: string, serial: string, demand = "1") {
        await this.createReceipt({ operationTypeName: opTypeName, productName, demand });
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);
        await this.generateLotOnMove(serial, demand, 0);
        await this.confirmAndValidateOperation();
    }

    /**
     * Open the "Manage Stock Moves" modal on the move at `rowIndex` and generate
     * the lot/serials covering the received quantity.
     */
    /**
     * Enter lot/serial numbers for a move of the currently-open incoming operation through
     * the Manage Stock Moves modal. Only receipts offer this: the "Generate Serials/Lots"
     * action is gated on the move's source being a supplier location.
     */
    async generateLotOnMove(lotName: string, quantity: string, rowIndex = 0) {
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
    /**
     * Route a move's stock into a destination package, picking the move by its product.
     * The moves repeater does not render its rows in sale-order line order, so a
     * multi-line operation must be addressed by product rather than by row index.
     */
    /**
     * The move row holding a product. The row must be matched on its product select's chosen
     * value: the row's text also contains every option the select can offer, so filtering a
     * row by text matches any row and silently reads another product's quantities.
     */
    private async moveRowForProduct(productName: string) {
        const selects = this.erpLocators.inventoryMoveProductSelectButton;

        // On an editable operation the product is a select, and the row's text carries every
        // option it offers — so the row has to be matched on the select's chosen value.
        if (await selects.count() > 0) {
            return this.page
                .getByRole("row")
                .filter({ has: selects.filter({ hasText: productName }) })
                .first();
        }

        // A read-only operation (a draft return, for one) renders the product as plain text,
        // and with no select there is nothing to pollute the row's text.
        return this.page.getByRole("row").filter({ hasText: productName }).first();
    }

    async setResultPackageForProduct(packageName: string, productName: string) {
        const row = await this.moveRowForProduct(productName);
        await expect(row).toBeVisible({ timeout: 15000 });

        const trigger = row.locator('button[wire\\:click*="manageLines"]').first();
        await trigger.click({ timeout: 15000 });

        await this.selectResultPackageInOpenMoveLinesModal(packageName);
    }

    /**
     * Route a move's stock into a destination package through the Manage Stock Moves
     * modal. On a delivery the line's "Pick From" is already resolved by the
     * reservation, so only the package has to be chosen.
     */
    async setResultPackageOnMove(packageName: string, rowIndex = 0) {
        await this.erpLocators.inventoryMoveManageLinesAction.nth(rowIndex).click({ timeout: 15000 });
        await this.selectResultPackageInOpenMoveLinesModal(packageName);
    }

    private async selectResultPackageInOpenMoveLinesModal(packageName: string) {
        const l = this.erpLocators;

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

        if (data.operationTypeName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationTypeSelect, data.operationTypeName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(800);
        } else if (data.operationType) {
            await this.selectOperationTypeForWarehouse(data.operationType, "Delivery Orders");
        }

        if (data.partnerName) {
            await this.selectBySearch(this.erpLocators.inventoryOperationPartnerSelect, data.partnerName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        if (data.origin) {
            await this.erpLocators.inventoryOperationAdditionalTab.click();
            await this.erpLocators.inventoryOperationOriginInput.fill(data.origin);
        }

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectOperationCreated();

        return this.readOperationReference();
    }

    /**
     * Mark as Todo then Check Availability so on-hand stock gets reserved before
     * a validate (needed for the partial-delivery backorder flow).
     */
    async reserveForValidate() {
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);
        await this.clickCheckAvailabilityIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);
    }

    /**
     * Validate a partially-reserved operation and confirm the "Create Back Order?"
     * modal, creating the backorder for the remaining quantity (create_backorder = Ask).
     */
    async validateCreatingBackorder() {
        await this.reserveForValidate();
        await this.erpLocators.inventoryOperationValidateButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
        await expect(this.erpLocators.inventoryOperationBackorderModal).toBeVisible({ timeout: 20000 });
        await this.erpLocators.inventoryOperationBackorderConfirmButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Validate a partially-reserved operation expecting no "Create Back Order?"
     * modal (create_backorder = Never or Always go straight through).
     */
    async validateWithoutBackorderModal() {
        await this.reserveForValidate();
        await this.erpLocators.inventoryOperationValidateButton.click({ timeout: 15000 });
        await this.page.waitForTimeout(1500);
        await expect(this.erpLocators.inventoryOperationBackorderModal).toBeHidden();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Assert how many delivery operations carry the given source document (origin).
     * A backorder replicates the origin, so the count is 2 when one was created,
     * 1 when it wasn't.
     */
    async expectDeliveryCountByOrigin(origin: string, count: number) {
        await this.gotoDeliveriesPage();
        await this.searchList(origin);
        const rows = this.erpLocators.inventoryTableRows.filter({ hasText: origin });
        await expect(rows).toHaveCount(count);
    }

    /**
     * Assert how many receipt operations carry the given source document (origin).
     * A backorder replicates the origin, so the count is 2 when one was created,
     * 1 when it wasn't.
     */
    async expectReceiptCountByOrigin(origin: string, count: number) {
        await this.gotoReceiptsPage();
        await this.searchList(origin);
        const rows = this.erpLocators.inventoryTableRows.filter({ hasText: origin });
        await expect(rows).toHaveCount(count);
    }

    /**
     * Set the first move's done quantity to a partial value via the "Manage Stock
     * Moves" modal (the inline table quantity auto-fills to demand and is not
     * editable), so a backorder is created for the remainder.
     */
    async setMoveDoneQuantity(quantity: string) {
        const l = this.erpLocators;
        await l.inventoryMoveManageLinesAction.first().click({ timeout: 15000 });
        await expect(l.inventoryMoveLinesModal).toBeVisible();

        const modal = this.page.locator(".fi-modal-window:visible").last();
        const row = modal.locator("table tbody tr").first();
        const qtyInput = row.locator('input[type="number"]').last();
        await qtyInput.waitFor({ state: "visible", timeout: 10000 });
        await qtyInput.fill(quantity);
        await this.page.waitForTimeout(400);

        await l.inventoryMoveLinesModalSaveButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(600);
    }

    /**
     * On an open operation whose demand exceeds the done quantity, mark it to do,
     * receive only `doneQty`, then validate. For `ask` the "Create Back Order?"
     * modal must appear and is confirmed (creating the backorder); for `never` /
     * `always` no modal appears (always still creates one, never does not).
     */
    async receivePartialWithBackorder(doneQty: string, policy: "ask" | "never" | "always") {
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(800);

        await this.setMoveDoneQuantity(doneQty);

        await this.erpLocators.inventoryOperationValidateButton.click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);

        if (policy === "ask") {
            await expect(this.erpLocators.inventoryOperationBackorderModal).toBeVisible({ timeout: 20000 });
            await this.erpLocators.inventoryOperationBackorderConfirmButton.click({ timeout: 15000 });
        } else {
            await expect(this.erpLocators.inventoryOperationBackorderModal).toBeHidden();
        }
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Edit a warehouse's Receipts operation type to the given backorder policy
     * (the warehouse default is Ask). The op-type list is scoped by the unique
     * warehouse name so the right record is found even with many op-types.
     */
    async editOperationTypeBackorderForWarehouse(warehouseName: string, policy: "ask" | "always" | "never") {
        await this.gotoOperationTypesPage();
        await this.searchList(warehouseName);
        const row = this.erpLocators.inventoryTableRows
            .filter({ hasText: warehouseName })
            .filter({ hasText: /Receipts/ })
            .first();
        await expect(row).toBeVisible({ timeout: 15000 });
        await row.locator("a").first().click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        const editUrl = `${this.page.url().replace(/\/$/, "")}/edit`;
        await this.page.goto(editUrl);
        await expect(this.erpLocators.inventoryConfigNameInput).toBeVisible({ timeout: 15000 });

        await this.erpLocators.inventoryOperationTypeBackorderSelect.selectOption(policy);
        await this.page.waitForTimeout(400);

        // The edit page's submit is "Save changes" (not the create page's
        // key-bindings-1), so target it by role.
        await this.page
            .getByRole("button", { name: /Save changes|^Save$/i })
            .first()
            .click({ timeout: 15000 });
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToast();
    }

    async deliveryFullFlow(data: DeliveryData) {
        await this.createDelivery(data);
        await this.confirmAndValidateOperation();
    }

    /**
     * Create a delivery with one or more product lines and return its reference.
     */
    async createDeliveryLines(lines: MoveLineInput[], operationType?: string): Promise<string> {
        await this.gotoDeliveriesPage();
        await this.erpLocators.inventoryOperationCreateButton.click();
        await expect(this.page).toHaveURL(/deliveries\/create/);

        if (operationType) {
            await this.selectOperationTypeForWarehouse(operationType, "Delivery Orders");
        }

        await this.addMoveLines(lines);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectOperationCreated();

        return this.readOperationReference();
    }

    /**
     * Deliver one or more product lines and validate. Lot/serial-tracked lines
     * reserve their existing stock on Check Availability (no manual lot pick).
     */
    async deliveryLinesFullFlow(lines: MoveLineInput[], operationType?: string) {
        await this.createDeliveryLines(lines, operationType);
        await this.confirmAndValidateOperation();
    }

    /**
     * Confirm the open operation (Mark as Todo). An At Confirm operation reserves
     * its stock here; a Manual one only reserves on Check Availability.
     */
    async markAsTodo() {
        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Reserve the open operation's stock (Check Availability).
     */
    async checkAvailability() {
        await this.clickCheckAvailabilityIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Assert the open operation offers the "Check Availability" action.
     */
    async expectCheckAvailabilityVisible() {
        await expect(this.erpLocators.inventoryOperationCheckAvailabilityButton).toBeVisible({ timeout: 15000 });
    }

    /**
     * Operations - Returns
     */

    /**
     * From the currently-open validated operation, open the Return modal and
     * submit it, creating the return operation. The modal pre-fills each
     * returnable move's quantity; pass `quantity` to return a partial amount of
     * the first move instead. Lands on the new return operation's draft edit page.
     */
    async returnCurrentOperation(quantity?: string) {
        const l = this.erpLocators;
        const startUrl = this.page.url();

        await l.inventoryOperationReturnButton.waitFor({ state: "visible", timeout: 60000 });

        for (let attempt = 0; attempt < 3; attempt++) {
            if (await l.inventoryReturnModal.isVisible().catch(() => false)) {
                break;
            }
            await l.inventoryOperationReturnButton.click({ timeout: 8000 }).catch(() => undefined);
            await this.page.waitForTimeout(700);
        }
        await expect(l.inventoryReturnModal).toBeVisible({ timeout: 10000 });
        await l.inventoryReturnModalQtyInput.first().waitFor({ state: "visible", timeout: 10000 });

        if (quantity) {
            await l.inventoryReturnModalQtyInput.first().fill(quantity);
        }

        await l.inventoryReturnModalSubmitButton.click({ timeout: 15000 });

        await this.page
            .waitForURL((url) => url.toString() !== startUrl, { timeout: 20000 })
            .catch(() => undefined);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(l.inventoryReturnModal).toBeHidden({ timeout: 10000 }).catch(() => undefined);
        await this.page.waitForTimeout(600);

        return this.readOperationReference();
    }

    /**
     * Validate the open return operation to Done. Unlike the shared operation
     * validator this clicks "Validate" exactly once and then waits for the button
     * to detach — never re-clicking — because on a return the "Return" action
     * renders in the header slot the "Validate" button vacates, so a retry click
     * would land on it and pop open the Return modal.
     */
    async validateReturnOperation() {
        const l = this.erpLocators;

        await this.clickMarkAsTodoIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(600);

        await this.clickCheckAvailabilityIfVisible();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(600);

        const validateBtn = l.inventoryOperationValidateButton;
        if (await validateBtn.isVisible().catch(() => false)) {
            await validateBtn.click({ timeout: 15000 }).catch(() => undefined);
            await this.page.waitForTimeout(800);

            if (await l.inventoryOperationNoBackorderButton.isVisible().catch(() => false)) {
                await l.inventoryOperationNoBackorderButton.click({ timeout: 15000 }).catch(() => undefined);
                await this.page.waitForTimeout(500);
            }

            await validateBtn.waitFor({ state: "detached", timeout: 15000 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(500);
        }

        await this.settleAfterValidation();
        await this.expectSuccessToastSoft();
    }

    /**
     * After validating an operation to Done, the "Return" action lands in the
     * header slot the "Validate" button vacated and Filament can auto-open its
     * modal. Poll until the page is stable — the Return button is present and no
     * Return modal is open — dismissing any stray modal as it appears. A single
     * Escape races the modal's render, so this retries until settled.
     */
    private async settleAfterValidation() {
        const l = this.erpLocators;
        let revalidations = 0;

        for (let attempt = 0; attempt < 30; attempt++) {
            await this.dismissReturnModalIfOpen();
            const modalOpen = await l.inventoryReturnModal.isVisible().catch(() => false);
            const returnReady = await l.inventoryOperationReturnButton.isVisible().catch(() => false);

            if (returnReady && !modalOpen) {
                return;
            }

            // A Validate click that lands while Livewire is mid-request is swallowed, so the
            // operation quietly stays open and the Return button never arrives. Re-issue the
            // validation instead of walking into a 15s wait for a button that is not coming.
            const stillOpen = await l.inventoryOperationValidateButton.isVisible().catch(() => false);

            if (!modalOpen && stillOpen && revalidations < 3) {
                revalidations++;
                await l.inventoryOperationValidateButton.click({ timeout: 15000 }).catch(() => undefined);
                await this.page.waitForTimeout(800);

                if (await l.inventoryOperationNoBackorderButton.isVisible().catch(() => false)) {
                    await l.inventoryOperationNoBackorderButton.click({ timeout: 15000 }).catch(() => undefined);
                }

                await this.page.waitForLoadState("networkidle").catch(() => undefined);
            }

            await this.page.waitForTimeout(1000);
        }
    }

    /**
     * Close the Return modal if it is open (dismisses a stray auto-open),
     * retrying because a single Escape can fire before the modal has rendered.
     */
    private async dismissReturnModalIfOpen() {
        const modal = this.erpLocators.inventoryReturnModal;
        for (let attempt = 0; attempt < 4; attempt++) {
            if (!(await modal.isVisible().catch(() => false))) {
                return;
            }
            await this.page.keyboard.press("Escape").catch(() => undefined);
            await this.page.waitForTimeout(400);
        }
    }

    /**
     * Return a validated operation and validate the resulting return operation.
     */
    async returnAndValidate(quantity?: string) {
        await this.returnCurrentOperation(quantity);
        await this.validateReturnOperation();
    }

    /**
     * Assert the page is on a return operation (a new operation created under
     * whichever resource its return type resolves to, opened on view or edit).
     */
    async expectOnReturnOperationPage() {
        await expect(this.page).toHaveURL(/operations\/(receipts|deliveries|internals|dropships)\/\d+\/(edit|view)/);
    }

    /**
     * Assert the current operation's view page shows the given source/destination
     * location. Both entries show on an operation's view page.
     */
    async expectOperationLocation(kind: "source" | "destination", locationName: string) {
        const entry = this.operationLocationEntry(kind);
        await expect(entry).toBeVisible({ timeout: 15000 });
        await expect(entry).toContainText(locationName);
    }

    /**
     * Read the source/destination location value shown on the current operation's
     * view page (strips the field label off the entry's text).
     */
    async readOperationLocation(kind: "source" | "destination"): Promise<string> {
        const label = kind === "source" ? "Source Location" : "Destination Location";
        const entry = this.operationLocationEntry(kind);
        await expect(entry).toBeVisible({ timeout: 15000 });
        const text = (await entry.textContent()) ?? "";
        return text.replace(label, "").replace(/\s+/g, " ").trim();
    }

    private operationLocationEntry(kind: "source" | "destination"): Locator {
        const label = kind === "source" ? "Source Location" : "Destination Location";
        return this.erpLocators.inventoryInfolistEntries.filter({ hasText: label }).first();
    }

    /**
     * Navigate to the current operation's read-only view page (its locations
     * render as text there, unlike the edit form's selects).
     */
    async gotoCurrentOperationView() {
        if (/\/view(\?.*)?$/.test(this.page.url())) {
            return;
        }
        const url = this.page.url().replace(/\/edit(\?.*)?$/, "/view");
        await this.page.goto(url);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    /**
     * Capture the source/destination locations of the current (source) operation,
     * create its return, and assert the return reverses them: the return's source
     * is the operation's destination and its destination is the operation's source.
     */
    async returnAndExpectReversedLocations() {
        await this.gotoCurrentOperationView();
        const sourceLoc = await this.readOperationLocation("source");
        const destLoc = await this.readOperationLocation("destination");

        await this.returnCurrentOperation();

        await this.gotoCurrentOperationView();
        await this.expectOperationLocation("source", destLoc);
        await this.expectOperationLocation("destination", sourceLoc);
    }

    /**
     * Create a partial return of the current operation, assert the return carries
     * the entered quantity, then assert it reverses the operation's locations.
     */
    async partialReturnAndExpectReversedLocations(productName: string, quantity: string) {
        await this.gotoCurrentOperationView();
        const sourceLoc = await this.readOperationLocation("source");
        const destLoc = await this.readOperationLocation("destination");

        await this.returnCurrentOperation(quantity);

        await this.expectOnReturnOperationPage();
        await this.expectCurrentOperationMoveQuantity(productName, quantity);

        await this.gotoCurrentOperationView();
        await this.expectOperationLocation("source", destLoc);
        await this.expectOperationLocation("destination", sourceLoc);
    }

    /**
     * Assert the current operation lists a move for the product at the given quantity.
     */
    /**
     * Assert the Demand of a move row on the currently-open operation. Demand renders as a
     * numeric input, so it is read off the field rather than the row's text.
     */
    async expectOperationMoveDemand(demand: string, rowIndex = 0) {
        const input = this.erpLocators.inventoryOperationMoveDemandInput.nth(rowIndex);
        await expect(input).toBeVisible({ timeout: 15000 });
        await expect(input).toHaveValue(new RegExp(`^${demand}(\\.0+)?$`));
    }

    /**
     * Assert the Demand of the move that carries `productName` on the currently-open
     * operation. Move rows are not ordered by sale-order line, so they are addressed by
     * product.
     */
    async expectOperationMoveDemandForProduct(productName: string, demand: string) {
        const row = await this.moveRowForProduct(productName);
        await expect(row).toBeVisible({ timeout: 15000 });

        const input = row.locator('input[id$=".product_uom_qty"]').first();
        await expect(input).toHaveValue(new RegExp(`^${demand}(\\.0+)?$`));
    }

    /**
     * Assert how many move lines the currently-open operation carries.
     */
    async expectOperationMoveCount(count: number) {
        await expect(this.erpLocators.inventoryOperationMoveDemandInput).toHaveCount(count);
    }

    async expectCurrentOperationMoveQuantity(productName: string, quantity: string) {
        const row = await this.moveRowForProduct(productName);
        await expect(row).toBeVisible();
        const escaped = quantity.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        await expect(row).toContainText(new RegExp(`(^|\\s)${escaped}(\\.0+)?(\\s|$)`));
    }

    /**
     * Assert the current operation is validated (Done): it no longer offers
     * "Validate" and instead exposes the "Return" action shown only once done.
     */
    async expectOperationDone() {
        await expect(this.erpLocators.inventoryOperationValidateButton).toBeHidden();
        await expect(this.erpLocators.inventoryOperationReturnButton).toBeVisible();
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

        if (data.operationType && data.operationTypeName) {
            await this.selectOperationTypeForWarehouse(data.operationType, data.operationTypeName);
        }

        await this.addMoveLines([{ productName: data.productName, demand: data.demand }]);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await this.expectOperationCreated();

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
     * Open a package from the list and land on its "Products" sub-page. The list defaults
     * to an "Internal Locations" view, which hides a package once a delivery has shipped
     * it to the customer, so switch to the unfiltered "Default" view first.
     */
    async gotoPackageProductsTab(packageName: string) {
        await this.gotoPackagesPage();

        const defaultView = this.erpLocators.inventoryPackageDefaultViewTab;
        if (await defaultView.isVisible().catch(() => false)) {
            await defaultView.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
        }

        await this.searchList(packageName);
        const link = this.erpLocators.inventoryTableRows.locator("a").filter({ hasText: packageName }).first();
        await expect(link).toBeVisible();
        await link.click();
        await this.page.waitForLoadState("networkidle");

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
     * Create a draft scrap for the product, routing it to the given scrap
     * (destination) location. Selecting the product auto-fills its unit.
     */
    async createScrapAtLocation(productName: string, qty: string, scrapLocationName: string) {
        await this.gotoScrapsPage();
        await this.erpLocators.inventoryScrapCreateButton.click();
        await expect(this.page).toHaveURL(/scraps\/create/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.selectBySearch(this.erpLocators.inventoryScrapProductSelect, productName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.erpLocators.inventoryScrapQtyInput.fill(qty);
        await this.selectFromFilamentDropdown(this.erpLocators.inventoryScrapDestinationLocationSelect, scrapLocationName);

        await this.erpLocators.inventoryOperationSaveButton.click();
        await expect(this.page).not.toHaveURL(/scraps\/create/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
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

        await this.page.waitForTimeout(500);

        await productSelects.first().waitFor({ state: "visible", timeout: 10000 }).catch(() => undefined);

        for (let i = 0; i < lines.length; i++) {
            if ((await productSelects.count()) < i + 1) {
                await this.erpLocators.inventoryOperationAddMoveButton.scrollIntoViewIfNeeded();
                await this.erpLocators.inventoryOperationAddMoveButton.click();
                await expect(productSelects.nth(i)).toBeVisible();
            }
            await this.selectBySearch(productSelects.nth(i), lines[i].productName);

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

    /**
     * Saving an operation redirects onto its edit page, and that redirect tears the
     * success toast down before it can be observed. Assert the redirect instead.
     */
    private async expectOperationCreated() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/\/create$/);
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.inventorySuccessToast).toBeVisible();
    }

    private async expectSuccessToastSoft() {
        try {
            await expect(this.erpLocators.inventorySuccessToast).toBeVisible({ timeout: 2_500 });
        } catch {
            // Ignore.
        }
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.inventoryValidationMessage.first()).toBeVisible();
    }
}
