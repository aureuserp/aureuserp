import { test, withAdminPage } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Invoice Vendor Products E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    test("Products Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorProductsPage();
    });

    test("Create Product - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();

        await invoicePage.createVendorProduct({
            name: `E2E Vendor Product ${key}`,
            price: "145",
        });
    });

    test("Edit Product - Updates Name", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Vendor Product ${key}`;
        const updatedName = `E2E Vendor Product Updated ${key}`;

        await invoicePage.createVendorProduct({
            name: originalName,
            price: "95",
        });

        await invoicePage.editVendorProduct(originalName, {
            name: updatedName,
            price: "105",
        });
    });

    test("Delete Product - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Vendor Product ${key}`;

        await invoicePage.createVendorProduct({
            name: productName,
            price: "60",
        });

        await invoicePage.deleteVendorProduct(productName);
    });
});
