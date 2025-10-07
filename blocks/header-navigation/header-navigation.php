<?php
/**
 * Plugin Name:       Header Navigation
 * Description:       Displays a navigation menu with links to the user's profile, their revisions, onboardings, and the logout page. Also includes a menu selector to choose from WordPress menus.
 * Requires at least: 6.6
 * Requires PHP:      7.0
 * Version:           1.0.1
 * Author:            Steven Sullivan, Jörg Hegner
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       header-navigation
 *
 * @package dki-wiki-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function dki_wiki_blocks_header_navigation_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'dki_wiki_blocks_header_navigation_block_init' );
