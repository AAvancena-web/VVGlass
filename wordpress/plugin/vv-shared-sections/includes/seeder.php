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
 * Writes one field, addressing it by key rather than by name.
 *
 * update_field() given a bare name resolves it through the post's own
 * "_{name}" reference meta. On a section that has just been created that meta
 * does not exist, and a field registered in code — as ours are — cannot be
 * found by name either, so ACF falls back to treating it as a plain text field.
 * A string survives that; the questions repeater would be written as one
 * serialised blob instead of rows, and then render as nothing.
 *
 * @param string $name    Field name.
 * @param mixed  $value   Value to store.
 * @param int    $post_id Section ID.
 */
function vvss_seed_field( $name, $value, $post_id ) {

	$map      = vvss_field_keys();
	$selector = isset( $map[ $name ] ) ? $map[ $name ] : 'field_vvss_' . $name;

	return update_field( $selector, $value, $post_id );
}

/**
 * Maps each section field name to the key it was registered under.
 *
 * @return array name => field key.
 */
function vvss_field_keys() {

	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map = array();

	if ( ! function_exists( 'acf_get_local_fields' ) ) {
		return $map;
	}

	foreach ( acf_get_local_fields( 'group_vvss_shared_section' ) as $field ) {
		if ( ! empty( $field['name'] ) && ! empty( $field['key'] ) ) {
			$map[ $field['name'] ] = $field['key'];
		}
	}

	return $map;
}

/**
 * Clears repeater meta an earlier seeder run stored in the wrong format.
 *
 * A correctly stored repeater keeps its row count in the meta row named after
 * the field. The earlier run wrote the whole rows array there instead, which
 * ACF cannot read back, so the questions render as nothing. Deleting the two
 * meta rows puts the field back to never-written, and the seeder then fills it
 * properly. Rows that were saved correctly are numeric and left alone.
 *
 * @param int $post_id Section ID.
 * @return array Names of the fields that were cleared.
 */
function vvss_repair_repeaters( $post_id ) {

	$repaired = array();

	if ( ! function_exists( 'acf_get_field' ) ) {
		return $repaired;
	}

	foreach ( vvss_field_keys() as $name => $key ) {

		$field = acf_get_field( $key );
		if ( ! $field || 'repeater' !== $field['type'] ) {
			continue;
		}

		$raw = get_post_meta( $post_id, $name, true );
		if ( '' === $raw || is_numeric( $raw ) ) {
			continue;
		}

		delete_post_meta( $post_id, $name );
		delete_post_meta( $post_id, '_' . $name );
		$repaired[] = $name;
	}

	return $repaired;
}

/**
 * Is there already a site-wide section — one with no Page Group?
 *
 * @return int Its ID, or 0.
 */
