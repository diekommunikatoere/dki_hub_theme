<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Include assets
require_once get_stylesheet_directory() . '/includes/assets/init.php';

// Include core files
require_once get_stylesheet_directory() . '/includes/core/init.php';

// Include features
require_once get_stylesheet_directory() . '/includes/features/init.php';



// Remove WP version from head
remove_action('wp_head', 'wp_generator');



// Extend search to include 'faq' post type
function dki_wiki_search_faq( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if ( is_search() ) {
            $post_types = $query->get( 'post_type' );
            if ( $post_types === 'post' || empty( $post_types ) ) {
                $query->set( 'post_type', array( 'post', 'faq' ) );
            } elseif ( is_array( $post_types ) ) {
                $post_types[] = 'faq';
                $query->set( 'post_type', $post_types );
            }
        }
    }
}
add_action( 'pre_get_posts', 'dki_wiki_search_faq' );