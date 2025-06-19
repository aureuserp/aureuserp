import { test, expect } from "../../../setup"
import path from 'path';
import { fileURLToPath } from 'url';
import {
    generateName,
    generateCategory,
    generateProductName,
    generateAccountNumber,
    generateBankName,
    generateIncoterms,
    generateTerm,
    generateTaxGroup,
    generateTaxName,
    generateAttribute,
    generateDescription,
    generateAddress,
    generateEmail,
    generatePhoneNumber
} from '../../../utils/faker'
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function createCustomer(adminPage) {

    /**
     * Redirecting to Customer inside Invoices plugin.
     */
    await adminPage.goto("admin/customer/partners");
    await adminPage.waitForSelector('a:has-text("New Customer")');

    /**
     * New Customer button clicked
     */
    await adminPage.getByRole('link', { name: 'New Customer' }).click();

    /**
     * Waiting for Customer edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Customer")');

    /**
     * Filling up the Customer details
     */
    const customerName = generateName();
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(customerName);

    /**
     * Uploading an image for the customer
     */
    const randomCustomerImg = Math.floor(Math.random() * 7) + 1;
    const customerImagePath = path.resolve(__dirname, `../../../utils/images/avatar/avatar${randomCustomerImg}.png`);
    await adminPage.locator('input[type="file"]').setInputFiles(customerImagePath);

    /**
     * Filling the address details
     */
    await adminPage.getByRole('textbox', { name: 'Street 1' }).fill(generateAddress().street)
    await adminPage.getByRole('textbox', { name: 'City' }).fill(generateAddress().city);
    await adminPage.getByRole('textbox', { name: 'ZIP' }).fill(generateAddress().zip);

    /**
     * Filling the conatct details
     */
    const emailName = customerName.replace(/\s/g, '-').toLowerCase();
    await adminPage.getByRole('textbox', { name: 'Email' }).fill(`${emailName}@demo.com`);
    await adminPage.getByRole('textbox', { name: 'Phone' }).fill(generatePhoneNumber());

    /**
     * Clicking on create
     */
    await adminPage.locator('#key-bindings-1').click();

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
    let contactName = generateName();
    while (contactName === customerName) {
        contactName = generateName();
    }
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(contactName);

    /**
    * Uploading an image of the contact person
    */
    const randomContactImg = Math.floor(Math.random() * 7) + 1;
    const contactImagePath = path.resolve(__dirname, `../../../utils/images/avatar/avatar${randomContactImg}.png`);
    await adminPage.locator('input[type="file"]').setInputFiles(contactImagePath);

    /**
     * Filling the address details
     */
    await adminPage.getByRole('textbox', { name: 'Street 1' }).fill(generateAddress().street)
    await adminPage.getByRole('textbox', { name: 'City' }).fill(generateAddress().city);
    await adminPage.getByRole('textbox', { name: 'ZIP' }).fill(generateAddress().zip);

    /**
     * Filling the conatct details
     */
    const contactEmailName = contactName.replace(/\s/g, '-').toLowerCase();
    await adminPage.getByRole('textbox', { name: 'Email' }).fill(`${contactEmailName}@demo.com`);
    await adminPage.getByRole('textbox', { name: 'Phone' }).fill(generatePhoneNumber());

    /**
     * Clicking on create button
     */

    await adminPage.getByRole('button', { name: 'Create', exact: true }).nth(4).click();
    //await adminPage.getByRole('button', { name: 'Create', exact: true }).nth(4).click();

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
    const bankName = generateBankName();
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(bankName);
    const bankCode = bankName.replace(/[^A-Za-z]/g, '').toUpperCase();
    await adminPage.getByRole('textbox', { name: 'Bank Identifier Code' }).fill(bankCode);
    const bankEmailName = bankName.replace(/\s/g, '-').toLowerCase();
    await adminPage.getByRole('textbox', { name: 'Email' }).fill(`${bankEmailName}@demo.com`);
    await adminPage.getByRole('textbox', { name: 'Phone' }).fill(generatePhoneNumber());

    await adminPage.getByRole('textbox', { name: 'Street 1' }).fill(generateAddress().street)
    await adminPage.getByRole('textbox', { name: 'City' }).fill(generateAddress().city);
    await adminPage.getByRole('textbox', { name: 'ZIP' }).fill(generateAddress().zip);

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

async function createProduct(adminPage) {

    /**
     * Redirecting to Products inside Invoices plugin.
     */
    await adminPage.goto("admin/customer/products");
    await adminPage.waitForSelector('a:has-text("New Product")');

    /**
     * New Product button clicked
     */
    await adminPage.getByRole('link', { name: 'New Product' }).click();

    /**
     * Waiting for Products edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Products")');

    /**
     * Filling up the required fields
     */
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateProductName());

    await adminPage.locator('[id="data\\.description"]').fill(generateDescription());
    //await adminPage.getByRole('textbox', { name: 'Description' }).fill(generateDescription());
    await adminPage.getByRole('spinbutton', { name: 'Price*' }).fill('100');

    /**
     * Adding a Category
     */
    await adminPage.locator('[id="data\\.settings"]').getByRole('button', { name: 'Create' }).click();
    await adminPage.getByPlaceholder('eg. Lamps').fill(generateCategory());
    await adminPage.getByRole('button', { name: 'Create' }).nth(4).click();

    /**
     * Saving the product
     */
    await adminPage.locator('#key-bindings-1').click();

    /**
     * Clicking on Attributes
     */
    await adminPage.getByRole('tab', { name: 'Attributes' }).click();

    /**
     * Waiting for Manage Attributes page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Manage Attributes")');

    /**
     * Clicking on Add Attribute
     */
    await adminPage.getByRole('button', { name: 'Add Attribute' }).click();

    /**
     * Filling up the required fields for attribute
     */
    await adminPage.getByTitle('Create').click();
    await adminPage.locator('[id="mountedFormComponentActionsData\\.0\\.name"]').fill('Color');
    await adminPage.getByRole('radio', { name: 'Color' }).check();
    await adminPage.locator('[id="mountedFormComponentActionsData\\.0\\.options"]').getByRole('textbox', { name: 'Name*' }).fill('RED');
    await adminPage.getByRole('spinbutton', { name: 'Extra Price*' }).fill('100');
    await adminPage.getByRole('button', { name: 'Add to options' }).click();
    await adminPage.getByRole('textbox', { name: 'Name*' }).nth(2).fill('BLUE');
    await adminPage.getByRole('spinbutton', { name: 'Extra Price*' }).nth(1).fill('2000');

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).click();

    /**
     * Selecting the values for the attribute
     */
    await adminPage.getByRole('textbox', { name: 'Select an option' }).click();
    await adminPage.getByRole('option', { name: 'BLUE' }).click();
    await adminPage.getByRole('option', { name: 'RED' }).click();
    await adminPage.getByRole('heading', { name: 'Create product attribute' }).click();

    /**
     * Clicking on Create button for creating the attribute
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    //await expect(adminPage.getByRole('heading', { name: 'Attribute created' })).toBeVisible();

    /**
     * Clicking on Generate Variants 
     */
    await adminPage.getByRole('button', { name: 'Generate Variants' }).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Variants generated' })).toBeVisible();
}