function vvss_sitewide_section_ids() {

	$found = get_posts(
		array(
			'post_type'      => 'shared_section',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	$ids = array();

	foreach ( $found as $id ) {
		$groups = wp_get_object_terms( $id, 'page_group', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $groups ) || empty( $groups ) ) {
			$ids[] = (int) $id;
		}
	}

	return $ids;
}

function vvss_sitewide_section_id() {

	$ids = vvss_sitewide_section_ids();
	if ( $ids ) {
		return $ids[0];
	}

	/*
	 * Belt and braces. The check above is the real one, but if a site-wide
	 * section has somehow been tagged with a Page Group it would not be found,
	 * and seeding again would make a second copy of the same block. Match the
	 * title too, so a section this seeder created is never duplicated.
	 */
	$data = require __DIR__ . '/seed-global.php';

	return vvss_section_exists( $data['title'] );
}

/**
 * Create the site-wide section: the intro, FAQ and contact block that appears
 * above the footer on every page.
 *
 * Skipped entirely when a site-wide section already exists, whatever it is
 * called, so an install that already has one is never given a second.
 *
 * @return array Log lines.
 */
function vvss_seed_global_section() {

	$log = array();

	$existing = vvss_sitewide_section_id();
	if ( $existing ) {
		$log[] = sprintf(
			'"%s" already covers this, so no new site-wide section was created.',
			get_the_title( $existing )
		);
		return $log;
	}

	$data = require __DIR__ . '/seed-global.php';

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'shared_section',
			'post_status' => 'publish',
			'post_title'  => $data['title'],
			'menu_order'  => 0,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		$log[] = sprintf( 'Could not create "%s": %s', $data['title'], $post_id->get_error_message() );
		return $log;
	}

	// No page_group terms: an empty Page Group is what makes it site-wide.

	if ( function_exists( 'update_field' ) ) {
		vvss_seed_field( 'show_above_footer', 1, $post_id );
		foreach ( $data['fields'] as $name => $value ) {
			vvss_seed_field( $name, $value, $post_id );
		}
		vvss_seed_field( 'faqs', $data['faqs'], $post_id );
	}

	$log[] = sprintf(
		'Created "%s" — the site-wide intro, %d FAQs and the contact block. Add your Contact Form 7 shortcode on its Contact tab.',
		$data['title'],
		count( $data['faqs'] )
	);

	return $log;
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

	// The site-wide block first, so it exists before anything sits above it.
	$log = array_merge( $log, vvss_seed_global_section() );

	foreach ( $data as $row ) {

		$existing = vvss_section_exists( $row['title'] );

		if ( $existing ) {

			// Repair anything an earlier run stored in the wrong format, then
			// refill only what that repair emptied. Everything else is left as
			// the client edited it.
			$repaired = function_exists( 'update_field' ) ? vvss_repair_repeaters( $existing ) : array();

			if ( in_array( 'content_questions', $repaired, true ) ) {
				vvss_seed_field( 'content_questions', $row['questions'], $existing );
				$log[] = sprintf( '"%s" already existed — re-saved its questions, which an earlier run had stored in the wrong format.', $row['title'] );
			} else {
				$log[] = sprintf( '"%s" already exists — left alone.', $row['title'] );
			}

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
			vvss_seed_field( 'show_above_footer', 1, $post_id );
			vvss_seed_field( 'content_eyebrow', $row['eyebrow'], $post_id );
			vvss_seed_field( 'content_heading', $row['heading'], $post_id );
			vvss_seed_field( 'content_intro', $row['intro'], $post_id );
			vvss_seed_field( 'content_qa_heading', $row['qa_heading'], $post_id );
			vvss_seed_field( 'content_outro', $row['outro'], $post_id );
			vvss_seed_field( 'content_cta_label', 'Book Your Consultation Here', $post_id );
			vvss_seed_field( 'content_cta_url', '#contact', $post_id );
			vvss_seed_field( 'content_questions', $row['questions'], $post_id );
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

/**
 * Warn when more than one site-wide section is switched on.
 *
 * Two of them means the intro, FAQ and contact block renders twice on every
 * page. Nothing here changes anything on its own — deciding which one to keep
 * is the editor's call — but silently outputting the block twice is worse than
 * saying so.
 */
function vvss_duplicate_sitewide_notice() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'edit-shared_section', 'shared_section', 'shared_section_page_vvss-setup', 'dashboard' ), true ) ) {
		return;
	}

	$enabled = array();
	foreach ( vvss_sitewide_section_ids() as $id ) {
		if ( 'publish' === get_post_status( $id ) && vvss_section_enabled( $id ) ) {
			$enabled[] = $id;
		}
	}

	if ( count( $enabled ) < 2 ) {
		return;
	}

	$links = array();
	foreach ( $enabled as $id ) {
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_edit_post_link( $id ) ),
			esc_html( get_the_title( $id ) )
		);
	}

	printf(
		'<div class="notice notice-warning"><p><strong>VV Shared Sections:</strong> %d sections have no Page Group, so all of them render above the footer on every page — the intro, FAQ and contact block is appearing more than once. Keep one and either switch the others off with <em>Show this section</em> or delete them: %s</p></div>',
		count( $enabled ),
		wp_kses_post( implode( ' &middot; ', $links ) )
	);
}
add_action( 'admin_notices', 'vvss_duplicate_sitewide_notice' );
