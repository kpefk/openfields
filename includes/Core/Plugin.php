<?php
/**
 * Core plugin bootstrap.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Core;

use OpenFields\Admin\FieldGroupEditScreen;
use OpenFields\FieldGroups\LocationCache;
use OpenFields\FieldTypes\FieldTypeRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin orchestrator.
 *
 * Owns the service container, registers core subsystems, and hooks them into
 * WordPress on {@see Plugin::boot()}.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Service container.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Whether boot() has already run.
	 *
	 * @var bool
	 */
	private bool $booted = false;

	/**
	 * Build the plugin and register its services.
	 */
	private function __construct() {
		$this->container = new Container();
		$this->register_services();
	}

	/**
	 * Retrieve the shared instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Access the service container.
	 *
	 * @return Container
	 */
	public function container(): Container {
		return $this->container;
	}

	/**
	 * Register core services in the container.
	 *
	 * @return void
	 */
	private function register_services(): void {
		$this->container->singleton(
			Security::class,
			static fn (): Security => new Security()
		);

		$this->container->singleton(
			PostType::class,
			static fn (): PostType => new PostType()
		);

		$this->container->singleton(
			MetaRegistrar::class,
			static fn (): MetaRegistrar => new MetaRegistrar()
		);

		$this->container->singleton(
			Assets::class,
			static fn (): Assets => new Assets()
		);

		$this->container->singleton(
			LocationCache::class,
			static fn (): LocationCache => new LocationCache()
		);

		$this->container->singleton(
			FieldTypeRegistry::class,
			static fn (): FieldTypeRegistry => new FieldTypeRegistry()
		);

		$this->container->singleton(
			FieldGroupEditScreen::class,
			static function ( Container $c ): FieldGroupEditScreen {
				$security = $c->get( Security::class );

				return new FieldGroupEditScreen(
					$security instanceof Security ? $security : new Security()
				);
			}
		);
	}

	/**
	 * Boot the plugin. Idempotent.
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		$post_type      = $this->container->get( PostType::class );
		$meta           = $this->container->get( MetaRegistrar::class );
		$assets         = $this->container->get( Assets::class );
		$location_cache = $this->container->get( LocationCache::class );
		$field_types    = $this->container->get( FieldTypeRegistry::class );

		add_action( 'init', array( $field_types, 'register_defaults' ), 1 );
		add_action( 'init', array( $post_type, 'register_status' ), 1 );
		add_action( 'init', array( $post_type, 'register' ) );
		add_action( 'init', array( $meta, 'register' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue' ) );

		// Invalidate cached location matches whenever a field group changes.
		add_action( 'save_post_' . PostType::POST_TYPE, array( $location_cache, 'invalidate' ) );
		add_action( 'deleted_post', array( $location_cache, 'invalidate' ) );

		if ( is_admin() ) {
			$edit_screen = $this->container->get( FieldGroupEditScreen::class );
			if ( $edit_screen instanceof FieldGroupEditScreen ) {
				$edit_screen->register();
			}
		}

		/**
		 * Fires after OpenFields has booted.
		 *
		 * @since 0.1.0
		 *
		 * @param Container $container The plugin service container.
		 */
		do_action( 'openfields/booted', $this->container );
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'openfields',
			false,
			dirname( plugin_basename( OPENFIELDS_FILE ) ) . '/languages'
		);
	}
}
