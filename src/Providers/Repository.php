<?php
/**
 * Repository for provider instances.
 *
 * @package WPGraphQL\Login\Providers
 * @since 0.1.0
 */

declare(strict_types=1);

namespace WPGraphQL\Login\Providers;

/**
 * Class - Repository
 */
class Repository {
	/**
	 * The class to use to model the data.
	 *
	 * @var class-string<\WPGraphQL\Login\Providers\Model>
	 */
	protected string $model_class;

	/**
	 * The class holding the database schema.
	 *
	 * @var class-string<\WPGraphQL\Login\Providers\Schema>
	 */
	protected string $schema_class;

	/**
	 * Initialize the repository.
	 *
	 * @param string $model_class The class to use to model the data.
	 * @param string $schema_class The class holding the database schema.
	 *
	 * @throws \InvalidArgumentException If the model class is not a subclass of Model or the schema class is not a subclass of Schema.
	 */
	public function __construct( string $model_class = Model::class, string $schema_class = Schema::class ) {
		if ( ! is_a( $model_class, Model::class, true ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %1$s is the model class name, %2$s is the expected class name.
					esc_html__( 'Model %1$s must be a subclass of %2$s.', 'wp-graphql-headless-login' ),
					esc_html( $model_class ),
					esc_html( Model::class ),
				)
			);
		}

		if ( ! is_a( $schema_class, Schema::class, true ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					// translators: %1$s is the schema class name, %2$s is the expected class name.
					esc_html__( 'Schema %1$s must be a subclass of %2$s.', 'wp-graphql-headless-login' ),
					esc_html( $schema_class ),
					esc_html( Schema::class ),
				)
			);
		}

		$this->model_class  = $model_class;
		$this->schema_class = $schema_class;
	}

	/**
	 * Find a provider instance by ID.
	 *
	 * @param int $id Provider instance ID.
	 */
	public function find_by_id( int $id ): ?Model {
		global $wpdb;
		$table_name = Schema::get_table_name();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM `{$table_name}` WHERE id = %d",
				$id
			),
			ARRAY_A
		);

		return $row ? new Model( $row ) : null;
	}

	/**
	 * Find a provider instance by slug.
	 *
	 * @param string $slug Provider instance slug.
	 */
	public function find_by_slug( string $slug ): ?Model {
		global $wpdb;
		$table_name = Schema::get_table_name();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM `{$table_name}` WHERE slug = %s",
				$slug
			),
			ARRAY_A
		);

		return $row ? new Model( $row ) : null;
	}

	/**
	 * Find provider instances by type.
	 *
	 * @param string $type Provider type slug.
	 * @param bool   $enabled_only Whether to only return enabled instances.
	 *
	 * @return array<int,\WPGraphQL\Login\Providers\Model>
	 */
	public function find_by_type( string $type, bool $enabled_only = false ): array {
		// Validate provider type exists.
		if ( ! ProviderRegistry::get_instance()->get_provider_type( $type ) ) {
			return [];
		}

		global $wpdb;
		$table_name = Schema::get_table_name();

		$where_clause = $wpdb->prepare( 'type = %s', $type );

		if ( $enabled_only ) {
			$where_clause .= $wpdb->prepare( ' AND is_enabled = %d', 1 );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				'SELECT * FROM %s WHERE %s ORDER BY order ASC',
				$table_name,
				$where_clause
			),
			ARRAY_A
		);

		return array_map( static fn ( $row ) => new Model( $row ), $rows ?: [] );
	}

	/**
	 * Find all provider instances.
	 *
	 * @param bool $enabled_only Whether to only return enabled instances.
	 *
	 * @return array<int,\WPGraphQL\Login\Providers\Model>
	 */
	public function find_all( bool $enabled_only = false ): array {
		global $wpdb;
		$table_name = Schema::get_table_name();

		if ( $enabled_only ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM `{$table_name}` WHERE is_enabled = %d ORDER BY `order` ASC",
					1
				),
				ARRAY_A
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$rows = $wpdb->get_results(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM `{$table_name}` ORDER BY `order` ASC",
				ARRAY_A
			);
		}

		return array_map( static fn ( $row ) => new Model( $row ), $rows ?: [] );
	}

	/**
	 * Delete a provider instance.
	 *
	 * @param \WPGraphQL\Login\Providers\Model $model The provider instance to delete.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function delete( Model $model ) {
		return $model->delete();
	}

	/**
	 * Delete multiple provider instances by IDs.
	 *
	 * @param array<int> $ids Array of provider IDs to delete.
	 *
	 * @return int Number of providers deleted.
	 */
	public function delete_by_ids( array $ids ): int {
		if ( empty( $ids ) ) {
			return 0;
		}

		// Sanitize IDs.
		$ids = array_map( 'absint', $ids );
		$ids = array_filter( $ids );

		if ( empty( $ids ) ) {
			return 0;
		}

		global $wpdb;
		$table_name = Schema::get_table_name();

		$placeholders = $wpdb->prepare(
			implode( ',', array_fill( 0, count( $ids ), '%d' ) ),
			$ids
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$deleted = $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM %s WHERE id IN (%s)',
				$table_name,
				$placeholders,
			)
		);

		return (int) $deleted;
	}

	/**
	 * Update the order of provider instances.
	 *
	 * @param array<int,int> $order_map Map of provider ID to order position.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update_order( array $order_map ): bool {
		if ( empty( $order_map ) ) {
			return true;
		}

		global $wpdb;
		$table_name = Schema::get_table_name();
		$success    = true;

		foreach ( $order_map as $id => $order ) {
			$id    = absint( $id );
			$order = absint( $order );

			if ( 0 === $id ) {
				continue;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$result = $wpdb->update(
				$table_name,
				[ 'order' => $order ],
				[ 'id' => $id ],
				[ '%d' ],
				[ '%d' ]
			);

			if ( false === $result ) {
				$success = false;
			}
		}

		return $success;
	}
}
