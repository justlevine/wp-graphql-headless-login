<?php
/**
 * The ProviderType Enum.
 *
 * @package WPGraphQL\Login\Type\Enum
 */

declare(strict_types=1);

namespace WPGraphQL\Login\Type\Enum;

use WPGraphQL\Login\Providers\ProviderRegistry;
use WPGraphQL\Login\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - ProviderTypeEnum
 */
class ProviderTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'ProviderType';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The type of authentication provider.', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$values = [];

		$types = ProviderRegistry::get_instance()->get_provider_types();

		foreach ( $types as $type => $provider_class ) {
			$values[ strtoupper( $type ) ] = [
				'value'       => $type,
				'description' => sprintf(
					// translators: %s is the provider type name.
					__( 'The %s provider type.', 'wp-graphql-headless-login' ),
					$type
				),
			];
		}

		return $values;
	}
}
