# Provider Refactoring Scratchpad

Use this document to track progress, decisions, and todo items during the refactoring process.

## Current Status: Database Layer Implementation

### Initial Commit: April 5, 2025
- Set up the basic structure for provider instance management
- Created schema class that extends the Database\Schema class
- Updated Model class to extend Database\Model
- Updated Instance and Registry classes to use the new model pattern

## Latest Progress: April 5, 2025
- Implemented Database\Schema and Database\Model abstract classes
- Created foundation for Provider Instance management using the new database structure
- Completed ProviderRegistry implementation with:
  - Provider type registration and management
  - Provider instance CRUD operations
  - Methods for finding instances by ID, slug, and type
  - Support for enabling/disabling instances

### Core Architecture Decisions

1. **Provider Type System**
```php
abstract class ProviderType {
    // Identity
    abstract public static function get_type(): string;
    abstract public static function get_name(): string;
    abstract public static function get_slug(): string;

    // Schema Definition
    abstract public static function get_client_options_schema(): array;
    abstract public static function get_login_options_schema(): array;

    // Authentication Logic
    abstract public function authenticate(array $input): array;
    abstract protected function map_user_data(array $data): array;
}
```

2. **Provider Instance Model**
```php
// Database schema
[
    'id' => 'BIGINT AUTO_INCREMENT',
    'type' => 'VARCHAR(100)', // e.g. 'oauth2'
    'name' => 'VARCHAR(255)', // Display name
    'slug' => 'VARCHAR(100)', // URL-friendly ID
    'is_enabled' => 'TINYINT(1)',
    'order' => 'INT',
    'client_options' => 'LONGTEXT', // JSON 
    'login_options' => 'LONGTEXT',  // JSON
    'created_at' => 'DATETIME',
    'updated_at' => 'DATETIME'
]
```

3. **Type Safety**
- Strict schema validation for client_options
- Type-safe provider configuration
- Runtime validation of options

## Implementation Plan

### Phase 1: Database Structure
- [x] Plan database table structure for provider instances
- [x] Create Schema class extending Database\Schema
- [x] Create Model class extending Database\Model
- [x] Update Instance class to work with new Model
- [x] Update Registry class to work with the Schema and Model

### Phase 2: ProviderRegistry Refactoring
- [ ] Update ProviderRegistry to support provider types vs instances
- [ ] Add methods to get instances by type, ID, and slug
- [ ] Update provider instance creation and management
- [ ] Implement validation for provider types and instances

### Phase 3: Client Class Updates
- [ ] Update Client class to work with provider instances
- [ ] Update authentication flow for new provider structure
- [ ] Ensure backwards compatibility where possible
- [ ] Test with existing providers (Facebook, GitHub, Google)

### Phase 4: GraphQL Schema Updates
- [ ] Update Provider enum to work with provider instances
- [ ] Update LoginClient type for provider instances
- [ ] Update Login mutation for provider instances
- [ ] Update LinkUserIdentity mutation for provider instances

### Phase 5: Admin UI Updates
- [ ] Update settings UI for multiple provider instances
- [ ] Create UI for adding/editing provider instances
- [ ] Implement instance management features (create, edit, delete)

### Phase 6: Testing
- [ ] Update test suite for new provider structure
- [ ] Test all provider functionality
- [ ] Test edge cases with multiple instances of the same provider type

## Decisions Made

### Database Structure
- Using the Database\Schema and Database\Model abstract classes for provider instances
- Created a Schema class that defines the table structure with proper column definitions
- Instance data is stored as a Model class that handles validation and sanitization
- Using a custom table with fields for type, name, slug, client_options, etc.
- JSON serialization/deserialization handled automatically for client_options and login_options
- Using auto-incremented IDs for provider instances and slugs for backward compatibility

### Class Architecture
- Model extends abstract Database\Model class
- Schema extends abstract Database\Schema class
- Renamed classes to be more concise:
  - `Providers\Schema` - Defines the database schema
  - `Providers\Model` - Data model for provider instances
  - `Providers\Instance` - Runtime instance of a provider
  - `Providers\Registry` - Registry for provider types and instances

