import { Locator, Page } from "@playwright/test";

export class ErpLocators {

    readonly page: Page;

    /**
     *  Plugin Install/Uninstall
     */

    readonly pluginSyncButton: Locator;
    readonly pluginActionsButton: Locator;
    readonly pluginName: Locator;
    readonly pluginDropdownPanel: Locator;
    readonly pluginInstallOption: Locator;
    readonly pluginUninstallOption: Locator;
    readonly pluginInstallConfirmButton: Locator;
    readonly pluginUninstallConfirmButton: Locator;
    readonly pluginSearchInput: Locator;
    readonly pluginInstallSuccessNotification: Locator;
    readonly pluginUninstallSuccessNotification: Locator;
    readonly pluginActionFailedNotification: Locator;
    readonly pluginUninstallModalReady: Locator;
    readonly pluginsPerPageSelect: Locator;

    /**
     * Companies
     */

    readonly allCompaniesCount: Locator;
    readonly companiesMenuLink: Locator;
    readonly companiesTable: Locator;
    readonly companiesCreateButton: Locator;
    readonly companiesNameInput: Locator;
    readonly companiesEmailInput: Locator;
    readonly companiesPhoneInput: Locator;
    readonly companiesStatusToggleOn: Locator;
    readonly companiesStatusToggleOff: Locator;
    readonly companiesSaveButton: Locator;
    readonly companiesSearchInput: Locator;
    readonly companiesRowActionsButton: Locator;
    readonly companiesEditButton: Locator;
    readonly companiesDeleteButton: Locator;
    readonly selectAllCompaniesButton: Locator;
    readonly bulkActionsButton: Locator;
    readonly forceDeleteButton: Locator;
    readonly companiesConfirmDeleteButton: Locator;
    readonly companiesStatusToggle: Locator;
    readonly companiesSuccessToast: Locator;
    readonly companiesErrorToast: Locator;
    readonly companiesFeildValidationMessage: Locator;
    readonly companiesValidationMessage: Locator;

    /**
     * Users
     */

    readonly usersMenuLink: Locator;
    readonly allUsersCount: Locator;
    readonly usersTable: Locator;
    readonly usersCreateButton: Locator;
    readonly usersInviteButton: Locator;
    readonly usersNameInput: Locator;
    readonly usersEmailInput: Locator;
    readonly usersPasswordInput: Locator;
    readonly usersPasswordConfirmationInput: Locator;
    readonly usersRoleSelect: Locator;
    readonly usersCompanySelect: Locator;
    readonly usersCompanySearchInput: Locator;
    readonly usersRoleOption: Locator;
    readonly usersCompanyOption: Locator;
    readonly usersSaveButton: Locator;
    readonly usersSearchInput: Locator;
    readonly usersRowActionsButton: Locator;
    readonly usersEditButton: Locator;
    readonly usersDeleteButton: Locator;
    readonly usersConfirmDeleteButton: Locator;
    readonly selectAllUsersButton: Locator;
    readonly usersBulkActionsButton: Locator;
    readonly usersForceDeleteButton: Locator;
    readonly usersStatusToggle: Locator;
    readonly usersCreateStatusToggle: Locator;
    readonly usersResetPasswordButton: Locator;
    readonly usersChangePasswordInput: Locator;
    readonly usersChangePasswordConfirmationInput: Locator;
    readonly usersChangePasswordSaveButton: Locator;
    readonly userMenuButton: Locator;
    readonly logoutButton: Locator;
    readonly usersSuccessToast: Locator;
    readonly usersErrorToast: Locator;
    readonly userFeildValidationMessage: Locator;
    readonly usersValidationMessage: Locator;
    readonly manageUsersEnableResetCard: Locator;
    readonly manageUsersEnableResetToggle: Locator;
    readonly manageUsersEnableInvitationToggle: Locator;
    readonly settingsSaveButton: Locator;

    /**
     * Sales - Customers, Products, Quotations
     */

    readonly salesCustomersTable: Locator;
    readonly salesCustomerCreateButton: Locator;
    readonly salesCustomerNewCreateButton: Locator;
    readonly salesCustomerNameInput: Locator;
    readonly salesCustomerEmailInput: Locator;
    readonly salesCustomerSaveButton: Locator;
    readonly salesCustomerSearchInput: Locator;
    readonly salesCustomerEditButton: Locator;
    readonly salesCustomerDeleteButton: Locator;