async function createInvoice(adminPage) {

    /**
     * Redirecting to Invoices under Invoice plugin.
     */
    await adminPage.goto("admin/customer/invoices");
    await adminPage.waitForSelector('a:has-text("New Invoice")');

    /**
     * New Invoices button clicked
     */
    await adminPage.getByRole('link', { name: 'New Invoice' }).click();

    /**
     * Waiting for Invoices edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Invoice")');

    /**
     * General Section
     */
    await adminPage.locator('.choices__inner').first().click();
    await adminPage.waitForSelector('input[name="search_terms"]');
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Clicked on Add Products.
     */
    await adminPage.getByRole('button', { name: 'Add Product' }).click();

    /**
     * Waiting for products fields to be visible after button clicked
     */
    await adminPage.waitForSelector('.fi-fo-repeater-item-content', { state: 'visible' });

    /**
     * Adding products details
     */
    await adminPage.evaluate(() => {
        window.scrollTo(0, document.body.scrollHeight);
    });
    const productDropdown = adminPage.locator('select[id*="product_id"]').locator('..').locator('..');
    await productDropdown.locator('.choices__inner').click();
    await productDropdown.locator('.choices__list[role="listbox"] .choices__item--choice').first().click();

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Invoice created' })).toBeVisible();

    /**
     * Confirming the invoice
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();
}

async function createCreditNote(adminPage) {

    /**
     * Redirecting to credit-note under Invoice plugin.
     */
    await adminPage.goto("admin/customer/credit-notes");
    await adminPage.waitForSelector('a:has-text("New Credit Note")');

    /**
     * New Credit Note button clicked
     */
    await adminPage.getByRole('link', { name: 'New Credit Note' }).click();

    /**
     * Waiting for create credit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Credit Note")');

    /**
     * General Section
     */
    await adminPage.locator('.choices__inner').first().click();
    await adminPage.waitForSelector('input[name="search_terms"]');
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Clicked on Add Products.
     */
    await adminPage.getByRole('button', { name: 'Add Product' }).click();

    /**
     * Waiting for products fields to be visible after button clicked
     */
    await adminPage.waitForSelector('.fi-fo-repeater-item-content', { state: 'visible' });

    /**
     * Adding products details
     */
    await adminPage.evaluate(() => {
        window.scrollTo(0, document.body.scrollHeight);
    });
    const productDropdown = adminPage.locator('select[id*="product_id"]').locator('..').locator('..');
    await productDropdown.locator('.choices__inner').click();
    await productDropdown.locator('.choices__list[role="listbox"] .choices__item--choice').first().click();

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Credit note created' })).toBeVisible();

    /**
     * Confirming the credit note
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();
}

async function createCustomersPayment(adminPage) {

    /**
     * Redirecting to payments under Invoice plugin.
     */
    await adminPage.goto("admin/customer/payments");
    await adminPage.waitForSelector('a:has-text("New Payment")');

    /**
     * New Payment button clicked
     */
    await adminPage.getByRole('link', { name: 'New Payment' }).click();

    /**
     * Waiting for create payment page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Payment")');


    /**
     * Customer Bank Account
     */
    await adminPage.locator('div:nth-child(3) > .fi-fo-field-wrp > div > .grid > .fi-input-wrp > .fi-input-wrp-input > div:nth-child(2) > .choices > .choices__inner').click();
    await adminPage.locator('#choices--datapartner_id-item-choice-1').click();

    /**
     * Payment Method Section
     */
    await adminPage.locator('div').filter({ hasText: /^Select an option$/ }).first().click();
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Payment Method
     */
    await adminPage.locator('div').filter({ hasText: /^Select an option$/ }).first().click();
    await adminPage.locator('#choices--datapayment_method_line_id-item-choice-1').click();

    /**
     * Filling Amount
     */
    await adminPage.getByRole('spinbutton', { name: 'Amount*' }).fill('1000');

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.locator('div').filter({ hasText: 'Payment created The payment' }).nth(3)).toBeVisible();

    /**
     * Storing the State of the payment
     */
    const statusText = await adminPage.locator('span').filter({ hasText: 'Draft' }).first().textContent();

    /**
     * Confirming the payment
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();

    /**
     * Expecting the status to be Inprocess
     */
    //await expect(statusText).toContain('In Process');
}

