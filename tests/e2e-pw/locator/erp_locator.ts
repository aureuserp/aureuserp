import { Locator, Page } from "@playwright/test";

export class ErpLocators {

    readonly page: Page;

    /**
     *  Plugin Install/Uninstall  
     */

    readonly pluginSyncButton: Locator;
    readonly pluginthreeDot: Locator;
    readonly pluginName : Locator;
    readonly pluginInstallButton: Locator;
    readonly pluginUninstallButton: Locator
    readonly pluginConfirmButton : Locator;
    readonly pluginSearchInput : Locator;
    readonly inventoryMoveProductSelectButton: Locator;
    readonly pluginCards : Locator;
    readonly pluginCardBadges : Locator;
    readonly pluginInstalledCards : Locator;
    readonly pluginNotInstalledCards : Locator;
    readonly pluginSuccessMessage : Locator;
    readonly pluginErrorMessage : Locator;

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
    readonly salesProductTrackingSelect: Locator;
    readonly salesQuotationOrderLineTab: Locator;
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
    readonly salesQuotationDeliveryRows: Locator;
    readonly salesQuotationDeliveryReferenceLinks: Locator;
    readonly salesQuotationWarehouseSelect: Locator;
    readonly salesQuotationOtherInformationTab: Locator;
    readonly salesQuotationDeliveredQuantityInputs: Locator;
    readonly salesQuotationLineTaxSelects: Locator;
    readonly salesQuotationLineSubtotalInputs: Locator;
    readonly salesQuotationSummaryItems: Locator;
    readonly salesDeliveryValidateButton: Locator;
    readonly salesDeliveryMarkAsTodoButton: Locator;
    readonly salesDeliveryCheckAvailabilityButton: Locator;
    readonly salesDeliveryNoBackorderButton: Locator;
    readonly salesDeliveryBackorderModal: Locator;
    readonly salesDeliveryBackorderConfirmButton: Locator;
    readonly salesDeliveryNextTransferButton: Locator;
    readonly salesDeliveryReturnButton: Locator;
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
     * Inventory - Settings (Operations, Products, Warehouses, Traceability, Logistics)
     */

    readonly inventoryManageOperationsToggleEnablePackages: Locator;
    readonly inventoryManageOperationsAnnualDay: Locator;
    readonly inventoryManageOperationsAnnualMonth: Locator;
    readonly inventoryManageProductsToggleEnableVariants: Locator;
    readonly inventoryManageProductsToggleEnableUom: Locator;
    readonly inventoryManageProductsToggleEnablePackagings: Locator;
    readonly inventoryManageWarehousesToggleEnableLocations: Locator;
    readonly inventoryManageWarehousesToggleEnableMultiSteps: Locator;
    readonly inventoryManageTraceabilityToggleEnableLots: Locator;
    readonly inventoryManageTraceabilityToggleDisplayOnDeliverySlips: Locator;
    readonly inventoryManageLogisticsToggleEnableDropshipping: Locator;
    readonly inventorySettingsSaveButton: Locator;

    /**
     * Inventory - Warehouses, Locations, Operation Types, Routes, Rules
     */

    readonly inventoryWarehouseCreateButton: Locator;
    readonly inventoryWarehouseNameInput: Locator;
    readonly inventoryWarehouseCodeInput: Locator;
    readonly inventoryWarehouseReceptionOneStep: Locator;
    readonly inventoryWarehouseReceptionTwoSteps: Locator;
    readonly inventoryWarehouseReceptionThreeSteps: Locator;
    readonly inventoryWarehouseDeliveryOneStep: Locator;
    readonly inventoryWarehouseDeliveryTwoSteps: Locator;
    readonly inventoryWarehouseDeliveryThreeSteps: Locator;
    readonly inventoryWarehouseSaveButton: Locator;
    readonly inventoryWarehouseEditSaveButton: Locator;
    readonly inventoryWarehouseTable: Locator;
    readonly inventoryWarehouseRowActions: Locator;
    readonly inventoryWarehouseEditAction: Locator;
    // readonly inventoryOpenWarehouseRow: Locator;
    readonly inventoryWarehouseDeleteAction: Locator;
    readonly inventoryWarehouseConfirmDeleteButton: Locator;

    readonly inventoryLocationsTable: Locator;
    readonly inventoryOperationTypesTable: Locator;
    readonly inventoryRoutesTable: Locator;
    readonly inventoryRulesTable: Locator;
    readonly inventoryStorageCategoriesTable: Locator;

