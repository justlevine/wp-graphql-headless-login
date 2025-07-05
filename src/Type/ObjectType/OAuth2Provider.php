<?php
/**
 * The OAuth2Provider object type.
 *
 * @package WPGraphQL\Login\Type\ObjectType
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Type\ObjectType;

use WPGraphQL\Login\Type\Interface\ProviderInterface;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - OAuth2Provider
 */
class OAuth2Provider extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OAuth2Provider';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'An OAuth2 authentication provider instance.', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'authorizationUrl' => [
				'type'        => 'String',
				'description' => __( 'The URL to redirect to for authorization.', 'wp-graphql-headless-login' ),
				'resolve'     => static function ( $provider ) {
					return $provider->get_authorization_url();
				},
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			ProviderInterface::get_type_name(),
		];
	}
}
