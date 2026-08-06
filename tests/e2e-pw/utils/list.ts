import { type Locator, type Page, expect } from "@playwright/test";

/**
 * The rows of a list, whichever markup the resource uses — some render records as table rows,
 * others as cards.
 */
export function listRows(page: Page): Locator {
    return page.locator("table tbody tr, .fi-ta-record");
}

/**
 * The row holding a record. Row actions must be taken *inside* it rather than on the first
 * row of the list: Filament splits a search query into words and keeps any record that
 * matches them in any column, so searching for one record routinely leaves others listed —
 * and with tests running in parallel the record on top is likely another worker's.
 */
export function rowByText(page: Page, text: string): Locator {
    return listRows(page).filter({ hasText: text }).first();
}

/**
 * Search a list and wait for the record to be on screen.
 *
 * Filament debounces the search, so the request has often not even left the browser when the
 * page next goes idle, and a re-render can swallow the typed value altogether — hence the
 * retype. An empty result is also a settled result: a delete test searches for the record it
 * has just removed.
 */
export async function filterListBySearch(page: Page, input: Locator, keyword: string): Promise<void> {
    const settled = async () => {
        const rows = listRows(page);
        const total = await rows.count();

        if (total === 0) {
            return true;
        }

        return (await rows.filter({ hasText: keyword }).count()) > 0;
    };

    for (let attempt = 0; attempt < 3; attempt++) {
        await input.fill(keyword);
        await page.waitForLoadState("networkidle").catch(() => undefined);

        for (let poll = 0; poll < 10; poll++) {
            if (await settled()) {
                return;
            }

            await page.waitForTimeout(500);
        }
    }

    await expect.poll(settled, { timeout: 15000 }).toBe(true);
}

/**
 * Take a row action on the given row: open its actions dropdown, then click the entry.
 * Row actions live inside the row, so scoping to it keeps a parallel run from acting on a
 * record that belongs to another worker.
 */
export async function clickRowAction(row: Locator, action: string): Promise<void> {
    const actions = row.locator('button[title="Actions"]');

    if ((await actions.count()) > 0) {
        await actions.first().click({ timeout: 60000 });
    }

    // Matched on the action's accessible name, exactly. A loose text match would also hit the
    // row's own cells — a company named "E2E Edit Company …" contains the word "Edit", and
    // clicking that cell opens the record instead of editing it.
    const link = row.getByRole("link", { name: action, exact: true });
    const button = row.getByRole("button", { name: action, exact: true });

    await link.or(button).first().click({ timeout: 60000 });
}
