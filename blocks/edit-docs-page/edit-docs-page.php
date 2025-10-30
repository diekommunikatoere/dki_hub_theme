<?php
/**
 * Plugin Name:         Docs Page Edit Links
 * Plugin URI:          https://hub.diekommunikatoere.de
 * Description:         Inserts a link to edit the currently opened BetterDocs page.
 * Requires at least:   6.5
 * Requires PHP:        7.0
 * Version:             0.2.0
 * Author:              Steven Sullivan, Jörg Hegner
 * License:             GPL-2.0-or-later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         edit-docs-page
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
function dki_wiki_edit_docs_page_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'dki_wiki_edit_docs_page_block_init' );