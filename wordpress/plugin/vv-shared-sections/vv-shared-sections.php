<?php
/**
 * Plugin Name:       VV Shared Sections
 * Plugin URI:        https://vvglass.com.au/
 * Description:       Reusable content, FAQ and contact blocks that render above the footer. Sections with no Page Group show on every page; sections with one show only on pages in that group. Deactivate to switch the whole thing off without losing any content.
 * Version:           1.3.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Digital Movement
 * Author URI:        https://www.digitalmovement.com.au/
 * License:           GPL-2.0-or-later
 * Text Domain:       vv-shared-sections
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/*
 * Guard against the older theme-based copy still being loaded.
 *
 * Plugins load before the theme's functions.php, so if the child theme still
 * has the original init.php AND still requires it, that file would try to
 * redeclare every function below and take the site down with a fatal error.
 * Replace the theme's init.php with the shim supplied alongside this plugin.
 */
if ( defined( 'VVSS_VERSION' ) ) {
	return;
}

define( 'VVSS_VERSION', '1.3.0' );
define( 'VVSS_FILE', __FILE__ );
define( 'VVSS_DIR', plugin_dir_path( __FILE__ ) );
define( 'VVSS_URL', plugin_dir_url( __FILE__ ) );

/**
 * 1. Register the "Shared Sections" custom post type (this is the admin menu).
 *
 * page-attributes is supported so menu_order controls the order that several
 * sections stack in on the same page (lower numbers render first).
 */
function vvss_register_cpt() {
	$labels = array(
		'name'          => __( 'Shared Sections', 'vv-shared-sections' ),
		'singular_name' => __( 'Shared Section', 'vv-shared-sections' ),
		'add_new'       => __( 'Add New', 'vv-shared-sections' ),
		'add_new_item'  => __( 'Add New Shared Section', 'vv-shared-sections' ),
		'edit_item'     => __( 'Edit Shared Section', 'vv-shared-sections' ),
		'new_item'      => __( 'New Shared Section', 'vv-shared-sections' ),
		'view_item'     => __( 'View Shared Section', 'vv-shared-sections' ),
		'search_items'  => __( 'Search Shared Sections', 'vv-shared-sections' ),
		'not_found'     => __( 'No shared sections found', 'vv-shared-sections' ),
		'menu_name'     => __( 'Shared Sections', 'vv-shared-sections' ),
	);

	register_post_type(
		'shared_section',
		array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-layout',
			'menu_position'   => 25,
			'supports'        => array( 'title', 'page-attributes' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'page',
		)
	);
}
add_action( 'init', 'vvss_register_cpt' );

/**
 * 1b. Register the "Page Group" taxonomy, shared by pages and sections.
 *
 * A page is tagged with the group it belongs to. A section tagged with the same
 * group appears on those pages. A section with NO group is treated as site-wide.
 */
