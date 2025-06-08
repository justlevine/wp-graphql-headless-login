<?php
/**
 * Abstract base class for provider types.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

/**
 * Class ProviderType
 *
 * @phpstan-type Setting array{
 *   description: string,
 *   label: string,
 *   type: string,
 *   default?: mixed,
 *   help?: string,
 *   isAdvanced?: bool,
 *   order?: int,
 *   required?: bool,
 *   sanitize_callback: callable(mixed): mixed,
 *   validate_callback?: callable(mixed): (\WP_Error|true),
 * }
 */
abstract class ProviderType {
	/**
	 * Registry of provider types.
	 *
	 * @var array<string,class-string<self>>
	 */
	protected static array $registry = [];

	/**
	 * Register a provider type.
	 *
	 * @param class-string<self> $provider_class The provider class to register.
	 *
	 * @return bool True if registered successfully, false if not.
	 */
	public static function register_type( string $provider_class ): bool {
		if ( ! is_subclass_of( $provider_class, self::class ) ) {
			return false;
		}

		/** @var class-string<self> $provider_class */
		$type = $provider_class::get_type();
		if ( empty( $type ) ) {
			return false;
		}

		self::$registry[ $type ] = $provider_class;

		/**
		 * Fires after a provider type is registered.
		 *
		 * @param class-string<\WPGraphQL\Login\Providers\ProviderType> $provider_class The provider class.
		 */
		do_action( 'graphql_login_provider_type_registered', $provider_class );

		return true;
	}

	/**
	 * Get a registered provider type.
	 *
	 * @param string $type The provider type.
	 *
	 * @return ?class-string<self> The provider class or null if not registered.
	 */
	public static function get_registered_type( string $type ): ?string {
		return self::$registry[ $type ] ?? null;
	}

	/**
	 * Get all registered provider types.
	 *
	 * @return array<string,class-string<self>>
	 */
	public static function get_registered_types(): array {
		return self::$registry;
	}

	/**
	 * Check if a provider type is registered.
	 *
	 * @param string $type The provider type.
	 */
	public static function has_registered_type( string $type ): bool {
		return isset( self::$registry[ $type ] );
	}

	/**
	 * Get the provider type.
	 *
	 * E.g. 'oauth2', 'saml', etc.
	 */
	abstract public static function get_type(): string;

	/**
	 * Get the provider name.
	 *
	 * E.g. 'Facebook', 'GitHub', etc.
	 */
	abstract public static function get_name(): string;

	/**
	 * Get the provider slug.
	 *
	 * E.g. 'facebook', 'github', etc.
	 */
	abstract public static function get_slug(): string;

	/**
	 * Get the client options schema.
	 *
	 * @return array<string,Setting>
	 */
	abstract public static function get_client_options_schema(): array;

	/**
	 * Get the login options schema.
	 *
	 * @return array<string,Setting>
	 */
	abstract public static function get_login_options_schema(): array;

	/**
	 * Authenticate the user with the provider.
	 *
	 * @param array<string,mixed> $input The authentication input.
	 * @return array<string,mixed>|\WP_Error The user data or WP_Error on failure.
	 */
	abstract public function authenticate( array $input );

	/**
	 * Map provider user data to WordPress user data.
	 *
	 * @param array<string,mixed> $data The provider user data.
	 * @return array<string,mixed> The WordPress user data.
	 */
	abstract protected function map_user_data( array $data ): array;

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
	 * Validate a single client option against the schema.
	 *
	 * @param string $key   The option key.
	 * @param mixed  $value The option value.
	 *
	 * @return true|\WP_Error True if valid, WP_Error if invalid.
	 */
	public function validate_client_option( string $key, $value ) {
		return static::validate_option( $key, $value, static::get_client_options_schema() );
	}

	/**
	 * Validate a single login option against the schema.
	 *
	 * @param string $key   The option key.
	 * @param mixed  $value The option value.
	 *
	 * @return true|\WP_Error True if valid, WP_Error if invalid.
	 */
	public function validate_login_option( string $key, $value ) {
		return static::validate_option( $key, $value, static::get_login_options_schema() );
	}

	/**
	 * Sanitize a single client option against the schema.
	 *
	 * @param string $key   The option key.
	 * @param mixed  $value The option value.
	 *
	 * @return mixed The sanitized value.
	 * @throws \InvalidArgumentException If the option key doesn't exist in the schema.
	 */
	public function sanitize_client_option( string $key, $value ) {
		return static::sanitize_option( $key, $value, static::get_client_options_schema() );
	}

	/**
	 * Sanitize a single login option against the schema.
	 *
	 * @param string $key   The option key.
	 * @param mixed  $value The option value.
	 *
	 * @return mixed The sanitized value.
	 * @throws \InvalidArgumentException If the option key doesn't exist in the schema.
	 */
	public function sanitize_login_option( string $key, $value ) {
		return static::sanitize_option( $key, $value, static::get_login_options_schema() );
	}

	/**
	 * Validate a value against a type.
	 *
	 * @param mixed  $value The value to validate.
	 * @param string $type  The type to validate against.
	 *
	 * @return true|\WP_Error True if valid, WP_Error if invalid.
	 */
	protected static function validate_type( $value, string $type ) {
		switch ( $type ) {
			case 'string':
				if ( ! is_string( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be a string.', 'wp-graphql-headless-login' ) );
				}
				break;
			case 'integer':
				if ( ! is_int( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be an integer.', 'wp-graphql-headless-login' ) );
				}
				break;
			case 'number':
				if ( ! is_numeric( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be a number.', 'wp-graphql-headless-login' ) );
				}
				break;
			case 'boolean':
				if ( ! is_bool( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be a boolean.', 'wp-graphql-headless-login' ) );
				}
				break;
			case 'array':
				if ( ! is_array( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be an array.', 'wp-graphql-headless-login' ) );
				}
				break;
			case 'object':
				if ( ! is_array( $value ) && ! is_object( $value ) ) {
					return new \WP_Error( 'invalid_type', __( 'Value must be an object.', 'wp-graphql-headless-login' ) );
				}
				break;
		}

		return true;
	}
}
