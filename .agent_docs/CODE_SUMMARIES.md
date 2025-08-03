# Code Summaries

This document provides concise, natural-language summaries of the most critical modules and functions in the codebase. Each summary is associated with the file path and, where possible, line numbers.

**Agents: Use this as your first stop to understand class/module responsibilities and relationships.**

---

## Agent Guidance

- **If you change or add a major class/module, update this file immediately.**
- **If you find a summary is out of date or unclear, update it.**
- **If you are unsure about a class/module's purpose, escalate as per AGENT_CONSTITUTION.md.**

---

---

## Authentication Provider Architecture (Detailed)

This section documents the core classes and their relationships in the authentication provider system, based on a file-by-file review.

### src/Auth/Auth.php
**Purpose:** Main entry point for authentication and identity linking. Handles login and identity-linking mutations. Orchestrates the authentication flow:
- Receives user input (e.g., login mutation).
- Fetches the provider instance (Model) from the ProviderRegistry.
- Instantiates a Client with the Model.
- Delegates authentication to the Client, which in turn delegates to the Model and its ProviderType.
- Handles user creation, error handling, and token issuance.

### src/Auth/Client.php
**Purpose:** Runtime authentication client. Wraps a provider Model and exposes methods to:
- Authenticate and retrieve user data via the provider.
- Match or create a WordPress user from provider data.
- Expose the provider slug and configuration.
**Key:** All provider-specific logic is delegated to the Model and its ProviderType. Client acts as a runtime adapter for authentication operations.

### src/Providers/Model.php
**Purpose:** Represents a configured provider instance (e.g., "Google OAuth").
- Holds provider configuration, including type, name, slug, and options.
- Validates and sanitizes options against the schema defined by its ProviderType.
- Delegates authentication and option logic to its ProviderType instance.
- Acts as the bridge between static provider type logic and dynamic, user-configured provider instances.

### src/Providers/ProviderType/AbstractProviderType.php
**Purpose:** Abstract base class for all provider types (e.g., OAuth2, SAML, Password).
- Defines static schemas for client and login options.
- Implements option validation and sanitization.
- Requires child classes to implement authentication, user-matching, and schema methods.
- Provides a single extension point for new provider types: subclass and implement required methods.

### Class & Lifecycle Diagram

```plaintext
User Input (login mutation)
	|
	v
   Auth::login()
	|
	v
ProviderRegistry::get_provider($slug)
	|
	v
   Model (provider instance)
	|
	v
   new Client(Model)
	|
	v
Client::authenticate_and_get_user_data($input)
	|
	v
Model::authenticate($input)
	|
	v
Model->provider_type->authenticate($input, $model)
	|
	v
ProviderType (e.g., OAuth2, SAML, Password)
	|
	v
ProviderType::get_user_from_data($data)
	|
	v
Client::get_user_from_data($data)
	|
	v
User creation/matching, token issuance, etc.
```

**Key Relationships:**
- `Auth` is the orchestrator.
- `Client` is a runtime wrapper for a `Model`.
- `Model` is a hydrated provider config, delegates to its `ProviderType`.
- `AbstractProviderType` is subclassed for each provider type, defines schemas and logic.

**Extensibility:**
To add a new provider, subclass `AbstractProviderType` and register it. No need to hook into multiple APIs; provider type determines all behavior.

---

## src/Main.php
**Purpose:** Entry point and singleton for the plugin. Initializes all major subsystems (schema filters, settings, user profile, provider registry, type registry) and sets up WordPress hooks for plugin lifecycle events.

## src/CoreSchemaFilters.php
**Purpose:** Registers filters and actions to modify the core GraphQL schema, extend the User model, and handle authentication token logic. Integrates with WordPress and GraphQL hooks.

## src/TypeRegistry.php
**Purpose:** Central registry for all GraphQL types, enums, interfaces, objects, fields, connections, and mutations. Handles registration and initialization of schema components.

## src/WoocommerceSchemaFilters.php
**Purpose:** Adds WooCommerce-specific fields and types to the GraphQL schema, including session tokens and customer objects, with version compatibility logic.

## src/Admin/Settings.php
**Purpose:** Registers plugin settings, REST API routes, and admin UI components. Integrates with WordPress settings and GraphQL settings tabs. Handles asset registration and upgrade routines.

## src/Admin/UserProfile.php
**Purpose:** Adds user identity fields and secret key management to the WordPress user profile page. Handles AJAX actions for unlinking identities and revoking user secrets.

---

## Agent Checklist (Before Submitting Code)

- [ ] All major classes/modules are summarized here.
- [ ] Summaries are accurate and reflect the current codebase.
- [ ] If you add/remove a major class/module, update this file.
- [ ] If you are unsure, escalate as per AGENT_CONSTITUTION.md.

---

**If you change class/module responsibilities, update this file.**
