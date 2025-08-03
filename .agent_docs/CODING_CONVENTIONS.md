# Coding Conventions & Style Guide

This document codifies the coding conventions ("vibe") of the `wp-graphql-headless-login` project, based on configuration and code analysis. It is intended for both human and autonomous contributors. **Agents: Read this before writing or modifying code.**

## Source of Truth

## Agent Guidance

- **Always check this document and the relevant config files before making code changes.**
- **If you are unsure about a convention, escalate as per AGENT_CONSTITUTION.md.**
- **If you find a discrepancy between code and convention, update this file and/or the config, and note it in your commit.**

---

The conventions are enforced by tooling in the repository, including:
- `phpcs.xml.dist` for PHP_CodeSniffer
- `phpstan.neon.dist` for PHPStan
- `.eslintrc.js` for ESLint
- `.prettierrc.js` for Prettier
- `tsconfig.json` for TypeScript
- `.editorconfig` for for everything else

The tooling configuration files define the coding standards and are the single source of truth for all code contributions. Any changes to the conventions should be reflected in these files, and any discrepancies between this document and the tooling should be resolved in favor of the tooling, and this document updated accordingly.

## Naming Conventions (PHP, JS/TS)
- **PHP Classes/Namespaces:** PascalCase, namespaced under `WPGraphQL\Login\...`
- **PHP Methods/Functions:** camelCase
- **Variables:** camelCase
- **Constants:** UPPER_SNAKE_CASE
- **JS/TS:** camelCase for variables/functions, PascalCase for components

## Formatting
- **PHP:** tabs, PSR-12 style, enforced by PHP_CodeSniffer
- **JS/TS:** tabs, enforced by Prettier and ESLint
- **Line Length:** 120 characters (soft limit)
- **Quotes:** Single quotes,
- **Trailing Commas:** Required where valid.

## Modularity & File Size
- **PHP:** Classes are small and focused; most files < 200 lines
- **JS/TS:** Modular and composable, with separate files for components and utilities
- **All**: DRY/SOLID** principles are broadly followed, with an emphasis on maintainability and readability over cleverness.

## Commenting Style
- **PHP:** DocBlocks for classes, methods, and functions
- **JS/TS:** JSDoc for functions/components as needed
- **Inline Comments:** Used sparingly, only for non-obvious logic

## Error Handling
- **PHP:** Exceptions for critical errors, `WP_Error` for recoverable plugin errors
- **JS/TS:** Try/catch for async, error boundaries for React components

## Linting & Formatting Tools
- **PHP:**
  - [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) (`composer run-script lint`)
  - [PHPStan](https://phpstan.org/) (`composer run-script phpstan`)
- **JS/TS:**
  - [ESLint](https://eslint.org/) (`npm run lint:js`)
  - [Prettier](https://prettier.io/) (auto-format on save)
  - [Stylelint](https://stylelint.io/) (`npm run lint:css`)

## Example: PHP Class
```php
namespace WPGraphQL\Login\Auth;

/**
 * Handles authentication logic.
 */
class Auth {
	/**
	 * Authenticates a user with the given username and password.
	 *
	 * @param string $username The username to authenticate.
	 * @param string $password The password to authenticate.
	 * @return bool True on success, false on failure.
	 */
	public function authenticate_user( string $username, string $password ): bool {
		// ...
	}
}
```

## Example: TS Function
```ts
// src/utils/formatDate.ts
/**
 * Formats a date to a human-readable string.
 */
export function formatDate(date: string | Date): string {
  return new Date(date).toLocaleDateString();
}
```

---

---

## Agent Checklist (Before Submitting Code)

- [ ] Code matches naming, formatting, and modularity conventions above.
- [ ] All relevant linters and formatters pass (see TECH_STACK.md for commands).
- [ ] If conventions or config files were updated, this file is updated too.
- [ ] If you are unsure, escalate as per AGENT_CONSTITUTION.md.

---

**If you change conventions, update this file and the relevant config.**
