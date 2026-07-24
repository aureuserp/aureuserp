import { Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type SalesCustomerData = {
    name: string;
    email?: string;
};

export type InvoicePolicy = "order" | "delivery";

export type ProductTracking = "qty" | "lot" | "serial";

export type SalesProductData = {
    name: string;
    price: string;
    invoicePolicy?: InvoicePolicy;
    tracking?: ProductTracking;
};

export type SalesQuotationLine = {
    productName: string;
    quantity: string;
    taxName?: string;
};

export type OrderTotals = {
    untaxed: string;
    tax?: string;
    total: string;
};

export type SalesQuotationData = {
    customerName: string;
    productName: string;
    quantity: string;
};

export type SalesOrderData = {
    customerName: string;
    lines: SalesQuotationLine[];
    warehouseName?: string;
};

export class SalesFlowPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    async ensureSalesPluginInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Sales");
    }

    /**
     * Navigate, retrying when a redirect still in flight from the previous page aborts or
     * interrupts this one. Landing here straight after saving a record elsewhere would
     * otherwise fail with net::ERR_ABORTED.
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

    async gotoCustomersPage() {
        await this.safeGoto("/admin/sale/orders/customers");
        await expect(this.page).toHaveURL(/sale\/orders\/customers/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.salesCustomerNewCreateButton).toBeVisible();
        await expect(this.erpLocators.salesCustomersTable.first()).toBeVisible();
    }

    async createCustomer(customer: SalesCustomerData) {
        await this.gotoCustomersPage();
        await this.erpLocators.salesCustomerNewCreateButton.click();
        await expect(this.page).toHaveURL(/customers\/create/);

        await this.fillWhenReady(this.erpLocators.salesCustomerNameInput, customer.name);
        if (customer.email) {
            await this.fillWhenReady(this.erpLocators.salesCustomerEmailInput, customer.email);
        }

        // "Create" redirects off the create form; the reachable outcome is that redirect,
        // whereas the success toast is torn down by it and cannot be relied on.
        await this.erpLocators.salesCustomerCreateButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/customers\/create/);
    }

    /**
     * Fill a field once its form is done hydrating. Livewire swaps the DOM after an SPA
     * navigation, which silently discards a value typed into the pre-swap markup, so the
     * value is read back and retyped if it did not stick.
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

    async editCustomer(originalName: string, updates: Partial<SalesCustomerData>) {
        await this.gotoCustomersPage();
        await this.searchList(originalName);
        // await this.openRowActions();
        // await this.clickMenuAction(/Edit/i);
        await this.erpLocators.salesCustomerEditButton.click();

        if (updates.name) {
            await this.erpLocators.salesCustomerNameInput.fill(updates.name);
        }
        if (updates.email) {
            await this.erpLocators.salesCustomerEmailInput.fill(updates.email);
        }

        await this.erpLocators.salesCustomerSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteCustomer(name: string) {
        await this.gotoCustomersPage();
        await this.searchList(name);
        // await this.openRowActions();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesCustomerDeleteButton.click();
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoProductsPage() {
        await this.safeGoto("/admin/sale/products/products");
        await expect(this.page).toHaveURL(/sale\/products\/products/);
        await expect(this.erpLocators.salesProductNewCreateButton).toBeVisible();
        await expect(this.erpLocators.salesProductsTable.first()).toBeVisible();
    }

    async createProduct(product: SalesProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.salesProductNewCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.fillWhenReady(this.erpLocators.salesProductNameInput, product.name);
        await this.fillWhenReady(this.erpLocators.salesProductPriceInput, product.price);

        if (product.invoicePolicy) {
            // invoice_policy renders as a native <select>; pick by its option value.
            await this.erpLocators.salesProductInvoicePolicySelect.selectOption(product.invoicePolicy);
        }

        // Products default to storable goods tracked "By Quantity"; only touch Track By
        // when a lot/serial product is wanted.
        if (product.tracking && product.tracking !== "qty") {
            await expect(this.erpLocators.salesProductTrackingSelect).toBeVisible();
            await this.erpLocators.salesProductTrackingSelect.selectOption(product.tracking);
            await this.page.waitForTimeout(300);
        }

        await this.erpLocators.salesProductCreateButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/products\/create/);
    }

    async editProduct(originalName: string, updates: Partial<SalesProductData>) {
        await this.gotoProductsPage();
        await this.searchList(originalName);
        await this.openRowActions();
        await this.erpLocators.salesProductEditButton.click();
        // await this.clickMenuAction(/Edit/i);

        if (updates.name) {
            await this.erpLocators.salesProductNameInput.fill(updates.name);
        }
        if (updates.price) {
            await this.erpLocators.salesProductPriceInput.fill(updates.price);
        }

        await this.erpLocators.salesProductSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteProduct(name: string) {
        await this.gotoProductsPage();
        await this.searchList(name);
        await this.openRowActions();
        await this.erpLocators.salesProductDeleteButton.click();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoQuotationsPage() {
        await this.safeGoto("/admin/sale/orders/quotations");
        await expect(this.page).toHaveURL(/sale\/orders\/quotations/);
        await expect(this.erpLocators.salesQuotationCreateButton).toBeVisible();
        await expect(this.erpLocators.salesProductsTable.first()).toBeVisible();
    }

    async createQuotation(quotation: SalesQuotationData) {
        await this.gotoQuotationsPage();
        await this.erpLocators.salesQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(this.erpLocators.salesQuotationCustomerSelect, quotation.customerName);
        await this.selectFirstOption(this.erpLocators.salesQuotationPaymentTermSelect);

        await this.erpLocators.salesQuotationAddProductButton.scrollIntoViewIfNeeded();
        await this.erpLocators.salesQuotationAddProductButton.click();
        await this.selectBySearch(this.erpLocators.salesQuotationProductSelectInput.first(), quotation.productName);
        await this.erpLocators.salesQuotationQuantityInput.first().fill(quotation.quantity);

        await this.erpLocators.salesQuotationSaveButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/quotations\/create/);
    }

    /**
     * Create a quotation with any number of product lines, optionally shipped from a
     * non-default warehouse. The warehouse is picked before the lines are added so each
     * line inherits it, which is what drives the delivery route (1/2/3-step).
     */
    async createOrderWithLines(order: SalesOrderData) {
        const l = this.erpLocators;

        await this.gotoQuotationsPage();
        await l.salesQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(l.salesQuotationCustomerSelect, order.customerName);
        await this.selectFirstOption(l.salesQuotationPaymentTermSelect);

        if (order.warehouseName) {
            await l.salesQuotationOtherInformationTab.click();
            await expect(l.salesQuotationWarehouseSelect).toBeVisible();
            await this.selectBySearch(l.salesQuotationWarehouseSelect, order.warehouseName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await l.salesQuotationOrderLineTab.click();
        }

        for (let index = 0; index < order.lines.length; index++) {
            const line = order.lines[index];

            await l.salesQuotationAddProductButton.scrollIntoViewIfNeeded();
            await l.salesQuotationAddProductButton.click();
            await this.selectBySearch(l.salesQuotationProductSelectInput.nth(index), line.productName);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            await l.salesQuotationQuantityInput.nth(index).fill(line.quantity);

            // The quantity recomputes the line on blur; let that round-trip finish before
            // touching the tax field, or its request overlaps and the submit stays disabled.
            await this.blurAndSettle();

            if (line.taxName) {
                await this.selectLineTax(index, line.taxName);
            }
        }

        await this.submitCreateForm();
    }

    /**
     * Submit the create form. Filament disables the submit button while a Livewire request
     * is in flight — a line recompute, say — and a click that lands in that window is
     * swallowed, so the submit is retried until the create page is actually left behind.
     */
    private async submitCreateForm() {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.clickWhenEnabled(this.erpLocators.salesQuotationSaveButton);
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
     * Add a tax to a line. The taxes field is a multi-select, so its panel stays open
     * after a pick; it is dismissed explicitly, and the line and order totals are only
     * recomputed once that Livewire round-trip lands.
     */
    async selectLineTax(lineIndex: number, taxName: string) {
        const l = this.erpLocators;

        await l.salesQuotationLineTaxSelects.nth(lineIndex).click();

        const option = l.salesSelectOption.filter({ hasText: new RegExp(this.escapeRegExp(taxName), "i") }).first();
        await expect(option).toBeVisible();
        await option.click();

        await this.page.keyboard.press("Escape");
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1500);
    }

    /**
     * Assert the order summary. `tax` is omitted for an untaxed order, where the app
     * renders no "Amount Tax" row at all.
     */
    async expectOrderTotals(totals: OrderTotals) {
        const items = this.erpLocators.salesQuotationSummaryItems;
        const taxRow = items.filter({ hasText: /Amount Tax/ });

        await expect(items.filter({ hasText: /Untaxed Amount/ }).first()).toContainText(totals.untaxed);

        if (totals.tax) {
            await expect(taxRow.first()).toContainText(totals.tax);
        } else {
            await expect(taxRow).toHaveCount(0);
        }

        await expect(items.filter({ hasText: /Amount Total/ }).first()).toContainText(totals.total);
    }

    async expectLineSubtotal(lineIndex: number, subtotal: string) {
        const input = this.erpLocators.salesQuotationLineSubtotalInputs.nth(lineIndex);
        await expect(input).toHaveValue(new RegExp(`^${subtotal}(\\.0+)?$`));
    }

    async expectLineQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.salesQuotationQuantityInput.nth(lineIndex);
        await expect(input).toHaveValue(new RegExp(`^${quantity}(\\.0+)?$`));
    }

    /**
     * Retype a line's ordered quantity. The field recomputes on blur, so the click away
     * is what triggers the Livewire round-trip.
     */
    async updateLineQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.salesQuotationQuantityInput.nth(lineIndex);
        await expect(input).toBeEnabled();
        await input.fill(quantity);
        await input.blur();
        await this.blurAndSettle();
    }

    /**
     * Append a product line to the order that is already open, without saving.
     */
    async addLineToOpenOrder(productName: string, quantity: string) {
        const l = this.erpLocators;
        const existingLines = await l.salesQuotationProductSelectInput.count();

        await l.salesQuotationAddProductButton.scrollIntoViewIfNeeded();
        await l.salesQuotationAddProductButton.click();
        await this.selectBySearch(l.salesQuotationProductSelectInput.nth(existingLines), productName);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await l.salesQuotationQuantityInput.nth(existingLines).fill(quantity);

        // The quantity recomputes the line on blur; let that round-trip finish, or the
        // save that follows lands while the form is busy and is dropped.
        await this.blurAndSettle();
    }

    async saveOrder() {
        await this.submitForm(this.erpLocators.salesQuotationSaveButton);
    }

    /**
     * Let the form's in-flight Livewire recompute finish. Filament keeps submit buttons
     * disabled while one is running, so anything that follows must wait it out.
     */
    private async blurAndSettle() {
        await this.page.keyboard.press("Tab");
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1200);
    }

    /**
     * Click a submit button once Filament re-enables it. It is disabled for the duration
     * of an in-flight Livewire request, and occasionally stays that way, so the wait is
     * bounded and the click is forced rather than letting the whole test time out.
     */
    /**
     * Click the submit exactly once. Playwright re-tries a click whose element moves or is
     * detached mid-dispatch, and Livewire re-renders the button as the form settles — on a
     * slow machine that retry lands a second click, the form is saved twice, and the line
     * added to a confirmed order is applied to its transfer twice (demand 2 becomes 4).
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
            await this.page.waitForTimeout(1000);
        }

        await button.click({ force: true, timeout: 15000 }).catch(() => undefined);
    }

    /**
     * Submit a form and make sure the request actually left the browser. A click that
     * lands while Livewire is mid-request is swallowed: the button is disabled for that
     * instant and nothing is saved, which on a loaded CI machine silently drops an added
     * order line. Retry until the submit is seen on the wire.
     */
    private async submitForm(button: ReturnType<Page["locator"]>) {
        // The submit must not be retried: a save that is merely slow is still on its way to
        // the server, and clicking again saves the form a second time — the line added to a
        // confirmed order is then applied to the delivery twice (demand 2 becomes 4). Click
        // once, and give the save as long as it needs.
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
     * Assert the order refuses a quantity below what has already been delivered.
     */
    async expectQuantityBelowDeliveredError(deliveredQuantity: string) {
        await expect(this.erpLocators.salesValidationMessage.first()).toContainText(
            new RegExp(`cannot reduce the quantity below the delivered quantity \\(${deliveredQuantity}`, "i"),
        );
    }

    /**
     * Assert the sale order's Delivered column for a line, which only moves once the
     * transfer that reaches the customer location is validated.
     */
    async expectDeliveredQuantity(lineIndex: number, quantity: string) {
        const input = this.erpLocators.salesQuotationDeliveredQuantityInputs.nth(lineIndex);
        await expect(input).toBeVisible();
        await expect(input).toHaveValue(new RegExp(`^${quantity}(\\.0+)?$`));
    }

    async editQuotationQuantity(searchKey: string, quantity: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        // await this.clickMenuAction(/Edit/i);
        await this.erpLocators.salesQuotationEditButton.click();
        await this.erpLocators.salesQuotationQuantityInput.first().fill(quantity);
        await this.erpLocators.salesQuotationSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteQuotation(searchKey: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.erpLocators.salesQuotationDeleteButton.click();
        // await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Confirm the open quotation. Confirming redirects the record onto the Order resource,
     * so the outcome is read from that landing page rather than from the success toast.
     */
    async confirmQuotation() {
        await this.erpLocators.salesQuotationConfirmButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).toHaveURL(/\/orders\/\d+/);
        await expect(this.erpLocators.salesQuotationConfirmButton).toHaveCount(0);
    }

    /**
     * Invoice the order. Submitting the dialog redirects, which can tear the success
     * toast down before it can be observed, so the outcome is read off the order itself:
     * with nothing left to invoice the Create Invoice action is gone.
     */
    async createInvoice() {
        await expect(this.erpLocators.salesQuotationCreateInvoiceButton).toBeVisible();
        await this.erpLocators.salesQuotationCreateInvoiceButton.click();
        await expect(this.erpLocators.salesQuotationInvoiceSubmitButton).toBeVisible();
        await this.erpLocators.salesQuotationInvoiceSubmitButton.click();

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.erpLocators.salesQuotationCreateInvoiceButton).toHaveCount(0);
    }

    // With the "Ordered Quantities" policy the Create Invoice button is shown as soon as the
    // order is confirmed.
    async expectCreateInvoiceButtonVisible() {
        await expect(this.erpLocators.salesQuotationCreateInvoiceButton).toBeVisible();
    }

    // With the "Delivered Quantities" policy the Create Invoice action is hidden until there
    // are delivered quantities to invoice, so the button is absent from the DOM.
    async expectCreateInvoiceButtonHidden() {
        await expect(this.erpLocators.salesQuotationCreateInvoiceButton).toHaveCount(0);
    }

    async sendQuotation() {
        await expect(this.erpLocators.salesQuotationSendButton).toBeVisible();
        await this.erpLocators.salesQuotationSendButton.click();
        // await expect(this.erpLocators.salesQuotationSendSubmitButton).toBeVisible();
        await this.page.waitForLoadState("networkidle");
        await this.erpLocators.salesQuotationSendSubmitButton.click();
        await this.expectSuccessToast();
    }

    // After confirmation a quotation becomes a sales order, so the record can live
    // under either the "quotations" or the "orders" resource slug.
    currentRecordRef(): { resource: string; id: string } {
        const url = this.page.url();
        const match = url.match(/\/(quotations|orders)\/(\d+)/);

        if (!match) {
            throw new Error(`Unable to determine quotation/order id from URL: ${url}`);
        }

        return { resource: match[1], id: match[2] };
    }

    async gotoOrderEdit(ref: { resource: string; id: string }) {
        await this.safeGoto(`/admin/sale/orders/${ref.resource}/${ref.id}/edit`);
        await this.page.waitForLoadState("networkidle");
    }

    async openInvoicesForCurrentQuotation(): Promise<string> {
        const { resource, id } = this.currentRecordRef();
        await this.safeGoto(`/admin/sale/orders/${resource}/${id}/invoices`);
        await expect(this.page).toHaveURL(new RegExp(`/${resource}/${id}/invoices`));
        await expect(this.erpLocators.salesInvoicesTable.first()).toBeVisible();

        return id;
    }

    async openDeliveriesForCurrentQuotation(): Promise<string> {
        const { resource, id } = this.currentRecordRef();
        await this.page.waitForLoadState("networkidle");
        await this.safeGoto(`/admin/sale/orders/${resource}/${id}/deliveries`);
        await expect(this.page).toHaveURL(new RegExp(`/${resource}/${id}/deliveries`));
        await expect(this.erpLocators.salesQuotationDeliveriesTable.first()).toBeVisible();

        return id;
    }

    /**
     * Read every operation listed on the sale order's Deliveries tab. A multi-step
     * warehouse links its whole Pick/Pack/Ship chain to the order, so this returns
     * one entry per transfer.
     */
    async readDeliveryRows(): Promise<Array<{ id: string; reference: string; state: string }>> {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        const rows = this.erpLocators.salesQuotationDeliveryRows;
        const total = await rows.count();
        const entries: Array<{ id: string; reference: string; state: string }> = [];

        for (let index = 0; index < total; index++) {
            const row = rows.nth(index);
            const link = row.locator('a[href*="/deliveries/"]').first();

            if (!(await link.count())) {
                continue;
            }

            const href = (await link.getAttribute("href")) ?? "";
            const id = href.match(/\/deliveries\/(\d+)/)?.[1];

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

    async expectDeliveryCount(count: number) {
        await this.openDeliveriesForCurrentQuotation();
        const rows = await this.readDeliveryRows();
        expect(rows).toHaveLength(count);
    }

    /**
     * Assert the operation whose reference matches `reference` shows `state` on the
     * order's Deliveries tab (Draft / Waiting / Ready / Done).
     */
    async expectDeliveryState(reference: string, state: string) {
        await this.openDeliveriesForCurrentQuotation();
        const row = this.erpLocators.salesQuotationDeliveryRows
            .filter({ hasText: new RegExp(this.escapeRegExp(reference)) })
            .first();
        await expect(row).toBeVisible();
        await expect(row).toContainText(new RegExp(state, "i"));
    }

    /**
     * Open a delivery of the current order on its edit page, where the Mark as Todo /
     * Check Availability / Validate / Return header actions live.
     */
    async openDeliveryEditPage(deliveryId: string) {
        const { resource, id } = this.currentRecordRef();
        await this.safeGoto(`/admin/sale/orders/${resource}/${id}/deliveries/${deliveryId}/edit`);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    /**
     * Open the order's delivery whose reference contains `part` (e.g. "/PICK/", "/OUT/").
     */
    async openDeliveryByReference(part: string): Promise<string> {
        await this.openDeliveriesForCurrentQuotation();
        const rows = await this.readDeliveryRows();
        const match = rows.find((row) => row.reference.includes(part));

        if (!match) {
            throw new Error(`No delivery matching "${part}". Found: ${rows.map((r) => r.reference).join(", ")}`);
        }

        await this.openDeliveryEditPage(match.id);

        return match.reference;
    }

    /**
     * Open the order's first transfer that has not been validated yet. Used after a back
     * order is created, where the remaining transfer cannot be addressed by row index —
     * the deliveries table does not list the transfers in creation order.
     */
    async openPendingDelivery(): Promise<string> {
        await this.openDeliveriesForCurrentQuotation();
        const rows = await this.readDeliveryRows();
        const pending = rows.find((row) => !/\bDone\b/i.test(row.state));

        if (!pending) {
            throw new Error(`Every transfer of this order is already Done: ${rows.map((r) => r.reference).join(", ")}`);
        }

        await this.openDeliveryEditPage(pending.id);

        return pending.reference;
    }

    async openDeliveryByIndex(index = 0): Promise<string> {
        await this.openDeliveriesForCurrentQuotation();
        const rows = await this.readDeliveryRows();

        if (!rows[index]) {
            throw new Error(`No delivery at index ${index}; the order has ${rows.length} transfers.`);
        }

        await this.openDeliveryEditPage(rows[index].id);

        return rows[index].reference;
    }

    /**
     * Assert the currently-open operation reached Done. Validate raises no notification,
     * so the transition is read off the header actions: Validate disappears and the
     * Return action — only offered on a validated transfer — appears.
     */
    async expectOpenDeliveryDone() {
        await expect(this.erpLocators.salesDeliveryValidateButton).toBeHidden();
        await expect(this.erpLocators.salesDeliveryReturnButton).toBeVisible();
    }

    /**
     * Drive the currently-open delivery to Done. Sale-order deliveries are already
     * confirmed and reserved at confirm, so Mark as Todo is normally absent; the
     * backorder prompt is declined when the transfer is short.
     */
    async validateOpenDelivery() {
        const l = this.erpLocators;

        if (await l.salesDeliveryMarkAsTodoButton.isVisible().catch(() => false)) {
            await l.salesDeliveryMarkAsTodoButton.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
        }

        if (await l.salesDeliveryCheckAvailabilityButton.isVisible().catch(() => false)) {
            await l.salesDeliveryCheckAvailabilityButton.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
        }

        await expect(l.salesDeliveryValidateButton).toBeVisible();
        await l.salesDeliveryValidateButton.click();
        await this.page.waitForTimeout(800);

        if (await l.salesDeliveryNoBackorderButton.isVisible().catch(() => false)) {
            await l.salesDeliveryNoBackorderButton.click();
        }

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(600);
        await this.expectOpenDeliveryDone();
    }

    /**
     * Validate the open delivery short of its demand and keep the remainder: the
     * "Create Back Order?" prompt is confirmed, so a second transfer is created for
     * the undelivered quantity.
     */
    async validateOpenDeliveryCreatingBackorder() {
        const l = this.erpLocators;

        await expect(l.salesDeliveryValidateButton).toBeVisible();
        await l.salesDeliveryValidateButton.click();

        await expect(l.salesDeliveryBackorderModal).toBeVisible({ timeout: 20000 });
        await l.salesDeliveryBackorderConfirmButton.click();

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
        await this.expectOpenDeliveryDone();
    }

    /**
     * Walk the whole transfer chain of a multi-step warehouse (Pick -> Pack -> Ship),
     * validating each transfer and following the "Next Transfer" header action, which
     * only appears while a downstream transfer is still open.
     */
    async validateDeliveryChain(maxSteps = 3) {
        for (let step = 0; step < maxSteps; step++) {
            await this.validateOpenDelivery();

            await this.page.reload().catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (!(await this.erpLocators.salesDeliveryNextTransferButton.isVisible().catch(() => false))) {
                return;
            }

            await this.erpLocators.salesDeliveryNextTransferButton.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
        }
    }

    async validateFirstDeliveryForCurrentQuotation() {
        await this.openDeliveryByIndex(0);
        await this.validateOpenDelivery();
    }

    async expectInvoiceRowPresent() {
        const rows = this.erpLocators.salesInvoicesTable.locator("tbody tr");
        await expect(rows.first()).toBeVisible();
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.salesValidationMessage.first()).toBeVisible();
    }

    async searchList(keyword: string) {
        await this.erpLocators.salesSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async openRowActions() {
        await this.erpLocators.salesRowActionsButton.first().click();
    }

    async clickMenuAction(label: RegExp) {
        const menuItem = this.page.getByRole("menuitem", { name: label }).first();
        if (await menuItem.isVisible()) {
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

    private async selectFirstOption(trigger: ReturnType<Page["locator"]>) {
        await trigger.click();
        await expect(this.erpLocators.salesSelectOption.first()).toBeVisible();
        await this.erpLocators.salesSelectOption.first().click();
    }

    private async selectFirstOptionIfEmpty(trigger: ReturnType<Page["locator"]>) {
        const currentValue = (await trigger.textContent())?.trim() ?? "";

        if (!currentValue || /select an option/i.test(currentValue)) {
            await this.selectFirstOption(trigger);
        }
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\\]\\]/g, "\\$&");
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.salesSuccessToast).toBeVisible();
    }
}
