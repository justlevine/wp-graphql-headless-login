<?php
/**
 * GitHub OAuth2 provider implementation.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Providers;

/**
 * Class GitHubProvider
 */
class GitHubProvider extends OAuth2Provider {
	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return 'GitHub';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'github';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return array_merge(
			parent::get_client_options_schema(),
			[
				'scope' => [
					'description'       => __( 'The OAuth2 scopes to request.', 'wp-graphql-headless-login' ),
					'type'              => 'string',
					'label'             => __( 'Scopes', 'wp-graphql-headless-login' ),
					'required'          => false,
					'default'           => 'user:email',
					'help'              => sprintf(
						/* translators: %s: GitHub scopes documentation URL */
						__( 'Space-separated list of scopes to request. See %s for available scopes.', 'wp-graphql-headless-login' ),
						'https://docs.github.com/apps/building-oauth-apps/scopes-for-oauth-apps/'
					),
					'sanitize_callback' => 'sanitize_text_field',
				],
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_login_options_schema(): array {
		return array_merge(
			parent::get_login_options_schema(),
			[
				'usernameSuffix' => [
					'description'       => __( 'Suffix to append to usernames.', 'wp-graphql-headless-login' ),
					'type'              => 'string',
					'label'             => __( 'Username Suffix', 'wp-graphql-headless-login' ),
					'required'          => false,
					'help'              => __( 'If set, this will be appended to usernames to avoid conflicts. E.g. "-gh" will turn "admin" into "admin-gh"', 'wp-graphql-headless-login' ),
					'default'           => '-gh',
					'sanitize_callback' => 'sanitize_text_field',
				],
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_authorization_url( array $client_config, array $state_params = [] ): string {
		$base_url = 'https://github.com/login/oauth/authorize';

		$params = [
			'client_id'    => $client_config['clientId'],
			'redirect_uri' => $client_config['redirectUri'],
			'scope'        => $client_config['scope'] ?? 'read:user user:email',
			'state'        => wp_json_encode( $state_params ),
		];

		return add_query_arg( urlencode_deep( $params ), $base_url );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_token_response( string $code, array $client_config ) {
		$body_data = [
			'client_id'     => $client_config['clientId'],
			'client_secret' => $client_config['clientSecret'],
			'code'          => $code,
			'redirect_uri'  => $client_config['redirectUri'],
		];

		$request_args = [
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $body_data ),
		];

		if ( false === $request_args['body'] ) {
			return new \WP_Error(
				'invalid_request_body',
				__( 'Failed to encode request body.', 'wp-graphql-headless-login' )
			);
		}

		$response = wp_remote_post( 'https://github.com/login/oauth/access_token', $request_args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['access_token'] ) ) {
			return new \WP_Error(
				'invalid_token_response',
				__( 'Invalid token response from GitHub.', 'wp-graphql-headless-login' )
			);
		}

		return [
			'access_token'  => $body['access_token'],
			// GitHub doesn't provide refresh tokens for OAuth Apps.
			'refresh_token' => null,
			'expires_in'    => null,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_user_data( string $access_token, array $client_config ) {
		$request_args = [
			'headers' => [
				'Accept'        => 'application/vnd.github.v3+json',
				'Authorization' => 'Bearer ' . $access_token,
			],
			'timeout' => 15,
		];

		// Get the user profile.
		$response = wp_remote_get( 'https://api.github.com/user', $request_args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['id'] ) ) {
			return new \WP_Error(
				'invalid_user_data',
				__( 'Invalid user data response from GitHub.', 'wp-graphql-headless-login' )
			);
		}

		// Get the user's primary email if it's not in the profile.
		if ( empty( $body['email'] ) ) {
			$email_response = wp_remote_get( 'https://api.github.com/user/emails', $request_args );

			if ( ! is_wp_error( $email_response ) ) {
				$emails = json_decode( wp_remote_retrieve_body( $email_response ), true );
				if ( is_array( $emails ) ) {
					foreach ( $emails as $email ) {
						if ( ! empty( $email['primary'] ) && ! empty( $email['verified'] ) ) {
							$body['email'] = $email['email'];
							break;
						}
					}
				}
			}
		}

		return [
			'id'        => (string) $body['id'],
			'email'     => $body['email'] ?? null,
			'login'     => $body['login'] ?? null,
			'firstName' => null,
			'lastName'  => null,
			'locale'    => null,
			'avatarUrl' => $body['avatar_url'] ?? null,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function map_user_data( array $data ): array {
		$mapped = parent::map_user_data( $data );

		// Get username suffix from options.
		$suffix = $this->login_options['usernameSuffix'] ?? '-gh';

		// If we have a GitHub login name, use it as the username.
		if ( ! empty( $data['login'] ) ) {
			$mapped['user_login'] = $data['login'] . $suffix;
		}

		return $mapped;
	}
}
