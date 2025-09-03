<?php
	/**
	 * Plugin Name:       Schulungen Read Status Widget
	 */
?>

<?php

	// Retrieve URL parameters and set read_status for schulung elements before rendering the page

	// On page load retrieve the url paramters "schulung_id" and "mark_as" and call set_read_status()
	if (isset($_GET['mark_as'])) {
		$mark_as = $_GET['mark_as'];

		set_read_status($mark_as);
	}

	// Set read status for schulung with post_id
	function set_read_status($mark_as) {
		$read_status = get_user_meta(get_current_user_id(), 'read_schulungen', true);
		$post_id = get_the_ID();

		if (!$read_status || !is_array($read_status)) {
			$read_status = array();
		}

		if ($mark_as === 'read') {
			if(!in_array($post_id, $read_status)){
				$read_status[] = $post_id;
			}
		} else {
			$read_status = array_diff($read_status, array($post_id));
		}

		update_user_meta(get_current_user_id(), 'read_schulungen', $read_status);
	}
?>

<?php

	// Retrieve URL parameters and set read_status for schulung elements before rendering the page

	// On page load retrieve the url paramters "schulung_id" and "mark_as" and call set_read_status_for_all()
	if (isset($_GET['mark_for_all_as'])) {
		$mark_for_all_as = $_GET['mark_for_all_as'];

		set_read_status_for_all($mark_for_all_as);
	}

	// Set read status of all users for schulung with post_id
	function set_read_status_for_all($mark_for_all_as) {
		$all_users = get_users();

		foreach ($all_users as $user) {
			$read_status = get_user_meta($user->ID, 'read_schulungen', true);
			$post_id = get_the_ID();

			if (!$read_status || !is_array($read_status)) {
				$read_status = array();
			}

			// Remove post_id from read_status array
			$read_status = array_diff($read_status, array($post_id));

			update_user_meta($user->ID, 'read_schulungen', $read_status);
		}
	}
?>

<?php

	// Get current user
	$current_user = wp_get_current_user();

	// Check if user has roles 'administrator' or 'um_moderator'
	$is_admin = in_array('administrator', $current_user->roles);
	$is_moderator = in_array('um_moderator', $current_user->roles);

	// Check if current post is post_type 'docs' and has taxonomy 'doc_category' with term_id 42
	$args = array(
		'post_type' => 'docs',
		'tax_query' => array(
			array(
				'taxonomy' => 'doc_category',
				'field' => 'term_id',
				'terms' => 42
			)
		)
	);
	$schulungen = get_posts($args);

	// Get the current post
	$post = get_post();

	// $post is in the array $schulungen
	$is_schulung = in_array($post, $schulungen);

	$readSchulungen = get_user_meta(get_current_user_id(), 'read_schulungen', true);
	
	// Check if user has read the schulung with post_id
	function get_read_status($post_id) {
		$read_status = get_user_meta(get_current_user_id(), 'read_schulungen', true);

		if (!$read_status || !is_array($read_status)) {
			return false;
		}

		$is_read = in_array($post_id, $read_status);

		return $is_read;
	}

	$read_status_text = get_read_status($post->ID) ? 'read' : 'unread';
	$read_status_text_de = get_read_status($post->ID) ? 'gelesen' : 'ungelesen';
	$read_status_headline = get_read_status($post->ID) ? 'Du hast diesen Artikel schon gelesen' : 'Du hast diesen Artikel noch nicht gelesen';
	$read_status_to_set = get_read_status($post->ID) ? 'unread' : 'read';
	$read_status_to_set_de = get_read_status($post->ID) ? 'ungelesen' : 'gelesen';

	/**
	 * ICONS
	 */

	$eyeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>';

	$checkmarkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/></svg>';

	$checkmarkDoneIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>';

	$removeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>';

	$icon_current = $read_status_to_set === 'read' ? $removeIcon : $checkmarkDoneIcon;
	$icon_to_set = $read_status_to_set === 'read' ? $checkmarkDoneIcon : $removeIcon;

	/**/
?>

<?php if($is_schulung): ?>
	<div class="schulungen-read-status-wrapper <?php echo $read_status_text ?>">
		<div class="mark-as-read-wrapper">
			<div class="header">
				<span class="schulung-checkmark">
					<?php echo $icon_current; ?>
				</span>
				<h5><?php echo $read_status_headline; ?></h5>
			</div>
			<a class="schulung-read-status" title="Als <?php echo $read_status_to_set_de ?> markieren" href="?mark_as=<?php echo $read_status_to_set ?>">
				<?php echo $icon_to_set; ?>
				Als <?php echo $read_status_to_set_de ?> markieren
			</a>
		</div>
		<?php if($is_admin || $is_moderator): ?>
			<div class="admin-mark-as-unread">
				<div class="header">
					<h6>Diesen Artikel für alle als ungelesen markieren</h6>
				</div>
				<a class="schulung-read-status" title="Für alle als ungelesen markieren" href="?mark_for_all_as=unread">
					<?php echo $removeIcon; ?>
					Für alle als ungelesen markieren
				</a>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>