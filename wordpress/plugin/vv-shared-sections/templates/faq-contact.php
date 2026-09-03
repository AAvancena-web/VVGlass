<?php
/**
 * Front-end template for a Shared Section.
 *
 * Renders, in order and only when they hold content:
 *   1. Content  — long-form copy with an always-open question list
 *   2. Intro    — heading, body copy and a CTA
 *   3. FAQ      — collapsible question list
 *   4. Contact  — contact cards and a map
 *
 * Expects $post_id (required) and $bleed (optional bool) from the caller.
 * Nothing is rendered for blank ACF fields — empty fields output no markup,
 * and an empty block is skipped entirely.
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	return; // ACF not active — render nothing.
}

$bleed = ! empty( $bleed );

/**
 * Return a trimmed ACF value, or '' when blank.
 */
$vvss_get = function ( $name ) use ( $post_id ) {
	$val = get_field( $name, $post_id );
	if ( is_string( $val ) ) {
		return trim( $val );
	}
	return $val;
};

/**
 * Normalise a repeater into question/answer pairs, dropping empty rows.
 */
$vvss_pairs = function ( $rows ) {
	$out = array();
	if ( ! is_array( $rows ) ) {
		return $out;
	}
	foreach ( $rows as $item ) {
		$q = isset( $item['question'] ) ? trim( $item['question'] ) : '';
		$a = isset( $item['answer'] ) ? trim( $item['answer'] ) : '';
		if ( '' !== $q || '' !== $a ) {
			$out[] = array(
				'question' => $q,
				'answer'   => isset( $item['answer'] ) ? $item['answer'] : '',
			);
		}
	}
	return $out;
};

/* ---------------- Content block ---------------- */
$content_eyebrow    = $vvss_get( 'content_eyebrow' );
$content_heading    = $vvss_get( 'content_heading' );
$content_intro      = $vvss_get( 'content_intro' );
$content_qa_heading = $vvss_get( 'content_qa_heading' );
$content_questions  = $vvss_pairs( $vvss_get( 'content_questions' ) );
$content_outro      = $vvss_get( 'content_outro' );
$content_cta_label  = $vvss_get( 'content_cta_label' );
$content_cta_url    = $vvss_get( 'content_cta_url' );
$has_content_cta    = ( $content_cta_label && $content_cta_url );
$has_content        = ( $content_eyebrow || $content_heading || $content_intro || $content_questions || $content_outro );

/* ---------------- Intro content ---------------- */
$intro_eyebrow    = $vvss_get( 'intro_eyebrow' );
$intro_heading    = $vvss_get( 'intro_heading' );
$intro_subheading = $vvss_get( 'intro_subheading' );
$intro_body       = $vvss_get( 'intro_body' );
$intro_image      = $vvss_get( 'intro_image' );
$has_intro_split  = ( is_array( $intro_image ) && ! empty( $intro_image['url'] ) );
$cta_label        = $vvss_get( 'cta_label' );
$cta_url          = $vvss_get( 'cta_url' );
$has_cta          = ( $cta_label && $cta_url );
$has_intro        = ( $intro_eyebrow || $intro_heading || $intro_subheading || $intro_body || $has_cta || $has_intro_split );

/* ---------------- FAQ content ---------------- */
$faq_eyebrow    = $vvss_get( 'faq_eyebrow' );
$faq_heading    = $vvss_get( 'faq_heading' );
$faq_subheading = $vvss_get( 'faq_subheading' );
$faqs           = $vvss_pairs( $vvss_get( 'faqs' ) );
$faq_open_first = (bool) get_field( 'faq_open_first', $post_id );
$has_faq        = ! empty( $faqs );

/* ---------------- Contact content ---------------- */
$contact_eyebrow    = $vvss_get( 'contact_eyebrow' );
$contact_heading    = $vvss_get( 'contact_heading' );
$contact_subheading = $vvss_get( 'contact_subheading' );
$phone              = $vvss_get( 'phone' );
$phone_note         = $vvss_get( 'phone_note' );
$email              = $vvss_get( 'email' );
$email_note         = $vvss_get( 'email_note' );
$location_label     = $vvss_get( 'location_label' );
$location           = $vvss_get( 'location' );
$location_note      = $vvss_get( 'location_note' );
$map_query          = $vvss_get( 'map_query' );

