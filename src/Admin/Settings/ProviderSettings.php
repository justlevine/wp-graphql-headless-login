<?php
/**
 * Registers the Providers Settings
 *
 * @package WPGraphQL\Login\Admin\Settings
 * @since 0.0.6
 */

declare( strict_types = 1 );

namespace WPGraphQL\Login\Admin\Settings;

use WPGraphQL\Login\Providers\ProviderRegistry;

/**
 * Class ProviderSettings
 *
 * @phpstan-import-type RenderFieldSetting from \WPGraphQL\Login\Admin\Settings\AbstractSettings
 */
class ProviderSettings {
	/**
	 * The screen slug.
	 */
	public static function get_slug(): string {
		return 'providers';
	}

	/**
	 * The screen title.
	 */
	public function get_title(): string {
		return __( 'Login Providers', 'wp-graphql-headless-login' );
	}

	/**
	 * The label used in the menu
	 */
	public function get_label(): string {
		return __( 'Providers', 'wp-graphql-headless-login' );
	}

	/**
	 * The screen description.
	 */
	public function get_description(): string {
		return __( 'Create and manage the provider configurations available to authenticate users.', 'wp-graphql-headless-login' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array<string,RenderFieldSetting>
	 */
	public function get_config(): array {
		return [
			'name'      => [
				'description' => __( 'The provider name.', 'wp-graphql-headless-login' ),
				'label'       => __( 'Name', 'wp-graphql-headless-login' ),
				'type'        => 'string',
				'help'        => __( 'This is the label that will be displayed to the user.', 'wp-graphql-headless-login' ),
				'isAdvanced'  => false,
				'order'       => 0,
				'required'    => true,
			],
			'type'      => [
				'description' => __( 'The provider type.', 'wp-graphql-headless-login' ),
				'label'       => __( 'Provider Type', 'wp-graphql-headless-login' ),
				'help'        => __( 'The provider or service ', 'wp-graphql-headless-login' ),
				'type'        => 'string',
				'controlType' => 'select',
				'isAdvanced'  => false,
				'order'       => 1,
				'required'    => true,
				'hidden'      => false,
				'enum'        => array_map(
					static fn ( $class_name ) => $class_name::get_name(),
					ProviderRegistry::get_instance()->get_provider_types(),
				) ?: [ 'Github' ],
			],
			'slug'      => [
				'description' => __( 'The provider slug.', 'wp-graphql-headless-login' ),
				'label'       => __( 'Provider Slug', 'wp-graphql-headless-login' ),
				'type'        => 'string',
				'help'        => __( 'This is the slug that will be used to identify the provider.', 'wp-graphql-headless-login' ),
				'isAdvanced'  => false,
				'hidden'      => true,
				'required'    => true,
			],
			'isEnabled' => [
				'description' => __( 'Whether the provider is enabled or not.', 'wp-graphql-headless-login' ),
				'label'       => __( 'Enable Provider', 'wp-graphql-headless-login' ),
				'type'        => 'boolean',
				'required'    => true,
				'hidden'      => false,
				'order'       => 2,
				'default'     => true,
			],
			'order'     => [
				'description' => __( 'The order in which the provider should disappear.', 'wp-graphql-headless-login' ),
				'label'       => __( 'Order', 'wp-graphql-headless-login' ),
				'type'        => 'integer',
				'default'     => 0,
				'help'        => __( 'This is the order in which the provider will be displayed to the user.', 'wp-graphql-headless-login' ),
				'hidden'      => false,
				'isAdvanced'  => false,
				'required'    => true,
			],
		];
	}

	/**
	 * @return array{
	 *  title: string,
	 *  label: string,
	 *  description: string,
	 *  fields: array<string,RenderFieldSetting>
	 * }
	 */
	public function get_render_config(): array {
		return [
			'title'              => $this->get_title(),
			'label'              => $this->get_label(),
			'description'        => $this->get_description(),
			'fields'             => $this->get_config(),
			'providerTypeFields' => $this->get_provider_types_config(),
		];
	}

	/**
	 * @return array<string,array<string,RenderFieldSetting>>
	 */
	protected function get_provider_types_config() {
		$provider_types = ProviderRegistry::get_instance()->get_provider_types();

		$config = [];

		foreach ( $provider_types as $type ) {
			$config[ $type::get_slug() ] = [
				'clientOptions' => [
					'description' => __( 'The client options for the provider.', 'wp-graphql-headless-login' ),
					'label'       => __( 'Client Options', 'wp-graphql-headless-login' ),
					'type'        => 'object',
					'properties'  => $type::get_client_options_schema(),
				],
				'loginOptions'  => [
					'description' => __( 'The login options for the provider.', 'wp-graphql-headless-login' ),
					'label'       => __( 'Login Options', 'wp-graphql-headless-login' ),
					'type'        => 'object',
					'properties'  => $type::get_login_options_schema(),
				],
			];
		}

		return $config;
	}
}
