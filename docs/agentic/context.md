# WP-GraphQL Headless Login Context

## Overview
WP-GraphQL Headless Login is a WordPress plugin that enables authentication with various providers (e.g., OAuth2, SAML) via GraphQL. It allows for headless WordPress applications to authenticate users through providers like Google, GitHub, and Facebook.

## Key Components

### Provider System (New)

#### ProviderType
The base class that defines a provider's capabilities:
- Type identification (OAuth2, SAML, etc.)
- Schema validation for options
- Authentication flow handling
- Instance validation

#### Provider
Runtime instance of a provider type:
- Instance-specific configuration
- Client options handling
- Authentication execution
- User data mapping

#### Repository
Database access layer for provider instances:
- CRUD operations
- Query by ID/slug/type
- Instance filtering
- Order management

### Database Structure

#### Schema Class
- Table creation/updates
- Column definitions
- Index management
- Data validation

#### Model Class
- Data validation
- JSON serialization
- Field tracking
- Timestamp handling

### Current Implementation Status

#### Completed
1. Base Classes
   - ProviderType abstract base
   - OAuth2Provider implementation
   - GitHubProvider example
   - Provider runtime class

2. Database Layer
   - Schema definitions
   - Model abstraction
   - Repository pattern
   - JSON handling

#### In Progress
1. Provider Registry
   - Type registration
   - Instance management
   - Provider factory

2. GraphQL Schema
   - Type definitions
   - Mutations
   - Query updates

### Codebase Map

#### Core Files
- `src/Providers/ProviderType.php`: Base provider definition
- `src/Providers/OAuth2Provider.php`: OAuth2 implementation
- `src/Providers/GitHubProvider.php`: GitHub provider example
- `src/Providers/Model.php`: Instance data model
- `src/Providers/Schema.php`: Database schema
- `src/Providers/Repository.php`: Data access layer

#### Future Changes
- `src/TypeRegistry.php`: Add provider types/mutations
- `src/Fields/RootQuery.php`: Update provider queries
- `src/Mutation/ProviderMutation.php`: Add instance mutations

## Development Guidelines

### Data Flow
1. Provider Type Registration
   ```php
   ProviderType::register_type(GitHubProvider::class);
   ```

2. Instance Creation
   ```php
   $instance = new Model([
     'type' => 'oauth2',
     'name' => 'GitHub',
     'slug' => 'github-1',
     'client_options' => [/*...*/],
     'login_options' => [/*...*/],
   ]);
   ```

3. Authentication Flow
   ```php
   $provider = new Provider($instance);
   $auth_url = $provider->get_authorization_url();
   $user_data = $provider->authenticate(['code' => $code]);
   ```

### Best Practices
1. Validation
   - Schema validation
   - Type checking
   - Required fields

2. Error Handling
   - WP_Error usage
   - Clear messages
   - Proper logging

3. Security
   - Input sanitization
   - Output escaping
   - Permission checks
