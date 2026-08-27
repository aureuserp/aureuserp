import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Customer Invoices E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Invoices Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomerInvoicesPage();
    });

    test("Create Invoice - Validation Errors (Missing Customer And Product)", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomerInvoicesPage();
        await invoicePage.erpLocators.invoiceCreateButton.click();
        await invoicePage.erpLocators.invoiceSaveButton.click();
        await invoicePage.expectValidationErrors();
    });

    test("Create Invoice - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Invoice Customer ${key}`;
        const productName = `E2E Invoice Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `invoice.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "150",
        });

        await invoicePage.createCustomerInvoice({
            partnerName: customerName,
            productName,
            quantity: "2",
        });
    });

    test("Edit Invoice - Updates Quantity", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Invoice Customer ${key}`;
        const productName = `E2E Invoice Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `invoice.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "180",
        });

        await invoicePage.createCustomerInvoice({
            partnerName: customerName,
            productName,
            quantity: "1",
        });

        await invoicePage.editCustomerInvoiceQuantity(customerName, "4");
    });

    test("Delete Invoice - Removes Draft", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Invoice Customer ${key}`;
        const productName = `E2E Invoice Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `invoice.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "160",
        });

        await invoicePage.createCustomerInvoice({
            partnerName: customerName,
            productName,
            quantity: "1",
        });

        await invoicePage.deleteCustomerInvoice(customerName);
    });
});