    readonly salesProductsTable: Locator;
    readonly salesProductNewCreateButton: Locator;
    readonly salesProductNameInput: Locator;
    readonly salesProductCategorySelect: Locator;
    readonly salesProductPriceInput: Locator;
    readonly salesProductInvoicePolicySelect: Locator;
    readonly salesProductUomSelect: Locator;
    readonly salesProductSaveButton: Locator;
    readonly salesProductCreateButton: Locator;
    readonly salesProductEditButton: Locator;
    readonly salesProductDeleteButton: Locator;

    readonly salesQuotationCreateButton: Locator;
    readonly salesQuotationEditButton: Locator;
    readonly salesQuotationCustomerSelect: Locator;
    readonly salesQuotationPaymentTermSelect: Locator;
    readonly salesQuotationAddProductButton: Locator;
    readonly salesQuotationProductSelectInput: Locator;
    readonly salesQuotationQuantityInput: Locator;
    readonly salesQuotationSaveButton: Locator;
    readonly salesQuotationDeleteButton: Locator;
    readonly salesQuotationConfirmButton: Locator;
    readonly salesQuotationSendButton: Locator;
    readonly salesQuotationSendSubmitButton: Locator;
    readonly salesQuotationSentRadio: Locator;
    readonly salesQuotationCreateInvoiceButton: Locator;
    readonly salesQuotationInvoiceSubmitButton: Locator;
    readonly salesQuotationDeliveriesTable: Locator;
    readonly salesQuotationDeliveryEditButton: Locator;
    readonly salesDeliveryValidateButton: Locator;
    readonly salesDeliveryNoBackorderButton: Locator;
    readonly salesInvoicesTable: Locator;

    readonly salesSearchInput: Locator;
    readonly salesRowActionsButton: Locator;
    readonly salesEditAction: Locator;
    readonly salesDeleteAction: Locator;
    readonly salesConfirmDeleteButton: Locator;

    readonly salesSelectSearchInput: Locator;
    readonly salesSelectOption: Locator;
    readonly salesSuccessToast: Locator;
    readonly salesValidationMessage: Locator;

    /**
     * Purchases - Vendors, Products, Quotations, Agreements
     */

    readonly purchaseAgreementSettingsToggle: Locator;

    readonly purchaseVendorsTable: Locator;
    readonly purchaseVendorNewCreateButton: Locator;
    readonly purchaseVendorNameInput: Locator;
    readonly purchaseVendorEmailInput: Locator;
    readonly purchaseVendorSaveButton: Locator;
    readonly purchaseVendorSearchInput: Locator;
    readonly purchaseVendorEditButton: Locator;
    readonly purchaseVendorDeleteButton: Locator;


    readonly purchaseProductsTable: Locator;
    readonly purchaseProductNewCreateButton: Locator;
    readonly purchaseProductCreateButton: Locator;
    readonly purchaseProductEditButton: Locator;
    readonly purchaseProductNameInput: Locator;
    readonly purchaseProductPriceInput: Locator;
    readonly purchaseProductSaveButton: Locator;
    readonly purchaseProductSearchInput: Locator;
    readonly purchaseProductDeleteButton: Locator;

    readonly purchaseQuotationsTable: Locator;
    readonly purchaseQuotationCreateButton: Locator;
    readonly purchaseQuotationEditButton: Locator;
    readonly purchaseQuotationVendorSelect: Locator;
    readonly purchaseQuotationAgreementSelect: Locator;
    readonly purchaseQuotationAddProductButton: Locator;
    readonly purchaseQuotationProductSelect: Locator;
    readonly purchaseQuotationQuantityInput: Locator;
    readonly purchaseQuotationUnitPriceInput: Locator;
    readonly purchaseQuotationSaveButton: Locator;
    readonly purchaseQuotationSavechangesButton: Locator;
    readonly purchaseQuotationDeleteButton: Locator;
    readonly purchaseQuotationConfirmButton: Locator;
    readonly purchaseQuotationBillsTable: Locator;
    readonly purchaseQuotationReceiptsTable: Locator;
    readonly purchaseQuotationValidationMessage: Locator;

    readonly purchaseOrdersTable: Locator;
    readonly purchaseOrderSearchInput: Locator;

