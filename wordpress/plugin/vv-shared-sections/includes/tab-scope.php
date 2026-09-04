<?php
/**
 * Show only the tabs a section actually uses.
 *
 * A section is one of two things, and the Page Group box decides which:
 *
 *   No Page Group  → site-wide. Carries the Intro, FAQ and Contact blocks that
 *                    appear on every page. It has no use for Content.
 *   A Page Group   → targeted. Carries the copy that varies between service
 *                    types. It has no use for Intro, FAQ or Contact.
 *
 * Rather than leave every editor to remember that, the tabs that do not apply
 * are hidden. Two rules keep this safe:
 *
 *   - Nothing is hidden on a section that has not been saved yet, because it
 *     has no terms and its type is not yet known.
 *   - A tab that already holds content is never hidden, so this can never make
 *     existing copy disappear.
 *
 * Hidden fields are not rendered and so are not submitted, and ACF only writes
 * the fields it receives — the values stay in the database either way.
 *
 * Switch the whole thing off with:
 *
 *     add_filter( 'vvss_scope_tabs', '__return_false' );
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Which tab each field sits under.
 *
 * Read from the registered field group rather than hardcoded, so a field added
 * later is picked up without touching this file.
 *
 * @return array Field key => tab label.
 */
function vvss_field_tabs() {

	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map = array();

	if ( ! function_exists( 'acf_get_local_fields' ) ) {
		return $map;
	}

	$tab = '';
	foreach ( acf_get_local_fields( 'group_vvss_shared_section' ) as $field ) {
		if ( empty( $field['key'] ) ) {
			continue;
		}
		if ( isset( $field['type'] ) && 'tab' === $field['type'] ) {
			$tab = isset( $field['label'] ) ? $field['label'] : '';
		}
		$map[ $field['key'] ] = $tab;
	}

	return $map;
}

/**
 * The section currently open in the editor, or 0.
 *
 * @return int
 */
function vvss_editing_section_id() {

	static $id = null;

	if ( null !== $id ) {
		return $id;
	}

	$id = 0;

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only; decides which fields to draw, changes nothing.
	if ( isset( $_GET['post'] ) ) {
		$id = (int) $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$id = (int) $_POST['post_ID'];
	} else {
		$post = get_post();
		$id   = $post ? (int) $post->ID : 0;
	}
	// phpcs:enable

	return $id;
}

/**
 * Does this section target particular page groups?
 *
 * @param int $post_id Section ID.
 * @return bool
 */
function vvss_section_is_targeted( $post_id ) {

	$groups = wp_get_object_terms( $post_id, 'page_group', array( 'fields' => 'ids' ) );

	if ( is_wp_error( $groups ) ) {
		return false;
	}

	return ! empty( $groups );
}

/**
 * Does any field on this tab already hold something?
 *
 * @param int    $post_id Section ID.
 * @param string $tab     Tab label.
 * @return bool
 */
function vvss_tab_has_content( $post_id, $tab ) {

	static $cache = array();

	$key = $post_id . '|' . $tab;

	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$cache[ $key ] = false;

	foreach ( vvss_field_tabs() as $field_key => $field_tab ) {

		if ( $field_tab !== $tab ) {
			continue;
		}

		$field = function_exists( 'acf_get_field' ) ? acf_get_field( $field_key ) : null;
		if ( ! $field || in_array( $field['type'], array( 'tab', 'message' ), true ) ) {
			continue;
		}

		/*
		 * Raw meta, not get_field(): ACF hands back a field's default_value when
		 * nothing is stored, so a Contact tab that has never been touched still
		 * reports the phone number and email it defaults to, and every tab looks
		 * full. Only a real stored value should keep a tab visible.
		 *
		 * A repeater keeps its row count here, so '0' means no rows, and a
		 * true/false field stores '0' for off. Both are empty for our purposes.
		 */
		$raw = get_post_meta( $post_id, $field['name'], true );

		if ( '' !== $raw && null !== $raw && '0' !== $raw && 0 !== $raw && array() !== $raw ) {
			$cache[ $key ] = true;
			break;
		}
	}

	return $cache[ $key ];
}

/**
 * Hide the tabs that do not apply to this section.
 *
 * @param array $field ACF field.
 * @return array|false The field, or false to hide it.
 */
function vvss_scope_fields_to_section_type( $field ) {

	if ( ! is_admin() || empty( $field['key'] ) ) {
		return $field;
	}

	if ( ! apply_filters( 'vvss_scope_tabs', true ) ) {
		return $field;
	}

	$tabs = vvss_field_tabs();
	if ( ! isset( $tabs[ $field['key'] ] ) ) {
		return $field;
	}

	$tab = $tabs[ $field['key'] ];
	if ( '' === $tab || 'Display' === $tab ) {
		return $field;
	}

	$post_id = vvss_editing_section_id();
	if ( ! $post_id || 'shared_section' !== get_post_type( $post_id ) ) {
		return $field;
	}

	// A section that has never been saved has no terms, so its type is unknown.
	if ( 'auto-draft' === get_post_status( $post_id ) ) {
		return $field;
	}

	$hidden = vvss_section_is_targeted( $post_id )
		? array( 'Intro', 'FAQ', 'Contact' )
		: array( 'Content' );

	if ( ! in_array( $tab, $hidden, true ) ) {
		return $field;
	}

	// Never hide a tab that already holds copy.
	if ( vvss_tab_has_content( $post_id, $tab ) ) {
		return $field;
	}

	return false;
}
add_filter( 'acf/prepare_field', 'vvss_scope_fields_to_section_type' );
