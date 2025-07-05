<?php
/**
 * Google OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */



namespace WPGraphQL\Login\Auth\ProviderType\OAuth2;

/**
 * Google OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Auth\ProviderType\OAuth2
 * @since 0.0.1
 */
class Google extends AbstractOAuth2Type {
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
		return __( 'Google', 'wp-graphql-headless-login' );
	}

	/**
	 * Get the provider slug.
	 */
	public static function get_slug(): string {
		return 'google';
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
			'hostedDomain' => ! empty( $settings['hostedDomain'] ) ? $settings['hostedDomain'] : null,
			'prompt'       => ! empty( $settings['promptType'] ) ? $settings['promptType'] : 'consent',
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
			'hostedDomain' => [
				'type'        => 'string',
				'description' => __( 'Hosted Domain', 'wp-graphql-headless-login' ),
				'help'        => __( 'Streamline the login process for accounts owned by a Google Cloud organization. To optimize for Google Cloud organization accounts generally instead of just one organization domain, set a value of an asterisk `*`.', 'wp-graphql-headless-login' ),
				'order'       => 10,
			],
			'promptType'   => [
				'type'        => 'string',
				'description' => __( 'Prompt Type', 'wp-graphql-headless-login' ),
				'help'        => __( 'The type of prompt displayed to the user when authenticating.', 'wp-graphql-headless-login' ),
				'enum'        => [ 'none', 'consent', 'select_account' ],
				'order'       => 11,
			],
			'scope'        => [
				'type'        => 'array',
				'description' => __( 'Scope', 'wp-graphql-headless-login' ),
				'help'        => sprintf(
					// translators: %s is the URL to the Google scopes documentation.
					__( 'The scope to request from the provider. See %s for a list of available scopes.', 'wp-graphql-headless-login' ),
					'<a href="https://developers.google.com/identity/protocols/oauth2/scopes" target="_blank" rel="noopener noreferrer">Google scopes documentation</a>'
				),
				'order'       => 12,
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
