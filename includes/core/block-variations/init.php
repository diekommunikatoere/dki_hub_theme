<?php

// Include block editor block variation configuration
add_action("enqueue_block_editor_assets", "dki_hub_block_editor_assets");

function dki_hub_block_editor_assets() {
	wp_enqueue_script("dki_hub-block-variations--button", get_stylesheet_directory_uri() . "/includes/core/block-variations/button.js", array("wp-blocks", "wp-dom-ready", "wp-edit-post"));
}