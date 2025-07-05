<?php
/**
 * The OAuth2 Abstract class.
 *
 * Should be extended to add support for a new OAuth2 provider.
 *
 * @package WPGraphQL\Login\Auth\ProviderConfig\OAuth2
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderConfig\OAuth2;

use WPGraphQL\Login\Auth\ProviderConfig\ProviderConfig;
use WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\AbstractProvider;

/**
 * Class - ProviderConfig
 */
abstract class OAuth2Config extends ProviderConfig {
	/**
	 * The client options.
	 *
	 * @var ?array<string,mixed>
	 */
	protected ?array $client_options;

	/**
	 * The provider class.
	 *
	 * @var class-string<\WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\AbstractProvider>
	 */
	protected string $provider_class;

	/**
	 * The OAuth2 provider instance.
	 *
	 * @var \WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\AbstractProvider
	 */
	protected $provider;

	/**
	 * The authorization URL.
	 *
	 * @var string
	 */
	protected string $authorization_url;

	/**
	 * {@inheritDoc}
	 *
	 * @param string $provider_class The OAuth2 provider class.
	 *
	 * @throws \InvalidArgumentException If the provider class is not a subclass of AbstractProvider.
	 */
	public function __construct( string $provider_class ) {
		$this->client_options = $this->prepare_client_options();

		if ( ! is_a( $provider_class, AbstractProvider::class, true ) && ! is_a( $provider_class, 'League\OAuth2\Client\Provider\AbstractProvider', true ) ) { // Check for the prefixed and unprefixed class names.
			throw new \InvalidArgumentException(
				sprintf(
					// translators: the provider class name.
					esc_html__( 'The provider class must extend AbstractProvider. %s does not', 'wp-graphql-headless-login' ),
					esc_html( $provider_class )
				)
			);
		}

		/** @var class-string<\WPGraphQL\Login\Vendor\League\OAuth2\Client\Provider\AbstractProvider> $provider_class */
		$this->provider = new $provider_class( $this->client_options );

		$this->authorization_url = $this->prepare_authorization_url( $this->client_options );

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_type(): string {
		return 'oauth2';
	}

	/**
	 * Gets the configuration array for the provider from the saved client options.
	 *
	 * @param array<string,mixed> $settings The settings stored in the database, keyed to the expected client option property.
	 *
	 * @return array<string,mixed>
	 */
	abstract protected function get_options( array $settings ): array;

	/**
	 * Maps the provider's user data to WP_User arguments.
	 *
	 * @param array<string,mixed> $owner_details The Resource Owner details returned from the Authentication provider.
	 *
	 * @return array<string,mixed>
	 */
	abstract public function get_user_data( array $owner_details ): array;

	/**
	 * Gets the authorization URL for the OAuth2 provider.
	 */
	public function get_authorization_url(): string {
		return $this->authorization_url;
	}
}
