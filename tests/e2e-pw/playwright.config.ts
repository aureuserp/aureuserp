import { defineConfig, devices } from "@playwright/test";
import dotenv from "dotenv";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export const TESTS_ROOT_PATH = __dirname;
export const STATE_DIR_PATH = `${TESTS_ROOT_PATH}/.state/`;
export const ADMIN_AUTH_STATE_PATH = `${STATE_DIR_PATH}/admin-auth.json`;

dotenv.config({ path: path.resolve(__dirname, "../../.env") });

const reporters = process.env.CI
    ? [["list"], ["blob", { outputDir: "./blob-report" }]]
    : [
          ["list"],
          [
              "html",
              {
                  outputFolder: "./playwright-report",
              },
          ],
      ];

export default defineConfig({
    testDir: "./tests",

    timeout: 420 * 1000,
    expect: { timeout: 50 * 1000 },

    outputDir: "./test-results",

    fullyParallel: !!process.env.CI,
    workers: 1,

    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,

    reportSlowTests: null,

    reporter: reporters,

    use: {
        baseURL: process.env.BASE_URL ?? "http://127.0.0.1:8000",
        actionTimeout: 30 * 1000,
        screenshot: { mode: "only-on-failure", fullPage: true },
        video: "on-first-retry",
        trace: "on-first-retry",
    },

    projects: [
        {
            name: "chromium",
            use: { ...devices["Desktop Chrome"] },
        },
    ],
});
