<?php
/**
 * One-time content seeder.
 *
 * Creates the five page groups and the five Content sections from the client's
 * content document, so nobody has to paste 6,400 words by hand.
 *
 * Deliberately manual: it runs from a button, never on activation, and it will
 * not touch a section that already exists. Running it twice is harmless.
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin page under Shared Sections.
 */
function vvss_setup_menu() {
	add_submenu_page(
		'edit.php?post_type=shared_section',
		__( 'Set Up Content', 'vv-shared-sections' ),
		__( 'Set Up Content', 'vv-shared-sections' ),
		'manage_options',
		'vvss-setup',
		'vvss_setup_page'
	);
}
add_action( 'admin_menu', 'vvss_setup_menu' );

/**
 * Has a section with this title already been created?
 */
function vvss_section_exists( $title ) {
	$found = get_posts(
		array(
			'post_type'      => 'shared_section',
			'post_status'    => array( 'publish', 'draft', 'pending' ),
			'title'          => $title,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	return ! empty( $found ) ? (int) $found[0] : 0;
}

/**
 * Create the five page groups and the five Content sections.
 *
 * @return array Lines describing what happened.
 */
function vvss_run_seed() {
	$log  = array();
	$data = require __DIR__ . '/seed-data.php';

	// Page groups first — a section cannot be tagged with a term that is missing.
	if ( ! taxonomy_exists( 'page_group' ) ) {
		vvss_register_taxonomy();
	}
	foreach ( array( 'Hub', 'Installation', 'Repair', 'Replacement', 'Fencing' ) as $name ) {
		if ( term_exists( $name, 'page_group' ) ) {
			$log[] = sprintf( 'Page Group "%s" already existed.', $name );
		} else {
			wp_insert_term( $name, 'page_group' );
			$log[] = sprintf( 'Created Page Group "%s".', $name );
		}
	}

	foreach ( $data as $row ) {

		if ( vvss_section_exists( $row['title'] ) ) {
			$log[] = sprintf( '"%s" already exists — left alone.', $row['title'] );
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'shared_section',
				'post_status' => 'publish',
				'post_title'  => $row['title'],
				'menu_order'  => 0,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$log[] = sprintf( 'Could not create "%s": %s', $row['title'], $post_id->get_error_message() );
			continue;
		}

		wp_set_object_terms( $post_id, $row['group'], 'page_group', false );

		update_post_meta( $post_id, 'show_above_footer', '1' );

		if ( function_exists( 'update_field' ) ) {
			update_field( 'show_above_footer', 1, $post_id );
			update_field( 'content_eyebrow', $row['eyebrow'], $post_id );
			update_field( 'content_heading', $row['heading'], $post_id );
			update_field( 'content_intro', $row['intro'], $post_id );
			update_field( 'content_qa_heading', $row['qa_heading'], $post_id );
			update_field( 'content_outro', $row['outro'], $post_id );
			update_field( 'content_cta_label', 'Book Your Consultation Here', $post_id );
			update_field( 'content_cta_url', '#contact', $post_id );
			update_field( 'content_questions', $row['questions'], $post_id );
		}

		$log[] = sprintf(
			'Created "%s" — %d questions, Page Group: %s.',
			$row['title'],
			count( $row['questions'] ),
			$row['group']
		);
	}

	update_option( 'vvss_seeded_at', current_time( 'mysql' ), false );
	return $log;
}

/**
 * The page itself.
 */
function vvss_setup_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'vv-shared-sections' ) );
	}

	$log = array();

	if ( isset( $_POST['vvss_seed'] ) && check_admin_referer( 'vvss_seed_action', 'vvss_seed_nonce' ) ) {
		$log = vvss_run_seed();
	}

	$seeded_at = get_option( 'vvss_seeded_at' );
	$acf_ready = function_exists( 'update_field' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Set Up Content', 'vv-shared-sections' ); ?></h1>

		<?php if ( ! $acf_ready ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'ACF PRO needs to be active before seeding, or the fields cannot be filled in.', 'vv-shared-sections' ); ?></p></div>
		<?php endif; ?>

		<?php if ( $log ) : ?>
			<div class="notice notice-success">
				<p><strong><?php esc_html_e( 'Done.', 'vv-shared-sections' ); ?></strong></p>
				<ul style="list-style:disc;margin-left:20px">
					<?php foreach ( $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:820px">
			<h2><?php esc_html_e( 'Shared sections', 'vv-shared-sections' ); ?></h2>
			<p><?php esc_html_e( 'Creates the five Page Groups and the five Content sections from the content document — around 6,400 words, word for word.', 'vv-shared-sections' ); ?></p>
			<p><?php esc_html_e( 'It never overwrites: a section whose title already exists is left exactly as it is, so running this twice is safe. Your existing site-wide section, with the intro, FAQ and contact details, is not touched.', 'vv-shared-sections' ); ?></p>
			<p><em><?php esc_html_e( 'You still need to tag your pages with a Page Group afterwards — Pages, All Pages, Bulk Edit.', 'vv-shared-sections' ); ?></em></p>

			<?php if ( $seeded_at ) : ?>
				<p><strong><?php echo esc_html( sprintf( __( 'Last run: %s', 'vv-shared-sections' ), $seeded_at ) ); ?></strong></p>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'vvss_seed_action', 'vvss_seed_nonce' ); ?>
				<p>
					<button type="submit" name="vvss_seed" value="1" class="button button-primary"<?php disabled( ! $acf_ready ); ?>>
						<?php echo $seeded_at ? esc_html__( 'Run again', 'vv-shared-sections' ) : esc_html__( 'Create the sections', 'vv-shared-sections' ); ?>
					</button>
				</p>
			</form>
		</div>

		<?php
		/**
		 * Lets the theme add its own seeding tools to this screen rather than
		 * putting a second one somewhere else in the admin.
		 */
		do_action( 'vvss_setup_page_extra' );
		?>
	</div>
	<?php
}