    readonly purchaseAgreementTable: Locator;
    readonly purchaseAgreementCreateButton: Locator;
    readonly purchaseAgreementVendorSelect: Locator;
    readonly purchaseAgreementTypeSelect: Locator;
    readonly purchaseAgreementReferenceInput: Locator;
    readonly purchaseAgreementProductSelect: Locator;
    readonly purchaseAgreementQuantityInput: Locator;
    readonly purchaseAgreementUnitPriceInput: Locator;
    readonly purchaseAgreementEditButton: Locator;
    readonly purchaseAgreementDeleteButton: Locator;
    readonly purchaseAgreementSaveButton: Locator;
    readonly purchaseAgreementConfirmButton: Locator;
    readonly purchaseAgreementSearchInput: Locator;
    readonly purchaseAgreementValidationMessage: Locator;

    readonly purchaseSearchInput: Locator;
    readonly purchaseRowActionsButton: Locator;
    readonly purchaseEditAction: Locator;
    readonly purchaseDeleteAction: Locator;
    readonly purchaseConfirmDeleteButton: Locator;
    readonly purchaseDialogConfirmButton: Locator;
    readonly purchaseAgreementConfirmedRadio: Locator;
    readonly purchaseSuccessToast: Locator;
    readonly purchaseValidationMessage: Locator;


    /* 
     *   Website Pages
     */

    readonly websitePagesHeading: Locator;
    readonly websitePagesTable: Locator;
    readonly websitePagesCreateButton: Locator;
    readonly websitePagesTitleInput: Locator;
    readonly websitePagesSlugInput: Locator;
    readonly websitePagesContentInput: Locator;
    readonly websitePagesEditableContent: Locator;
    readonly websitePagesMetaTitleInput: Locator;
    readonly websitePagesMetaKeywordsInput: Locator;
    readonly websitePagesMetaDescriptionInput: Locator;
    readonly websitePagesHeaderVisibleToggle: Locator;
    readonly websitePagesFooterVisibleToggle: Locator;
    readonly websitePagesSaveButton: Locator;
    readonly websitePagesSearchInput: Locator;
    readonly websitePagesRowActionsButton: Locator;
    readonly websitePagesEditButton: Locator;
    readonly websitePagesEditLink: Locator;
    readonly websitePagesEditActionButton: Locator;
    readonly websitePagesDeleteButton: Locator;
    readonly websitePagesDeleteLink: Locator;
    readonly websitePagesDeleteActionButton: Locator;
    readonly websitePagesConfirmDeleteButton: Locator;
    readonly websitePagesSuccessToast: Locator;

    /**
     * Website Blog Categories
     */

    readonly blogCategoriesHeading: Locator;
    readonly blogCategoriesTable: Locator;
    readonly blogCategoriesCreateButton: Locator;
    readonly deleteBlogCategoryRowButton: Locator;
    readonly blogCategoriesNameInput: Locator;
    readonly blogCategoriesSlugInput: Locator;
    readonly blogCategoriesSubTitleInput: Locator;
    readonly blogCategoriesSearchInput: Locator;
    readonly blogCategoriesSaveButton: Locator;
    readonly blogCategoriesConfirmDeleteButton: Locator;
    readonly blogCategoriesSuccessToast: Locator;

    /**
     * Website Blog Posts
     */

    readonly blogPostsHeading: Locator;
    readonly blogPostsTable: Locator;
    readonly blogPostsCreateButton: Locator;
    readonly blogPostsTitleInput: Locator;
    readonly blogPostsSlugInput: Locator;
    readonly blogPostsSubTitleInput: Locator;
    readonly blogPostsContentInput: Locator;
    readonly blogPostsDeleteButton: Locator;
    readonly blogPostsEditableContent: Locator;
    readonly blogPostsMetaTitleInput: Locator;
    readonly blogPostsMetaKeywordsInput: Locator;
    readonly blogPostsMetaDescriptionInput: Locator;
    readonly blogPostsCategorySelect: Locator;
    readonly blogPostsSearchInput: Locator;
    readonly blogPostsSaveButton: Locator;
    readonly blogPostsConfirmDeleteButton: Locator;
    readonly blogPostsSuccessToast: Locator;