    readonly inventoryStorageCategoryCreateButton: Locator;
    readonly inventoryLocationCreateButton: Locator;
    readonly inventoryRouteCreateButton: Locator;
    readonly inventoryOperationTypeCreateButton: Locator;
    readonly inventoryOperationTypeTypeSelect: Locator;
    readonly inventoryOperationTypeBackorderSelect: Locator;
    readonly inventoryOperationTypeReservationGroup: Locator;
    readonly inventoryOperationTypeWarehouseSelect: Locator;
    readonly inventoryOperationTypeReturnSelect: Locator;
    readonly inventoryOperationTypeUseCreateLotsToggle: Locator;
    readonly inventoryOperationTypeUseExistingLotsToggle: Locator;
    readonly inventoryLocationTypeSelect: Locator;
    readonly inventoryLocationParentSelect: Locator;
    readonly inventoryLocationStorageCategorySelect: Locator;
    readonly inventoryLocationIsScrapToggle: Locator;
    readonly inventoryPutawayRuleCreateButton: Locator;
    readonly inventoryConfigNameInput: Locator;
    readonly inventoryConfigSequenceCodeInput: Locator;
    readonly inventoryConfigSaveButton: Locator;

    /**
     * Inventory - Products
     */

    readonly inventoryProductCreateButton: Locator;
    readonly inventoryProductNameInput: Locator;
    readonly inventoryProductPriceInput: Locator;
    readonly inventoryProductSaveButton: Locator;
    readonly inventoryProductEditSaveButton: Locator;
    readonly inventoryProductQuantityEditableInput: Locator;
    readonly inventoryProductTable: Locator;
    readonly inventoryProductRowActions: Locator;
    readonly inventoryProductEditAction: Locator;
    readonly inventoryProductDeleteAction: Locator;
    readonly inventoryProductIsStorableToggle: Locator;
    readonly inventoryProductQuantitiesTab: Locator;
    readonly inventoryProductMovesTab: Locator;
    readonly inventoryProductQuantityCreateButton: Locator;
    readonly inventoryProductQuantityOpenModal: Locator;
    readonly inventoryProductQuantityProductSelect: Locator;
    readonly inventoryProductQuantityLocationSelect: Locator;
    readonly inventoryProductQuantityPackageSelect: Locator;
    readonly inventoryProductQuantityInput: Locator;
    readonly inventoryProductQuantityDialogCreate: Locator;
    readonly inventoryProductQuantityTableRows: Locator;
    readonly inventoryProductQuantityOnHandCells: Locator;
    readonly inventoryProductQuantityOnHandInlineInputs: Locator;
    readonly inventoryProductQuantityReservedCells: Locator;

    /**
     * Inventory - Operations (Receipts, Deliveries, Internals)
     */

    readonly inventoryOperationCreateButton: Locator;
    readonly inventoryOperationPartnerSelect: Locator;
    readonly inventoryOperationTypeSelect: Locator;
    readonly inventoryOperationSourceLocationSelect: Locator;
    readonly inventoryOperationDestinationLocationSelect: Locator;
    readonly inventoryOperationAddMoveButton: Locator;
    readonly inventoryOperationMoveProductSelect: Locator;
    readonly inventoryOperationMoveDemandInput: Locator;
    readonly inventoryOperationMoveQuantityInput: Locator;
    readonly inventoryOperationSaveButton: Locator;
    readonly inventoryOperationEditSaveButton: Locator;
    readonly inventoryOperationConfirmButton: Locator;
    readonly inventoryOperationMarkAsTodoButton: Locator;
    readonly inventoryOperationCheckAvailabilityButton: Locator;
    readonly inventoryOperationValidateButton: Locator;
    readonly inventoryOperationNoBackorderButton: Locator;
    readonly inventoryOperationBackorderModal: Locator;
    readonly inventoryOperationBackorderConfirmButton: Locator;
    readonly inventoryOperationOriginInput: Locator;
    readonly inventoryOperationAdditionalTab: Locator;
    readonly inventoryOperationNextTransferButton: Locator;
    readonly inventoryOperationReturnButton: Locator;
    readonly inventoryInfolistEntries: Locator;
    readonly inventoryReturnModal: Locator;
    readonly inventoryReturnModalQtyInput: Locator;
    readonly inventoryReturnModalSubmitButton: Locator;
    readonly inventoryOperationStateBadge: Locator;
    readonly inventoryOperationTable: Locator;
    readonly inventoryOperationRowActions: Locator;
    readonly inventoryOperationEditAction: Locator;
    readonly inventoryOperationDeleteAction: Locator;

    /**
     * Inventory - Generic UI helpers
     */

    readonly inventorySearchInput: Locator;
    readonly inventorySelectSearchInput: Locator;
    readonly inventorySelectOption: Locator;
    readonly inventorySuccessToast: Locator;
    readonly inventoryErrorToast: Locator;
    readonly inventoryValidationMessage: Locator;
    readonly inventoryConfirmDialogButton: Locator;
    readonly inventoryTableRows: Locator;
    readonly inventoryPageHeading: Locator;
    readonly inventorySelectPanel: Locator;

    /**
     * Inventory - Traceability (product lot/serial tracking)
     */

