<?php
/**
 * Schema definition for the Providers table.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

use WPGraphQL\Login\Database\Schema as BaseSchema;

/**
 * Class - Schema
 *
 * @phpstan-import-type ColumnDefinition from \WPGraphQL\Login\Database\Schema
 */
class Schema extends BaseSchema {
	/**
	 * The schema version.
	 *
	 * @var string
	 */
	protected static string $schema_version = '1.0.0';

	/**
	 * The name of the table (without prefix)
	 *
	 * @var string
	 */
	protected static string $table_name = 'providers';

	/**
	 * The primary key for the table
	 *
	 * @var string
	 */
	protected static $primary_key = 'id';

	/**
	 * {@inheritDoc}
	 */
	protected static function definitions(): array {
		return [
			'id'             => [
				'sql_type'          => 'BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'sanitize_callback' => 'absint',
			],
			'type'           => [
				'sql_type'          => 'VARCHAR(100) NOT NULL',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'name'           => [
				'sql_type'          => 'VARCHAR(255) NOT NULL',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'slug'           => [
				'sql_type'          => 'VARCHAR(100) NOT NULL',
				'sanitize_callback' => 'sanitize_key',
			],
			'is_enabled'     => [
				'sql_type'          => 'TINYINT(1) NOT NULL DEFAULT 0',
				'sanitize_callback' => 'rest_sanitize_boolean',
			],
			'order'          => [
				'sql_type'          => 'INT(11) NOT NULL DEFAULT 0',
				'sanitize_callback' => 'absint',
			],
			'client_options' => [
				'sql_type'          => 'LONGTEXT NULL',
				'is_json'           => true,
				'sanitize_callback' => static fn ( $value ) => static::sanitize_json_field( $value ),
			],
			'login_options'  => [
				'sql_type'          => 'LONGTEXT NULL',
				'is_json'           => true,
				'sanitize_callback' => static fn ( $value ) => static::sanitize_json_field( $value ),
			],
			'created_at'     => [
				'sql_type'          => 'DATETIME NOT NULL',
				'sanitize_callback' => static function ( $value ) {
					return gmdate( 'Y-m-d H:i:s', is_numeric( $value ) ? (int) $value : strtotime( $value ) );
				},
			],
			'updated_at'     => [
				'sql_type'          => 'DATETIME NOT NULL',
				'sanitize_callback' => static function ( $value ) {
					return gmdate( 'Y-m-d H:i:s', is_numeric( $value ) ? (int) $value : strtotime( $value ) );
				},
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected static array $indexes = [
		'type'       => 'KEY type (type)',
		'slug'       => 'UNIQUE KEY slug (slug)',
		'is_enabled' => 'KEY is_enabled (is_enabled)',
		'order'      => 'KEY order (order)',
	];
}
