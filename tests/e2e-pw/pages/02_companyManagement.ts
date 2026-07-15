import { type Locator, Page, expect } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { clickRowAction, filterListBySearch, rowByText } from "../utils/list";

export type CompanyData = {
    name: string;
    email?: string;
    phone?: string;
    status?: "true" | "false";
};

export class CompanyManagementPage {
    /**
     * Page and shared locators
     */
    readonly page: Page;
    readonly erpLocators: ErpLocators;
    companyCount: number = 0;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Navigate to companies listing and read initial count
     */
    async gotoCompaniesPage() {
        await this.page.goto("/admin/companies");
        await expect(this.page).toHaveURL(/.*companies/);
        await expect(this.erpLocators.companiesTable.first()).toBeVisible();

        await this.refreshCompanyCount();
    }

    /**
     * Read and cache company count from UI
     */
    async refreshCompanyCount(): Promise<number> {
        const countText = await this.erpLocators.allCompaniesCount.textContent();
        this.companyCount = countText ? parseInt(countText.trim()) : 0;

        return this.companyCount;
    }

    /**
     * Open create company form
     */
    async openCreateCompanyForm() {
        await this.erpLocators.companiesCreateButton.click();
        await expect(this.page).toHaveURL(/.*(create|companies)/);
    }

    /**
     * Create company using provided data
     */
    async createCompany(companyData: CompanyData) {
        await this.openCreateCompanyForm();

        await this.erpLocators.companiesNameInput.fill(companyData.name);
        if (companyData.email) await this.erpLocators.companiesEmailInput.fill(companyData.email);
        if (companyData.phone) await this.erpLocators.companiesPhoneInput.fill(companyData.phone);
        if (companyData.status && companyData.status.toLowerCase() !== "true") {
            const toggle = this.erpLocators.companiesStatusToggleOn;
            await toggle.click();
        }

        await this.refillIfEmptied(this.erpLocators.companiesNameInput, companyData.name);
        if (companyData.email) {
            await this.refillIfEmptied(this.erpLocators.companiesEmailInput, companyData.email);
        }


        for (let attempt = 0; attempt < 3; attempt++) {
            await expect(this.erpLocators.companiesSaveButton).toBeEnabled({ timeout: 60000 });
            await this.erpLocators.companiesSaveButton.click().catch(() => undefined);

            const left = await this.page
                .waitForURL((url) => !/companies\/create/.test(url.toString()), { timeout: 60000 })
                .then(() => true)
                .catch(() => false);

            if (left) {
                await this.page.waitForLoadState("networkidle").catch(() => undefined);

                return;
            }
        }

        await expect(this.page).not.toHaveURL(/companies\/create/);
    }

    /**
     * Type a value back into a field that a re-render has emptied.
     */
    private async refillIfEmptied(input: Locator, value: string) {
        for (let attempt = 0; attempt < 3; attempt++) {
            if ((await input.inputValue().catch(() => "")) === value) {
                return;
            }

            await input.fill(value);
            await this.page.waitForTimeout(300);
        }

        await expect(input).toHaveValue(value);
    }

    /**
     * Search company using listing search input
     */
    async searchCompany(keyword: string) {
        await filterListBySearch(this.page, this.erpLocators.companiesSearchInput, keyword);
    }

    /**
     * Assert company row is visible in list
     */
    async assertCompanyVisible(identifier: string) {
        await this.searchCompany(identifier);
        await expect(this.page.getByText(identifier).first()).toBeVisible();
    }

    /**
     * Edit company name by opening first matched row action
     */
    async editCompany(searchKey: string, updates: Partial<CompanyData>) {
        await this.searchCompany(searchKey);

        // Inside the row that holds this company: the search can legitimately leave other
        // records listed, and with parallel workers the row on top is somebody else's.
        await clickRowAction(rowByText(this.page, searchKey), "Edit");

        if (updates.name) await this.erpLocators.companiesNameInput.fill(updates.name);
        if (updates.email) await this.erpLocators.companiesEmailInput.fill(updates.email);
        if (updates.phone) await this.erpLocators.companiesPhoneInput.fill(updates.phone);

        await this.erpLocators.companiesSaveButton.click();

        // The test that follows asserts the new name is listed; a toast here would only tell
        // us that *somebody's* save succeeded, which with several workers is not this one's.
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Delete company from list
     */
    async deleteCompany(searchKey: string) {
        await this.searchCompany(searchKey);

        await clickRowAction(rowByText(this.page, searchKey), "Delete");
        await this.erpLocators.companiesConfirmDeleteButton.click();

        await this.expectCompanyAbsent(searchKey);
    }

    async expectCompanyAbsent(name: string) {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.gotoCompaniesPage();
        await this.searchCompany(name);
        await expect(this.page.getByText(name, { exact: true })).toHaveCount(0);
    }

    /**
     * Bulk delete companies from list
     */
    async bulkDeleteCompanies(companyNames: string[]) {

        const sharedKey = companyNames[0].split(" ").pop() ?? companyNames[0];
        await this.searchCompany(sharedKey);

        for (const name of companyNames) {
            await rowByText(this.page, name).locator('input[type="checkbox"]').first().check();
        }

        await this.erpLocators.bulkActionsButton.click();
        await this.erpLocators.forceDeleteButton.click();
        await this.erpLocators.companiesConfirmDeleteButton.click();

        for (const name of companyNames) {
            await this.expectCompanyAbsent(name);
        }
    }


    /**
     * Reusable assertion for success toast/notification
     */
}
