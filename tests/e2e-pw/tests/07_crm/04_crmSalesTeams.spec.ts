import { test, withAdminPage } from "../../setup";
import { CRMFlowPage } from "../../pages/07_crmManagement";

const TEAMS_OVERVIEW = "/admin/crm/sales/teams";
const TEAMS = "/admin/crm/configurations/teams";

test.describe("CRM Sales Teams E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    test("Sales Teams Overview - Grid And List", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        await crmPage.gotoCRMPage(TEAMS_OVERVIEW, /Sales Teams/);

        for (const layout of ["grid", "list"] as const) {
            await crmPage.expectLayout(TEAMS_OVERVIEW, layout);
        }
    });

    test("Sales Teams Listing - Loads Table", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        await crmPage.gotoCRMPage(TEAMS, /Sales Teams/);
    });

    test("Create Sales Team - Valid Inputs", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Team ${Date.now()}`;

        const team = await crmPage.createRecord(`${TEAMS}/create`, name);
        await crmPage.expectListed(TEAMS, name);

        await crmPage.deleteRecord(team, TEAMS);
    });

    test("Edit Sales Team - Updates Name", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const key = Date.now();
        const updatedName = `E2E CRM Team Updated ${key}`;

        const team = await crmPage.createRecord(`${TEAMS}/create`, `E2E CRM Team ${key}`);
        await crmPage.editRecord(team, updatedName);
        await crmPage.expectListed(TEAMS, updatedName);

        await crmPage.deleteRecord(team, TEAMS);
    });

    test("Delete Sales Team - Removes Record", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Team ${Date.now()}`;

        const team = await crmPage.createRecord(`${TEAMS}/create`, name);
        await crmPage.deleteRecord(team, TEAMS);

        await crmPage.expectNotListed(TEAMS, name);
    });
});
