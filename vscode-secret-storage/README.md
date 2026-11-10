# AV-PvH Print Website Secret Storage

This is a small VS Code extension for storing the WordPress admin password in VS Code SecretStorage and exposing it to the workspace deploy task.

Commands:
- `AV-PvH Print Website: Deploy`
- `AV-PvH Print Website: Save WP Admin Password`
- `AV-PvH Print Website: Clear WP Admin Password`

Typical flow:
1. Copy `env.playwright` to `.env`
2. Fill in `WP_BASE_URL` and `WP_ADMIN_USER`
3. Leave `WP_ADMIN_PASSWORD` empty
4. Install this extension into VS Code with `npm run install:vscode-secret-storage`
5. Run `AV-PvH Print Website: Save WP Admin Password`
6. Run `AV-PvH Print Website: Deploy`

When run from WSL, the installer also attempts to install into the Windows VS Code extensions folder so opening the same project in Windows VS Code still works.
