export async function loginAsAdmin(page:any) {
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
    await page.goto("/admin/login");

    await page.waitForLoadState("networkidle").catch(() => undefined);

    /**
     * Type until Livewire has the value: a field typed before its Alpine wrapper starts
     * (the revealable password starts late on a slow server) keeps the text in the input
     * but never reaches the form, which then submits empty ("field is required").
     */
    const fillLivewireField = async (selector: string, property: string, value: string) => {
        for (let attempt = 0; attempt < 10; attempt++) {
            await page.fill(selector, value);

            const synced = await page.evaluate(
                ([selector, property]: string[]) => {
                    const input = document.querySelector(selector);
                    const id = input?.closest("[wire\\:id]")?.getAttribute("wire:id");

                    return id ? (window as any).Livewire?.find(id)?.$get(property) : null;
                },
                [selector, property]
            );

            if (synced === value) {
                return;
            }

            await page.waitForTimeout(500);
        }

        throw new Error(`Login form never received ${property}`);
    };

    await fillLivewireField('input[type="email"]', "data.email", adminCredentials.email);
    await fillLivewireField('input[type="password"]', "data.password", adminCredentials.password);

    /**
     * Arm the wait before submitting: waitForNavigation() only sees a navigation that
     * starts after it is called, so a login that redirects straight away leaves it waiting
     * for a second navigation that never comes, and the run hangs on the login page.
     */
    await Promise.all([
        page.waitForURL((url: URL) => !url.toString().includes("/admin/login"), { timeout: 60000 }),
        page.press('input[type="password"]', "Enter"),
    ]);
    await page.waitForLoadState("networkidle").catch(() => undefined);

    return adminCredentials;
}