async function createVendor(adminPage) {

    /**
     * Redirecting to Vendors inside Invoices plugin.
     */
    await adminPage.goto("admin/vendors/vendors");
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

    /**
     * Clicking on create
     */
    await adminPage.locator('#key-bindings-1').click();

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

async function createBill(adminPage) {

    /**
     * Redirecting to Bills under Invoice plugin.
     */
    await adminPage.goto("admin/vendors/bills");
    await adminPage.waitForSelector('a:has-text("Bills")');

    /**
     * New Bill button clicked
     */
    await adminPage.getByRole('link', { name: 'New Bill' }).click();

    /**
     * Waiting for Bill edit page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Bill")');

    /**
     * General Section
     */
    await adminPage.locator('.choices__inner').first().click();
    await adminPage.waitForSelector('input[name="search_terms"]');
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Clicked on Add Products.
     */
    await adminPage.getByRole('button', { name: 'Add Product' }).click();

    /**
     * Waiting for products fields to be visible after button clicked
     */
    await adminPage.waitForSelector('.fi-fo-repeater-item-content', { state: 'visible' });

    /**
     * Adding products details
     */
    await adminPage.evaluate(() => {
        window.scrollTo(0, document.body.scrollHeight);
    });
    const productDropdown = adminPage.locator('select[id*="product_id"]').locator('..').locator('..');
    await productDropdown.locator('.choices__inner').click();
    await productDropdown.locator('.choices__list[role="listbox"] .choices__item--choice').first().click();

    /**
     * Clicking on Create button
     */
    //await adminPage.getByRole('button', { name: 'Create', exact: true }).click();
    await adminPage.locator('#key-bindings-1').click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Bill created' })).toBeVisible();

    /**
     * Confirming the Bill
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();
}

async function createRefunds(adminPage) {

    /**
     * Redirecting to refunds under Invoice plugin.
     */
    await adminPage.goto("admin/vendors/refunds");
    await adminPage.waitForSelector('a:has-text("New Refund")');

    /**
     * New Refund button clicked
     */
    await adminPage.getByRole('link', { name: 'New Refund' }).click();

    /**
     * Waiting for create Refund page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Refund")');

    /**
     * General Section
     */
    await adminPage.locator('.choices__inner').first().click();
    await adminPage.waitForSelector('input[name="search_terms"]');
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Clicked on Add Products.
     */
    await adminPage.getByRole('button', { name: 'Add Product' }).click();

    /**
     * Waiting for products fields to be visible after button clicked
     */
    await adminPage.waitForSelector('.fi-fo-repeater-item-content', { state: 'visible' });

    /**
     * Adding products details
     */
    await adminPage.evaluate(() => {
        window.scrollTo(0, document.body.scrollHeight);
    });
    const productDropdown = adminPage.locator('select[id*="product_id"]').locator('..').locator('..');
    await productDropdown.locator('.choices__inner').click();
    await productDropdown.locator('.choices__list[role="listbox"] .choices__item--choice').first().click();

    /**
     * Clicking on Create button
     */
    //await adminPage.getByRole('button', { name: 'Create', exact: true }).click();
    await adminPage.locator('#key-bindings-1').click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Refund Created' })).toBeVisible();

    /**
     * Confirming the Refund
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();
}

async function createVendorsPayment(adminPage) {

    /**
     * Redirecting to payments under Invoice plugin.
     */
    await adminPage.goto("admin/vendors/payments");
    await adminPage.waitForSelector('a:has-text("New Payment")');

    /**
     * New Payment button clicked
     */
    await adminPage.getByRole('link', { name: 'New Payment' }).click();

    /**
     * Waiting for create payment page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Payment")');

    /**
     * Vendor Bank Account
     */
    await adminPage.locator('div:nth-child(3) > .fi-fo-field-wrp > div > .grid > .fi-input-wrp > .fi-input-wrp-input > div:nth-child(2) > .choices > .choices__inner').click();
    await adminPage.locator('#choices--datapartner_id-item-choice-1').click();

    /**
     * Payment Method Section
     */
    await adminPage.locator('div').filter({ hasText: /^Select an option$/ }).first().click();
    await adminPage.locator('[data-id="1"]').nth(0).click();

    /**
     * Payment Method
     */
    await adminPage.locator('div').filter({ hasText: /^Select an option$/ }).first().click();
    await adminPage.locator('#choices--datapayment_method_line_id-item-choice-1').click();

    /**
     * Filling Amount
     */
    await adminPage.getByRole('spinbutton', { name: 'Amount*' }).fill('1000');

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create', exact: true }).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.locator('div').filter({ hasText: 'Payment created The payment' }).nth(3)).toBeVisible();

    /**
     * Storing the State of the payment
     */
    //const statusText = await adminPage.locator('span').filter({ hasText: 'Draft' }).first().textContent();

    /**
     * Confirming the payment
     */
    await adminPage.getByRole('button', { name: 'Confirm' }).click();

    /**
     * Expecting the status to be Inprocess
     */
    //await expect(statusText).toContain('In Process');
}

async function createBankAccount(adminPage) {

    /**
     * Redirecting to Bank Account inside Invoices Configuration.
     */
    await adminPage.goto("/admin/invoices/configurations/bank-accounts");
    await adminPage.waitForSelector('a:has-text("Bank Accounts")');

    /**
     * New Bank Account button clicked
     */
    await adminPage.getByRole('button', { name: 'New Bank Account' }).click();

    /**
     * Waiting for create payment page to appear
     */
    await adminPage.waitForSelector('h2:has-text("Create bank account")');

    /**
     * Filling bank account details
     */
    await adminPage.getByRole('textbox', { name: 'Account Number*' }).fill(generateAccountNumber());

    /**
     * Creating new bank
     */
    await adminPage.getByTitle('Create').click();
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateBankName());
    await adminPage.getByRole('button', { name: 'Create' }).click();

    /**
     * Selecting Account holder name from the drop down 
     */
    await adminPage.locator('div:nth-child(3) > .fi-fo-field-wrp > div > .grid > .fi-input-wrp > .fi-input-wrp-input > div:nth-child(2) > .choices > .choices__inner').click();
    await adminPage.locator('#choices--mountedActionsData0partner_id-item-choice-1').click();

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Bank Account created' })).toBeVisible();
}

