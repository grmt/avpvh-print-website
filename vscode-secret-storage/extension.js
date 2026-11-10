const vscode = require('vscode');

const SECRET_KEY = 'printmyblog.wpAdminPassword';

async function promptForPassword() {
  return vscode.window.showInputBox({
    password: true,
    ignoreFocusOut: true,
    prompt: 'Enter the WordPress admin password used for Playwright deployment',
    placeHolder: 'WordPress admin password',
  });
}

async function savePassword(context, password) {
  await context.secrets.store(SECRET_KEY, password);
}

async function requestPassword(context) {
  let password = await context.secrets.get(SECRET_KEY);
  if (password) {
    return password;
  }

  password = await promptForPassword();
  if (!password) {
    throw new Error('No WordPress admin password was provided.');
  }

  await savePassword(context, password);
  void vscode.window.showInformationMessage('AV-PvH Print Website admin password saved to VS Code SecretStorage.');
  return password;
}

function activate(context) {
  context.subscriptions.push(
    vscode.commands.registerCommand('printmyblog.deployPlugin', async () => {
      const tasks = await vscode.tasks.fetchTasks();
      const deployTask = tasks.find((task) => task.name === 'AV-PvH Print Website: Deploy Plugin to WordPress');

      if (!deployTask) {
        void vscode.window.showErrorMessage(
          'Could not find the task "AV-PvH Print Website: Deploy Plugin to WordPress".'
        );
        return;
      }

      await vscode.tasks.executeTask(deployTask);
    })
  );

  context.subscriptions.push(
    vscode.commands.registerCommand('printmyblog.setWpAdminPassword', async () => {
      const password = await promptForPassword();
      if (!password) {
        return;
      }
      await savePassword(context, password);
      void vscode.window.showInformationMessage('AV-PvH Print Website admin password saved to VS Code SecretStorage.');
    })
  );

  context.subscriptions.push(
    vscode.commands.registerCommand('printmyblog.getWpAdminPassword', async () => {
      try {
        return await requestPassword(context);
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Unable to read the saved password.';
        void vscode.window.showErrorMessage(message);
        return '';
      }
    })
  );

  context.subscriptions.push(
    vscode.commands.registerCommand('printmyblog.clearWpAdminPassword', async () => {
      await context.secrets.delete(SECRET_KEY);
      void vscode.window.showInformationMessage('AV-PvH Print Website admin password removed from VS Code SecretStorage.');
    })
  );
}

function deactivate() {}

module.exports = {
  activate,
  deactivate,
};
