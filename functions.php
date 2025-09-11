<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Include CPT registrations
require_once get_stylesheet_directory() . '/includes/utils/register-cpt-faq.php';
require_once get_stylesheet_directory() . '/includes/utils/admin-faq-reorder-page.php';

// Include new React-based FAQ editor
require_once get_stylesheet_directory() . '/includes/utils/faq-rest-api.php';
require_once get_stylesheet_directory() . '/includes/utils/faq-editor-admin-page.php';

// Initialize default section orders on theme setup
function dki_wiki_initialize_section_orders() {
    dki_wiki_bulk_set_default_section_orders();
}
add_action( 'after_setup_theme', 'dki_wiki_initialize_section_orders' );


// Remove WP version from head
remove_action('wp_head', 'wp_generator');


// ----- START - Enqueue styles
//
function dki_wiki_enqueue_public_styles() {
    wp_enqueue_style( 'dki_wiki-style', get_stylesheet_directory_uri() . '/includes/css/styles.css', array(), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'dki_wiki_enqueue_public_styles' );


function dki_wiki_enqueue_dashboard_styles() {
    wp_enqueue_style('dki_wiki_admin-style', get_stylesheet_directory_uri() . '/includes/css/modules/admin/variables.css', array(), '1.0.0', 'all');
    wp_enqueue_style('dki_wiki_faq-style', get_stylesheet_directory_uri() . '/includes/css/modules/admin/faq_admin.css', array(), '1.0.0', 'all');
    wp_enqueue_style('dki_wiki_faq-editor-style', get_stylesheet_directory_uri() . '/includes/js/admin/faq-editor/faq-editor.css', array(), '1.0.0', 'all');
}
add_action( 'admin_enqueue_scripts', 'dki_wiki_enqueue_dashboard_styles');

// ----- END - Enqueue styles



// ----- START - Enqueue block styles
//
/* function dki_wiki_enqueue_block_styles() {
    wp_enqueue_block_style( 'betterdocs/searchbox', array(
        'handle' => 'betterdocs-searchbox',
        'src'    => get_stylesheet_directory_uri() . '/includes/css/modules/betterdocs.css',
        'path'   => get_stylesheet_directory_uri() . '/includes/css/modules/betterdocs.css',
    ) );
}
add_action( 'init', 'dki_wiki_enqueue_block_styles' ); */



// ----- START - Scan all js files in /includes/js/ and enqueue them as modules
//
function dki_wiki_enqueue_scripts() {
    $js_files = glob( get_stylesheet_directory() . '/includes/js/*.js' );
    foreach( $js_files as $js_file ) {
        $js_file_name = basename( $js_file, '.js' );
        wp_enqueue_script_module( $js_file_name, get_stylesheet_directory_uri() . '/includes/js/' . $js_file_name . '.js', array(), '1.0.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'dki_wiki_enqueue_scripts' );

// ----- END - Enqueue scripts



// ----- START - Enable Admin JS modules
//
function dki_wiki_enqueue_admin_scripts() {
    $admin_js_files = glob( get_stylesheet_directory() . '/includes/js/admin/*.js' );
    foreach( $admin_js_files as $admin_js_file ) {
        $admin_js_file_name = basename( $admin_js_file, '.js' );
        wp_enqueue_script_module( $admin_js_file_name, get_stylesheet_directory_uri() . '/includes/js/admin/' . $admin_js_file_name . '.js', array(), '1.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'dki_wiki_enqueue_admin_scripts' );



// ----- START - Allow SVG uploads
//
function fgd_mime_types( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'fgd_mime_types' );

// ----- END - Allow SVG uploads



// ----- START - Add favicon to public and admin area from WordPress root directory
//
function dki_hub_add_favicon() {
    echo '
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#ffffff">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-config" content="/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
    ';
}
add_action( 'wp_head', 'dki_hub_add_favicon' );
add_action( 'admin_head', 'dki_hub_add_favicon' );

// ----- END - Add favicon to public and admin area from WordPress root directory



// START ----- Search all folders in the 'blocks' directory for block.json files and register them
//

function dki_wiki_blocks_register_blocks() {
    $blocks_dir = __DIR__ . '/blocks';
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

// END ----- Search all folders in the 'blocks' directory for block.json files and register them


// START ----- Set allowed blocks for users in group "team"
// https://developer.wordpress.org/reference/hooks/allowed_block_types_all/

function example_filter_allowed_block_types_when_post_provided( $allowed_block_types, $editor_context ) {
	$user = wp_get_current_user();
	$allowed_role = array( 'team', 'um_team' );
	if ( array_intersect( $allowed_role, $user->roles ) ) {
		if ( ! empty( $editor_context->post ) ) {
			return array( 
				'core/audio',
				'core/button',
				'core/buttons',
				'core/code',
				'core/column',
				'core/columns',
				'core/cover',
				'core/details',
				'core/embed',
				'core/file',
				'core/footnotes',
				'core/gallery',
				'core/group',
				'core/heading',
				'core/image',
				'core/list',
				'core/list-item',
				'core/media-text',
				'core/navigation-link',
				'core/paragraph',
				'core/preformatted',
				'core/pullquote',
				'core/quote',
				'core/separator',
				'core/spacer',
				'core/table',
				'core/verse',
				'core/video'
			);
		}
		return $allowed_block_types;
	}
}
add_filter( 'allowed_block_types_all', 'example_filter_allowed_block_types_when_post_provided', 10, 2 );

// END ----- Set allowed blocks for users in group "team"



// START ----- Add "read_schulungen" status to all users
//

function add_read_schulungen_to_all_users() {
    $users = get_users();
    foreach ($users as $user) {
        if( get_user_meta($user->ID, 'read_schulungen', true) ) {
            continue;
        }
        update_user_meta($user->ID, 'read_schulungen', array());
    }
}
add_action('init', 'add_read_schulungen_to_all_users');

// END ----- Add "read_schulungen" status to all users



// Handle login form submission
function perform_login() {
	error_log('Login attempt received');

	$formData = $_POST['formData'];

	error_log('Login attempt username: ' . $formData['username']);

	// Verify nonce
	if (!isset($formData['nonce']) || !wp_verify_nonce($formData['nonce'], 'login_form_nonce')) {
		error_log('Login nonce verification failed');
		wp_send_json_error('Login security check failed');
	}

	// Sanitize input
	$username = sanitize_user($formData['username']);
	$password = $formData['password'];
	$remember = isset($formData['remember-me']);

	$user = wp_signon(array(
		'user_login' => $username,
		'user_password' => $password,
		'remember' => $remember
	));
	
	if (is_wp_error($user)) {
		error_log('Login failed for user: ' . $username);
		wp_send_json_error($user->get_error_message());
	} else {
		error_log('Login successful for user: ' . $username);
		wp_clear_auth_cookie();
		wp_set_auth_cookie($user->ID, isset($_POST['remember-me']), true);
		wp_send_json_success('Login successful');
	}

	wp_die();
}

function check_attempted_login($user, $username, $password) {
    if (get_transient('attempted_login')) {
        $datas = get_transient('attempted_login');
        if ($datas['tried'] >= 3) {
            $until = get_option('_transient_timeout_' . 'attempted_login');
            $time = time_to_go($until);
            return new WP_Error('too_many_tried', sprintf(__('<strong>Fehler</strong>: Das hast das Authentifizierungslimit erreicht. Versuche es in %1$s wieder.'), $time));
        }
    }
    return $user;
}
add_filter('authenticate', 'check_attempted_login', 30, 3);

function login_failed($username) {
    if (get_transient('attempted_login')) {
        $datas = get_transient('attempted_login');
        $datas['tried']++;
        if ($datas['tried'] <= 3)
            set_transient('attempted_login', $datas, 300);
    } else {
        $datas = array(
            'tried' => 1
        );
        set_transient('attempted_login', $datas, 300);
    }
}
add_action('wp_login_failed', 'login_failed', 10, 1);

add_action('wp_ajax_perform_login', 'perform_login');
add_action('wp_ajax_nopriv_perform_login', 'perform_login');


// Enqueue login form script
function login_ajax_script($nonce) {
	$nonce = wp_create_nonce('login_form_nonce');
	wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri() . '/includes/js/ajax-handler.js', array('jquery'), null, true );
	wp_localize_script( 
		'ajax-script', 
		'login_ajax_object', 
		array( 
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => $nonce,
			'home_url' => home_url()
		));
}
add_action( 'wp_enqueue_scripts', 'login_ajax_script' );



// Handle updating user meta for read schulungen
function schulungen_mark_as_read_unread() {
    // Get current user ID
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(array('message' => 'User not logged in', 'status' => 'error'));
        return;
    }

    if (!isset($_POST['formData']) || !is_array($_POST['formData'])) {
        wp_send_json_error(array('message' => 'Invalid form data', 'status' => 'error'));
        return;
    }

    $formData = $_POST['formData'];

    if (!isset($formData['schulungId']) || !isset($formData['setReadStatusTo']) || !isset($formData['nonce'])) {
        wp_send_json_error(array('message' => 'Missing parameters', 'status' => 'error'));
        return;
    }

    $schulung_id = intval($formData['schulungId']);
    $marked_as = sanitize_text_field($formData['setReadStatusTo']);
    $nonce = sanitize_text_field($formData['nonce']);

    // Verify nonce
    if (!wp_verify_nonce($nonce, 'schulung_mark_as_' . $marked_as . '_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed', 'status' => 'error'));
        return;
    }

    // Get current read schulungen
    $read_schulungen = get_user_meta($user_id, 'read_schulungen', true);

    // Check if read schulungen is an array
    if (!is_array($read_schulungen)) {
        $read_schulungen = array();
    }

    // Update read_schulungen based on marked_as status
    if ($marked_as === 'read' && !in_array($schulung_id, $read_schulungen)) {
        $read_schulungen[] = $schulung_id;
    } elseif ($marked_as === 'unread') {
        $read_schulungen = array_diff($read_schulungen, array($schulung_id));
    }

    // Update user meta
    $updated = update_user_meta($user_id, 'read_schulungen', array_unique($read_schulungen));

    if ($updated !== false) {
        wp_send_json_success(array('message' => 'Schulung status updated successfully', 'status' => $marked_as));
    } else {
        wp_send_json_error(array('message' => 'Failed to update schulung status', 'status' => 'error', 'error' => 'Database update failed'));
    }
}
add_action('wp_ajax_schulungen_mark_as_read_unread', 'schulungen_mark_as_read_unread');
add_action('wp_ajax_nopriv_schulungen_mark_as_read_unread', 'schulungen_mark_as_read_unread');

function schulungen_ajax_script() {
    $nonce_mark_as_read_nonce = wp_create_nonce('schulung_mark_as_read_nonce');
    $nonce_mark_as_unread_nonce = wp_create_nonce('schulung_mark_as_unread_nonce');

    wp_enqueue_script('schulungen-ajax-script', get_stylesheet_directory_uri() . '/includes/js/ajax-handler.js', array('jquery'), null, true);
    wp_localize_script(
        'schulungen-ajax-script',
        'schulungen_ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce_mark_as_read_nonce' => $nonce_mark_as_read_nonce,
            'nonce_mark_as_unread_nonce' => $nonce_mark_as_unread_nonce
        )
    );
}
add_action('wp_enqueue_scripts', 'schulungen_ajax_script' );

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

// Enqueue admin FAQ reorder script
function dki_wiki_enqueue_admin_faq_reorder( $hook ) {
    $screen = get_current_screen();
    $is_faq_list = ( $screen->post_type === 'faq' && $screen->base === 'edit' );
    $is_section_list = ( $screen->taxonomy === 'faq_section' && $screen->base === 'edit-tags' );
    $is_reorder_page = ( $screen->id === 'faq_page_faq-reorder' );

    if ( $is_faq_list || $is_section_list || $is_reorder_page ) {
        if ( $is_reorder_page ) {
            // Enqueue for reorder page
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'admin-faq-reorder', get_stylesheet_directory_uri() . '/includes/js/admin/admin-faq-reorder.js', array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
            wp_localize_script( 'admin-faq-reorder', 'faqReorder', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'faq_reorder_nonce' ),
                'isReorderPage' => true,
                'saveAction' => 'update_faq_reorder_bulk'
            ) );
            wp_enqueue_style( 'admin-faq-reorder-style', get_stylesheet_directory_uri() . '/includes/css/admin-faq-reorder.css', array(), '1.0.0' );
        } else {
            // Existing enqueue for list views, but disable drag in JS
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'admin-faq-reorder', get_stylesheet_directory_uri() . '/includes/js/admin-faq-reorder.js', array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
            wp_localize_script( 'admin-faq-reorder', 'faqReorder', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'faq_reorder_nonce' ),
                'isReorderPage' => false
            ) );
        }
        // Add order column to FAQ list
        add_filter( 'manage_faq_posts_columns', 'dki_wiki_faq_order_column' );
        add_action( 'manage_faq_posts_custom_column', 'dki_wiki_faq_order_column_content', 10, 2 );
    }
}
add_action( 'admin_enqueue_scripts', 'dki_wiki_enqueue_admin_faq_reorder' );

