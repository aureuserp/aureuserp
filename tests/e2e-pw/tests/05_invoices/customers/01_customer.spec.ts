import { test, withAdminPage } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Invoice Customers E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    test("Customers Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomersPage();
    });

    test("Create Customer - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();

        await invoicePage.createCustomer({
            name: `E2E Invoice Customer ${key}`,
            email: `invoice.customer+${key}@example.com`,
            phone: `90000${String(key).slice(-5)}`,
        });
    });

    test("Edit Customer - Updates Name", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Invoice Customer ${key}`;
        const updatedName = `E2E Invoice Customer Updated ${key}`;

        await invoicePage.createCustomer({
            name: originalName,
            email: `invoice.customer+${key}@example.com`,
        });

        await invoicePage.editCustomer(originalName, { name: updatedName });
    });

    test("Delete Customer - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Invoice Customer ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `invoice.customer+${key}@example.com`,
        });

        await invoicePage.deleteCustomer(customerName);
    });
});
