import { expect, Page } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export const invoiceConfigurationResources = [
    { label: "Bank Accounts", path: "/admin/invoices/configurations/bank-accounts" },
    { label: "Incoterms", path: "/admin/invoices/configurations/incoterms" },
    { label: "Payment Terms", path: "/admin/invoices/configurations/payment-terms" },
    { label: "Product Categories", path: "/admin/invoices/configurations/product-categories" },
    { label: "Product Attributes", path: "/admin/invoices/configurations/product-attributes" },
    { label: "Currencies", path: "/admin/invoices/configurations/currencies" },
    { label: "Tax Groups", path: "/admin/invoices/configurations/tax-groups" },
    { label: "Taxes", path: "/admin/invoices/configurations/taxes" },
] as const;

type PartnerData = {
    name: string;
    email?: string;
    phone?: string;
};

type ProductData = {
    name: string;
    price: string;
};

type DocumentData = {
    partnerName: string;
    productName: string;
    quantity: string;
    unitPrice?: string;
};

type PaymentData = {
    partnerName: string;
    amount: string;
    memo?: string;
    paymentType: "receive" | "send";
};

export class InvoiceManagementPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    private readonly customerRoutes = {
        customers: "/admin/invoices/customers/customers",
        products: "/admin/invoices/customers/products",
        invoices: "/admin/invoices/customers/invoices",
        creditNotes: "/admin/invoices/customers/credit-notes",
        payments: "/admin/invoices/customers/payments",
    } as const;

    private readonly vendorRoutes = {
        vendors: "/admin/invoices/vendors/vendors",
        products: "/admin/invoices/vendors/products",
        bills: "/admin/invoices/vendors/bills",
        refunds: "/admin/invoices/vendors/refunds",
        payments: "/admin/invoices/vendors/payments",
    } as const;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    async ensureInvoicePluginInstalled(): Promise<void> {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Invoices");
    }

    async gotoGenericInvoiceListPage(path: string): Promise<void> {
        await this.gotoListPage(path);
    }

    async gotoInvoiceSettingsPage(): Promise<void> {
        await this.page.goto("/admin/settings/products");
        await expect(this.page).toHaveURL(/admin\/settings\/products/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.invoiceSettingsUomToggle).toBeVisible();
    }

    async toggleUomSettingAndRestore(): Promise<void> {
        await this.gotoInvoiceSettingsPage();

        const initialState = await this.readToggleState(this.erpLocators.invoiceSettingsUomToggle);
        await this.erpLocators.invoiceSettingsUomToggle.click();
        await this.erpLocators.invoiceSettingsSaveButton.click();
        await this.expectSuccessToast();

        await this.gotoInvoiceSettingsPage();
        await expect
            .poll(async () => this.readToggleState(this.erpLocators.invoiceSettingsUomToggle))
            .toBe(! initialState);

        await this.erpLocators.invoiceSettingsUomToggle.click();
        await this.erpLocators.invoiceSettingsSaveButton.click();
        await this.expectSuccessToast();

        await this.gotoInvoiceSettingsPage();
        await expect
            .poll(async () => this.readToggleState(this.erpLocators.invoiceSettingsUomToggle))
            .toBe(initialState);
    }

    async gotoCustomersPage(): Promise<void> {
        await this.gotoListPage(this.customerRoutes.customers);
    }

    async createCustomer(customer: PartnerData): Promise<void> {
        await this.createPartner(this.customerRoutes.customers, customer);
    }

    async editCustomer(searchKey: string, updates: Partial<PartnerData>): Promise<void> {
        await this.editPartner(this.customerRoutes.customers, searchKey, updates);
    }

    async deleteCustomer(searchKey: string): Promise<void> {
        await this.deletePartner(this.customerRoutes.customers, searchKey);
    }

    async gotoCustomerProductsPage(): Promise<void> {
        await this.gotoListPage(this.customerRoutes.products);
    }

    async createCustomerProduct(product: ProductData): Promise<void> {
        await this.createProduct(this.customerRoutes.products, product);
    }

    async editCustomerProduct(searchKey: string, updates: Partial<ProductData>): Promise<void> {
        await this.editProduct(this.customerRoutes.products, searchKey, updates);
    }

    async deleteCustomerProduct(searchKey: string): Promise<void> {
        await this.deleteProduct(this.customerRoutes.products, searchKey);
    }

    async gotoCustomerInvoicesPage(): Promise<void> {
        await this.gotoListPage(this.customerRoutes.invoices);
    }

    async createCustomerInvoice(document: DocumentData): Promise<void> {
        await this.createDocument(this.customerRoutes.invoices, document);
    }

    async editCustomerInvoiceQuantity(searchKey: string, quantity: string): Promise<void> {
        await this.editDocumentQuantity(this.customerRoutes.invoices, searchKey, quantity);
    }

    async deleteCustomerInvoice(searchKey: string): Promise<void> {
        await this.deleteDocument(this.customerRoutes.invoices, searchKey);
    }

    async gotoCustomerCreditNotesPage(): Promise<void> {
        await this.gotoListPage(this.customerRoutes.creditNotes);
    }

    async createCustomerCreditNote(document: DocumentData): Promise<void> {
        await this.createDocument(this.customerRoutes.creditNotes, document);
    }

    async editCustomerCreditNoteQuantity(searchKey: string, quantity: string): Promise<void> {
        await this.editDocumentQuantity(this.customerRoutes.creditNotes, searchKey, quantity);
    }

    async deleteCustomerCreditNote(searchKey: string): Promise<void> {
        await this.deleteDocument(this.customerRoutes.creditNotes, searchKey);
    }

    async gotoCustomerPaymentsPage(): Promise<void> {
        await this.gotoListPage(this.customerRoutes.payments);
    }

    async createCustomerPayment(payment: Omit<PaymentData, "paymentType">): Promise<void> {
        await this.createPayment(this.customerRoutes.payments, { ...payment, paymentType: "receive" });
    }

    async editCustomerPayment(searchKey: string, updates: Partial<Omit<PaymentData, "paymentType">>): Promise<void> {
        await this.editPayment(this.customerRoutes.payments, searchKey, updates);
    }

    async deleteCustomerPayment(searchKey: string): Promise<void> {
        await this.deletePayment(this.customerRoutes.payments, searchKey);
    }

    async gotoVendorsPage(): Promise<void> {
        await this.gotoListPage(this.vendorRoutes.vendors);
    }

    async createVendor(vendor: PartnerData): Promise<void> {
        await this.createPartner(this.vendorRoutes.vendors, vendor);
    }

    async editVendor(searchKey: string, updates: Partial<PartnerData>): Promise<void> {
        await this.editPartner(this.vendorRoutes.vendors, searchKey, updates);
    }

    async deleteVendor(searchKey: string): Promise<void> {
        await this.deletePartner(this.vendorRoutes.vendors, searchKey);
    }

    async gotoVendorProductsPage(): Promise<void> {
        await this.gotoListPage(this.vendorRoutes.products);
    }

    async createVendorProduct(product: ProductData): Promise<void> {
        await this.createProduct(this.vendorRoutes.products, product);
    }

    async editVendorProduct(searchKey: string, updates: Partial<ProductData>): Promise<void> {
        await this.editProduct(this.vendorRoutes.products, searchKey, updates);
    }

    async deleteVendorProduct(searchKey: string): Promise<void> {
        await this.deleteProduct(this.vendorRoutes.products, searchKey);
    }

    async gotoVendorBillsPage(): Promise<void> {
        await this.gotoListPage(this.vendorRoutes.bills);
    }

    async createVendorBill(document: DocumentData): Promise<void> {
        await this.createDocument(this.vendorRoutes.bills, document);
    }

    async editVendorBillQuantity(searchKey: string, quantity: string): Promise<void> {
        await this.editDocumentQuantity(this.vendorRoutes.bills, searchKey, quantity);
    }

    async deleteVendorBill(searchKey: string): Promise<void> {
        await this.deleteDocument(this.vendorRoutes.bills, searchKey);
    }

    async gotoVendorRefundsPage(): Promise<void> {
        await this.gotoListPage(this.vendorRoutes.refunds);
    }

    async createVendorRefund(document: DocumentData): Promise<void> {
        await this.createDocument(this.vendorRoutes.refunds, document);
    }

    async editVendorRefundQuantity(searchKey: string, quantity: string): Promise<void> {
        await this.editDocumentQuantity(this.vendorRoutes.refunds, searchKey, quantity);
    }

    async deleteVendorRefund(searchKey: string): Promise<void> {
        await this.deleteDocument(this.vendorRoutes.refunds, searchKey);
    }

    async gotoVendorPaymentsPage(): Promise<void> {
        await this.gotoListPage(this.vendorRoutes.payments);
    }

    async createVendorPayment(payment: Omit<PaymentData, "paymentType">): Promise<void> {
        await this.createPayment(this.vendorRoutes.payments, { ...payment, paymentType: "send" });
    }

    async editVendorPayment(searchKey: string, updates: Partial<Omit<PaymentData, "paymentType">>): Promise<void> {
        await this.editPayment(this.vendorRoutes.payments, searchKey, updates);
    }

    async deleteVendorPayment(searchKey: string): Promise<void> {
        await this.deletePayment(this.vendorRoutes.payments, searchKey);
    }

    async expectValidationErrors(): Promise<void> {
        await expect(this.erpLocators.invoiceValidationMessage.first()).toBeVisible();
    }

    async searchList(keyword: string): Promise<void> {
        await this.erpLocators.invoiceSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async openRowActions(): Promise<void> {
        await this.erpLocators.invoiceRowActionsButton.first().click();
    }

    async clickMenuAction(label: RegExp): Promise<void> {
        const menuItem = this.page.getByRole("menuitem", { name: label }).first();

        if (await menuItem.isVisible().catch(() => false)) {
            await menuItem.click();

            return;
        }

        const fallback = this.page
            .locator("a.fi-ac-btn-action, button.fi-ac-btn-action, a, button")
            .filter({ hasText: label })
            .first();

        await fallback.click();
    }

    async selectBySearch(trigger: ReturnType<Page["locator"]>, value: string): Promise<void> {
        await trigger.click();
        await expect(this.erpLocators.invoiceSelectSearchInput).toBeVisible();
        await this.erpLocators.invoiceSelectSearchInput.fill(value);

        const option = this.erpLocators.invoiceSelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(option).toBeVisible();
        await option.click();
    }

    private async createPartner(listPath: string, partner: PartnerData): Promise<void> {
        await this.gotoListPage(listPath);
        await this.openCreateForm();

        await this.erpLocators.invoicePartnerNameInput.fill(partner.name);
        if (partner.email) {
            await this.erpLocators.invoicePartnerEmailInput.fill(partner.email);
        }
        if (partner.phone) {
            await this.erpLocators.invoicePartnerPhoneInput.fill(partner.phone);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async editPartner(listPath: string, searchKey: string, updates: Partial<PartnerData>): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);

        if (updates.name) {
            await this.erpLocators.invoicePartnerNameInput.fill(updates.name);
        }
        if (updates.email) {
            await this.erpLocators.invoicePartnerEmailInput.fill(updates.email);
        }
        if (updates.phone) {
            await this.erpLocators.invoicePartnerPhoneInput.fill(updates.phone);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async deletePartner(listPath: string, searchKey: string): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.invoiceConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    private async createProduct(listPath: string, product: ProductData): Promise<void> {
        await this.gotoListPage(listPath);
        await this.openCreateForm();

        await this.erpLocators.invoiceProductNameInput.fill(product.name);
        await this.erpLocators.invoiceProductPriceInput.fill(product.price);

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async editProduct(listPath: string, searchKey: string, updates: Partial<ProductData>): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);

        if (updates.name) {
            await this.erpLocators.invoiceProductNameInput.fill(updates.name);
        }
        if (updates.price) {
            await this.erpLocators.invoiceProductPriceInput.fill(updates.price);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async deleteProduct(listPath: string, searchKey: string): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.invoiceConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    private async createDocument(listPath: string, document: DocumentData): Promise<void> {
        await this.gotoListPage(listPath);
        await this.openCreateForm();

        await this.selectBySearch(this.erpLocators.invoiceDocumentPartnerSelect, document.partnerName);
        await this.selectFirstOptionIfEmpty(this.erpLocators.invoiceDocumentJournalSelect);
        await this.selectFirstOptionIfEmpty(this.erpLocators.invoiceDocumentCurrencySelect);

        await this.erpLocators.invoiceDocumentAddProductButton.scrollIntoViewIfNeeded();
        await this.erpLocators.invoiceDocumentAddProductButton.click();
        await this.selectBySearch(this.erpLocators.invoiceDocumentProductSelect, document.productName);
        await this.erpLocators.invoiceDocumentQuantityInput.fill(document.quantity);

        if (document.unitPrice) {
            await this.erpLocators.invoiceDocumentUnitPriceInput.fill(document.unitPrice);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async editDocumentQuantity(listPath: string, searchKey: string, quantity: string): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);
        await this.erpLocators.invoiceDocumentQuantityInput.fill(quantity);
        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async deleteDocument(listPath: string, searchKey: string): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.invoiceConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    private async createPayment(listPath: string, payment: PaymentData): Promise<void> {
        await this.gotoListPage(listPath);
        await this.openCreateForm();

        if (payment.paymentType === "send") {
            await this.erpLocators.invoicePaymentSendToggle.click();
        } else {
            await this.erpLocators.invoicePaymentReceiveToggle.click();
        }

        await this.selectBySearch(this.erpLocators.invoicePaymentPartnerSelect, payment.partnerName);
        await this.selectFirstOptionIfEmpty(this.erpLocators.invoicePaymentJournalSelect);
        await this.selectFirstOptionIfEmpty(this.erpLocators.invoicePaymentCurrencySelect);
        await this.erpLocators.invoicePaymentAmountInput.fill(payment.amount);

        if (payment.memo) {
            await this.erpLocators.invoicePaymentMemoInput.fill(payment.memo);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async editPayment(
        listPath: string,
        searchKey: string,
        updates: Partial<Omit<PaymentData, "paymentType">>,
    ): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Edit/i);

        if (updates.amount) {
            await this.erpLocators.invoicePaymentAmountInput.fill(updates.amount);
        }
        if (updates.memo) {
            await this.erpLocators.invoicePaymentMemoInput.fill(updates.memo);
        }

        await this.erpLocators.invoiceSaveButton.click();
        await this.expectSuccessToast();
    }

    private async deletePayment(listPath: string, searchKey: string): Promise<void> {
        await this.gotoListPage(listPath);
        await this.searchList(searchKey);
        await this.openRowActions();
        await this.clickMenuAction(/Delete/i);
        await this.erpLocators.invoiceConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    private async gotoListPage(path: string): Promise<void> {
        await this.page.goto(path);
        await expect(this.page).toHaveURL(new RegExp(this.escapeRegExp(path)));
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.invoiceListTable.first()).toBeVisible();
    }

    private async openCreateForm(): Promise<void> {
        await expect(this.erpLocators.invoiceCreateButton).toBeVisible();
        await this.erpLocators.invoiceCreateButton.click();
        await expect(this.page).toHaveURL(/\/create$/);
    }

    private async selectFirstOption(trigger: ReturnType<Page["locator"]>): Promise<void> {
        await trigger.click();
        await expect(this.erpLocators.invoiceSelectOption.first()).toBeVisible();
        await this.erpLocators.invoiceSelectOption.first().click();
    }

    private async selectFirstOptionIfEmpty(trigger: ReturnType<Page["locator"]>): Promise<void> {
        const currentValue = (await trigger.textContent())?.trim() ?? "";

        if (! currentValue || /select an option/i.test(currentValue)) {
            await this.selectFirstOption(trigger);
        }
    }

    private async expectSuccessToast(): Promise<void> {
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.invoiceSuccessToast).toBeVisible();
    }

    private async readToggleState(toggle: ReturnType<Page["locator"]>): Promise<boolean> {
        const tag = await toggle.evaluate((element) => element.tagName.toLowerCase());

        if (tag === "input") {
            return await toggle.isChecked();
        }

        return (await toggle.getAttribute("aria-checked")) !== "false";
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }
}