// Build an AU-friendly tel: href (leading 0 -> +61).
$phone_href = preg_replace( '/[^0-9+]/', '', (string) $phone );
if ( '' !== $phone_href && '0' === substr( $phone_href, 0, 1 ) ) {
	$phone_href = '+61' . substr( $phone_href, 1 );
}

// Google Maps embed (no API key required); only when a location is provided.
$map_src = $map_query ? 'https://www.google.com/maps?q=' . rawurlencode( $map_query ) . '&z=12&output=embed' : '';

// Does the Contact block have anything worth showing?
$has_contact = ( $phone || $email || $location || $map_src );

// Nothing at all to render? Output nothing (no wrapper).
if ( ! $has_content && ! $has_intro && ! $has_faq && ! $has_contact ) {
	return;
}

// Split FAQs into two balanced columns.
$total = count( $faqs );
$half  = (int) ceil( $total / 2 );
$col1  = array_slice( $faqs, 0, $half );
$col2  = array_slice( $faqs, $half );

/**
 * Render a single FAQ column.
 */
$vvss_render_col = function ( $items, $open_first = false ) {
	if ( empty( $items ) ) {
		return;
	}
	echo '<div class="vvss-faq-col">';
	foreach ( $items as $i => $item ) {
		$open = ( $open_first && 0 === $i ) ? ' open' : '';
		echo '<details class="vvss-qa"' . $open . '>'; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Fixed literal.
		echo '<summary>' . esc_html( $item['question'] ) . '<span class="vvss-icon">+</span></summary>';
		if ( '' !== trim( (string) $item['answer'] ) ) {
			echo '<div class="vvss-answer">' . wp_kses_post( $item['answer'] ) . '</div>';
		}
		echo '</details>';
	}
	echo '</div>';
};

