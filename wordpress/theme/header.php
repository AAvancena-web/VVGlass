<?php
/**
 * The theme header — VV Glass redesign.
 *
 * Set VVG_REDESIGN to false in functions.php and this hands straight back to
 * header-legacy.php, which is your original file renamed and untouched.
 *
 * @package siteorigin-corp-child
 */

if ( function_exists( 'vvg_redesign_active' ) && ! vvg_redesign_active() ) {
	$vvg_legacy = get_theme_file_path( 'header-legacy.php' );
	if ( file_exists( $vvg_legacy ) ) {
		require $vvg_legacy;
		return;
	}
}

$vvg_phone      = function_exists( 'vvg_contact' ) ? vvg_contact( 'phone' ) : '0412 991 904';
$vvg_phone_href = function_exists( 'vvg_tel_href' ) ? vvg_tel_href() : 'tel:0412991904';
$vvg_email      = function_exists( 'vvg_contact' ) ? vvg_contact( 'email' ) : 'damian@vvglass.com.au';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KXB9NSQP');</script>
<!-- End Google Tag Manager -->
<?php
/*
 * The gtag.js snippet that used to sit here has been removed. It was included
 * twice, each with its own gtag('config','G-T2S7QD9EQ3') call, which fires two
 * page_view events per load and inflates sessions. GTM above is the single
 * place GA4 should be configured now. If GTM does NOT currently contain a GA4
 * tag, add one there rather than putting the snippet back.
 */