    readonly inventoryProductTrackingSelect: Locator;
    readonly inventoryMoveManageLinesAction: Locator;
    readonly inventoryMoveLinesModal: Locator;
    readonly inventoryMoveGenerateLotsAction: Locator;
    readonly inventoryMoveLinesFirstLotInput: Locator;
    readonly inventoryMoveLinesQuantityReceivedInput: Locator;
    readonly inventoryMoveLinesGenerateSubmit: Locator;
    readonly inventoryMoveLinesModalSaveButton: Locator;
    readonly inventoryMoveLinesResultPackageSelect: Locator;

    /**
     * Inventory - Packages & Package Types
     */

    readonly inventoryPackageTypeCreateButton: Locator;
    readonly inventoryPackageTypeNameInput: Locator;
    readonly inventoryPackageTypeLengthInput: Locator;
    readonly inventoryPackageTypeWidthInput: Locator;
    readonly inventoryPackageTypeHeightInput: Locator;
    readonly inventoryPackageTypeBaseWeightInput: Locator;
    readonly inventoryPackageTypeMaxWeightInput: Locator;
    readonly inventoryPackageTypeSaveButton: Locator;
    readonly inventoryPackageTypeTable: Locator;
    readonly inventoryPackageCreateButton: Locator;
    readonly inventoryPackageNameInput: Locator;
    readonly inventoryPackageTypeSelect: Locator;
    readonly inventoryPackageLocationSelect: Locator;
    readonly inventoryPackageSaveButton: Locator;
    readonly inventoryPackageTable: Locator;
    readonly inventoryPackageDeleteAction: Locator;
    readonly inventoryPackageDefaultViewTab: Locator;

    readonly inventoryScrapCreateButton: Locator;
    readonly inventoryScrapProductSelect: Locator;
    readonly inventoryScrapQtyInput: Locator;
    readonly inventoryScrapSourceLocationSelect: Locator;
    readonly inventoryScrapDestinationLocationSelect: Locator;

    readonly inventoryQuantityCountedInput: Locator;
    readonly inventoryQuantityApplyAction: Locator;

   /**
    * Purchases - Vendors, Products, Quotations, Agreements 
    */
     
    readonly purchaseAgreementSettingsToggle: Locator;
    readonly purchaseVendorsTable: Locator;
    readonly purchaseVendorNewCreateButton: Locator;
    readonly purchaseVendorNameInput: Locator;
    readonly purchaseVendorEmailInput: Locator;
    readonly purchaseVendorSaveButton: Locator;
    readonly purchaseVendorCreateButton: Locator;
    readonly purchaseQuotationConfirmOrderButton: Locator;
    readonly purchaseQuotationCreateSubmitButton: Locator;
    readonly purchaseProductTrackingSelect: Locator;
    readonly purchaseQuotationOperationTypeSelect: Locator;
    readonly purchaseQuotationLineTaxSelects: Locator;
    readonly purchaseQuotationLineSubtotalInputs: Locator;
    readonly purchaseQuotationReceivedQuantityInputs: Locator;
    readonly purchaseQuotationSummaryItems: Locator;
    readonly purchaseQuotationReceiptRows: Locator;
    readonly purchaseQuotationReceiptReferenceLinks: Locator;
    readonly purchaseCreateBillButton: Locator;
    readonly purchaseBillSubmitButton: Locator;
    readonly purchaseBillsTableRows: Locator;
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

        this.pluginSyncButton = page.locator('text=Sync Available Plugins');
        this.pluginthreeDot = page.locator('button[title="Actions"]');
        this.pluginName = page.locator('.fi-size-lg.fi-font-semibold.fi-ta-text-item.fi-ta-text.fi-inline');
        // Every card keeps its own dropdown in the DOM, so these are scoped to the open one:
        // an unscoped match resolves to the first card's hidden button and never clicks.
        this.pluginInstallButton = page.locator('button.fi-color.fi-color-success.fi-text-color-700:visible');
        this.pluginUninstallButton = page.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item:visible');
        this.pluginConfirmButton = page.locator('span[x-show="! isProcessing"]');
        this.pluginSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        // The selected product of a move row. A row's own text also contains every option of
        // its product select, so matching a row on its text finds every row, not the right one.
        this.inventoryMoveProductSelectButton = page.locator('.fi-select-input-btn');

