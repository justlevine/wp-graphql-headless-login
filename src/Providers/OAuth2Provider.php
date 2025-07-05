<?php
/**
 * OAuth2 Provider base class.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

/**
 * Class OAuth2Provider
 */
abstract class OAuth2Provider extends ProviderType {
	/**
	 * Get the provider type.
	 */
	public static function get_type(): string {
		return 'oauth2';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return [
			'clientId'     => [
				'description'       => __( 'The OAuth2 client ID.', 'wp-graphql-headless-login' ),
				'type'              => 'string',
				'label'             => __( 'Client ID', 'wp-graphql-headless-login' ),
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			],
			'clientSecret' => [
				'description'       => __( 'The OAuth2 client secret.', 'wp-graphql-headless-login' ),
				'type'              => 'string',
				'label'             => __( 'Client Secret', 'wp-graphql-headless-login' ),
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			],
			'redirectUri'  => [
				'description'       => __( 'The OAuth2 redirect URI.', 'wp-graphql-headless-login' ),
				'type'              => 'string',
				'label'             => __( 'Redirect URI', 'wp-graphql-headless-login' ),
				'required'          => true,
				'sanitize_callback' => 'esc_url_raw',
			],
			'scope'        => [
				'description'       => __( 'The OAuth2 scopes to request.', 'wp-graphql-headless-login' ),
				'type'              => 'string',
				'label'             => __( 'Scopes', 'wp-graphql-headless-login' ),
				'required'          => false,
				'help'              => __( 'Space-separated list of scopes to request from the provider.', 'wp-graphql-headless-login' ),
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_login_options_schema(): array {
		return [
			'createUserIfNoneExists' => [
				'description'       => __( 'Whether to create a new user if none exists.', 'wp-graphql-headless-login' ),
				'type'              => 'boolean',
				'label'             => __( 'Create User If None Exists', 'wp-graphql-headless-login' ),
				'required'          => false,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			],
			'linkExistingUsers'      => [
				'description'       => __( 'Whether to link the account to an existing user with the same email.', 'wp-graphql-headless-login' ),
				'type'              => 'boolean',
				'label'             => __( 'Link Existing Users', 'wp-graphql-headless-login' ),
				'required'          => false,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			],
		];
	}

	/**
	 * Get the authorization URL for the provider.
	 *
	 * @param array<string,mixed> $client_config The client configuration.
	 * @param array<string,mixed> $state_params Additional state parameters.
	 */
	abstract public function get_authorization_url( array $client_config, array $state_params = [] ): string;

	/**
	 * Exchange the authorization code for an access token.
	 *
	 * @param string              $code The authorization code.
	 * @param array<string,mixed> $client_config The client configuration.
	 *
	 * @return array{
	 *   access_token: string,
	 *   refresh_token?: ?string,
	 *   expires_in?: ?int,
	 * }|\WP_Error
	 */
	abstract protected function get_token_response( string $code, array $client_config );

	/**
	 * Get the user data from the provider.
	 *
	 * @param string              $access_token The access token.
	 * @param array<string,mixed> $client_config The client configuration.
	 *
	 * @return array{
	 *  id: string,
	 *  email?: ?string,
	 *  login?: ?string,
	 *  firstName?: ?string,
	 *  lastName?: ?string,
	 *  locale?: ?string,
	 *  avatarUrl?: ?string,
	 * }|\WP_Error
	 */
	abstract protected function get_user_data( string $access_token, array $client_config );

	/**
	 * {@inheritDoc}
	 */
	public function authenticate( array $input ) {
		if ( empty( $input['code'] ) ) {
			return new \WP_Error(
				'missing_code',
				__( 'No authorization code provided.', 'wp-graphql-headless-login' )
			);
		}

		$provider = ProviderRegistry::get_instance()->get_provider( $this->get_slug() );
		if ( ! $provider ) {
			return new \WP_Error(
				'invalid_provider',
				__( 'Provider not found.', 'wp-graphql-headless-login' )
			);
		}

		$token_response = $this->get_token_response( $input['code'], $provider->client_options );
		if ( is_wp_error( $token_response ) ) {
			return $token_response;
		}

		$user_data = $this->get_user_data( $token_response['access_token'], $provider->client_options );
		if ( is_wp_error( $user_data ) ) {
			return $user_data;
		}

		return $this->map_user_data( $user_data );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function map_user_data( array $data ): array {
		return [
			'user_login'      => $data['login'] ?? '',
			'user_email'      => $data['email'] ?? '',
			'first_name'      => $data['firstName'] ?? '',
			'last_name'       => $data['lastName'] ?? '',
			'locale'          => $data['locale'] ?? get_locale(),
			'user_avatar_url' => $data['avatarUrl'] ?? '',
		];
	}
}
