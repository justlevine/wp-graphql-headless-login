<?php
/**
 * The Headless Login Client model
 *
 * @package WPGraphQL\Login\Model
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Model;

use WPGraphQL\Model\Model;

/**
 * Class - Client
 *
 * @property ?string $authorizationUrl
 * @property array<string,mixed> $clientOptions
 * @property ?string $id
 * @property bool    $isEnabled
 * @property array<string,mixed> $loginOptions
 * @property ?string $name
 * @property ?int    $order
 * @property string  $provider
 *
 * @extends \WPGraphQL\Model\Model<\WPGraphQL\Login\Auth\Client>
 */
class Client extends Model {
	/**
	 * Client constructor.
	 *
	 * @param \WPGraphQL\Login\Auth\Client $client The incoming Client to be modeled.
	 *
	 * @return void
	 */
	public function __construct( \WPGraphQL\Login\Auth\Client $client ) {
		$this->data = $client;

		$allowed_restricted_field = [
			'authorizationUrl',
			'clientId',
			'isEnabled',
			'name',
			'order',
			'provider',
			'type',
		];

		parent::__construct( 'manage_options', $allowed_restricted_field );
	}

	/**
	 * Initialize the object
	 *
	 * @return void
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$provider      = $this->data->get_provider();
			$provider_type = $provider->get_provider_type();

			$slug = $this->data->get_provider_slug();

			$this->fields = [
				'authorizationUrl' => static fn () => method_exists( $provider_type, 'get_authorization_url' ) ? $provider_type->get_authorization_url( $provider ) : null,
				'clientOptions'    => static fn () => $provider->client_options + [ '__typename' => $slug ],
				'clientId'         => static fn () => $$provider->client_options['clientId'] ?? null,
				'isEnabled'        => static fn () => ! empty( $provider->is_enabled ),
				'loginOptions'     => static fn () => $provider->login_options + [ '__typename' => $slug ],
				'name'             => static fn () => $provider->name ?? null,
				'order'            => static fn () => $provider->order ?? null,
				'provider'         => static fn () => $slug,
			];
		}
	}
}
