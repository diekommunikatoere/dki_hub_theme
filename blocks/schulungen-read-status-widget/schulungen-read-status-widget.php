<?php
/**
 * Plugin Name:       Schulungen Read Status Widget
 * Description:       Inserts a block that allows users to see their read status for the current page and mark it as read. Also admins and moderators can reset the read status for all users.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       schulungen-read-status-widget
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
function dki_wiki_schulungen_read_status_widget_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'dki_wiki_schulungen_read_status_widget_init' );