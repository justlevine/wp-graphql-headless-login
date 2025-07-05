<?php
/**
 * Defines the static methods used by AbstractProviderType.
 *
 * @package WPGraphQL\Login\Auth\ProviderType
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType;

/**
 * Trait - ProviderTypeStaticTrait
 *
 * @phpstan-import-type Setting from \WPGraphQL\Login\Admin\Settings\AbstractSettings
 * @phpstan-import-type FieldConfig from \AxeWP\GraphQL\Interfaces\TypeWithFields
 */
trait ProviderTypeStaticTrait {
	/**
	 * Validate a single option against a schema.
	 *
	 * @param string                $key    The option key.
	 * @param mixed                 $value  The option value.
	 * @param array<string,Setting> $schema The schema to validate against.
	 *
	 * @return true|\WP_Error True if valid, WP_Error if invalid.
	 */
	public static function validate_option( string $key, $value, array $schema ) {
		// Check if the key exists in the schema.
		if ( ! isset( $schema[ $key ] ) ) {
			// translators: %s is the field key.
			return new \WP_Error( 'unknown_field', sprintf( __( 'Unknown field: %s', 'wp-graphql-headless-login' ), $key ) );
		}

		$setting = $schema[ $key ];

		// Check if required field is missing.
		if ( null === $value && ! empty( $setting['required'] ) ) {
			// translators: %s is the field key.
			return new \WP_Error( 'missing_required_field', sprintf( __( 'Missing required field: %s', 'wp-graphql-headless-login' ), $key ) );
		}

		// Skip validation if value is null and field is not required.
		if ( null === $value ) {
			return true;
		}

		// Validate the value if a callback is provided.
		if ( isset( $setting['validate_callback'] ) ) {
			$validation_result = $setting['validate_callback']( $value );

			if ( true !== $validation_result ) {
				return $validation_result instanceof \WP_Error ? $validation_result : new \WP_Error( 'invalid_field_value', $validation_result );
			}
		}

		// Type validation.
		return static::validate_type( $value, $setting['type'] );
	}

	/**
	 * Sanitize a single option against a schema.
	 *
	 * @param string                $key    The option key.
	 * @param mixed                 $value  The option value.
	 * @param array<string,Setting> $schema The schema to validate against.
	 *
	 * @return mixed The sanitized value.
	 * @throws \InvalidArgumentException If the option key doesn't exist in the schema.
	 */
	public static function sanitize_option( string $key, $value, array $schema ) {
		// Check if the key exists in the schema.
		if ( ! isset( $schema[ $key ] ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s is the field key.
					esc_html__( 'Unknown field: %s', 'wp-graphql-headless-login' ),
					esc_html( $key )
				)
			);
		}

		$setting = $schema[ $key ];

		// Apply sanitization if a callback is provided.
		if ( isset( $setting['sanitize_callback'] ) && is_callable( $setting['sanitize_callback'] ) ) {
			return $setting['sanitize_callback']( $value );
		}

		return $value;
	}

	/**
	 * Gets the WPGraphQL fields config for the provider settings.
	 *
	 * @return array<string,FieldConfig>
	 */
	public static function get_client_options_fields(): array {
		$fields = array_merge(
			static::default_client_options_fields(),
			static::client_options_fields(),
		);

		/**
		 * Filters the GraphQL fields for the provider's Client Options.
		 *
		 * @param array<string,FieldConfig> $fields An array of WPGraphQL field $configs.
		 * @param string                    $slug  The provider slug.
		 */
		$fields = apply_filters( 'graphql_login_client_options_fields', $fields, static::get_slug() );

		return apply_filters( 'graphql_login_' . static::get_slug() . '_client_options_fields', $fields );
	}

	/**
	 * Gets the WP REST schema config for the client options.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function get_client_options_schema(): array {
		$settings = array_merge(
			static::default_client_options_schema(),
			static::client_options_schema(),
		);

		/**
		 * Filters the WP REST schema for the provider's Client Options settings.
		 *
		 * Useful for modifying Client Options displayed in the admin.
		 *
		 * @param array  $settings An array of WP REST schema $configs.
		 * @param string $slug     The provider slug.
		 */
		$settings = apply_filters( 'graphql_login_client_options_schema', $settings, static::get_slug() );

		return apply_filters( 'graphql_login_' . static::get_slug() . '_client_options_schema', $settings );
	}

	/**
	 * Gets the WPGraphQL fields config for the provider settings.
	 *
	 * @return array<string,FieldConfig>
	 */
	public static function get_login_options_fields(): array {
		$fields = array_merge(
			static::default_login_options_fields(),
			static::login_options_fields(),
		);

		/**
		 * Filters the GraphQL fields for the provider's Client Options.
		 *
		 * @param array $fields An array of WPGraphQL field $configs.
		 * @param string $slug The provider slug.
		 */
		$fields = apply_filters( 'graphql_login_login_options_fields', $fields, static::get_slug() );

		return apply_filters( 'graphql_login_' . static::get_slug() . '_login_options_fields', $fields );
	}

	/**
	 * Gets the WP REST schema config for the Login options.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function get_login_options_schema(): array {
		$settings = array_merge(
			static::default_login_options_schema(),
			static::login_options_schema(),
		);

		/**
		 * Filters the WP REST schema for the provider's Login Options settings.
		 *
		 * Useful for modifying Client Options displayed in the admin.
		 *
		 * @param array  $settings An array of WP REST schema $configs.
		 * @param string $slug     The provider slug.
		 */
		$settings = apply_filters( 'graphql_login_login_options_schema', $settings, static::get_slug() );

		return apply_filters( 'graphql_login_' . static::get_slug() . '_login_options_schema', $settings );
	}

	/**
	 * Gets the default WPGraphQL fields config for the provider client options.
	 *
	 * @return array<string,FieldConfig>
	 */
	public static function default_client_options_fields(): array {
		return [
			'todo' => [
				'type'        => 'Boolean',
				'description' => static fn () => __( 'This field exists solely to generate the  ClientOptions interface, in lieu of the shared custom fields that will be added in a future release', 'wp-graphql-headless-login' ),
			],
		];
	}

	/**
	 * Gets the default WPGraphQL fields config for the provider client options.
	 *
	 * @return array<string,FieldConfig>
	 */
	public static function default_login_options_fields(): array {
		return [
			'useAuthenticationCookie' => [
				'type'        => 'Boolean',
				'description' => static fn () => __( 'Whether to set a WordPress authentication cookie on successful login.', 'wp-graphql-headless-login' ),
			],
		];
	}

	/**
	 * Returns the schema properties for the client options.
	 *
	 * @see ProviderConfig::client_options_schema().
	 *
	 * @return array<string,array<string,mixed>>
	 */
	protected static function default_client_options_schema(): array {
		return [];
	}

	/**
	 * Returns the default schema properties for the Login options.
	 *
	 * @see ProviderConfig::login_options_schema().
	 *
	 * @return array<string,Setting>
	 */
	protected static function default_login_options_schema(): array {
		return [
			'useAuthenticationCookie' => [
				'description'       => __( 'Set authentication cookie', 'wp-graphql-headless-login' ),
				'label'             => __( 'Set Authentication Cookie', 'wp-graphql-headless-login' ),
				'type'              => 'boolean',
				'help'              => __( 'If enabled, a WordPress authentication cookie will be set after a successful login. This is useful for granting access to the WordPress dashboard or other protected areas of the WordPress backend without having to re-authenticate.', 'wp-graphql-headless-login' ),
				'order'             => 2,
				'sanitize_callback' => static function ( $value ) {
					return (bool) $value; },
			],
		];
	}
}
