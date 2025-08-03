<?php
/**
 * Instagram OAuth2 Provider Type
 *
 * @package WPGraphQL\Login\Providers\ProviderType\OAuth2
 * @since 0.0.1
 */

namespace WPGraphQL\Login\Providers\ProviderType\OAuth2;

/**
 * Class - Instagram
 */
class Instagram extends AbstractOAuth2Type {
	/**
	 * Instagram constructor.
	 */
	public function __construct() {
		parent::__construct( \WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\Instagram::class );
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
		return __( 'Instagram', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'instagram';
	}

	/**
	 * {@inheritDoc}
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
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return [
			'scope' => [
				'type'              => 'array',
				'label'             => __( 'Scope', 'wp-graphql-headless-login' ),
				'description'       => __( 'Scope', 'wp-graphql-headless-login' ),
				// translators: %s is the URL to the Instagram scopes documentation.
				'help'              => sprintf(
					// translators: %s is the URL to the Instagram scopes documentation.
					__( 'The scope to request from the provider. See %s for a list of available scopes.', 'wp-graphql-headless-login' ),
					'<a href="https://developers.facebook.com/docs/instagram-basic-display-api/overview#permissions" target="_blank" rel="noopener noreferrer">Instagram scopes documentation</a>'
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
