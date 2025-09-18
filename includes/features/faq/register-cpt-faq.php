<?php
/**
 * Register Custom Post Types and Taxonomies for FAQs
 *
 * @package DKI Wiki Theme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register FAQ Custom Post Type and Section Taxonomy
 */
function dki_wiki_register_faqs_cpt() {
    // Register FAQ Post Type
    $faq_labels = array(
        'name'                  => _x( 'FAQs', 'Post type general name', 'dki-wiki' ),
        'singular_name'         => _x( 'FAQ', 'Post type singular name', 'dki-wiki' ),
        'menu_name'             => _x( 'FAQs', 'Admin Menu text', 'dki-wiki' ),
        'name_admin_bar'        => _x( 'FAQ', 'Add New on Toolbar', 'dki-wiki' ),
        'add_new'               => __( 'Neue FAQ hinzufügen', 'dki-wiki' ),
        'add_new_item'          => __( 'Neue FAQ hinzufügen', 'dki-wiki' ),
        'new_item'              => __( 'Neue FAQ', 'dki-wiki' ),
        'edit_item'             => __( 'FAQ bearbeiten', 'dki-wiki' ),
        'view_item'             => __( 'FAQ ansehen', 'dki-wiki' ),
        'all_items'             => __( 'Alle FAQs', 'dki-wiki' ),
        'search_items'          => __( 'FAQs durchsuchen', 'dki-wiki' ),
        'parent_item_colon'     => __( 'Elter FAQ:', 'dki-wiki' ),
        'not_found'             => __( 'Keine FAQs gefunden.', 'dki-wiki' ),
        'not_found_in_trash'    => __( 'Keine FAQs im Papierkorb gefunden.', 'dki-wiki' ),
        'insert_into_item'      => __( 'In FAQ einfügen', 'dki-wiki' ),
        'uploaded_to_this_item' => __( 'Zu dieser FAQ hochgeladen', 'dki-wiki' ),
        'filter_items_list'     => __( 'FAQs listen filtern', 'dki-wiki' ),
        'items_list_navigation' => __( 'FAQs-Liste navigieren', 'dki-wiki' ),
        'items_list'            => __( 'FAQs-Liste', 'dki-wiki' ),
    );

    $faq_args = array(
        'labels'             => $faq_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'   => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'faq' ),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-editor-help',
    );

    register_post_type( 'faq', $faq_args );

    // Register FAQ Section Taxonomy
    $section_labels = array(
        'name'              => _x( 'Abschnitte', 'taxonomy general name', 'dki-wiki' ),
        'singular_name'     => _x( 'Abschnitt', 'taxonomy singular name', 'dki-wiki' ),
        'search_items'      => __( 'Abschnitte durchsuchen', 'dki-wiki' ),
        'all_items'         => __( 'Alle Abschnitte', 'dki-wiki' ),
        'parent_item'       => __( 'Elter Abschnitt', 'dki-wiki' ),
        'parent_item_colon' => __( 'Elter Abschnitt:', 'dki-wiki' ),
        'edit_item'         => __( 'Abschnitt bearbeiten', 'dki-wiki' ),
        'update_item'       => __( 'Abschnitt aktualisieren', 'dki-wiki' ),
        'add_new_item'      => __( 'Neuen Abschnitt hinzufügen', 'dki-wiki' ),
        'new_item_name'     => __( 'Neuer Abschnittsname', 'dki-wiki' ),
        'menu_name'         => __( 'Abschnitte', 'dki-wiki' ),
    );

    $section_args = array(
        'hierarchical'      => true,
        'labels'            => $section_labels,
        'show_ui'           => false,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'faq-section' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'faq_section', array( 'faq' ), $section_args );

    // Register term meta for section order
    register_term_meta( 'faq_section', '_section_order', array(
        'type' => 'integer',
        'description' => __( 'Benutzerdefinierte Reihenfolge für Abschnitte', 'dki-wiki' ),
        'single' => true,
        'show_in_rest' => true,
    ) );
}

