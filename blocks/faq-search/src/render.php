<?php
/**
 * FAQ Search Block Server Side Rendering
 */
?>

<div class="wp-block-dki-wiki-faq-search">
    <div class="faq-search-wrapper">
        <input 
            type="text" 
            class="faq-search-input" 
            placeholder="<?php esc_attr_e('FAQs durchsuchen...', 'dki-wiki'); ?>"
            aria-label="<?php esc_attr_e('FAQs durchsuchen', 'dki-wiki'); ?>"
        >
        <button class="faq-search-clear" aria-label="<?php esc_attr_e('Suche löschen', 'dki-wiki'); ?>" hidden>
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>