<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// Remoce WP version from head
remove_action('wp_head', 'wp_generator');


// ----- START - Enqueue styles
//
function dki_wiki_enqueue_styles() {
    wp_enqueue_style( 'dki_wiki-style', get_stylesheet_directory_uri() . '/includes/css/styles.css', array(), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'dki_wiki_enqueue_styles' );

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
function fgd_add_favicon() {
    echo '
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#ffd144">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#ffd144">
        <meta name="msapplication-config" content="/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
    ';
}
add_action( 'wp_head', 'fgd_add_favicon' );
add_action( 'admin_head', 'fgd_add_favicon' );

// ----- END - Add favicon to public and admin area from WordPress root directory



// ----- START - Add lazy loading to "post-featured-image"
// 
add_action( 'elementor/frontend/widget/before_render', function( $widget ) {
    if( 'theme-post-featured-image' === $widget->get_name() ) {
        add_filter( 'wp_get_attachment_image_attributes', 'add_lazy_loading_to_images', 10, 3 );
    }
}, 10 );

function add_lazy_loading_to_images( $attr, $attachment, $size ) {
    $attr['loading'] = 'lazy';
    return $attr;
}

add_action( 'elementor/frontend/widget/after_render', function( $widget ) {
    if( 'theme-post-featured-image' === $widget->get_name() ) {
        remove_filter( 'wp_get_attachment_image_attributes', 'add_lazy_loading_to_images', 10, 3 );
    }
}, 10 );

// ----- END - lazy loading



// ----- START - Register Single Page page template
//
function register_single_page_template( $single_templates ) {
    $single_templates['/includes/templates/single-page.php'] = 'Single Page';
    return $single_templates;
}
add_filter( 'theme_page_templates', 'register_single_page_template' );

// ----- END - Register Single Post page template



// ----- START - Register Single Post page template
//
function register_single_post_template( $single_templates ) {
    $single_templates['/includes/templates/single-post.php'] = 'Single Post';
    return $single_templates;
}
add_filter( 'theme_post_templates', 'register_single_post_template' );

// ----- END - Register Single Post page template



// ----- START - Register custom text format in TinyMCE
//
require_once( __DIR__ . '/includes/utils/post_editor_styles.php' );

// ----- END - Register custom text format in TinyMCE



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
				'core/form',
				'core/form-input',
				'core/form-submission-notification',
				'core/form-submit-button',
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
				'core/social-link',
				'core/social-links',
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
add_action('wp_enqueue_scripts', 'schulungen_ajax_script');