function vvss_register_taxonomy() {
	register_taxonomy(
		'page_group',
		array( 'page', 'shared_section' ),
		array(
			'labels'             => array(
				'name'          => __( 'Page Groups', 'vv-shared-sections' ),
				'singular_name' => __( 'Page Group', 'vv-shared-sections' ),
				'add_new_item'  => __( 'Add New Page Group', 'vv-shared-sections' ),
				'menu_name'     => __( 'Page Groups', 'vv-shared-sections' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => true, // Checkboxes rather than a free-text tag box.
			'rewrite'            => false,
		)
	);
}
add_action( 'init', 'vvss_register_taxonomy', 9 );

/**
 * 1c. Seed the five starting groups on activation, so the boxes are not empty
 * on day one. Editors can rename, delete or add to these freely afterwards.
 *
 * Runs once ever — the option guard means deactivating and reactivating will
 * not recreate terms you deliberately deleted.
 */
function vvss_seed_page_groups() {
	if ( get_option( 'vvss_groups_seeded' ) ) {
		return;
	}

	// Activation fires before init, so make sure the taxonomy exists first.
	if ( ! taxonomy_exists( 'page_group' ) ) {
		vvss_register_taxonomy();
	}

	foreach ( array( 'Hub', 'Installation', 'Repair', 'Replacement', 'Fencing' ) as $name ) {
		if ( ! term_exists( $name, 'page_group' ) ) {
			wp_insert_term( $name, 'page_group' );
		}
	}
	update_option( 'vvss_groups_seeded', 1, false );
}
register_activation_hook( __FILE__, 'vvss_seed_page_groups' );

/**
 * 2. Register the ACF field groups in code.
 */
require_once VVSS_DIR . 'includes/acf-fields.php';

if ( is_admin() ) {
	require_once VVSS_DIR . 'includes/seeder.php';
}

/**
 * 3. Front-end styles.
 */
function vvss_enqueue_assets() {
	$path = VVSS_DIR . 'assets/shared-section.css';
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_style( 'vvss-shared-section', VVSS_URL . 'assets/shared-section.css', array(), filemtime( $path ) );
}
add_action( 'wp_enqueue_scripts', 'vvss_enqueue_assets' );

/**
 * Is a section switched on?
 *
 * ACF's default_value only applies on the edit screen, so a section created
 * programmatically (or imported) has no meta row at all. Treat a missing value
 * as ON to match what the field shows an editor, and only ever hide on an
 * explicit '0'.
 *
 * @param int $post_id Shared Section post ID.
 * @return bool
 */
function vvss_section_enabled( $post_id ) {
	$value = get_post_meta( $post_id, 'show_above_footer', true );
	return ( '0' !== (string) $value );
}

/**
 * Which sections should render on a given page?
 *
 * Resolution:
 *   1. Every enabled section with NO page_group  → site-wide, always included.
 *   2. Plus, either
 *        a. the sections named in this page's "Shared Section Override" field, or
 *        b. every enabled section sharing a page_group with this page.
 *      The override replaces the group match only; site-wide sections still show.
 *
 * Order: page-group sections always render ABOVE site-wide ones, so the
 * varying content sits above the global intro, FAQ and contact block without
 * anyone having to set Order by hand. menu_order still sorts within each band.
 * Use the vvss_sections_for_page filter for anything unusual.
 *
 * @param int $object_id Page/post ID to resolve for. Defaults to the queried object.
 * @return int[] Shared Section post IDs, in render order.
 */
function vvss_get_sections_for( $object_id = 0 ) {
	$object_id = $object_id ? absint( $object_id ) : (int) get_queried_object_id();

	static $cache = array();
	if ( isset( $cache[ $object_id ] ) ) {
		return $cache[ $object_id ];
	}

	$sections = get_posts(
		array(
			'post_type'        => 'shared_section',
			'post_status'      => 'publish',
			'posts_per_page'   => 50,
			'orderby'          => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'suppress_filters' => false,
			'no_found_rows'    => true,
		)
	);

	if ( empty( $sections ) ) {
		$cache[ $object_id ] = array();
		return array();
	}

	// Groups this page belongs to.
	$page_groups = array();
	if ( $object_id ) {
		$terms = wp_get_object_terms( $object_id, 'page_group', array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $terms ) ) {
			$page_groups = $terms;
		}
	}

	// Explicit per-page override, if the page names one.
	$override_ids = array();
	if ( $object_id && function_exists( 'get_field' ) ) {
		$override = get_field( 'shared_section_override', $object_id );
		if ( $override ) {
			foreach ( (array) $override as $item ) {
				$override_ids[] = is_object( $item ) ? (int) $item->ID : (int) $item;
			}
		}
	}

	// Two bands, kept apart so the varying content always lands above the
	// site-wide block. Each band stays in the query's menu_order, then date.
	$targeted = array();
	$sitewide = array();

	foreach ( $sections as $section ) {
		if ( ! vvss_section_enabled( $section->ID ) ) {
			continue;
		}

		$section_groups = wp_get_object_terms( $section->ID, 'page_group', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $section_groups ) ) {
			$section_groups = array();
		}

		// No group on the section: site-wide, always in.
		if ( empty( $section_groups ) ) {
			$sitewide[] = (int) $section->ID;
			continue;
		}

		// Grouped sections: an override on the page replaces the group match.
		if ( $override_ids ) {
			if ( in_array( (int) $section->ID, $override_ids, true ) ) {
				$targeted[] = (int) $section->ID;
			}
			continue;
		}

		if ( array_intersect( $section_groups, $page_groups ) ) {
			$targeted[] = (int) $section->ID;
		}
	}

	$resolved = array_merge( $targeted, $sitewide );

	/**
	 * Filter the resolved section IDs for a page.
	 *
	 * @param int[] $resolved  Shared Section IDs in render order.
	 * @param int   $object_id The page being rendered.
	 */
	$resolved = (array) apply_filters( 'vvss_sections_for_page', $resolved, $object_id );

	$cache[ $object_id ] = $resolved;
	return $resolved;
}

/**
 * Render a Shared Section by ID and return the HTML.
 *
 * @param int  $post_id Shared Section post ID.
 * @param bool $bleed   Add the full-bleed modifier (for use inside a narrow container).
 * @return string
 */
