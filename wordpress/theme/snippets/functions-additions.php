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

	/*
	 * Montserrat only. The theme already ships Muli as a local @font-face
	 * (font/Muli.ttf, Muli-Bold, Muli-SemiBold) in style.css, so pulling
	 * Mulish from Google as well would be a second copy of the same typeface
	 * over the network.
	 */
	wp_enqueue_style(
		'vvg-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	// Homepage-only styles, so inner pages carry none of this weight.
	if ( is_front_page() ) {
		$home = get_stylesheet_directory() . '/assets/vvg-home.css';
		if ( file_exists( $home ) ) {
			wp_enqueue_style(
				'vvg-home',
				get_stylesheet_directory_uri() . '/assets/vvg-home.css',
				array( 'vvg-redesign' ),
				filemtime( $home )
			);
		}
	}
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

/* ==========================================================================
   5. Homepage hero
   --------------------------------------------------------------------------
   The theme's services_slider shortcode outputs only the heading and the
   paragraph, so the eyebrow, the second button and the trust row from the
   design have nowhere to render — no amount of CSS can add them.

   Re-registering the same shortcode tag replaces the callback, and because
   this block sits at the END of functions.php it registers last and wins. The
   original function is left completely untouched, so turning the redesign off
   restores it.
   ========================================================================== */
function vvg_services_slider() {

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'custom_slider', 'option' ) ) {
		return '';
	}

	$heading = function_exists( 'get_field' ) ? get_field( 'banner_heading' ) : '';
	$content = function_exists( 'get_field' ) ? get_field( 'banner_content' ) : '';

	// Eyebrow: an ACF field if one exists, otherwise a filterable default.
	$eyebrow = function_exists( 'get_field' ) ? trim( (string) get_field( 'banner_eyebrow' ) ) : '';
	if ( '' === $eyebrow ) {
		$eyebrow = apply_filters( 'vvg_hero_eyebrow', 'Australian Window Solutions' );
	}

	/*
	 * The primary button lives inside the banner_content WYSIWYG. Lift it out
	 * so it can sit in a row beside the call button instead of on its own
	 * line, then drop the empty paragraph it leaves behind.
	 */
	$cta = '';
	if ( $content && preg_match( '#<a[^>]*class="[^"]*\bbtn\b[^"]*"[^>]*>.*?</a>#is', $content, $m ) ) {
		$cta     = $m[0];
		$content = str_replace( $m[0], '', $content );
		$content = preg_replace( '#<p>(\s|&nbsp;)*</p>#i', '', $content );
	}
	if ( '' === $cta ) {
		$cta = '<a class="btn" href="#contact">' . esc_html__( 'Book Your Consultation Here', 'siteorigin-corp' ) . '</a>';
	}

	$trust = apply_filters(
		'vvg_hero_trust',
		array(
			'shield' => 'Licensed &amp; Insured',
			'clock'  => '24/7 Emergency Response',
			'star'   => '5 Star Rated Service',
		)
	);

	$icons = array(
		'shield' => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5zm-1 14-4-4 1.4-1.4L11 13.2l4.6-4.6L17 10z"/>',
		'clock'  => '<path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 10.6V6h-2v7.4l5 3 1-1.7z"/>',
		'star'   => '<path d="m12 2 2.9 6.3 6.9.8-5.1 4.6 1.4 6.8L12 17.1 5.9 20.5l1.4-6.8L2.2 9.1l6.9-.8z"/>',
	);

	$phone      = vvg_contact( 'phone' );
	$phone_href = vvg_tel_href();

	ob_start();
	?>
	<div class="single-featured-image-header">
		<ul class="review-slider2">
			<?php
			while ( have_rows( 'custom_slider', 'option' ) ) :
				the_row();
				$banner_image = get_sub_field( 'banner_image' );
				if ( empty( $banner_image['url'] ) ) {
					continue;
				}
				?>
				<li class="custom_slide_box">
					<div class="banner-image">
						<img src="<?php echo esc_url( $banner_image['url'] ); ?>" alt="<?php echo esc_attr( isset( $banner_image['alt'] ) ? $banner_image['alt'] : '' ); ?>">
					</div>
				</li>
			<?php endwhile; ?>
		</ul>

		<div class="slider_controls2">
			<button type="button" class="slick-prev">Previous</button>
			<div class="slick-dots"></div>
			<button type="button" class="slick-next">Next</button>
		</div>

		<div class="form-banner-and-content main-content">
			<div class="banner-content">

				<?php if ( $eyebrow ) : ?>
					<span class="vvg-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( $heading ) : ?>
					<h1 class="slider_heading"><?php echo esc_html( $heading ); ?></h1>
				<?php endif; ?>

				<?php if ( trim( (string) $content ) ) : ?>
					<div class="slider_paragraph"><?php echo wp_kses_post( $content ); ?></div>
				<?php endif; ?>

				<div class="vvg-hero-actions">
					<?php echo wp_kses_post( $cta ); ?>
					<a class="btn vvg-btn-hero-light" href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( sprintf( 'Call %s', $phone ) ); ?></a>
				</div>

				<?php if ( $trust ) : ?>
					<ul class="vvg-hero-trust">
						<?php foreach ( $trust as $key => $label ) : ?>
							<li>
								<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['star']; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?></svg>
								<?php echo wp_kses_post( $label ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div>

			<?php if ( is_front_page() ) : ?>
				<div class="banner-fixed-form get-in-touch-form">
					<h4 class="font-30"><?php esc_html_e( 'Get In Touch With VV Glass', 'siteorigin-corp' ); ?></h4>
					<?php echo vvg_enquiry_form(); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- CF7 output. ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// Only take over while the redesign is on; otherwise the theme's own version stands.
add_action(
	'init',
	function () {
		if ( vvg_redesign_active() ) {
			add_shortcode( 'services_slider', 'vvg_services_slider' );
		}
	},
	20
);

/* ==========================================================================
   6. Homepage template support
   ========================================================================== */
require_once get_stylesheet_directory() . '/includes/acf-home.php';

/**
 * Inline SVG icons used by the homepage template.
 */
function vvg_icon( $name ) {
	$paths = array(
		'shield'    => '<path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5zm-1 14-4-4 1.4-1.4L11 13.2l4.6-4.6L17 10z"/>',
		'clock'     => '<path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 10.6V6h-2v7.4l5 3 1-1.7z"/>',
		'star'      => '<path d="m12 2 2.9 6.3 6.9.8-5.1 4.6 1.4 6.8L12 17.1 5.9 20.5l1.4-6.8L2.2 9.1l6.9-.8z"/>',
		'bolt'      => '<path d="M13 2 4.5 13.5H11l-1 8.5 8.5-11.5H12z"/>',
		'grid'      => '<path d="M3 3h18v18H3zm2 2v6h6V5zm8 0v6h6V5zM5 13v6h6v-6zm8 0v6h6v-6z"/>',
		'clipboard' => '<path d="M19 3h-4.2a3 3 0 0 0-5.6 0H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0a1 1 0 1 1-1 1 1 1 0 0 1 1-1zm-1 15-4-4 1.4-1.4L11 15.2l5.6-5.6L18 11z"/>',
		'person'    => '<path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4 0-8 2-8 4.5V21h16v-2.5C20 16 16 14 12 14z"/>',
		'chat'      => '<path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM7 9h10v2H7zm0-4h10v2H7zm0 8h7v2H7z"/>',
		'arrow'     => '<path d="M4 11h12.2l-5.6-5.6L12 4l8 8-8 8-1.4-1.4 5.6-5.6H4z"/>',
	);
	$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['star'];
	return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Render one of the ACF button pairs, or nothing when either half is blank.
 */
function vvg_cta( $name, $classes = 'vvg-btn' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	$label = trim( (string) get_field( "{$name}_label" ) );
	$url   = trim( (string) get_field( "{$name}_url" ) );
	if ( '' === $label || '' === $url ) {
		return '';
	}
	return '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
}
