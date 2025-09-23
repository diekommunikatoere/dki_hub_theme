<?php

// Include frontend styles
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'dki-frontend-styles', get_stylesheet_directory_uri() . '/includes/assets/css/styles.css', get_parent_theme_file_uri( 'style.css' ), filemtime( get_stylesheet_directory() . '/includes/assets/css/styles.css' ), 'all' );
    wp_enqueue_style( 'dki-frontend-block-variations', get_stylesheet_directory_uri() . '/includes/assets/css/block-variations.css', get_parent_theme_file_uri( 'style.css' ), filemtime( get_stylesheet_directory() . '/includes/assets/css/block-variations.css' ), 'all' );
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'twentytwentyfive-style' );
} );

// Include scripts
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script( 'dki-frontend-scripts', get_stylesheet_directory_uri() . '/includes/assets/js/scripts.js', array(), filemtime( get_stylesheet_directory() . '/includes/assets/js/scripts.js' ), true );
} );

// Include admin styles
add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_style( 'dki-admin-variables', get_stylesheet_directory_uri() . '/includes/assets/css/modules/admin/variables.css', get_parent_theme_file_uri( 'style.css' ), filemtime( get_stylesheet_directory() . '/includes/assets/css/modules/admin/variables.css' ), 'all' );
    wp_enqueue_style( 'dki-admin-faq-styles', get_stylesheet_directory_uri() . '/includes/assets/css/modules/admin/faq_admin.css', get_parent_theme_file_uri( 'style.css' ), filemtime( get_stylesheet_directory() . '/includes/assets/css/modules/admin/faq_admin.css' ), 'all' );
    wp_enqueue_style( 'dki-admin-block-variations', get_stylesheet_directory_uri() . '/includes/assets/css/block-variations.css', get_parent_theme_file_uri( 'style.css' ), filemtime( get_stylesheet_directory() . '/includes/assets/css/block-variations.css' ), 'all' );
} );


// Include admin scripts
add_action( 'admin_enqueue_scripts', function() {
} );
