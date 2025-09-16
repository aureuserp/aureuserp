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


    /**
     * Wait for the dashboard to load (fallbacks for SPA or non-navigation flows).
     */
    console.log("dashboard loading...");

    // First try a dashboard-specific selector; if not present, wait for URL change as a fallback.
    try {
        // Adjust the selector to a reliable element on your dashboard if needed.
        await page.waitForSelector('text=Dashboard', { timeout: 10000 });
    } catch (e) {
        try {
            await page.waitForURL(/admin|dashboard/, { timeout: 10000 });
        } catch (e) {
            // last resort: short pause so tests don't hang indefinitely
            await page.waitForTimeout(2000);
        }
    }

    return adminCredentials;
}