// Hook the registration function
add_action( 'init', 'dki_wiki_register_faqs_cpt' );

// Set default order on new section creation
function dki_wiki_set_default_section_order_on_create( $term_id ) {
    // Get the term to verify it's faq_section
    $term = get_term( $term_id, 'faq_section' );
    if ( is_wp_error( $term ) || ! $term ) {
        return;
    }

    // Check if already has order
    $existing_order = get_term_meta( $term_id, '_section_order', true );
    if ( $existing_order !== '' ) {
        return; // Already set, skip
    }

    // Find max order
    $sections = get_terms( array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
        'fields' => 'ids',
    ) );
    $max_order = 0;
    foreach ( $sections as $sec_id ) {
        $order = get_term_meta( $sec_id, '_section_order', true );
        if ( is_numeric( $order ) && $order > $max_order ) {
            $max_order = intval( $order );
        }
    }

    // Set to max + 1
    update_term_meta( $term_id, '_section_order', $max_order + 1 );
}
add_action( 'created_faq_section', 'dki_wiki_set_default_section_order_on_create' );

// Bulk set defaults for existing sections
function dki_wiki_bulk_set_default_section_orders() {
    $initialized = get_option( 'dki_section_orders_initialized', false );
    if ( $initialized ) {
        return;
    }

    $sections = get_terms( array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
    ) );

    if ( empty( $sections ) || is_wp_error( $sections ) ) {
        return;
    }

    // Find max order and collect terms without order
    $max_order = 0;
    $terms_without_order = array();
    foreach ( $sections as $term ) {
        $order = get_term_meta( $term->term_id, '_section_order', true );
        if ( $order === '' ) {
            $terms_without_order[] = $term;
        } elseif ( is_numeric( $order ) && $order > $max_order ) {
            $max_order = intval( $order );
        }
    }

    if ( empty( $terms_without_order ) ) {
        update_option( 'dki_section_orders_initialized', true );
        return;
    }

    // Sort without order by name
    usort( $terms_without_order, function( $a, $b ) {
        return strnatcasecmp( $a->name, $b->name );
    } );

    // Assign sequential orders starting after max
    $current_order = $max_order + 1;
    foreach ( $terms_without_order as $term ) {
        update_term_meta( $term->term_id, '_section_order', $current_order );
        $current_order++;
    }

    update_option( 'dki_section_orders_initialized', true );

    // Optional: Admin notice
    if ( is_admin() ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success"><p>' . __( 'Default section orders have been set for FAQs.', 'dki-wiki' ) . '</p></div>';
        } );
    }
}

// Metabox for FAQ order
/* function dki_wiki_faq_order_metabox() {
    add_meta_box(
        'faq_order',
        __( 'Reihenfolge', 'dki-wiki' ),
        'dki_wiki_faq_order_metabox_callback',
        'faq',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'dki_wiki_faq_order_metabox' );

function dki_wiki_faq_order_metabox_callback( $post ) {
    wp_nonce_field( 'faq_order_nonce', 'faq_order_nonce' );
    $order = get_post_meta( $post->ID, '_faq_order', true );
    echo '<label for="faq_order">' . __( 'Reihenfolge (Nummer):', 'dki-wiki' ) . '</label> ';
    echo '<input type="number" id="faq_order" name="faq_order" value="' . esc_attr( $order ) . '" size="25" />';
}

function dki_wiki_save_faq_order( $post_id ) {
    if ( ! isset( $_POST['faq_order_nonce'] ) || ! wp_verify_nonce( $_POST['faq_order_nonce'], 'faq_order_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['faq_order'] ) ) {
        update_post_meta( $post_id, '_faq_order', intval( $_POST['faq_order'] ) );
    }
}
add_action( 'save_post', 'dki_wiki_save_faq_order' ); */