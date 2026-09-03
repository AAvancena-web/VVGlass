<?php
/**
 * ACF fields for the custom homepage template.
 *
 * Registered in code, so the fields appear as soon as a page is assigned the
 * "VV Glass Homepage" template — nothing to build by hand, and nothing that
 * can be lost in a database migration.
 *
 * @package siteorigin-corp-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'vvg_register_home_fields' );
function vvg_register_home_fields() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/**
	 * Small helpers to keep the field definitions readable.
	 */
	$tab  = function ( $key, $label ) {
		return array( 'key' => "field_vvg_tab_{$key}", 'label' => $label, 'type' => 'tab' );
	};
	$text = function ( $name, $label, $default = '', $instructions = '' ) {
		return array(
			'key'           => "field_vvg_{$name}",
			'label'         => $label,
			'name'          => $name,
			'type'          => 'text',
			'default_value' => $default,
			'instructions'  => $instructions,
		);
	};
	$area = function ( $name, $label, $default = '', $rows = 3 ) {
		return array(
			'key'           => "field_vvg_{$name}",
			'label'         => $label,
			'name'          => $name,
			'type'          => 'textarea',
			'rows'          => $rows,
			'new_lines'     => '',
			'default_value' => $default,
		);
	};
	$wysiwyg = function ( $name, $label ) {
		return array(
			'key'          => "field_vvg_{$name}",
			'label'        => $label,
			'name'         => $name,
			'type'         => 'wysiwyg',
			'tabs'         => 'all',
			'media_upload' => 0,
		);
	};
	$image = function ( $name, $label, $instructions = '' ) {
		return array(
			'key'           => "field_vvg_{$name}",
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'array',
			'preview_size'  => 'medium',
			'instructions'  => $instructions,
		);
	};
	$link = function ( $name, $label, $default_label = 'Book Your Consultation Here', $default_url = '#contact' ) {
		return array(
			array(
				'key'           => "field_vvg_{$name}_label",
				'label'         => "{$label} — Button Label",
				'name'          => "{$name}_label",
				'type'          => 'text',
				'default_value' => $default_label,
			),
			array(
				'key'           => "field_vvg_{$name}_url",
				'label'         => "{$label} — Button URL",
				'name'          => "{$name}_url",
				'type'          => 'text',
				'default_value' => $default_url,
				'instructions'  => 'Use #contact to scroll to the contact form, or a full URL.',
			),
		);
	};
	$repeater = function ( $name, $label, $subs, $button = 'Add Row', $instructions = '' ) {
		return array(
			'key'          => "field_vvg_{$name}",
			'label'        => $label,
			'name'         => $name,
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => $button,
			'instructions' => $instructions,
			'sub_fields'   => $subs,
		);
	};
	$sub = function ( $key, $name, $label, $type = 'text', $extra = array() ) {
		return array_merge(
			array( 'key' => "field_vvg_sub_{$key}", 'label' => $label, 'name' => $name, 'type' => $type ),
			$extra
		);
	};

	$fields = array();

	/* ---------------- Hero ---------------- */
	$fields[] = $tab( 'hero', 'Hero' );
	$fields[] = $text( 'hero_eyebrow', 'Eyebrow', 'Australian Window Solutions' );
	$fields[] = $text( 'hero_heading', 'Heading', 'VV Glass – When Quality Matters' );
	$fields[] = $area( 'hero_text', 'Paragraph', 'The Trusted Australian Window Solutions for Glass Glazing, Installation & Repair Servicing Sydney, Wollongong & Surrounds' );
	$fields   = array_merge( $fields, $link( 'hero_cta', 'Hero' ) );
	$fields[] = $repeater(
		'hero_slides',
		'Background Slides',
		array( $sub( 'slide_image', 'image', 'Image', 'image', array( 'return_format' => 'array', 'preview_size' => 'medium' ) ) ),
		'Add Slide',
		'Rotates behind the hero. One image is fine.'
	);
	$fields[] = $repeater(
		'hero_trust',
		'Trust Row',
		array(
			$sub( 'trust_icon', 'icon', 'Icon', 'select', array(
				'choices' => array( 'shield' => 'Shield', 'clock' => 'Clock', 'star' => 'Star', 'bolt' => 'Lightning' ),
				'default_value' => 'shield',
			) ),
			$sub( 'trust_label', 'label', 'Label' ),
		),
		'Add Item'
	);
	$fields[] = $text( 'hero_form_heading', 'Form Heading', 'Get In Touch With VV Glass' );
	$fields[] = $text( 'hero_form_sub', 'Form Subheading', 'Fast, obligation-free quotes across Sydney & Wollongong' );

	/* ---------------- Why Choose Us ---------------- */
	$fields[] = $tab( 'why', 'Why Choose Us' );
	$fields[] = $text( 'why_eyebrow', 'Eyebrow', 'Why Choose Us' );
	$fields[] = $text( 'why_heading', 'Heading', 'Why Choose VV Glass for Your Australian Window Solutions ?' );
	$fields[] = $image( 'why_image', 'Image', 'Sits on the left on desktop, below the copy on mobile.' );
	$fields[] = $repeater(
		'why_points',
		'Points',
		array( $sub( 'point_title', 'title', 'Title' ), $sub( 'point_text', 'text', 'Text', 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ) ),
		'Add Point'
	);
	$fields = array_merge( $fields, $link( 'why_cta', 'Why Choose Us' ) );

	/* ---------------- Services ---------------- */
	$fields[] = $tab( 'services', 'Services' );
	$fields[] = $text( 'services_eyebrow', 'Eyebrow', 'Our Services' );
	$fields[] = $text( 'services_heading', 'Heading', 'Our Glass and Glazing Services' );
	$fields[] = $repeater(
		'services_items',
		'Services',
		array(
			$sub( 'svc_image', 'image', 'Image', 'image', array( 'return_format' => 'array', 'preview_size' => 'medium' ) ),
			$sub( 'svc_title', 'title', 'Title' ),
			$sub( 'svc_url', 'url', 'Link URL' ),
			$sub( 'svc_text', 'text', 'Text', 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
		),
		'Add Service'
	);
	$fields = array_merge( $fields, $link( 'services_cta', 'Services' ) );

	/* ---------------- Projects ---------------- */
	$fields[] = $tab( 'projects', 'Projects' );
	$fields[] = $text( 'projects_eyebrow', 'Eyebrow', 'Our Recent Projects' );
	$fields[] = $text( 'projects_heading', 'Heading', 'Where Quality Meets Clarity & Craftsmanship' );
	$fields[] = array(
		'key'           => 'field_vvg_projects_gallery',
		'label'         => 'Images',
		'name'          => 'projects_gallery',
		'type'          => 'gallery',
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'instructions'  => 'Shown two across. Clicking one opens the lightbox.',
	);
	$fields = array_merge( $fields, $link( 'projects_cta', 'Projects' ) );

	/* ---------------- Glazing partner band ---------------- */
	$fields[] = $tab( 'band', 'Glazing Partner' );
	$fields[] = $text( 'band_heading', 'Heading', 'Your Reliable Glass Glazing Partner' );
	$fields[] = $area( 'band_text', 'Intro Paragraph', '', 4 );
	$fields[] = $repeater(
		'band_items',
		'Cards',
		array(
			$sub( 'band_icon', 'icon', 'Icon', 'select', array(
				'choices' => array( 'bolt' => 'Lightning', 'grid' => 'Window', 'shield' => 'Shield' ),
				'default_value' => 'bolt',
			) ),
			$sub( 'band_title', 'title', 'Title' ),
			$sub( 'band_body', 'text', 'Text', 'textarea', array( 'rows' => 3, 'new_lines' => '' ) ),
		),
		'Add Card'
	);
	$fields = array_merge( $fields, $link( 'band_cta', 'Glazing Partner' ) );

	/* ---------------- Review ---------------- */
	$fields[] = $tab( 'review', 'Review' );
	$fields[] = $text( 'review_eyebrow', 'Eyebrow', 'Customer Review' );
	$fields[] = $text( 'review_heading', 'Heading', 'What our Customer Says' );
	$fields[] = $text( 'review_sub', 'Subheading', 'Our Award Winning Renovation Reviews' );
	$fields[] = $area( 'review_quote', 'Quote', '', 3 );
	$fields[] = $text( 'review_name', 'Name' );
	$fields[] = $text( 'review_location', 'Location' );
	$fields = array_merge( $fields, $link( 'review_cta', 'Review' ) );

	/* ---------------- About ---------------- */
	$fields[] = $tab( 'about', 'About' );
	$fields[] = $text( 'about_eyebrow', 'Eyebrow', 'About Us' );
	$fields[] = $text( 'about_heading', 'Heading', 'Clear Australian Window Solutions for Every Pane' );
	$fields[] = $wysiwyg( 'about_body', 'Body' );
	$fields[] = $image( 'about_image', 'Image', 'Sits on the right on desktop, below the copy on mobile.' );
	$fields = array_merge( $fields, $link( 'about_cta', 'About' ) );

	/* ---------------- Pillars ---------------- */
	$fields[] = $tab( 'pillars', 'How We Work' );
	$fields[] = $text( 'pillars_eyebrow', 'Eyebrow', 'How We Work' );
	$fields[] = $text( 'pillars_heading', 'Heading', 'A Simpler Way to Manage Your Glazing Project' );
	$fields[] = $area( 'pillars_text', 'Intro Paragraph', 'From the first phone call to the final clean-up, we keep your project organised, accountable and easy to follow.' );
	$fields[] = $repeater(
		'pillars_items',
		'Cards',
		array(
			$sub( 'pillar_icon', 'icon', 'Icon', 'select', array(
				'choices' => array( 'clipboard' => 'Clipboard', 'person' => 'Person', 'chat' => 'Chat' ),
				'default_value' => 'clipboard',
			) ),
			$sub( 'pillar_title', 'title', 'Title' ),
			$sub( 'pillar_body', 'text', 'Text', 'textarea', array( 'rows' => 4, 'new_lines' => '' ) ),
		),
		'Add Card'
	);
	$fields = array_merge( $fields, $link( 'pillars_cta', 'How We Work' ) );

	acf_add_local_field_group(
		array(
			'key'        => 'group_vvg_home',
			'title'      => 'Homepage Sections',
			'location'   => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'template-home.php',
					),
				),
			),
			'menu_order' => 0,
			'position'   => 'normal',
			'style'      => 'default',
			'active'     => true,
			'fields'     => $fields,
			/*
			 * The template supplies the whole page, so WPBakery's editor and the
			 * default content box would only cause confusion here.
			 */
			'hide_on_screen' => array( 'the_content' ),
		)
	);
}
