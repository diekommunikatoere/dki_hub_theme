<?php
/**
 * Admin page for React-based FAQ Editor
 *
 * @package DKI Wiki Theme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Replace the existing FAQ reorder page with the new React-based editor
 */
function dki_wiki_create_faq_admin_menu() {
    // Add top-level FAQ menu with icon
    add_menu_page(
        __( 'FAQs', 'dki-wiki' ),  // Page title
        __( 'FAQs', 'dki-wiki' ),  // Menu title
        'edit_posts',  // Capability
        'faq-admin',  // Menu slug (parent)
        '__return_null',  // Blank callback for parent
        'dashicons-editor-help',  // Icon
        5  // Position
    );

    // Add the only submenu item: FAQ Editor
    add_submenu_page(
        'faq-admin',  // Parent slug
        __( 'FAQ Editor', 'dki-wiki' ),  // Page title
        __( 'FAQ Editor', 'dki-wiki' ),  // Menu title
        'edit_posts',  // Capability
        'faq-editor',  // Menu slug
        'dki_wiki_faq_editor_page_callback'  // Callback
    );
}
add_action( 'admin_menu', 'dki_wiki_create_faq_admin_menu', 10 );

/**
 * Redirect top-level FAQ menu click to FAQ Editor
 */
function dki_wiki_redirect_faq_parent() {
    $screen = get_current_screen();
    if ( is_admin() && ( isset( $_GET['page'] ) && $_GET['page'] === 'faq-admin' ) ) {
        wp_redirect( admin_url( 'admin.php?page=faq-editor' ) );
        exit;
    }
}
add_action( 'admin_init', 'dki_wiki_redirect_faq_parent' );

/**
 * Force parent and submenu file for active highlighting on FAQ Editor page
 */
function dki_wiki_fix_faq_menu_active( $parent_file ) {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'admin_page_faq-editor' ) {
        return 'faq-editor';  // Set parent to top-level
    }
    return $parent_file;
}
add_filter( 'parent_file', 'dki_wiki_fix_faq_menu_active' );

function dki_wiki_fix_faq_submenu_active( $submenu_file ) {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'admin_page_faq-editor' ) {
        return 'faq-editor';  // Set submenu to editor
    }
    return $submenu_file;
}
add_filter( 'submenu_file', 'dki_wiki_fix_faq_submenu_active' );

/**
 * Enqueue React FAQ Editor assets
 */
