<?php
/**
 * The Rest Controller for Provider instances.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.1.0
 */

declare(strict_types=1);

namespace WPGraphQL\Login\Providers;

/**
 * Class RestController
 */
class RestController extends \WP_REST_Controller {
	/**
	 * The namespace for the REST API endpoints.
	 */
	public const NAMESPACE = 'wp-graphql-login/v1';

	/**
	 * The REST API base.
	 */
	public const REST_BASE = 'providers';

	/**
	 * Repository instance.
	 *
	 * @var \WPGraphQL\Login\Providers\Repository
	 */
	protected Repository $repository;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->repository = new Repository();
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes(): void {
		// Get all providers.
		register_rest_route(
			self::NAMESPACE,
			'/' . self::REST_BASE,
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			]
		);

		// Create provider.
		register_rest_route(
			self::NAMESPACE,
			'/' . self::REST_BASE,
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'create_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
			]
		);

		// Get single provider.
		register_rest_route(
			self::NAMESPACE,
			'/' . self::REST_BASE . '/(?P<id>[\d]+)',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => [
					'id' => [
						'description' => __( 'Unique identifier for the provider.', 'wp-graphql-headless-login' ),
						'type'        => 'integer',
						'required'    => true,
					],
				],
			]
		);

		// Update provider.
		register_rest_route(
			self::NAMESPACE,
			'/' . self::REST_BASE . '/(?P<id>[\d]+)',
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
			]
		);

		// Delete provider.
		register_rest_route(
			self::NAMESPACE,
			'/' . self::REST_BASE . '/(?P<id>[\d]+)',
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'args'                => [
					'id' => [
						'description' => __( 'Unique identifier for the provider.', 'wp-graphql-headless-login' ),
						'type'        => 'integer',
						'required'    => true,
					],
				],
			]
		);
	}

	/**
	 * Check if current user can list providers.
	 *
	 * @param \WP_REST_Request<array{type?:string,enabled?:bool}> $request The request object.
	 */
	public function get_items_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get a collection of providers.
	 *
	 * @param \WP_REST_Request<array{type?:string,enabled?:bool}> $request The request object.
	 */
	public function get_items( $request ): \WP_REST_Response {
		$type    = $request['type'] ?? null;
		$enabled = $request['enabled'] ?? null;

		if ( null !== $type ) {
			$items = $this->repository->find_by_type( $type, $enabled ?? false );
		} else {
			$items = $this->repository->find_all( $enabled ?? false );
		}

		return new \WP_REST_Response(
			array_map( static fn ( $item ) => $item->to_array(), $items ),
			200
		);
	}

	/**
	 * Check if current user can create providers.
	 *
	 * @param \WP_REST_Request<array{type:string,name:string,slug:string,is_enabled:bool,client_options:array<string,mixed>,login_options:array<string,mixed>}> $request The request object.
	 */
	public function create_item_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Create a provider.
	 *
	 * @param \WP_REST_Request<array{type:string,name:string,slug:string,is_enabled:bool,client_options:array<string,mixed>,login_options:array<string,mixed>}> $request The request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_item( $request ) {
		try {
			$provider = new Model( $request->get_params() );

			$result = $provider->save();

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new \WP_REST_Response( $provider->to_array(), 201 );
		} catch ( \InvalidArgumentException $e ) {
			return new \WP_Error(
				'rest_provider_invalid_data',
				$e->getMessage(),
				[ 'status' => 400 ]
			);
		}
	}

	/**
	 * Check if current user can read provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 */
	public function get_item_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get a provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		$id = absint( $request['id'] );

		if ( empty( $id ) ) {
			return new \WP_Error(
				'rest_provider_invalid_id',
				__( 'Invalid provider ID.', 'wp-graphql-headless-login' ),
				[ 'status' => 400 ]
			);
		}

		$provider = $this->repository->find_by_id( $id );

		if ( null === $provider ) {
			return new \WP_Error(
				'rest_provider_not_found',
				__( 'Provider not found.', 'wp-graphql-headless-login' ),
				[ 'status' => 404 ]
			);
		}

		return new \WP_REST_Response( $provider->to_array(), 200 );
	}

	/**
	 * Check if current user can update provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 */
	public function update_item_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Update a provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$id = absint( $request['id'] );

		if ( empty( $id ) ) {
			return new \WP_Error(
				'rest_provider_invalid_id',
				__( 'Invalid provider ID.', 'wp-graphql-headless-login' ),
				[ 'status' => 400 ]
			);
		}

		$provider = $this->repository->find_by_id( $id );

		if ( null === $provider ) {
			return new \WP_Error(
				'rest_provider_not_found',
				__( 'Provider not found.', 'wp-graphql-headless-login' ),
				[ 'status' => 404 ]
			);
		}

		try {
			foreach ( $request->get_params() as $key => $value ) {
				if ( 'id' === $key ) {
					continue;
				}

				$provider->$key = $value;
			}

			$result = $provider->save();

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new \WP_REST_Response( $provider->to_array(), 200 );
		} catch ( \InvalidArgumentException $e ) {
			return new \WP_Error(
				'rest_provider_invalid_data',
				$e->getMessage(),
				[ 'status' => 400 ]
			);
		}
	}

	/**
	 * Check if current user can delete provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 */
	public function delete_item_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Delete a provider.
	 *
	 * @param \WP_REST_Request<array{id:int}> $request The request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_item( $request ) {
		$id = absint( $request['id'] );

		if ( empty( $id ) ) {
			return new \WP_Error(
				'rest_provider_invalid_id',
				__( 'Invalid provider ID.', 'wp-graphql-headless-login' ),
				[ 'status' => 400 ]
			);
		}

		$provider = $this->repository->find_by_id( $id );

		if ( null === $provider ) {
			return new \WP_Error(
				'rest_provider_not_found',
				__( 'Provider not found.', 'wp-graphql-headless-login' ),
				[ 'status' => 404 ]
			);
		}

		$result = $this->repository->delete( $provider );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( null, 204 );
	}

	/**
	 * Get the collection parameters.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_collection_params(): array {
		return [
			'type'    => [
				'description' => __( 'Filter providers by type.', 'wp-graphql-headless-login' ),
				'type'        => 'string',
				'required'    => false,
			],
			'enabled' => [
				'description' => __( 'Filter providers by enabled status.', 'wp-graphql-headless-login' ),
				'type'        => 'boolean',
				'required'    => false,
			],
		];
	}

	/**
	 * Get the item schema.
	 *
	 * @return array<string,mixed>
	 */
	public function get_item_schema(): array {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'provider',
			'type'       => 'object',
			'properties' => [
				'id'             => [
					'description' => __( 'Unique identifier for the provider.', 'wp-graphql-headless-login' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'type'           => [
					'description' => __( 'The provider type.', 'wp-graphql-headless-login' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => true,
					'arg_options' => [
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
				'name'           => [
					'description' => __( 'The provider name.', 'wp-graphql-headless-login' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => true,
					'arg_options' => [
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
				'slug'           => [
					'description' => __( 'An alphanumeric identifier for the provider.', 'wp-graphql-headless-login' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => true,
					'arg_options' => [
						'sanitize_callback' => 'sanitize_key',
					],
				],
				'is_enabled'     => [
					'description' => __( 'Whether the provider is enabled.', 'wp-graphql-headless-login' ),
					'type'        => 'boolean',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => false,
					'default'     => false,
				],
				'order'          => [
					'description' => __( 'The order of the provider.', 'wp-graphql-headless-login' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => false,
					'default'     => 0,
				],
				'client_options' => [
					'description' => __( 'The provider client options.', 'wp-graphql-headless-login' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => false,
				],
				'login_options'  => [
					'description' => __( 'The provider login options.', 'wp-graphql-headless-login' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'required'    => false,
				],
				'created_at'     => [
					'description' => __( 'The date the provider was created.', 'wp-graphql-headless-login' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'updated_at'     => [
					'description' => __( 'The date the provider was last updated.', 'wp-graphql-headless-login' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
			],
		];
	}
}
