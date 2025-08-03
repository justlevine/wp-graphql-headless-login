# CONTRIBUTING.md

Welcome! Thank you for your interest in contributing to **Headless Login for WPGraphQL**. We value all forms of contribution—code, documentation, testing, bug reports, feature requests, and community support.

---

## Table of Contents
- [Getting Help](#getting-help)
- [Ways to Contribute](#ways-to-contribute)
- [Project Overview](#project-overview)
- [Development Setup](#development-setup)
- [Testing & Linting](#testing--linting)
- [Coding Standards](#coding-standards)
- [Submitting Issues](#submitting-issues)
- [Submitting Pull Requests](#submitting-pull-requests)
- [Review & Merge Process](#review--merge-process)
- [Code of Conduct](#code-of-conduct)
- [Recognition & Thanks](#recognition--thanks)
- [Key Resources](#key-resources)

---

## Getting Help
- For questions, join the [WPGraphQL Discord](https://discord.gg/55h7WmYZff) or open a [GitHub Discussion](https://github.com/AxeWP/wp-graphql-headless-login/discussions).
- For bugs or feature requests, open a [GitHub Issue](https://github.com/AxeWP/wp-graphql-headless-login/issues).
- For security issues, please email the maintainers directly (see `README.md`).

## Ways to Contribute
- **Code:** Bug fixes, new features, refactoring, tests.
- **Documentation:** Improve docs, clarify usage, add examples.
- **Testing:** Report bugs, write or improve tests.
- **Support:** Help answer questions in issues or Discord.
- **Ideas:** Suggest features or improvements.

## Project Overview
Headless Login for WPGraphQL is a WordPress plugin that enables secure, headless authentication for WPGraphQL, supporting passwords, OAuth2/OpenID Connect, JWT, and more. See [README.md](./README.md) for a full description, requirements, and install instructions.

## Development Setup
1. **Clone the repo:**
   ```bash
   git clone https://github.com/AxeWP/wp-graphql-headless-login.git
   cd wp-graphql-headless-login
   ```
2. **Install dependencies:**
   - PHP: `composer install`
   - Node: `npm ci`
3. **Set up environment:**
   - Copy `.env.dist` to `.env` and update as needed.
   - For tests: `composer install-test-env`
4. **Start development environment:**
   - Docker: `docker compose up`
   - Stop: `docker compose down`

## Testing & Linting
- **Run all linters:** `npm run lint`
- **Lint PHP:** `composer run-script lint`
- **Lint JS:** `npm run lint:js`
- **Lint CSS:** `npm run lint:css`
- **Lint TypeScript:** `npm run ts:check`
- **Fix code style:** `composer run-script fix-cs`, `npm run lint:js-fix`, `npm run lint:css-fix`
- **Static analysis:** `composer run-script phpstan`
- **Run tests:**
  - All: See below
  - Codeception: `./bin/run-codeception.sh` (inside container)

## Coding Standards
- **PHP:** PSR-12, 4 spaces, PascalCase for classes, camelCase for methods/vars, UPPER_SNAKE_CASE for constants, DocBlocks required.
- **JS/TS:** Prettier, ESLint, 2 spaces, camelCase for vars/functions, PascalCase for components, JSDoc for functions/components.
- **General:** 120 char line length (soft), single quotes (PHP), double quotes (JS/TS), trailing commas in JS/TS.
- See `.agent_docs/CODING_CONVENTIONS.md` for full details.

## Submitting Issues
1. Search [existing issues](https://github.com/AxeWP/wp-graphql-headless-login/issues) first.
2. Use the issue template and provide as much detail as possible (steps to reproduce, environment, logs, etc.).
3. For security issues, do **not** open a public issue—email the maintainers.

## Submitting Pull Requests
1. Fork the repo and create a feature branch.
2. Follow the coding standards and ensure all tests/linters pass.
3. Add or update documentation as needed.
4. Reference related issues in your PR description.
5. Use the PR template if available.
6. Be responsive to review feedback.

## Review & Merge Process
- PRs are reviewed by maintainers and/or core contributors.
- All CI checks must pass before merge.
- Reviews focus on correctness, clarity, tests, and adherence to standards.
- Maintainers may request changes or clarify requirements.
- Merges are typically completed within a week.

## Code of Conduct
All contributors are expected to follow our [Code of Conduct](./CODE_OF_CONDUCT.md) and treat others with respect and professionalism.

## Recognition & Thanks
We appreciate all contributions! Top contributors may be recognized in release notes or the README. Thank you for helping make this project better.

## Key Resources
- [README.md](./README.md): Project overview, install, usage
- [.agent_docs/ARCHITECTURE.md](.agent_docs/ARCHITECTURE.md): Architecture & dependency graph
- [.agent_docs/TECH_STACK.md](.agent_docs/TECH_STACK.md): Technology stack & commands
- [.agent_docs/CODING_CONVENTIONS.md](.agent_docs/CODING_CONVENTIONS.md): Coding conventions
- [.agent_docs/CODE_SUMMARIES.md](.agent_docs/CODE_SUMMARIES.md): Module/function summaries
- [WPGraphQL Discord](https://discord.gg/55h7WmYZff): Community support
- [GitHub Issues](https://github.com/AxeWP/wp-graphql-headless-login/issues): Bug reports & feature requests

---

Thank you for contributing! Your help makes this project—and the WPGraphQL ecosystem—stronger.
