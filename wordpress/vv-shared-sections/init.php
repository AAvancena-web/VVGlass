<?php
/**
 * VV Shared Sections — theme include (no plugin required).
 *
 * Place the "vv-shared-sections" folder inside your active (child) theme, then
 * add this line to your theme's functions.php:
 *
 *     require_once get_theme_file_path( 'vv-shared-sections/init.php' );
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! defined( 'VVSS_VERSION' ) ) {
	define( 'VVSS_VERSION', '1.1.0' );
}

/**
 * Convert an absolute path inside the active/parent theme to its public URL.
 * Keeps the include location-independent (theme root or a subfolder).
 */
function vvss_path_to_uri( $path ) {
	$path           = wp_normalize_path( $path );
	$stylesheet_dir = wp_normalize_path( get_stylesheet_directory() );
	$template_dir   = wp_normalize_path( get_template_directory() );

	if ( 0 === strpos( $path, $stylesheet_dir ) ) {
		return str_replace( $stylesheet_dir, get_stylesheet_directory_uri(), $path );
	}
	if ( 0 === strpos( $path, $template_dir ) ) {
		return str_replace( $template_dir, get_template_directory_uri(), $path );
	}

	$content_dir = wp_normalize_path( WP_CONTENT_DIR );
	if ( 0 === strpos( $path, $content_dir ) ) {
		return content_url( str_replace( $content_dir, '', $path ) );
	}
	return $path;
}

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
 * 1c. Seed the five starting groups once, so the boxes are not empty on day one.
 * Editors can rename, delete or add to these freely afterwards.
 */
function vvss_seed_page_groups() {
	if ( get_option( 'vvss_groups_seeded' ) ) {
		return;
	}
	foreach ( array( 'Hub', 'Installation', 'Repair', 'Replacement', 'Fencing' ) as $name ) {
		if ( ! term_exists( $name, 'page_group' ) ) {
			wp_insert_term( $name, 'page_group' );
		}
	}
	update_option( 'vvss_groups_seeded', 1, false );
}
add_action( 'init', 'vvss_seed_page_groups', 11 );

/**
 * 2. Register the ACF field groups in code.
 */
require_once __DIR__ . '/includes/acf-fields.php';

/**
 * 3. Front-end styles (loaded from the theme folder).
 */
function vvss_enqueue_assets() {
	$path = __DIR__ . '/assets/shared-section.css';
	if ( ! file_exists( $path ) ) {
		return;
	}
	wp_enqueue_style( 'vvss-shared-section', vvss_path_to_uri( $path ), array(), filemtime( $path ) );
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
 *   3. Sorted by menu_order, then by date, so stacking order is editable.
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

	$resolved = array();

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
			$resolved[] = (int) $section->ID;
			continue;
		}

		// Grouped sections: an override on the page replaces the group match.
		if ( $override_ids ) {
			if ( in_array( (int) $section->ID, $override_ids, true ) ) {
				$resolved[] = (int) $section->ID;
			}
			continue;
		}

		if ( array_intersect( $section_groups, $page_groups ) ) {
			$resolved[] = (int) $section->ID;
		}
	}

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
	include __DIR__ . '/templates/faq-contact.php';
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
 * 4b. Auto-output the resolved sections just above the footer.
 *
 * Hooked to siteorigin_corp_footer_before, which fires AFTER the theme closes
 * #content and .corp-container, so the block is already full width and needs no
 * negative-margin trickery. (The old get_footer hook fired while still inside
 * .corp-container, which is why the stylesheet used a 100vw hack — that hack
 * overflows by the scrollbar width on Windows.)
 *
 * Note: siteorigin_corp_footer_before only fires when the page's footer is
 * enabled in the SiteOrigin page settings. Change the hook with:
 *   add_filter( 'vvss_output_hook', fn() => 'get_footer' );
 * and the block will fall back to rendering inside the container.
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

/**
 * Which action the automatic output is attached to.
 */
function vvss_output_hook() {
	return (string) apply_filters( 'vvss_output_hook', 'siteorigin_corp_footer_before' );
}
add_action( 'wp', function () {
	add_action( vvss_output_hook(), 'vvss_render_above_footer' );
} );

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
