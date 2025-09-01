import { PanelBody, TextControl, Button } from "@wordpress/components";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";

import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
	const { loginFormTitle, usernameLabel, usernamePlaceholder, passwordLabel, passwordPlaceholder, rememberMeLabel, submitLabel, forgotPasswordLabel } = attributes;

	const handleLoginFormTitleChange = (newLoginFormTitle) => {
		setAttributes({ loginFormTitle: newLoginFormTitle });
	};

	const handleUsernameLabelChange = (newUsernameLabel) => {
		setAttributes({ usernameLabel: newUsernameLabel });
	};

	const handleUsernamePlaceholderChange = (newUsernamePlaceholder) => {
		setAttributes({ usernamePlaceholder: newUsernamePlaceholder });
	};

	const handlePasswordLabelChange = (newPasswordLabel) => {
		setAttributes({ passwordLabel: newPasswordLabel });
	};

	const handlePasswordPlaceholderChange = (newPasswordPlaceholder) => {
		setAttributes({ passwordPlaceholder: newPasswordPlaceholder });
	};

	const handleRememberMeLabelChange = (newRememberMeLabel) => {
		setAttributes({ rememberMeLabel: newRememberMeLabel });
	};

	const handleSubmitLabelChange = (newSubmitLabel) => {
		setAttributes({ submitLabel: newSubmitLabel });
	};

	const handleForgotPasswordLabelChange = (newForgotPasswordLabel) => {
		setAttributes({ forgotPasswordLabel: newForgotPasswordLabel });
	};

	const handleLogin = () => {
		// Check if the username and password are valid
		if (username && password) {
			// Call a function to handle the login process
			performLogin(username, password);
		} else {
			// Display an error message
			alert("Please enter a valid username and password.");
		}
	};

	const performLogin = (username, password) => {
		// Make an AJAX request to the server-side login endpoint
		// Using the WordPress REST API or a custom endpoint
		// Handle the response and update the block's state accordingly
	};

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__("Login Form Settings", "dki-wiki")}>
					<TextControl label={__("Login Form Title", "dki-wiki")} value={loginFormTitle} onChange={handleLoginFormTitleChange} />
					<h2>{__("Login Form Settings", "dki-wiki")}</h2>
					<TextControl label={__("Username Label", "dki-wi")} value={usernameLabel} onChange={handleUsernameLabelChange} />
					<TextControl label={__("Username Placeholder", "dki-wiki")} value={usernamePlaceholder} onChange={handleUsernamePlaceholderChange} />
					<TextControl label={__("Password Label", "dki-wiki")} value={passwordLabel} onChange={handlePasswordLabelChange} />
					<TextControl label={__("Password Placeholder", "dki-wiki")} value={passwordPlaceholder} onChange={handlePasswordPlaceholderChange} />
					<TextControl label={__("Remember Me Label", "dki-wiki")} value={rememberMeLabel} onChange={handleRememberMeLabelChange} />
					<TextControl label={__("Submit Button Label", "dki-wiki")} value={submitLabel} onChange={handleSubmitLabelChange} />
					<TextControl label={__("Forgot Password Label", "dki-wiki")} value={forgotPasswordLabel} onChange={handleForgotPasswordLabelChange} />
				</PanelBody>
			</InspectorControls>

			<h2>{loginFormTitle}</h2>
			<form>
				<div class="login-field-wrapper username">
					<label for="username">{usernameLabel}</label>
					<input type="text" id="username" name="username" placeholder={usernamePlaceholder} />
				</div>
				<div class="login-field-wrapper password">
					<label for="password">{passwordLabel}</label>
					<input type="password" id="password" name="password" placeholder={passwordPlaceholder} />
				</div>
				<div class="login-field-wrapper submit">
					<label for="remember-me">
						<input type="checkbox" id="remember-me" name="remember-me" />
						{rememberMeLabel}
					</label>
					<Button isPrimary>{submitLabel}</Button>
				</div>
				<div class="forgot-password">
					<a href="#">{forgotPasswordLabel}</a>
				</div>
			</form>
		</div>
	);
}