        this.pluginCards = page.locator('.fi-ta-record');
        this.pluginCardBadges = page.locator('.fi-ta-record .fi-badge');
        // Installing a plugin moves its card to the top of the list, so cards are selected
        // by their state badge rather than by position.
        this.pluginInstalledCards = page.locator('.fi-ta-record')
            .filter({ has: page.locator('.fi-badge', { hasText: /^\s*Installed\s*$/ }) });
        this.pluginNotInstalledCards = page.locator('.fi-ta-record')
            .filter({ has: page.locator('.fi-badge', { hasText: /^\s*Not Installed\s*$/ }) });
        this.pluginSuccessMessage = page.locator('h3.fi-no-notification-title');
        this.pluginErrorMessage = page.locator('.fi-toast-message-error');

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
        // "Track By" only renders once traceability is enabled and the product is storable.
        this.salesProductTrackingSelect = page.locator('select[id="form.tracking"]').first();
        this.salesQuotationOrderLineTab = page.getByRole("tab", { name: /Order Line/i }).first();
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
        // The sale order's Deliveries tab lists every operation linked to the order
        // (Pick, Pack and Ship for multi-step warehouses). Each row's Edit action lives
        // inside an "Actions" dropdown, so rows are opened through their reference link.
        this.salesQuotationDeliveryRows = page.locator("table tbody tr");
        this.salesQuotationDeliveryReferenceLinks = page.locator('table tbody tr a[href*="/deliveries/"]');
        this.salesQuotationWarehouseSelect = page.locator('[wire\\:key$="form.warehouse_id"] button.fi-select-input-btn').first();
        this.salesQuotationOtherInformationTab = page.getByRole("tab", { name: /Other Information/i }).first();
        this.salesQuotationDeliveredQuantityInputs = page.locator('input[id^="form.products."][id$=".qty_delivered"]');
        this.salesQuotationLineTaxSelects = page.locator('[wire\\:key*="form.products."][wire\\:key*=".taxes."] button.fi-select-input-btn');
        this.salesQuotationLineSubtotalInputs = page.locator('input[id^="form.products."][id$=".price_subtotal"]');
        // The order totals are a Livewire island, not form fields: one ".invoice-item" per
        // row ("Untaxed Amount", "Amount Tax", "Amount Total", "Margin"). The tax row is
        // only rendered when the tax is greater than zero.
        this.salesQuotationSummaryItems = page.locator(".invoice-item");
        this.salesDeliveryValidateButton = page.getByRole("button", { name: /Validate/i }).first();
        this.salesDeliveryMarkAsTodoButton = page.getByRole("button", { name: /Mark as Todo/i }).first();
        this.salesDeliveryCheckAvailabilityButton = page.getByRole("button", { name: /Check Availability/i }).first();
        this.salesDeliveryNoBackorderButton = page.getByRole("button", { name: /No Backorder/i }).first();
        this.salesDeliveryBackorderModal = page.getByRole("heading", { name: /Create Back Order/i }).first();
        this.salesDeliveryBackorderConfirmButton = page
            .getByRole("dialog")
            .filter({ hasText: /Create Back Order/i })
            .getByRole("button", { name: /^Confirm$/i })
            .first();
        // Only rendered while the operation has a downstream transfer still to process.
        this.salesDeliveryNextTransferButton = page.locator("a,button").filter({ hasText: /Next Transfer/i }).first();
        // Only rendered once the operation is validated (state Done).
        this.salesDeliveryReturnButton = page.getByRole("button", { name: /^Return$/i }).first();
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
         * Inventory - Settings (Operations, Products, Warehouses, Traceability, Logistics)
         */

        this.inventoryManageOperationsToggleEnablePackages = page.getByRole("switch", { name: /Storage Locations|Packages|Enable Packages/i }).first();
        this.inventoryManageOperationsAnnualDay = page.locator('input[id="form.annual_inventory_day"]');
        this.inventoryManageOperationsAnnualMonth = page.locator('[wire\\:key$="form.annual_inventory_month"] button.fi-select-input-btn').first();
        this.inventoryManageProductsToggleEnableVariants = page.getByRole("switch", { name: /Variants/i }).first();
        this.inventoryManageProductsToggleEnableUom = page.getByRole("switch", { name: /Units of Measure|UoM|UOM/i }).first();
        this.inventoryManageProductsToggleEnablePackagings = page.getByRole("switch", { name: /Packagings/i }).first();
        this.inventoryManageWarehousesToggleEnableLocations = page.getByRole("switch", { name: /Storage Locations|Locations/i }).first();
        this.inventoryManageWarehousesToggleEnableMultiSteps = page.getByRole("switch", { name: /Multi-Step Routes|Multi Step|Multi-step/i }).first();
        this.inventoryManageTraceabilityToggleEnableLots = page.getByRole("switch", { name: /Lots & Serial Numbers|Lots|Serial/i }).first();
        this.inventoryManageTraceabilityToggleDisplayOnDeliverySlips = page.getByRole("switch", { name: /Display Lots & Serial Numbers on Delivery Slips|Delivery Slips/i }).first();
        this.inventoryManageLogisticsToggleEnableDropshipping = page.getByRole("switch", { name: /Dropshipping/i }).first();
        this.inventorySettingsSaveButton = page.getByRole("button", { name: /Save changes|save|update|submit/i }).first();

        /**
         * Inventory - Warehouses, Locations, Operation Types, Routes, Rules
         */

