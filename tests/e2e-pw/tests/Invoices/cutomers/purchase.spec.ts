import { test, expect } from "../../../setup";
import {
    generateVendorName,
    generateName,
} from "../../../utils/faker";

async function createVendorIndividual(adminPage) {

    /**
     * Redirecting to Vendor inside purchase plugin.
     */
    await adminPage.goto("/admin/purchase/orders/vendors");
    await adminPage.waitForSelector('a:has-text("New Vendor")');

    /**
     * New Vendor button clicked
     */
    await adminPage.getByRole('link', { name: 'New Vendor' }).click();

    /**
     * Waiting for Vendors edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Vendor")');

    /**
     * Filling up the Vendor details
     */
    const vendorName = generateName();
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(vendorName);

    // await adminPage.getByText('Browse').click();
    // await adminPage.locator('input[type="file"]').setInputFiles(filePath);
    /**
     * Clicking on create
     */
    //await adminPage.locator('#key-bindings-1').click();

    /**
     * Clicking on contacts
     */
    await adminPage.getByRole('main').locator('a').filter({ hasText: 'Contacts' }).click();
    //await adminPage.locator('a').filter({ hasText: 'Contacts' }).click();

    /**
     * Waiting for contacts page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Manage Contacts")');

    /**
     * Clicking on Add Contact
     */
    await adminPage.getByRole('button', { name: 'Add Contact' }).click();
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(vendorName);

    /**
     * Clicking on create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).nth(4).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Contact created' })).toBeVisible();

    /**
     * Clicking on Bank Accounts
     */
    await adminPage.getByRole('tab', { name: 'Bank Accounts' }).click();

    /**
    * Waiting for Manage Bank Accounts page to appear
    */
    await adminPage.waitForSelector('h1:has-text("Manage Bank Accounts")');

    /**
     * Clicking on New Bank Account
     */
    await adminPage.getByRole('button', { name: 'New Bank Account' }).click();

    /**
     * Filling bank account details
     */
    await adminPage.getByRole('textbox', { name: 'Account Number*' }).fill(generateAccountNumber());
    await adminPage.getByRole('switch', { name: 'Can Send Money' }).click();

    /**
     * Creating new bank
     */
    await adminPage.getByTitle('Create').click();

    /**
     * Filling bank details
     */
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(vendorName);

    /**
     * Clicking create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(3).click();
    //await adminPage.getByRole('button', { name: 'Create' }).click();

    /**
     * Selecting Account holder name from the drop down 
     */
    await adminPage.locator('div:nth-child(4) > .fi-fo-field-wrp > div > .grid > .fi-input-wrp > .fi-input-wrp-input > div:nth-child(2) > .choices > .choices__inner').click();
    await adminPage.locator('[id="choices--mountedTableActionsData0partner_id-item-choice-1"]').click();

    /**
     * Clicking on create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();
}

async function createVendorCompany(adminPage) {

    /**
     * Redirecting to Vendor inside purchase plugin.
     */
    await adminPage.goto("/admin/purchase/orders/vendors");
    await adminPage.getByRole('link', { name: 'Create Vendor' }).click();

    /**
     * New Vendor button clicked
     */
    await adminPage.getByRole('link', { name: 'Create Vendor' }).click();

    /**
     * Waiting for Vendor edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Vendor")');

    /**
     * Filling up the Vendor details
     */
    const vendorName = generateVendorName();
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(vendorName);

    /**
     * Clicking on create
     */
    await adminPage.locator('#key-bindings-1').click();

    /**
     * Clicking on contacts
     */
    await adminPage.getByRole('main').locator('a').filter({ hasText: 'Contacts' }).click();

    /**
     * Waiting for contacts page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Manage Contacts")');

    /**
     * Clicking on Add Contact
     */
    await adminPage.getByRole('button', { name: 'Add Contact' }).click();
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(customerName);

    /**
     * Clicking on create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).nth(4).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Contact created' })).toBeVisible();

    /**
     * Clicking on Bank Accounts
     */
    await adminPage.getByRole('tab', { name: 'Bank Accounts' }).click();

    /**
    * Waiting for Manage Bank Accounts page to appear
    */
    await adminPage.waitForSelector('h1:has-text("Manage Bank Accounts")');

    /**
     * Clicking on New Bank Account
     */
    await adminPage.getByRole('button', { name: 'New Bank Account' }).click();

    /**
     * Filling bank account details
     */
    await adminPage.getByRole('textbox', { name: 'Account Number*' }).fill(generateAccountNumber());
    await adminPage.getByRole('switch', { name: 'Can Send Money' }).click();

    /**
     * Creating new bank
     */
    await adminPage.getByTitle('Create').click();

    /**
     * Filling bank details
     */
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateBankName());

    /**
     * Clicking create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(3).click();

    /**
     * Selecting Account holder name from the drop down 
     */
    await adminPage.locator('div:nth-child(4) > .fi-fo-field-wrp > div > .grid > .fi-input-wrp > .fi-input-wrp-input > div:nth-child(2) > .choices > .choices__inner').click();
    await adminPage.locator('[id="choices--mountedTableActionsData0partner_id-item-choice-1"]').click();

    /**
     * Clicking on create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();
}

test.describe("Purchase Order", () => {
    test.describe("Create Vendor", () => {
        test("should create individual", async ({ adminPage }) => {
            await createVendorIndividual(adminPage)
        });
        test("should create company", async ({ adminPage }) => {
            await createVendorCompany(adminPage)
        });
    });
});