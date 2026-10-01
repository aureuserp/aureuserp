import { test, withAdminPage } from "../../setup";
import { InvoiceManagementPage } from "../../pages/06_invoiceManagement";

test.describe("Invoice Settings E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    test("Settings - Manage Products page loads", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoInvoiceSettingsPage();
    });

    test("Settings - Unit of Measure can be toggled and restored", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.toggleUomSettingAndRestore();
    });
});
