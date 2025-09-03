<?php
/**
 * Plugin Name:       Schulungen Query Loop
 * Description:       Query Loop for Schulungen
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       schulungen-query-loop
 *
 * @package dki-wiki-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// Add support custom_fields to the post type 'docs'
add_action( 'init', 'schulungen_query_loop_add_custom_fields' );
function schulungen_query_loop_add_custom_fields() {
	add_post_type_support( 'docs', 'custom-fields' );
}

// Add button to the post edit screen to remove the current schulung from all users
// The post type is 'docs'
// The taxonomy is 'doc_category'
// The term_id is 42
add_action( 'add_meta_boxes', 'schulungen_query_loop_add_meta_box' );
function schulungen_query_loop_add_meta_box() {
	add_meta_box(
		'schulungen_query_loop_meta_box',
		'Remove Schulung from all users',
		'schulungen_query_loop_meta_box_callback',
		'post',
		'side',
		'high'
	);
}

// Meta box callback function
// Get all users and remove the currently open schulung from their read_schulungen array
function schulungen_query_loop_meta_box_callback( $post ) {
	$users = get_users();
	
	foreach ($users as $user) {
		$read_status = get_user_meta($user->ID, 'read_schulungen', true);
		if (!$read_status || !is_array($read_status)) {
			$read_status = array();
		}
		$read_status = array_diff($read_status, array($post->ID));
		update_user_meta($user->ID, 'read_schulungen', $read_status);
	}
}


/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_schulungen_query_loop_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_schulungen_query_loop_block_init' );