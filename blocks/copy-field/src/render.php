<?php
/**
 * Copy Field Block - Server-side rendering
 */

// Extract attributes with defaults
$input_type = isset($attributes['inputType']) ? $attributes['inputType'] : 'richtext';
$label = isset($attributes['label']) ? $attributes['label'] : __('Text kopieren:', 'copy-field');
$placeholder = isset($attributes['placeholder']) ? $attributes['placeholder'] : __('Formatierten Text hier eingeben...', 'copy-field');
$content = isset($attributes['content']) ? $attributes['content'] : '';
$code_language = isset($attributes['codeLanguage']) ? $attributes['codeLanguage'] : 'bash';
$copy_button_text = isset($attributes['copyButtonText']) ? $attributes['copyButtonText'] : __('Kopieren', 'copy-field');

// Generate unique ID for this block instance
$block_id = 'copy-field-' . wp_unique_id();

// Add copy icon SVG
$copy_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
?>

<div <?php echo get_block_wrapper_attributes(['class' => 'copy-field-wrapper']); ?>>
	<div class="copy-field-header">
		<?php if (!empty($label)): ?>
			<label class="copy-field-label" for="<?php echo esc_attr($block_id); ?>">
				<?php echo esc_html($label); ?>
			</label>
		<?php endif; ?>
		
		<button 
			type="button"
			class="copy-field-button"
			data-copy-target="#<?php echo esc_attr($block_id); ?>"
			data-copy-text="<?php echo esc_attr($copy_button_text); ?>"
			data-copied-text="<?php echo esc_attr(__('Kopiert!', 'copy-field')); ?>"
			data-error-text="<?php echo esc_attr(__('Fehler beim Kopieren', 'copy-field')); ?>"
			<?php echo empty($content) ? 'disabled' : ''; ?>
		>
			<?php echo $copy_icon; ?>
			<span class="copy-button-text"><?php echo esc_html($copy_button_text); ?></span>
		</button>
	</div>
	
	<div class="copy-field-content-wrapper">
		<?php if ($input_type === 'richtext'): ?>
			<div 
				id="<?php echo esc_attr($block_id); ?>"
				class="copy-field-richtext readonly"
				contenteditable="false"
				data-placeholder="<?php echo esc_attr($placeholder); ?>"
			>
				<?php echo wp_kses_post($content); ?>
			</div>
		<?php elseif ($input_type === 'code'): ?>
			<div
				id="<?php echo esc_attr($block_id); ?>"
				class="copy-field-code-block"
				data-language="<?php echo esc_attr($code_language); ?>"
			><?php echo esc_textarea($content); ?></div>
		<?php endif; ?>
	</div>
	
	<?php if ($input_type === 'code' && !empty($code_language)): ?>
		<small class="copy-field-language-label">
			<?php echo esc_html(__('Sprache:', 'copy-field')); ?> <?php echo esc_html($code_language); ?>
		</small>
	<?php endif; ?>
</div>