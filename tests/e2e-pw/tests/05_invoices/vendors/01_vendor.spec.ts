import { test, withAdminPage } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Invoice Vendors E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    test("Vendors Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorsPage();
    });

    test("Create Vendor - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();

        await invoicePage.createVendor({
            name: `E2E Invoice Vendor ${key}`,
            email: `invoice.vendor+${key}@example.com`,
            phone: `91000${String(key).slice(-5)}`,
        });
    });

    test("Edit Vendor - Updates Name", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Invoice Vendor ${key}`;
        const updatedName = `E2E Invoice Vendor Updated ${key}`;

        await invoicePage.createVendor({
            name: originalName,
            email: `invoice.vendor+${key}@example.com`,
        });

        await invoicePage.editVendor(originalName, { name: updatedName });
    });

    test("Delete Vendor - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Invoice Vendor ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `invoice.vendor+${key}@example.com`,
        });

        await invoicePage.deleteVendor(vendorName);
    });
});
