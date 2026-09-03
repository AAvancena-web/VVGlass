<?php
/**
 * One-time seeder for the homepage template fields.
 *
 * Fills in every text field and repeater row so the page is standing up before
 * anyone opens it. Images are left empty on purpose — they have to be chosen
 * from the media library, and guessing would be worse than an obvious gap.
 *
 * Runs from a button, never automatically, and never overwrites a field that
 * already has something in it.
 *
 * @package siteorigin-corp-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The content, matching the approved design.
 */
function vvg_home_seed_data() {
	return array(
		'hero_eyebrow'      => 'Australian Window Solutions',
		'hero_heading'      => 'VV Glass – When Quality Matters',
		'hero_text'         => 'The Trusted Australian Window Solutions for Glass Glazing, Installation & Repair Servicing Sydney, Wollongong & Surrounds',
		'hero_cta_label'    => 'Book Your Consultation Here',
		'hero_cta_url'      => '#contact',
		'hero_form_heading' => 'Get In Touch With VV Glass',
		'hero_form_sub'     => 'Fast, obligation-free quotes across Sydney & Wollongong',
		'hero_trust'        => array(
			array( 'icon' => 'shield', 'label' => 'Licensed & Insured' ),
			array( 'icon' => 'clock', 'label' => '24/7 Emergency Response' ),
			array( 'icon' => 'star', 'label' => '5 Star Rated Service' ),
		),

		'why_eyebrow'   => 'Why Choose Us',
		'why_heading'   => 'Why Choose VV Glass for Your Australian Window Solutions ?',
		'why_cta_label' => 'Book Your Consultation Here',
		'why_cta_url'   => '#contact',
		'why_points'    => array(
			array( 'title' => 'Fast Reliable Service', 'text' => 'Emergency repairs and servicing with fast response times so you stay safe and secure.' ),
			array( 'title' => 'High-Quality & Compliant', 'text' => 'We use durable, certified glass solutions that comply with all Australian safety and building standards.' ),
			array( 'title' => 'Transparent Pricing', 'text' => 'Honest quotes with no surprises or hidden costs, ever!' ),
			array( 'title' => 'Fully Licensed & Insured', 'text' => 'Enjoy peace of mind! Our licensed glaziers are trained and insured professionals.' ),
			array( 'title' => 'Customer-Centric Focus', 'text' => 'From your first enquiry to the final clean-up, your satisfaction guides everything we do.' ),
		),

		'services_eyebrow'   => 'Our Services',
		'services_heading'   => 'Our Glass and Glazing Services',
		'services_cta_label' => 'Book Your Consultation Here',
		'services_cta_url'   => '#contact',
		'services_items'     => array(
			array( 'title' => 'Glass Installation', 'url' => '/glass-installation/', 'text' => 'Custom window & glass installation: residential, commercial, custom designs' ),
			array( 'title' => 'Glass Repair', 'url' => '/glass-repair/', 'text' => 'Speedy repairs for chipped, cracked, or shattered glass: 24/7 emergency support' ),
			array( 'title' => 'Glass Replacement', 'url' => '/glass-replacement/', 'text' => 'Precision replacement services: windows, shopfronts, façades' ),
			array( 'title' => 'Glass Fencing', 'url' => '/glass-fencing/', 'text' => 'Stylish, secure, compliant glass fencing for pools, patios, and balconies' ),
		),

		'projects_eyebrow'   => 'Our Recent Projects',
		'projects_heading'   => 'Where Quality Meets Clarity & Craftsmanship',
		'projects_cta_label' => 'Book Your Consultation Here',
		'projects_cta_url'   => '#contact',

		'band_heading'   => 'Your Reliable Glass Glazing Partner',
		'band_text'      => "We deliver expert glazing services across residential, commercial, and industrial sectors, available anytime, day or night. Whether it's a safety-driven replacement, a bespoke glass design, or an urgent repair, our team treats every project with care and precision.",
		'band_cta_label' => 'Book Your Consultation Here',
		'band_cta_url'   => '#contact',
		'band_items'     => array(
			array( 'icon' => 'bolt', 'title' => 'Emergency Glass Repairs 24/7', 'text' => 'We offer quick turnaround for emergency glass repair, replacement, and installation services for residential, commercial, and industrial clients.' ),
			array( 'icon' => 'grid', 'title' => 'Window Glass Repair & Replacement', 'text' => 'Our team of expert glaziers provides seamless installation and restoration services to residential, commercial, and industrial clients.' ),
			array( 'icon' => 'shield', 'title' => 'Safety & Security Glass', 'text' => 'We deliver secure and elegant solutions, including balustrades, shower screens, and partitions, that elevate the aesthetics of the area.' ),
		),

		'review_eyebrow'   => 'Customer Review',
		'review_heading'   => 'What our Customer Says',
		'review_sub'       => 'Our Award Winning Renovation Reviews',
		'review_quote'     => '"VV Glass replaced my broken shopfront within hours of my call. Highly professional and excellent quality work!"',
		'review_name'      => 'Mark T.',
		'review_location'  => 'Sydney',
		'review_cta_label' => 'Book Your Consultation Here',
		'review_cta_url'   => '#contact',

		'about_eyebrow'   => 'About Us',
		'about_heading'   => 'Clear Australian Window Solutions for Every Pane',
		'about_body'      => '<p>VV Glass is proud to be an Australian-owned window glazing company focused on delivering durability, design, and safety. Our fully licensed and insured glaziers bring decades of experience and a passion for precision to every project. From a single window fix to large-scale commercial façades, we bring clarity and craftsmanship to every pane.</p>' .
							"<p>We believe in transparent communication, clear pricing, and reliable service. Whether you're a homeowner looking to replace a broken window, a business owner in need of a shopfront upgrade, or an industrial company looking for custom-designed glass solutions to meet the unique safety and functional needs of their facility, we treat every Australian window solutions project with equal care and attention.</p>",
		'about_cta_label' => 'Book Your Consultation Here',
		'about_cta_url'   => '#contact',

		'pillars_eyebrow'   => 'How We Work',
		'pillars_heading'   => 'A Simpler Way to Manage Your Glazing Project',
		'pillars_text'      => 'From the first phone call to the final clean-up, we keep your project organised, accountable and easy to follow.',
		'pillars_cta_label' => 'Book Your Consultation Here',
		'pillars_cta_url'   => '#contact',
		'pillars_items'     => array(
			array( 'icon' => 'clipboard', 'title' => 'Fully Project Managed', 'text' => 'We expertly handle every detail from start to finish. From initial consultation through installation and aftercare, our professional team ensures your project runs smoothly, on time, and to the highest standard. You can relax knowing every step is managed with precision.' ),
			array( 'icon' => 'person', 'title' => 'Single Contact', 'text' => "With us, you will have one dedicated point of contact, ensuring you always speak to someone who understands your project. It's a simpler, more efficient way to work, designed to reduce your stress, provide continuity and understanding for faster resolutions and a more reliable partnership." ),
			array( 'icon' => 'chat', 'title' => 'Clear Communication', 'text' => "We believe great service starts with being easy to talk to. That's why we provide honest updates, straightforward pricing, and clear timelines. You'll always know what's happening and when to expect results. Ultimately, this transparency helps us build a relationship based on trust." ),
		),
	);
}