function vvss_render_section( $post_id, $bleed = false ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return '';
	}
	ob_start();
	include VVSS_DIR . 'templates/faq-contact.php';
	return ob_get_clean();
}

/**
 * 4. Shortcode: [shared_section id="123"] or [shared_section slug="faq-contact"].
 *
 * With no attributes it renders whatever would appear automatically on this
 * page, so the shortcode can be used to place the block manually in page
 * content instead of relying on the automatic output.
 *
 * Output from the shortcode lands inside the theme's narrow .corp-container,
 * so it carries the full-bleed modifier.
 */
function vvss_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'   => 0,
			'slug' => '',
		),
		$atts,
		'shared_section'
	);

	$post_id = absint( $atts['id'] );

	if ( ! $post_id && ! empty( $atts['slug'] ) ) {
		$found = get_page_by_path( sanitize_title( $atts['slug'] ), OBJECT, 'shared_section' );
		if ( $found ) {
			$post_id = $found->ID;
		}
	}

	if ( $post_id ) {
		return vvss_render_section( $post_id, true );
	}

	$out = '';
	foreach ( vvss_get_sections_for() as $id ) {
		$out .= vvss_render_section( $id, true );
	}
	return $out;
}
add_shortcode( 'shared_section', 'vvss_shortcode' );

/**
 * Which action the automatic output is attached to.
 *
 * siteorigin_corp_footer_before fires AFTER the theme closes #content and
 * .corp-container, so the block is already full width and needs no negative
 * margins. Note it sits inside the theme's page-level footer toggle, so a page
 * with its footer disabled will not show the block. To fall back to rendering
 * inside the container instead:
 *
 *   add_filter( 'vvss_output_hook', fn() => 'get_footer' );
 */
function vvss_output_hook() {
	return (string) apply_filters( 'vvss_output_hook', 'siteorigin_corp_footer_before' );
}

/**
 * 4b. Auto-output the resolved sections just above the footer.
 */
function vvss_render_above_footer() {
	static $done = false;
	if ( $done || is_admin() ) {
		return;
	}

	if ( ! apply_filters( 'vvss_show_above_footer', true ) ) {
		return;
	}

	$ids = vvss_get_sections_for();
	if ( empty( $ids ) ) {
		return;
	}

	$done  = true;
	$bleed = ( 'get_footer' === vvss_output_hook() );

	foreach ( $ids as $id ) {
		echo vvss_render_section( $id, $bleed ); // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Escaped in template.
	}
}
add_action(
	'wp',
	function () {
		add_action( vvss_output_hook(), 'vvss_render_above_footer' );
	}
);

/**
 * 5. Admin list columns: the shortcode, plus which pages a section lands on.
 */
function vvss_admin_columns( $columns ) {
	$columns['vvss_scope']     = __( 'Applies To', 'vv-shared-sections' );
	$columns['vvss_shortcode'] = __( 'Shortcode', 'vv-shared-sections' );
	return $columns;
}
add_filter( 'manage_shared_section_posts_columns', 'vvss_admin_columns' );

function vvss_admin_column_content( $column, $post_id ) {
	if ( 'vvss_shortcode' === $column ) {
		printf( '<code>[shared_section id="%d"]</code>', absint( $post_id ) );
		return;
	}

	if ( 'vvss_scope' === $column ) {
		if ( ! vvss_section_enabled( $post_id ) ) {
			echo '<em>' . esc_html__( 'Off', 'vv-shared-sections' ) . '</em>';
			return;
		}
		$terms = get_the_terms( $post_id, 'page_group' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			echo '<strong>' . esc_html__( 'Every page', 'vv-shared-sections' ) . '</strong>';
			return;
		}
		echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) );
	}
}
add_action( 'manage_shared_section_posts_custom_column', 'vvss_admin_column_content', 10, 2 );

/**
 * 6. Friendly notice if ACF is not active.
 */
function vvss_acf_notice() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		echo '<div class="notice notice-warning"><p><strong>VV Shared Sections</strong> needs the <em>Advanced Custom Fields</em> plugin active. ACF PRO is required for the FAQ and question repeaters.</p></div>';
	}
}
add_action( 'admin_notices', 'vvss_acf_notice' );

/**
 * 7. "Settings"-style row link pointing at the sections list.
 */
function vvss_action_links( $links ) {
	array_unshift(
		$links,
		'<a href="' . esc_url( admin_url( 'edit.php?post_type=shared_section' ) ) . '">' . esc_html__( 'Sections', 'vv-shared-sections' ) . '</a>'
	);
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'vvss_action_links' );
