import { Page, expect, type Locator } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type EmployeeData = {
    name: string;
    jobTitle?: string;
    workEmail?: string;
    workPhone?: string;
    mobilePhone?: string;
    department?: string;
    jobPosition?: string;
    manager?: string;
};

export type DepartmentData = {
    name: string;
    manager?: string;
    parent?: string;
    company?: string;
};

export type EmploymentTypeData = {
    name: string;
    code?: string;
    country?: string;
};

export type EmployeeCategoryData = {
    name: string;
};

export type WorkLocationData = {
    name: string;
    locationNumber?: string;
    company: string;
};

export type DepartureReasonData = {
    name: string;
};

export type SkillTypeData = {
    name: string;
};

export type JobPositionData = {
    name: string;
    department?: string;
    company?: string;
    employmentType?: string;
};

export type ActivityPlanData = {
    name: string;
    department?: string;
    company?: string;
};

export type ConfigurationSlug =
    | "employment-types"
    | "employee-categories"
    | "work-locations"
    | "departure-reasons"
    | "skill-types"
    | "job-positions"
    | "activity-plans";

export class EmployeeManagementPage {
    /**
     * Page and shared locators
     */
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Ensure the Employees plugin is installed before running employee tests
     */
    async ensureEmployeesPluginInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Employees");
    }

    /**
     * Navigate to employees listing
     */
    async gotoEmployeesPage() {
        await this.page.goto("/admin/employees/employees");
        await expect(this.page).toHaveURL(/employees\/employees/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.employeeCreateButton).toBeVisible();
        await expect(this.erpLocators.employeesTable.first()).toBeVisible();
    }

    /**
     * Open create employee form
     */
    async openCreateEmployeeForm() {
        await this.erpLocators.employeeCreateButton.click();
        await expect(this.page).toHaveURL(/employees\/employees\/create/);
    }

    /**
     * Create employee using provided data
     */
    async createEmployee(employee: EmployeeData) {
        await this.gotoEmployeesPage();
        await this.openCreateEmployeeForm();

        await this.erpLocators.employeeNameInput.fill(employee.name);
        if (employee.jobTitle) await this.erpLocators.employeeJobTitleInput.fill(employee.jobTitle);
        if (employee.workEmail) await this.erpLocators.employeeWorkEmailInput.fill(employee.workEmail);
        if (employee.workPhone) await this.erpLocators.employeeWorkPhoneInput.fill(employee.workPhone);
        if (employee.mobilePhone) await this.erpLocators.employeeMobilePhoneInput.fill(employee.mobilePhone);

        if (employee.department) {
            await this.selectBySearch(this.erpLocators.employeeDepartmentSelect, employee.department);
        }
        if (employee.jobPosition) {
            await this.selectBySearch(this.erpLocators.employeeJobPositionSelect, employee.jobPosition);
        }
        if (employee.manager) {
            await this.selectBySearch(this.erpLocators.employeeManagerSelect, employee.manager);
        }

        await this.erpLocators.employeeSaveButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Search employees in listing
     */
    async searchEmployee(keyword: string) {
        await this.erpLocators.employeeSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Assert employee record is visible in listing
     */
    async assertEmployeeVisible(identifier: string) {
        await this.searchEmployee(identifier);
        await expect(this.page.getByText(identifier).first()).toBeVisible();
    }

    /**
     * Edit employee name by opening first matched row edit action
     */
    async editEmployee(searchKey: string, updates: Partial<EmployeeData>) {
        await this.gotoEmployeesPage();
        await this.searchEmployee(searchKey);
        await this.erpLocators.employeeEditButton.click();

        if (updates.name) await this.erpLocators.employeeNameInput.fill(updates.name);
        if (updates.jobTitle) await this.erpLocators.employeeJobTitleInput.fill(updates.jobTitle);
        if (updates.workEmail) await this.erpLocators.employeeWorkEmailInput.fill(updates.workEmail);
        if (updates.workPhone) await this.erpLocators.employeeWorkPhoneInput.fill(updates.workPhone);
        if (updates.mobilePhone) await this.erpLocators.employeeMobilePhoneInput.fill(updates.mobilePhone);

        if (updates.department) {
            await this.selectBySearch(this.erpLocators.employeeDepartmentSelect, updates.department);
        }
        if (updates.jobPosition) {
            await this.selectBySearch(this.erpLocators.employeeJobPositionSelect, updates.jobPosition);
        }
        if (updates.manager) {
            await this.selectBySearch(this.erpLocators.employeeManagerSelect, updates.manager);
        }

        await this.erpLocators.employeeEditSaveButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Delete employee from listing
     */
    async deleteEmployee(searchKey: string) {
        await this.gotoEmployeesPage();
        await this.searchEmployee(searchKey);
        await this.erpLocators.employeeDeleteButton.click();
        await this.erpLocators.employeeConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Bulk delete employees from listing
     */
    async bulkDeleteEmployees(searchKey: string) {
        await this.gotoEmployeesPage();
        await this.searchEmployee(searchKey);
        await this.erpLocators.selectAllEmployeesButton.click();
        await this.erpLocators.employeeBulkActionsButton.click();
        await this.erpLocators.employeeBulkDeleteButton.click();
        await this.erpLocators.employeeConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Open employee view page from listing
     */
    async viewEmployee(searchKey: string) {
        await this.gotoEmployeesPage();
        await this.searchEmployee(searchKey);
        await this.erpLocators.employeeViewButton.click();
        await expect(this.page).toHaveURL(/employees\/employees\/\d+/);
    }

    /**
     * Validate required fields by submitting blank form
     */
    async createEmployeeWithMissingName() {
        await this.gotoEmployeesPage();
        await this.openCreateEmployeeForm();
        await this.erpLocators.employeeSaveButton.click();
        await this.expectValidationErrors();
    }

    /**
     * Validate invalid work email input
     */
    async createEmployeeWithInvalidEmail(name: string, invalidEmail: string) {
        await this.gotoEmployeesPage();
        await this.openCreateEmployeeForm();
        await this.erpLocators.employeeNameInput.fill(name);
        await this.erpLocators.employeeWorkEmailInput.fill(invalidEmail);
        await this.erpLocators.employeeSaveButton.click();
        await this.expectValidationErrors();
    }

    /**
     * Navigate to departments listing
     */
    async gotoDepartmentsPage() {
        await this.page.goto("/admin/employees/departments");
        await expect(this.page).toHaveURL(/employees\/departments/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.departmentCreateButton).toBeVisible();
        await expect(this.erpLocators.departmentsTable.first()).toBeVisible();
    }

    /**
     * Open create department form
     */
    async openCreateDepartmentForm() {
        await this.erpLocators.departmentCreateButton.click();
        await expect(this.page).toHaveURL(/employees\/departments\/create/);
    }

    /**
     * Create department using provided data
     */
    async createDepartment(department: DepartmentData) {
        await this.gotoDepartmentsPage();
        await this.openCreateDepartmentForm();

        await this.erpLocators.departmentNameInput.fill(department.name);

        if (department.parent) {
            await this.selectBySearch(this.erpLocators.departmentParentSelect, department.parent);
        }
        if (department.manager) {
            await this.selectBySearch(this.erpLocators.departmentManagerSelect, department.manager);
        }
        if (department.company) {
            await this.selectBySearch(this.erpLocators.departmentCompanySelect, department.company);
        }

        await this.erpLocators.departmentSaveButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Search department in listing
     */
    async searchDepartment(keyword: string) {
        await this.erpLocators.departmentSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Assert department record is visible in listing
     */
    async assertDepartmentVisible(identifier: string) {
        await this.searchDepartment(identifier);
        await expect(this.page.getByText(identifier).first()).toBeVisible();
    }

    /**
     * Edit department by opening row actions dropdown
     */
    async editDepartment(searchKey: string, updates: Partial<DepartmentData>) {
        await this.gotoDepartmentsPage();
        await this.searchDepartment(searchKey);
        await this.erpLocators.departmentEditButton.click();

        if (updates.name) await this.erpLocators.departmentNameInput.fill(updates.name);
        if (updates.parent) {
            await this.selectBySearch(this.erpLocators.departmentParentSelect, updates.parent);
        }
        if (updates.manager) {
            await this.selectBySearch(this.erpLocators.departmentManagerSelect, updates.manager);
        }
        if (updates.company) {
            await this.selectBySearch(this.erpLocators.departmentCompanySelect, updates.company);
        }

        await this.erpLocators.departmentEditSaveButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Delete department from listing
     */
    async deleteDepartment(searchKey: string) {
        await this.gotoDepartmentsPage();
        await this.searchDepartment(searchKey);
        await this.erpLocators.departmentDeleteButton.click();
        await this.erpLocators.departmentConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Validate required fields on department by submitting blank
     */
    async createDepartmentWithMissingName() {
        await this.gotoDepartmentsPage();
        await this.openCreateDepartmentForm();
        await this.erpLocators.departmentSaveButton.click();
        await this.expectValidationErrors();
    }

    /**
     * Select dropdown option by searching for a value
     */
    async selectBySearch(trigger: Locator, value: string) {
        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        const option = this.erpLocators.employeeSelectOption
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(this.erpLocators.employeeSelectSearchInput).toBeVisible();
        await this.erpLocators.employeeSelectSearchInput.fill(value);
        await expect(option).toBeVisible();
        await option.click();
    }

    /**
     * ------------------------------------------------------------------
     * Configurations - generic helpers
     * ------------------------------------------------------------------
     */

    /**
     * Navigate to a specific configuration listing page
     */
    async gotoConfigurationPage(slug: ConfigurationSlug) {
        await this.page.goto(`/admin/employees/configurations/${slug}`);
        await expect(this.page).toHaveURL(new RegExp(`employees/configurations/${slug}`));
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.configurationTable.first()).toBeVisible();
    }

    /**
     * Open create form: either modal or dedicated create page
     */
    async openCreateConfigurationForm(slug: ConfigurationSlug) {
        await this.erpLocators.configurationCreateButton.click();
        if (this.hasDedicatedCreatePage(slug)) {
            await expect(this.page).toHaveURL(new RegExp(`${slug}/create`));
        } else {
            await expect(this.page.getByRole("dialog")).toBeVisible();
        }
    }

    /**
     * Submit the appropriate save button depending on create style
     */
    async submitConfigurationCreate(slug: ConfigurationSlug) {
        if (this.hasDedicatedCreatePage(slug)) {
            await this.erpLocators.configurationCreatePageSaveButton.click();
        } else {
            await this.erpLocators.configurationModalCreateButton.click();
        }
        await this.expectConfigurationSuccessToast();
    }

    /**
     * Submit the appropriate edit save button
     */
    async submitConfigurationEdit(slug: ConfigurationSlug) {
        if (this.hasDedicatedCreatePage(slug)) {
            await this.erpLocators.configurationEditPageSaveButton.click();
        } else {
            await this.erpLocators.configurationModalSaveButton.click();
        }
        await this.expectConfigurationSuccessToast();
    }

    /**
     * Search a configuration listing
     */
    async searchConfiguration(keyword: string) {
        await this.erpLocators.configurationSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Assert a configuration record is visible in the listing
     */
    async assertConfigurationVisible(identifier: string) {
        await this.searchConfiguration(identifier);
        await expect(this.page.getByText(identifier).first()).toBeVisible();
    }

    /**
     * Click edit action for the first matched row.
     * Skill Types wraps actions in a dropdown, so open the Actions menu first.
     */
    async openEditForConfigurationRow(slug: ConfigurationSlug) {
        if (slug === "skill-types") {
            await this.erpLocators.configurationRowActionsButton.click();
        }
        await this.erpLocators.configurationEditButton.click();
    }

    /**
     * Click delete action for the first matched row
     */
    async deleteConfigurationRow(slug: ConfigurationSlug) {
        if (slug === "skill-types") {
            await this.erpLocators.configurationRowActionsButton.click();
        }
        await this.erpLocators.configurationDeleteButton.click();
        await this.erpLocators.configurationConfirmDeleteButton.click();
        await this.expectConfigurationSuccessToast();
    }

    /**
     * Validate missing name on any configuration form
     */
    async createConfigurationWithMissingName(slug: ConfigurationSlug) {
        await this.gotoConfigurationPage(slug);
        await this.openCreateConfigurationForm(slug);
        if (this.hasDedicatedCreatePage(slug)) {
            await this.erpLocators.configurationCreatePageSaveButton.click();
        } else {
            await this.erpLocators.configurationModalCreateButton.click();
        }
        await expect(this.erpLocators.configurationValidationMessage.first()).toBeVisible();
    }

    /**
     * Employment Type helpers
     */
    async createEmploymentType(data: EmploymentTypeData) {
        await this.gotoConfigurationPage("employment-types");
        await this.openCreateConfigurationForm("employment-types");
        await this.erpLocators.configurationNameInput.fill(data.name);
        if (data.code) await this.erpLocators.configurationCodeInput.fill(data.code);
        if (data.country) await this.selectBySearch(this.erpLocators.configurationCountrySelect, data.country);
        await this.submitConfigurationCreate("employment-types");
    }

    async editEmploymentType(searchKey: string, updates: Partial<EmploymentTypeData>) {
        await this.gotoConfigurationPage("employment-types");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("employment-types");
        if (updates.name) await this.erpLocators.configurationNameInput.fill(updates.name);
        if (updates.code) await this.erpLocators.configurationCodeInput.fill(updates.code);
        if (updates.country) await this.selectBySearch(this.erpLocators.configurationCountrySelect, updates.country);
        await this.submitConfigurationEdit("employment-types");
    }

    async deleteEmploymentType(searchKey: string) {
        await this.gotoConfigurationPage("employment-types");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("employment-types");
    }

    /**
     * Employee Category helpers
     */
    async createEmployeeCategory(data: EmployeeCategoryData) {
        await this.gotoConfigurationPage("employee-categories");
        await this.openCreateConfigurationForm("employee-categories");
        await this.erpLocators.configurationNameInput.fill(data.name);
        await this.submitConfigurationCreate("employee-categories");
    }

    async editEmployeeCategory(searchKey: string, newName: string) {
        await this.gotoConfigurationPage("employee-categories");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("employee-categories");
        await this.erpLocators.configurationNameInput.fill(newName);
        await this.submitConfigurationEdit("employee-categories");
    }

    async deleteEmployeeCategory(searchKey: string) {
        await this.gotoConfigurationPage("employee-categories");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("employee-categories");
    }

    /**
     * Work Location helpers
     */
    async createWorkLocation(data: WorkLocationData) {
        await this.gotoConfigurationPage("work-locations");
        await this.openCreateConfigurationForm("work-locations");
        await this.erpLocators.configurationNameInput.fill(data.name);
        if (data.locationNumber) await this.erpLocators.configurationLocationNumberInput.fill(data.locationNumber);
        await this.selectBySearch(this.erpLocators.configurationCompanySelect, data.company);
        const typeOption = this.erpLocators.configurationLocationTypeOption;
        if (await typeOption.count()) {
            await typeOption.click();
        }
        await this.submitConfigurationCreate("work-locations");
    }

    async editWorkLocation(searchKey: string, updates: Partial<WorkLocationData>) {
        await this.gotoConfigurationPage("work-locations");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("work-locations");
        if (updates.name) await this.erpLocators.configurationNameInput.fill(updates.name);
        if (updates.locationNumber) await this.erpLocators.configurationLocationNumberInput.fill(updates.locationNumber);
        if (updates.company) await this.selectBySearch(this.erpLocators.configurationCompanySelect, updates.company);
        await this.submitConfigurationEdit("work-locations");
    }

    async deleteWorkLocation(searchKey: string) {
        await this.gotoConfigurationPage("work-locations");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("work-locations");
    }

    /**
     * Departure Reason helpers
     */
    async createDepartureReason(data: DepartureReasonData) {
        await this.gotoConfigurationPage("departure-reasons");
        await this.openCreateConfigurationForm("departure-reasons");
        await this.erpLocators.configurationNameInput.fill(data.name);
        await this.submitConfigurationCreate("departure-reasons");
    }

    async editDepartureReason(searchKey: string, newName: string) {
        await this.gotoConfigurationPage("departure-reasons");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("departure-reasons");
        await this.erpLocators.configurationNameInput.fill(newName);
        await this.submitConfigurationEdit("departure-reasons");
    }

    async deleteDepartureReason(searchKey: string) {
        await this.gotoConfigurationPage("departure-reasons");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("departure-reasons");
    }

    /**
     * Skill Type helpers (uses ActionGroup dropdown for row actions)
     */
    async createSkillType(data: SkillTypeData) {
        await this.gotoConfigurationPage("skill-types");
        await this.openCreateConfigurationForm("skill-types");
        await this.erpLocators.configurationNameInput.fill(data.name);
        await this.submitConfigurationCreate("skill-types");
    }

    async editSkillType(searchKey: string, newName: string) {
        await this.gotoConfigurationPage("skill-types");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("skill-types");
        await this.erpLocators.configurationNameInput.fill(newName);
        await this.submitConfigurationEdit("skill-types");
    }

    async deleteSkillType(searchKey: string) {
        await this.gotoConfigurationPage("skill-types");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("skill-types");
    }

    /**
     * Job Position helpers (uses dedicated create page)
     */
    async createJobPosition(data: JobPositionData) {
        await this.gotoConfigurationPage("job-positions");
        await this.openCreateConfigurationForm("job-positions");
        await this.erpLocators.configurationNameInput.fill(data.name);
        if (data.department) await this.selectBySearch(this.erpLocators.configurationDepartmentSelect, data.department);
        if (data.company) await this.selectBySearch(this.erpLocators.configurationCompanySelect, data.company);
        if (data.employmentType) await this.selectBySearch(this.erpLocators.configurationEmploymentTypeSelect, data.employmentType);
        await this.submitConfigurationCreate("job-positions");
    }

    async editJobPosition(searchKey: string, updates: Partial<JobPositionData>) {
        await this.gotoConfigurationPage("job-positions");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("job-positions");
        if (updates.name) await this.erpLocators.configurationNameInput.fill(updates.name);
        if (updates.department) await this.selectBySearch(this.erpLocators.configurationDepartmentSelect, updates.department);
        if (updates.company) await this.selectBySearch(this.erpLocators.configurationCompanySelect, updates.company);
        if (updates.employmentType) await this.selectBySearch(this.erpLocators.configurationEmploymentTypeSelect, updates.employmentType);
        await this.submitConfigurationEdit("job-positions");
    }

    async deleteJobPosition(searchKey: string) {
        await this.gotoConfigurationPage("job-positions");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("job-positions");
    }

    /**
     * Activity Plan helpers
     */
    async createActivityPlan(data: ActivityPlanData) {
        await this.gotoConfigurationPage("activity-plans");
        await this.openCreateConfigurationForm("activity-plans");
        await this.erpLocators.configurationNameInput.fill(data.name);
        if (data.department) await this.selectBySearch(this.erpLocators.configurationDepartmentSelect, data.department);
        if (data.company) await this.selectBySearch(this.erpLocators.configurationCompanySelect, data.company);
        await this.submitConfigurationCreate("activity-plans");
    }

    async editActivityPlan(searchKey: string, updates: Partial<ActivityPlanData>) {
        await this.gotoConfigurationPage("activity-plans");
        await this.searchConfiguration(searchKey);
        await this.openEditForConfigurationRow("activity-plans");
        if (updates.name) await this.erpLocators.configurationNameInput.fill(updates.name);
        if (updates.department) await this.selectBySearch(this.erpLocators.configurationDepartmentSelect, updates.department);
        if (updates.company) await this.selectBySearch(this.erpLocators.configurationCompanySelect, updates.company);
        await this.submitConfigurationEdit("activity-plans");
    }

    async deleteActivityPlan(searchKey: string) {
        await this.gotoConfigurationPage("activity-plans");
        await this.searchConfiguration(searchKey);
        await this.deleteConfigurationRow("activity-plans");
    }

    /**
     * Whether a configuration resource opens a dedicated create page
     * instead of a modal for create/edit
     */
    private hasDedicatedCreatePage(slug: ConfigurationSlug): boolean {
        return slug === "job-positions";
    }

    /**
     * Reusable assertion for success toast/notification
     */
    private async expectSuccessToast() {
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.employeeSuccessToast).toBeVisible();
    }

    /**
     * Reusable assertion for configuration success toast
     */
    private async expectConfigurationSuccessToast() {
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.configurationSuccessToast).toBeVisible();
    }

    /**
     * Reusable assertion for inline validation errors
     */
    async expectValidationErrors() {
        await expect(this.erpLocators.employeeValidationMessage.first()).toBeVisible();
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\\]\\]/g, "\\$&");
    }
}
