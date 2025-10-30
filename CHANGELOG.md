# 🚀 CHANGELOG — v1.2.0

### Upgrade

* Upgraded to **Filament v4.1** — added support for the new Compact Repeater layout, improved Column Manager synchronization in repeatable entries, and refined overall UI consistency for a smoother user experience.

### 🧩 Features
* #858 [fixed] - Plugin Manager with GUI Install/Uninstall.


### 🐛 Fixes

* #805 [fixed] - fix Bank model filename.
* #806 [fixed] - issue_template_fixed.
* #808 [fixed] - Fixed Route [login] not defined error.
* #541 [fixed] - Parent Products Should Not Be Displayed in Product Dropdown.
* #826 [fixed] - fix employee updation when related user already selected.
* #817 [fixed] - Conflicting notifications.
* #818 [fixed] - Company dropdown in customer creation form shows all users, customers, employees, and companies.
* #831 [fixed] - Translation appearing on the recruitment dashboard graph.

* #821 [fixed] - Internal server error when creating a quotation with optional products.
* #833 [fixed] - Force deleting a single applicant deletes the entire data grid.
* #834 [fixed] - Getting server error when adding a rule in the filter of any data grid.
* #832 [fixed] - “Favourite” label missing from column reordering settings.
* #824 [fixed] - Plugins hide quickly when browse by phone screen.
* #827 [fixed] - Role permission automatically removed after creating a user from employee creation.
* #848 [fixed] - Exception error when applying country filter on Company data grid.
* #850 [fixed] - Prefilled data not loading automatically in form fields.
* #854 [fixed] - Customer ID displayed when editing an invoice linked to a soft-deleted customer.


### ✨ Improvements
* #804 [fixed] - improved navigations.
* #809 [fixed] - Refactor email notification handling for PO/RFQ with detailed status messages.
* #857 [fixed] - update the ui of the chatter.
* #856 [fixed] - fixed namespace issue in ci/cd.
* #853 [fixed] - filament version upgrade 4.1.
* #835 [fixed] - Add “Open Product” action in product repeater items.
* #828 [fixed] - Update APP_NAME from AureusERP to YourERP.
* #822 [fixed] - Reorderable Columns.
* #812 [fixed] - fixed unique name issue.
* #813 [fixed] - Improve Page Navigation Layout for Clustered and Record Views.
* #816 [fixed] - refactor codebase.




# 🚀 CHANGELOG — v1.1.0

### 🐛 Fixes

* #748 [fixed] - Internal server error when opening the dashboard calendar in Time-off plugin.
* #754 [fixed] - Fixed installation issue.
* #755 [fixed] - Exception error after changing admin password – "Route [login] not defined.
* #756 [fixed] - Added Missing Translation.
* #757 [fixed] - Form table repeater.
* #759 [fixed] - remove redundant theme imports from plugins to reduce CSS size.
* #762 [fixed] - Updated Readme.md.
* #763 [fixed] - enhance ERP installation command with admin user options.
* #764 [fixed] - updated permissions for various resources in filament-shield.
* #767 [fixed] - time-off bug fix.
* #770 [fixed] - Optimized role performance issue.
* #771 [fixed] - Refactor TableView plugin.
* #772 [fixed] - Refactored IconPicker.
* #774 [fixed] - Database not wiped during reinstallation, causing duplicate UOM (Unit of Measure) entries.
* #777 [fixed] - Terminal error during composer install due to missing SQLite database file.
* #780 [fixed] - Access Control Failure: Users Can View Restricted Sections Without Proper Permissions.
* #781 [fixed] - Update accept invitation translations.
* #783 [fixed] - Added 'buy' action placeholder text in RuleResource.
* #784 [fixed] - Fixed dashboard issue.
* #785 [fixed] - fix double notification on Quotation mailing.
* #786 [fixed] - remove the unused imports.
* #787 [fixed] - fix label translation key.
* #789 [fixed] - Navigation refactor.
* #790 [fixed] - sale Invoice Creation Issue.
* #794 [fixed] - made dashboard form responsive.
* #795 [fixed] - Fixed logout issue.
* #796 [fixed] - Fixed website login route issue.
* #797 [fixed] - Removed double section from project dashboard.
* #798 [fixed] - Emploment Type Create Modal, Translation need to be updated. #121
* #799 [fixed] - Fix form submission in CancelQuotationAction to enable email sending.
* #800 [fixed] - Refactor purchase order form.
* #801 [fixed] - Added missing navigation icon in DepartmentResource .
* #802 [fixed] - In Tags, View the tag there color should appear instead of icon. #152