?>
<meta name="google-site-verification" content="x8WXTOgdjn52MaUbq5Wjp4pcWYjIKIiNDFXy6bJFquI" />
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'vvg' ); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KXB9NSQP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
do_action( 'siteorigin_corp_body_top' );
?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'siteorigin-corp' ); ?></a>

	<?php
	do_action( 'siteorigin_corp_header_before' );

	if ( siteorigin_page_setting( 'header', true ) ) :
	?>

	<!-- ============ TOP BAR ============ -->
	<div class="vvg-topbar">
		<div class="vvg-container">
			<span class="vvg-tagline"><?php echo esc_html( apply_filters( 'vvg_topbar_tagline', 'Servicing Sydney, Wollongong & surrounds · 24/7 emergency glazing' ) ); ?></span>
			<a href="tel:<?php echo esc_attr( $vvg_phone_href ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.9 15.9 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z"/></svg>
				<?php echo esc_html( $vvg_phone ); ?>
			</a>
			<a href="mailto:<?php echo esc_attr( $vvg_email ); ?>">
				<svg class="vvg-ico-mail" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.6 7.4 8.4 5.9 8.4-5.9"/></svg>
				<?php echo esc_html( $vvg_email ); ?>
			</a>
		</div>
	</div>

	<!-- ============ HEADER ============ -->
	<header id="masthead" class="vvg-header">
		<div class="vvg-container">
			<div class="vvg-header-inner">

				<a class="vvg-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> home">
					<?php
					if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<span class="vvg-brand-fallback">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
					}
					?>
				</a>

				<nav class="vvg-nav" aria-label="<?php esc_attr_e( 'Primary', 'siteorigin-corp' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'vvg-primary-menu',
							'container'      => false,
							'menu_class'     => 'menu',
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>

				<a class="vvg-btn vvg-header-cta" href="#contact"><?php esc_html_e( 'Book Your Consultation Here', 'siteorigin-corp' ); ?></a>

				<div class="vvg-header-tools">
					<a class="vvg-phone-circle" href="tel:<?php echo esc_attr( $vvg_phone_href ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Call VV Glass on %s', $vvg_phone ) ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.9 15.9 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z"/></svg>
					</a>
					<button class="vvg-burger" id="vvgBurger" aria-label="<?php esc_attr_e( 'Open menu', 'siteorigin-corp' ); ?>" aria-expanded="false" aria-controls="vvgMobileNav">
						<span></span><span></span><span></span>
					</button>
				</div>

			</div>
		</div>
		<div class="vvg-scroll-progress" aria-hidden="true"><span id="vvgScrollProgress"></span></div>
	</header>

	<div class="vvg-nav-overlay" id="vvgNavOverlay"></div>
	<nav class="vvg-mobile-nav" id="vvgMobileNav" aria-label="<?php esc_attr_e( 'Mobile', 'siteorigin-corp' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'vvg-mobile-menu',
				'container'      => false,
				'menu_class'     => 'menu',
				'fallback_cb'    => false,
			)
		);
		?>
		<div class="vvg-m-contact">
			<a href="tel:<?php echo esc_attr( $vvg_phone_href ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.9 15.9 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z"/></svg><?php echo esc_html( $vvg_phone ); ?></a>
			<a href="mailto:<?php echo esc_attr( $vvg_email ); ?>"><svg class="vvg-ico-mail" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.6 7.4 8.4 5.9 8.4-5.9"/></svg><?php echo esc_html( $vvg_email ); ?></a>
		</div>
		<a class="vvg-btn vvg-btn-block" href="#contact"><?php esc_html_e( 'Book Your Consultation Here', 'siteorigin-corp' ); ?></a>
	</nav>

	<?php
	endif;
	do_action( 'siteorigin_corp_content_before' );

	/*
	 * Inner-page banner. The homepage builds its own hero in the page content,
	 * so this only runs elsewhere. Styling now lives in vvg-redesign.css rather
	 * than the 50-line inline <style> block this replaces.
	 */
	if ( ! is_front_page() ) :

		$vvg_bg = ( is_singular() && has_post_thumbnail() ) ? get_the_post_thumbnail_url( get_queried_object_id(), 'full' ) : '';

		if ( is_singular() && function_exists( 'get_field' ) && get_field( 'banner_heading' ) ) {
			$vvg_heading = get_field( 'banner_heading' );
		} elseif ( is_singular() ) {
			$vvg_heading = get_the_title();
		} elseif ( is_archive() ) {
			$vvg_heading = wp_strip_all_tags( get_the_archive_title() );
		} elseif ( is_search() ) {
			$vvg_heading = __( 'Search Results', 'siteorigin-corp' );
		} else {
			$vvg_heading = get_bloginfo( 'name' );
		}

		$vvg_sub = ( is_singular() && function_exists( 'get_field' ) && get_field( 'banner_content' ) ) ? get_field( 'banner_content' ) : '';
		?>
		<section class="vvg-page-banner"<?php echo $vvg_bg ? ' style="background-image:url(\'' . esc_url( $vvg_bg ) . '\');"' : ''; ?>>
			<div class="vvg-container">
				<div class="vvg-page-banner-grid">
					<div class="vvg-page-banner-text">
						<h1><?php echo esc_html( $vvg_heading ); ?></h1>
						<?php if ( $vvg_sub ) : ?>
							<div class="vvg-page-banner-sub"><?php echo wp_kses_post( $vvg_sub ); ?></div>
						<?php endif; ?>
						<?php if ( function_exists( 'custom_breadcrumbs' ) ) : ?>
							<div class="vvg-page-banner-crumbs"><?php custom_breadcrumbs(); ?></div>
						<?php endif; ?>
						<div class="vvg-page-banner-actions">
							<a class="vvg-btn" href="#vvg-banner-form"><?php esc_html_e( 'Book Your Consultation Here', 'siteorigin-corp' ); ?></a>
							<a class="vvg-btn vvg-btn-light" href="tel:<?php echo esc_attr( $vvg_phone_href ); ?>"><?php echo esc_html( sprintf( 'Call %s', $vvg_phone ) ); ?></a>
						</div>
					</div>
					<div class="vvg-form-card vvg-form-compact" id="vvg-banner-form">
						<h2><?php esc_html_e( 'Get In Touch With VV Glass', 'siteorigin-corp' ); ?></h2>
						<p class="vvg-form-sub"><?php esc_html_e( 'Fast, obligation-free quotes across Sydney & Wollongong', 'siteorigin-corp' ); ?></p>
						<?php
						if ( function_exists( 'vvg_enquiry_form' ) ) {
							echo vvg_enquiry_form(); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- CF7 output.
						}
						?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<div id="content" class="site-content">
		<div class="corp-container">
			<?php do_action( 'siteorigin_corp_content_top' ); ?>
