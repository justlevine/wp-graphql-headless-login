<?php
/**
 * The ClientOptions GraphQL Object.
 *
 * @package WPGraphQL\Login\Type\WPInterface
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Type\WPInterface;

use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;

/**
 * Class - ClientOptions
 */
class ClientOptions extends InterfaceType {
	use TypeResolverTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function type_name(): string {
		return 'LoginClientOptions';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Client Options for the Headless Login provider.', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		// Aggregate client options fields from all provider types.
		$fields         = [];
		$provider_types = \WPGraphQL\Login\Providers\ProviderRegistry::get_instance()->get_provider_types();
		foreach ( $provider_types as $provider_type ) {
			if ( method_exists( $provider_type, 'get_client_options_fields' ) ) {
				$fields = array_merge( $fields, $provider_type::get_client_options_fields() );
			}
		}
		return $fields;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param array<string,mixed> $value The value.
	 */
	public static function get_resolved_type_name( $value ): ?string {
		return graphql_format_type_name( ucfirst( $value['__typename'] ) . 'ClientOptions' );
	}
}
