import { test } from "../../setup";
import { SalesFlowPage } from "../../pages/05_salesFlow";

test.describe("Sales Customers E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.ensureSalesPluginInstalled();
    });

    test("Customers Listing - Loads Table", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoCustomersPage();
    });

    test("Create Customer - Valid Inputs", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();

        await salesPage.createCustomer({
            name: `E2E Sales Customer ${key}`,
            email: `sales.customer+${key}@example.com`,
        });
    });

    test("Create Customer - Validation Errors", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoCustomersPage();
        await salesPage.erpLocators.salesCustomerCreateButton.click();
        await salesPage.erpLocators.salesCustomerSaveButton.click();
        await salesPage.expectValidationErrors();
    });

    test("Edit Customer - Updates Name", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Sales Customer ${key}`;
        const updatedName = `E2E Sales Customer Updated ${key}`;

        await salesPage.createCustomer({
            name: originalName,
            email: `sales.customer+${key}@example.com`,
        });

        await salesPage.editCustomer(originalName, { name: updatedName });
    });

    test("Delete Customer - Removes Record", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Sales Customer ${key}`;

        await salesPage.createCustomer({
            name: customerName,
            email: `sales.customer+${key}@example.com`,
        });

        await salesPage.deleteCustomer(customerName);
    });
});
