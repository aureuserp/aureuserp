import { test } from "../../setup";
import { EmployeeManagementPage, type EmployeeData } from "../../pages/06_employeeManagement";

test.describe("Employees Module E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.ensureEmployeesPluginInstalled();
    });

    test.beforeEach(async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.gotoEmployeesPage();
    });

    test("Access Employees Listing - Loads Table", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.gotoEmployeesPage();
    });

    test("Create Employee - Valid Inputs", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const employeeData: EmployeeData = {
            name: `E2E Employee ${key}`,
            jobTitle: "Software Engineer",
            workEmail: `employee+${key}@example.com`,
            workPhone: "9999999999",
            mobilePhone: "8888888888",
        };

        await employeePage.createEmployee(employeeData);
        await employeePage.gotoEmployeesPage();
        await employeePage.assertEmployeeVisible(employeeData.name);
    });

    test("Create Employee - Minimal Required Fields (Name Only)", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const employeeData: EmployeeData = {
            name: `E2E Minimal Employee ${key}`,
        };

        await employeePage.createEmployee(employeeData);
        await employeePage.gotoEmployeesPage();
        await employeePage.assertEmployeeVisible(employeeData.name);
    });

    test("Create Employee - Missing Name Validation", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.createEmployeeWithMissingName();
    });

    test("Create Employee - Invalid Work Email Validation", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();

        await employeePage.createEmployeeWithInvalidEmail(
            `E2E Invalid Email Employee ${key}`,
            "not-a-valid-email"
        );
    });

    test("Edit Employee - Updates Name", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Employee ${key}`;
        const updatedName = `E2E Edited Employee ${key}`;

        await employeePage.createEmployee({
            name: originalName,
            workEmail: `edit.employee+${key}@example.com`,
        });

        await employeePage.editEmployee(originalName, { name: updatedName });
        await employeePage.gotoEmployeesPage();
        await employeePage.assertEmployeeVisible(updatedName);
    });

    test("Edit Employee - Updates Job Title", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const employeeName = `E2E Job Title Employee ${key}`;

        await employeePage.createEmployee({
            name: employeeName,
            jobTitle: "Junior Developer",
            workEmail: `job.title+${key}@example.com`,
        });

        await employeePage.editEmployee(employeeName, { jobTitle: "Senior Developer" });
        await employeePage.gotoEmployeesPage();
        await employeePage.assertEmployeeVisible(employeeName);
    });

    test("View Employee - Opens Detail Page", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const employeeName = `E2E View Employee ${key}`;

        await employeePage.createEmployee({
            name: employeeName,
            workEmail: `view.employee+${key}@example.com`,
        });

        await employeePage.viewEmployee(employeeName);
    });

    test("Delete Employee - Removes Record", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const employeeName = `E2E Delete Employee ${key}`;

        await employeePage.createEmployee({
            name: employeeName,
            workEmail: `delete.employee+${key}@example.com`,
        });

        await employeePage.deleteEmployee(employeeName);
    });

    test("Bulk Delete Employees - Removes Records", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const bulkKey = `E2E Bulk Employee ${key}`;
        const employees = [
            { name: `${bulkKey} 1`, workEmail: `bulk.employee1+${key}@example.com` },
            { name: `${bulkKey} 2`, workEmail: `bulk.employee2+${key}@example.com` },
        ];

        for (const employee of employees) {
            await employeePage.createEmployee(employee);
        }

        await employeePage.bulkDeleteEmployees(bulkKey);
    });
});
