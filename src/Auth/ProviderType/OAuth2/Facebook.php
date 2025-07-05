<?php
/**
 * Facebook OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType\OAuth2;

/**
 * Facebook OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */
class Facebook extends AbstractOAuth2Type {
	/**
	 * Get the provider type.
	 */
	public static function get_type(): string {
		return 'oauth2';
	}

	/**
	 * Get the provider name.
	 */
	public static function get_name(): string {
		return __( 'Facebook', 'wp-graphql-headless-login' );
	}

	/**
	 * Get the provider slug.
	 */
	public static function get_slug(): string {
		return 'facebook';
	}

	/**
	 * Get the provider options from settings.
	 *
	 * @param array<string,mixed> $settings Provider settings array.
	 * @return array<string,mixed> Options for the provider.
	 */
	protected function get_options( array $settings ): array {
		return [
			'clientId'        => $settings['clientId'] ?? null,
			'clientSecret'    => $settings['clientSecret'] ?? null,
			'redirectUri'     => $settings['redirectUri'] ?? null,
			'graphApiVersion' => $settings['graphAPIVersion'] ?? 'v15.0',
			'scope'           => ! empty( $settings['scope'] ) ? $settings['scope'] : [],
		];
	}

	/**
	 * Get the client options schema.
	 *
	 * @return array<string,mixed> Client options schema.
	 */
	public static function get_client_options_schema(): array {
		return [
			'graphAPIVersion' => [
				'type'        => 'string',
				'description' => __( 'Graph API Version', 'wp-graphql-headless-login' ),
				'help'        => __( 'The version of the Facebook Graph API to use. E.g. `v15.0`.', 'wp-graphql-headless-login' ),
				'pattern'     => 'v(\d+\.){1,}\d+',
				'order'       => 10,
			],
			'enableBetaTier'  => [
				'type'        => 'boolean',
				'description' => __( 'Enable Beta Tier', 'wp-graphql-headless-login' ),
				'advanced'    => true,
				'order'       => 11,
			],
			'scope'           => [
				'type'        => 'array',
				'description' => __( 'User Fields', 'wp-graphql-headless-login' ),
				'help'        => sprintf(
					// translators: %s is the URL to the Facebook Graph API documentation.
					__( 'The fields to request from the Facebook Graph API. See %s for a list of available fields.', 'wp-graphql-headless-login' ),
					'<a href="https://developers.facebook.com/docs/graph-api/reference/user" target="_blank" rel="noopener noreferrer">Facebook Graph API documentation</a>'
				),
				'order'       => 12,
				'items'       => [ 'type' => 'string' ],
			],
		];
	}

	/**
	 * Get the login options schema.
	 *
	 * @return array<string,mixed> Login options schema.
	 */
	public static function get_login_options_schema(): array {
		return [];
	}

	/**
	 * Map provider user data to WordPress user data.
	 *
	 * @param array<string,mixed> $data Provider user data.
	 * @return array<string,mixed> WordPress user data.
	 */
	protected function map_user_data( array $data ): array {
		// Implement mapping logic as needed.
		return $data;
	}
}
