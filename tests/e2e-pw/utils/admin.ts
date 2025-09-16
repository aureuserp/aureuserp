export async function loginAsAdmin(page) {
    /**
     * Admin credentials.
     */
    const adminCredentials = {
        email: "admin@example.com",
        password: "admin123",
    };

    /**
     * Authenticate the admin user.
     */
    console.log("logging in as admin...");
    await page.goto("admin/login");
    await page.fill('input[type="email"]', adminCredentials.email);
    await page.fill('input[type="password"]', adminCredentials.password);
    await page.press('input[type="password"]', 'Enter'),
        console.log("submitting...");


    return adminCredentials;
}