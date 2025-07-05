<?php
/**
 * The Provider Interface type.
 *
 * @package WPGraphQL\Login\Type\Interface
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Type\Interface;

use WPGraphQL\Login\Providers\Provider;
use WPGraphQL\Login\Type\Enum\ProviderTypeEnum;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;

/**
 * Class - ProviderInterface
 */
class ProviderInterface extends InterfaceType {
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
				'description' => __( 'The unique identifier for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'type'          => [
				'type'        => [ 'non_null' => ProviderTypeEnum::get_type_name() ],
				'description' => __( 'The type of provider.', 'wp-graphql-headless-login' ),
			],
			'name'          => [
				'type'        => 'String',
				'description' => __( 'The display name of the provider instance.', 'wp-graphql-headless-login' ),
			],
			'slug'          => [
				'type'        => 'String',
				'description' => __( 'The unique slug for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'isEnabled'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the provider instance is enabled.', 'wp-graphql-headless-login' ),
			],
			'order'         => [
				'type'        => 'Int',
				'description' => __( 'The order in which the provider instance should appear.', 'wp-graphql-headless-login' ),
			],
			'clientOptions' => [
				'type'        => 'String',
				'description' => __( 'The client-specific options for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'loginOptions'  => [
				'type'        => 'String',
				'description' => __( 'The login-specific options for the provider instance.', 'wp-graphql-headless-login' ),
			],
			'createdAt'     => [
				'type'        => 'String',
				'description' => __( 'The date the provider instance was created.', 'wp-graphql-headless-login' ),
			],
			'updatedAt'     => [
				'type'        => 'String',
				'description' => __( 'The date the provider instance was last updated.', 'wp-graphql-headless-login' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_type_config(): array {
		$config = parent::get_type_config();

		$config['resolveType'] = static function ( Provider $provider ) {
			return ucfirst( $provider->get_type() ) . 'Provider';
		};

		return $config;
	}
}
