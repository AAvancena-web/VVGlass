<?php
/**
 * VV Glass redesign — add this block to the END of the child theme's
 * functions.php (after the existing code, before nothing else).
 *
 * Do not include the opening <?php tag below when pasting — your
 * functions.php already has one.
 */

/* ==========================================================================
   1. Kill switch
   --------------------------------------------------------------------------
   Template files have no Deactivate button, so this is the equivalent.

   Set to false and header.php / footer.php hand straight back to
   header-legacy.php / footer-legacy.php — your untouched originals — and the
   redesign stylesheet stops loading. One line, full revert, no FTP juggling.
   ========================================================================== */
if ( ! defined( 'VVG_REDESIGN' ) ) {
	define( 'VVG_REDESIGN', true );
}

function vvg_redesign_active() {
	return (bool) apply_filters( 'vvg_redesign_active', VVG_REDESIGN );
}

/* ==========================================================================
   2. Redesign assets
   --------------------------------------------------------------------------
   Loaded after the child theme stylesheet so it wins on equal specificity,
   and only when the redesign is switched on.

   Units are px throughout, deliberately. The theme sets html{font-size:calc()}
   which collapses the root below ~10px under 1200px, so rem values would
   silently shrink on tablet and mobile.
   ========================================================================== */
function vvg_enqueue_redesign() {
	if ( ! vvg_redesign_active() ) {
		return;
	}

	$css = get_stylesheet_directory() . '/assets/vvg-redesign.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'vvg-redesign',
			get_stylesheet_directory_uri() . '/assets/vvg-redesign.css',
			array( 'chld_thm_cfg_child' ),
			filemtime( $css )
		);
	}

	$js = get_stylesheet_directory() . '/assets/vvg-redesign.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'vvg-redesign',
			get_stylesheet_directory_uri() . '/assets/vvg-redesign.js',
			array(),
			filemtime( $js ),
			true
		);
	}

	wp_enqueue_style(
		'vvg-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'vvg_enqueue_redesign', 20 );

/* ==========================================================================
   3. Contact details, from one place
   --------------------------------------------------------------------------
   Reads the site-wide Shared Section so the header, footer and contact block
   all quote the same phone number and email. Falls back to the constants below
   if the plugin is inactive or the field is empty, so the header never renders
   blank.
   ========================================================================== */
function vvg_contact( $what ) {
	static $cache = null;

	$fallback = array(
		'phone'   => '0412 991 904',
		'email'   => 'damian@vvglass.com.au',
		'address' => 'Engadine, NSW 2233',
		'map'     => 'https://maps.app.goo.gl/PJBdqJGm9LsEoLTp8',
	);

	if ( null === $cache ) {
		$cache = array();

		if ( function_exists( 'get_field' ) && post_type_exists( 'shared_section' ) ) {
			$ids = get_posts(
				array(
					'post_type'      => 'shared_section',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'orderby'        => 'menu_order',
					'order'          => 'DESC',
					'no_found_rows'  => true,
				)
			);
			if ( $ids ) {
				$cache['phone'] = trim( (string) get_field( 'phone', $ids[0] ) );
				$cache['email'] = trim( (string) get_field( 'email', $ids[0] ) );
			}
		}
	}

	$value = isset( $cache[ $what ] ) && '' !== $cache[ $what ] ? $cache[ $what ] : '';
	if ( '' === $value ) {
		$value = isset( $fallback[ $what ] ) ? $fallback[ $what ] : '';
	}

	return apply_filters( "vvg_contact_{$what}", $value );
}

/**
 * Australian tel: href — leading 0 becomes +61.
 */
function vvg_tel_href( $number = '' ) {
	$number = $number ? $number : vvg_contact( 'phone' );
	$digits = preg_replace( '/[^0-9+]/', '', (string) $number );
	if ( '' !== $digits && '0' === substr( $digits, 0, 1 ) ) {
		$digits = '+61' . substr( $digits, 1 );
	}
	return $digits;
}

/* ==========================================================================
   4. The Contact Form 7 id, in one place
   --------------------------------------------------------------------------
   Was hardcoded in both functions.php and header.php. Rebuild the form and
   only this line needs changing.
   ========================================================================== */
if ( ! defined( 'VVG_ENQUIRY_FORM_ID' ) ) {
	define( 'VVG_ENQUIRY_FORM_ID', '7c79e65' );
}

function vvg_enquiry_form() {
	if ( ! shortcode_exists( 'contact-form-7' ) ) {
		return '';
	}
	return do_shortcode( '[contact-form-7 id="' . VVG_ENQUIRY_FORM_ID . '" title="Get In Touch"]' );
}
