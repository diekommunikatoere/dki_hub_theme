<?php
	/**
	 * Block Name:        Header Navigation
	 * Description:       Displays a navigation menu with user profile dropdown containing links to profile, revisions, onboardings and logout.
	 */

	// Get the selected navigation menu ID from block attributes
	$navigation_menu_id = $attributes["selectedMenuId"];
	$menu_items = $attributes["menuItems"];

	// Get current user
	$user = wp_get_current_user();

	// Get user ID
	$user_id = $user->ID;

	// Get user role
	$user_role = $user->roles[0];

	// Format user role
	$formatted_user_role = ($user_role == "administrator") ? "Admin" : (($user_role == "um_team") ? "Team" : (($user_role == "um_moderator") ? "Moderator" : null));

	// Get user display name
	$user_display_name = $user->display_name;

	// Get user avatar
	$user_avatar = get_avatar_url($user_id);

	// Get current url
	$currentUrl = $_SERVER['REQUEST_URI'];

	$userIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';

	$revisionIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M315 315l158.4-215L444.1 70.6 229 229 315 315zm-187 5s0 0 0 0l0-71.7c0-15.3 7.2-29.6 19.5-38.6L420.6 8.4C428 2.9 437 0 446.2 0c11.4 0 22.4 4.5 30.5 12.6l54.8 54.8c8.1 8.1 12.6 19 12.6 30.5c0 9.2-2.9 18.2-8.4 25.6L334.4 396.5c-9 12.3-23.4 19.5-38.6 19.5L224 416l-25.4 25.4c-12.5 12.5-32.8 12.5-45.3 0l-50.7-50.7c-12.5-12.5-12.5-32.8 0-45.3L128 320zM7 466.3l63-63 70.6 70.6-31 31c-4.5 4.5-10.6 7-17 7L24 512c-13.3 0-24-10.7-24-24l0-4.7c0-6.4 2.5-12.5 7-17z"/></svg>';

	$schulungenIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M320 32c-8.1 0-16.1 1.4-23.7 4.1L15.8 137.4C6.3 140.9 0 149.9 0 160s6.3 19.1 15.8 22.6l57.9 20.9C57.3 229.3 48 259.8 48 291.9l0 28.1c0 28.4-10.8 57.7-22.3 80.8c-6.5 13-13.9 25.8-22.5 37.6C0 442.7-.9 448.3 .9 453.4s6 8.9 11.2 10.2l64 16c4.2 1.1 8.7 .3 12.4-2s6.3-6.1 7.1-10.4c8.6-42.8 4.3-81.2-2.1-108.7C90.3 344.3 86 329.8 80 316.5l0-24.6c0-30.2 10.2-58.7 27.9-81.5c12.9-15.5 29.6-28 49.2-35.7l157-61.7c8.2-3.2 17.5 .8 20.7 9s-.8 17.5-9 20.7l-157 61.7c-12.4 4.9-23.3 12.4-32.2 21.6l159.6 57.6c7.6 2.7 15.6 4.1 23.7 4.1s16.1-1.4 23.7-4.1L624.2 182.6c9.5-3.4 15.8-12.5 15.8-22.6s-6.3-19.1-15.8-22.6L343.7 36.1C336.1 33.4 328.1 32 320 32zM128 408c0 35.3 86 72 192 72s192-36.7 192-72L496.7 262.6 354.5 314c-11.1 4-22.8 6-34.5 6s-23.5-2-34.5-6L143.3 262.6 128 408z"/></svg>';

	$settingsIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>';

	$logoutIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>';
?>

<div class="header-navigation-wrapper">
	<div class="header-navigation-wrapper-inner">

		<?php if ($user->ID > 0): // Only show if logged in ?>
			<a href="/" class="header-navigation-logo-link" aria-label="Zur Startseite">
				<img src="/wp-content/uploads/2024/06/DKI_Wiki_logo.svg" class="header-navigation-logo" />
				<span>die kommunikatöre® Hub</span>
			</a>
			<nav class="header-navigation-menu">
				<?php if ($navigation_menu_id > 0): ?>
					<ul>
					<?php
						foreach($menu_items as $item) {
							$active_class = ($currentUrl == (parse_url($item['url'], PHP_URL_PATH) . "/")) ? ' active' : '';
							$target = '';
							if(isset($item['target'])){
								$target = $item['target'];
							}
							?>
							<li>
								<?php
								echo '<a class="nav-item nav-link' . $active_class . '" href="' . esc_url($item['url']) . '"' . ($target ? ' target="' . esc_attr($target) . '"' : '') . '>' . esc_html($item['title']) . '</a>';
								?>
							</li>
						<?php
						}
					?>
				</ul>
				<?php endif; ?>
				<!-- Profile Dropdown -->
				<li class="nav-item dropdown">
					<button class="nav-link dropdown-toggle" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Profil von <?php echo esc_attr($user_display_name); ?>" aria-label="Profil von <?php echo esc_attr($user_display_name); ?>" type="button">
						<img src="<?php echo esc_url($user_avatar); ?>" class="nav-item-avatar" width="32" height="32" alt="Profilbild von <?php echo esc_attr($user_display_name); ?>">
					</button>
					<nav class="dropdown-menu" aria-labelledby="profileDropdown">
						<div class="user-info">
							<div class="user-name"><?php echo esc_html($user_display_name); ?></div>
							<div class="user-role <?php echo esc_attr(lcfirst($formatted_user_role)); ?>"><?php echo esc_html($formatted_user_role); ?></div>
						</div>
						<span class="divider"></span>
						<ul>
							<li class="dropdown-item<?php if($currentUrl == '/user/') echo ' active'; ?>">
								<a href="/user">
									<span>
										<?php echo $userIcon; ?>
										Mein Profil
									</span>
								</a>
							</li>
						</ul>
						<span class="divider"></span>
						<?php if($user_role == 'administrator' ): ?>
							<ul>
								<li class="dropdown-item">
									<a href="/wp-admin/">
										<span>
											<?php echo $settingsIcon; ?>
											WP-Admin
										</span>
									</a>
								</li>
							</ul>
						<?php endif; ?>
						<span class="divider"></span>
						<ul>
							<li class="dropdown-item">
								<a href="/logout">
									<span>
										<?php echo $logoutIcon; ?>
										Ausloggen
									</span>
								</a>
							</li>
						</ul>
					</nav>
				</li>
			</nav>
		<?php else: // No user, show only menu if selected ?>
			<?php if ($navigation_menu_id > 0): ?>
				<nav class="header-navigation-menu">
					<?php
						wp_nav_menu(array(
							'menu' => $navigation_menu_id,
							'container' => false,
							'menu_class' => 'nav navbar-nav header-nav-menu',
							'fallback_cb' => false,
							'depth' => 2,
							'walker' => new class extends Walker_Nav_Menu { /* same walker as above */ },
						));
					?>
				</nav>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
