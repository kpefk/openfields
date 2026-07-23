<?php
/**
 * Import/Export admin screen.
 *
 * @package OpenFields
 */

declare( strict_types=1 );

namespace OpenFields\Admin;

use OpenFields\Core\PostType;
use OpenFields\Core\Security;
use OpenFields\ImportExport\Exporter;
use OpenFields\ImportExport\Importer;

defined( 'ABSPATH' ) || exit;

/**
 * Adds an "Import &amp; Export" page under Field Groups: it shows the current
 * groups as JSON and generated PHP for copying, and accepts pasted JSON to
 * import.
 */
final class ImportExportScreen {

	private const PAGE_SLUG     = 'openfields-import-export';
	private const IMPORT_ACTION = 'openfields_import';
	private const NONCE_ACTION  = 'openfields_import';

	/**
	 * Exporter.
	 *
	 * @var Exporter
	 */
	private Exporter $exporter;

	/**
	 * Importer.
	 *
	 * @var Importer
	 */
	private Importer $importer;

	/**
	 * Security helper.
	 *
	 * @var Security
	 */
	private Security $security;

	/**
	 * Build the screen with its collaborators.
	 *
	 * @param Exporter $exporter Exporter.
	 * @param Importer $importer Importer.
	 * @param Security $security Security helper.
	 */
	public function __construct( Exporter $exporter, Importer $importer, Security $security ) {
		$this->exporter = $exporter;
		$this->importer = $importer;
		$this->security = $security;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_' . self::IMPORT_ACTION, array( $this, 'handle_import' ) );
	}

	/**
	 * Add the submenu page under Field Groups.
	 *
	 * @return void
	 */
	public function add_page(): void {
		add_submenu_page(
			'edit.php?post_type=' . PostType::POST_TYPE,
			__( 'Import & Export', 'openfields' ),
			__( 'Import & Export', 'openfields' ),
			Security::CAP_MANAGE_FIELD_GROUPS,
			self::PAGE_SLUG,
			array( $this, 'render' )
		);
	}

	/**
	 * Render the page.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->security->can_manage_field_groups() ) {
			return;
		}

		$imported = isset( $_GET['openfields_imported'] ) ? absint( $_GET['openfields_imported'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display of a redirect count.

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Import & Export', 'openfields' ) . '</h1>';

		if ( $imported > 0 ) {
			printf(
				'<div class="notice notice-success"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: %d: number of imported field groups. */
						_n( 'Imported %d field group.', 'Imported %d field groups.', $imported, 'openfields' ),
						$imported
					)
				)
			);
		}

		echo '<h2>' . esc_html__( 'Export (JSON)', 'openfields' ) . '</h2>';
		printf(
			'<textarea class="large-text code" rows="8" readonly>%s</textarea>',
			esc_textarea( $this->exporter->to_json() )
		);

		echo '<h2>' . esc_html__( 'Export (PHP)', 'openfields' ) . '</h2>';
		printf(
			'<textarea class="large-text code" rows="8" readonly>%s</textarea>',
			esc_textarea( $this->exporter->to_php() )
		);

		echo '<h2>' . esc_html__( 'Import (JSON)', 'openfields' ) . '</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		printf( '<input type="hidden" name="action" value="%s" />', esc_attr( self::IMPORT_ACTION ) );
		wp_nonce_field( self::NONCE_ACTION );
		echo '<textarea class="large-text code" rows="8" name="openfields_import_json"></textarea>';
		submit_button( __( 'Import', 'openfields' ) );
		echo '</form>';

		echo '</div>';
	}

	/**
	 * Handle an import submission.
	 *
	 * @return void
	 */
	public function handle_import(): void {
		check_admin_referer( self::NONCE_ACTION );

		if ( ! $this->security->can_manage_field_groups() ) {
			wp_die( esc_html__( 'You are not allowed to import field groups.', 'openfields' ) );
		}

		$json = isset( $_POST['openfields_import_json'] )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON payload validated by the importer.
			? (string) wp_unslash( $_POST['openfields_import_json'] )
			: '';

		$imported = $this->importer->from_json( $json );

		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type'           => PostType::POST_TYPE,
					'page'                => self::PAGE_SLUG,
					'openfields_imported' => count( $imported ),
				),
				admin_url( 'edit.php' )
			)
		);
		exit;
	}
}
