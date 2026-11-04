import { test, withAdminPage } from "../../setup";
import { CRMFlowPage } from "../../pages/07_crmManagement";

const LEADS = "/admin/crm/leads/leads";

test.describe("CRM Leads E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    test("Leads Listing - Loads Table", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        await crmPage.gotoCRMPage(LEADS, /Leads/);
    });

    test("Leads Layouts - List, Kanban And Graph", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);

        for (const layout of ["list", "graph"] as const) {
            await crmPage.expectLayout(LEADS, layout);
        }

        await crmPage.expectDefaultGroupBy(LEADS, "team.name");
        await crmPage.expectLayout(`${LEADS}?grouping=user.name`, "kanban");
    });

    test("Create Lead - Valid Inputs", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Lead ${Date.now()}`;

        const lead = await crmPage.createRecord(`${LEADS}/create`, name);
        await crmPage.expectListed(LEADS, name);

        await crmPage.deleteRecord(lead, LEADS);
    });

    test("Edit Lead - Updates Name", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const key = Date.now();
        const updatedName = `E2E CRM Lead Updated ${key}`;

        const lead = await crmPage.createRecord(`${LEADS}/create`, `E2E CRM Lead ${key}`);
        await crmPage.editRecord(lead, updatedName);
        await crmPage.expectListed(LEADS, updatedName);

        await crmPage.deleteRecord(lead, LEADS);
    });

    test("Delete Lead - Removes Record", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Lead ${Date.now()}`;

        const lead = await crmPage.createRecord(`${LEADS}/create`, name);
        await crmPage.deleteRecord(lead, LEADS);

        await crmPage.expectNotListed(LEADS, name);
    });
});
