import { test } from "../../setup";
import { SalesFlowPage } from "../../pages/05_salesFlow";

test.describe("Sales Quotations E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.ensureSalesPluginInstalled();
    });

    test("Quotations Listing - Loads Table", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoQuotationsPage();
    });

    test("Create Quotation - Valid Inputs", async ({ adminPage }) => {
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
            quantity: "2",
        });
    });

    test("Create Quotation - Validation Errors (Missing Customer)", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoQuotationsPage();
        await salesPage.erpLocators.salesQuotationCreateButton.click();
        await salesPage.erpLocators.salesQuotationSaveButton.click();
        await salesPage.expectValidationErrors();
    });

    test("Create Quotation - Validation Errors (Missing Product)", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Sales Customer ${key}`;

        await salesPage.createCustomer({
            name: customerName,
            email: `sales.customer+${key}@example.com`,
        });

        await salesPage.gotoQuotationsPage();
        await salesPage.erpLocators.salesQuotationCreateButton.click();
        await salesPage.selectBySearch(salesPage.erpLocators.salesQuotationCustomerSelect, customerName);
        await salesPage.erpLocators.salesQuotationSaveButton.click();
        await salesPage.expectValidationErrors();
    });

    test("Edit Quotation - Update Quantity", async ({ adminPage }) => {
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

        await salesPage.editQuotationQuantity(customerName, "3");
    });

    test("Delete Quotation - Removes Draft", async ({ adminPage }) => {
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

        await salesPage.deleteQuotation(customerName);
    });
});
