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
use WPGraphQL\Login\Providers\ProviderType\AbstractProviderType;

/**
 * Class - Model
 *
 * @property-read int             $id
 * @property string               $type
 * @property string               $name
 * @property string               $slug
 * @property bool                 $is_enabled
 * @property int                  $order
 * @property ?array<string,mixed> $settings
 * @property string               $created_at
 * @property string               $updated_at
 */
class Model extends BaseModel {
	/**
	 * The provider type instance.
	 *
	 * @var \WPGraphQL\Login\Providers\ProviderType\AbstractProviderType
	 */
	protected AbstractProviderType $provider_type;

	/**
	 * Provider-specific settings (serialized config)
	 *
	 * @var array<string,mixed>
	 */
	protected array $settings = [];

	/**
	 * Convert the model to an array for REST/JS use.
	 *
	 * @return array<string,mixed>
	 */
	public function to_array(): array {
		return [
			'id'         => $this->id,
			'name'       => $this->name,
			'type'       => $this->type,
			'enabled'    => (bool) $this->is_enabled,
			'order'      => $this->order,
			'settings'   => $this->settings,
			'slug'       => $this->slug,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}

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

		$provider_type = ProviderRegistry::get_instance()->get_provider_type( $this->type );

		if ( null === $provider_type ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %s is the provider type.
					esc_html__( 'Invalid provider type: %s', 'wp-graphql-headless-login' ),
					esc_html( $this->type )
				)
			);
		}

		$this->provider_type = $provider_type;

			// Provider-specific settings (batch sanitize/validate).
		if ( isset( $data['settings'] ) && is_array( $data['settings'] ) ) {
			$this->settings = $this->provider_type->sanitize_and_validate_options( $data['settings'] );
		}
	}

	/**
	 * Validates and updates the provider settings.
	 *
	 * @param array<string,mixed> $settings The settings to update.
	 *
	 * @throws \InvalidArgumentException If the value is invalid for the provider type.
	 */
	public function update_settings( array $settings ): void {
		$this->settings = $this->provider_type->sanitize_and_validate_options( $settings );
	}

	/**
	 * Authenticates, using the provider type instance.
	 *
	 * @param array<string,mixed> $input The authentication input.
	 *
	 * @return array<string,mixed>|\WP_Error
	 */
	public function authenticate( array $input ) {
		return $this->provider_type->authenticate( $input, $this );
	}

	/**
	 * Gets the provider type instance
	 */
	public function get_provider_type(): AbstractProviderType {
		return $this->provider_type;
	}

	/**
	 * Get a specific client option value.
	 *
	 * @param string $key The option key.
	 * @param mixed  $fallback Default value if key doesn't exist.
	 *
	 * @return mixed
	 */

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

		// Required shared fields.
		if ( empty( $this->type ) ) {
			$errors->add( 'required_type', __( 'Provider type is required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->name ) ) {
			$errors->add( 'required_name', __( 'Provider name is required.', 'wp-graphql-headless-login' ) );
		}

		if ( empty( $this->slug ) ) {
			$errors->add( 'required_slug', __( 'Provider slug is required.', 'wp-graphql-headless-login' ) );
		}

		// Validate provider-specific settings in batch.
		$provider_errors = $this->provider_type->validate_options( $this->settings );
		if ( is_wp_error( $provider_errors ) ) {
			foreach ( $provider_errors->get_error_messages() as $msg ) {
				$errors->add( 'invalid_settings', $msg );
			}
		}

		return $errors->has_errors() ? $errors : true;
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