    constructor(page: Page) {
        this.page = page;

        /**
         *  Plugin Install/Uninstall  
         */

        this.pluginSyncButton = page.getByRole('button', { name: 'Sync Available Plugins' });
        this.pluginActionsButton = page.locator('button[title="Actions"]');
        this.pluginName = page.locator('.fi-size-lg.fi-font-semibold.fi-ta-text-item.fi-ta-text.fi-inline');
        // Only the open dropdown panel is visible, so the install/uninstall
        // options below always belong to the plugin whose menu was opened.
        this.pluginDropdownPanel = page.locator('.fi-dropdown-panel:visible');
        this.pluginInstallOption = this.pluginDropdownPanel.getByRole('button', { name: 'Install', exact: true });
        this.pluginUninstallOption = this.pluginDropdownPanel.getByRole('button', { name: 'Uninstall', exact: true });
        this.pluginInstallConfirmButton = page.getByRole('dialog').getByRole('button', { name: 'Install Plugin' });
        this.pluginUninstallConfirmButton = page.getByRole('dialog').getByRole('button', { name: 'Uninstall Plugin' });
        this.pluginSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.pluginInstallSuccessNotification = page.locator('h3.fi-no-notification-title', { hasText: 'Plugin Installed Successfully' }).first();
        this.pluginUninstallSuccessNotification = page.locator('h3.fi-no-notification-title', { hasText: 'Plugin Uninstalled Successfully' }).first();
        this.pluginActionFailedNotification = page.locator('h3.fi-no-notification-title', { hasText: /Installation Failed|Uninstallation Failed/ }).first();
        // The uninstall confirmation heading is rendered for every plugin, even
        // when the submit button is hidden, so it marks the modal as ready.
        this.pluginUninstallModalReady = page.getByRole('dialog').getByText('Uninstall Confirmation').first();
        this.pluginsPerPageSelect = page.locator('.fi-pagination-records-per-page-select-ctn select:visible');

        /**
         * Companies
         */

        this.allCompaniesCount = page.locator('span.fi-badge-label-ctn').nth(0);
        this.companiesMenuLink = page.getByRole("link", { name: /companies/i });
        this.companiesTable = page.locator("table");
        this.companiesCreateButton = page.locator("a,button").filter({ hasText: /new company|create company|add company|create/i }).first();
        this.companiesNameInput = page.locator('input[id="form.name"]').first();
        this.companiesEmailInput = page.locator('input[id="form.email"]').first();
        this.companiesPhoneInput = page.locator('input[id="form.phone"]').first();
        this.companiesStatusToggleOn = page.locator('button[aria-checked="true"]');
        this.companiesStatusToggleOff = page.locator('button[aria-checked="false"]');
        this.companiesSaveButton = page.locator('button[type="submit"]').nth(1);
        this.companiesSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.companiesRowActionsButton = page.locator('div.fi-ta-text-item').nth(0);
        this.companiesEditButton = page.locator("a.fi-ac-btn-action");
        this.companiesDeleteButton = page.locator("button.fi-ac-btn-action");
        this.selectAllCompaniesButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.bulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.forceDeleteButton = page.locator('span.fi-dropdown-list-item-label').nth(4);
        this.companiesConfirmDeleteButton = page.locator("button[x-data='filamentFormButton']");
        this.companiesStatusToggle = page.locator('button[role="switch"], input[type="checkbox"]').first();
        this.companiesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.companiesErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.companiesFeildValidationMessage = page.locator(".fi-fo-field-wrp-error-message", { hasText: /Company name already exists. Please use a unique name./ });
        this.companiesValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        /**
         * Users
         */

        this.usersMenuLink = page.getByRole("link", { name: /users/i });
        this.allUsersCount = page.locator('span.fi-badge-label-ctn').nth(0);
        this.usersTable = page.locator("table");
        this.usersCreateButton = page.locator("a,button").filter({ hasText: /new user|create user|add user|create/i }).first();
        this.usersInviteButton = page.locator("a,button").filter({ hasText: /invite user|user invitation|invite/i }).first();
        this.usersNameInput = page.locator('input[id="form.name"]');
        this.usersEmailInput = page.locator('input[id="form.email"]');
        this.usersPasswordInput = page.locator('input[id="form.password"]');
        this.usersPasswordConfirmationInput = page.locator('input[id="form.password_confirmation"]');
        this.usersRoleSelect = page.locator('div.fi-select-input-value-ctn').nth(0);
        this.usersCompanySelect = page.locator('div.fi-select-input-value-ctn').nth(5);
        this.usersCompanySearchInput = page.locator('input[type="search"], input[placeholder*="Search"], input[placeholder*="search"]');
        this.usersRoleOption = page.locator('[role="option"], .fi-select-option, li').filter({ hasText: /./ });
        this.usersCompanyOption = page.locator('[role="option"], .fi-select-option, li').filter({ hasText: /./ });
        this.usersSaveButton = page.locator('button[x-data="filamentFormButton"]');
        this.usersSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.usersRowActionsButton = page.locator('div.fi-ta-text-item').nth(0);
        this.usersEditButton = page.locator("a.fi-ac-btn-action").nth(0);
        this.usersDeleteButton = page.locator("button.fi-ac-btn-action");
        this.usersConfirmDeleteButton = page.getByRole('dialog').getByRole('button', { name: 'Delete' });
        this.selectAllUsersButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.usersBulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.usersForceDeleteButton = page.locator('span.fi-dropdown-list-item-label').nth(3);
        this.usersStatusToggle = page.locator('button[role="switch"], input[type="checkbox"]').first();
        this.usersCreateStatusToggle = page.locator('button.fi-fo-toggle');
        this.usersResetPasswordButton = page.locator("button,a").filter({ hasText: /Change Password/i }).first();
        this.usersChangePasswordInput = page.getByRole('textbox', { name: 'New Password*' });
        this.usersChangePasswordConfirmationInput = page.getByRole('textbox', { name: 'Confirm New Password' });
        this.usersChangePasswordSaveButton = page.getByRole('button', { name: 'Submit' });
        this.userMenuButton = page.locator('button[aria-label="User menu"]');
        this.logoutButton = page.getByRole('textbox', { name: 'Confirm New Password' });
        this.usersSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.usersErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.userFeildValidationMessage = page.locator(".fi-fo-field-wrp-error-message", { hasText: /The email has already been taken./ });
        this.usersValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");
        this.manageUsersEnableResetCard = page
            .locator("div,section,li,fieldset")
            .filter({ hasText: /Enable Reset Password|Allow users to reset their password/i })
            .first();
        this.manageUsersEnableResetToggle = page.getByRole("switch", { name: /Enable Reset Password/i });
        this.manageUsersEnableInvitationToggle = page.getByRole("switch", { name: /Enable User Invitation/i });
        this.settingsSaveButton = page.getByRole("button", { name: /Save changes|save|update|submit/i }).first();

        /**
         * Sales - Customers, Products, Quotations
         */

        this.salesCustomersTable = page.locator("div.fi-ta-content-grid, div.fi-ta-empty-state, table");
        this.salesCustomerNewCreateButton = page.locator("a,button").filter({ hasText: /new customer|create customer|add customer|create/i }).first();
        this.salesCustomerNameInput = page.locator('input[id="form.name"]').first();
        this.salesCustomerEmailInput = page.locator('input[id="form.email"]').first();
        this.salesCustomerCreateButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesCustomerSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.salesCustomerDeleteButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesCustomerSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.salesCustomerEditButton = page.getByRole('link', { name: 'Edit' }).first();

        this.salesProductsTable = page.locator("table, div.fi-ta-empty-state");
        this.salesProductNewCreateButton = page.locator("a,button").filter({ hasText: /new product|create product|add product|create/i }).first();
        this.salesProductNameInput = page.locator('input[id="form.name"]').first();
        this.salesProductCategorySelect = page.locator('input[id="form.category_id"], [role="combobox"][aria-label*="Category"], [role="combobox"][aria-labelledby*="Category"]').first();
        this.salesProductPriceInput = page.locator('input[id="form.price"]').first();
        this.salesProductInvoicePolicySelect = page.locator('select[id="form.invoice_policy"]').first();
        this.salesProductUomSelect = page.locator('input[id="form.uom_id"], [role="combobox"][aria-label*="UOM"], [role="combobox"][aria-labelledby*="UOM"]').first();
        this.salesProductCreateButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesProductEditButton = page.getByRole('link', { name: 'Edit' });
        this.salesProductSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.salesProductDeleteButton = page.getByRole('button', { name: 'Delete' });

        this.salesQuotationCreateButton = page.locator("a,button").filter({ hasText: /new quotation|create quotation|add quotation|create/i }).first();
        this.salesQuotationEditButton = page.getByRole('link', { name: 'Edit' }).first();
        this.salesQuotationCustomerSelect = page.locator('[wire\\:key$="form.partner_id"] button.fi-select-input-btn').first();
        this.salesQuotationPaymentTermSelect = page.locator('[wire\\:key$="form.payment_term_id"] button.fi-select-input-btn').first();
        this.salesQuotationAddProductButton = page.getByRole("button", { name: /Add Product/i }).first();
        this.salesQuotationProductSelectInput = page.locator('[wire\\:key*=".form.products."][wire\\:key*=".product_id."] button.fi-select-input-btn');
        this.salesQuotationQuantityInput = page.locator('input[id^="form.products."][id$=".product_qty"]');
        this.salesQuotationDeleteButton = page.getByRole('button', { name: 'Delete' }).first();
        this.salesQuotationSaveButton = page.locator('button[type="submit"]').filter({ hasText: /^\s*(Create|Save changes|Submit)\s*$/i }).first();
        this.salesQuotationConfirmButton = page.getByRole("button", { name: /Confirm/i }).first();
        this.salesQuotationSendButton = page.getByRole("button", { name: /Send by Email|Send/i }).first();
        this.salesQuotationSendSubmitButton = page.getByRole("dialog").getByRole("button", { name: /Send|Submit/i }).first(); 
        this.salesQuotationSentRadio = page.getByRole("radio", { name: /Quotation Sent/i });
        this.salesQuotationCreateInvoiceButton = page.getByRole("button", { name: /Create Invoice/i }).first();
        this.salesQuotationInvoiceSubmitButton = page.getByRole("dialog").getByRole("button", { name: /^(Submit|Confirm|Create Invoice)$/i }).first();
        this.salesQuotationDeliveriesTable = page.locator("table, div.fi-ta-empty-state");
        this.salesQuotationDeliveryEditButton = page.getByRole('table').getByRole('link', { name: 'Edit' });
        this.salesDeliveryValidateButton = page.getByRole("button", { name: /Validate/i }).first();
        this.salesDeliveryNoBackorderButton = page.getByRole("button", { name: /No Backorder/i }).first();
        this.salesInvoicesTable = page.locator("table, div.fi-ta-empty-state");

        this.salesSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.salesRowActionsButton = page.getByRole('button', { name: 'Actions' });
        this.salesEditAction = page.getByRole("menuitem", { name: /Edit/i }).first();
        this.salesDeleteAction = page.getByRole("menuitem", { name: /Delete/i }).first();
        this.salesConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();

        this.salesSelectSearchInput = page.locator('.fi-dropdown-panel[role="listbox"]:visible input.fi-input[aria-label="Search"]').last();
        this.salesSelectOption = page.locator('.fi-dropdown-panel[role="listbox"]:visible [role="option"]');
        this.salesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.salesValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        /**
         * Purchases - Vendors, Products, Quotations, Agreements
         */

        this.purchaseAgreementSettingsToggle = page.getByRole("switch", { name: /Purchase Agreements/i });

        this.purchaseVendorsTable = page.locator("div.fi-ta-content-grid, div.fi-ta-empty-state, table");
        this.purchaseVendorNewCreateButton = page.locator("a,button").filter({ hasText: /new vendor|create vendor|add vendor|create/i }).first();
        this.purchaseVendorNameInput = page.locator('input[id="form.name"]').first();
        this.purchaseVendorEmailInput = page.locator('input[id="form.email"]').first();
        this.purchaseVendorEditButton = page.getByRole('link', { name: 'Edit' }).first();
        this.purchaseVendorDeleteButton = page.locator('button[id="key-bindings-1"]').first();
        this.purchaseVendorSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.purchaseVendorSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);

