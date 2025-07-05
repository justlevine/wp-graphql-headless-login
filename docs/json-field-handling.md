# JSON Field Handling in Database Models

## Overview

This plugin implements a **schema-driven approach** to JSON field handling in database models. The system automatically handles JSON encoding/decoding based on schema definitions, eliminating the need for manual JSON operations in application code.

## Architecture

The JSON field handling follows a three-layer architecture:

### 1. Database Table
- **Purpose**: Physical storage of Provider config entries as table rows
- **JSON Storage**: Nested array data is stored as JSON strings in LONGTEXT columns

### 2. Schema Layer  
- **Purpose**: Defines the shape and structure of Provider config entries
- **JSON Definition**: Fields marked with `is_json: true` are automatically handled as JSON
- **Sanitization**: Provides sanitize callbacks that encode arrays to JSON for database storage

### 3. Model Layer
- **Purpose**: Provides a type-safe interface for reading and writing data
- **Reading**: Automatically decodes JSON fields to arrays when accessed
- **Writing**: Stores arrays in memory, encodes to JSON only during save operations

## Implementation Details

### Schema Definition (Provider Level)

Mark JSON fields in your schema's `definitions()` method:

```php
// /src/Providers/Schema.php
protected static function definitions(): array {
    return [
        'client_options' => [
            'sql_type'          => 'LONGTEXT NULL',
            'is_json'           => true,  // Marks this as a JSON field
            'sanitize_callback' => static function ( $value ) {
                if ( is_array( $value ) ) {
                    return wp_json_encode( $value );
                }
                if ( is_string( $value ) && ! empty( $value ) ) {
                    json_decode( $value );
                    return json_last_error() === JSON_ERROR_NONE ? $value : null;
                }
                return null;
            },
        ],
        // ... other fields
    ];
}
```

### Base Model Behavior (Generic Level)

All JSON handling is done automatically by the Base Model class:

```php
// /src/Database/Model.php handles ALL JSON logic
// No JSON-specific code needed in individual model classes
```

### Provider Model (Application Level)

The Provider Model focuses on business logic, not JSON handling:

```php
// /src/Providers/Model.php 
class Model extends BaseModel {
    // No JSON-specific code needed!
    // All JSON handling is delegated to the Base Model
    
    // Focus on provider-specific business logic:
    protected function validate() {
        // Validate provider types, required fields, etc.
        // JSON validation is handled automatically by Base Model
    }
}
```

### Model Behavior

#### Loading Data from Database
When data is loaded from the database, JSON fields are decoded **once** for efficiency:

```php
// Data comes from database with JSON strings
$db_row = [
    'id' => 1,
    'client_options' => '{"client_id":"abc123","scopes":["read","write"]}' // JSON string
];

// Model automatically decodes JSON fields during construction
$provider = Model::from_array( $db_row );

// client_options is now an array in memory (decoded once)
// No repeated JSON decoding happens on access
```

#### Reading JSON Fields
Accessing JSON fields is now very fast - no decoding overhead:

```php
// These calls are fast - just return the already-decoded array
$options = $provider->client_options;  // Returns array immediately
$client_id = $provider->client_options['client_id'];  // No JSON decoding
$scopes = $provider->client_options['scopes'];  // No JSON decoding
```

#### Writing JSON Fields  
Setting JSON fields works with arrays directly:

```php
$provider = new Provider( Schema::class );

// Set as array - stored as array in memory
$provider->client_options = [
    'client_id'     => 'abc123',
    'client_secret' => 'xyz789',
    'scopes'        => [ 'read', 'write' ]
];

// JSON encoding happens only during save (not on every access)
$provider->save(); // client_options is encoded to JSON for database storage
```

### Validation and Error Handling

The model validates JSON field values during assignment:

```php
// Valid - arrays are accepted
$provider->client_options = [ 'key' => 'value' ];

// Valid - null is accepted  
$provider->client_options = null;

// Invalid - objects are rejected
$provider->client_options = new stdClass(); // Throws InvalidArgumentException
```

## Developer Workflow

### 1. Define Provider Types
Developers define Provider Types in code, specifying which fields contain nested arrays:

```php
// In Schema class
'login_options' => [
    'sql_type' => 'LONGTEXT NULL',
    'is_json'  => true,
    'sanitize_callback' => /* JSON encoding callback */
],
```

### 2. Site Owner Configuration
Site owners configure provider instances via REST API, providing nested data structures:

```json
POST /wp-json/wp-graphql-headless-login/v1/providers
{
    "type": "oauth2",
    "name": "Google OAuth",
    "client_options": {
        "client_id": "abc123",
        "client_secret": "xyz789",
        "redirect_uri": "https://example.com/callback"
    }
}
```

### 3. Application Usage
The application reads provider configs with automatic JSON decoding:

```php
$provider = Provider::find_by_slug( 'google-oauth' );
$client_id = $provider->client_options['client_id']; // Direct array access
```

## Benefits

- **Type Safety**: JSON fields are always arrays in application code
- **Developer Experience**: No manual JSON encoding/decoding required
- **Schema-Driven**: JSON behavior is defined once in the schema
- **Performance**: 
  - JSON decoding happens **once** when loading from database
  - Field access is fast (no repeated JSON decoding)
  - Only modified fields are saved to database
- **Validation**: Invalid JSON field values are caught early
- **Maintainable**: Adding new JSON fields only requires schema changes

## Migration from Hardcoded Approach

If migrating from a hardcoded `$json_fields` approach:

1. **Remove** the `$json_fields` property from Model classes
2. **Add** `is_json: true` to relevant fields in Schema definitions  
3. **Add** appropriate sanitize callbacks for JSON encoding
4. **Test** that existing data is properly decoded/encoded

The Model class will automatically detect JSON fields from the schema and handle them appropriately.