async function createIncoterms(adminPage) {

    /**
     * Redirecting to Incoterms inside Invoices Configuration.
     */
    await adminPage.goto("/admin/invoices/configurations/inco-terms");
    await adminPage.waitForSelector('a:has-text("Incoterms")');

    /**
     * New Incoterms button clicked
     */
    await adminPage.getByRole('button', { name: 'New Incoterms' }).click();

    /**
     * Waiting for Create Incoterms modal to appear
     */
    await adminPage.waitForSelector('h2:has-text("Create Incoterms")');

    /**
     * Filling up the required fields
     */
    await adminPage.getByRole('textbox', { name: 'Code*' }).fill(generateIncoterms());
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateIncoterms());

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Incoterm created' })).toBeVisible();
}

async function createPaymentTerm(adminPage) {

    /**
     * Redirecting to Payment Terms inside Invoices Configuration.
     */
    await adminPage.goto("/admin/invoices/configurations/payment-terms");
    await adminPage.waitForSelector('a:has-text("Payment Terms")');

    /**
     * New Payment Terms button clicked
     */
    await adminPage.getByRole('link', { name: 'New Payment Terms' }).click();

    /**
     * Waiting for Create Incoterms page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Payment Terms")');

    /**
     * Filling up the required fields
     */
    await adminPage.getByRole('textbox', { name: 'Payment Term*' }).fill(generateTerm());

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Payment term created' })).toBeVisible();
}