        this.inventoryWarehouseCreateButton = page.locator("a,button").filter({ hasText: /new warehouse|create warehouse|add warehouse|create/i }).first();
        this.inventoryWarehouseNameInput = page.locator('input[id="form.name"]').first();
        this.inventoryWarehouseCodeInput = page.locator('input[id="form.code"]').first();
        this.inventoryWarehouseReceptionOneStep = page.getByRole("radio", { name: /One step/i }).first();
        this.inventoryWarehouseReceptionTwoSteps = page.getByRole("radio", { name: /Two steps/i }).first();
        this.inventoryWarehouseReceptionThreeSteps = page.getByRole("radio", { name: /Three steps/i }).first();
        this.inventoryWarehouseDeliveryOneStep = page.getByRole("radio", { name: /One step/i }).nth(1);
        this.inventoryWarehouseDeliveryTwoSteps = page.getByRole("radio", { name: /Two steps/i }).nth(1);
        this.inventoryWarehouseDeliveryThreeSteps = page.getByRole("radio", { name: /Three steps/i }).nth(1);
        this.inventoryWarehouseSaveButton = page.locator('button[id="key-bindings-1"]').first();
        // this.inventoryOpenWarehouseRow = page.
        this.inventoryWarehouseEditSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.inventoryWarehouseTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryWarehouseRowActions = page.getByRole("button", { name: "Actions" }).first();
        this.inventoryWarehouseEditAction = page.locator("a.fi-ac-link-action").nth(1);  
        this.inventoryWarehouseDeleteAction = page.getByRole("button", { name: /Delete/i }).first();
        this.inventoryWarehouseConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();

        this.inventoryLocationsTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryOperationTypesTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryRoutesTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryRulesTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryStorageCategoriesTable = page.locator("table, div.fi-ta-empty-state");

        this.inventoryStorageCategoryCreateButton = page.locator("a,button").filter({ hasText: /New Storage Category|create/i }).first();
        this.inventoryLocationCreateButton = page.locator("a,button").filter({ hasText: /New Location|create/i }).first();
        this.inventoryRouteCreateButton = page.locator("a,button").filter({ hasText: /New Route|create/i }).first();
        this.inventoryOperationTypeCreateButton = page.locator("a,button").filter({ hasText: /New Operation Type|create/i }).first();
        this.inventoryOperationTypeTypeSelect = page.locator('select[id="form.type"]').first();
        this.inventoryOperationTypeBackorderSelect = page.locator('select[id="form.create_backorder"]').first();
        this.inventoryOperationTypeWarehouseSelect = page.locator('[wire\\:key$="form.warehouse_id"] button.fi-select-input-btn').first();
        this.inventoryOperationTypeReturnSelect = page.locator('[wire\\:key$="form.return_operation_type_id"] button.fi-select-input-btn').first();
        this.inventoryOperationTypeUseCreateLotsToggle = page.locator('[wire\\:key$="form.use_create_lots"] button[role="switch"]').first();
        this.inventoryOperationTypeUseExistingLotsToggle = page.locator('[wire\\:key$="form.use_existing_lots"] button[role="switch"]').first();
        this.inventoryLocationTypeSelect = page.locator('select[id="form.type"]').first();
        this.inventoryLocationParentSelect = page.locator('[wire\\:key$="form.parent_id"] button.fi-select-input-btn').first();
        this.inventoryLocationStorageCategorySelect = page.locator('[wire\\:key$="form.storage_category_id"] button.fi-select-input-btn').first();
        this.inventoryPutawayRuleCreateButton = page.locator("a,button").filter({ hasText: /New Putaway Rule/i }).first();
        this.inventoryLocationIsScrapToggle = page.getByRole("switch", { name: /Scrap Location/i }).first();
        this.inventoryOperationTypeReservationGroup = page.locator(".fi-fo-radio").filter({ hasText: "Manual" }).first();
        this.inventoryConfigNameInput = page.locator('input[id="form.name"]').first();
        this.inventoryConfigSequenceCodeInput = page.locator('input[id="form.sequence_code"]').first();
        this.inventoryConfigSaveButton = page.locator('button[id="key-bindings-1"]').first();

        /**
         * Inventory - Products
         */

