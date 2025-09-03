<?php
	/**
	 * Plugin Name:       Login Form
	 */

?>

<?php
	$loginFormTitle = isset($attributes['loginFormTitle']) ? $attributes['loginFormTitle'] : __('Login', 'dki-wiki');
	$usernameLabel = isset($attributes['usernameLabel']) ? $attributes['usernameLabel'] : __('Username', 'dki-wiki');
	$username = isset($attributes['username']) ? $attributes['username'] : __('Username', 'dki-wiki');
	$usernamePlaceholder = isset($attributes['usernamePlaceholder']) ? $attributes['usernamePlaceholder'] : __('Enter your username', 'dki-wiki');
	$passwordLabel = isset($attributes['passwordLabel']) ? $attributes['passwordLabel'] : __('Password', 'dki-wiki');
	$password = isset($attributes['password']) ? $attributes['password'] : __('Password', 'dki-wiki');
	$passwordPlaceholder = isset($attributes['passwordPlaceholder']) ? $attributes['passwordPlaceholder'] : __('Enter your password', 'dki-wiki');
	$rememberMeLabel = isset($attributes['rememberMeLabel']) ? $attributes['rememberMeLabel'] : __('Remember me', 'dki-wiki');
	$submitLabel = isset($attributes['submitLabel']) ? $attributes['submitLabel'] : __('Login', 'dki-wiki');
	$forgotPasswordLabel = isset($attributes['forgotPasswordLabel']) ? $attributes['forgotPasswordLabel'] : __('Forgot Password?', 'dki-wiki');

	$formLabels = array(
		'loginFormTitle' => $loginFormTitle,
		'usernameLabel' => $usernameLabel,
		'username' => $username,
		'usernamePlaceholder' => $usernamePlaceholder,
		'passwordLabel' => $passwordLabel,
		'password' => $password,
		'passwordPlaceholder' => $passwordPlaceholder,
		'rememberMeLabel' => $rememberMeLabel,
		'submitLabel' => $submitLabel,
		'forgotPasswordLabel' => $forgotPasswordLabel
	);

	 /* ICONS */
	 $mailIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 64C150 64 64 150 64 256s86 192 192 192c17.7 0 32 14.3 32 32s-14.3 32-32 32C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256l0 32c0 53-43 96-96 96c-29.3 0-55.6-13.2-73.2-33.9C320 371.1 289.5 384 256 384c-70.7 0-128-57.3-128-128s57.3-128 128-128c27.9 0 53.7 8.9 74.7 24.1c5.7-5 13.1-8.1 21.3-8.1c17.7 0 32 14.3 32 32l0 80 0 32c0 17.7 14.3 32 32 32s32-14.3 32-32l0-32c0-106-86-192-192-192zm64 192a64 64 0 1 0 -128 0 64 64 0 1 0 128 0z"/></svg>';

	 $passwordIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/></svg>';

	$icons = array(
		'mailIcon' => $mailIcon,
		'passwordIcon' => $passwordIcon
	);
?>
<?php 
function render_login_form($formLabels, $icons){

	$nonce = wp_create_nonce('login_form_nonce');
    ob_start();
    ?>

	<div id="login-form" class="login-form-wrapper">
		<h1><?php echo esc_html($formLabels['loginFormTitle']); ?></h1>
		<form class="login-form">
			<div class="login-field-wrapper username">
				<label for="username"><?php echo esc_html($formLabels['usernameLabel']); ?></label>
				<div class="input-wrapper">
					<input 
						type="text" 
						id="username" 
						name="username" 
						value="<?php echo esc_attr($formLabels['username']); ?>"
						placeholder="<?php echo esc_attr($formLabels['usernamePlaceholder']); ?>"
					>
					<span class="icon">
						<?php echo $icons['mailIcon']; ?>
					   </span>
				</div>
			</div>
			<div class="login-field-wrapper password">
				<label for="password"><?php echo esc_html($formLabels['passwordLabel']); ?></label>
				<div class="input-wrapper">
					<input 
						type="password" 
						id="password" 
						name="password" 
						value="<?php echo esc_html($formLabels['password']); ?>" 
						placeholder="<?php echo esc_attr($formLabels['passwordPlaceholder']); ?>"
					>
					<span class="icon">
						<?php echo $icons['passwordIcon']; ?>
					</span>
				</div>
			</div>
			<div class="login-field-wrapper submit">
				<label for="remember-me">
					<input type="checkbox" id="remember-me" name="remember-me">
					<?php echo esc_html($formLabels['rememberMeLabel']); ?>
					<!-- <span class="checkmark"></span> -->
				</label>
				<button type="submit"><?php echo esc_html($formLabels['submitLabel']); ?></button>
			</div>
			<div class="forgot-password">
				<a href="/password-reset"><?php echo esc_html($formLabels['forgotPasswordLabel']); ?></a>
			</div>
		</form>
		<div id="login-message"></div>

	</div>
	<?php
    return ob_get_clean();
}

echo render_login_form($formLabels, $icons);

?>