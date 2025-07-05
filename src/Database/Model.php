<?php
/**
 * Models the Database table for a given schema.
 *
 * @package WPGraphQL\Login\Database
 * @since @todo
 */

declare( strict_types=1 );

namespace WPGraphQL\Login\Database;

/**
 * Class - Model
 *
 * @phpstan-implements \ArrayAccess<string, mixed>
 */
abstract class Model implements \ArrayAccess {
	/**
	 * The schema.
	 *
	 * @var class-string<\WPGraphQL\Login\Database\Schema>
	 */
	protected static $schema;

	/**
	 * The model data.
	 *
	 * @var array<string,mixed>
	 */
	protected array $data = [];

	/**
	 * Tracked fields that need to be updated. Keyed to themselves for easy access.
	 *
	 * @var array<string,string>
	 */
	protected array $dirty_fields = [];

	/**
	 * Initialize an instance of the model
	 *
	 * @param class-string<\WPGraphQL\Login\Database\Schema> $schema The schema to use for the model.
	 * @param array<string,mixed>                            $data Optional. Initial data for the model.
	 *
	 * @return void
	 * @throws \InvalidArgumentException If the schema is not a subclass of Schema.
	 */
	public function __construct( string $schema, array $data = [] ) {
		if ( ! is_subclass_of( $schema, Schema::class ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %1$s is the schema class name, %2$s is the expected class name.
					esc_html__( 'Schema %1$s must be a subclass of %2$s.', 'wp-graphql-headless-login' ),
					esc_html( $schema ),
					esc_html( Schema::class ),
				)
			);
		}

		static::$schema = $schema;

		// Prime the data.
		foreach ( static::$schema::get_columns() as $field_name => $definition ) {
			$value = $data[ $field_name ] ?? ( $definition['default'] ?? null );

			// Resolve the potential callable.
			if ( is_callable( $value ) ) {
				$value = $value();
			}

			// If the column is a JSON field, decode it.
			if ( ! empty( $definition['is_json'] ) && is_string( $value ) ) {
				// Decode JSON field to an array.
				$value = $this->decode_json_field( $value );
			}

			$this->data[ $field_name ] = $value;
		}

