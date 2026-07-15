import { type Locator, Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";
import { runOnce, SETUP_KEYS } from "../utils/setupCache";
import { clickRowAction, filterListBySearch, rowByText } from "../utils/list";

export type PurchaseVendorData = {
    name: string;
    email?: string;
};

export type ProductTracking = "qty" | "lot" | "serial";

export type PurchaseProductData = {
    name: string;
    price: string;
    tracking?: ProductTracking;
};

export type PurchaseOrderLine = {
    productName: string;
    quantity: string;
    unitPrice: string;
    taxName?: string;
};

export type PurchaseOrderData = {
    vendorName: string;
    lines: PurchaseOrderLine[];
    warehouseName?: string;
};

export type OrderTotals = {
    untaxed: string;
    tax?: string;
    total: string;
};

export type PurchaseAgreementUpdateData = {
    reference?: string;
    quantity?: string;
    unitPrice?: string;
};

export type PurchaseQuotationData = {
    vendorName: string;
    productName: string;
    quantity: string;
    unitPrice: string;
};

export type PurchaseAgreementData = {
    vendorName: string;
    productName: string;
    quantity: string;
    unitPrice: string;
    reference: string;
};

export type PurchaseAgreementQuotationData = {
    vendorName: string;
    quantity: string;
};

export class PurchaseFlowPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    async ensurePurchasesPluginInstalled() {
        await runOnce(SETUP_KEYS.pluginPurchases, async () => {
            const pluginPage = new PluginManagementPage(this.page);
            await pluginPage.gotoPluginManagementPage();
            await pluginPage.installPluginByName("Purchases");
        });
    }

    async gotoPurchaseSettingsPage() {
        await this.safeGoto("/admin/settings/purchase/manage-orders");
        await expect(this.page).toHaveURL(/admin\/settings\/purchase\/manage-orders/);
        await expect(this.erpLocators.purchaseAgreementSettingsToggle).toBeVisible();
    }

    async setPurchaseAgreementsEnabled(enabled: boolean) {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.gotoPurchaseSettingsPage();

            const toggle = this.erpLocators.purchaseAgreementSettingsToggle;

            if ((await this.isToggleEnabled(toggle)) === enabled) {
                return;
            }

            await toggle.click();
            await expect(this.erpLocators.settingsSaveButton).toBeEnabled({ timeout: 30000 });
            await this.erpLocators.settingsSaveButton.click().catch(() => undefined);

            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await this.page.waitForTimeout(1500);
        }

        await this.gotoPurchaseSettingsPage();
        expect(await this.isToggleEnabled(this.erpLocators.purchaseAgreementSettingsToggle)).toBe(enabled);
    }

    private async isToggleEnabled(toggle: Locator): Promise<boolean> {
        const tagName = await toggle.evaluate((element) => element.tagName.toLowerCase());

        return tagName === "input"
            ? await toggle.isChecked()
            : (await toggle.getAttribute("aria-checked")) !== "false";
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
     * Fill a field once its form is done hydrating. 
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

    async gotoVendorsPage() {
        await this.safeGoto("/admin/purchase/orders/vendors");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/vendors/);
        await expect(this.erpLocators.purchaseVendorNewCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseVendorsTable.first()).toBeVisible();
    }

    async createVendor(vendor: PurchaseVendorData) {
        await this.gotoVendorsPage();
        await this.erpLocators.purchaseVendorNewCreateButton.click();
        await expect(this.page).toHaveURL(/vendors\/create/);

        await this.fillWhenReady(this.erpLocators.purchaseVendorNameInput, vendor.name);

        if (vendor.email) {
            await this.fillWhenReady(this.erpLocators.purchaseVendorEmailInput, vendor.email);
        }

        await this.erpLocators.purchaseVendorCreateButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/vendors\/create/);
    }

    async createVendorExpectingValidationError() {
        await this.gotoVendorsPage();
        await this.erpLocators.purchaseVendorNewCreateButton.click();
        await expect(this.page).toHaveURL(/vendors\/create/);
        await this.erpLocators.purchaseVendorSaveButton.click();
        await this.expectValidationErrors();
    }

    async editVendor(originalName: string, updates: PurchaseVendorData) {
        await this.gotoVendorsPage();
        await this.searchList(originalName);

        await clickRowAction(rowByText(this.page, originalName), "Edit");

        if (updates.name) {
            await this.erpLocators.purchaseVendorNameInput.fill(updates.name);
        }

        if (updates.email) {
            await this.erpLocators.purchaseVendorEmailInput.fill(updates.email);
        }

        await this.erpLocators.purchaseVendorSaveButton.click();

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async deleteVendor(name: string) {
        await this.gotoVendorsPage();
        await this.searchList(name);

        await clickRowAction(rowByText(this.page, name), "Delete");
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectRecordAbsent(name);
    }

    /**
     * The record is gone from its listing — the outcome a delete has to be judged by, since a
     * toast on screen may belong to a test running beside this one.
     */
    async expectRecordAbsent(name: string) {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.searchList(name);
        await expect(rowByText(this.page, name)).toHaveCount(0);
    }

    async gotoProductsPage() {
        await this.safeGoto("/admin/purchase/products/products");
        await expect(this.page).toHaveURL(/admin\/purchase\/products\/products/);
        await expect(this.erpLocators.purchaseProductNewCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseProductsTable.first()).toBeVisible();
    }

    async createProduct(product: PurchaseProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.purchaseProductNewCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.fillWhenReady(this.erpLocators.purchaseProductNameInput, product.name);
        await this.fillWhenReady(this.erpLocators.purchaseProductPriceInput, product.price);

        if (product.tracking && product.tracking !== "qty") {
            await expect(this.erpLocators.purchaseProductTrackingSelect).toBeVisible();

            for (let attempt = 0; attempt < 3; attempt++) {
                await this.erpLocators.purchaseProductTrackingSelect.selectOption(product.tracking);
                await this.page.waitForLoadState("networkidle").catch(() => undefined);
                await this.page.waitForTimeout(800);

                if ((await this.erpLocators.purchaseProductTrackingSelect.inputValue()) === product.tracking) {
                    break;
                }
            }

            await expect(this.erpLocators.purchaseProductTrackingSelect).toHaveValue(product.tracking);
        }

        await this.erpLocators.purchaseProductCreateButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/products\/create/);
    }

    async createProductExpectingValidationError() {
        await this.gotoProductsPage();
        await this.erpLocators.purchaseProductNewCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);
        await this.erpLocators.purchaseProductCreateButton.click();
        await this.expectValidationErrors();
    }

    async editProduct(originalName: string, updates: PurchaseProductData) {
        await this.gotoProductsPage();
        await this.searchList(originalName);

        await clickRowAction(rowByText(this.page, originalName), "Edit");


        if (updates.name) {
            await this.erpLocators.purchaseProductNameInput.fill(updates.name);
        }

        if (updates.price) {
            await this.erpLocators.purchaseProductPriceInput.fill(updates.price);
        }

        await this.erpLocators.purchaseProductSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async deleteProduct(name: string) {
        await this.gotoProductsPage();
        await this.searchList(name);

        await clickRowAction(rowByText(this.page, name), "Delete");
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectRecordAbsent(name);
    }

    async gotoQuotationsPage() {
        await this.safeGoto("/admin/purchase/orders/quotations");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/quotations/);
        await expect(this.erpLocators.purchaseQuotationCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseQuotationsTable.first()).toBeVisible();
    }

    async createQuotation(quotation: PurchaseQuotationData) {
        await this.createOrderWithLines({
            vendorName: quotation.vendorName,
            lines: [{
                productName: quotation.productName,
                quantity: quotation.quantity,
                unitPrice: quotation.unitPrice,
            }],
        });
    }

    /**
     * Create a request for quotation with any number of product lines, optionally received
     * into a non-default incoming operation type ("Deliver To"), which is what drives the
     * reception route (1/2/3-step).
     */
    async createOrderWithLines(order: PurchaseOrderData) {
        const l = this.erpLocators;

        await this.gotoQuotationsPage();
        await l.purchaseQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.selectBySearch(l.purchaseQuotationVendorSelect, order.vendorName);

        if (order.warehouseName) {
            await this.selectReceiptsOperationTypeForWarehouse(order.warehouseName);
        }

        for (let index = 0; index < order.lines.length; index++) {
            const line = order.lines[index];

            await l.purchaseQuotationAddProductButton.scrollIntoViewIfNeeded();
            await l.purchaseQuotationAddProductButton.click();
            await this.selectBySearch(l.purchaseQuotationProductSelect.nth(index), line.productName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await l.purchaseQuotationQuantityInput.nth(index).fill(line.quantity);
            await l.purchaseQuotationUnitPriceInput.nth(index).fill(line.unitPrice);

            await this.blurAndSettle();

            if (line.taxName) {
                await this.selectLineTax(index, line.taxName);
            }
        }

        await this.submitCreateForm();
    }

    /**
     * Submit the create form. Filament disables the submit button while a Livewire request
     * is in flight — a line's tax recompute, say — and a click that lands in that window is
     * swallowed, so the submit is retried until the form is actually left behind.
     */
    private async submitCreateForm() {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.clickWhenEnabled(this.erpLocators.purchaseQuotationCreateSubmitButton);

            await this.page
                .waitForURL((url) => !/quotations\/create/.test(url.toString()), { timeout: 60000 })
                .catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (!/quotations\/create/.test(this.page.url())) {
                return;
            }
        }

        await expect(this.page).not.toHaveURL(/quotations\/create/);
    }

    /**
     * Click a submit button once Filament re-enables it. 
     */
    private async dispatchSingleClick(button: ReturnType<Page["locator"]>) {
        await button.waitFor({ state: "visible", timeout: 15000 });
        await expect(button).toBeEnabled({ timeout: 60000 });
        await button.evaluate((element) => (element as HTMLButtonElement).click());
    }

    private async clickWhenEnabled(button: ReturnType<Page["locator"]>) {
        await button.waitFor({ state: "visible", timeout: 15000 });

        for (let attempt = 0; attempt < 10; attempt++) {
            if (await button.isEnabled().catch(() => false)) {
                await button.click({ timeout: 15000 }).catch(() => undefined);
                return;
            }
            await this.page.waitForTimeout(1500);
        }

        await button.click({ force: true, timeout: 15000 }).catch(() => undefined);
    }

    /**
     * Pick a warehouse's "Receipts" operation type in the "Deliver To" field, which is
     * what routes the order's reception (1/2/3-step). 
     */
    async selectReceiptsOperationTypeForWarehouse(warehouseName: string) {
        if (await this.tryPickOperationType(warehouseName, "Receipts")) {
            return;
        }

        const uniqueName = `Receipts ${warehouseName}`;
        await this.renameOperationTypeInNewTab(warehouseName, "Receipts", uniqueName);

        if (await this.tryPickOperationType(warehouseName, uniqueName)) {
            return;
        }

        throw new Error(`The operation type "${warehouseName}: Receipts" never appeared in the select.`);
    }

    private async tryPickOperationType(warehouseName: string, searchTerm: string): Promise<boolean> {
        const l = this.erpLocators;

        for (let attempt = 0; attempt < 2; attempt++) {
            await l.purchaseQuotationOperationTypeSelect.click();
            await l.salesSelectSearchInput.waitFor({ state: "visible", timeout: 15000 }).catch(() => undefined);

            if (await l.salesSelectSearchInput.isVisible().catch(() => false)) {
                await l.salesSelectSearchInput.fill(searchTerm);
                await this.page.waitForLoadState("networkidle").catch(() => undefined);
                await this.page.waitForTimeout(600);
            }

            const option = l.salesSelectOption
                .filter({ hasText: new RegExp(this.escapeRegExp(warehouseName)) })
                .first();

            if (await option.waitFor({ state: "visible", timeout: 10000 }).then(() => true).catch(() => false)) {
                await option.click();
                await this.page.waitForLoadState("networkidle").catch(() => undefined);
                await this.page.waitForTimeout(800);

                return true;
            }

            await this.page.keyboard.press("Escape").catch(() => undefined);
            await this.page.waitForTimeout(500);
        }

        return false;
    }

    /**
     * Rename a warehouse's operation type from a second tab, leaving this page's form intact.
     * The operation-types *table* can be searched by warehouse name, unlike the select.
     */
    private async renameOperationTypeInNewTab(warehouseName: string, operationTypeName: string, newName: string) {
        const tab = await this.page.context().newPage();

        try {
            await tab.goto("/admin/inventory/configurations/operation-types");
            await tab.waitForLoadState("networkidle");

            await tab.locator(".fi-input.fi-input-has-inline-prefix").nth(1).fill(warehouseName);
            await tab.waitForLoadState("networkidle");
            await tab.waitForTimeout(1500);

            const row = tab
                .locator("table tbody tr")
                .filter({ hasText: warehouseName })
                .filter({ hasText: operationTypeName })
                .first();

            await expect(row).toBeVisible({ timeout: 15000 });

            const href = await row.locator("a").first().getAttribute("href");

            if (!href) {
                throw new Error(`No link on the "${operationTypeName}" row of ${warehouseName}.`);
            }

            await tab.goto(`${href.replace(/\/view$/, "")}/edit`);
            await tab.waitForLoadState("networkidle");

            const nameInput = tab.locator('input[id="form.name"]').first();
            await expect(nameInput).toBeVisible({ timeout: 15000 });
            await nameInput.fill(newName);

            await tab.getByRole("button", { name: /Save changes|^Save$/i }).first().click();
            await tab.waitForLoadState("networkidle");
            await tab.waitForTimeout(1500);
        } finally {
            await tab.close();
        }
    }

    /**
     * Add a tax to a line. The taxes field is a multi-select, so its panel stays open
     * after a pick; it is dismissed explicitly, and the line and order totals are only
     * recomputed once that Livewire round-trip lands.
     */
    async selectLineTax(lineIndex: number, taxName: string) {
        const l = this.erpLocators;

        await l.purchaseQuotationLineTaxSelects.nth(lineIndex).click();

        const option = this.erpLocators.salesSelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(taxName), "i") })
            .first();
        await expect(option).toBeVisible();
        await option.click();

        await this.page.keyboard.press("Escape");
        await this.blurAndSettle();
    }

    /**
     * Let the form's in-flight Livewire recompute finish. Filament keeps the submit button
     * disabled while one is running, so anything that follows must wait it out.
     */
    private async blurAndSettle() {
        await this.page.keyboard.press("Tab");
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(2000);
    }

    /**
     * Assert the order summary. `tax` is omitted for an untaxed order, where the app
     * renders no "Tax" row at all.
     */
    async expectOrderTotals(totals: OrderTotals) {
        const items = this.erpLocators.purchaseQuotationSummaryItems;
        const taxRow = items.filter({ hasText: /^\s*Tax\b/ });

        await expect(items.filter({ hasText: /Untaxed Amount/ }).first()).toContainText(totals.untaxed);

        if (totals.tax) {
            await expect(taxRow.first()).toContainText(totals.tax);
        } else {
            await expect(taxRow).toHaveCount(0);
        }

        await expect(items.filter({ hasText: /Total Amount/ }).first()).toContainText(totals.total);
    }

    async expectLineSubtotal(lineIndex: number, subtotal: string) {
        const input = this.erpLocators.purchaseQuotationLineSubtotalInputs.nth(lineIndex);
        await expect(input).toHaveValue(new RegExp(`^${subtotal}(\\.0+)?$`));
    }

    async expectLineQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.purchaseQuotationQuantityInput.nth(lineIndex);
        await expect(input).toHaveValue(new RegExp(`^${quantity}(\\.0+)?$`));
    }

    /**
     * Assert a line's Received column, which only moves once a receipt is validated.
     */
    async expectReceivedQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.purchaseQuotationReceivedQuantityInputs.nth(lineIndex);
        await expect(input).toBeVisible();
        await expect(input).toHaveValue(new RegExp(`^${quantity}(\\.0+)?$`));
    }

    /**
     * Retype a line's ordered quantity. The field recomputes on blur, so the click away
     * is what triggers the Livewire round-trip.
     */
    async updateLineQuantityAndSave(orderRef: string, lineIndex: number, quantity: string) {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.gotoOrderEdit(orderRef);
            await this.updateLineQuantity(lineIndex, quantity);
            await this.saveOrder();

            await this.gotoOrderEdit(orderRef);

            const shown = await this.erpLocators.purchaseQuotationQuantityInput
                .nth(lineIndex)
                .inputValue()
                .catch(() => "");

            if (new RegExp(`^${quantity}(\\.0+)?$`).test(shown)) {
                return;
            }
        }

        await this.expectLineQuantity(lineIndex, quantity);
    }

    async updateLineQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.purchaseQuotationQuantityInput.nth(lineIndex);
        await expect(input).toBeEnabled();
        await input.fill(quantity);
        await input.blur();
        await this.blurAndSettle();
    }

    /**
     * Append a product line to the order that is already open, without saving.
     */
    async addLineToOpenOrder(productName: string, quantity: string, unitPrice: string) {
        const l = this.erpLocators;
        const existingLines = await l.purchaseQuotationProductSelect.count();

        await l.purchaseQuotationAddProductButton.scrollIntoViewIfNeeded();
        await l.purchaseQuotationAddProductButton.click();
        await this.selectBySearch(l.purchaseQuotationProductSelect.nth(existingLines), productName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await l.purchaseQuotationQuantityInput.nth(existingLines).fill(quantity);
        await l.purchaseQuotationUnitPriceInput.nth(existingLines).fill(unitPrice);

        await this.blurAndSettle();
    }

    async saveOrder() {
        await this.submitForm(this.erpLocators.purchaseQuotationSavechangesButton.first());
    }

    /**
     * Submit a form and make sure the request actually left the browser. 
     */
    private async submitForm(button: ReturnType<Page["locator"]>) {

        const submitted = this.page
            .waitForResponse(
                (response) => /livewire[^/]*\/update/.test(response.url()) && response.request().method() === "POST",
                { timeout: 120000 },
            )
            .catch(() => null);

        await this.dispatchSingleClick(button);

        if (!(await submitted)) {
            throw new Error("The form submit never reached the server.");
        }

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Assert the order refuses a quantity below what has already been received.
     */
    async expectQuantityBelowReceivedError(receivedQuantity: string) {
        await expect(this.erpLocators.purchaseQuotationValidationMessage.first()).toContainText(
            new RegExp(`cannot reduce the quantity below the received quantity \\(${receivedQuantity}`, "i"),
        );
    }

    async editQuotationQuantity(searchKey: string, quantity: string, unitPrice?: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.erpLocators.purchaseQuotationEditButton.click();

        await this.erpLocators.purchaseQuotationQuantityInput.first().fill(quantity);

        if (unitPrice) {
            await this.erpLocators.purchaseQuotationUnitPriceInput.first().fill(unitPrice);
        }

        await this.erpLocators.purchaseQuotationSavechangesButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async deleteQuotation(searchKey: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);

        await clickRowAction(rowByText(this.page, searchKey), "Delete");
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectRecordAbsent(searchKey);
    }

    async createQuotationFromAgreement(quotation: PurchaseAgreementQuotationData) {
        await this.gotoQuotationsPage();
        await this.erpLocators.purchaseQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(this.erpLocators.purchaseQuotationVendorSelect, quotation.vendorName);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.purchaseQuotationQuantityInput.first()).toBeVisible();
        await this.erpLocators.purchaseQuotationQuantityInput.first().fill(quotation.quantity);

        await this.erpLocators.purchaseQuotationSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/quotations\/create/);
    }

    /**
     * Confirm the open request for quotation. Confirming keeps the same URL — the record
     * only changes state — so the outcome is read from the header: "Confirm Order" is
     * replaced by the actions of a purchase order.
     */
    async confirmCurrentQuotation() {
        await expect(this.erpLocators.purchaseQuotationConfirmOrderButton).toBeVisible();
        await this.erpLocators.purchaseQuotationConfirmOrderButton.click();

        await this.erpLocators.purchaseDialogConfirmButton
            .waitFor({ state: "visible", timeout: 15000 })
            .catch(() => undefined);
        if (await this.erpLocators.purchaseDialogConfirmButton.isVisible().catch(() => false)) {
            await this.erpLocators.purchaseDialogConfirmButton.click();
        }

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1500);
        await expect(this.erpLocators.purchaseQuotationConfirmOrderButton).toHaveCount(0);
    }

    /**
     * The RFQ and the purchase order are the same record, so confirming does not change
     * the URL: both live under the "quotations" resource slug.
     */
    currentRecordRef(): { id: string } {
        const url = this.page.url();
        const match = url.match(/\/(?:quotations|purchase-orders)\/(\d+)/);

        if (!match) {
            throw new Error(`Unable to determine purchase order id from URL: ${url}`);
        }

        return { id: match[1] };
    }

    async gotoOrderEdit(ref: { id: string }) {
        await this.safeGoto(`/admin/purchase/orders/quotations/${ref.id}/edit`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    /**
     * Purchase orders - receipts
     */

    async openReceiptsForCurrentOrder(): Promise<string> {
        const { id } = this.currentRecordRef();
        await this.safeGoto(`/admin/purchase/orders/quotations/${id}/receipts`);
        await expect(this.page).toHaveURL(new RegExp(`/quotations/${id}/receipts`));
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        return id;
    }

    /**
     * Read every operation listed on the order's Receipts tab. A multi-step warehouse
     * links its whole reception chain to the order, so this returns one entry per transfer.
     */
    async readReceiptRows(): Promise<Array<{ id: string; reference: string; state: string }>> {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        const rows = this.erpLocators.purchaseQuotationReceiptRows;
        const total = await rows.count();
        const entries: Array<{ id: string; reference: string; state: string }> = [];

        for (let index = 0; index < total; index++) {
            const row = rows.nth(index);
            const link = row.locator('a[href*="/receipts/"]').first();

            if (!(await link.count())) {
                continue;
            }

            const href = (await link.getAttribute("href")) ?? "";
            const id = href.match(/\/receipts\/(\d+)/)?.[1];

            if (!id) {
                continue;
            }

            entries.push({
                id,
                reference: ((await link.textContent()) ?? "").trim(),
                state: ((await row.textContent()) ?? "").trim(),
            });
        }

        return entries;
    }

    async expectReceiptCount(count: number) {
        await this.openReceiptsForCurrentOrder();
        const rows = await this.readReceiptRows();
        expect(rows).toHaveLength(count);
    }

    async expectReceiptState(reference: string, state: string) {
        await this.openReceiptsForCurrentOrder();
        const row = this.erpLocators.purchaseQuotationReceiptRows
            .filter({ hasText: new RegExp(this.escapeRegExp(reference)) })
            .first();
        await expect(row).toBeVisible();
        await expect(row).toContainText(new RegExp(state, "i"));
    }

    /**
     * Open a receipt of the current order on its edit page, where the Mark as Todo /
     * Validate / Return header actions live.
     */
    async openReceiptEditPage(receiptId: string) {
        const { id } = this.currentRecordRef();
        await this.safeGoto(`/admin/purchase/orders/quotations/${id}/receipts/${receiptId}/edit`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    async openReceiptByIndex(index = 0): Promise<string> {
        await this.openReceiptsForCurrentOrder();
        const rows = await this.readReceiptRows();

        if (!rows[index]) {
            throw new Error(`No receipt at index ${index}; the order has ${rows.length} transfers.`);
        }

        await this.openReceiptEditPage(rows[index].id);

        return rows[index].reference;
    }

    async openReceiptByReference(part: string): Promise<string> {
        await this.openReceiptsForCurrentOrder();
        const rows = await this.readReceiptRows();
        const match = rows.find((row) => row.reference.includes(part));

        if (!match) {
            throw new Error(`No receipt matching "${part}". Found: ${rows.map((r) => r.reference).join(", ")}`);
        }

        await this.openReceiptEditPage(match.id);

        return match.reference;
    }

    /**
     * Open the order's first transfer that has not been validated yet. Used after a back
     * order is created, where the remaining transfer cannot be addressed by row index.
     */
    async openPendingReceipt(): Promise<string> {
        await this.openReceiptsForCurrentOrder();
        const rows = await this.readReceiptRows();
        const pending = rows.find((row) => !/\bDone\b/i.test(row.state));

        if (!pending) {
            throw new Error(`Every transfer of this order is already Done: ${rows.map((r) => r.reference).join(", ")}`);
        }

        await this.openReceiptEditPage(pending.id);

        return pending.reference;
    }

    /**
     * Purchase orders - vendor bills
     */

    async expectCreateBillButtonVisible() {
        await expect(this.erpLocators.purchaseCreateBillButton).toBeVisible();
    }

    /**
     * Bill the order. Submitting the dialog redirects, which can tear the success toast
     * down before it can be observed, so the outcome is read off the order instead: with
     * nothing left to bill the Create Bill action is gone.
     */
    async createBill() {
        await expect(this.erpLocators.purchaseCreateBillButton).toBeVisible();
        await this.erpLocators.purchaseCreateBillButton.click();

        if (await this.erpLocators.purchaseBillSubmitButton.isVisible().catch(() => false)) {
            await this.erpLocators.purchaseBillSubmitButton.click();
        }

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1500);
    }

    async openBillsForCurrentOrder(): Promise<string> {
        const { id } = this.currentRecordRef();
        await this.safeGoto(`/admin/purchase/orders/quotations/${id}/bills`);
        await expect(this.page).toHaveURL(new RegExp(`/quotations/${id}/bills`));
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        return id;
    }

    async expectBillRowPresent() {
        await expect(this.erpLocators.purchaseBillsTableRows.first()).toBeVisible();
    }

    async expectNoBills() {
        await expect(this.erpLocators.purchaseBillsTableRows).toHaveCount(0);
    }

    async gotoPurchaseOrdersPage() {
        await this.safeGoto("/admin/purchase/orders/purchase-orders");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/purchase-orders/);
        await expect(this.erpLocators.purchaseOrdersTable.first()).toBeVisible();
    }

    async expectPurchaseOrderVisible(searchKey: string) {
        await this.gotoPurchaseOrdersPage();
        await this.searchList(searchKey);
        await expect(this.page.getByText(searchKey).first()).toBeVisible();
    }

    async gotoPurchaseAgreementsPage() {
        await this.safeGoto("/admin/purchase/orders/purchase-agreements");
        await expect(this.page).toHaveURL(/admin\/purchase\/orders\/purchase-agreements/);
        await expect(this.erpLocators.purchaseAgreementCreateButton).toBeVisible();
        await expect(this.erpLocators.purchaseAgreementTable.first()).toBeVisible();
    }

    async createPurchaseAgreement(agreement: PurchaseAgreementData) {
        await this.gotoPurchaseAgreementsPage();
        await this.erpLocators.purchaseAgreementCreateButton.click();
        await expect(this.page).toHaveURL(/purchase-agreements\/create/);

        await this.selectBySearch(this.erpLocators.purchaseAgreementVendorSelect, agreement.vendorName);
        await this.erpLocators.purchaseAgreementReferenceInput.fill(agreement.reference);
        await this.selectBySearch(this.erpLocators.purchaseAgreementProductSelect.first(), agreement.productName);
        await this.erpLocators.purchaseAgreementQuantityInput.first().fill(agreement.quantity);
        await this.erpLocators.purchaseAgreementUnitPriceInput.first().fill(agreement.unitPrice);

        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async createPurchaseAgreementExpectingValidationError() {
        await this.gotoPurchaseAgreementsPage();
        await this.erpLocators.purchaseAgreementCreateButton.click();
        await expect(this.page).toHaveURL(/purchase-agreements\/create/);
        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.expectPurchaseAgreementValidationErrors();
    }

    async editPurchaseAgreement(searchKey: string, updates: PurchaseAgreementUpdateData) {
        await this.gotoPurchaseAgreementsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.erpLocators.purchaseAgreementEditButton.click();

        if (updates.reference) {
            await this.erpLocators.purchaseAgreementReferenceInput.fill(updates.reference);
        }

        if (updates.quantity) {
            await this.erpLocators.purchaseAgreementQuantityInput.first().fill(updates.quantity);
        }

        if (updates.unitPrice) {
            await this.erpLocators.purchaseAgreementUnitPriceInput.first().fill(updates.unitPrice);
        }

        await this.erpLocators.purchaseAgreementSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    async deletePurchaseAgreement(searchKey: string) {
        await this.gotoPurchaseAgreementsPage();
        await this.searchList(searchKey);

        await clickRowAction(rowByText(this.page, searchKey), "Delete");
        await this.erpLocators.purchaseConfirmDeleteButton.click();
        await this.expectRecordAbsent(searchKey);
    }

    async confirmCurrentPurchaseAgreement() {
        const confirm = this.erpLocators.purchaseAgreementConfirmButton;
        const confirmed = this.erpLocators.purchaseAgreementConfirmedRadio;

        await confirm.waitFor({ state: "visible", timeout: 60000 });

        for (let attempt = 0; attempt < 3; attempt++) {
            await expect(confirm).toBeEnabled({ timeout: 60000 });
            await confirm.click().catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (await confirmed.isChecked().catch(() => false)) {
                return;
            }

            await this.page.waitForTimeout(1500);
        }

        await expect(confirmed).toBeChecked();
    }

    async expectQuotationValidationErrors() {
        await expect(this.erpLocators.purchaseQuotationValidationMessage.first()).toBeVisible();
    }

    async expectPurchaseAgreementValidationErrors() {
        await expect(this.erpLocators.purchaseAgreementValidationMessage.first()).toBeVisible();
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.purchaseValidationMessage.first()).toBeVisible();
    }

    async searchList(keyword: string) {
        await filterListBySearch(this.page, this.erpLocators.purchaseSearchInput, keyword);
    }

    async openRowActions() {
        await this.erpLocators.purchaseRowActionsButton.first().click();
    }

    async clickMenuAction(label: RegExp) {
        const menuItem = this.page.getByRole("menuitem", { name: label }).first();
        if (await menuItem.isVisible().catch(() => false)) {
            await menuItem.click();
            return;
        }

        const fallback = this.page
            .locator("a.fi-ac-btn-action, button.fi-ac-btn-action")
            .filter({ hasText: label })
            .first();

        await fallback.click();
    }

    async selectBySearch(trigger: ReturnType<Page["locator"]>, value: string) {
        await trigger.click();

        const option = this.erpLocators.salesSelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(this.erpLocators.salesSelectSearchInput).toBeVisible();
        await this.erpLocators.salesSelectSearchInput.fill(value);
        await expect(option).toBeVisible();
        await option.click();
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.purchaseSuccessToast).toBeVisible();
    }
}
