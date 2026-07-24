import { type Locator, type Page, expect } from "@playwright/test";
import { anyDialog, ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type WebsitePageData = {
    title: string;
    content: string;
    metaTitle?: string;
    metaKeywords?: string;
    metaDescription?: string;
    isHeaderVisible?: boolean;
    isFooterVisible?: boolean;
};

export type BlogCategoryData = {
    name: string;
    subTitle?: string;
};

export type BlogPostData = {
    title: string;
    content: string;
    categoryName: string;
    subTitle?: string;
    metaTitle?: string;
    metaKeywords?: string;
    metaDescription?: string;
};

export class WebsiteManagementPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;
    readonly pluginPage: PluginManagementPage;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
        this.pluginPage = new PluginManagementPage(page);
    }

    async ensureWebsitePluginInstalled(): Promise<void> {
        await this.pluginPage.gotoPluginManagementPage();
        await this.pluginPage.installPluginByName("Website");
    }

    async ensureBlogsPluginInstalled(): Promise<void> {
        await this.pluginPage.gotoPluginManagementPage();
        await this.pluginPage.installPluginByName("Blog");
    }

    async gotoWebsitePagesPage(): Promise<void> {
        await this.page.goto("/admin/website/pages");
        await expect(this.page).toHaveURL(/website\/pages/);
        await expect(this.erpLocators.websitePagesHeading).toBeVisible();
        await expect(this.erpLocators.websitePagesTable).toBeVisible();
    }

    async gotoBlogCategoriesPage(): Promise<void> {
        await this.page.goto("/admin/website/configurations/categories");
        await expect(this.page).toHaveURL(/website\/configurations\/categories/);
        await expect(this.erpLocators.blogCategoriesHeading).toBeVisible();
        await expect(this.erpLocators.blogCategoriesTable).toBeVisible();
    }

    async gotoBlogPostsPage(): Promise<void> {
        await this.page.goto("/admin/website/posts");
        await expect(this.page).toHaveURL(/website\/posts/);
        await expect(this.erpLocators.blogPostsHeading).toBeVisible();
        await expect(this.erpLocators.blogPostsTable).toBeVisible();
    }

    async createWebsitePage(pageData: WebsitePageData): Promise<void> {
        await this.gotoWebsitePagesPage();
        await this.erpLocators.websitePagesCreateButton.click();
        await expect(this.page).toHaveURL(/website\/pages\/create/);

        await this.erpLocators.websitePagesTitleInput.fill(pageData.title);
        await this.fillContent(pageData.content);

        if (pageData.metaTitle) {
            await this.erpLocators.websitePagesMetaTitleInput.fill(pageData.metaTitle);
        }
        if (pageData.metaKeywords) {
            await this.erpLocators.websitePagesMetaKeywordsInput.fill(pageData.metaKeywords);
        }
        if (pageData.metaDescription) {
            await this.erpLocators.websitePagesMetaDescriptionInput.fill(pageData.metaDescription);
        }

        if (typeof pageData.isHeaderVisible === "boolean") {
            await this.toggleSwitch(this.erpLocators.websitePagesHeaderVisibleToggle, pageData.isHeaderVisible);
        }
        if (typeof pageData.isFooterVisible === "boolean") {
            await this.toggleSwitch(this.erpLocators.websitePagesFooterVisibleToggle, pageData.isFooterVisible);
        }

        await this.erpLocators.websitePagesSaveButton.click();
        await this.expectSuccessToast();
    }

    async editWebsitePage(originalTitle: string, updates: Partial<WebsitePageData>): Promise<void> {
        await this.gotoWebsitePagesPage();
        await this.searchPage(originalTitle);
        await this.openRowActions();
        await this.erpLocators.websitePagesEditButton.click();
        // await this.clickAction(
        //     this.erpLocators.websitePagesEditButton,
        //     this.erpLocators.websitePagesEditLink,
        //     this.erpLocators.websitePagesEditActionButton,
        // );

        if (updates.title) {
            await this.erpLocators.websitePagesTitleInput.fill(updates.title);
        }
        if (updates.content) {
            await this.fillContent(updates.content);
        }
        if (updates.metaTitle) {
            await this.erpLocators.websitePagesMetaTitleInput.fill(updates.metaTitle);
        }
        if (updates.metaKeywords) {
            await this.erpLocators.websitePagesMetaKeywordsInput.fill(updates.metaKeywords);
        }
        if (updates.metaDescription) {
            await this.erpLocators.websitePagesMetaDescriptionInput.fill(updates.metaDescription);
        }
        if (typeof updates.isHeaderVisible === "boolean") {
            await this.toggleSwitch(this.erpLocators.websitePagesHeaderVisibleToggle, updates.isHeaderVisible);
        }
        if (typeof updates.isFooterVisible === "boolean") {
            await this.toggleSwitch(this.erpLocators.websitePagesFooterVisibleToggle, updates.isFooterVisible);
        }

        await this.erpLocators.websitePagesSaveButton.click();
        await this.expectSuccessToast();
    }

    async deleteWebsitePage(title: string): Promise<void> {
        await this.gotoWebsitePagesPage();
        await this.searchPage(title);
        await this.openRowActions();
        await this.clickAction(
            this.erpLocators.websitePagesDeleteButton,
            this.erpLocators.websitePagesDeleteLink,
            this.erpLocators.websitePagesDeleteActionButton,
        );
        await this.erpLocators.websitePagesConfirmDeleteButton.click();
        await this.expectSuccessToast();
    }

    async searchPage(keyword: string): Promise<void> {
        await this.erpLocators.websitePagesSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async expectPageListed(title: string): Promise<void> {
        await expect(this.erpLocators.websitePagesTable).toContainText(title);
    }

    async expectPageNotListed(title: string): Promise<void> {
        await expect(this.erpLocators.websitePagesTable).not.toContainText(title);
    }

    async createBlogCategory(categoryData: BlogCategoryData): Promise<void> {
        await this.gotoBlogCategoriesPage();
        await this.erpLocators.blogCategoriesCreateButton.click();
        await expect(this.erpLocators.blogCategoriesNameInput).toBeVisible();

        await this.erpLocators.blogCategoriesNameInput.fill(categoryData.name);

        if (categoryData.subTitle) {
            await this.erpLocators.blogCategoriesSubTitleInput.fill(categoryData.subTitle);
        }

        await this.clickVisibleButton(/create|save|submit/i);

        // The success toast is torn down by the redirect that follows the save, so the
        // category being listed is the signal that it was really created.
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.gotoBlogCategoriesPage();
        await this.searchBlogCategory(categoryData.name);
        await this.expectBlogCategoryListed(categoryData.name);
    }

    async editBlogCategory(originalName: string, updates: Partial<BlogCategoryData>): Promise<void> {
        await this.gotoBlogCategoriesPage();
        await this.searchBlogCategory(originalName);

        const row = this.findTableRow(this.erpLocators.blogCategoriesTable, originalName);
        await expect(row).toBeVisible();
        await this.clickRowAction(row, /edit/i);

        if (updates.name) {
            await this.erpLocators.blogCategoriesNameInput.fill(updates.name);
        }

        if (typeof updates.subTitle === "string") {
            await this.erpLocators.blogCategoriesSubTitleInput.fill(updates.subTitle);
        }

        await this.clickVisibleButton(/save|submit/i);
        await this.expectBlogCategorySuccessToast();
    }

    async deleteBlogCategory(name: string): Promise<void> {
        await this.gotoBlogCategoriesPage();
        await this.searchBlogCategory(name);

        const row = this.findTableRow(this.erpLocators.blogCategoriesTable, name);
        await expect(row).toBeVisible();
        // await this.clickRowAction(row, /delete/i);
        await this.erpLocators.deleteBlogCategoryRowButton.click();
        await this.erpLocators.blogCategoriesConfirmDeleteButton.click();
        await this.expectBlogCategorySuccessToast();
    }

    async searchBlogCategory(keyword: string): Promise<void> {
        await this.erpLocators.blogCategoriesSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async expectBlogCategoryListed(name: string): Promise<void> {
        await expect(this.erpLocators.blogCategoriesTable).toContainText(name);
    }

    async expectBlogCategoryNotListed(name: string): Promise<void> {
        await expect(this.erpLocators.blogCategoriesTable).not.toContainText(name);
    }

    async createBlogPost(postData: BlogPostData): Promise<void> {
        await this.gotoBlogPostsPage();
        await this.erpLocators.blogPostsCreateButton.click();
        await expect(this.page).toHaveURL(/website\/posts\/create/);

        await this.erpLocators.blogPostsTitleInput.fill(postData.title);

        if (postData.subTitle) {
            await this.erpLocators.blogPostsSubTitleInput.fill(postData.subTitle);
        }

        await this.fillBlogContent(postData.content);
        await this.selectFilamentOption(this.erpLocators.blogPostsCategorySelect, postData.categoryName);

        if (postData.metaTitle) {
            await this.erpLocators.blogPostsMetaTitleInput.fill(postData.metaTitle);
        }

        if (postData.metaKeywords) {
            await this.erpLocators.blogPostsMetaKeywordsInput.fill(postData.metaKeywords);
        }

        if (postData.metaDescription) {
            await this.erpLocators.blogPostsMetaDescriptionInput.fill(postData.metaDescription);
        }

        await this.erpLocators.blogPostsSaveButton.click();

        // Saving redirects off the create form and takes the success toast with it; leaving
        // the create page is what proves the post was saved.
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).not.toHaveURL(/website\/posts\/create/);
    }

    async editBlogPost(originalTitle: string, updates: Partial<BlogPostData>): Promise<void> {
        await this.openBlogPostEditPage(originalTitle);

        if (updates.title) {
            await this.erpLocators.blogPostsTitleInput.fill(updates.title);
        }

        if (typeof updates.subTitle === "string") {
            await this.erpLocators.blogPostsSubTitleInput.fill(updates.subTitle);
        }

        if (updates.content) {
            await this.fillBlogContent(updates.content);
        }

        if (updates.categoryName) {
            await this.selectFilamentOption(this.erpLocators.blogPostsCategorySelect, updates.categoryName);
        }

        if (updates.metaTitle) {
            await this.erpLocators.blogPostsMetaTitleInput.fill(updates.metaTitle);
        }

        if (updates.metaKeywords) {
            await this.erpLocators.blogPostsMetaKeywordsInput.fill(updates.metaKeywords);
        }

        if (updates.metaDescription) {
            await this.erpLocators.blogPostsMetaDescriptionInput.fill(updates.metaDescription);
        }

        await this.erpLocators.blogPostsSaveButton.click();
        await this.expectBlogPostSuccessToast();
    }

    async deleteBlogPost(title: string): Promise<void> {
        await this.gotoBlogPostsPage();
        await this.searchBlogPost(title);

        const row = this.findTableRow(this.erpLocators.blogPostsTable, title);
        await expect(row).toBeVisible();
        await this.clickRowAction(row, /delete/i);
        await this.erpLocators.blogPostsDeleteButton.click();
        await this.erpLocators.blogPostsConfirmDeleteButton.click();
        await this.expectBlogPostSuccessToast();
    }

    async publishBlogPost(title: string): Promise<void> {
        await this.openBlogPostEditPage(title);

        const publishButton = this.page.getByRole("button", { name: /publish/i }).first();
        if (await publishButton.isVisible().catch(() => false)) {
            await publishButton.click();
            await this.expectBlogPostSuccessToast();
        }
    }

    async draftBlogPost(title: string): Promise<void> {
        await this.openBlogPostEditPage(title);

        const draftButton = this.page.getByRole("button", { name: /draft/i }).first();
        if (await draftButton.isVisible().catch(() => false)) {
            await draftButton.click();
            await this.expectBlogPostSuccessToast();
        }
    }

    async searchBlogPost(keyword: string): Promise<void> {
        await this.erpLocators.blogPostsSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
    }

    async expectBlogPostListed(title: string): Promise<void> {
        await expect(this.erpLocators.blogPostsTable).toContainText(title);
    }

    async expectBlogPostNotListed(title: string): Promise<void> {
        await expect(this.erpLocators.blogPostsTable).not.toContainText(title);
    }

    async checkBlogCategoryOnFrontend(categorySlug: string, categoryName: string): Promise<void> {
        await this.page.goto("/blog");
        await expect(this.page).toHaveURL(/\/blog$/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.getByRole("link", { name: categoryName, exact: true }).first()).toBeVisible();

        await this.page.goto(`/blog/${categorySlug}`);
        await expect(this.page).toHaveURL(new RegExp(`blog/${categorySlug}$`));
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).toContainText(categoryName);
    }

    async checkPublishedBlogOnFrontend(
        categorySlug: string,
        postSlug: string,
        postTitle: string,
        expectedContent: string,
        categoryName: string,
        subTitle?: string,
    ): Promise<void> {
        await this.page.goto("/blog");
        await expect(this.page).toHaveURL(/\/blog$/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).toContainText(postTitle);
        await expect(this.page.locator("body")).toContainText(categoryName);

        if (subTitle) {
            await expect(this.page.locator("body")).toContainText(subTitle);
        }

        await this.page.goto(`/blog/${categorySlug}`);
        await expect(this.page).toHaveURL(new RegExp(`blog/${categorySlug}$`));
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).toContainText(postTitle);
        await expect(this.page.locator("body")).toContainText(categoryName);

        await this.page.goto(`/blog/${categorySlug}/${postSlug}`);
        await expect(this.page).toHaveURL(new RegExp(`blog/${categorySlug}/${postSlug}$`));
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).toContainText(postTitle);
        await expect(this.page.locator("body")).toContainText(expectedContent);
    }

    async expectBlogPostNotListedOnFrontend(categorySlug: string, postTitle: string): Promise<void> {
        await this.page.goto("/blog");
        await expect(this.page).toHaveURL(/\/blog$/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).not.toContainText(postTitle);

        await this.page.goto(`/blog/${categorySlug}`);
        await expect(this.page).toHaveURL(new RegExp(`blog/${categorySlug}$`));
        await this.page.waitForLoadState("networkidle");
        await expect(this.page.locator("body")).not.toContainText(postTitle);
    }

    private async fillContent(content: string): Promise<void> {
        if (await this.erpLocators.websitePagesContentInput.isVisible().catch(() => false)) {
            await this.erpLocators.websitePagesContentInput.fill(content);
            return;
        }

        await expect(this.erpLocators.websitePagesEditableContent.first()).toBeVisible();
        await this.erpLocators.websitePagesEditableContent.first().click();
        await this.erpLocators.websitePagesEditableContent.first().fill(content);
    }

    private async fillBlogContent(content: string): Promise<void> {
        if (await this.erpLocators.blogPostsContentInput.isVisible().catch(() => false)) {
            await this.erpLocators.blogPostsContentInput.fill(content);
            return;
        }

        await expect(this.erpLocators.blogPostsEditableContent.first()).toBeVisible();
        await this.erpLocators.blogPostsEditableContent.first().click();
        await this.erpLocators.blogPostsEditableContent.first().fill(content);
    }

    private async openRowActions(): Promise<void> {
        await this.erpLocators.websitePagesRowActionsButton.click();
        await this.page.waitForTimeout(300);
    }

    private async clickAction(menuItem: Locator, fallbackLink: Locator, fallbackButton: Locator): Promise<void> {
        if (await menuItem.isVisible().catch(() => false)) {
            await menuItem.click();
            return;
        }

        if (await fallbackLink.isVisible().catch(() => false)) {
            await fallbackLink.click();
            return;
        }

        await fallbackButton.click();
    }

    private async toggleSwitch(toggle: Locator, checked: boolean): Promise<void> {
        if (!(await toggle.isVisible().catch(() => false))) {
            return;
        }

        const isChecked = (await toggle.getAttribute("aria-checked")) === "true";
        if (isChecked !== checked) {
            await toggle.click();
        }
    }

    private async selectFilamentOption(select: Locator, optionText: string): Promise<void> {
        await select.click();

        if (await this.erpLocators.salesSelectSearchInput.isVisible().catch(() => false)) {
            await this.erpLocators.salesSelectSearchInput.fill(optionText);
        }

        const option = this.erpLocators.salesSelectOption.filter({ hasText: optionText }).first();
        await expect(option).toBeVisible();
        await option.click();
    }

    private findTableRow(table: Locator, text: string): Locator {
        return table.locator("tr").filter({ hasText: text }).first();
    }

    private async clickVisibleButton(name: RegExp): Promise<void> {
        const dialogButton = anyDialog(this.page).getByRole("button", { name }).last();

        if (await dialogButton.isVisible().catch(() => false)) {
            await dialogButton.click();
            return;
        }

        await this.page.getByRole("button", { name }).last().click();
    }

    private async clickRowAction(row: Locator, actionName: RegExp): Promise<void> {
        const directButton = row.getByRole("button", { name: actionName }).first();
        if (await directButton.isVisible().catch(() => false)) {
            await directButton.click();
            return;
        }

        const directLink = row.getByRole("link", { name: actionName }).first();
        if (await directLink.isVisible().catch(() => false)) {
            await directLink.click();
            return;
        }

        const actionsButton = row.getByRole("button", { name: /actions/i }).first();
        if (await actionsButton.isVisible().catch(() => false)) {
            await actionsButton.click();
            await this.page.waitForTimeout(200);
        }

        await this.clickAction(
            this.page.getByRole("menuitem", { name: actionName }).first(),
            this.page.getByRole("link", { name: actionName }).first(),
            this.page.getByRole("button", { name: actionName }).first(),
        );
    }

    private async openBlogPostEditPage(title: string): Promise<void> {
        await this.gotoBlogPostsPage();
        await this.searchBlogPost(title);

        const row = this.findTableRow(this.erpLocators.blogPostsTable, title);
        await expect(row).toBeVisible();
        await this.clickRowAction(row, /edit/i);
        await expect(this.page).toHaveURL(/website\/posts\/.+\/edit/);
    }

    async publishPage(title: string): Promise<void> {
        await this.gotoWebsitePagesPage();
        await this.searchPage(title);
        await this.openRowActions();
        await this.erpLocators.websitePagesEditButton.click();

        await this.page.waitForLoadState("networkidle");

        const publishButton = this.page.getByRole("button", { name: /publish/i }).first();
        if (await publishButton.isVisible().catch(() => false)) {
            await publishButton.click();
            await this.expectSuccessToast();
        }
    }

    async draftPage(title: string): Promise<void> {
        await this.gotoWebsitePagesPage();
        await this.searchPage(title);
        await this.openRowActions();
        await this.erpLocators.websitePagesEditButton.click();

        await this.page.waitForLoadState("networkidle");

        const draftButton = this.page.getByRole("button", { name: /draft/i }).first();
        if (await draftButton.isVisible().catch(() => false)) {
            await draftButton.click();
            await this.expectSuccessToast();
            await this.erpLocators.websitePagesSaveButton.click();
        }
    }

    async checkPageOnFrontend(slug: string, expectedContent: string, headerVisible: boolean, footerVisible: boolean, pageTitle?: string): Promise<void> {
        await this.page.goto(`/pages/${slug}`, { waitUntil: "domcontentloaded" });
        await expect(this.page).toHaveURL(new RegExp(slug));
        await this.page.waitForLoadState("networkidle");

        await expect(this.page.locator("body")).toContainText(expectedContent);

        const footer = this.page.locator("footer").first();

        await expect(footer).toBeVisible();

        if (! pageTitle) {
            return;
        }

        const headerLinks = this.page.locator("header, nav").getByRole("link", { name: pageTitle, exact: true });
        const footerLinks = footer.getByRole("link", { name: pageTitle, exact: true });

        if (headerVisible) {
            await expect.poll(async () => await headerLinks.count()).toBeGreaterThan(0);
        } else {
            await expect(headerLinks).toHaveCount(0);
        }

        if (footerVisible) {
            await expect.poll(async () => await footerLinks.count()).toBeGreaterThan(0);
            await expect(footerLinks.first()).toBeVisible();
        } else {
            await expect(footerLinks).toHaveCount(0);
        }
    }

    async checkPageNotAccessibleOnFrontend(slug: string): Promise<void> {
        try {
            const response = await this.page.goto(`/pages/${slug}`, { waitUntil: "domcontentloaded" });
            
            // If we get a 404 or error status, that's expected
            if (response && !response.ok()) {
                return; // Page correctly not accessible
            }

            // If page loaded, verify it's a 404 page or doesn't contain expected content
            const is404 = await this.page.locator("h1, h2").filter({ hasText: /404|not found|page not found/i }).isVisible().catch(() => false);
            expect(is404).toBeTruthy();
        } catch (error: unknown) {
            // Navigation error is expected for unpublished/draft pages
            const errorMessage = error instanceof Error ? error.message : String(error);
            expect(errorMessage).toContain("ERR_ABORTED");
        }
    }

    private async expectSuccessToast(): Promise<void> {
        await expect(this.erpLocators.websitePagesSuccessToast).toBeVisible();
    }

    private async expectBlogCategorySuccessToast(): Promise<void> {
        await expect(this.erpLocators.blogCategoriesSuccessToast).toBeVisible();
    }

    private async expectBlogPostSuccessToast(): Promise<void> {
        await expect(this.erpLocators.blogPostsSuccessToast).toBeVisible();
    }
}