function dki_wiki_enqueue_faq_editor_assets( $hook ) {
    // Only load on the FAQ editor page
    if ( $hook !== 'faqs_page_faq-editor' ) {
        return;
    }

    // Enqueue WordPress dependencies that React app needs
    wp_enqueue_script( 'wp-api-fetch' );
    wp_enqueue_script( 'wp-i18n' );
    
    // Enqueue React and ReactDOM from WordPress
    wp_enqueue_script( 'react' );
    wp_enqueue_script( 'react-dom' );

    // Check if built React app exists
    $js_file = get_stylesheet_directory() . '/includes/assets/js/admin/faq-editor/faq-editor.js';
    $css_file = get_stylesheet_directory() . '/includes/assets/css/modules/admin/faq-editor.css';
    
    if ( file_exists( $js_file ) ) {
        wp_enqueue_script(
            'dki-faq-editor',
            get_stylesheet_directory_uri() . '/includes/assets/js/admin/faq-editor/faq-editor.js',
            array( 'react', 'react-dom', 'wp-api-fetch', 'wp-i18n' ),
            filemtime( $js_file ),
            true
        );

        // Pass configuration to React app
        wp_localize_script( 'dki-faq-editor', 'dkiFAQEditor', array(
            'apiUrl' => home_url( '/wp-json' ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'currentUser' => array(
                'id' => get_current_user_id(),
                'name' => wp_get_current_user()->display_name,
                'capabilities' => array(
                    'edit_posts' => current_user_can( 'edit_posts' ),
                    'delete_posts' => current_user_can( 'delete_posts' ),
                ),
            ),
            'labels' => array(
                'faqEditor' => __( 'FAQ Editor', 'dki-wiki' ),
                'sections' => __( 'Sections', 'dki-wiki' ),
                'faqItems' => __( 'FAQ Items', 'dki-wiki' ),
                'addSection' => __( 'Add Section', 'dki-wiki' ),
                'addFAQItem' => __( 'Add FAQ Item', 'dki-wiki' ),
                'edit' => __( 'Edit', 'dki-wiki' ),
                'delete' => __( 'Delete', 'dki-wiki' ),
                'save' => __( 'Save', 'dki-wiki' ),
                'cancel' => __( 'Cancel', 'dki-wiki' ),
                'loading' => __( 'Loading...', 'dki-wiki' ),
                'error' => __( 'Error', 'dki-wiki' ),
                'success' => __( 'Success', 'dki-wiki' ),
                'confirmDelete' => __( 'Are you sure you want to delete this item?', 'dki-wiki' ),
                'unsavedChanges' => __( 'You have unsaved changes. Are you sure you want to leave?', 'dki-wiki' ),
            ),
        ));

        if ( file_exists( $css_file ) ) {
            wp_enqueue_style(
                'dki-faq-editor',
                get_stylesheet_directory_uri() . '/includes/assets/css/modules/admin/faq-editor.css',
                array(),
                filemtime( $css_file )
            );
        }
    }

    // Enqueue admin styles for WordPress styling
    wp_enqueue_style( 'wp-admin' );
    wp_enqueue_style( 'common' );
    wp_enqueue_style( 'forms' );
    wp_enqueue_style( 'admin-menu' );
    wp_enqueue_style( 'dashboard' );
}
add_action( 'admin_enqueue_scripts', 'dki_wiki_enqueue_faq_editor_assets' );

/**
 * FAQ Editor page callback
 */
function dki_wiki_faq_editor_page_callback() {
    // Check permissions
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'dki-wiki' ) );
    }

    $js_file = get_stylesheet_directory() . '/includes/assets/js/admin/faq-editor/faq-editor.js';

    ?>
    <div class="wrap">
        <div id="dki-faq-editor-root">
            <?php if ( ! file_exists( $js_file ) ) : ?>
                <div class="notice notice-warning">
                    <h2><?php _e( 'FAQ Editor Not Built Yet', 'dki-wiki' ); ?></h2>
                    <p><?php _e( 'The React FAQ editor needs to be built first. Please run the following commands in the theme directory:', 'dki-wiki' ); ?></p>
                    <pre style="background: #f1f1f1; padding: 15px; border-radius: 4px; font-family: monospace;"></pre>
                    <p><?php _e( 'After building, refresh this page to load the FAQ editor.', 'dki-wiki' ); ?></p>
                    
                    <h3><?php _e( 'Fallback: Use Current FAQ Management', 'dki-wiki' ); ?></h3>
                    <p><?php _e( 'You can continue using the current FAQ management system:', 'dki-wiki' ); ?></p>
                    <ul>
                        <li><a href="<?php echo admin_url( 'edit.php?post_type=faq' ); ?>"><?php _e( 'Manage FAQ Items', 'dki-wiki' ); ?></a></li>
                        <li><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=faq_section&post_type=faq' ); ?>"><?php _e( 'Manage FAQ Sections', 'dki-wiki' ); ?></a></li>
                        <li><a href="<?php echo admin_url( 'edit.php?post_type=faq&page=faq-reorder' ); ?>"><?php _e( 'Reorder FAQ Items', 'dki-wiki' ); ?></a></li>
                    </ul>
                </div>
            <?php else : ?>
                <!-- React app will mount here -->
                <div style="display: flex; align-items: center; justify-content: center; min-height: 400px; color: #666;">
                    <div>
                        <div class="spinner is-active" style="float: none; margin: 0 auto 20px;"></div>
                        <p><?php _e( 'Loading FAQ Editor...', 'dki-wiki' ); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        /* Ensure the React app has proper styling context */
        #dki-faq-editor-root {
            margin-top: 20px;
        }
        
        /* Hide the loading spinner once React loads */
        .faq-editor + .spinner {
            display: none;
        }
    </style>
    <?php
}

/**
 * Add admin notice to encourage using the new FAQ Editor
 */
function dki_wiki_faq_editor_admin_notice() {
    $screen = get_current_screen();
    
    // Show notice on FAQ-related pages but not on the new editor page
    if ( $screen && 
         ( $screen->post_type === 'faq' || $screen->taxonomy === 'faq_section' ) &&
         $screen->id !== 'toplevel_page_faq-editor' 
    ) {
        $js_file = get_stylesheet_directory() . '/includes/assets/js/admin/faq-editor/faq-editor.js';
        
        if ( file_exists( $js_file ) ) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php _e( 'New FAQ Editor Available!', 'dki-wiki' ); ?></strong>
                    <?php _e( 'Try our new all-in-one FAQ editor with drag-and-drop functionality.', 'dki-wiki' ); ?>
                    <a href="<?php echo admin_url( 'admin.php?page=faq-editor' ); ?>" class="button button-secondary" style="margin-left: 10px;">
                        <?php _e( 'Open FAQ Editor', 'dki-wiki' ); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
}
add_action( 'admin_notices', 'dki_wiki_faq_editor_admin_notice' );
