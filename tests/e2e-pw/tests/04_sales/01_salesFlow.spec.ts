import { test } from "../../setup";
import { SalesFlowPage } from "../../pages/05_salesFlow";

test.describe("Sales Order Flow E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.ensureSalesPluginInstalled();
    });

    test("Quotation Validation - Requires Customer And Product", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoQuotationsPage();
        await salesPage.erpLocators.salesQuotationCreateButton.click();
        await salesPage.erpLocators.salesQuotationSaveButton.click();
        await salesPage.expectValidationErrors();
    });

    test("Sales Flow - Customer To Invoice", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();

        const customerName = `E2E Sales Customer ${key}`;
        const productName = `E2E Sales Product ${key}`;

        await salesPage.createCustomer({
            name: customerName,
            email: `sales.customer+${key}@example.com`,
        });

        await salesPage.createProduct({
            name: productName,
            price: "100",
        });

        await salesPage.createQuotation({
            customerName,
            productName,
            quantity: "1",
        });

        await salesPage.confirmQuotation();
        await salesPage.createInvoice();
        await salesPage.openInvoicesForCurrentQuotation();
        await salesPage.expectInvoiceRowPresent();
    });
});
