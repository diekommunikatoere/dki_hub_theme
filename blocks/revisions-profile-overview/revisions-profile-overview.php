<?php
/**
 * Plugin Name:       Revisions Profile Overview
 * Description:       Overview of all revisions of a user. If they are a moderator or an admin, they can see all revisions of all users. If they are a user, they can see all revisions of themselves.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Steven Sullivan, Jörg Hegner
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       revisions-profile-overview
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
function dki_wiki_blocks_revisions_profile_overview_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'dki_wiki_blocks_revisions_profile_overview_block_init' );
