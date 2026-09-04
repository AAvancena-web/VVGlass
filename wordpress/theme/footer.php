<?php
/**
 * The theme footer — VV Glass redesign.
 *
 * Set VVG_REDESIGN to false in functions.php and this hands straight back to
 * footer-legacy.php, which is your original file renamed and untouched.
 *
 * The footer markup stays widget-driven, exactly as before, so your existing
 * footer widgets keep working — the redesign is applied in CSS.
 *
 * @package siteorigin-corp-child
 */

if ( function_exists( 'vvg_redesign_active' ) && ! vvg_redesign_active() ) {
	$vvg_legacy = get_theme_file_path( 'footer-legacy.php' );
	if ( file_exists( $vvg_legacy ) ) {
		require $vvg_legacy;
		return;
	}
}

$vvg_phone      = function_exists( 'vvg_contact' ) ? vvg_contact( 'phone' ) : '0412 991 904';
$vvg_phone_href = function_exists( 'vvg_tel_href' ) ? vvg_tel_href() : '+61412991904';
?>

		</div><!-- .corp-container -->
	</div><!-- #content -->

	<?php
	if ( siteorigin_page_setting( 'footer', true ) ) {
		do_action( 'siteorigin_corp_footer_before' );
		?>

		<footer id="colophon" class="site-footer vvg-footer">

			<?php do_action( 'siteorigin_corp_footer_top' ); ?>

			<?php if ( siteorigin_page_setting( 'footer_widgets' ) ) { ?>
				<div class="corp-container">
					<?php
					if ( is_active_sidebar( 'sidebar-footer' ) ) {
						$corp_sidebars = wp_get_sidebars_widgets();
						?>
						<div class="widgets widgets-<?php echo count( $corp_sidebars['sidebar-footer'] ); ?>" aria-label="<?php esc_attr_e( 'Footer Widgets', 'siteorigin-corp' ); ?>">
							<?php dynamic_sidebar( 'sidebar-footer' ); ?>
						</div>
						<?php
					}
					?>
				</div><!-- .corp-container -->
			<?php } ?>

			<div class="bottom-bar">
				<div class="corp-container">
					<div class="site-info">
						<?php
						siteorigin_corp_footer_text();

						if ( function_exists( 'the_privacy_policy_link' ) && siteorigin_setting( 'footer_privacy_policy_link' ) ) {
							the_privacy_policy_link( '<span>', '</span>' );
						}

						$credit_text = apply_filters(
							'siteorigin_corp_footer_credits',
							'<span>' . sprintf( esc_html__( ' %s', 'siteorigin-corp' ), '' ) . '</span>'
						);

						if ( ! empty( $credit_text ) ) {
							echo wp_kses_post( $credit_text );
						}
						?>
					</div><!-- .site-info -->
					<?php
					$widget = siteorigin_setting( 'footer_social_widget' );

					if ( has_nav_menu( 'menu-2' ) || ! empty( $widget['networks'] ) ) { ?>
						<div class="footer-menu">
							<?php wp_nav_menu( array( 'theme_location' => 'menu-2', 'depth' => 1, 'fallback_cb' => '' ) ); ?>
							<?php if ( ! empty( $widget['networks'] ) && class_exists( 'SiteOrigin_Widget_SocialMediaButtons_Widget' ) ) {
								the_widget( 'SiteOrigin_Widget_SocialMediaButtons_Widget', $widget );
							}
							?>
						</div><!-- .footer-menu -->
					<?php } ?>
				</div><!-- .corp-container -->
			</div><!-- .bottom-bar -->

			<?php do_action( 'siteorigin_corp_footer_bottom' ); ?>

		</footer><!-- #colophon -->
	<?php } ?>
</div><!-- #page -->

<?php if ( siteorigin_setting( 'navigation_scroll_to_top' ) ) { ?>
	<div id="scroll-to-top">
		<span class="screen-reader-text"><?php esc_html_e( 'Scroll to top', 'siteorigin-corp' ); ?></span>
		<?php siteorigin_corp_display_icon( 'up-arrow' ); ?>
	</div>
<?php } ?>

<!-- Floating call button, mobile only -->
<a class="vvg-call-now" href="tel:<?php echo esc_attr( $vvg_phone_href ); ?>"
	aria-label="<?php echo esc_attr( sprintf( 'Call %s', $vvg_phone ) ); ?>">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.9 15.9 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z"/></svg>
	<?php // Icon and number only — "Call Now" is what the phone icon already says. ?>
	<?php echo esc_html( $vvg_phone ); ?>
