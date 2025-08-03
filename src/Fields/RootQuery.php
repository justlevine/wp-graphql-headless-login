<?php
/**
 * Registers fields to RootQuery
 *
 * @package WPGraphQL\Login\Fields
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Fields;

use WPGraphQL\Login\Auth\Client as AuthClient;
use WPGraphQL\Login\Model\Client as ClientModel;
use WPGraphQL\Login\Providers\ProviderRegistry;
use WPGraphQL\Login\Type\Enum\ProviderEnum;
use WPGraphQL\Login\Type\Enum\ProviderTypeEnum;
use WPGraphQL\Login\Type\WPObject\Client;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\FieldsType;

/**
 * Class - RootQuery
 */
class RootQuery extends FieldsType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RootQuery';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_type_name(): string {
		return static::type_name();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'loginClients' => [
				'type'        => [ 'list_of' => Client::get_type_name() ],
				'description' => static fn () => __( 'The registered Headless Login clients.', 'wp-graphql-headless-login' ),
				'args'        => [
					'type'        => [
						'type'        => ProviderTypeEnum::get_type_name(),
						'description' => static fn () => __( 'Filter providers by type.', 'wp-graphql-headless-login' ),
					],
					'enabledOnly' => [
						'type'         => 'Boolean',
						'description'  => static fn () => __( 'Only return enabled providers. Defaults to `true`.', 'wp-graphql-headless-login' ),
						'defaultValue' => true,
					],
				],
				'resolve'     => static function ( $source, array $args ): ?array {
					$registry = ProviderRegistry::get_instance();

					$type = ! empty( $args['type'] ) ? $args['type'] : null;

					$providers = null !== $type
							? $registry->get_providers_by_type( $type, $args['enabledOnly'] ?? true )
							: $registry->get_providers();

					$clients = [];
					foreach ( $providers as $provider ) {
						$client = new AuthClient( $provider );

						$clients[] = new ClientModel( $client );
					}

					return ! empty( $clients ) ? $clients : null;
				},
			],
			'loginClient'  => [
				'type'        => Client::get_type_name(),
				'description' => static fn () => __( 'The Headless Login client for the provided client ID.', 'wp-graphql-headless-login' ),
				'args'        => [
					'id'       => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => static fn () => __( 'The Client ID.', 'wp-graphql-headless-login' ),
					],
					'provider' => [
						'type'              => ProviderEnum::get_type_name(),
						'description'       => static fn () => __( 'The Provider slug.', 'wp-graphql-headless-login' ),
						// @todo remove in next breaking.
						'deprecationReason' => static fn () => __( 'Use `id` instead.', 'wp-graphql-headless-login' ),
					],
				],
				'resolve'     => static function ( $source, array $args ): ?ClientModel {
					// Deprecation message for the old `provider`.
					if ( ! empty( $args['provider'] ) ) {
						_deprecated_argument(
							'loginClient',
							'@since @todo',
							'The `provider` argument is deprecated and will be removed in a future release. Use `id` instead.'
						);

						$args['id'] = ! empty( $args['id'] ) ? $args['id'] : $args['provider'];
					}

					$provider = ProviderRegistry::get_instance()->get_provider( $args['id'] );

					if ( null === $provider ) {
						return null;
					}

					$client = new AuthClient( $provider );

					return new ClientModel( $client );
				},
			],
		];
	}
}
