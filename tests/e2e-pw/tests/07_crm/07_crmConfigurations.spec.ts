import { test, withAdminPage } from "../../setup";
import { CRMFlowPage } from "../../pages/07_crmManagement";

const TAGS = "/admin/crm/configurations/tags";
const LOST_REASONS = "/admin/crm/configurations/lost-reasons";

const CONFIGURATION_PAGES: { name: string; path: string; title: RegExp }[] = [
    { name: "Stages", path: "/admin/crm/configurations/stages", title: /Stages/ },
    { name: "Activity Types", path: "/admin/crm/configurations/activity-types", title: /Activity Types/ },
    { name: "Activity Plans", path: "/admin/crm/configurations/activity-plans", title: /Activity Plans/ },
    { name: "Recurring Plans", path: "/admin/crm/configurations/recurring-plans", title: /Recurring Plans/ },
    { name: "Partner Levels", path: "/admin/crm/configurations/partner-levels", title: /Levels/ },
    { name: "Tags", path: TAGS, title: /Tags/ },
    { name: "Lost Reasons", path: LOST_REASONS, title: /Lost Reasons/ },
];

test.describe("CRM Configurations E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    for (const configuration of CONFIGURATION_PAGES) {
        test(`${configuration.name} - Loads Table`, async ({ adminPage }) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.gotoCRMPage(configuration.path, configuration.title);
        });
    }

    test("Create Tag - Valid Inputs", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Tag ${Date.now()}`;

        await crmPage.createInModal(TAGS, /New Tag/, name);

        await crmPage.deleteInModal(TAGS, name);
    });

    test("Edit Tag - Updates Name", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const key = Date.now();
        const name = `E2E CRM Tag ${key}`;
        const updatedName = `E2E CRM Tag Updated ${key}`;

        await crmPage.createInModal(TAGS, /New Tag/, name);
        await crmPage.editInModal(TAGS, name, updatedName);

        await crmPage.deleteInModal(TAGS, updatedName);
    });

    test("Delete Tag - Removes Record", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Tag ${Date.now()}`;

        await crmPage.createInModal(TAGS, /New Tag/, name);
        await crmPage.deleteInModal(TAGS, name);

        await crmPage.expectNotListed(TAGS, name);
    });

    test("Create Lost Reason - Valid Inputs", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Lost Reason ${Date.now()}`;

        await crmPage.createInModal(LOST_REASONS, /New Lost Reason/, name);

        await crmPage.deleteInModal(LOST_REASONS, name);
    });

    test("Edit Lost Reason - Updates Name", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const key = Date.now();
        const name = `E2E CRM Lost Reason ${key}`;
        const updatedName = `E2E CRM Lost Reason Updated ${key}`;

        await crmPage.createInModal(LOST_REASONS, /New Lost Reason/, name);
        await crmPage.editInModal(LOST_REASONS, name, updatedName);

        await crmPage.deleteInModal(LOST_REASONS, updatedName);
    });

    test("Delete Lost Reason - Removes Record", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Lost Reason ${Date.now()}`;

        await crmPage.createInModal(LOST_REASONS, /New Lost Reason/, name);
        await crmPage.deleteInModal(LOST_REASONS, name);

        await crmPage.expectNotListed(LOST_REASONS, name);
    });
});
