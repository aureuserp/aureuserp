import { test, expect } from "../../setup";
import { WebsiteManagementPage } from "../../pages/06_websiteManagement";

test.describe("Website Pages - Listing", () => {
    test.beforeAll(async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        await websitePage.ensureWebsitePluginInstalled();
    });

    test("Website Pages Listing - Loads Table", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);

        await websitePage.gotoWebsitePagesPage();
    });

    test("Create Website Page - Valid Inputs", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E Website Page ${key}`;

        await websitePage.createWebsitePage({
            title,
            content: `This is the content for ${title}`,
            metaTitle: `Meta title for ${title}`,
            metaKeywords: `website,e2e,${key}`,
            metaDescription: `Meta description for ${title}`,
            isHeaderVisible: true,
            isFooterVisible: true,
        });

        await websitePage.gotoWebsitePagesPage();
        await websitePage.searchPage(title);
        await websitePage.expectPageListed(title);
    });

    test("Edit Website Page - Updates Title and Metadata", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const originalTitle = `E2E Website Page ${key}`;
        const updatedTitle = `E2E Website Page Updated ${key}`;

        await websitePage.createWebsitePage({
            title: originalTitle,
            content: `Page content for ${originalTitle}`,
        });

        await websitePage.editWebsitePage(originalTitle, {
            title: updatedTitle,
            content: `Updated content for ${updatedTitle}`,
            metaTitle: `Updated meta title ${key}`,
            metaDescription: `Updated meta description ${key}`,
            isHeaderVisible: false,
            isFooterVisible: false,
        });

        await websitePage.gotoWebsitePagesPage();
        await websitePage.searchPage(updatedTitle);
        await websitePage.expectPageListed(updatedTitle);
    });

    test("Publish Website Page - Makes Page Visible on Frontend", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E Published Page ${key}`;
        const content = `Published content for ${title}`;

        await websitePage.createWebsitePage({
            title,
            content,
            isHeaderVisible: true,
            isFooterVisible: true,
        });

        await websitePage.publishPage(title);

        // Get the slug from the page (assuming it's generated from title)
        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

        await websitePage.checkPageOnFrontend(slug, content, true, true, title);
    });

    test("Draft Website Page - Hides Page from Frontend", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E Draft Page ${key}`;
        const content = `Draft content for ${title}`;

        await websitePage.createWebsitePage({
            title,
            content,
            isHeaderVisible: true,
            isFooterVisible: true,
        });

        await websitePage.publishPage(title);
        await websitePage.draftPage(title);

        // After drafting, the page should not be accessible
        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        await websitePage.checkPageNotAccessibleOnFrontend(slug);
    });

    test("Website Page Header and Footer Visibility - Header Only", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E Header Only Page ${key}`;
        const content = `Content for header only page ${title}`;

        await websitePage.createWebsitePage({
            title,
            content,
            isHeaderVisible: true,
            isFooterVisible: false,
        });

        await websitePage.publishPage(title);

        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        await websitePage.checkPageOnFrontend(slug, content, true, false, title);
    });

    test("Website Page Header and Footer Visibility - Footer Only", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E Footer Only Page ${key}`;
        const content = `Content for footer only page ${title}`;

        await websitePage.createWebsitePage({
            title,
            content,
            isHeaderVisible: false,
            isFooterVisible: true,
        });

        await websitePage.publishPage(title);

        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        await websitePage.checkPageOnFrontend(slug, content, false, true, title);
    });

    test("Website Page Header and Footer Visibility - Neither Visible", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const title = `E2E No Header Footer Page ${key}`;
        const content = `Content for no header footer page ${title}`;

        await websitePage.createWebsitePage({
            title,
            content,
            isHeaderVisible: false,
            isFooterVisible: false,
        });

        await websitePage.publishPage(title);

        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        await websitePage.checkPageOnFrontend(slug, content, false, false, title);
    });
});
