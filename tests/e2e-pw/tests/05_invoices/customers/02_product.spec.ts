import { test, withAdminPage } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Invoice Customer Products E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    test("Products Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomerProductsPage();
    });

    test("Create Product - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();

        await invoicePage.createCustomerProduct({
            name: `E2E Invoice Product ${key}`,
            price: "120",
        });
    });

    test("Edit Product - Updates Name", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Invoice Product ${key}`;
        const updatedName = `E2E Invoice Product Updated ${key}`;

        await invoicePage.createCustomerProduct({
            name: originalName,
            price: "75",
        });

        await invoicePage.editCustomerProduct(originalName, {
            name: updatedName,
            price: "90",
        });
    });

    test("Delete Product - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Invoice Product ${key}`;

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "55",
        });

        await invoicePage.deleteCustomerProduct(productName);
    });
});
