<?php
/**
 * The Authentication Client.
 *
 * @package WPGraphQL\Login\Auth
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth;

use WPGraphQL\Login\Providers\Model;

/**
 * Class - Client
 */
class Client {
	/**
	 * The modeled Provider
	 *
	 * @var \WPGraphQL\Login\Providers\Model
	 */
	private Model $provider;

	/**
	 * The class constructor.
	 *
	 * @param \WPGraphQL\Login\Providers\Model $provider The modeled provider config.
	 */
	public function __construct( Model $provider ) {
		$this->provider = $provider;

		/**
		 * Fires after the Client is instantiated.
		 *
		 * @param string                           $slug            The slug of the provider config.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		do_action( 'graphql_login_client_init', $this->provider->slug, $this->provider, $this );
	}

	/**
	 * Gets the provider slug.
	 */
	public function get_provider_slug(): string {
		return $this->provider->slug;
	}

	/**
	 * Returns the provider config used to configure the client.
	 */
	public function get_provider(): Model {
		return $this->provider;
	}

	/**
	 * Uses the provider config to authenticate and return the user.
	 *
	 * @param array<string,mixed> $input The mutation input data.
	 *
	 * @return array<string,mixed>|\WP_Error
	 */
	public function authenticate_and_get_user_data( array $input ) {
		/**
		 * Fires before the user is authenticated.
		 *
		 * @param string                           $slug            The provider slug.
		 * @param array<string,mixed>              $input           The mutation input data.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		do_action( 'graphql_login_before_authenticate', $this->provider->slug, $input, $this->provider, $this );

		$user_data = $this->provider->authenticate( $input );

		/**
		 * Filters the user data returned from the Authentication provider.
		 *
		 * @param array<string,mixed>|\WP_Error    $user_data       The user data.
		 * @param string                           $slug            The provider slug.
		 * @param array<string,mixed>              $input           The mutation input data.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		$user_data = apply_filters( 'graphql_login_authenticated_user_data', $user_data, $this->provider->slug, $input, $this->provider, $this );

		/**
		 * Fires when the user is authenticated.
		 *
		 * @param array<string,mixed>|\WP_Error    $user_data       The user data.
		 * @param string                           $slug            The provider slug.
		 * @param array<string,mixed>              $input           The mutation input data.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		do_action( 'graphql_login_after_authenticate', $user_data, $this->provider->slug, $input, $this->provider, $this );

		return $user_data;
	}

	/**
	 * Uses the authenticated user data to return the user.
	 *
	 * @param array<string,mixed> $data the user data.
	 *
	 * @return \WP_User|false
	 */
	public function get_user_from_data( array $data ) {
		/**
		 * Shortcircuits the user matching logic, allowing you to provide your own logic for matching the user from the provider user data.
		 * If null is returned, the default matching logic will be used.
		 *
		 * @param \WP_User|false|null              $pre_get_user    The user matched from the data. If null, the default matching logic will be used.
		 * @param array<string,mixed>              $data            The user data from the provider.
		 * @param string                           $slug            The provider slug.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		$user = apply_filters( 'graphql_login_pre_get_user_from_data', null, $data, $this->provider->slug, $this->provider, $this );

		if ( null === $user ) {
			$user = $this->provider->get_provider_type()->get_user_from_data( $data ) ?: false;
		}

		/**
		 * Fires when the user is matched from the data.
		 * Useful for updating custom meta fields from the provider.
		 *
		 * @param \WP_User|false                   $user            The user matched from the data.
		 * @param array<string,mixed>|\WP_User     $user_data       The user data from the provider.
		 * @param string                           $slug            The provider slug.
		 * @param \WPGraphQL\Login\Providers\Model $provider        The Provider used to configure the client.
		 * @param \WPGraphQL\Login\Auth\Client     $client          The Client instance.
		 */
		do_action( 'graphql_login_get_user_from_data', $user, $data, $this->provider->slug, $this->provider, $this );

		return $user;
	}

	/**
	 * Maybe creates a user from the provided user data.
	 *
	 * @param array<string,mixed>|mixed $user_data The user data.
	 *
	 * @return \WP_User|\WP_Error|false
	 */
	public function maybe_create_user( $user_data ) {
		/**
		 * Deprecated filter. Use `graphql_login_create_user_data` instead.
		 *
		 * @param array $user_data       The WordPress user data.
		 * @param self  $provider_config An instance of the provider configuration.
		 *
		 * @since 0.0.1
		 * @deprecated 0.1.4
		 */
		$user_data = apply_filters_deprecated(
			'graphql_login_mapped_user_data',
			[ $user_data, $this ],
			'0.1.4',
			'graphql_login_create_user_data'
		);

		/**
		 * Filters the user data mapped from the Authentication provider before creating the user.
		 * Useful for mapping custom fields from the Authentication provider to the WP_User.
		 *
		 * @param array $user_data       The WordPress user data.
		 * @param self  $provider_config An instance of the provider configuration.
		 *
		 * @since 0.1.4
		 */
		$user_data = apply_filters( 'graphql_login_create_user_data', $user_data, $this );

		if ( ! is_array( $user_data ) ) {
			return false;
		}

		return User::maybe_create_user( $this, $user_data );
	}
}
