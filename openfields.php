<?php
/**
 * Plugin Name:       OpenFields
 * Plugin URI:        https://github.com/kpefk/openfields
 * Description:       Open Source custom fields, field groups and content builders for WordPress — a GPL-compatible alternative to ACF PRO.
 * Version:           0.1.0-alpha
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            KPEFK
 * Author URI:        https://github.com/kpefk
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       openfields
 * Domain Path:       /languages
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields;

defined( 'ABSPATH' ) || exit;

define( 'OPENFIELDS_VERSION', '0.1.0-alpha' );
define( 'OPENFIELDS_FILE', __FILE__ );
define( 'OPENFIELDS_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPENFIELDS_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENFIELDS_MIN_PHP', '8.1' );

/**
 * Load the Composer autoloader if it is available.
 *
 * The plugin ships without a committed `vendor/` directory; `composer install`
 * generates it. Guarding the require keeps activation from fataling on a fresh
 * checkout that has not run Composer yet.
 *
 * @return bool True when the autoloader was loaded.
 */
function load_autoloader(): bool {
	$autoloader = OPENFIELDS_PATH . 'vendor/autoload.php';

	if ( is_readable( $autoloader ) ) {
		require_once $autoloader;

		return true;
	}

	return false;
}

/**
 * Render the admin notice shown when a conflicting ACF installation is active.
 *
 * @return void
 */
function render_acf_conflict_notice(): void {
	$message = __( 'OpenFields is inactive because Advanced Custom Fields (ACF) is active. The two plugins provide the same functions and cannot run at the same time. Deactivate one of them.', 'openfields' );

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html( $message )
	);
}

/**
 * Boot the plugin once all plugins are loaded.
 *
 * Detects ACF specifically (not a stray `get_field()` from another plugin) and
 * bails safely with an admin notice on conflict. Otherwise loads the autoloader
 * and hands control to the core container (added in a later milestone).
 *
 * @return void
 */
function bootstrap(): void {
	if ( defined( 'ACF_VERSION' ) || class_exists( 'ACF', false ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\render_acf_conflict_notice' );

		return;
	}

	if ( ! load_autoloader() ) {
		return;
	}

	// Public API functions. Loaded here (not via Composer's files autoload) so
	// their ABSPATH guard is only evaluated inside a WordPress request.
	require_once OPENFIELDS_PATH . 'includes/Api/functions.php';

	if ( class_exists( Core\Plugin::class ) ) {
		Core\Plugin::instance()->boot();
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap', 5 );

/**
 * Run activation tasks.
 *
 * Activation may fire before {@see bootstrap()}, so load the autoloader here.
 *
 * @return void
 */
function activate(): void {
	if ( load_autoloader() && class_exists( Core\Activation::class ) ) {
		Core\Activation::activate();
	}
}

/**
 * Run deactivation tasks.
 *
 * @return void
 */
function deactivate(): void {
	if ( load_autoloader() && class_exists( Core\Activation::class ) ) {
		Core\Activation::deactivate();
	}
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );
