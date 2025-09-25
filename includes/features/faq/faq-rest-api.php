<?php
/**
 * REST API endpoints for FAQ management
 *
 * @package DKI Wiki Theme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register REST API endpoints for FAQ management
 */
function dki_wiki_register_faq_rest_endpoints() {
    // Register FAQ Sections endpoints
    register_rest_route( 'dki/v1', '/faq-sections', array(
        'methods' => 'GET',
        'callback' => 'dki_wiki_get_faq_sections',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
    ));

    register_rest_route( 'dki/v1', '/faq-sections', array(
        'methods' => 'POST',
        'callback' => 'dki_wiki_create_faq_section',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'name' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_string( $param ) && ! empty( trim( $param ) );
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-sections/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'dki_wiki_update_faq_section',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'id' => array(
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
            'name' => array(
                'required' => false,
                'validate_callback' => function( $param ) {
                    return is_string( $param ) && ! empty( trim( $param ) );
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-sections/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'dki_wiki_delete_faq_section',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'id' => array(
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-sections/reorder', array(
        'methods' => 'POST',
        'callback' => 'dki_wiki_reorder_faq_sections',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'sectionsOrder' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_array( $param ) && ! empty( $param );
                },
            ),
        ),
    ));

    // Register FAQ Items endpoints
    register_rest_route( 'dki/v1', '/faq-items', array(
        'methods' => 'GET',
        'callback' => 'dki_wiki_get_faq_items',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'section' => array(
                'required' => false,
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-items', array(
        'methods' => 'POST',
        'callback' => 'dki_wiki_create_faq_item',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'title' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_string( $param ) && ! empty( trim( $param ) );
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'content' => array(
                'required' => true,
                'sanitize_callback' => 'wp_kses_post',
            ),
            'sectionId' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-items/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'dki_wiki_update_faq_item',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'id' => array(
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
            'title' => array(
                'required' => false,
                'validate_callback' => function( $param ) {
                    return is_string( $param ) && ! empty( trim( $param ) );
                },
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'content' => array(
                'required' => false,
                'sanitize_callback' => 'wp_kses_post',
            ),
            'sectionId' => array(
                'required' => false,
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-items/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'dki_wiki_delete_faq_item',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'id' => array(
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ));

    register_rest_route( 'dki/v1', '/faq-items/reorder', array(
        'methods' => 'POST',
        'callback' => 'dki_wiki_reorder_faq_items',
        'permission_callback' => 'dki_wiki_faq_permissions_check',
        'args' => array(
            'sectionId' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_numeric( $param );
                },
            ),
            'faqsOrder' => array(
                'required' => true,
                'validate_callback' => function( $param ) {
                    return is_array( $param );
                },
            ),
        ),
    ));
}
add_action( 'rest_api_init', 'dki_wiki_register_faq_rest_endpoints' );

/**
 * Permission callback for FAQ endpoints
 */
function dki_wiki_faq_permissions_check() {
    return current_user_can( 'edit_posts' );
}

/**
 * Format section data for API response
 */
function dki_wiki_format_section_for_api( $term ) {
    $order = get_term_meta( $term->term_id, '_section_order', true );
    
    return array(
        'id' => $term->term_id,
        'name' => $term->name,
        'slug' => $term->slug,
        'description' => $term->description,
        'order' => $order ? intval( $order ) : 0,
        'count' => $term->count,
    );
}

/**
 * Format FAQ item data for API response
 */
function dki_wiki_format_faq_item_for_api( $post ) {
    $order = get_post_meta( $post->ID, '_faq_order', true );
    $sections = wp_get_post_terms( $post->ID, 'faq_section' );
    $section_id = ! empty( $sections ) ? $sections[0]->term_id : 0;
    
    return array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'content' => $post->post_content,
        'sectionId' => $section_id,
        'order' => $order ? intval( $order ) : 0,
        'status' => $post->post_status,
        'dateCreated' => $post->post_date,
        'dateModified' => $post->post_modified,
    );
}

// Section API callbacks
function dki_wiki_get_faq_sections( $request ) {
    $sections = get_terms( array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
        'orderby' => 'meta_value_num',
        'meta_key' => '_section_order',
        'order' => 'ASC'
    ));

    if ( is_wp_error( $sections ) ) {
        return new WP_Error( 'sections_error', __( 'Could not retrieve sections.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    $formatted_sections = array_map( 'dki_wiki_format_section_for_api', $sections );
    return rest_ensure_response( $formatted_sections );
}

function dki_wiki_create_faq_section( $request ) {
    $name = $request->get_param( 'name' );
    $description = $request->get_param( 'description' );

    $term = wp_insert_term( $name, 'faq_section', array(
        'description' => $description ?: '',
    ));

    if ( is_wp_error( $term ) ) {
        return new WP_Error( 'create_error', __( 'Could not create section.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    // Set order for new section
    $existing_sections = get_terms( array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
        'fields' => 'ids',
    ));
    
    $max_order = 0;
    foreach ( $existing_sections as $section_id ) {
        $order = get_term_meta( $section_id, '_section_order', true );
        if ( is_numeric( $order ) && $order > $max_order ) {
            $max_order = intval( $order );
        }
    }
    
    update_term_meta( $term['term_id'], '_section_order', $max_order + 1 );

    $created_term = get_term( $term['term_id'], 'faq_section' );
    $formatted_section = dki_wiki_format_section_for_api( $created_term );
    
    return rest_ensure_response( $formatted_section );
}

function dki_wiki_update_faq_section( $request ) {
    $id = $request->get_param( 'id' );
    $name = $request->get_param( 'name' );
    $description = $request->get_param( 'description' );

    $update_data = array();
    if ( $name !== null ) {
        $update_data['name'] = $name;
    }
    if ( $description !== null ) {
        $update_data['description'] = $description;
    }

    $term = wp_update_term( $id, 'faq_section', $update_data );

    if ( is_wp_error( $term ) ) {
        return new WP_Error( 'update_error', __( 'Could not update section.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    $updated_term = get_term( $id, 'faq_section' );
    $formatted_section = dki_wiki_format_section_for_api( $updated_term );
    
    return rest_ensure_response( $formatted_section );
}

function dki_wiki_delete_faq_section( $request ) {
    $id = $request->get_param( 'id' );

    // Delete all FAQ items in this section first
    $faq_items = get_posts( array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'faq_section',
                'field' => 'term_id',
                'terms' => $id,
            ),
        ),
    ));

    foreach ( $faq_items as $faq_item ) {
        wp_delete_post( $faq_item->ID, true );
    }

    $result = wp_delete_term( $id, 'faq_section' );

    if ( is_wp_error( $result ) ) {
        return new WP_Error( 'delete_error', __( 'Could not delete section.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    return rest_ensure_response( array( 'success' => true, 'message' => __( 'Section deleted successfully.', 'dki-wiki' ) ) );
}

function dki_wiki_reorder_faq_sections( $request ) {
    $sections_order = $request->get_param( 'sectionsOrder' );

    foreach ( $sections_order as $index => $section_id ) {
        update_term_meta( $section_id, '_section_order', $index + 1 );
    }

    return rest_ensure_response( array( 'success' => true, 'message' => __( 'Sections reordered successfully.', 'dki-wiki' ) ) );
}

// FAQ Items API callbacks
function dki_wiki_get_faq_items( $request ) {
    $section_id = $request->get_param( 'section' );

    $args = array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num',
        'meta_key' => '_faq_order',
        'order' => 'ASC',
        'post_status' => 'publish',
    );

    if ( $section_id ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'faq_section',
                'field' => 'term_id',
                'terms' => $section_id,
            ),
        );
    }

    $posts = get_posts( $args );
    $formatted_items = array_map( 'dki_wiki_format_faq_item_for_api', $posts );
    
    return rest_ensure_response( $formatted_items );
}

function dki_wiki_create_faq_item( $request ) {
    $title = $request->get_param( 'title' );
    $content = $request->get_param( 'content' );
    $section_id = $request->get_param( 'sectionId' );

    $post_id = wp_insert_post( array(
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_type' => 'faq',
    ));

    if ( is_wp_error( $post_id ) ) {
        return new WP_Error( 'create_error', __( 'Could not create FAQ item.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    // Assign to section
    wp_set_post_terms( $post_id, array( $section_id ), 'faq_section' );

    // Set order for new FAQ item
    $existing_faqs = get_posts( array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => 'faq_section',
                'field' => 'term_id',
                'terms' => $section_id,
            ),
        ),
    ));
    
    $max_order = 0;
    foreach ( $existing_faqs as $faq_id ) {
        $order = get_post_meta( $faq_id, '_faq_order', true );
        if ( is_numeric( $order ) && $order > $max_order ) {
            $max_order = intval( $order );
        }
    }
    
    update_post_meta( $post_id, '_faq_order', $max_order + 1 );

    $created_post = get_post( $post_id );
    $formatted_item = dki_wiki_format_faq_item_for_api( $created_post );
    
    return rest_ensure_response( $formatted_item );
}

function dki_wiki_update_faq_item( $request ) {
    $id = $request->get_param( 'id' );
    $title = $request->get_param( 'title' );
    $content = $request->get_param( 'content' );
    $section_id = $request->get_param( 'sectionId' );

    $update_data = array( 'ID' => $id );
    if ( $title !== null ) {
        $update_data['post_title'] = $title;
    }
    if ( $content !== null ) {
        $update_data['post_content'] = $content;
    }

    $result = wp_update_post( $update_data );

    if ( is_wp_error( $result ) ) {
        return new WP_Error( 'update_error', __( 'Could not update FAQ item.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    // Update section if provided
    if ( $section_id !== null ) {
        wp_set_post_terms( $id, array( $section_id ), 'faq_section' );
    }

    $updated_post = get_post( $id );
    $formatted_item = dki_wiki_format_faq_item_for_api( $updated_post );
    
    return rest_ensure_response( $formatted_item );
}

function dki_wiki_delete_faq_item( $request ) {
    $id = $request->get_param( 'id' );

    $result = wp_delete_post( $id, true );

    if ( ! $result ) {
        return new WP_Error( 'delete_error', __( 'Could not delete FAQ item.', 'dki-wiki' ), array( 'status' => 500 ) );
    }

    return rest_ensure_response( array( 'success' => true, 'message' => __( 'FAQ item deleted successfully.', 'dki-wiki' ) ) );
}

function dki_wiki_reorder_faq_items( $request ) {
    $section_id = $request->get_param( 'sectionId' );
    $faqs_order = $request->get_param( 'faqsOrder' );

    foreach ( $faqs_order as $index => $faq_id ) {
        update_post_meta( $faq_id, '_faq_order', $index + 1 );
    }

    return rest_ensure_response( array( 'success' => true, 'message' => __( 'FAQ items reordered successfully.', 'dki-wiki' ) ) );
}
