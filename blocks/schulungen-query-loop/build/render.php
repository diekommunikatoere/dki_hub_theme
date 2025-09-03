<?php
	/**
	 * Plugin Name:       Schulungen Query Loop
	 */
	
	// Ensure this file is being included by a parent file
	if (!defined('ABSPATH')) exit;

	// Retrieve URL parameters and set read_status for schulung elements before rendering the page
	add_action('template_redirect', 'handle_schulung_status_change');

	function handle_schulung_status_change() {
		if (isset($_GET['schulung_id']) && isset($_GET['mark_as']) && wp_verify_nonce($_GET['_wpnonce'], 'schulung_status_change')) {
			$schulung_id = intval($_GET['schulung_id']);
			$mark_as = sanitize_text_field($_GET['mark_as']);

			set_read_status($schulung_id, $mark_as);
			wp_safe_redirect(remove_query_arg(array('schulung_id', 'mark_as', '_wpnonce')));
			exit;
		}
	}

	// Set read status for schulung with post_id
	function set_read_status($post_id, $mark_as) {
		$user_id = get_current_user_id();
		if (!$user_id) return;

		$read_status = get_user_meta($user_id, 'read_schulungen', true);

		if (!is_array($read_status)) {
			$read_status = array();
		}

		if ($mark_as === 'read') {
			if (!in_array($post_id, $read_status)) {
				$read_status[] = $post_id;
			}
		} else {
			$read_status = array_diff($read_status, array($post_id));
		}

		update_user_meta($user_id, 'read_schulungen', array_unique($read_status));
	}

	// Get all posts with post_type 'docs' and taxonomy 'doc_category' with term_id 42
	$args = array(
		'post_type' => 'docs',
		'tax_query' => array(
			array(
				'taxonomy' => 'doc_category',
				'field' => 'term_id',
				'terms' => 42
			)
		),
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC'
	);
	$schulungen = get_posts($args);

	$readSchulungen = get_user_meta(get_current_user_id(), 'read_schulungen', true);
	if (!is_array($readSchulungen)) {
		$readSchulungen = array();
	}

	// Check if user has read the schulung with post_id
	function get_read_status($post_id) {
		$read_status = get_user_meta(get_current_user_id(), 'read_schulungen', true);

		if (!is_array($read_status)) {
			return false;
		}

		return in_array($post_id, $read_status);
	}

	$eyeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>';

	$checkmarkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/></svg>';

	$checkmarkDoneIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>';

	$removeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>';

?>

<h2>Schulungen</h2>

<div class="schulungen-wrapper todo">
    <h3>ToDo</h3>
    <div class="schulungen-list">
        <?php foreach ($schulungen as $schulung) : ?>
            <?php if (in_array($schulung->ID, $readSchulungen)) continue; ?>
            <?php $read_status = get_read_status($schulung->ID); ?>
            <?php $read_status_button = $read_status ? 'Als ungelesen markieren' : 'Als gelesen markieren'; ?>
            <div class="schulung" data-read-status="read" data-user-id="<?php echo get_current_user_id(); ?>" data-post-id="<?php echo $schulung->ID; ?>">
                <span class="schulung-checkmark">
                    <?php echo $checkmarkIcon; ?>
                </span>
                <p class="schulung-title"><?php echo esc_html($schulung->post_title); ?></p>
                <a href="<?php echo esc_url(get_permalink($schulung->ID)); ?>" class="schulung-view" title="Schulung ansehen">
                    <?php echo $eyeIcon; ?>
                </a>
                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('schulung_id' => $schulung->ID, 'mark_as' => 'read'), get_permalink()), 'schulung_status_change', '_wpnonce')); ?>"
                   class="schulung-read-status mark-as-read"
                   data-post-id="<?php echo $schulung->ID; ?>"
                   data-set-read-status-to="read"
                   title="<?php echo esc_attr($read_status_button); ?>">
                    <span class="icon set-to-read show">
						<?php echo $checkmarkIcon; ?>
					</span>
					<span class="icon set-to-unread">
						<?php echo $removeIcon; ?>
					</span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="schulungen-wrapper done">
    <h3>Done</h3>
    <div class="schulungen-list">
        <?php foreach ($schulungen as $schulung) : ?>
            <?php if (!in_array($schulung->ID, $readSchulungen)) continue; ?>
            <?php $read_status = get_read_status($schulung->ID); ?>
            <?php $read_status_button = $read_status ? 'Als ungelesen markieren' : 'Als gelesen markieren'; ?>
            <div class="schulung" data-read-status="unread" data-user-id="<?php echo get_current_user_id(); ?>" data-post-id="<?php echo $schulung->ID; ?>">
                <span class="schulung-checkmark">
                    <?php echo $checkmarkDoneIcon; ?>
                </span>
                <p class="schulung-title"><?php echo esc_html($schulung->post_title); ?></p>
                <a href="<?php echo esc_url(get_permalink($schulung->ID)); ?>" class="schulung-view" title="Schulung ansehen">
                    <?php echo $eyeIcon; ?>
                </a>
                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('schulung_id' => $schulung->ID, 'mark_as' => 'unread'), get_permalink()), 'schulung_status_change', '_wpnonce')); ?>"
                   class="schulung-read-status mark-as-unread"
                   data-post-id="<?php echo $schulung->ID; ?>"
                   data-set-read-status-to="unread"
                   title="<?php echo esc_attr($read_status_button); ?>">
                    <span class="icon set-to-read">
						<?php echo $checkmarkIcon; ?>
					</span>
					<span class="icon set-to-unread show">
						<?php echo $removeIcon; ?>
					</span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>