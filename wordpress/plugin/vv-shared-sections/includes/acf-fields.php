<?php
/**
 * ACF field groups for the Shared Section post type.
 *
 * Registered in code so no manual field building is required — as soon as
 * ACF PRO is active, the fields appear on the "Shared Section" edit screen.
 *
 * All field keys from v1.0 are preserved, so existing content is untouched.
 * New in v1.1: the "Content" tab (open copy + question list) and a per-page
 * override field group on pages.
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'vvss_register_acf_fields' );
function vvss_register_acf_fields() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_vvss_shared_section',
			'title'    => 'Shared Section Content',
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'shared_section',
					),
				),
			),
			'menu_order' => 0,
			'position'   => 'normal',
			'style'      => 'default',
			'active'     => true,
			'fields'     => array(

				/* ---------------- Display ---------------- */
				array(
					'key'   => 'field_vvss_display_tab',
					'label' => 'Display',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_vvss_show_above_footer',
					'label'         => 'Show this section',
					'name'          => 'show_above_footer',
					'type'          => 'true_false',
					'instructions'  => 'When on, this section is output automatically just above the footer. Which pages it appears on is decided by the <strong>Page Group</strong> box in the sidebar. You can also place it manually anywhere with the [shared_section] shortcode.',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'     => 'field_vvss_scope_note',
					'label'   => 'How Page Groups work',
					'type'    => 'message',
					'message' => "<strong>Leave Page Group empty</strong> and this section appears on every page. That is where the intro, FAQ and contact details belong.\n\n<strong>Tick one or more Page Groups</strong> and it appears only on pages tagged with the same group. That is where copy that changes between service types belongs.\n\nSections with a Page Group always render <strong>above</strong> the site-wide ones, so the varying content sits on top of the global intro, FAQ and contact block. No need to set anything. <strong>Order</strong> (Page Attributes) only sorts sections within the same band.",
					'new_lines' => 'wpautop',
					'esc_html'  => 0,
				),

				/* ---------------- Content (open copy + questions) ---------------- */
				array(
					'key'   => 'field_vvss_content_tab',
					'label' => 'Content',
					'type'  => 'tab',
				),
				array(
					'key'     => 'field_vvss_content_note',
					'label'   => '',
					'type'    => 'message',
					'message' => 'Long-form copy with an optional list of questions. Unlike the FAQ tab, these questions are always <strong>shown open</strong> — nothing is collapsed. Leave the whole tab blank to skip this block.',
					'esc_html' => 0,
				),
				array(
					'key'   => 'field_vvss_content_eyebrow',
					'label' => 'Eyebrow',
					'name'  => 'content_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_vvss_content_heading',
					'label' => 'Heading',
					'name'  => 'content_heading',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_vvss_content_intro',
					'label'        => 'Opening Copy',
					'name'         => 'content_intro',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'media_upload' => 0,
				),
				array(
					'key'           => 'field_vvss_content_qa_heading',
					'label'         => 'Questions Heading',
					'name'          => 'content_qa_heading',
					'type'          => 'text',
					'instructions'  => 'Sits above the question list. Leave blank to hide it.',
					'default_value' => 'Questions We Get Asked Often',
				),
				array(
					'key'          => 'field_vvss_content_questions',
					'label'        => 'Questions',
					'name'         => 'content_questions',
					'type'         => 'repeater',
					'instructions' => 'Shown open in two columns, in this order.',
					'layout'       => 'block',
					'button_label' => 'Add Question',
					'sub_fields'   => array(
						array(
							'key'   => 'field_vvss_content_question',
							'label' => 'Question',
							'name'  => 'question',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_vvss_content_answer',
							'label'        => 'Answer',
							'name'         => 'answer',
							'type'         => 'wysiwyg',
							'tabs'         => 'visual',
							'media_upload' => 0,
							'toolbar'      => 'basic',
						),
					),
				),
				array(
					'key'          => 'field_vvss_content_outro',
					'label'        => 'Closing Copy',
					'name'         => 'content_outro',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'media_upload' => 0,
				),
				array(
					'key'           => 'field_vvss_content_cta_label',
					'label'         => 'CTA Button Label',
					'name'          => 'content_cta_label',
					'type'          => 'text',
					'default_value' => 'Book Your Consultation Here',
				),
				array(
					'key'          => 'field_vvss_content_cta_url',
					'label'        => 'CTA Button URL',
					'name'         => 'content_cta_url',
					'type'         => 'url',
					'instructions' => 'Leave blank to hide the button.',
				),

				/* ---------------- Intro ---------------- */
				array(
					'key'   => 'field_vvss_intro_tab',
					'label' => 'Intro',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_vvss_intro_eyebrow',
					'label'         => 'Intro Eyebrow',
					'name'          => 'intro_eyebrow',
					'type'          => 'text',
					'default_value' => 'Why VV Glass',
				),
				array(
					'key'           => 'field_vvss_intro_heading',
					'label'         => 'Intro Heading',
					'name'          => 'intro_heading',
					'type'          => 'text',
					'default_value' => 'Glass That Works Harder for Your Property',
				),
				array(
					'key'           => 'field_vvss_intro_subheading',
					'label'         => 'Intro Subheading',
					'name'          => 'intro_subheading',
					'type'          => 'text',
					'default_value' => 'Built for the Way Your Space Is Used Every Day',
				),
				array(
					'key'          => 'field_vvss_intro_body',
					'label'        => 'Intro Body',
					'name'         => 'intro_body',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'media_upload' => 0,
				),
				array(
					'key'           => 'field_vvss_intro_image',
					'label'         => 'Intro Image',
					'name'          => 'intro_image',
					'type'          => 'image',
					'instructions'  => 'Optional. Add one and the intro switches to copy on the left with the image on the right. Leave empty and the copy stays centred full width.',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_vvss_cta_label',
					'label'         => 'CTA Button Label',
					'name'          => 'cta_label',
					'type'          => 'text',
					'default_value' => 'Book Your Consultation Here',
				),
				array(
					'key'          => 'field_vvss_cta_url',
					'label'        => 'CTA Button URL',
					'name'         => 'cta_url',
					'type'         => 'url',
					'instructions' => 'Where the button links, e.g. /contact/ or https://vvglass.com.au/contact/. Leave blank to hide the button.',
				),

				/* ---------------- FAQ ---------------- */
				array(
					'key'   => 'field_vvss_faq_tab',
					'label' => 'FAQ',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_vvss_faq_eyebrow',
					'label'         => 'FAQ Eyebrow',
					'name'          => 'faq_eyebrow',
					'type'          => 'text',
					'default_value' => 'Got Questions?',
				),
				array(
					'key'           => 'field_vvss_faq_heading',
					'label'         => 'FAQ Heading',
					'name'          => 'faq_heading',
					'type'          => 'text',
					'default_value' => 'Frequently Asked Questions',
				),
				array(
					'key'           => 'field_vvss_faq_subheading',
					'label'         => 'FAQ Subheading',
					'name'          => 'faq_subheading',
					'type'          => 'text',
					'default_value' => "Everything you need to know about our glazing services. Can't find your answer? Get in touch below.",
				),
				array(
					'key'           => 'field_vvss_faq_open_first',
					'label'         => 'Open the first question by default',
					'name'          => 'faq_open_first',
					'type'          => 'true_false',
					'instructions'  => 'Off means visitors see the full list of questions with every answer collapsed.',
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'          => 'field_vvss_faqs',
					'label'        => 'FAQ Items',
					'name'         => 'faqs',
					'type'         => 'repeater',
					'instructions' => 'Add your questions and answers. They are split evenly into two columns (e.g. 8 items = 4 left / 4 right).',
					'layout'       => 'block',
					'button_label' => 'Add FAQ',
					'sub_fields'   => array(
						array(
							'key'   => 'field_vvss_faq_question',
							'label' => 'Question',
							'name'  => 'question',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_vvss_faq_answer',
							'label'        => 'Answer',
							'name'         => 'answer',
							'type'         => 'wysiwyg',
							'tabs'         => 'visual',
							'media_upload' => 0,
							'toolbar'      => 'basic',
						),
					),
				),

				/* ---------------- Contact ---------------- */
				array(
					'key'   => 'field_vvss_contact_tab',
					'label' => 'Contact',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_vvss_contact_eyebrow',
					'label'         => 'Contact Eyebrow',
					'name'          => 'contact_eyebrow',
					'type'          => 'text',
					'default_value' => 'Get In Touch',
				),
				array(
					'key'           => 'field_vvss_contact_heading',
					'label'         => 'Contact Heading',
					'name'          => 'contact_heading',
					'type'          => 'text',
					'default_value' => 'Contact VV Glass',
				),
				array(
					'key'           => 'field_vvss_contact_subheading',
					'label'         => 'Contact Subheading',
					'name'          => 'contact_subheading',
					'type'          => 'text',
					'default_value' => "Reach out for a free quote or fast emergency response. We're here to help across Sydney & Wollongong.",
				),
				array(
					'key'           => 'field_vvss_contact_form_heading',
					'label'         => 'Form Heading',
					'name'          => 'contact_form_heading',
					'type'          => 'text',
					'default_value' => 'Get In Touch with VV Glass',
				),
				array(
					'key'           => 'field_vvss_contact_form_sub',
					'label'         => 'Form Subheading',
					'name'          => 'contact_form_sub',
					'type'          => 'text',
					'default_value' => "Tell us about your project and we'll come back with clear, practical advice.",
				),
				array(
					'key'          => 'field_vvss_contact_form_shortcode',
					'label'        => 'Form Shortcode',
					'name'         => 'contact_form_shortcode',
					'type'         => 'text',
					'instructions' => 'The Contact Form 7 shortcode for the enquiry form, e.g. <code>[contact-form-7 id="123"]</code>. Leave blank and the theme\'s enquiry form is used if it is available. With no form at all, the map moves back beside the contact cards.',
				),
				array(
					'key'           => 'field_vvss_phone',
					'label'         => 'Phone',
					'name'          => 'phone',
					'type'          => 'text',
					'default_value' => '0412 991 904',
				),
				array(
					'key'           => 'field_vvss_phone_note',
					'label'         => 'Phone Note',
					'name'          => 'phone_note',
					'type'          => 'text',
					'default_value' => 'Available 24/7 for bookings, quotes & emergencies.',
				),
				array(
					'key'           => 'field_vvss_email',
					'label'         => 'Email',
					'name'          => 'email',
					'type'          => 'email',
					'default_value' => 'damian@vvglass.com.au',
				),
				array(
					'key'           => 'field_vvss_email_note',
					'label'         => 'Email Note',
					'name'          => 'email_note',
					'type'          => 'text',
					'default_value' => 'We reply to enquiries within one business day.',
				),
				array(
					'key'           => 'field_vvss_location_label',
					'label'         => 'Location Label',
					'name'          => 'location_label',
					'type'          => 'text',
					'default_value' => 'Based In',
				),
				array(
					'key'           => 'field_vvss_location',
					'label'         => 'Location',
					'name'          => 'location',
					'type'          => 'text',
					'default_value' => 'Engadine, NSW 2233',
				),
				array(
					'key'           => 'field_vvss_location_note',
					'label'         => 'Location Note',
					'name'          => 'location_note',
					'type'          => 'textarea',
					'rows'          => 2,
					'new_lines'     => '',
					'default_value' => 'Servicing Sydney & Wollongong: CBD, North Shore, Eastern Suburbs, Sutherland Shire, Illawarra',
				),
				array(
					'key'           => 'field_vvss_map_query',
					'label'         => 'Map Location',
					'name'          => 'map_query',
					'type'          => 'text',
					'instructions'  => 'Address or place used to build the Google Maps embed, e.g. "Engadine NSW 2233 Australia". Leave blank to hide the map.',
					'default_value' => 'Engadine NSW 2233 Australia',
				),
			),
		)
	);

	/* ------------------------------------------------------------------
	 * Per-page override.
	 * ------------------------------------------------------------------ */
	acf_add_local_field_group(
		array(
			'key'      => 'group_vvss_page_override',
			'title'    => 'Shared Sections',
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
			'menu_order' => 20,
			'position'   => 'side',
			'style'      => 'default',
			'active'     => true,
			'fields'     => array(
				array(
					'key'           => 'field_vvss_page_override',
					'label'         => 'Use these sections instead',
					'name'          => 'shared_section_override',
					'type'          => 'post_object',
					'instructions'  => 'Optional. Leave empty and this page uses whatever matches its Page Group. Choosing one or more here replaces that choice for this page only. Sections with no Page Group (the site-wide ones) always appear either way.',
					'post_type'     => array( 'shared_section' ),
					'return_format' => 'id',
					'multiple'      => 1,
					'ui'            => 1,
					'allow_null'    => 1,
				),
			),
		)
	);
}
