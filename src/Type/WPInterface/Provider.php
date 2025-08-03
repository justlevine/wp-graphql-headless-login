<?php
/**
 * The Provider Interface type.
 *
 * @package WPGraphQL\Login\Type\WPInterface
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Type\WPInterface;

use WPGraphQL\Login\Type\Enum\ProviderTypeEnum;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;

/**
 * Class - Provider
 */
class Provider extends InterfaceType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Provider';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'A authentication provider instance.', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'id'            => [
				'type'        => 'ID',
				'description' => static fn () => __( 'The unique identifier for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'type'          => [
				'type'        => [ 'non_null' => ProviderTypeEnum::get_type_name() ],
				'description' => static fn () => __( 'The type of provider.', 'wp-graphql-headless-login' ),
			],
			'name'          => [
				'type'        => 'String',
				'description' => static fn () => __( 'The display name of the provider instance.', 'wp-graphql-headless-login' ),
			],
			'slug'          => [
				'type'        => 'String',
				'description' => static fn () => __( 'The unique slug for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'isEnabled'     => [
				'type'        => 'Boolean',
				'description' => static fn () => __( 'Whether the provider instance is enabled.', 'wp-graphql-headless-login' ),
			],
			'order'         => [
				'type'        => 'Int',
				'description' => static fn () => __( 'The order in which the provider instance should appear.', 'wp-graphql-headless-login' ),
			],
			'clientOptions' => [
				'type'        => 'String',
				'description' => static fn () => __( 'The client-specific options for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'loginOptions'  => [
				'type'        => 'String',
				'description' => static fn () => __( 'The login-specific options for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'createdAt'     => [
				'type'        => 'String',
				'description' => static fn () => __( 'The date the provider instance was created.', 'wp-graphql-headless-login' ),
			],
			'updatedAt'     => [
				'type'        => 'String',
				'description' => static fn () => __( 'The date the provider instance was last updated.', 'wp-graphql-headless-login' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_type_config(): array {
		$config = parent::get_type_config();

			$config['resolveType'] = static function ( $provider ) {
				// $provider is a Model instance; get the provider type slug for GraphQL type resolution
				if ( method_exists( $provider, 'get_provider_type' ) ) {
					$type = $provider->get_provider_type()->get_type();
				} elseif ( method_exists( $provider, 'get_type' ) ) {
					$type = $provider->get_type();
				} else {
					$type = 'unknown';
				}
				return ucfirst( $type ) . 'Provider';
			};

		return $config;
	}
}
