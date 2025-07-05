<?php
/**
 * Site Token provider type.
 *
 * @package WPGraphQL\Login\Auth\ProviderType
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType;

use GraphQL\Error\UserError;
use WPGraphQL\Login\Auth\User;

class SiteToken extends AbstractProviderType {
	/**
	 * {@inheritDoc}
	 */
	public static function get_type(): string {
		return 'siteToken';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return __( 'Site Token', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'siteToken';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return [
			'headerKey' => [
				'type'        => 'string',
				'label'       => __( 'Header Key', 'wp-graphql-headless-login' ),
				'description' => __( 'The custom header that will be used to store the site access token.', 'wp-graphql-headless-login' ),
				'help'        => __( 'The custom header that will be used to store the site access token. The header should only be set on a SERVER-SIDE request. E.g. `X-My-Site-Token`', 'wp-graphql-headless-login' ),
				'order'       => 1,
			],
			'secretKey' => [
				'type'        => 'string',
				'label'       => __( 'Site Secret', 'wp-graphql-headless-login' ),
				'description' => __( 'The secret used to authenticate the site token.', 'wp-graphql-headless-login' ),
				'help'        => __( 'The secret used to authenticate the site token. This should be the same as the value you set on your custom Header key.  The secret should only be set on a SERVER-SIDE request.', 'wp-graphql-headless-login' ),
				'order'       => 2,
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_login_options_schema(): array {
		return [
			'metaKey' => [
				'type'        => 'string',
				'description' => __( 'The User meta key to check for the identity', 'wp-graphql-headless-login' ),
				'default'     => 'email',
				'help'        => __( 'The WP_User key to check for the identity. Accepts `id`, `slug`, `email`, `login`, or a custom meta field.', 'wp-graphql-headless-login' ),
				'order'       => 1,
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function authenticate( array $input ) {
		$args = $this->prepare_mutation_input( $input );

		$client_options = $this->get_client_options();
		$header_key     = ! empty( $client_options['headerKey'] ) ? strtoupper( str_replace( '-', '_', $client_options['headerKey'] ) ) : '';

		if ( empty( $header_key ) ) {
			return new \WP_Error(
				'graphql-headless-login-missing-header-key',
				__( 'Header key for site token authentication is not defined.', 'wp-graphql-headless-login' )
			);
		}

		$secret = isset( $_SERVER[ 'HTTP_' . $header_key ] ) ? sanitize_text_field( $_SERVER[ 'HTTP_' . $header_key ] ) : '';

		if ( empty( $secret ) ) {
			return new \WP_Error(
				'graphql-headless-login-missing-header-token',
				__( 'Missing site token in custom header.', 'wp-graphql-headless-login' )
			);
		}

		if ( ! isset( $client_options['secretKey'] ) || $secret !== $client_options['secretKey'] ) {
			return new \WP_Error(
				'graphql-headless-login-invalid-header-token',
				__( 'Invalid site token.', 'wp-graphql-headless-login' )
			);
		}

		return $args;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_user_from_data( array $data ) {
		if ( empty( $data['subject_identity'] ) ) {
			return null;
		}

		$user = User::get_user_by_identity( static::get_slug(), $data['subject_identity'] );
		if ( $user instanceof \WP_User ) {
			return $user;
		}

		$client_options = $this->get_login_options();
		$meta_key       = $client_options['metaKey'] ?? 'user_email';
		$user           = User::get_user_by( $meta_key, $data['subject_identity'] );

		if ( $user instanceof \WP_User ) {
			User::link_user_identity( $user->ID, static::get_slug(), $data['subject_identity'] );
		}

		return $user;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array{subject_identity: ?string}
	 *
	 * @throws \GraphQL\Error\UserError
	 */
	protected function prepare_mutation_input( array $input ): array {
		if ( ! isset( $input['identity'] ) ) {
			throw new UserError(
				esc_html__( 'The SITE_TOKEN provider requires the use of the `identity` input arg.', 'wp-graphql-headless-login' )
			);
		}

		return [
			'subject_identity' => ! empty( $input['identity'] ) ? sanitize_text_field( $input['identity'] ) : null,
		];
	}
}