</a>

<?php wp_footer(); ?>
<?php do_action( 'siteorigin_corp_footer_after' ); ?>

<?php
/*
 * The two sticky-header scroll handlers that used to live here have been
 * removed — the redesigned header is sticky in CSS, so adding a .fixed class
 * on scroll did nothing but run jQuery on every scroll event.
 *
 * Everything below is unchanged and still needed: the homepage sliders, the
 * tel input filter, the CF7 redirect and the show-more toggle. These move into
 * vvg-redesign.js during the homepage port, once the sliders they drive are
 * replaced. Leaving them here for now means the homepage keeps working while
 * only the header and footer change.
 */
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
    $('.banner-slider').slick({
        dots: true, infinite: true, speed: 800, fade: true,
        autoplay: true, autoplaySpeed: 15000, cssEase: 'linear', arrows: false
    });
});
</script>

<script>
	jQuery(document).ready(function(){
		window.dispatchEvent(new Event('resize'));
	});
</script>

<script>
	jQuery(document).ready(function() {
  jQuery("input[type='tel']").bind({
    keydown: function(e) {
      if (e.shiftKey === true ) {
        if (e.which == 9) { return true; }
        return false;
      }
      if (e.which > 57) {
        if(e.which >=96 && e.which <= 105){ return true; }
        return false;
      }
      if (e.which==32) { return false; }
      return true;
    }
  });
});
</script>

<script>
document.addEventListener( 'wpcf7mailsent', function( event ) {
    setTimeout( () => { location = '/thank-you/'; }, 3000 );
}, false );
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('.review-slider2').slick({
			slidesToShow: 1, slidesToScroll: 1, touchMove: false, arrows: true,
			dots: true, infinite: true, autoplay: true, autoplaySpeed: 2000,
			responsive: [
				{ breakpoint: 993, settings: { slidesToShow: 1, slidesToScroll: 1 } },
				{ breakpoint: 768, settings: { slidesToShow: 1, slidesToScroll: 1 } },
				{ breakpoint: 520, settings: { slidesToShow: 1, slidesToScroll: 1 } }
			],
			appendDots: $('.slider_controls2 .slick-dots'),
			prevArrow: $('.slider_controls2 .slick-prev'),
			nextArrow: $('.slider_controls2 .slick-next')
		});
	});
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('.custom-service-slider').slick({
			slidesToShow: 3, slidesToScroll: 2, touchMove: true, arrows: true,
			dots: true, infinite: true, autoplay: true, autoplaySpeed: 2000,
			responsive: [
				{ breakpoint: 993, settings: { slidesToShow: 2, slidesToScroll: 2 } },
				{ breakpoint: 601, settings: { slidesToShow: 1, slidesToScroll: 2 } },
				{ breakpoint: 520, settings: { slidesToShow: 1, slidesToScroll: 1 } }
			],
			appendDots: $('.slider_controls3 .slick-dots'),
			prevArrow: $('.slider_controls3 .slick-prev'),
			nextArrow: $('.slider_controls3 .slick-next')
		});
	});
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('.installation-service-slider').slick({
			slidesToShow: 3, slidesToScroll: 3, touchMove: true, arrows: true,
			dots: true, infinite: true, autoplay: true, autoplaySpeed: 2000,
			responsive: [
				{ breakpoint: 993, settings: { slidesToShow: 2, slidesToScroll: 2 } },
				{ breakpoint: 601, settings: { slidesToShow: 1, slidesToScroll: 2 } },
				{ breakpoint: 520, settings: { slidesToShow: 1, slidesToScroll: 1 } }
			],
			appendDots: $('.slider_controls3 .slick-dots'),
			prevArrow: $('.slider_controls3 .slick-prev'),
			nextArrow: $('.slider_controls3 .slick-next')
		});
	});
</script>

<script>
jQuery(document).ready(function(){
  jQuery('.show-more-btn').click(function(){
    var target = jQuery(this).data('target');
    var textMore = jQuery(this).data('text-more');
    var textLess = jQuery(this).data('text-less');
    var $target = jQuery(target);

    $target.slideToggle(function(){
      if ($target.is(':visible')) {
        jQuery('.show-more-btn[data-target="' + target + '"]').text(textLess);
      } else {
        jQuery('.show-more-btn[data-target="' + target + '"]').text(textMore);
      }
    });
  });
});
</script>

</body>
</html>
