import { Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type SalesCustomerData = {
    name: string;
    email?: string;
};

export type SalesProductData = {
    name: string;
    price: string;
};

export type SalesQuotationData = {
    customerName: string;
    productName: string;
    quantity: string;
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

    async gotoCustomersPage() {
        await this.page.goto("/admin/sale/orders/customers");
        await expect(this.page).toHaveURL(/sale\/orders\/customers/);
        await expect(this.erpLocators.salesCustomersTable.first()).toBeVisible();
    }

    async createCustomer(customer: SalesCustomerData) {
        await this.gotoCustomersPage();
        await this.erpLocators.salesCustomerCreateButton.click();
        await expect(this.page).toHaveURL(/customers\/create/);

        await this.erpLocators.salesCustomerNameInput.fill(customer.name);
        if (customer.email) {
            await this.erpLocators.salesCustomerEmailInput.fill(customer.email);
        }

        await this.erpLocators.salesCustomerSaveButton.click();
        await this.expectSuccessToast();
    }

    async editCustomer(originalName: string, updates: Partial<SalesCustomerData>) {
        await this.gotoCustomersPage();
        await this.searchList(originalName);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);

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
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoProductsPage() {
        await this.page.goto("/admin/sale/products/products");
        await expect(this.page).toHaveURL(/sale\/products\/products/);
        await expect(this.erpLocators.salesProductsTable.first()).toBeVisible();
    }

    async createProduct(product: SalesProductData) {
        await this.gotoProductsPage();
        await this.erpLocators.salesProductCreateButton.click();
        await expect(this.page).toHaveURL(/products\/create/);

        await this.erpLocators.salesProductNameInput.fill(product.name);
        await this.selectFirstOption(this.erpLocators.salesProductCategorySelect);
        await this.erpLocators.salesProductPriceInput.fill(product.price);
        await this.selectFirstOption(this.erpLocators.salesProductUomSelect);

        await this.erpLocators.salesProductSaveButton.click();
        await this.expectSuccessToast();
    }

    async editProduct(originalName: string, updates: Partial<SalesProductData>) {
        await this.gotoProductsPage();
        await this.searchList(originalName);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);

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
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async gotoQuotationsPage() {
        await this.page.goto("/admin/sale/orders/quotations");
        await expect(this.page).toHaveURL(/sale\/orders\/quotations/);
        await expect(this.erpLocators.salesCustomersTable.first()).toBeVisible();
    }

    async createQuotation(quotation: SalesQuotationData) {
        await this.gotoQuotationsPage();
        await this.erpLocators.salesQuotationCreateButton.click();
        await expect(this.page).toHaveURL(/quotations\/create/);

        await this.selectBySearch(this.erpLocators.salesQuotationCustomerSelect, quotation.customerName);
        await this.selectFirstOptionIfEmpty(this.erpLocators.salesQuotationPaymentTermSelect);

        await this.erpLocators.salesQuotationAddProductButton.scrollIntoViewIfNeeded();
        await this.erpLocators.salesQuotationAddProductButton.click();
        await this.selectBySearch(this.erpLocators.salesQuotationProductSelectInput.first(), quotation.productName);
        await this.erpLocators.salesQuotationQuantityInput.first().fill(quotation.quantity);

        await this.erpLocators.salesQuotationSaveButton.click();
        await this.expectSuccessToast();
    }

    async editQuotationQuantity(searchKey: string, quantity: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);
        await this.erpLocators.salesQuotationQuantityInput.first().fill(quantity);
        await this.erpLocators.salesQuotationSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteQuotation(searchKey: string) {
        await this.gotoQuotationsPage();
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.salesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async confirmQuotation() {
        await this.erpLocators.salesQuotationConfirmButton.click();
        await this.expectSuccessToast();
    }

    async createInvoice() {
        await this.erpLocators.salesQuotationCreateInvoiceButton.click();
        await this.erpLocators.salesQuotationInvoiceSubmitButton.click();
        await this.expectSuccessToast();
    }

    async openInvoicesForCurrentQuotation(): Promise<string> {
        const url = this.page.url();
        const match = url.match(/quotations\/(\d+)/);

        if (!match) {
            throw new Error(`Unable to determine quotation id from URL: ${url}`);
        }

        const quotationId = match[1];
        await this.page.goto(`/admin/sale/orders/quotations/${quotationId}/invoices`);
        await expect(this.page).toHaveURL(new RegExp(`/quotations/${quotationId}/invoices`));
        await expect(this.erpLocators.salesInvoicesTable.first()).toBeVisible();

        return quotationId;
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
        await this.erpLocators.salesSelectSearchInput.fill(value);
        await this.erpLocators.salesSelectOption.filter({ hasText: value }).first().click();
    }

    private async selectFirstOption(trigger: ReturnType<Page["locator"]>) {
        await trigger.click();
        await this.erpLocators.salesSelectOption.first().click();
    }

    private async selectFirstOptionIfEmpty(trigger: ReturnType<Page["locator"]>) {
        const currentValue = await trigger.inputValue().catch(() => "");
        if (!currentValue) {
            await this.selectFirstOption(trigger);
        }
    }

    private async expectSuccessToast() {
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.salesSuccessToast).toBeVisible();
    }
}
