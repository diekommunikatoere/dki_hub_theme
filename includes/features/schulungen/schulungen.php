<?php



// Add "read_schulungen" status to all users
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