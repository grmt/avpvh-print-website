import { chromium } from 'playwright';
import path from 'node:path';
import process from 'node:process';
import fs from 'node:fs';

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function loadEnvFile(filePath) {
  if (!fs.existsSync(filePath)) {
    return;
  }
  const contents = fs.readFileSync(filePath, 'utf8');
  for (const rawLine of contents.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line || line.startsWith('#')) {
      continue;
    }
    const separatorIndex = line.indexOf('=');
    if (separatorIndex === -1) {
      continue;
    }
    const key = line.slice(0, separatorIndex).trim();
    let value = line.slice(separatorIndex + 1).trim();
    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'"))
    ) {
      value = value.slice(1, -1);
    }
    if (!(key in process.env)) {
      process.env[key] = value;
    }
  }
}

function requiredEnv(name) {
  const value = process.env[name];
  if (!value) {
    throw new Error(
      `Missing required environment variable: ${name}. Copy env.playwright to .env and fill in your WordPress admin credentials.`
    );
  }
  return value;
}

function normalizeBaseUrl(url) {
  return url.replace(/\/+$/, '');
}

async function clickFirst(page, selectors) {
  for (const selector of selectors) {
    const locator = page.locator(selector).first();
    if (await locator.count()) {
      await locator.click();
      return true;
    }
  }
  return false;
}

async function waitForAny(page, selectors, timeout = 5000) {
  const start = Date.now();
  while (Date.now() - start < timeout) {
    for (const selector of selectors) {
      if (await page.locator(selector).first().count()) {
        return true;
      }
    }
    await sleep(200);
  }
  return false;
}

async function login(page, baseUrl, username, password) {
  await page.goto(`${baseUrl}/wp-login.php`, { waitUntil: 'domcontentloaded' });
  await page.locator('#user_login').fill(username);
  await page.locator('#user_pass').fill(password);
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#wp-submit').click(),
  ]);
}

async function uploadPlugin(page, baseUrl, pluginZip) {
  await page.goto(`${baseUrl}/wp-admin/plugin-install.php?tab=upload`, {
    waitUntil: 'domcontentloaded',
  });

  const fileInput = page.locator('input[type="file"][name="pluginzip"], #pluginzip').first();
  await fileInput.setInputFiles(pluginZip);

  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#install-plugin-submit, input[name="install-plugin-submit"]').first().click(),
  ]);

  const overwriteSelectors = [
    'a.button.button-primary.upgrade',
    'a.button-primary:has-text("Replace current with uploaded")',
    'button.button-primary:has-text("Replace current with uploaded")',
    'input.button-primary[value*="Replace current"]',
    'a.button-primary:has-text("Replace current version with uploaded")',
    'button.button-primary:has-text("Replace current version with uploaded")',
    'input.button-primary[value*="Replace current version"]',
    'a.button[href*="overwrite-package"]',
    'button[name="overwrite-package"]',
    'input[name="overwrite-package"]',
  ];

  await waitForAny(page, overwriteSelectors, 8000);
  const overwriteClicked = await clickFirst(page, overwriteSelectors);

  await page.waitForLoadState('networkidle');
  return overwriteClicked;
}

async function activatePlugin(page) {
  const activated = await clickFirst(page, [
    'a.button.button-primary[href*="action=activate"]',
    'a.button-primary:has-text("Activate Plugin")',
    'text=Activate Plugin',
  ]);

  if (activated) {
    await page.waitForLoadState('networkidle');
  }
}

async function main() {
  const configuredEnvFile = process.env.PLAYWRIGHT_ENV_FILE;
  if (configuredEnvFile) {
    loadEnvFile(path.resolve(configuredEnvFile));
  } else {
    loadEnvFile(path.resolve('.env'));
    loadEnvFile(path.resolve('env.playwright'));
  }

  const baseUrl = normalizeBaseUrl(requiredEnv('WP_BASE_URL'));
  const username = requiredEnv('WP_ADMIN_USER');
  const password = requiredEnv('WP_ADMIN_PASSWORD');
  const pluginZip = path.resolve(process.env.PLUGIN_ZIP_PATH || 'dist/print-my-blog.zip');
  const headless = process.env.PLAYWRIGHT_HEADLESS !== 'false';
  const locale = process.env.PLAYWRIGHT_LOCALE || 'en-US';
  const closeDelayMs = Number.parseInt(
    process.env.PLAYWRIGHT_CLOSE_DELAY_MS || (headless ? '0' : '5000'),
    10
  );

  if (!fs.existsSync(pluginZip)) {
    throw new Error(`Plugin zip not found: ${pluginZip}`);
  }

  const browser = await chromium.launch({ headless });
  const context = await browser.newContext({ locale });
  const page = await context.newPage();

  try {
    await login(page, baseUrl, username, password);
    const overwriteClicked = await uploadPlugin(page, baseUrl, pluginZip);
    await activatePlugin(page);
    console.log(
      `Deployment finished. Overwrite step ${overwriteClicked ? 'clicked' : 'not needed or not shown'}. Current page: ${page.url()}`
    );
    if (closeDelayMs > 0) {
      console.log(`Waiting ${closeDelayMs}ms before closing the browser...`);
      await sleep(closeDelayMs);
    }
  } finally {
    await context.close();
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exitCode = 1;
});