/**
 * Which page should this go on?
 */
function vvg_home_target_id() {
	$id = (int) get_option( 'page_on_front' );
	if ( $id ) {
		return $id;
	}
	$found = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-home.php',
			'no_found_rows'  => true,
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Maps each homepage field name to the key it was registered under.
 *
 * update_field() given a bare name resolves it through the post's own
 * "_{name}" reference meta. On a page that has never stored these fields that
 * meta does not exist, and a field registered in code — as ours are — cannot be
 * found by name either, so ACF falls back to treating it as a plain text field.
 * A string survives that; a repeater is written as one serialised blob instead
 * of rows, and then renders as nothing. Passing the key skips the guesswork.
 *
 * @return array name => field key.
 */
function vvg_home_field_keys() {

	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map = array();

	if ( ! function_exists( 'acf_get_local_fields' ) ) {
		return $map;
	}

	foreach ( acf_get_local_fields( 'group_vvg_home' ) as $field ) {
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
 * ACF cannot read back, so those sections rendered as nothing. Deleting the two
 * meta rows puts the field back to never-written, and the seeder then fills it
 * properly. Rows that were saved correctly are numeric and left alone.
 *
 * @param int   $page Page ID.
 * @param array $keys name => field key.
 * @return array Names of the fields that were cleared.
 */
function vvg_home_repair_repeaters( $page, $keys ) {

	$repaired = array();

	if ( ! function_exists( 'acf_get_field' ) ) {
		return $repaired;
	}

	foreach ( $keys as $name => $key ) {

		$field = acf_get_field( $key );
		if ( ! $field || 'repeater' !== $field['type'] ) {
			continue;
		}

		$raw = get_post_meta( $page, $name, true );
		if ( '' === $raw || is_numeric( $raw ) ) {
			continue;
		}

		delete_post_meta( $page, $name );
		delete_post_meta( $page, '_' . $name );
		$repaired[] = $name;
	}

	return $repaired;
}

/**
 * Fill the fields. Never overwrites anything already set.
 *
 * @param bool $assign_template Also switch the page to the homepage template.
 * @return array Lines describing what happened.
 */
function vvg_run_home_seed( $assign_template = false ) {

	$log  = array();
	$page = vvg_home_target_id();

	if ( ! $page ) {
		return array( 'No front page is set (Settings → Reading), so there is nothing to fill in.' );
	}

	$log[] = sprintf( 'Target page: "%s" (ID %d).', get_the_title( $page ), $page );

	if ( $assign_template ) {
		$current = get_post_meta( $page, '_wp_page_template', true );
		if ( 'template-home.php' === $current ) {
			$log[] = 'Already using the VV Glass Homepage template.';
		} else {
			update_post_meta( $page, '_wp_page_template', 'template-home.php' );
			$log[] = 'Switched the page to the VV Glass Homepage template. Its old page-builder content is still in the database — switch back to Default to see it again.';
		}
	}

	if ( ! function_exists( 'update_field' ) ) {
		$log[] = 'ACF is not active, so no fields were filled.';
		return $log;
	}

	$keys        = vvg_home_field_keys();

	$repaired = vvg_home_repair_repeaters( $page, $keys );
	if ( $repaired ) {
		$log[] = sprintf(
			'Cleared %s, which an earlier run had stored in a format ACF cannot read back. They are refilled below.',
			implode( ', ', $repaired )
		);
	}
	$filled      = 0;
	$skipped     = 0;
	$unresolved  = array();

	foreach ( vvg_home_seed_data() as $name => $value ) {

		// Always address the field by key — see vvg_home_field_keys().
		$selector = isset( $keys[ $name ] ) ? $keys[ $name ] : 'field_vvg_' . $name;

		if ( ! isset( $keys[ $name ] ) ) {
			$unresolved[] = $name;
		}

		$existing = get_field( $selector, $page );

		$is_empty = ( null === $existing || '' === $existing || array() === $existing || false === $existing );
		if ( ! $is_empty ) {
			$skipped++;
			continue;
		}

		update_field( $selector, $value, $page );
		$filled++;
	}

	$log[] = sprintf( '%d fields filled, %d left alone because they already had content.', $filled, $skipped );

	if ( $unresolved ) {
		$log[] = sprintf(
			'Could not look up a field key for: %s. Check that includes/acf-home.php is uploaded and that ACF is active.',
			implode( ', ', $unresolved )
		);
	}
	$log[] = 'Images are not seeded — add the hero slides, the Why Choose Us image, the four service images, the project gallery and the About image from the media library.';

	update_option( 'vvg_home_seeded_at', current_time( 'mysql' ), false );
	return $log;
}

/**
 * Adds the homepage tools to the plugin's Set Up Content screen, so there is
 * one place to do this rather than two.
 */
function vvg_home_seed_ui() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$log = array();
	if ( isset( $_POST['vvg_home_seed'] ) && check_admin_referer( 'vvg_home_seed_action', 'vvg_home_seed_nonce' ) ) {
		$log = vvg_run_home_seed( ! empty( $_POST['vvg_assign_template'] ) );
	}

	$seeded_at = get_option( 'vvg_home_seeded_at' );
	?>
	<div class="card" style="max-width:820px;margin-top:20px">
		<h2><?php esc_html_e( 'Homepage', 'siteorigin-corp' ); ?></h2>

		<?php if ( $log ) : ?>
			<div class="notice notice-success inline" style="margin:10px 0">
				<ul style="list-style:disc;margin:10px 0 10px 20px">
					<?php foreach ( $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Fills in every heading, paragraph and repeater row on the homepage template so the page is standing up before you open it.', 'siteorigin-corp' ); ?></p>
		<p><?php esc_html_e( 'Nothing already filled in is overwritten. Images are left empty — those have to be chosen from the media library.', 'siteorigin-corp' ); ?></p>

		<?php if ( $seeded_at ) : ?>
			<p><strong><?php echo esc_html( sprintf( __( 'Last run: %s', 'siteorigin-corp' ), $seeded_at ) ); ?></strong></p>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'vvg_home_seed_action', 'vvg_home_seed_nonce' ); ?>
			<p>
				<label>
					<input type="checkbox" name="vvg_assign_template" value="1" checked>
					<?php esc_html_e( 'Also switch the front page to the VV Glass Homepage template', 'siteorigin-corp' ); ?>
				</label>
			</p>
			<p>
				<button type="submit" name="vvg_home_seed" value="1" class="button button-primary">
					<?php echo $seeded_at ? esc_html__( 'Run again', 'siteorigin-corp' ) : esc_html__( 'Fill in the homepage', 'siteorigin-corp' ); ?>
				</button>
			</p>
		</form>
	</div>
	<?php
}
add_action( 'vvss_setup_page_extra', 'vvg_home_seed_ui' );

/**
 * If the plugin is not active there is no screen to hook onto, so fall back to
 * a page of our own under Tools.
 */
function vvg_home_seed_fallback_menu() {
	if ( post_type_exists( 'shared_section' ) ) {
		return; // The plugin's screen is hosting us.
	}
	add_management_page(
		__( 'VV Glass Homepage', 'siteorigin-corp' ),
		__( 'VV Glass Homepage', 'siteorigin-corp' ),
		'manage_options',
		'vvg-home-setup',
		function () {
			echo '<div class="wrap"><h1>' . esc_html__( 'VV Glass Homepage', 'siteorigin-corp' ) . '</h1>';
			vvg_home_seed_ui();
			echo '</div>';
		}
	);
}
add_action( 'admin_menu', 'vvg_home_seed_fallback_menu', 20 );
