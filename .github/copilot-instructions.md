# AV-PvH Print Website - Development Guidelines

## Code Style
- **PHP**: Follow WordPress Coding Standards. Use `composer phpcs` to check and `composer phpcbf` to auto-fix.
- **JavaScript**: Use webpack with @wordpress/scripts. Run `npm run build` to compile assets.
- **Static Analysis**: Run `composer phpstan` for PHP static analysis (Level 0).

## Architecture
- **Framework**: Built on Twine framework with service locator dependency injection.
- **Structure**: PSR-4 namespaced classes in `src/PrintMyBlog/`, controllers handle requests, generators produce output files.
- **Extensibility**: Use service registries (DesignRegistry, FileFormatRegistry) and WordPress hooks for extensions.
- **Data Flow**: Project (CPT) → Design + Format → Generator → File Output.

## Build and Test
- **Install**: `composer install && npm install`
- **Build JS**: `npm run build` (or `npm run start` for watch mode)
- **Code Quality**: `composer phpcs && composer phpstan`
- **Package**: `npm run package:plugin` creates deployable zip
- **Deploy**: `npm run deploy:playwright` (requires `.env` with WordPress credentials)

## Conventions
- **Hooks**: Register in `Init.php` setHooks() method
- **Controllers**: Extend base controllers, use `$this->context->reuse()` for dependencies
- **Generators**: Extend `ProjectFileGeneratorBase.php` for new output formats
- **Entities**: Use ORM entities for data persistence, value objects for immutable data
- **Templates**: PHP views in `templates/`, design files in `designs/`
- **Versioning**: Semantic versioning - major version changes may break backward compatibility

See [README.md](README.md) for detailed setup and deployment instructions.</content>
<parameter name="filePath">/mnt/c/03-src/wordpress/printmyblog/.github/copilot-instructions.md