// Add order column to FAQ admin list
function dki_wiki_faq_order_column( $columns ) {
    $columns['order'] = __( 'Reihenfolge', 'dki-wiki' );
    return $columns;
}

// Content for order column
function dki_wiki_faq_order_column_content( $column, $post_id ) {
    if ( $column === 'order' ) {
        $order = get_post_meta( $post_id, '_faq_order', true );
        echo esc_html( $order ?: 'N/A' );
    }
}

// AJAX handler for updating FAQ order
function dki_wiki_update_faq_order() {
    check_ajax_referer( 'faq_reorder_nonce', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Insufficient permissions' );
    }

    $order = isset( $_POST['order'] ) ? (array) $_POST['order'] : array();
    foreach ( $order as $index => $post_id ) {
        update_post_meta( $post_id, '_faq_order', $index + 1 );
    }

    wp_send_json_success( 'Order updated' );
}

// New AJAX handler for bulk reorder on subpage (sections and nested FAQs)
function dki_wiki_update_faq_reorder_bulk() {
    check_ajax_referer( 'faq_reorder_nonce', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Insufficient permissions' );
    }

    $sections_order = isset( $_POST['sections_order'] ) ? (array) $_POST['sections_order'] : array();
    $faqs_per_section = isset( $_POST['faqs_per_section'] ) ? (array) $_POST['faqs_per_section'] : array();

    // Update sections order
    foreach ( $sections_order as $index => $term_id ) {
        update_term_meta( $term_id, '_section_order', $index + 1 );
    }

    // Update FAQs per section
    foreach ( $faqs_per_section as $section_id => $faq_order ) {
        if ( is_array( $faq_order ) ) {
            foreach ( $faq_order as $index => $post_id ) {
                update_post_meta( $post_id, '_faq_order', $index + 1 );
            }
        }
    }

    wp_send_json_success( __( 'Reihenfolge gespeichert.', 'dki-wiki' ) );
}
add_action( 'wp_ajax_update_faq_order', 'dki_wiki_update_faq_order' );
add_action( 'wp_ajax_update_faq_reorder_bulk', 'dki_wiki_update_faq_reorder_bulk' );

