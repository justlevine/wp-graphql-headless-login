# Technology Stack & Tooling

This document provides a comprehensive overview of the core technology stack, dependencies, and operational commands for the `wp-graphql-headless-login` project. It is intended to serve as a reference for both human and autonomous agents. **Agents: Read this before running, building, or testing code.**

## Agent Guidance

- **Always check this document before running build, lint, or test commands.**
- **If a command fails, check for missing dependencies, outdated config, or version mismatches.**
- **If you add or remove a tool, update this file and the relevant config.**

---

## Summary Table of Identified Technologies

| Category         | Technology / Tool                | Purpose / Notes                                  | Official Documentation                                  |
|------------------|----------------------------------|--------------------------------------------------|---------------------------------------------------------|
| Language         | PHP (>=7.4)                      | Main backend language                            | [PHP](https://www.php.net/)                             |
| Language         | JavaScript/TypeScript            | Frontend/admin scripts, build tools              | [TypeScript](https://www.typescriptlang.org/)           |
| Framework        | WordPress                        | CMS platform                                     | [WordPress](https://wordpress.org/)                     |
| Framework        | WPGraphQL                        | GraphQL API for WordPress                        | [WPGraphQL](https://www.wpgraphql.com/)                 |
| Build Tool       | Webpack                          | JS/CSS bundling                                  | [Webpack](https://webpack.js.org/)                      |
| Build Tool       | Babel                            | JS transpilation                                 | [Babel](https://babeljs.io/)                            |
| Linting          | PHP_CodeSniffer (phpcs)          | PHP code style                                   | [phpcs](https://github.com/squizlabs/PHP_CodeSniffer)   |
| Linting          | PHPStan                          | PHP static analysis                              | [PHPStan](https://phpstan.org/)                         |
| Linting          | ESLint                           | JS/TS linting                                    | [ESLint](https://eslint.org/)                           |
| Linting          | Stylelint                        | CSS/SCSS linting                                 | [Stylelint](https://stylelint.io/)                      |
| Linting          | Prettier                         | JS/TS formatting                                 | [Prettier](https://prettier.io/)                        |
| Testing          | Codeception                      | PHP/WordPress testing                            | [Codeception](https://codeception.com/)                 |
| Testing          | WP-Browser                       | WP testing utilities                             | [WP-Browser](https://github.com/lucatume/wp-browser)    |
| Containerization | Docker Compose                   | Local dev/test environments                      | [Docker Compose](https://docs.docker.com/compose/)      |
| CI/CD            | GitHub Actions                   | Automated testing, linting, builds                | [GitHub Actions](https://docs.github.com/actions)        |

## Key Commands (For Agents)

| Purpose                | Command (run from repo root)                  |
|------------------------|-----------------------------------------------|
| Build assets           | `npm run build`                               |
| Development build      | `npm run dev`                                 |
| Lint all (PHP, JS, CSS)| `npm run lint`                               |
| Lint PHP               | `composer run-script lint`                    |
| Lint JS                | `npm run lint:js`                             |
| Lint CSS               | `npm run lint:css`                            |
| Lint TypeScript        | `npm run ts:check`                            |
| Fix PHP code style     | `composer run-script fix-cs`                  |
| Fix JS code style      | `npm run lint:js-fix`                         |
| Fix CSS code style     | `npm run lint:css-fix`                        |
| PHPStan (static check) | `composer run-script phpstan`                 |
| Run tests (all)        | `npm run test` or see Codeception below       |
| Codeception tests      | `composer run-script codeception` (see docs)  |
| Install Composer deps  | `composer install`                            |
| Install Node deps      | `npm ci`                                      |
| Start Docker env       | `docker compose up`                           |
| Stop Docker env        | `docker compose down`                         |

## CI/CD Overview

- **GitHub Actions** is used for all CI/CD workflows:
  - PHP linting and static analysis (`phpcs`, `phpstan`)
  - JS/CSS/TS linting and build checks
  - Codeception and WP-Browser test runs
  - Schema and package linting
  - Multi-version matrix for PHP and WordPress
- See `.github/workflows/` for detailed workflow files.

## Notable Dependencies

### PHP (Composer)
- Production: `axepress/wp-graphql-plugin-boilerplate`, `league/oauth2-*`, `firebase/php-jwt`
- Dev: `phpstan/phpstan`, `axepress/wp-graphql-cs`, `lucatume/wp-browser`, `codeception/*`, etc.

### Node (NPM)
- Production: `@wordpress/*`, `clsx`
- Dev: `@babel/*`, `@typescript-eslint/*`, `@wordpress/scripts`, `eslint`, `prettier`, `stylelint`, `webpack`, etc.

---

---

## Agent Checklist (Before Running or Submitting Code)

- [ ] All dependencies are installed (`composer install`, `npm ci`).
- [ ] All relevant build, lint, and test commands pass (see above).
- [ ] If you add/remove a tool or dependency, update this file and config.
- [ ] If you are unsure, escalate as per AGENT_CONSTITUTION.md.

---

**If you change the tech stack or commands, update this file and the relevant config.**
