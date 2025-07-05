<?php
/**
 * Registry for provider types and instances.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

use WPGraphQL\Login\Auth\ProviderType\AbstractProviderType;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class ProviderRegistry
 */
class ProviderRegistry implements Registrable {
	/**
	 * The provider classes to register by default.
	 */
	private const PROVIDER_TYPE_CLASSES = [
		// GitHubProvider::class,
	];

	/**
	 * Registry of provider types.
	 *
	 * @var array<string,\WPGraphQL\Login\Auth\ProviderType\AbstractProviderType>
	 */
	protected static array $type_registry = [];

	/**
	 * The singleton instance.
	 */
	private static ?self $instance = null;

	/**
	 * The instance repository.
	 */
	private Repository $repository;

	/**
	 * Get the singleton instance.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		self::get_instance();
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->repository = new Repository();

		// Register built-in provider types.
		$this->register_core_provider_types();

		/**
		 * Register additional provider types.
		 *
		 * @param self $registry The provider registry instance.
		 */
		do_action( 'graphql_login_init_providers', $this );
	}

	/**
	 * Register core provider types.
	 */
	private function register_core_provider_types(): void {
		foreach ( self::PROVIDER_TYPE_CLASSES as $provider_class ) {
			self::register_provider_type( $provider_class );
		}
	}

	/**
	 * Register a provider type.
	 *
	 * @param class-string<\WPGraphQL\Login\Auth\ProviderType\AbstractProviderType> $provider_type_class The provider type class to register.
	 *
	 * @return bool True if registered successfully, false if not.
	 */
	private static function register_provider_type( string $provider_type_class ): bool {
		if ( ! is_subclass_of( $provider_type_class, AbstractProviderType::class ) ) {
			return false;
		}

		$type = $provider_type_class::get_type();
		if ( empty( $type ) ) {
			return false;
		}

		self::$type_registry[ $type ] = new $provider_type_class();

		/**
		 * Fires after a provider type is registered.
		 *
		 * @param \WPGraphQL\Login\Auth\ProviderType\AbstractProviderType $provider_type The provider type instance.
		 */
		do_action( 'graphql_login_provider_type_registered', self::$type_registry[ $type ] );

		return true;
	}

	/**
	 * Get a provider instance by slug.
	 *
	 * @param string $slug The provider instance slug.
	 */
	public function get_provider( string $slug ): ?Model {
		return $this->repository->find_by_slug( $slug );
	}

	/**
	 * Get all provider instances.
	 *
	 * @param bool $enabled_only Whether to only return enabled providers.
	 *
	 * @return \WPGraphQL\Login\Providers\Model[]
	 */
	public function get_providers( bool $enabled_only = false ): array {
		return $this->repository->find_all( $enabled_only );
	}

	/**
	 * Get all provider instances by type.
	 *
	 * @param string $type The provider type.
	 * @param bool   $enabled_only Whether to only return enabled providers.
	 *
	 * @return \WPGraphQL\Login\Providers\Model[]
	 */
	public function get_providers_by_type( string $type, bool $enabled_only = false ): array {
		return $this->repository->find_by_type(
			$type,
			$enabled_only
		);
	}

	/**
	 * Create a new provider instance.
	 *
	 * @param array<string,mixed> $data The provider instance data.
	 *
	 * @return \WPGraphQL\Login\Providers\Model|\WP_Error The provider instance or WP_Error on failure.
	 */
	public function create_provider( array $data ) {
		$instance = new Model( $data );

		$result = $instance->save();
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $instance;
	}

	/**
	 * Update a provider instance.
	 *
	 * @param string              $slug The provider instance slug.
	 * @param array<string,mixed> $data The provider instance data.
	 *
	 * @return \WPGraphQL\Login\Providers\Model|\WP_Error The provider instance or WP_Error on failure.
	 */
	public function update_provider( string $slug, array $data ) {
		$instance = $this->repository->find_by_slug( $slug );

		if ( null === $instance ) {
			return new \WP_Error(
				'provider_not_found',
				__( 'Provider not found.', 'wp-graphql-headless-login' )
			);
		}

		foreach ( $data as $key => $value ) {
			$instance->$key = $value;
		}

		$result = $instance->save();
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $instance;
	}

	/**
	 * Delete a provider instance.
	 *
	 * @param string $slug The provider instance slug.
	 *
	 * @return \WPGraphQL\Login\Providers\Model|\WP_Error The deleted provider instance or WP_Error on failure.
	 */
	public function delete_provider( string $slug ) {
		$instance = $this->repository->find_by_slug( $slug );

		if ( null === $instance ) {
			return new \WP_Error(
				'provider_not_found',
				__( 'Provider not found.', 'wp-graphql-headless-login' )
			);
		}

		$result = $this->repository->delete( $instance );

		if ( ! $result ) {
			return new \WP_Error(
				'provider_delete_failed',
				__( 'Failed to delete provider.', 'wp-graphql-headless-login' )
			);
		}

		return $instance;
	}

	/**
	 * Get all registered provider types keyed to their slug.
	 *
	 * @return array<string,\WPGraphQL\Login\Auth\ProviderType\AbstractProviderType>
	 */
	public function get_provider_types(): array {
		return self::$type_registry;
	}

	/**
	 * Get a provider type by type.
	 *
	 * @param string $type The provider type slug.
	 */
	public function get_provider_type( string $type ): ?AbstractProviderType {
		return self::$type_registry[ $type ] ?? null;
	}
}