// For sections: Add order field to term edit form
function dki_wiki_faq_section_order_field( $taxonomy ) {
    if ( $taxonomy['name'] === 'faq_section' ) {
        wp_add_inline_script( 'admin-faq-reorder', 'console.log("Section order ready");' ); // Placeholder for term sortable
    }
}
add_action( 'edit_terms_fields', 'dki_wiki_faq_section_order_field' );

// Metabox for section order (simple number field for terms)
function dki_wiki_edit_faq_section_form_fields( $taxonomy ) {
    //
}
add_action( 'faq_section_edit_form_fields', 'dki_wiki_faq_section_order_edit', 10, 2 );

function dki_wiki_faq_section_order_edit( $term, $taxonomy ) {
    if ( $taxonomy === 'faq_section' ) {
        $order = get_term_meta( $term->term_id, '_section_order', true );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="section_order"><?php _e( 'Reihenfolge', 'dki-wiki' ); ?></label></th>
            <td>
                <input type="number" name="section_order" id="section_order" value="<?php echo esc_attr( $order ); ?>" />
                <p class="description"><?php _e( 'Benutzerdefinierte Reihenfolge für diesen Abschnitt.', 'dki-wiki' ); ?></p>
            </td>
        </tr>
        <?php
    }
}
add_action( 'faq_section_edit_form_fields', 'dki_wiki_faq_section_order_edit', 10, 2 );

// Save section order
function dki_wiki_save_faq_section_order( $term_id ) {
    if ( isset( $_POST['section_order'] ) ) {
        $order = intval( $_POST['section_order'] );
        update_term_meta( $term_id, '_section_order', $order );
    }
}
add_action( 'edited_faq_section', 'dki_wiki_save_faq_section_order' );
add_action( 'create_faq_section', 'dki_wiki_save_faq_section_order' );

// Add FAQ reorder subpage to admin menu
function dki_wiki_add_faq_reorder_submenu() {
    add_submenu_page(
        'edit.php?post_type=faq',  // Parent slug
        __( 'Reihenfolge anpassen', 'dki-wiki' ),  // Page title
        __( 'Reihenfolge', 'dki-wiki' ),  // Menu title
        'edit_posts',  // Capability
        'faq-reorder',  // Menu slug
        'dki_wiki_faq_reorder_page'  // Callback
    );
}
add_action( 'admin_menu', 'dki_wiki_add_faq_reorder_submenu' );