### Database Operations
- Using the Database\Model class for CRUD operations
- Added methods to find models by ID, slug, and type
- Added methods to handle JSON serialization for options fields
- Implemented provider instance creation and updating with validation

## Recent Insights
- The Database\Schema class handles table creation, indexes, and schema versioning
- The Database\Model class provides a clean interface for CRUD operations with validation
- The abstract classes allow for consistent patterns across different data types
- Provider instances can be stored with all necessary metadata and options
- JSON serialization/deserialization provides a flexible way to store complex options

## Questions & Research

### Model Operations
- Q: The abstract Model class doesn't have a delete method. How should we handle deletions?
- A: Implement the delete directly in the Registry class until a more general solution is available

### Instance Creation
- Q: Should we validate provider types during instance creation in the Registry class?
- A: Yes, provider types should be validated before creating an instance to ensure they exist

### Database Structure
- Q: Should we include additional metadata fields for provider instances?
- A: Start with the essential fields and add more as needed

## Code Snippets

### Sample code to create a provider instance:
```php
// Create a new provider instance
$registry = \WPGraphQL\Login\Providers\Registry::get_instance();

try {
    $id = $registry->create_instance([
        'provider_type' => 'github',
        'provider_name' => 'GitHub (Production)',
        'provider_slug' => 'github-prod',
        'is_enabled' => true,
        'client_options' => [
            'clientId' => 'your-client-id',
            'clientSecret' => 'your-client-secret',
        ],
    ]);
    
    // Get the created instance
    $instance = $registry->get_instance($id);
} catch (\Exception $e) {
    // Handle error
}
```

### Sample code to retrieve provider instances by type:
```php
// Get all GitHub provider instances
$registry = \WPGraphQL\Login\Providers\Registry::get_instance();
$instances = $registry->get_instances_by_type('github');

// Get enabled instances only
$enabled_instances = $registry->get_instances_by_type('github', true);
```

## Next Steps

1. Implement data migration from the existing options-based provider storage
2. Update the GraphQL schema to work with provider instances
3. Update the admin UI to support multiple instances of the same provider type
4. Complete the Registry class implementation for provider instance management
5. Update the Client class to work with the new provider instance model

### Implementation Details

#### ProviderRegistry Class Structure
The new ProviderRegistry implements:
1. **Provider Type Management**
   - register_provider_type(): Registers provider config classes
   - get_provider_type(): Gets provider config class by type
   - get_provider_types(): Gets all registered provider types
   - has_provider_type(): Checks if a provider type exists

2. **Provider Instance Management**
   - get_provider(): Gets provider instance by ID
   - get_provider_by_slug(): Gets provider instance by slug
   - get_providers_by_type(): Gets all instances of a specific type
   - get_providers(): Gets all provider instances
   - create_provider(): Creates new provider instance
   - update_provider(): Updates existing provider instance
   - delete_provider(): Deletes provider instance
   - enable_provider()/disable_provider(): Toggle instance status

#### Key Design Decisions
1. Used singleton pattern for Registry to ensure single source of truth
2. Separated provider type and instance management
3. Added validation for provider types during instance creation
4. Added proper error handling with exceptions
5. Implemented instance filtering by enabled status

## Next Steps

1. Add Migration Layer:
   - Create migration class to handle provider data migration
   - Add migration command or admin tool
   - Document migration process

2. Update GraphQL Schema:
   - Update Provider enum to work with instances
   - Update mutations to handle provider instances
   - Add new queries/mutations for instance management

3. Update Admin UI:
   - Create interface for managing provider instances
   - Add provider instance CRUD operations
   - Implement instance status management

## Questions & Research

### Migration Strategy
- Q: Should we provide automated migration from old options to new instances?
- A: Yes, but make it optional. Some users may want to reconfigure from scratch.

### GraphQL Schema Design
- Q: How should we identify providers in GraphQL operations?
- A: Use a combination of provider type and instance ID/slug

