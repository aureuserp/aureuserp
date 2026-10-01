import { test, withAdminPage } from "../../setup";
import {
    invoiceConfigurationResources,
    InvoiceManagementPage,
} from "../../pages/06_invoiceManagement";

test.describe("Invoice Configuration E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.ensureInvoicePluginInstalled();
        });
    });

    for (const resource of invoiceConfigurationResources) {
        test(`Configuration - ${resource.label} listing loads`, async ({ adminPage }) => {
            const invoicePage = new InvoiceManagementPage(adminPage);
            await invoicePage.gotoGenericInvoiceListPage(resource.path);
        });
    }
});
