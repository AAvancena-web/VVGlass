<?php
/**
 * Template Name: VV Glass Homepage
 *
 * The homepage, built from ACF fields rather than page-builder content, so the
 * markup matches the design exactly instead of being restyled into shape.
 *
 * Every section renders only when its fields hold something, so a half-filled
 * page degrades quietly rather than leaving empty shells behind.
 *
 * The contact block, FAQ and shared copy come from the VV Shared Sections
 * plugin, which outputs above the footer — they are deliberately not repeated
 * here.
 *
 * @package siteorigin-corp-child
 */

if ( ! function_exists( 'get_field' ) ) {
	// Without ACF there is nothing to render; fall back to the normal page.
	get_template_part( 'page' );
	return;
}

get_header();

$f = function ( $name ) {
	$v = get_field( $name );
	return is_string( $v ) ? trim( $v ) : $v;
};

/* ============================ HERO ============================ */
$hero_slides = $f( 'hero_slides' );
$hero_trust  = $f( 'hero_trust' );
$hero_head   = $f( 'hero_heading' );
$hero_text   = $f( 'hero_text' );
$hero_eyebrow = $f( 'hero_eyebrow' );

if ( $hero_head || $hero_slides ) : ?>
<section class="vvg-hero">
	<?php if ( $hero_slides ) : ?>
	<div class="vvg-hero-slides" aria-hidden="true">
		<?php foreach ( $hero_slides as $i => $row ) :
			if ( empty( $row['image']['url'] ) ) { continue; } ?>
			<div class="vvg-hero-slide<?php echo 0 === $i ? ' is-active' : ''; ?>">
				<img src="<?php echo esc_url( $row['image']['url'] ); ?>" alt=""
					<?php echo 0 === $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="vvg-container">
		<div class="vvg-hero-grid">
			<div class="vvg-hero-content">
				<?php if ( $hero_eyebrow ) : ?><span class="vvg-eyebrow vvg-eyebrow-dark"><?php echo esc_html( $hero_eyebrow ); ?></span><?php endif; ?>
				<?php if ( $hero_head ) : ?><h1><?php echo esc_html( $hero_head ); ?></h1><?php endif; ?>
				<?php if ( $hero_text ) : ?><p><?php echo esc_html( $hero_text ); ?></p><?php endif; ?>

				<div class="vvg-hero-actions">
					<?php
					echo vvg_cta( 'hero_cta' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper.
					if ( function_exists( 'vvg_contact' ) ) :
						?>
						<a class="vvg-btn vvg-btn-light" href="tel:<?php echo esc_attr( vvg_tel_href() ); ?>"><?php echo esc_html( sprintf( 'Call %s', vvg_contact( 'phone' ) ) ); ?></a>
					<?php endif; ?>
				</div>

				<?php if ( $hero_trust ) : ?>
				<ul class="vvg-hero-trust">
					<?php foreach ( $hero_trust as $row ) :
						if ( empty( $row['label'] ) ) { continue; } ?>
						<li><?php echo vvg_icon( isset( $row['icon'] ) ? $row['icon'] : 'star' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?><?php echo esc_html( $row['label'] ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

			<div class="vvg-form-card vvg-form-compact">
				<?php if ( $f( 'hero_form_heading' ) ) : ?><h2><?php echo esc_html( $f( 'hero_form_heading' ) ); ?></h2><?php endif; ?>
				<?php if ( $f( 'hero_form_sub' ) ) : ?><p class="vvg-form-sub"><?php echo esc_html( $f( 'hero_form_sub' ) ); ?></p><?php endif; ?>
				<?php echo function_exists( 'vvg_enquiry_form' ) ? vvg_enquiry_form() : ''; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- CF7 output. ?>
			</div>
		</div>
	</div>

	<?php if ( is_array( $hero_slides ) && count( $hero_slides ) > 1 ) : ?>
	<div class="vvg-hero-dots">
		<?php foreach ( $hero_slides as $i => $row ) : ?>
			<button type="button"<?php echo 0 === $i ? ' class="is-active"' : ''; ?> aria-label="<?php echo esc_attr( sprintf( 'Show slide %d', $i + 1 ) ); ?>"></button>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<?php /* ============================ WHY CHOOSE US ============================ */
$why_points = $f( 'why_points' );
$why_image  = $f( 'why_image' );
if ( $f( 'why_heading' ) || $why_points ) : ?>
<section class="vvg-section">
	<div class="vvg-container">
		<div class="vvg-split">
			<?php if ( $why_image && ! empty( $why_image['url'] ) ) : ?>
			<div class="vvg-split-media vvg-split-media-first">
				<img src="<?php echo esc_url( $why_image['url'] ); ?>" alt="<?php echo esc_attr( $why_image['alt'] ?? '' ); ?>" loading="lazy">
			</div>
			<?php endif; ?>
			<div class="vvg-split-copy">
				<?php if ( $f( 'why_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'why_eyebrow' ) ); ?></span><?php endif; ?>
				<?php if ( $f( 'why_heading' ) ) : ?><h2><?php echo esc_html( $f( 'why_heading' ) ); ?></h2><?php endif; ?>
				<?php if ( $why_points ) : ?>
				<div class="vvg-features">
					<?php foreach ( $why_points as $row ) : ?>
					<div class="vvg-feature">
						<div class="vvg-feature-ico"><?php echo vvg_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?></div>
						<div>
							<?php if ( ! empty( $row['title'] ) ) : ?><h3><?php echo esc_html( $row['title'] ); ?></h3><?php endif; ?>
							<?php if ( ! empty( $row['text'] ) ) : ?><p><?php echo esc_html( $row['text'] ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php echo vvg_cta( 'why_cta' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ SERVICES ============================ */
$services = $f( 'services_items' );
if ( $services ) : ?>
<section class="vvg-section vvg-section-cream">
	<div class="vvg-container">
		<div class="vvg-section-head vvg-center">
			<?php if ( $f( 'services_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'services_eyebrow' ) ); ?></span><?php endif; ?>
			<?php if ( $f( 'services_heading' ) ) : ?><h2><?php echo esc_html( $f( 'services_heading' ) ); ?></h2><?php endif; ?>
		</div>
		<div class="vvg-cards">
			<?php foreach ( $services as $row ) : ?>
			<article class="vvg-card">
				<?php if ( ! empty( $row['image']['url'] ) ) : ?>
				<div class="vvg-card-media"><img src="<?php echo esc_url( $row['image']['url'] ); ?>" alt="<?php echo esc_attr( $row['title'] ?? '' ); ?>" loading="lazy"></div>
				<?php endif; ?>
				<div class="vvg-card-body">
					<?php if ( ! empty( $row['title'] ) ) : ?>
					<h3><?php if ( ! empty( $row['url'] ) ) : ?><a href="<?php echo esc_url( $row['url'] ); ?>"><?php echo esc_html( $row['title'] ); ?></a><?php else : echo esc_html( $row['title'] ); endif; ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $row['text'] ) ) : ?><p><?php echo esc_html( $row['text'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $row['url'] ) ) : ?>
					<a class="vvg-card-link" href="<?php echo esc_url( $row['url'] ); ?>">Learn more <?php echo vvg_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?></a>
					<?php endif; ?>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
		<?php $cta = vvg_cta( 'services_cta' ); if ( $cta ) : ?><div class="vvg-section-cta"><?php echo $cta; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?></div><?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ PROJECTS ============================ */
$gallery = $f( 'projects_gallery' );
if ( $gallery ) : ?>
<section class="vvg-section">
	<div class="vvg-container">
		<div class="vvg-section-head vvg-center">
			<?php if ( $f( 'projects_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'projects_eyebrow' ) ); ?></span><?php endif; ?>
			<?php if ( $f( 'projects_heading' ) ) : ?><h2><?php echo esc_html( $f( 'projects_heading' ) ); ?></h2><?php endif; ?>
		</div>
		<div class="vvg-projects">
			<?php foreach ( $gallery as $i => $img ) : ?>
			<button type="button" class="vvg-project" aria-label="<?php echo esc_attr( sprintf( 'Open project image %d', $i + 1 ) ); ?>">
				<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>" loading="lazy">
			</button>
			<?php endforeach; ?>
		</div>
		<?php $cta = vvg_cta( 'projects_cta' ); if ( $cta ) : ?><div class="vvg-section-cta"><?php echo $cta; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?></div><?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ GLAZING PARTNER BAND ============================ */
$band_items = $f( 'band_items' );
if ( $f( 'band_heading' ) || $band_items ) : ?>
<section class="vvg-section vvg-band">
	<div class="vvg-container">
		<div class="vvg-section-head vvg-center">
			<?php if ( $f( 'band_heading' ) ) : ?><h2><?php echo esc_html( $f( 'band_heading' ) ); ?></h2><?php endif; ?>
			<?php if ( $f( 'band_text' ) ) : ?><p><?php echo esc_html( $f( 'band_text' ) ); ?></p><?php endif; ?>
		</div>
		<?php if ( $band_items ) : ?>
		<div class="vvg-band-grid">
			<?php foreach ( $band_items as $row ) : ?>
			<div class="vvg-band-item">
				<div class="vvg-band-ico"><?php echo vvg_icon( $row['icon'] ?? 'bolt' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?></div>
				<?php if ( ! empty( $row['title'] ) ) : ?><h3><?php echo esc_html( $row['title'] ); ?></h3><?php endif; ?>
				<?php if ( ! empty( $row['text'] ) ) : ?><p><?php echo esc_html( $row['text'] ); ?></p><?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<?php $cta = vvg_cta( 'band_cta', 'vvg-btn vvg-btn-light' ); if ( $cta ) : ?><div class="vvg-section-cta"><?php echo $cta; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?></div><?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ REVIEW ============================ */
if ( $f( 'review_quote' ) ) : ?>
<section class="vvg-section vvg-section-cream">
	<div class="vvg-container">
		<div class="vvg-section-head vvg-center">
			<?php if ( $f( 'review_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'review_eyebrow' ) ); ?></span><?php endif; ?>
			<?php if ( $f( 'review_heading' ) ) : ?><h2><?php echo esc_html( $f( 'review_heading' ) ); ?></h2><?php endif; ?>
			<?php if ( $f( 'review_sub' ) ) : ?><p><?php echo esc_html( $f( 'review_sub' ) ); ?></p><?php endif; ?>
		</div>
		<figure class="vvg-review">
			<span class="vvg-review-mark" aria-hidden="true">&ldquo;</span>
			<div class="vvg-stars" aria-label="5 out of 5 stars">
				<?php for ( $i = 0; $i < 5; $i++ ) { echo vvg_icon( 'star' ); } // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?>
			</div>
			<blockquote><?php echo esc_html( $f( 'review_quote' ) ); ?></blockquote>
			<?php if ( $f( 'review_name' ) ) : ?>
			<figcaption class="vvg-review-name">
				<?php echo esc_html( $f( 'review_name' ) ); ?>
				<?php if ( $f( 'review_location' ) ) : ?><span><?php echo esc_html( $f( 'review_location' ) ); ?></span><?php endif; ?>
			</figcaption>
			<?php endif; ?>
		</figure>
		<?php $cta = vvg_cta( 'review_cta' ); if ( $cta ) : ?><div class="vvg-section-cta"><?php echo $cta; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?></div><?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ ABOUT ============================ */
$about_image = $f( 'about_image' );
if ( $f( 'about_heading' ) || $f( 'about_body' ) ) : ?>
<section class="vvg-section">
	<div class="vvg-container">
		<div class="vvg-split">
			<div class="vvg-split-copy">
				<?php if ( $f( 'about_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'about_eyebrow' ) ); ?></span><?php endif; ?>
				<?php if ( $f( 'about_heading' ) ) : ?><h2><?php echo esc_html( $f( 'about_heading' ) ); ?></h2><?php endif; ?>
				<?php if ( $f( 'about_body' ) ) : ?><div class="vvg-rte"><?php echo wp_kses_post( $f( 'about_body' ) ); ?></div><?php endif; ?>
				<?php echo vvg_cta( 'about_cta' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?>
			</div>
			<?php if ( $about_image && ! empty( $about_image['url'] ) ) : ?>
			<div class="vvg-split-media">
				<img src="<?php echo esc_url( $about_image['url'] ); ?>" alt="<?php echo esc_attr( $about_image['alt'] ?? '' ); ?>" loading="lazy">
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============================ PILLARS ============================ */
$pillars = $f( 'pillars_items' );
if ( $pillars ) : ?>
<section class="vvg-section vvg-section-sand">
	<div class="vvg-container">
		<div class="vvg-section-head vvg-center">
			<?php if ( $f( 'pillars_eyebrow' ) ) : ?><span class="vvg-eyebrow"><?php echo esc_html( $f( 'pillars_eyebrow' ) ); ?></span><?php endif; ?>
			<?php if ( $f( 'pillars_heading' ) ) : ?><h2><?php echo esc_html( $f( 'pillars_heading' ) ); ?></h2><?php endif; ?>
			<?php if ( $f( 'pillars_text' ) ) : ?><p><?php echo esc_html( $f( 'pillars_text' ) ); ?></p><?php endif; ?>
		</div>
		<div class="vvg-pillars">
			<?php foreach ( $pillars as $row ) : ?>
			<div class="vvg-pillar">
				<div class="vvg-pillar-ico"><?php echo vvg_icon( $row['icon'] ?? 'clipboard' ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed path data. ?></div>
				<?php if ( ! empty( $row['title'] ) ) : ?><h3><?php echo esc_html( $row['title'] ); ?></h3><?php endif; ?>
				<?php if ( ! empty( $row['text'] ) ) : ?><p><?php echo esc_html( $row['text'] ); ?></p><?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php $cta = vvg_cta( 'pillars_cta' ); if ( $cta ) : ?><div class="vvg-section-cta"><?php echo $cta; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in helper. ?></div><?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php
/*
 * The shared sections plugin renders the Word-document copy, the FAQ and the
 * contact block above the footer, so nothing more is needed here.
 */
get_footer();