$wrapper_class = 'vv-shared-section' . ( $bleed ? ' vv-shared-section--bleed' : '' );
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">

	<?php if ( $has_content ) : ?>
	<!-- Content -->
	<section class="vvss-content-section">
		<div class="vvss-wrap">
			<?php if ( $content_eyebrow || $content_heading ) : ?>
			<div class="vvss-section-head">
				<?php if ( $content_eyebrow ) : ?><span class="vvss-eyebrow"><?php echo esc_html( $content_eyebrow ); ?></span><?php endif; ?>
				<?php if ( $content_heading ) : ?><h2><?php echo esc_html( $content_heading ); ?></h2><?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ( $content_intro ) : ?>
			<div class="vvss-copy-block"><?php echo wp_kses_post( $content_intro ); ?></div>
			<?php endif; ?>

			<?php if ( $content_questions ) : ?>
				<?php if ( $content_qa_heading ) : ?>
				<h2 class="vvss-qa-heading"><?php echo esc_html( $content_qa_heading ); ?></h2>
				<?php endif; ?>
				<div class="vvss-qa-grid">
					<?php foreach ( $content_questions as $item ) : ?>
					<div class="vvss-qa-item">
						<?php if ( '' !== $item['question'] ) : ?>
						<h3><?php echo esc_html( $item['question'] ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== trim( (string) $item['answer'] ) ) : ?>
						<div class="vvss-qa-answer"><?php echo wp_kses_post( $item['answer'] ); ?></div>
						<?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $content_outro ) : ?>
			<div class="vvss-copy-block vvss-copy-block--outro"><?php echo wp_kses_post( $content_outro ); ?></div>
			<?php endif; ?>

			<?php if ( $has_content_cta ) : ?>
			<div class="vvss-cta-wrap">
				<a class="vvss-btn" href="<?php echo esc_url( $content_cta_url ); ?>"><?php echo esc_html( $content_cta_label ); ?></a>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $has_intro ) : ?>
	<!-- Intro -->
	<section class="vvss-intro-section<?php echo $has_intro_split ? ' vvss-intro-split' : ''; ?>">
		<div class="vvss-wrap">
			<div class="vvss-intro-grid">
				<div class="vvss-intro-col">
					<?php if ( $intro_eyebrow || $intro_heading || $intro_subheading ) : ?>
					<div class="vvss-section-head">
						<?php if ( $intro_eyebrow ) : ?><span class="vvss-eyebrow"><?php echo esc_html( $intro_eyebrow ); ?></span><?php endif; ?>
						<?php if ( $intro_heading ) : ?><h2><?php echo esc_html( $intro_heading ); ?></h2><?php endif; ?>
						<?php if ( $intro_subheading ) : ?><p class="vvss-intro-sub"><?php echo esc_html( $intro_subheading ); ?></p><?php endif; ?>
					</div>
					<?php endif; ?>

					<?php if ( $intro_body ) : ?>
					<div class="vvss-intro-body"><?php echo wp_kses_post( $intro_body ); ?></div>
					<?php endif; ?>

					<?php if ( $has_cta ) : ?>
					<div class="vvss-cta-wrap">
						<a class="vvss-btn" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
					</div>
					<?php endif; ?>
				</div>

				<?php if ( $has_intro_split ) : ?>
				<div class="vvss-intro-media">
					<img src="<?php echo esc_url( $intro_image['url'] ); ?>"
						alt="<?php echo esc_attr( isset( $intro_image['alt'] ) ? $intro_image['alt'] : '' ); ?>"
						loading="lazy"
						<?php if ( ! empty( $intro_image['width'] ) ) : ?>width="<?php echo esc_attr( $intro_image['width'] ); ?>"<?php endif; ?>
						<?php if ( ! empty( $intro_image['height'] ) ) : ?>height="<?php echo esc_attr( $intro_image['height'] ); ?>"<?php endif; ?>>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $has_faq ) : ?>
	<!-- FAQ -->
	<section class="vvss-faq-section">
		<div class="vvss-wrap">
			<?php if ( $faq_eyebrow || $faq_heading || $faq_subheading ) : ?>
			<div class="vvss-section-head">
				<?php if ( $faq_eyebrow ) : ?><span class="vvss-eyebrow"><?php echo esc_html( $faq_eyebrow ); ?></span><?php endif; ?>
				<?php if ( $faq_heading ) : ?><h2><?php echo esc_html( $faq_heading ); ?></h2><?php endif; ?>
				<?php if ( $faq_subheading ) : ?><p><?php echo esc_html( $faq_subheading ); ?></p><?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="vvss-faq">
				<?php
				$vvss_render_col( $col1, $faq_open_first );
				$vvss_render_col( $col2, false );
				?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $has_contact ) : ?>
	<!-- Contact -->
	<section class="vvss-contact-section">
		<div class="vvss-wrap">
			<?php if ( $contact_eyebrow || $contact_heading || $contact_subheading ) : ?>
			<div class="vvss-section-head">
				<?php if ( $contact_eyebrow ) : ?><span class="vvss-eyebrow"><?php echo esc_html( $contact_eyebrow ); ?></span><?php endif; ?>
				<?php if ( $contact_heading ) : ?><h2><?php echo esc_html( $contact_heading ); ?></h2><?php endif; ?>
				<?php if ( $contact_subheading ) : ?><p><?php echo esc_html( $contact_subheading ); ?></p><?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="vvss-contact-grid">
				<div class="vvss-contact-cards">

					<?php if ( $phone ) : ?>
					<div class="vvss-info-card">
						<div class="vvss-ico">
							<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8a15.9 15.9 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z"/></svg>
						</div>
						<div>
							<h3>Phone</h3>
							<a class="vvss-big" href="tel:<?php echo esc_attr( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
							<?php if ( $phone_note ) : ?><p><?php echo esc_html( $phone_note ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $email ) : ?>
					<div class="vvss-info-card">
						<div class="vvss-ico">
							<svg class="vvss-ico-mail" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.6 7.4 8.4 5.9 8.4-5.9"/></svg>
						</div>
						<div>
							<h3>Email</h3>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
							<?php if ( $email_note ) : ?><p><?php echo esc_html( $email_note ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $location ) : ?>
					<div class="vvss-info-card">
						<div class="vvss-ico">
							<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
						</div>
						<div>
							<?php if ( $location_label ) : ?><h3><?php echo esc_html( $location_label ); ?></h3><?php endif; ?>
							<p class="vvss-strong"><?php echo esc_html( $location ); ?></p>
							<?php if ( $location_note ) : ?><p><?php echo esc_html( $location_note ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

				</div>

				<?php if ( $map_src ) : ?>
				<div class="vvss-map-panel">
					<iframe
						title="<?php echo esc_attr( $location ? $location : 'Location' ); ?> map"
						src="<?php echo esc_url( $map_src ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						allowfullscreen></iframe>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</div>
<?php
