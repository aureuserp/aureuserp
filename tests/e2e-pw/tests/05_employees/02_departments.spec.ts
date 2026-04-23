import { test } from "../../setup";
import { EmployeeManagementPage, type DepartmentData } from "../../pages/06_employeeManagement";

test.describe("Departments Module E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.ensureEmployeesPluginInstalled();
    });

    test.beforeEach(async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.gotoDepartmentsPage();
    });

    test("Access Departments Listing - Loads Table", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.gotoDepartmentsPage();
    });

    test("Create Department - Valid Inputs", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const departmentData: DepartmentData = {
            name: `E2E Department ${key}`,
        };

        await employeePage.createDepartment(departmentData);
        await employeePage.gotoDepartmentsPage();
        await employeePage.assertDepartmentVisible(departmentData.name);
    });

    test("Create Department - Missing Name Validation", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        await employeePage.createDepartmentWithMissingName();
    });

    test("Edit Department - Updates Name", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Department ${key}`;
        const updatedName = `E2E Edited Department ${key}`;

        await employeePage.createDepartment({ name: originalName });
        await employeePage.editDepartment(originalName, { name: updatedName });
        await employeePage.gotoDepartmentsPage();
        await employeePage.assertDepartmentVisible(updatedName);
    });

    test("Delete Department - Removes Record", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const departmentName = `E2E Delete Department ${key}`;

        await employeePage.createDepartment({ name: departmentName });
        await employeePage.deleteDepartment(departmentName);
    });

    test("Create Employee With Department - Link Employee To Department", async ({ adminPage }) => {
        const employeePage = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const departmentName = `E2E Linked Department ${key}`;
        const employeeName = `E2E Linked Employee ${key}`;

        await employeePage.createDepartment({ name: departmentName });

        await employeePage.createEmployee({
            name: employeeName,
            workEmail: `linked.employee+${key}@example.com`,
            department: departmentName,
        });

        await employeePage.gotoEmployeesPage();
        await employeePage.assertEmployeeVisible(employeeName);
    });
});