# 🚀 CHANGELOG — v1.0.0

### Upgrade

* Upgraded to **Filament v4** for improved performance, UI enhancements, and compatibility.

### 🐛 Fixes

* #745 [fixed] - Product dropdown in packaging shows all products instead of only tangible goods.
* #742 [fixed] - Internal server error when force deleting a leave type that is already in use.
* #738 [fixed] - Company ID displayed when editing quotation linked to a deleted company #738
* #737 [fixed] - Company ID displayed when editing department linked to a deleted company #737
* #736 [fixed] - Company field shows ID when linked company is soft deleted in purchase agreement #736
* #735 [fixed] - Company data grid missing archived option and lacking validation for force delete #735
* #733 [fixed] - Internal server error when sending sales order by email after confirmation #733
* #732 [fixed] - Company ID is displayed when editing an invoice linked to a deleted company #732
* #724 [fixed] - Bug: Timesheet table displays wrong minutes #724
* #708 [fixed] - Internal Server Error When Clicking "Reorder Records" Button on Companies Data Grid Page #708
* #704 [fixed] - Internal Server Error When Forcing Deletion of a User Assigned to an Employee #704
* #696 [fixed] - Internal Server Error When Creating Role Without Guard Name #696
* #695 [fixed] - Changing Guard Name of Role Assigned to Admin User Causes System Lockout #695
* #693 [fixed] - System Fails to Assign Permissions Using "Select All" for Roles With More Than 6 Plugins #693
* #690 [fixed] - System Allows Multiple Employees to Be Assigned to the Same User #690
* #689 [fixed] - "Create Invoice" Button Not Visible After Confirming Sales Quotation in Sales Order Page #689
* #688 [fixed] - Internal Server Error on Project Dashboard When Clicking Projects Dropdown #688
* #687 [fixed] - Internal Server Error on Time-Off Dashboard When Leave Type Is Created Without Color #687
* #686 [fixed] - Internal Server Error on Project Dashboard When Changing Data Limit to "All" in Top Assignees and Top Projects #686
* #683 [fixed] - Admin User Should Not Be Deletable to Prevent System Lockout #683
* #678 [fixed] - Default Admin role can be deleted, causing permanent system lockout #678
* #677 [fixed] - Default Admin role allows removal of critical permissions from itself, leading to lockout #677
* #675 [fixed] - System Allows "Request Date From" to Be Greater Than "Request Date To" Without Validation in Time-Off Creation #675
* #672 [fixed] - Job Description and Job Requirements Display Data Inside <p> Tags #672
* #665 [fixed] - Quotation Currency Not Updating Automatically Based on Selected Company #665
* #664 [fixed] - Bill Currency Not Updating Automatically Based on Selected Company #664
* #663 [fixed] - Company ID Displayed Instead of Name When Editing Routes Linked to a Deleted Company #663
* #662 [fixed] - Check Availability Button Not Working on Delivery Page After Clicking "Mark as To Do #662
* #661 [fixed] - Parent Department ID Displayed When Editing Child Department After Parent Deletion #661
* #660 [fixed] - Internal Server Error When Adding Long External Notes While Creating Inventory Location #660
* #659 [fixed] - Internal Server Error When Adding Long Description in Employee Time-Off #659
* #657 [fixed] - No Success Message Displayed When Creating Time Off #657
* #618 [fixed] - Filament Path Shown Instead of Success Message When Deleting Candidates #618
* #615 [fixed] - Soft Deleted Applicants Still Visible in Data Grid Despite Success Message #615
* #612 [fixed] - Internal Server Error When Adding Skills to an Applicant in Recruitment Plugin #612
* #608 [fixed] - Internal Server Error When Adding Long Note While Creating Payment Terms #608
* #605 [fixed] - Missing Route Validation on Sales Order Confirmation After Editing the Order #605
* #649 [fixed] - Unnamed Checkboxes Displayed in Roles Widget Section #649
* #642 [fixed] - Internal Server Error When Clicking "Set as Checked" After Confirming Credit Note in Bill Creation #642
* #623 [fixed] - Extension Tab Overflows Screen on Applicants Page #623
* #622 [fixed] - Internal Server Error When Deleting Employee Used in Time Off Records #622
* #620 [fixed] - Edit Button on Delivery Opens View Page Instead of Edit Page #620
* #603 [fixed] - RFQ Label Missing in Purchase Plugin – Displays Only "Quotation" Instead of "RFQ" #603
* #602 [fixed] - No Success Message Displayed After Deleting Tax Group #602
* #600 [fixed] - Internal Server Error When Adding Long Description While Creating Tax #600
* #559 [fixed] - Sales Orders jump to Sales Orders State after Click confirm #559
* #550 [fixed] - Company ID Displayed Instead of Name After Deleting Company in Product Creation #550
* #549 [fixed] - Favorites Icon Not Visible on Product Page but Still Functioning on Click #549
* #547 [fixed] - Internal Server Error When Editing Partner Linked to Deleted Company #547
* #546 [fixed] - Account Holder Name Selection Not Working Correctly While Creating Bank Account
* #545 [fixed] - "Configuration" Breadcrumb Disappears After Managing Due Terms in Payment Terms Module
* #544 [fixed] - Translation Keys Displayed Instead of Labels in Payment Terms Sorting Dropdown
* #543 [fixed] - Incorrect Labeling on Vendor Payments Page Showing Customer Fields Instead of Vendor
* #542 [fixed] - Login and Register Buttons Missing on Mobile View After Installing Website Plugin
* #540 [fixed] - Created Customer Not Visible in Vendor Dropdown During Bill Creation
* #539 [fixed] - Product Icon Not Displayed in Invoicing Customer Products
* #538 [fixed] - Incorrect Breadcrumb Hierarchy on Vendor Creation Page
* #533 [fixed] - Server Error (500) When Trying to Add a Product to a Purchase Quotation
* #519 [fixed] - Internal Server Error When Returning a Dropship Transfer
* #515 [fixed] - Internal Server Error When Adding Products in Invoices Due to Missing Currency Value
* #514 [fixed] - Unable to Recreate Category with Same Name After Deletion Due to Slug Conflict
* #513 [fixed] - Internal Server Error on Blog Page After Deleting Associated Category
* #512 [fixed] - Category ID Displayed Instead of Name After Deleting Used Blog Category
* #511 [fixed] - Unpublished Pages Are Visible on Frontend Despite Not Clicking Publish
* #509 [fixed] - Activity Types Count Displays Zero Despite Existing Records
* #508 [fixed] - Terminal Error on Running `php artisan db:seed` After Installing Invoice Plugin
* #507 [fixed] - Internal Server Error When Re-running `php artisan erp:install` and Attempting Login
* #505 [fixed] - Incorrect Button Label on Public Holidays Page
* #504 [fixed] - No Validation for Start Date Being Later Than End Date in Public Holiday Creation
* #503 [fixed] - Internal Server Error When Creating Accrual Plan with Carry Over Date Set to Day 31
* #502 [fixed] - Internal Server Error When Searching on Accrual Plans
* #501 [fixed] - Incorrect Leave Count When Selecting Time Off Dates from Calendar
* #500 [fixed] - Internal Server Error When Selecting "Created By" Filter in Job Positions
* #499 [fixed] - Internal Server Error When Editing Activity Plan After Deleting Associated Company
* #498 [fixed] - Department ID Visible Instead of Name After Department Deletion in Employee Creation
* #497 [fixed] - Internal Server Error When Searching on Skills Page in Employees Plugin
* #496 [fixed] - Internal Server Error When Selecting 'Group By Type' Filter on Manage Resume Page
* #495 [fixed] - Internal Server Error Occurs Instead of Validation Message When Deleting Used Project Stage
* #494 [fixed] - Project ID Visible in Task Stages After Deleting the Associated Project
* #493 [fixed] - Internal Server Error When Deleting a Task Stage That Is in Use
* #492 [fixed] - Internal Server Error When Creating a Task Due to Incorrect Data Type in `json_decode`
* #491 [fixed] - Internal Server Error When Entering Non-Numeric Text in "Allocated Hours" Field During Project Creation
* #489 [fixed] - Purchase Agreement Allows End Date Earlier Than Start Date Without Validation
* #488 [fixed] - Vendor ID Displayed Instead of Name After Deleting Used Vendor in Purchase Agreement
* #485 [fixed] - Internal Server Error When Adding Product in Quotation Creation in Purchase Plugin
* #480 [fixed] - Lineitem Display UX Issue (Purchase/Order/Etc) - Design/Layout Fix
* #479 [fixed] - Package ID Displayed Instead of Package Name When Selecting Non-Internal Location During Quantity Creation
* #478 [fixed] - Internal Server Error When Searching in Scrap Moves Search Bar
* #475 [fixed] - Internal Server Error When Selecting "Scheduled At" From Filter Dropdown on Receipt Page
* #474 [fixed] - Internal Server Error When Editing Receipt With Deleted Product
* #473 [fixed] - Contact ID Displayed Instead of Name After Deleting Contact in Receipt
* #467 [fixed] - \[Clean Code] Removed Duplication from Task Model in Project Plugin
* #466 [fixed] - Task Creation Error: `json_decode(): Argument #1 ($json) must be of type string, Webkul\Project\Enums\TaskState given`
* #464 [fixed] - Internal Server Error When Editing Quotation After Deleting Associated Product
* #463 [fixed] - Internal Server Error When Deleting Order Lines After Sales Order Confirmation
* #462 [fixed] - Customer ID Visible After Customer Deletion During Quotation Creation
* #461 [fixed] - Internal Server Error When Creating Refund with Payment Term Using 'Days After End of Next Month' Delay Type
* #460 [fixed] - Exception Thrown When Clicking "Created By" Column in Resume Datagrid
* #457 [fixed] - Tax Configuration Details Not Appearing on View Page After Save
* #456 [fixed] - Exception Thrown When Editing and Saving Reporting by Employee
* #455 [fixed] - Requested Date From, Requested Date To, and Requested Days/Hours not showing in Reporting by Employee
* #444 [fixed] - Internal Server Error When Entering String in Lead Time Field While Creating Quotation
* #443 [fixed] - Product ID Displayed in Edit Quotation After Deleting Associated Product
* #437 [fixed] - Product ID Displayed Instead of Name in Packagings After Deleting the Associated Product
* #432 [fixed] - Internal Server Error When Creating Activity Type with Out-of-Range Delay Count
* #431 [fixed] - Internal Server Error When Selecting Associated Model in Activity Types Filter
* #422 [fixed] - Internal Server Error When Saving Changes in Sales Orders
* #420 [fixed] - No Validation Triggered for Negative Packaging Quantity in Quotation
* #418 [fixed] - Internal Server Error When Searching in Quotations
* #416 [fixed] - Internal Server Error When Deleting Tax Group Used in Taxes
* #415 [fixed] - Internal Server Error When Deleting Taxes Used in Invoices or Credit Notes
* #414 [fixed] - Translation Keys Displayed Instead of Success Message When Creating Payment Due Term
* #413 [fixed] - Payment Term ID Displayed Instead of Payment Term (Deleted) After Deletion in Invoice
* #412 [fixed] - Recipient Bank ID Displayed Instead of Label After Bank Account Deletion in Vendor Bills
* #411 [fixed] - Customer Bank Account ID Displayed Instead of Account Number When Deleted in Invoices Section
* #410 [fixed] - Internal Server Error When Searching on Payments Page in Invoices Under Customers Section
* #409 [fixed] - Internal Server Error When Exceeding 350 Characters Limit in Credit Note (Reason displayed on Credit Note) on View Invoice
* #408 [fixed] - Payment Method Dropdown Displays Blank When Attempting to Pay Invoice
* #405 [fixed] - Tooltip Displays File Path Instead of Meaningful Suggestion
* #404 [fixed] - Toggle buttons for "Is Visible Header Menu" and "Is Visible Footer Menu" are not working independently
* #403 [fixed] - Old Attribute Values Still Visible After Changing Product Attribute
* #402 [fixed] - Internal Server Error When Creating Storage Categories Under Inventory Configuration
* #400 [fixed] - wrong link: in settings manage logistic goes to contacts
* #399 [fixed] - Internal Server Error When Deleting a Variant Option from an Existing Attribute
* #395 [fixed] - Description Content Wrapped in <p> Tags on View Lots Page After Lot Creation
* #390 [fixed] - Internal Server Error When Searching on Manage Operation Page in Product Packages
* #387 [fixed] - Product ID Visible Instead of Name After Product Deletion in Storage Categories (Capacity by Products)
* #386 [fixed] - Exception error when creating a new My Time Offs
* #385 [fixed] - Exception error when creating a new Time Off Allocation
* #384 [fixed] - Exception error when creating "By Employees" Reporting with document upload
* #379 [fixed] - Source and Destination Location IDs Shown Instead of Location Names After Deleting Locations in Operation Type
* #378 [fixed] - Leave Accrual Levels not visible after saving a new Accrual Plan
* #377 [fixed] - Inappropriate translation appearing in the Color section when creating a new Leave Type
* #375 [fixed] - Internal Server Error When Editing Attribute and Deleting Variant Option inside the Product
* #370 [fixed] - Internal Server Error When Creating Receipt with a Deleted Operation Type
* #361 [fixed] - Internal Server Error When Editing Product After Deleting Assigned Attribute from Configuration
* #356 [fixed] - Internal Server Error Occurs When Editing Receipt with Deleted Operation Type
* #355 [fixed] - Internal Server Error Occurs When Editing Receipt With Deleted Location
* #354 [fixed] - Incorrect or inappropriate translation in warning message when creating Activity Plan for Sales
* #353 [fixed] - Disabled Fields Become Editable After Saving Stock Moves on Validated Receipt
* #352 [fixed] - Internal Server Error When Selecting Destination Location with Non-Internal Location Type
* #350 [fixed] - Internal Server Error When Searching with a Single alphabet in Mega Search
* #349 [fixed] - Tooltip Displays File Path Instead of Proper Suggestion
* #348 [fixed] - Internal Server Error When Creating Receipt with "Internal Transfers" After Deleting All Locations
* #347 [fixed] - Source and Destination Location IDs Shown Instead of Location Names After Deletion in Internal Transfers
* #346 [fixed] - Warehouse ID Displayed Instead of Warehouse Name in Operation Types After Deletion
* #345 [fixed] - Internal Server Error When Creating Duplicate Product Quantity Instead of Validation Error
* #340 [fixed] - Exception error appears when adding a skill while editing a candidate
* #329 [fixed] - "Date From" and "Date To" missing on view page after creating Job Position
* #328 [fixed] - Selected company in Activity Plan is not saved correctly — defaults to another company
* #326 [fixed] - Unable to Create Quotation for Products with Cost Price Greater Than Sales Price
* #325 [fixed] - Terminal Error on Running php artisan migrate:fresh When Sales Orders Exist
* #324 [fixed] - Internal Server Error When Selecting Currency in Filter Dropdown
* #320 [fixed] - Negative Price Value Allowed During Product Creation
* #317 [fixed] - Internal Server Error When Creating a Warehouse
* #316 [fixed] - Company Logo heading is missing when logo and color are not added during company creation
* #315 [fixed] - Description and Deadline Fields Become Blank After Creating a Receipt
* #314 [fixed] - Internal Server Error When Searching on Manage Variants Page
* #313 [fixed] - Product ID Displayed After Deleting Product That Is in Use
* #312 [fixed] - Internal Server Error on Applying Adjustment After Entering Counted Quantity
* #310 [fixed] - Internal Server Error When Entering String in Counted Quantity Field in Adjustment Section
* #309 [fixed] - Internal Server Error When Using Search Bar in Adjustment Section
* #308 [fixed] - Internal Server Error When Generating Receipt with Negative Demand Value
* #307 [fixed] - Destination Package Displays Index Value Instead of Name Upon Location Selection
* #306 [fixed] - Internal Server Error When Creating a New Storage Category
* #296 [fixed] - Exceptional Error While Creating a New Company with Logo Image
* #294 [fixed] - Internal Server Error When Adding Vendor Price Inside Product
* #293 [fixed] - Internal Server Error When Deleting Vendor Price Inside Product
* #292 [fixed] - Internal Server Error When Confirming Order in Quotation
* #289 [fixed] - Internal Server Error When Adding Duplicate Product Capacity in Storage Category
* #288 [fixed] - Internal Server Error When Adding Duplicate Package Type Capacity in Storage Category
* #287 [fixed] - Internal Server Error When Adding a New Warehouse Location with Certain Location Types
* #283 [fixed] - Internal Server Error When Adding Replenishment Under Procurement
* #275 [fixed] - Product edit on customer page redirected to vendor's product edit page #275
* #274 [fixed] - Internal server error when clicking on preview button on edit invoice page #274
* #271 [fixed] - Internal server error when deleting a receipt on the edit receipt page #271
* #270 [fixed] - Internal server error when confirming back orders before validating receipts #270
* #267 [fixed] - Internal server error when validating a receipt after deleting a warehouse #267
* #266 [fixed] - Internal server error when force deleting a warehouse instead of validation message #266
* #265 [fixed] - Internal server error when creating operation type in configuration #265
* #264 [fixed] - Product moves can be deleted after validation in inventory operations #264
* #257 [fixed] - Internal server error when creating a receipt with null operation type #257
* #253 [fixed] - Internal server error when creating a new invoice: missing method `calculateDateMaturity` #253
* #252 [fixed] - Error when uninstalling contacts plugin: "There are no commands defined in the 'contacts' namespace" #252
* #213 [fixed] - Installation error when installing inventories plugin before purchases plugin #213
* #204 [fixed] - Error running Laravel optimize command #204
* #198 [fixed] - Internal server error when adding a product to a category #198
* #195 [fixed] - Internal server error when changing parent of a parent category #195
* #194 [fixed] - Internal server error when deleting parent or root category #194
* #191 [fixed] - Internal server error when mass deleting products in inventory section #191
* #189 [fixed] - Random number displays as bank name after deleting associated bank #189
* #188 [fixed] - File does not exist at path when running `php artisan <plugin-name>:install` #188
* #187 [fixed] - Archived count shows zero despite containing data in industries section #187
* #186 [fixed] - Internal server error when forcing delete in archived contacts #186
* #184 [fixed] - Internal server error when saving blank default quotation validity #184
* #182 [fixed] - Variants toggle button not functioning in manage products settings #182
* #181 [fixed] - Discount toggle button not functioning in manage pricing settings #181
* #179 [fixed] - Exceptional error on adding skills in candidate edit page #179
* #178 [fixed] - Archived count increases after deleting activity plans, but no activity types are visible #178
* #174 [fixed] - Internal server error when manually adding a state #174
* #173 [fixed] - Newly created customer not visible on customers page #173
* #172 [fixed] - First product name displays as '6' in edit invoice page after sales order #172
* #169 [fixed] - Status toggle button not visible when creating/editing job position #169
* #165 [fixed] - Exceptional error when creating a UTM source with long name #165
* #164 [fixed] - Exceptional error when creating a UTM medium with long name #164
* #163 [fixed] - Incorrect success message displayed after deleting quotations #163
* #161 [fixed] - Internal server error when adding a product with zero price in quotation #161
* #160 [fixed] - Unable to install #160
* #159 [fixed] - 500 internal server error when creating a refuse reason with long name (no spaces) #159
* #158 [fixed] - 500 internal server error when creating a degree with long name (no spaces) #158
* #154 [fixed] - Incorrect success message displayed after deleting tax group #154
* #151 [fixed] - 500 internal server error when creating a tag with long name #151
* #150 [fixed] - Internal server error when creating tax without selecting tax group #150
* #148 [fixed] - 500 internal server error when saving activity type with long content #148
* #147 [fixed] - Payment term created with negative early discount values without validation #147
* #143 [fixed] - Default user missing after saving activity type in recruitment configurations #143
* #142 [fixed] - Disabled payment terms still visible in invoice and credit note creation #142
* #138 [fixed] - Incoterms exceeding three characters can be created #138
* #137 [fixed] - Duplicate bank account numbers allowed in configuration section #137
* #136 [fixed] - Add character validation for user name to prevent UI issues #136
* #134 [fixed] - Add character validation for team name to prevent UI issues #134
* #133 [fixed] - Internal server error when adding non-numeric long text in branch contact information #133
* #132 [fixed] - Internal server error when entering large text in memo field in customer payments #132
* #131 [fixed] - 500 internal server error when adding long text in company information #131
* #129 [fixed] - Amount field allows text input and causes internal server error in customer payments #129
* #128 [fixed] - Customer payments shows 'No Payments' even after successful payment creation #128
* #126 [fixed] - Creating company: city heading changes to address after filling address field #126
* #124 [fixed] - Color details not appearing on the view company page #124
* #107 [fixed] - Internal server error when exceeding weight limit in product creation #107
* #103 [fixed] - Product image not visible on products page after saving #103
* #100 [fixed] - Unnecessary time displayed in due date on invoice preview #100
* #91 [fixed] - Internal server error when selecting "10 days after end of next month" in payment term #91
* #90 [fixed] - Incorrect display of due date and payment term on invoice view page #90
* #88 [fixed] - Product name not visible when collapsing all in invoice creation #88
* #82 [fixed] - No default value for the project tag color #82
* #78 [fixed] - Global search error and its resolution #78
* #77 [fixed] - Install plugins error #77
* #30 [fixed] - Exception error in mega search admin panel #30
* #29 [fixed] - Tags section accepting emojis & random alphanumeric text #29
