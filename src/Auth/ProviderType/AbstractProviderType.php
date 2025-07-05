<?php
/**
 * Abstract base class for provider types.
 *
 * @package WPGraphQL\Login\Auth\ProviderType
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType;

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
abstract class AbstractProviderType {
	use ProviderTypeStaticTrait;

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
	 * Gets the user from the data returned by the provider.
	 *
	 * @param array<string,mixed> $data The data returned by the provider.
	 *
	 * @return ?\WP_User
	 */
	abstract public function get_user_from_data( array $data );

	/**
	 * Process and validate the input data passed to the GraphQL mutation.
	 *
	 * @param array<string,mixed> $input The mutation input.
	 *
	 * @return array<string,mixed>
	 */
	abstract protected function prepare_mutation_input( array $input ): array;

	/**
	 * The constructor
	 */
	public function __construct() {
		/**
		 * Fires after the provider is initialized.
		 *
		 * @param string $slug            The provider slug.
		 * @param self   $provider_config The ProviderConfig static class.
		 */
		do_action( 'graphql_login_after_provider_type_init', static::get_slug(), $this );
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

	/**
	 * Gets the WPGraphQL fields config for the provider settings.
	 *
	 * Should probably be overwritten by the child ProviderConfig.
	 *
	 * @return array<string,mixed>
	 */
	protected static function client_options_fields(): array {
		return [];
	}

	/**
	 * Returns the schema properties for the client options.
	 *
	 * Adds the optional 'help' and `required' property key for use on the frontend. This will be stripped when registering the setting.
	 *
	 * Should probably be overwritten by the child ProviderConfig.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/schema
	 *
	 * @return array<string,mixed>
	 */
	protected static function client_options_schema(): array {
		return [];
	}

	/**
	 * Gets the WPGraphQL fields config for the provider settings.
	 *
	 * Should probably be overwritten by the child ProviderConfig.
	 *
	 * @return array<string,mixed>
	 */
	protected static function login_options_fields(): array {
		return [];
	}

	/**
	 * Returns the schema properties for the provider settings.
	 *
	 * Adds the optional 'help' property key for use on the frontend. This will be stripped when registering the setting.
	 *
	 * Should probably be overwritten by the child ProviderConfig.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/schema
	 *
	 * @return array<string,mixed>
	 */
	protected static function login_options_schema(): array {
		return [];
	}
}