        this.inventoryProductCreateButton = page.locator("a,button").filter({ hasText: /new product|create product|add product|create/i }).first();
        this.inventoryProductNameInput = page.locator('input[id="form.name"]').first();
        this.inventoryProductPriceInput = page.locator('input[id="form.price"]').first();
        this.inventoryProductSaveButton = page.locator('button[id="key-bindings-1"]').first();
        this.inventoryProductEditSaveButton = page.getByRole("button", { name: /Save changes/i }).first();
        this.inventoryProductQuantityEditableInput = page.locator('table tbody tr input:not([type="hidden"])').first();
        this.inventoryProductTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryProductRowActions = page.getByRole("button", { name: "Actions" }).first();
        this.inventoryProductEditAction = page.getByRole("link", { name: /Edit/i }).first();
        this.inventoryProductDeleteAction = page.getByRole("button", { name: /Delete/i }).first();
        this.inventoryProductIsStorableToggle = page.getByRole("switch", { name: /Track Inventory|Storable/i }).first();
        this.inventoryProductQuantitiesTab = page.locator("a, li").filter({ hasText: /On Hand|Quantities/i }).first();
        this.inventoryProductMovesTab = page.getByRole('link', { name: 'IN/OUT' });
        this.inventoryProductQuantityCreateButton = page.getByRole('button', { name: 'Add Quantity' });
        // The open Filament action-modal window (`.fi-modal-window` is only
        // rendered when the modal is open). All on-hand quantity dialog
        // children are scoped to it.
        this.inventoryProductQuantityOpenModal = page.locator('.fi-modal-window:visible').first();
        this.inventoryProductQuantityDialogCreate = page
            .locator('.fi-modal-footer button.fi-btn, .fi-modal-window button[type="submit"]')
            .filter({ hasText: /Create|Save|Submit/i })
            .first();
        this.inventoryProductQuantityProductSelect = page
            .locator('.fi-modal-window:visible div.fi-fo-field, .fi-modal-window:visible div[data-field-wrapper]')
            .filter({ has: page.locator('label', { hasText: /^Product$/i }) })
            .locator('button.fi-select-input-btn')
            .first();
        this.inventoryProductQuantityLocationSelect = page
            .locator('.fi-modal-window:visible, .fi-modal:visible, [role="dialog"]:visible')
            .last()
            .locator('label:has-text("Location")')
            .locator('xpath=ancestor::div[@data-field-wrapper][1]')
            .locator('button.fi-select-input-btn')
            .first();
        this.inventoryProductQuantityPackageSelect = page
            .locator('.fi-modal-window:visible, .fi-modal:visible, [role="dialog"]:visible')
            .last()
            .locator('label:has-text("Package")')
            .locator('xpath=ancestor::div[@data-field-wrapper][1]')
            .locator('button.fi-select-input-btn')
            .first();
        this.inventoryProductQuantityInput = page
            .locator('.fi-modal-window:visible div.fi-fo-field, .fi-modal-window:visible div[data-field-wrapper]')
            .filter({ has: page.locator('label', { hasText: /On Hand Quantity|Quantity|On-hand/i }) })
            .locator('input')
            .first();
        this.inventoryProductQuantityTableRows = page.locator("table tbody tr");
        // Per-column cell locators for the product quantities table. The on-hand
        // column is rendered as Filament `TextInputColumn` (editable inline input);
        // the reserved column is a `TextColumn` (read-only text content).
        this.inventoryProductQuantityOnHandCells = page.locator("td.fi-ta-cell-quantity");
        this.inventoryProductQuantityOnHandInlineInputs = page.locator(
            "td.fi-ta-cell-quantity .fi-ta-text-input input:not([type=hidden])"
        );
        this.inventoryProductQuantityReservedCells = page.locator("td.fi-ta-cell-reserved-quantity");

        /**
         * Inventory - Operations (Receipts, Deliveries, Internals)
         */