		// Reset dirty fields as this is initial data.
		$this->dirty_fields = [];
	}

	/**
	 * Magic getter for the model.
	 *
	 * @param string $field The field name.
	 *
	 * @return mixed The value of the field.
	 *
	 * @throws \InvalidArgumentException If the field does not exist.
	 */
	public function __get( string $field ) {
		if ( ! array_key_exists( $field, $this->data ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s: The field name.
					esc_html__( 'Field %s does not exist in the model.', 'wp-graphql-headless-login' ),
					esc_html( $field ),
				)
			);
		}

		// Return the value directly - JSON fields should already be decoded in memory.
		return $this->data[ $field ];
	}

	/**
	 * Magic setter for the model.
	 *
	 * @param string $field The field name.
	 * @param mixed  $value The value to set.
	 *
	 * @throws \InvalidArgumentException If the field does not exist.
	 * @throws \InvalidArgumentException If the value is not valid.
	 */
	public function __set( string $field, $value ): void {
		$column_defs = self::$schema::get_columns();

		// Bail if the field doesn't exist in the schema.
		if ( ! array_key_exists( $field, $column_defs ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s: The field name.
					esc_html__( 'Field %s does not exist in the model.', 'wp-graphql-headless-login' ),
					esc_html( $field ),
				)
			);
		}

		$is_valid = false;
		// For JSON fields, validate and store arrays directly without encoding.
		// Encoding will happen during save via sanitize callback.
		if ( ! empty( $column_defs[ $field ]['is_json'] ) ) {
			$is_valid = is_array( $value );

			// Array contents are sanitized previously, and the json sanitization will handle on save.
			$sanitized_value = $is_valid ? $value : null;
		} else {
			// For non-JSON fields, apply sanitization immediately.
			$sanitize_callback = $column_defs[ $field ]['sanitize_callback'] ?? null;
			$sanitized_value   = is_callable( $sanitize_callback ) ? $sanitize_callback( $value ) : $value;

			// Validate the value.
			$validate_callback = $column_defs[ $field ]['validate_callback'] ?? null;
			$is_valid          = is_callable( $validate_callback ) ? $validate_callback( $sanitized_value ) : true;
		}

		if ( ! $is_valid ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s: The field name.
					esc_html__( 'Field %s is not valid.', 'wp-graphql-headless-login' ),
					esc_html( $field ),
				)
			);
		}

		if ( ! array_key_exists( $field, $this->dirty_fields ) || $this->data[ $field ] !== $sanitized_value ) {
			$this->dirty_fields[ $field ] = $field;
			$this->data[ $field ]         = $sanitized_value;
		}
	}

	/**
	 * Magic isset method for the model.
	 *
	 * @param string $field The field name.
	 */
	public function __isset( string $field ): bool {
		return isset( $this->data[ $field ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists( $offset ): bool {
		return $this->__isset( (string) $offset );
	}

	/**
	 * {@inheritDoc}
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->__get( (string) $offset );
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet( $offset, $value ): void {
		$this->__set( (string) $offset, $value );
	}

	/**
	 * ArrayAccess: Unset an offset.
	 *
	 * @param mixed $offset The offset to unset.
	 * @throws \BadMethodCallException Always throws as unsetting fields is not supported.
	 */
	public function offsetUnset( $offset ): void {
		throw new \BadMethodCallException(
			esc_html__( 'Unsetting model fields is not supported. Set the field to null instead.', 'wp-graphql-headless-login' )
		);
	}

	/**
	 * Checks if the model exists in the database.
	 *
	 * I.e. if the primary key has a value.
	 */
	public function exists(): bool {
		$primary_key = static::$schema::get_primary_key();

		if ( null === $primary_key ) {
			return false;
		}

		return null !== $this->$primary_key;
	}

	/**
	 * Convert the model to an array for API responses.
	 *
	 * @return array<string,mixed>
	 */
	public function to_array(): array {
		return $this->data;
	}

	/**
	 * Get the modified fields.
	 *
	 * @return array<string,mixed> Array of modified fields and their values.
	 */
	public function get_modified_fields(): array {
		$modified = [];
		foreach ( array_keys( $this->dirty_fields ) as $field ) {
			$modified[ $field ] = $this->data[ $field ];
		}
		return $modified;
	}

	/**
	 * Check if a field is a JSON field.
	 *
	 * @param string $field The field name.
	 */
	protected function is_json_field( string $field ): bool {
		return in_array( $field, static::$schema::get_json_fields(), true );
	}

	/**
	 * Get the modified fields for database storage (with JSON fields encoded).
	 *
	 * @return array<string,mixed> Array of modified fields and their database-ready values.
	 */
	protected function get_modified_fields_for_db(): array {
		$column_defs = static::$schema::get_columns();
		$modified    = [];

		foreach ( array_keys( $this->dirty_fields ) as $field ) {
			$value = $this->data[ $field ];

			// Apply sanitization for database storage.
			$sanitize_callback = $column_defs[ $field ]['sanitize_callback'] ?? null;
			if ( is_callable( $sanitize_callback ) ) {
				$value = $sanitize_callback( $value );
			}

			$modified[ $field ] = $value;
		}

		return $modified;
	}

	/**
	 * Check if the model has been modified.
	 */
	public function is_modified(): bool {
		return ! empty( $this->dirty_fields );
	}

	/**
	 * Reset the modified fields tracking.
	 */
	public function reset_modified(): void {
		$this->dirty_fields = [];
	}

	/**
	 * Save the model to the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		global $wpdb;

		// It's a success if there are no fields to update.
		if ( empty( $this->dirty_fields ) ) {
			return true;
		}

		$table_name  = static::$schema::get_table_name();
		$primary_key = static::$schema::get_primary_key();

		// Get the fields that need to be updated, with JSON fields properly encoded.
		$fields = $this->get_modified_fields_for_db();

		// Update the database entry if it exists.
		if ( $this->exists() ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$rows_updated = $wpdb->update(
				$table_name,
				$fields,
				[ $primary_key => $this->$primary_key ],
			);

			if ( false === $rows_updated ) {
				return new \WP_Error(
					'graphql-login-model-update-failed',
					esc_html__( 'Failed to update model', 'wp-graphql-headless-login' ),
					[ 'error' => $wpdb->last_error ]
				);
			}

			return true;
		}

		// Insert the model if it doesn't exist.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$rows_updated = $wpdb->insert(
			$table_name,
			$fields,
		);

		if ( false === $rows_updated ) {
			return new \WP_Error(
				'graphql-login-model-create-failed',
				esc_html__( 'Failed to create model', 'wp-graphql-headless-login' ),
				[ 'error' => $wpdb->last_error ]
			);
		}

		// Update the primary key.
		if ( null !== $primary_key ) {
			$this->$primary_key = $wpdb->insert_id;
		}

		return true;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function delete() {
		global $wpdb;

		$table_name  = static::$schema::get_table_name();
		$primary_key = static::$schema::get_primary_key();
		if ( null === $primary_key || ! $this->exists() ) {
			return new \WP_Error(
				'graphql-login-model-delete-failed',
				esc_html__( 'Object does not exist or has no primary key.s', 'wp-graphql-headless-login' )
			);
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows_deleted = $wpdb->delete(
			$table_name,
			[ $primary_key => $this->$primary_key ],
		);

		if ( false === $rows_deleted ) {
			return new \WP_Error(
				'graphql-login-model-delete-failed',
				esc_html__( 'Failed to delete from database.', 'wp-graphql-headless-login' ),
				[ 'error' => $wpdb->last_error ]
			);
		}

		// Reset the model data.
		$this->data         = [];
		$this->dirty_fields = [];
		return true;
	}

	/**
	 * Decode JSON field after loading data.
	 *
	 * @param string $json The JSON string to decode.
	 * @return array<string,mixed>|null Decoded JSON as an associative array, or null if decoding fails.
	 */
	protected function decode_json_field( string $json ): ?array {
		if ( empty( $json ) ) {
			return null;
		}

		// Decode JSON and return as array.
		$data = json_decode( $json, true );

		return is_array( $data ) ? $data : null;
	}
}
