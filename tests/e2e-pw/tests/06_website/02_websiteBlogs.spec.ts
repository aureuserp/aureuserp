import { test } from "../../setup";
import { WebsiteManagementPage } from "../../pages/06_websiteManagement";

const slugify = (value: string): string =>
    value.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");

test.describe("Website Blogs", () => {
    test.beforeAll(async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);

        await websitePage.ensureWebsitePluginInstalled();
        await websitePage.ensureBlogsPluginInstalled();
    });

    test("Blog Categories Listing - Loads Table", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);

        await websitePage.gotoBlogCategoriesPage();
    });

    test("Create Blog Category - Valid Inputs", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Blog Category ${key}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.gotoBlogCategoriesPage();
        await websitePage.searchBlogCategory(categoryName);
        await websitePage.expectBlogCategoryListed(categoryName);
        await websitePage.checkBlogCategoryOnFrontend(slugify(categoryName), categoryName);
    });

    test("Edit Blog Category - Updates Name and Subtitle", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const originalCategoryName = `E2E Blog Category ${key}`;
        const updatedCategoryName = `E2E Blog Category Updated ${key}`;

        await websitePage.createBlogCategory({
            name: originalCategoryName,
            subTitle: `Sub title for ${originalCategoryName}`,
        });

        await websitePage.editBlogCategory(originalCategoryName, {
            name: updatedCategoryName,
            subTitle: `Sub title for ${updatedCategoryName}`,
        });

        await websitePage.gotoBlogCategoriesPage();
        await websitePage.searchBlogCategory(updatedCategoryName);
        await websitePage.expectBlogCategoryListed(updatedCategoryName);
    });

    test("Delete Blog Category - Removes Record", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Blog Category Delete ${key}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.deleteBlogCategory(categoryName);
        await websitePage.searchBlogCategory(categoryName);
        await websitePage.expectBlogCategoryNotListed(categoryName);
    });

    test("Blog Posts Listing - Loads Table", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);

        await websitePage.gotoBlogPostsPage();
    });

    test("Create Blog Post - Valid Inputs", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Blog Category ${key}`;
        const postTitle = `E2E Blog Post ${key}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.createBlogPost({
            title: postTitle,
            subTitle: `Sub title for ${postTitle}`,
            content: `Content for ${postTitle}`,
            categoryName,
            metaTitle: `Meta title for ${postTitle}`,
            metaKeywords: `blog,e2e,${key}`,
            metaDescription: `Meta description for ${postTitle}`,
        });

        await websitePage.gotoBlogPostsPage();
        await websitePage.searchBlogPost(postTitle);
        await websitePage.expectBlogPostListed(postTitle);
    });

    test("Edit Blog Post - Updates Title, Category, and Metadata", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const originalCategoryName = `E2E Blog Category ${key}`;
        const updatedCategoryName = `E2E Blog Category Updated ${key}`;
        const originalPostTitle = `E2E Blog Post ${key}`;
        const updatedPostTitle = `E2E Blog Post Updated ${key}`;

        await websitePage.createBlogCategory({
            name: originalCategoryName,
            subTitle: `Sub title for ${originalCategoryName}`,
        });

        await websitePage.createBlogPost({
            title: originalPostTitle,
            subTitle: `Sub title for ${originalPostTitle}`,
            content: `Content for ${originalPostTitle}`,
            categoryName: originalCategoryName,
        });

        await websitePage.editBlogPost(originalPostTitle, {
            title: updatedPostTitle,
            subTitle: `Sub title for ${updatedPostTitle}`,
            content: `Updated content for ${updatedPostTitle}`,
        });

        await websitePage.gotoBlogPostsPage();
        await websitePage.searchBlogPost(updatedPostTitle);
        await websitePage.expectBlogPostListed(updatedPostTitle);
    });

    test("Publish Blog Post - Shows on Frontend", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Frontend Category ${key}`;
        const postTitle = `E2E Published Blog ${key}`;
        const postContent = `Frontend blog content for ${postTitle}`;
        const postSubTitle = `Sub title for ${postTitle}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.createBlogPost({
            title: postTitle,
            subTitle: postSubTitle,
            content: postContent,
            categoryName,
            metaTitle: `Meta title for ${postTitle}`,
            metaKeywords: `published,blog,${key}`,
            metaDescription: `Meta description for ${postTitle}`,
        });

        await websitePage.publishBlogPost(postTitle);
        await websitePage.checkPublishedBlogOnFrontend(
            slugify(categoryName),
            slugify(postTitle),
            postTitle,
            postContent,
            categoryName,
            postSubTitle,
        );
    });

    test("Draft Blog Post - Hides Post From Frontend Listings", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Draft Category ${key}`;
        const postTitle = `E2E Draft Blog ${key}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.createBlogPost({
            title: postTitle,
            subTitle: `Sub title for ${postTitle}`,
            content: `Content for ${postTitle}`,
            categoryName,
        });

        await websitePage.publishBlogPost(postTitle);
        await websitePage.draftBlogPost(postTitle);
        await websitePage.expectBlogPostNotListedOnFrontend(slugify(categoryName), postTitle);
    });

    test("Delete Blog Post - Removes Record", async ({ adminPage }) => {
        const websitePage = new WebsiteManagementPage(adminPage);
        const key = Date.now();
        const categoryName = `E2E Delete Category ${key}`;
        const postTitle = `E2E Delete Blog ${key}`;

        await websitePage.createBlogCategory({
            name: categoryName,
            subTitle: `Sub title for ${categoryName}`,
        });

        await websitePage.createBlogPost({
            title: postTitle,
            subTitle: `Sub title for ${postTitle}`,
            content: `Content for ${postTitle}`,
            categoryName,
        });

        await websitePage.deleteBlogPost(postTitle);
        await websitePage.searchBlogPost(postTitle);
        await websitePage.expectBlogPostNotListed(postTitle);
    });
});