        this.inventoryOperationCreateButton = page.locator("a,button").filter({ hasText: /new receipt|new delivery|new internal|new transfer|create receipt|create delivery|create internal|create transfer|create/i }).first();
        this.inventoryOperationPartnerSelect = page.locator('[wire\\:key$="form.partner_id"] button.fi-select-input-btn').first();
        this.inventoryOperationTypeSelect = page.locator('[wire\\:key$="form.operation_type_id"] button.fi-select-input-btn').first();
        this.inventoryOperationSourceLocationSelect = page.locator('[wire\\:key$="form.source_location_id"] button.fi-select-input-btn').first();
        this.inventoryOperationDestinationLocationSelect = page.locator('[wire\\:key$="form.destination_location_id"] button.fi-select-input-btn').first();
        this.inventoryOperationAddMoveButton = page.getByRole("button", { name: /Add a line|Add Move|Add Item|Add to Cart|Add product|Add/i }).first();
        this.inventoryOperationMoveProductSelect = page.locator('[wire\\:key*=".form.moves."][wire\\:key*=".product_id."] button.fi-select-input-btn');
        this.inventoryOperationMoveDemandInput = page.locator('input[id^="form.moves."][id$=".product_uom_qty"]');
        this.inventoryOperationMoveQuantityInput = page.locator('input[id^="form.moves."][id$=".quantity"]');
        this.inventoryOperationSaveButton = page.locator('button[id="key-bindings-1"]').first();
        this.inventoryOperationEditSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.inventoryOperationConfirmButton = page.getByRole("button", { name: /^Confirm$/i }).first();
        this.inventoryOperationMarkAsTodoButton = page.getByRole("button", { name: /Mark as Todo/i }).first();
        this.inventoryOperationCheckAvailabilityButton = page.getByRole("button", { name: /Check Availability/i }).first();
        this.inventoryOperationValidateButton = page.getByRole('button', { name: 'Validate' }).first();
        this.inventoryOperationNoBackorderButton = page.getByRole("button", { name: /No Backorder/i }).first();
        this.inventoryOperationBackorderModal = page.getByRole("heading", { name: /Create Back Order/i }).first();
        this.inventoryOperationBackorderConfirmButton = page
            .getByRole("dialog")
            .filter({ hasText: /Create Back Order/i })
            .getByRole("button", { name: /^Confirm$/i })
            .first();
        this.inventoryOperationOriginInput = page.locator('input[id="form.origin"]').first();
        this.inventoryOperationAdditionalTab = page.getByRole("tab", { name: /Additional/i }).first();
        this.inventoryOperationNextTransferButton = page.locator("a,button").filter({ hasText: /Next Transfer/i }).first();
        // The "Return" header action is only visible once an operation is validated (state DONE).
        this.inventoryOperationReturnButton = page.getByRole("button", { name: /^Return$/i }).first();
        // A Filament infolist entry (view page): each `.fi-in-entry` holds both its
        // label and value, so filtering by the label text reads a field's value.
        this.inventoryInfolistEntries = page.locator(".fi-in-entry");
        this.inventoryReturnModal = page.locator(".fi-modal-window:visible").filter({ hasText: /Quantity/i }).first();
        // Each returnable move renders one editable "Qty" spinbutton in the modal's repeater table.
        this.inventoryReturnModalQtyInput = page
            .locator(".fi-modal-window:visible")
            .getByRole("spinbutton", { name: /Qty/i });
        this.inventoryReturnModalSubmitButton = page
            .locator(".fi-modal-window:visible")
            .getByRole("button", { name: /^Submit$/i })
            .first();
        this.inventoryOperationStateBadge = page.locator('[wire\\:key$="form.state"], .fi-progress-stepper').first();
        this.inventoryOperationTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryOperationRowActions = page.getByRole("button", { name: "Actions" }).first();
        this.inventoryOperationEditAction = page.getByRole("link", { name: /Edit/i }).first();
        this.inventoryOperationDeleteAction = page.getByRole("button", { name: /Delete/i }).first();

        /**
         * Inventory - Generic UI helpers
         */

        this.inventorySearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.inventorySelectSearchInput = page.locator('.fi-dropdown-panel[role="listbox"]:visible input.fi-input[aria-label="Search"]').last();
        this.inventorySelectOption = page.locator('.fi-dropdown-panel[role="listbox"]:visible [role="option"]');
        this.inventorySuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.inventoryErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.inventoryValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");
        this.inventoryConfirmDialogButton = page.getByRole("dialog").getByRole("button", { name: /Confirm|Delete|Yes/i }).first();
        this.inventoryTableRows = page.locator("table tbody tr");
        this.inventoryPageHeading = page.locator("h1").first();
        this.inventorySelectPanel = page.locator('.fi-dropdown-panel[role="listbox"]:visible');

        /**
         * Inventory - Traceability (product lot/serial tracking)
         */

        this.inventoryProductTrackingSelect = page.locator('select[id="form.tracking"]').first();
        // "Manage Stock Moves" lot/serial detail flow, reached from the suffix
        // action on a confirmed move's quantity field.
        this.inventoryMoveManageLinesAction = page.locator('button[wire\\:click*="manageLines"]');
        this.inventoryMoveLinesModal = page.locator('.fi-modal-window:visible').filter({ hasText: /Manage Stock Moves/i }).first();
        this.inventoryMoveGenerateLotsAction = page.getByRole("button", { name: /Generate Serials\/Lots/i }).first();
        this.inventoryMoveLinesFirstLotInput = page.getByLabel(/First Lot Number/i).first();
        this.inventoryMoveLinesQuantityReceivedInput = page.getByLabel(/Quantity Received/i).first();
        this.inventoryMoveLinesGenerateSubmit = page.locator('.fi-modal-window:visible').last().locator('button[type="submit"], .fi-modal-footer-actions button').first();
        this.inventoryMoveLinesModalSaveButton = page.locator('.fi-modal-window:visible').filter({ hasText: /Manage Stock Moves/i }).getByRole("button", { name: /^Save$/i }).first();
        this.inventoryMoveLinesResultPackageSelect = page
            .locator('.fi-modal-window:visible')
            .filter({ hasText: /Manage Stock Moves/i })
            .locator('[wire\\:key*="result_package_id"] button.fi-select-input-btn')
            .first();

        /**
         * Inventory - Packages & Package Types
         */

