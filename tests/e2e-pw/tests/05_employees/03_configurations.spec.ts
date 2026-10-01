import { test } from "../../setup";
import {
    EmployeeManagementPage,
    type EmploymentTypeData,
    type EmployeeCategoryData,
    type WorkLocationData,
    type DepartureReasonData,
    type SkillTypeData,
    type JobPositionData,
    type ActivityPlanData,
} from "../../pages/06_employeeManagement";

test.describe("Employees Configurations - Employment Types", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Employment Types Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("employment-types");
    });

    test("Create Employment Type - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: EmploymentTypeData = {
            name: `E2E Employment Type ${key}`,
            code: `ET-${key}`,
        };

        await page.createEmploymentType(data);
        await page.gotoConfigurationPage("employment-types");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Employment Type - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("employment-types");
    });

    test("Edit Employment Type - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Employment Type ${key}`;
        const updatedName = `E2E Edited Employment Type ${key}`;

        await page.createEmploymentType({ name: originalName });
        await page.editEmploymentType(originalName, { name: updatedName });
        await page.gotoConfigurationPage("employment-types");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Employment Type - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Employment Type ${key}`;

        await page.createEmploymentType({ name });
        await page.deleteEmploymentType(name);
    });
});

test.describe("Employees Configurations - Employee Categories", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Employee Categories Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("employee-categories");
    });

    test("Create Employee Category - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: EmployeeCategoryData = {
            name: `E2E Employee Category ${key}`,
        };

        await page.createEmployeeCategory(data);
        await page.gotoConfigurationPage("employee-categories");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Employee Category - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("employee-categories");
    });

    test("Edit Employee Category - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Category ${key}`;
        const updatedName = `E2E Edited Category ${key}`;

        await page.createEmployeeCategory({ name: originalName });
        await page.editEmployeeCategory(originalName, updatedName);
        await page.gotoConfigurationPage("employee-categories");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Employee Category - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Category ${key}`;

        await page.createEmployeeCategory({ name });
        await page.deleteEmployeeCategory(name);
    });
});

test.describe("Employees Configurations - Work Locations", () => {
    const companyName = "YourCompany";

    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Work Locations Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("work-locations");
    });

    test("Create Work Location - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: WorkLocationData = {
            name: `E2E Work Location ${key}`,
            locationNumber: `WL-${key}`,
            company: companyName,
        };

        await page.createWorkLocation(data);
        await page.gotoConfigurationPage("work-locations");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Work Location - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("work-locations");
    });

    test("Edit Work Location - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Work Location ${key}`;
        const updatedName = `E2E Edited Work Location ${key}`;

        await page.createWorkLocation({ name: originalName, company: companyName });
        await page.editWorkLocation(originalName, { name: updatedName });
        await page.gotoConfigurationPage("work-locations");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Work Location - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Work Location ${key}`;

        await page.createWorkLocation({ name, company: companyName });
        await page.deleteWorkLocation(name);
    });
});

test.describe("Employees Configurations - Departure Reasons", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Departure Reasons Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("departure-reasons");
    });

    test("Create Departure Reason - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: DepartureReasonData = {
            name: `E2E Departure Reason ${key}`,
        };

        await page.createDepartureReason(data);
        await page.gotoConfigurationPage("departure-reasons");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Departure Reason - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("departure-reasons");
    });

    test("Edit Departure Reason - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Departure Reason ${key}`;
        const updatedName = `E2E Edited Departure Reason ${key}`;

        await page.createDepartureReason({ name: originalName });
        await page.editDepartureReason(originalName, updatedName);
        await page.gotoConfigurationPage("departure-reasons");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Departure Reason - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Departure Reason ${key}`;

        await page.createDepartureReason({ name });
        await page.deleteDepartureReason(name);
    });
});

test.describe("Employees Configurations - Skill Types", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Skill Types Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("skill-types");
    });

    test("Create Skill Type - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: SkillTypeData = {
            name: `E2E Skill Type ${key}`,
        };

        await page.createSkillType(data);
        await page.gotoConfigurationPage("skill-types");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Skill Type - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("skill-types");
    });

    test("Edit Skill Type - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Skill Type ${key}`;
        const updatedName = `E2E Edited Skill Type ${key}`;

        await page.createSkillType({ name: originalName });
        await page.editSkillType(originalName, updatedName);
        await page.gotoConfigurationPage("skill-types");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Skill Type - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Skill Type ${key}`;

        await page.createSkillType({ name });
        await page.deleteSkillType(name);
    });
});

test.describe("Employees Configurations - Job Positions", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Job Positions Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("job-positions");
    });

    test("Create Job Position - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: JobPositionData = {
            name: `E2E Job Position ${key}`,
        };

        await page.createJobPosition(data);
        await page.gotoConfigurationPage("job-positions");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Job Position - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("job-positions");
    });

    test("Edit Job Position - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Job Position ${key}`;
        const updatedName = `E2E Edited Job Position ${key}`;

        await page.createJobPosition({ name: originalName });
        await page.editJobPosition(originalName, { name: updatedName });
        await page.gotoConfigurationPage("job-positions");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Job Position - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Job Position ${key}`;

        await page.createJobPosition({ name });
        await page.deleteJobPosition(name);
    });
});

test.describe("Employees Configurations - Activity Plans", () => {
    test.beforeAll(async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.ensureEmployeesPluginInstalled();
    });

    test("Access Activity Plans Listing - Loads Table", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.gotoConfigurationPage("activity-plans");
    });

    test("Create Activity Plan - Valid Inputs", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const data: ActivityPlanData = {
            name: `E2E Activity Plan ${key}`,
        };

        await page.createActivityPlan(data);
        await page.gotoConfigurationPage("activity-plans");
        await page.assertConfigurationVisible(data.name);
    });

    test("Create Activity Plan - Missing Name Validation", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        await page.createConfigurationWithMissingName("activity-plans");
    });

    test("Edit Activity Plan - Updates Name", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Editable Activity Plan ${key}`;
        const updatedName = `E2E Edited Activity Plan ${key}`;

        await page.createActivityPlan({ name: originalName });
        await page.editActivityPlan(originalName, { name: updatedName });
        await page.gotoConfigurationPage("activity-plans");
        await page.assertConfigurationVisible(updatedName);
    });

    test("Delete Activity Plan - Removes Record", async ({ adminPage }) => {
        const page = new EmployeeManagementPage(adminPage);
        const key = Date.now();
        const name = `E2E Delete Activity Plan ${key}`;

        await page.createActivityPlan({ name });
        await page.deleteActivityPlan(name);
    });
});
