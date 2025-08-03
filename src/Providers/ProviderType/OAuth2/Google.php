<?php
/**
 * Google OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Providers\ProviderType\OAuth2
 * @since 0.0.1
 */

namespace WPGraphQL\Login\Providers\ProviderType\OAuth2;

/**
 * Class - Instagram
 */
class Google extends AbstractOAuth2Type {
	/**
	 * Google constructor.
	 */
	public function __construct() {
		parent::__construct( \WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\Google::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_type(): string {
		return 'oauth2';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return __( 'Google', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'google';
	}

	/**
	 * {@inheritDoc}
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
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return [
			'hostedDomain' => [
				'type'              => 'string',
				'label'             => __( 'Hosted Domain', 'wp-graphql-headless-login' ),
				'description'       => __( 'Hosted Domain', 'wp-graphql-headless-login' ),
				'help'              => __( 'Streamline the login process for accounts owned by a Google Cloud organization. To optimize for Google Cloud organization accounts generally instead of just one organization domain, set a value of an asterisk `*`.', 'wp-graphql-headless-login' ),
				'order'             => 10,
				'sanitize_callback' => 'sanitize_text_field',
			],
			'promptType'   => [
				'type'              => 'string',
				'label'             => __( 'Prompt Type', 'wp-graphql-headless-login' ),
				'description'       => __( 'Prompt Type', 'wp-graphql-headless-login' ),
				'help'              => __( 'The type of prompt displayed to the user when authenticating.', 'wp-graphql-headless-login' ),
				'enum'              => [ 'none', 'consent', 'select_account' ],
				'order'             => 11,
				'sanitize_callback' => 'sanitize_text_field',
			],
			'scope'        => [
				'type'              => 'array',
				'label'             => __( 'Scope', 'wp-graphql-headless-login' ),
				'description'       => __( 'Scope', 'wp-graphql-headless-login' ),
				'help'              => sprintf(
					// translators: %s is the URL to the Google scopes documentation.
					__( 'The scope to request from the provider. See %s for a list of available scopes.', 'wp-graphql-headless-login' ),
					'<a href="https://developers.google.com/identity/protocols/oauth2/scopes" target="_blank" rel="noopener noreferrer">Google scopes documentation</a>'
				),
				'order'             => 12,
				'advanced'          => true,
				'items'             => [ 'type' => 'string' ],
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_login_options_schema(): array {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function map_user_data( array $data ): array {
		// Implement mapping logic as needed.
		return $data;
	}
}
