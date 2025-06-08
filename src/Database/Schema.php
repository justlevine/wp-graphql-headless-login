<?php
/**
 * Abstract class for defining a database schema.
 *
 * @package WPGraphQL\Login\Database
 * @since @todo
 */

declare( strict_types=1 );

namespace WPGraphQL\Login\Database;

/**
 * Class - Schema
 *
 * @phpstan-type ColumnDefinition array{
 *   sql_type: string,
 *   default?: mixed|callable():mixed,
 *   is_json?: bool,
 *   sanitize_callback?: callable(string):mixed,
 *   validate_callback?: callable(string):bool,
 * }
 */
abstract class Schema {
	/**
	 * The table prefix for the plugin
	 *
	 * @var string
	 */
	public const TABLE_PREFIX = 'graphql_login_';

	/**
	 * The schema version.
	 *
	 * @var string
	 */
	protected static string $schema_version;

	/**
	 * The name of the table (prefixed with the name of the plugin)
	 *
	 * @var string
	 */
	protected static string $table_name;

	/**
	 * A list of the columns for the model.
	 *
	 * @var array<string,ColumnDefinition>
	 */
	protected static array $columns;

	/**
	 * A list of the indexes for the model
	 *
	 * In the format of 'index_name' => 'index_def', e.g.
	 *     'url' => 'url'
	 *
	 * @var string[]
	 */
	protected static array $indexes;

	/**
	 * The name of the primary key for the model
	 *
	 * @var string
	 */
	protected static $primary_key = null;

	/**
	 * The column definitions.
	 *
	 * @return array<string,ColumnDefinition>
	 */
	abstract protected static function definitions(): array;

	/**
	 * Return the table name.
	 */
	public static function get_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . self::TABLE_PREFIX . static::$table_name;
	}

	/**
	 * Get the column definitions.
	 *
	 * @return array<string,ColumnDefinition>
	 */
	public static function get_columns(): array {
		if ( ! isset( static::$columns ) ) {
			static::$columns = static::definitions();
		}

		return static::$columns;
	}

	/**
	 * Get the indexes.
	 *
	 * @return array<string,string>
	 */
	public static function get_indexes(): array {
		return static::$indexes;
	}

	/**
	 * Get the primary key.
	 */
	public static function get_primary_key(): ?string {
		return static::$primary_key;
	}

	/**
	 * Get the fields that should be treated as JSON.
	 *
	 * @return string[] Array of field names that are JSON fields.
	 */
	public static function get_json_fields(): array {
		$json_fields = [];
		foreach ( static::get_columns() as $field_name => $definition ) {
			if ( ! empty( $definition['is_json'] ) ) {
				$json_fields[] = $field_name;
			}
		}
		return $json_fields;
	}

	/**
	 * Create the table if it doesn't exist.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public static function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = static::get_table_name();

		$columns_sql = '';
		foreach ( static::$columns as $column_name => $column_def ) {
			$columns_sql .= $column_name . ' ' . $column_def['sql_type'] . ', ' . PHP_EOL;
		}

		$indexes_sql = '';
		foreach ( static::$indexes as $index ) {
			$indexes_sql .= $index . ', ' . PHP_EOL;
		}

		$sql = "CREATE TABLE $table_name (
			" . rtrim( $columns_sql, ', ' . PHP_EOL ) . '
			' . ( static::$primary_key ? 'PRIMARY KEY ( ' . static::$primary_key . ' )' : '' ) . '
			' . ( ! empty( $indexes_sql ) ? rtrim( $indexes_sql, ', ' . PHP_EOL ) : '' ) . "
		) $charset_collate;";

		if ( ! function_exists( 'dbDelta' ) ) {
			// @phpstan-ignore requireOnce.fileNotFound
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Direct database call is necessary for schema creation and there's no appropriate caching for schema operations.
		dbDelta( $sql );
		// phpcs:enable

		if ( $wpdb->last_error ) {
			return new \WP_Error(
				'graphql-login-schema-create-failed',
				esc_html__( 'Failed to create schema', 'wp-graphql-headless-login' ),
				[ 'error' => $wpdb->last_error ]
			);
		}

		update_option( $table_name . '_db_version', static::$schema_version );

		return true; // Return true to indicate success.
	}

	/**
	 * Check if the table exists.
	 */
	public static function table_exists(): bool {
		global $wpdb;

		$table_name = static::get_table_name();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct DB query is necessary here and caching would be inefficient for a simple existence check.
		$result = $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
		);
		// phpcs:enable

		return $result === $table_name;
	}

	/**
	 * Check if the table is up to date.
	 */
	public static function needs_schema_updata(): bool {
		$table_name = static::get_table_name();

		$version = get_option( $table_name . '_db_version' );

		if ( empty( $version ) ) {
			return true;
		}

		return version_compare( $version, static::$schema_version, '<' );
	}

	/**
	 * Drop the table.
	 */
	public static function drop_table(): void {
		global $wpdb;

		$table_name = static::get_table_name();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Direct database call is necessary for schema deletion and there's no appropriate caching for schema operations.
		$wpdb->query(
			$wpdb->prepare(
				'DROP TABLE IF EXISTS %s',
				$table_name
			)
		);
		// phpcs:enable

		// Delete the version option.
		delete_option( $table_name . '_db_version' );
	}

	/**
	 * Sanitize JSON field before saving to the database.
	 *
	 * @param array<string,mixed>|string $value The value to sanitize.
	 * @return ?non-empty-string
	 */
	protected static function sanitize_json_field( $value ): ?string {
		// If it's already an array, encode it to JSON for storage.
		if ( is_array( $value ) ) {
			return wp_json_encode( $value ) ?: null;
		}

		// Return null for invalid values.
		if ( ! is_string( $value ) || empty( $value ) ) {
			return null;
		}

		// If it's a string, validate it's valid JSON.
		json_decode( $value );
		return json_last_error() === JSON_ERROR_NONE ? $value : null;
	}
}
