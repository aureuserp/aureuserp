import { test, expect } from "../../setup";
import { CompanyManagementPage, type CompanyData } from "../../pages/03_companyManagement";

test.describe("Companies Module E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        await companyPage.gotoCompaniesPage();
    });

    test("Access Companies Listing - Loads Table", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        await companyPage.gotoCompaniesPage();
    });

    test("Create Company - Valid Inputs", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        await companyPage.gotoCompaniesPage();
        const initialCount = await companyPage.refreshCompanyCount();
        const key = Date.now();
        const companyData: CompanyData = {
            name: `E2E Company ${key}`,
            email: `company+${key}@example.com`,
            phone: "9999999999",
            status: "true",
        };

        await companyPage.createCompany(companyData);
        await companyPage.gotoCompaniesPage();
        const updatedCount = await companyPage.refreshCompanyCount();
        expect(updatedCount).toBe(initialCount + 1);
    });

    test("Create Company - Inactive Status", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const key = Date.now();
        const companyData: CompanyData = {
            name: `E2E Company ${key}`,
            email: `company+${key}@example.com`,
            phone: "9999999999",
            status: "false",
        };

        await companyPage.createCompany(companyData);
        await companyPage.gotoCompaniesPage();
        await companyPage.assertCompanyVisible(companyData.name);
    });

    test("Edit Company - Updates Name In Listing", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Edit Company ${key}`;
        const updatedName = `E2E Edited Company ${key}`;

        await companyPage.createCompany({ name: originalName, email: `edit+${key}@example.com` });
        await companyPage.gotoCompaniesPage();
        await companyPage.editCompany(originalName, { name: updatedName });
        await companyPage.gotoCompaniesPage();
        await companyPage.assertCompanyVisible(updatedName);
    });

    test("Delete Company - Removes Record", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Company ${key}`;

        await companyPage.createCompany({ name, email: `delete+${key}@example.com` });
        await companyPage.gotoCompaniesPage();
        await companyPage.deleteCompany(name);
    });

    test("Bulk Delete Companies - Removes Records", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const key = Date.now();
        const companyNames = [`E2E Bulk Delete Company 1 ${key}`, `E2E Bulk Delete Company 2 ${key}`];

        for (const name of companyNames) {
            await companyPage.createCompany({ name, email: `bulk-delete+${key}@example.com` });
            await companyPage.gotoCompaniesPage();
        }
        await companyPage.bulkDeleteCompanies(companyNames);
        await companyPage.gotoCompaniesPage();
    });
});