async function createTaxGroups(adminPage) {

    /**
    * Redirecting to Tax Groups inside Invoices Configuration.
    */
    await adminPage.goto("/admin/invoices/configurations/tax-groups");
    await adminPage.waitForSelector('a:has-text("Tax Groups")');

    /**
     * New Tax Groups button clicked
     */
    await adminPage.getByRole('link', { name: 'New Tax Groups' }).click();

    /**
     * Waiting for Create Tax Groups page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Tax Groups")');

    /**
     * Filling up the required fields
     */
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateTaxGroup());
    await adminPage.locator('div').filter({ hasText: /^Select an option$/ }).first().click();
    await adminPage.getByRole('option', { name: 'DummyCorp LLC' }).click();

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Tax group created' })).toBeVisible();
}

async function createTaxes(adminPage) {

    /**
    * Redirecting to Taxs in Invoices Configuration.
    */
    await adminPage.goto("/admin/invoices/configurations/taxes");
    await adminPage.waitForSelector('a:has-text("Taxes")');

    /**
     * New Tax button clicked
     */
    await adminPage.getByRole('link', { name: 'New Taxes' }).click();

    /**
     * Waiting for Create Taxes page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Taxes")');

    /**
     * Filling up the required fields
     */
    await adminPage.getByRole('textbox', { name: 'Name*' }).fill(generateTaxName());
    await adminPage.getByLabel('Tax Type*').selectOption('purchase');
    await adminPage.getByLabel('Tax Computation*').selectOption('fixed');
    await adminPage.getByRole('switch', { name: 'Status' }).click();
    await adminPage.getByRole('spinbutton', { name: 'Amount*' }).fill('15');
    await adminPage.getByLabel('Tax Group*').click();
    await adminPage.getByLabel('Tax Group*').selectOption('1');

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Tax created' })).toBeVisible();
}

