# Print My Blog
WordPress Plugin that simplifies printing your entire blog. Please see https://wordpress.org/plugins/print-my-blog/

## Note to Developers on Backward Compatibility

This plugin uses [Semantic Versioning](https://semver.org/); meaning that whenever the major version changes (eg from 1.0.0 to 2.0.0) you should expect backward-incompatible changes.
If you make customizations, please contact us to inform us of them so we can avoid breaking them (eg 
created an issue in GitHub discussing what your integration/customization does and what it depends on.)

## PHP Code Sniffing
This project uses Composer for PHP code sniffing. Go to the root directory and run `composer phpcbf` to do code sniffing and automatically fix PHP code style.

## Building Javascript
This project uses NPM and webpack for building some Javascript. Go to the root directory and run `npm run build` to re-build the JS files in `assets/scripts/src`.

## Deploying the Plugin with Playwright
This repo now includes a simple browser-driven deploy flow for uploading the plugin zip through the WordPress admin.

1. Install dependencies: `npm install`
2. Build a plugin zip from the current working tree: `npm run package:plugin`
3. Copy the tracked env template into your local secret file:

   ```bash
   cp env.playwright .env
   ```

4. Fill in `.env` with your WordPress admin details. The deploy script loads `.env` automatically:

   ```bash
   WP_BASE_URL="https://your-site.example"
   WP_ADMIN_USER="admin"
   WP_ADMIN_PASSWORD=""
   PLAYWRIGHT_HEADLESS=false
   PLAYWRIGHT_LOCALE="en-US"
   PLAYWRIGHT_CLOSE_DELAY_MS=5000
   ```

5. Save the password in VS Code SecretStorage:
   `Print My Blog: Save WP Admin Password`

6. Deploy from the Command Palette:
   `Print My Blog: Deploy`

You can also still run the VS Code task directly:
`Tasks: Run Task` -> `Print My Blog: Deploy Plugin`

The `Print My Blog: Save WP Admin Password` command is contributed by the local extension in `vscode-secret-storage`, so install it first:

```bash
npm run install:vscode-secret-storage
```

Then reload VS Code and use the task-based flow.

When run from WSL, the installer also tries to copy the extension into your Windows VS Code extensions folder so the same project works from both environments.

You can still run the deployment script directly from a terminal if you prefer:
`npm run deploy:playwright`

Optional environment variables:
- `PLUGIN_ZIP_PATH` to override the default zip path (`dist/print-my-blog.zip`)
- `PLAYWRIGHT_HEADLESS=false` to watch the browser run
- `PLAYWRIGHT_LOCALE="en-US"` to force the browser UI/request locale
- `PLAYWRIGHT_CLOSE_DELAY_MS=5000` to keep the browser open briefly after a successful deploy
- `PLAYWRIGHT_ENV_FILE` to load a different env file path instead of `.env`
- `VSCODE_EXTENSIONS_DIR` to override where `npm run install:vscode-secret-storage` installs the local extension
- `WINDOWS_VSCODE_EXTENSIONS_DIR` to override the Windows VS Code extensions path used from WSL

## VS Code SecretStorage
This repo includes a small local extension in `vscode-secret-storage` for keeping the WordPress admin password out of `.env`.

The flow is:
1. Copy `env.playwright` to `.env`
2. Fill in `WP_BASE_URL` and `WP_ADMIN_USER`
3. Leave `WP_ADMIN_PASSWORD` empty
4. In VS Code, run `Print My Blog: Save WP Admin Password`
5. Run `Print My Blog: Deploy`

The deploy command runs the task `Print My Blog: Deploy Plugin`, which injects the saved password into the Playwright process at runtime.
