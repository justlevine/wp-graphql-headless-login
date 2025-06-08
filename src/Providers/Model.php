<?php
/**
 * Model for provider instances.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

use WPGraphQL\Login\Database\Model as BaseModel;

/**
 * Class - Model
 *
 * @property-read int    $id
 * @property string      $type
 * @property string      $name
 * @property string      $slug
 * @property bool        $is_enabled
 * @property int         $order
 * @property ?array<string,mixed> $client_options
 * @property ?array<string,mixed> $login_options
 * @property string      $created_at
 * @property string      $updated_at
 */
class Model extends BaseModel {
	/**
	 * The provider type instance.
	 *
	 * @var \WPGraphQL\Login\Providers\ProviderType
	 */
	protected ProviderType $provider_type;

	/**
	 * {@inheritDoc}
	 *
	 * @param array<string,mixed> $data Optional. Initial data for the model.
	 *
	 * @throws \InvalidArgumentException If the provider type is invalid.
	 */
	public function __construct( array $data = [] ) {
		parent::__construct( Schema::class, $data );

		// Check if type is provided.
		if ( empty( $this->type ) ) {
			throw new \InvalidArgumentException(
				esc_html__( 'Provider type is required.', 'wp-graphql-headless-login' )
			);
		}

		$provider_type = ProviderType::get_registered_type( $this->type );

		if ( null === $provider_type ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s is the provider type.
					esc_html__( 'Invalid provider type: %s', 'wp-graphql-headless-login' ),
					esc_html( $this->type )
				)
			);
		}

		$this->provider_type = new $provider_type();
	}

	/**
	 * {@inheritDoc}
	 *
	 * Adds provider-specific validation for client_options and login_options.
	 *
	 * @throws \InvalidArgumentException If the value is invalid for the provider type.
	 */
	public function __set( string $field, $value ): void {
		// Other fields are handled by the parent model.
		if ( 'client_options' !== $field && 'login_options' !== $field ) {
			parent::__set( $field, $value );

			return;
		}

		if ( ! is_array( $value ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s: The field name.
					esc_html__( 'Field %s must be an array.', 'wp-graphql-headless-login' ),
					esc_html( $field )
				)
			);
		}

		foreach ( $value as $key => $option_value ) {
			if ( 'client_options' === $field ) {
				$this->set_client_option( $key, $option_value );
			} else {
				$this->set_login_option( $key, $option_value );
			}
		}
	}

	/**
	 * Get a specific client option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $fallback Default value if key doesn't exist.
	 *
	 * @return mixed
	 */
	public function get_client_option( string $key, $fallback = null ) {
		return $this->client_options[ $key ] ?? $fallback;
	}

	/**
	 * Set a specific client option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $value The option value.
	 *
	 * @throws \InvalidArgumentException If the option is invalid for the current provider type.
	 */
	public function set_client_option( string $key, $value ): void {
		// Sanitize the value first.
		$sanitized_value = $this->provider_type->sanitize_client_option( $key, $value );

		// Then validate the sanitized value.
		$valid = $this->provider_type->validate_client_option( $key, $sanitized_value );
		if ( is_wp_error( $valid ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %1$s: The option key, %2$s: The validation error message.
					esc_html__( 'Invalid client option "%1$s": %2$s', 'wp-graphql-headless-login' ),
					esc_html( $key ),
					esc_html( $valid->get_error_message() )
				)
			);
		}

		// Update the options array with the sanitized value.
		$options              = $this->client_options ?? [];
		$options[ $key ]      = $sanitized_value;
		$this->client_options = $options;
	}

	/**
	 * Get a specific login option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $fallback Default value if key doesn't exist.
	 *
	 * @return mixed
	 */
	public function get_login_option( string $key, $fallback = null ) {
		return $this->login_options[ $key ] ?? $fallback;
	}

	/**
	 * Set a specific login option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $value The option value.
	 *
	 * @throws \InvalidArgumentException If the option is invalid for the current provider type.
	 */
	public function set_login_option( string $key, $value ): void {
		// Sanitize the value first.
		$sanitized_value = $this->provider_type->sanitize_login_option( $key, $value );

		// Then validate the sanitized value.
		$valid = $this->provider_type->validate_login_option( $key, $sanitized_value );
		if ( is_wp_error( $valid ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %1$s: The option key, %2$s: The validation error message.
					esc_html__( 'Invalid login option "%1$s": %2$s', 'wp-graphql-headless-login' ),
					esc_html( $key ),
					esc_html( $valid->get_error_message() )
				)
			);
		}

		// Update the options array with the sanitized value.
		$options             = $this->login_options ?? [];
		$options[ $key ]     = $sanitized_value;
		$this->login_options = $options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save() {
		// Set timestamps.
		if ( ! $this->exists() ) {
			$this->created_at = gmdate( 'Y-m-d H:i:s' );
		}
		$this->updated_at = gmdate( 'Y-m-d H:i:s' );

		// Validate before save.
		$is_valid = $this->validate();
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		return parent::save();
	}

	/**
	 * Validates required fields before save.
	 *
	 * @return true|\WP_Error
	 */
	protected function validate() {
		$errors = new \WP_Error();

		// Required fields.
		if ( empty( $this->type ) ) {
			$errors->add( 'required_type', __( 'Provider type is required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->name ) ) {
			$errors->add( 'required_name', __( 'Provider name is required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->slug ) ) {
			$errors->add( 'required_slug', __( 'Provider slug is required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->client_options ) || ! is_array( $this->client_options ) ) {
			$errors->add( 'required_client_options', __( 'Client options are required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->login_options ) || ! is_array( $this->login_options ) ) {
			$errors->add( 'required_login_options', __( 'Login options are required.', 'wp-graphql-headless-login' ) );
		}

		// Validate provider type exists and validate options against schema.
		if ( $errors->has_errors() ) {
			return $errors;
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Overrides parent to remove the ID field on insert operations.
	 *
	 * @return array<string,mixed> Array of modified fields and their database-ready values.
	 */
	protected function get_modified_fields_for_db(): array {
		$fields = parent::get_modified_fields_for_db();

		// Remove the ID field for insert operations (auto-increment handled by database).
		if ( ! $this->exists() && isset( $fields['id'] ) ) {
			unset( $fields['id'] );
		}

		return $fields;
	}
}
