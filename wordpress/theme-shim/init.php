<?php
/**
 * Placeholder — VV Shared Sections now runs as a plugin.
 *
 * The child theme's functions.php still contains:
 *
 *     require_once get_stylesheet_directory() . '/init.php';
 *
 * require_once on a MISSING file is a fatal error, so deleting the original
 * init.php without also editing functions.php would white-screen the site.
 * This file keeps that require harmless while the plugin does the real work.
 *
 * To remove it properly:
 *   1. Delete the require_once line from functions.php.
 *   2. Delete this file.
 *
 * @package VV_Shared_Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Intentionally empty.
