<?php
/**
 * Plugin Name:       FAQ Display
 * Description:       Ein Block zur Anzeige von FAQs in nested Akkordeons.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       faq-display
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
function dki_wiki_faq_display_block_init() {
    register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'dki_wiki_faq_display_block_init' );