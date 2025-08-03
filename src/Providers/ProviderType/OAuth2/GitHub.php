<?php
/**
 * GitHub OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Providers\ProviderType\OAuth2
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers\ProviderType\OAuth2;

/**
 * GitHub OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Providers\ProviderType\OAuth2
 * @since 0.0.1
 */
class GitHub extends AbstractOAuth2Type {
	/**
	 * GitHub constructor.
	 */
	public function __construct() {
		parent::__construct( \WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\Github::class );
	}

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
		return __( 'GitHub', 'wp-graphql-headless-login' );
	}

	/**
	 * Get the provider slug.
	 */
	public static function get_slug(): string {
		return 'github';
	}

	/**
	 * Get the provider options from settings.
	 *
	 * @param array<string,mixed> $settings Provider settings array.
	 * @return array<string,mixed> Options for the provider.
	 */
	protected function get_options( array $settings ): array {
		return [
			'clientId'     => $settings['clientId'] ?? null,
			'clientSecret' => $settings['clientSecret'] ?? null,
			'redirectUri'  => $settings['redirectUri'] ?? null,
			'scope'        => ! empty( $settings['scope'] ) ? $settings['scope'] : [],
		];
	}

	/**
	 * Get the client options schema.
	 *
	 * @return array<string,mixed> Client options schema.
	 */
	public static function get_client_options_schema(): array {
		return [
			'scope' => [
				'type'        => 'array',
				'description' => __( 'Scope', 'wp-graphql-headless-login' ),
				'help'        => sprintf(
					// translators: %s is the URL to the GitHub scopes documentation.
					__( 'The scope to request from the provider. See %s for a list of available scopes.', 'wp-graphql-headless-login' ),
					'<a href="https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/scopes-for-oauth-apps#available-scopes" target="_blank" rel="noopener noreferrer">GitHub scopes documentation</a>'
				),
				'order'       => 10,
				'advanced'    => true,
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
