<?php
/**
 * A password provider type.
 *
 * @package WPGraphQL\Login\Auth\ProviderType
 * @since 0.0.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Auth\ProviderType;

use GraphQL\Error\UserError;

class Password extends AbstractProviderType {
	/**
	 * {@inheritDoc}
	 */
	public static function get_type(): string {
		return 'password';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return __( 'Password', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'password';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_client_options_schema(): array {
		return [];
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
	public function authenticate( array $input ) {
		// Get the args from the input.
		$args = $this->prepare_mutation_input( $input );

		if ( empty( $args['username'] ) || empty( $args['password'] ) ) {
			return new \WP_Error(
				'graphql-headless-login-missing-credentials',
				__( 'Missing username or password.', 'wp-graphql-headless-login' )
			);
		}

		$user = wp_authenticate( $args['username'], $args['password'] );

		// Obsfucate any authentication errors.
		if ( $user instanceof \WP_Error ) {
			graphql_debug( wp_strip_all_tags( $user->get_error_message() ) );

			return new \WP_Error(
				'graphql-headless-authentication-failed',
				__( 'Failed to authenticate.', 'wp-graphql-headless-login' )
			);
		}

		return $user->to_array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_user_from_data( array $data ) {
		$user = ! empty( $data['ID'] ) ? get_user_by( 'id', (int) $data['ID'] ) : false;

		return $user instanceof \WP_User ? $user : null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array{username: ?string, password: ?string}
	 *
	 * @throws \GraphQL\Error\UserError
	 */
	protected function prepare_mutation_input( array $input ): array {
		if ( ! isset( $input['credentials'] ) ) {
			throw new UserError(
				esc_html__( 'The PASSWORD provider requires the use of the `credentials` input arg.', 'wp-graphql-headless-login' )
			);
		}

		return [
			'username' => ! empty( $input['credentials']['username'] ) ? sanitize_text_field( $input['credentials']['username'] ) : null,
			'password' => ! empty( $input['credentials']['password'] ) ? trim( $input['credentials']['password'] ) : null,
		];
	}
}