        this.inventoryPackageTypeCreateButton = page.locator("a,button").filter({ hasText: /new package type|create/i }).first();
        this.inventoryPackageTypeNameInput = page.locator('input[id="form.name"]').first();
        this.inventoryPackageTypeLengthInput = page.locator('input[id="form.length"]').first();
        this.inventoryPackageTypeWidthInput = page.locator('input[id="form.width"]').first();
        this.inventoryPackageTypeHeightInput = page.locator('input[id="form.height"]').first();
        this.inventoryPackageTypeBaseWeightInput = page.locator('input[id="form.base_weight"]').first();
        this.inventoryPackageTypeMaxWeightInput = page.locator('input[id="form.max_weight"]').first();
        this.inventoryPackageTypeSaveButton = page.locator('button[id="key-bindings-1"]').first();
        this.inventoryPackageTypeTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryPackageCreateButton = page.locator("a,button").filter({ hasText: /new package|create/i }).first();
        this.inventoryPackageNameInput = page.locator('input[id="form.name"]').first();
        this.inventoryPackageTypeSelect = page.locator('[wire\\:key$="form.package_type_id"] button.fi-select-input-btn').first();
        this.inventoryPackageLocationSelect = page.locator('[wire\\:key$="form.location_id"] button.fi-select-input-btn').first();
        this.inventoryPackageSaveButton = page.locator('button[id="key-bindings-1"]').first();
        this.inventoryPackageTable = page.locator("table, div.fi-ta-empty-state");
        this.inventoryPackageDeleteAction = page.getByRole("button", { name: /Delete/i }).first();
        // The packages list ships with an "Internal Locations" preset view as its default;
        // "Default" is the unfiltered view that also lists shipped-out packages.
        this.inventoryPackageDefaultViewTab = page.locator("button,a").filter({ hasText: /^\s*Default\s*$/ }).first();

        this.inventoryScrapCreateButton = page.locator("a,button").filter({ hasText: /new scrap|create/i }).first();
        this.inventoryScrapProductSelect = page.locator('[wire\\:key$="form.product_id"] button.fi-select-input-btn').first();
        this.inventoryScrapQtyInput = page.locator('input[id="form.qty"]').first();
        this.inventoryScrapSourceLocationSelect = page.locator('[wire\\:key$="form.source_location_id"] button.fi-select-input-btn').first();
        this.inventoryScrapDestinationLocationSelect = page.locator('[wire\\:key$="form.destination_location_id"] button.fi-select-input-btn').first();

        this.inventoryQuantityCountedInput = page.locator('table tbody tr input:not([type="hidden"])').first();
        this.inventoryQuantityApplyAction = page.getByRole("button", { name: /^Apply$/i }).first();

        /* 
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
        // On a create form key-bindings-1 is "Create" and key-bindings-2 is
        // "Create & create another", which leaves the form open instead of redirecting.
        this.purchaseVendorCreateButton = page.locator('button[id="key-bindings-1"]').first();
        // Match "Confirm Order" exactly: a confirmed order also offers "Confirm Receipt Date",
        // which the looser purchaseQuotationConfirmButton regex would match.
        this.purchaseQuotationConfirmOrderButton = page.getByRole("button", { name: /^Confirm Order$/i }).first();
        // Target the create form's own submit. "#key-bindings-1" is index-based and becomes
        // the Delete header action once the save redirects onto the edit page.
        this.purchaseQuotationCreateSubmitButton = page
            .locator('button[type="submit"]')
            .filter({ hasText: /^\s*Create\s*$/ })
            .first();
        this.purchaseProductTrackingSelect = page.locator('select[id="form.tracking"]').first();
        // "Deliver To" — the incoming operation type the purchase order receives into.
        this.purchaseQuotationOperationTypeSelect = page.locator('[wire\\:key$="form.operation_type_id"] button.fi-select-input-btn').first();
        this.purchaseQuotationLineTaxSelects = page.locator('[wire\\:key*="form.products."][wire\\:key*=".taxes."] button.fi-select-input-btn');
        this.purchaseQuotationLineSubtotalInputs = page.locator('input[id^="form.products."][id$=".price_subtotal"]');
        this.purchaseQuotationReceivedQuantityInputs = page.locator('input[id^="form.products."][id$=".qty_received"]');
        // The order totals are a Livewire island: rows labelled "Untaxed Amount", "Tax"
        // (rendered only when the tax is above zero) and "Total".
        this.purchaseQuotationSummaryItems = page.locator(".invoice-item");
        this.purchaseQuotationReceiptRows = page.locator("table tbody tr");
        this.purchaseQuotationReceiptReferenceLinks = page.locator('table tbody tr a[href*="/receipts/"]');
        this.purchaseCreateBillButton = page.getByRole("button", { name: /Create Bill/i }).first();
        this.purchaseBillSubmitButton = page.getByRole("dialog").getByRole("button", { name: /^(Submit|Confirm|Create Bill)$/i }).first();
        this.purchaseBillsTableRows = page.locator("table tbody tr");
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
        // The form's real submit — a loose name match picks "Create & create another", which
        // saves but resets the form in place instead of redirecting to the saved record.
        this.blogPostsSaveButton = page
            .locator('button[type="submit"]')
            .filter({ hasText: /^\s*(Create|Save changes|Submit)\s*$/i })
            .first();
        this.blogPostsConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /delete/i }).first();
        this.blogPostsSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
    }
}
