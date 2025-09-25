<?php

function dki_wiki_blocks_register_blocks() {
    $blocks_dir = get_stylesheet_directory() . '/blocks';
    $block_folders = scandir($blocks_dir);
    foreach ($block_folders as $block_folder) {
        if ($block_folder === '.' || $block_folder === '..') {
            continue;
        }
        $block_json = $blocks_dir . '/' . $block_folder . '/build/block.json';
        if (file_exists($block_json)) {
            register_block_type( $blocks_dir . '/' . $block_folder . '/build' );
        }
    }
}

add_action('init', 'dki_wiki_blocks_register_blocks');