### Instance Management
- Q: Should we allow duplicate slugs across different provider types?
- A: No, keep slugs unique across all instances for simpler lookups

# Refactoring Learnings and Insights

## Code Architecture Review

### Core Abstractions
1. **Database Layer**
   - `Database\Schema`: Handles table creation/updates 
   - `Database\Model`: Provides CRUD functionality
   - Features validation, sanitization, dirty tracking

2. **Provider Structure** 
   - `ProviderConfig`: Base provider type definition
   - `Providers\Model`: Provider instance storage
   - `Providers\Repository`: Instance management
   - `Providers\Schema`: Database structure

### Key Patterns Used
1. **Active Record Pattern**
   - Models manage their own persistence
   - Validation/sanitization at model level
   - Tracks modified fields for efficient updates

2. **Repository Pattern** 
   - Encapsulates data access logic
   - Provides collection methods (find_by_x)
   - Handles caching and queries

3. **Database Schema Management**
   - Version tracking per table
   - Clean upgrade path
   - Safe schema modifications

## Implementation Notes

### Provider Instance Schema
```php
// Key fields in provider instances table
[
    'id' => 'BIGINT AUTO_INCREMENT',
    'type' => 'VARCHAR(100)', // e.g. 'oauth2'
    'name' => 'VARCHAR(255)', // Display name
    'slug' => 'VARCHAR(100)', // URL-friendly ID
    'is_enabled' => 'TINYINT(1)',
    'order' => 'INT',
    'client_options' => 'LONGTEXT', // JSON 
    'login_options' => 'LONGTEXT',  // JSON
    'created_at' => 'DATETIME',
    'updated_at' => 'DATETIME'
]
```

### Data Migration Strategy
1. Create new tables
2. Migrate existing provider settings
3. Update GraphQL schema
4. Deploy with breaking change notice

## Best Practices

### Database Operations
1. Use prepared statements consistently
2. Validate before save
3. Cache where appropriate
4. Handle JSON fields properly

### Error Handling
1. Use WP_Error for WordPress consistency
2. Validate early
3. Clear error messages
4. Proper exception handling

### Code Organization
1. Clear separation of concerns
2. Consistent naming conventions
3. Thorough documentation
4. Type hints and strict types

## Remaining Tasks

1. **Migration Layer**
   - [ ] Create Migration class
   - [ ] Add CLI command
   - [ ] Document process

2. **GraphQL Updates**
   - [ ] Update Provider enum
   - [ ] Add instance mutations
   - [ ] Update queries

3. **Admin UI**
   - [ ] Provider instance management
   - [ ] Settings screen updates
   - [ ] Migration tools

## Questions & Answers

Q: How to handle provider type validation?
A: Validate in Model class against registered types

Q: Should we allow duplicate slugs?
A: No, enforce unique slugs across all instances

Q: How to handle options migration?
A: Provide optional migration tool, allow manual reconfiguration

## Code Examples

### Creating Provider Instance
```php
$provider = new \WPGraphQL\Login\Providers\Model([
    'type' => 'github',
    'name' => 'GitHub (Production)',
    'slug' => 'github-prod',
    'is_enabled' => true,
    'client_options' => [
        'clientId' => 'xxx',
        'clientSecret' => 'yyy'
    ]
]);

$result = $provider->save();
```

### Finding Providers
```php
$repository = new \WPGraphQL\Login\Providers\Repository();

// Get all enabled GitHub providers
$instances = $repository->find_by_type('github', true);

// Get by ID
$provider = $repository->find_by_id(123);

// Get by slug
$provider = $repository->find_by_slug('github-prod');
```

## Agent Interaction Best Practices

1. **Clear Communication**
   - Be specific about file changes
   - Explain reasoning
   - Show complete examples
   - Validate changes

2. **Systematic Approach** 
   - Research first
   - Plan changes
   - Test assumptions
   - Document decisions

3. **Code Quality**
   - Follow WordPress standards
   - Add proper typehints
   - Document thoroughly
   - Think about edge cases