        this.purchaseProductsTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseProductNewCreateButton = page.locator("a,button").filter({ hasText: /new product|create product|add product|create/i }).first();
        this.purchaseProductCreateButton = page.locator('button[id="key-bindings-1"]').first();
        this.purchaseProductEditButton = page.getByRole('link', { name: 'Edit' });
        this.purchaseProductNameInput = page.locator('input[id="form.name"]').first();
        this.purchaseProductPriceInput = page.locator('input[id="form.price"]').first();
        this.purchaseProductSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.purchaseProductSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.purchaseProductDeleteButton = page.getByRole('button', { name: 'Delete' });

        this.purchaseQuotationsTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseQuotationCreateButton = page.locator("a,button").filter({ hasText: /new request for quotation|New RFQ|create quotation|add quotation|create/i }).first();
        this.purchaseQuotationEditButton = page.getByRole('link', { name: 'Edit' }).first();
        this.purchaseQuotationDeleteButton = page.getByRole('button', { name: 'Delete' }).first();
        this.purchaseQuotationVendorSelect = page.locator('[wire\\:key$="form.partner_id"] button.fi-select-input-btn').first();
        this.purchaseQuotationAgreementSelect = page.locator('[wire\\:key$="form.requisition_id"] button.fi-select-input-btn').first();
        this.purchaseQuotationAddProductButton = page.getByRole("button", { name: /Add Product/i }).first();
        this.purchaseQuotationProductSelect = page.locator('[wire\\:key*=".form.products."][wire\\:key*=".product_id."] button.fi-select-input-btn');
        this.purchaseQuotationQuantityInput = page.locator('input[id^="form.products."][id$=".product_qty"]');
        this.purchaseQuotationUnitPriceInput = page.locator('input[id^="form.products."][id$=".price_unit"]');
        // this.purchaseQuotationSaveButton = page.getByRole('button', { name: /^(Create|Save changes|Submit)$/i }).first();
        this.purchaseQuotationSaveButton = page.locator('#key-bindings-1');
        this.purchaseQuotationSavechangesButton = page.getByRole('button', { name: 'Save changes' });
        this.purchaseQuotationConfirmButton = page.getByRole("button", { name: /Confirm Order|Confirm/i }).first();
        this.purchaseQuotationBillsTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseQuotationReceiptsTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseQuotationValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        this.purchaseOrdersTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseOrderSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);

        this.purchaseAgreementTable = page.locator("table, div.fi-ta-empty-state");
        this.purchaseAgreementCreateButton = page.locator("a,button").filter({ hasText: /new purchase agreement|create purchase agreement|add purchase agreement|create/i }).first();
        this.purchaseAgreementVendorSelect = page.locator('[wire\\:key$="form.partner_id"] button.fi-select-input-btn').first();
        this.purchaseAgreementTypeSelect = page.getByRole("combobox", { name: /Agreement Type/i }).first();
        this.purchaseAgreementReferenceInput = page.getByRole("textbox", { name: /Reference/i }).first();
        this.purchaseAgreementProductSelect = page.locator('[wire\\:key*=".form.lines."][wire\\:key*=".product_id."] button.fi-select-input-btn');
        this.purchaseAgreementQuantityInput = page.locator('input[id^="form.lines."][id$=".qty"]');
        this.purchaseAgreementUnitPriceInput = page.locator('input[id^="form.lines."][id$=".price_unit"]');
        this.purchaseAgreementEditButton = page.getByRole('link', { name: 'Edit' });
        this.purchaseAgreementDeleteButton = page.getByRole('button', { name: 'Delete' });
        this.purchaseAgreementSaveButton = page.getByRole("button", { name: /^(Create|Save changes|Submit)$/i }).first();
        this.purchaseAgreementConfirmButton = page.getByRole("button", { name: /^Confirm$/i }).first();
        this.purchaseAgreementSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.purchaseAgreementValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        this.purchaseSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.purchaseRowActionsButton = page.getByRole("button", { name: "Actions" });
        this.purchaseEditAction = page.getByRole("menuitem", { name: /Edit/i }).first();
        this.purchaseDeleteAction = page.getByRole("menuitem", { name: /Delete/i }).first();
        this.purchaseConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();
        this.purchaseDialogConfirmButton = page.getByRole("dialog").getByRole("button", { name: /Confirm/i }).first();
        this.purchaseAgreementConfirmedRadio = page.getByRole("radio", { name: /Confirmed/i }).first();
        this.purchaseSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.purchaseValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        /**
         * Website Pages
         */

        this.websitePagesHeading = page.locator("h1, h2, h3").filter({ hasText: /pages/i }).first();
        this.websitePagesTable = page.locator("table, div.fi-ta-empty-state").first();
        this.websitePagesCreateButton = page.locator("a,button").filter({ hasText: /new page|create page|add page|create/i }).first();
        this.websitePagesTitleInput = page.locator('input[id="form.title"]');
        this.websitePagesSlugInput = page.locator('input[id="form.slug"]');
        this.websitePagesContentInput = page.locator('textarea[id="form.content"], input[id="form.content"], [id="form.content"]');
        this.websitePagesEditableContent = page.locator('[contenteditable="true"]');
        this.websitePagesMetaTitleInput = page.locator('input[id="form.meta_title"]');
        this.websitePagesMetaKeywordsInput = page.locator('input[id="form.meta_keywords"], input[name="form.meta_keywords"]');
        this.websitePagesMetaDescriptionInput = page.locator('textarea[id="form.meta_description"]');
        this.websitePagesHeaderVisibleToggle = page.getByRole('switch', { name: 'Is Visible Header Menu' });
        this.websitePagesFooterVisibleToggle = page.getByRole('switch', { name: 'Is Visible Footer Menu' });
        this.websitePagesSaveButton = page.getByRole("button", { name: /save|create|submit/i }).first();
        this.websitePagesSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.websitePagesRowActionsButton = page.locator("div.fi-ta-text-item").first();
        this.websitePagesEditButton = page.getByRole('tab', { name: 'Edit' });
        this.websitePagesEditLink = page.getByRole("link", { name: /edit/i }).first();
        this.websitePagesEditActionButton = page.getByRole("button", { name: /edit/i }).first();
        this.websitePagesDeleteButton = page.getByRole("menuitem", { name: /delete/i }).first();
        this.websitePagesDeleteLink = page.getByRole("link", { name: /delete/i }).first();
        this.websitePagesDeleteActionButton = page.getByRole("button", { name: /delete/i }).first();
        this.websitePagesConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /delete/i }).first();
        this.websitePagesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();

        /**
         * Website Blog Categories
         */

        this.blogCategoriesHeading = page.locator("h1, h2, h3").filter({ hasText: /categories/i }).first();
        this.blogCategoriesTable = page.locator("table, div.fi-ta-empty-state").first();
        this.blogCategoriesCreateButton = page.locator("a,button").filter({ hasText: /new category|create category|add category|create/i }).first();
        this.deleteBlogCategoryRowButton = page.locator('button.fi-ac-link-action').nth(1); 
        this.blogCategoriesNameInput = page.getByRole("textbox", { name: /^Name/ }).first();
        this.blogCategoriesSlugInput = page.getByRole("textbox", { name: /^Slug$/ }).first();
        this.blogCategoriesSubTitleInput = page.getByRole("textbox", { name: /^Sub Title$/ }).first();
        this.blogCategoriesSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.blogCategoriesSaveButton = page.getByRole("button", { name: /create|save|submit/i }).last();
        this.blogCategoriesConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /delete/i }).first();
        this.blogCategoriesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();

        /**
         * Website Blog Posts
         */

        this.blogPostsHeading = page.locator("h1, h2, h3").filter({ hasText: /blog posts|posts/i }).first();
        this.blogPostsTable = page.locator("table, div.fi-ta-empty-state").first();
        this.blogPostsCreateButton = page.locator("a,button").filter({ hasText: /new post|create post|add post|create/i }).first();
        this.blogPostsDeleteButton = page.getByRole('button', { name: 'Delete' });
        this.blogPostsTitleInput = page.getByRole("textbox", { name: /^Title/ }).first();
        this.blogPostsSlugInput = page.getByRole("textbox", { name: /^Slug$/ }).first();
        this.blogPostsSubTitleInput = page.getByRole("textbox", { name: /^Sub Title$/ }).first();
        this.blogPostsContentInput = page.locator('textarea[id="form.content"], input[id="form.content"], [id="form.content"]').first();
        this.blogPostsEditableContent = page.locator('[contenteditable="true"]');
        this.blogPostsMetaTitleInput = page.getByRole("textbox", { name: /^Meta Title$/ }).first();
        this.blogPostsMetaKeywordsInput = page.getByRole("textbox", { name: /^Meta Keywords$/ }).first();
        this.blogPostsMetaDescriptionInput = page.getByRole("textbox", { name: /^Meta Description$/ }).first();
        this.blogPostsCategorySelect = page.locator("button").filter({ hasText: /^Select an option$/ }).first();
        this.blogPostsSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.blogPostsSaveButton = page.getByRole("button", { name: /create|save|submit/i }).last();
        this.blogPostsConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /delete/i }).first();
        this.blogPostsSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
    }
}
