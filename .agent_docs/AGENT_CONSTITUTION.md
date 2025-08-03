# Agent Constitution

This document provides the prime directives for any AI agent contributing to this repository. Adherence to these principles is mandatory.

---

## 1. Core Directives & Workflow

Your primary goal is to develop and maintain secure, headless authentication for WordPress via WPGraphQL. Target users are developers building decoupled or headless WordPress applications that require robust, extensible authentication flows.

Before executing any task, you **must** consult the local context repository located in the `.agent_docs/` directory.

The prescribed development workflow is as follows:
1.  **Consult the Knowledge Base:**
    *   Start with `_INDEX.md` for a table of contents.
    *   Review `ARCHITECTURE.md` before modifying existing components or adding new ones.
    *   Review `CODING_CONVENTIONS.md` before writing or modifying any code to ensure adherence to the project's "coding vibe."
    *   Review `TECH_STACK.md` for a list of key commands for testing, linting, and building.
2.  **Execute Task:** Make code changes in strict accordance with the coding conventions, and latest best practices.
3.  **Verify Locally:**
    *   Run relevant linters, formatters, typecheckers etc (e.g. `npm run lint`, `composer run-script lint`).
4.  ** Update Docs and Contexts:**
    *   If your changes affect the architecture, coding conventions, or technology stack, update the relevant `.agent_docs/` files.
    *   If you notice any discrepancies or missing information in the `.agent_docs/` files, update them immediately.
5.  **Finalize:** Ensure all CI/CD checks pass before submitting a pull request.

## 2. Tool and Context Usage

You may use any tools, contexts, memory systems, or MCPs you have access to in order to complete your tasks efficiently and safely. There are no artificial restrictions on tool usage beyond those imposed by the environment or platform. Use your best judgment and always prefer safe, auditable actions.

## 3. Escalation Protocol (Human-in-the-Loop)

If you encounter too-ambiguous requirements, tool failures, or recursive errors, you must halt execution and request human assistance.

**Trigger Conditions for Escalation:**
-   If your confidence in a generated artifact or decision falls below 80%.
-   If you cannot interpret a requirement unambiguously.
-   If a required tool fails with a non-recoverable error.

**For all escalations, provide:**
1.  The task or decision in question.
2.  The context and prompt that led to the issue.
3.  Any alternative options considered.

This ensures efficient, targeted human intervention only when necessary.
