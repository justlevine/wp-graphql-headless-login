<?php
/**
 * Generic OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType\OAuth2;

/**
 * Generic OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */
class Generic extends AbstractOAuth2Type {
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
		return __( 'OAuth2 (Generic)', 'wp-graphql-headless-login' );
	}

	/**
	 * Get the provider slug.
	 */
	public static function get_slug(): string {
		return 'oauth2-generic';
	}

	/**
	 * Get the provider options from settings.
	 *
	 * @param array<string,mixed> $settings Provider settings array.
	 * @return array<string,mixed> Options for the provider.
	 */
	protected function get_options( array $settings ): array {
		return [
			'clientId'                => $settings['clientId'] ?? null,
			'clientSecret'            => $settings['clientSecret'] ?? null,
			'redirectUri'             => $settings['redirectUri'] ?? null,
			'urlAuthorize'            => ! empty( $settings['urlAuthorize'] ) ? $settings['urlAuthorize'] : null,
			'urlAccessToken'          => ! empty( $settings['urlAccessToken'] ) ? $settings['urlAccessToken'] : null,
			'urlResourceOwnerDetails' => ! empty( $settings['urlResourceOwnerDetails'] ) ? $settings['urlResourceOwnerDetails'] : null,
			'scope'                   => ! empty( $settings['scope'] ) ? $settings['scope'] : [],
			'scopeSeparator'          => $settings['scopeSeparator'] ?? ',',
		];
	}

	/**
	 * Get the client options schema.
	 *
	 * @return array<string,mixed> Client options schema.
	 */
	public static function get_client_options_schema(): array {
		return [
			'urlAuthorize'            => [
				'type'        => 'string',
				'description' => __( 'Authorization URL', 'wp-graphql-headless-login' ),
				'help'        => __( 'The URL to redirect the user to in order to authorize the client.', 'wp-graphql-headless-login' ),
				'order'       => 10,
			],
			'urlAccessToken'          => [
				'type'        => 'string',
				'description' => __( 'Access token URL', 'wp-graphql-headless-login' ),
				'help'        => __( 'The URL to request an access token.', 'wp-graphql-headless-login' ),
				'order'       => 11,
			],
			'urlResourceOwnerDetails' => [
				'type'        => 'string',
				'description' => __( 'Resource Owner URL', 'wp-graphql-headless-login' ),
				'help'        => __( 'The URL to request the resource owner details.', 'wp-graphql-headless-login' ),
				'order'       => 12,
			],
			'scope'                   => [
				'type'        => 'array',
				'description' => __( 'Scope', 'wp-graphql-headless-login' ),
				'order'       => 13,
				'items'       => [ 'type' => 'string' ],
			],
			'scopeSeparator'          => [
				'type'        => 'string',
				'description' => __( 'Scope Separator', 'wp-graphql-headless-login' ),
				'order'       => 14,
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
