<?php

/**
 * Register custom text styles to TinyMCE editor in post editor
 * 
 */

// Add custom dropdown to TinyMCE editor in the first row of the toolbar
add_filter( 'mce_buttons_2', 'add_custom_text_format' );

function add_custom_text_format( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

// Add custom text styles to the dropdown
add_filter( 'tiny_mce_before_init', 'add_custom_text_format_options' );

function add_custom_text_format_options( $settings ) {
    $style_formats = array(
        array(
            'title' => 'Headline Inline',
            'inline' => 'span',
            'classes' => 'headline-inline',
            'wrapper' => false,
        )
    );

    $settings['style_formats'] = wp_json_encode( $style_formats );

    return $settings;
}

// Add stylesheets to the TinyMCE editor
add_action( 'admin_init', 'add_custom_text_format_editor_styles' );

function add_custom_text_format_editor_styles() {
    add_editor_style( "includes/css/styles.css" );
}