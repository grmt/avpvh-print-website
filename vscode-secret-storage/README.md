# Print My Blog Secret Storage

This is a small VS Code extension for storing the WordPress admin password in VS Code SecretStorage and exposing it to the workspace deploy task.

Commands:
- `Print My Blog: Deploy`
- `Print My Blog: Save WP Admin Password`
- `Print My Blog: Clear WP Admin Password`

Typical flow:
1. Copy `env.playwright` to `.env`
2. Fill in `WP_BASE_URL` and `WP_ADMIN_USER`
3. Leave `WP_ADMIN_PASSWORD` empty
4. Install this extension into VS Code with `npm run install:vscode-secret-storage`
5. Run `Print My Blog: Save WP Admin Password`
6. Run `Print My Blog: Deploy`

When run from WSL, the installer also attempts to install into the Windows VS Code extensions folder so opening the same project in Windows VS Code still works.