async function createAttribute(adminPage) {

    /**
    * Redirecting to Product Attributes in Invoices Configuration.
    */
    await adminPage.goto("/admin/invoices/configurations/product-attributes");
    await adminPage.waitForSelector('a:has-text("Attributes")');

    /**
     * New Attribute button clicked
     */
    await adminPage.getByRole('link', { name: 'New Attribute' }).click();

    /**
     * Waiting for Create Taxes page to appear
     */
    await adminPage.waitForSelector('h1:has-text("Create Attribute")');

    /**
     * Filling up the Attribute with their values
     */
    const { attribute, values } = generateAttribute();
    await adminPage.locator('[id="data\\.name"]').fill(attribute);


    await adminPage.locator('[id="data\\.options"]').getByRole('textbox', { name: 'Name*' }).fill(values[0]);
    await adminPage.getByRole('spinbutton', { name: 'Extra Price*' }).fill('1000');

    await adminPage.getByRole('button', { name: 'Add to options' }).click();
    await adminPage.getByRole('textbox', { name: 'Name*' }).nth(2).fill(values[1]);
    await adminPage.getByRole('spinbutton', { name: 'Extra Price*' }).nth(1).fill('2000');

    await adminPage.getByRole('button', { name: 'Add to options' }).click();
    await adminPage.getByRole('textbox', { name: 'Name*' }).nth(3).fill(values[2]);
    await adminPage.getByRole('spinbutton', { name: 'Extra Price*' }).nth(2).fill('1300');

    /**
     * Clicking on Create button
     */
    await adminPage.getByRole('button', { name: 'Create' }).nth(1).click();

    /**
     * Waiting for success message
     */
    await expect(adminPage.getByRole('heading', { name: 'Attribute created' })).toBeVisible();
}

test.describe("Customers management", () => {
    test("should create a new customer", async ({ adminPage }) => {
        await createCustomer(adminPage);
    });

    test("should create a new product", async ({ adminPage }) => {
        await createProduct(adminPage);
    });

    test("should create a new invoice", async ({ adminPage }) => {
        await createInvoice(adminPage);
    });

    test("should create a new credit note", async ({ adminPage }) => {
        await createCreditNote(adminPage);
    });

    test("should create a new payment", async ({ adminPage }) => {
        await createCustomersPayment(adminPage);
    });
});


test.describe("Vendors management", () => {
    test("should create a new vendor", async ({ adminPage }) => {
        await createVendor(adminPage);
    });

    test("should create a new bills", async ({ adminPage }) => {
        await createBill(adminPage);
    });

    test("should create a new refund", async ({ adminPage }) => {
        await createRefunds(adminPage);
    });

    test("should create a new payment", async ({ adminPage }) => {
        await createVendorsPayment(adminPage);
    });
});


test.describe("Invoices configuration", () => {
    test("should create a new Bank Account", async ({ adminPage }) => {
        await createBankAccount(adminPage);
    });

    test("should create a new incoterms", async ({ adminPage }) => {
        await createIncoterms(adminPage);
    });

    test("should create a new payment term", async ({ adminPage }) => {
        await createPaymentTerm(adminPage);
    });

    test("should create a new tax groups", async ({ adminPage }) => {
        await createTaxGroups(adminPage);
    });

    test("should create a new taxs ", async ({ adminPage }) => {
        await createTaxes(adminPage);
    });

    test("should create a new attribute", async ({ adminPage }) => {
        await createAttribute(adminPage);
    });

});