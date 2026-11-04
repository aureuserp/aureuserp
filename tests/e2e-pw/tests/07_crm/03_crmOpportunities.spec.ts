import { test, withAdminPage } from "../../setup";
import { CRMFlowPage } from "../../pages/07_crmManagement";

const PIPELINE = "/admin/crm/sales/pipeline";

test.describe("CRM Opportunities E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    test("Pipeline Layouts - Kanban, List And Graph", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);

        for (const layout of ["kanban", "list", "graph"] as const) {
            await crmPage.expectLayout(PIPELINE, layout);
        }
    });

    test("Create Opportunity - Valid Inputs", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Opportunity ${Date.now()}`;

        const opportunity = await crmPage.createRecord(`${PIPELINE}/create`, name);
        await crmPage.expectListed(`${PIPELINE}?layout=list`, name);

        await crmPage.deleteRecord(opportunity, PIPELINE);
    });

    test("Edit Opportunity - Updates Name", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const key = Date.now();
        const updatedName = `E2E CRM Opportunity Updated ${key}`;

        const opportunity = await crmPage.createRecord(`${PIPELINE}/create`, `E2E CRM Opportunity ${key}`);
        await crmPage.editRecord(opportunity, updatedName);
        await crmPage.expectListed(`${PIPELINE}?layout=list`, updatedName);

        await crmPage.deleteRecord(opportunity, PIPELINE);
    });

    test("Delete Opportunity - Removes Record", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Opportunity ${Date.now()}`;

        const opportunity = await crmPage.createRecord(`${PIPELINE}/create`, name);
        await crmPage.deleteRecord(opportunity, PIPELINE);

        await crmPage.expectNotListed(`${PIPELINE}?layout=list`, name);
    });
});
