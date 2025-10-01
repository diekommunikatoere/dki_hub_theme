<?php
/**
 * FAQ Display Block Server Side Rendering
 */

// Get FAQ sections ordered by '_section_order'
$sections = get_terms(array(
    'taxonomy' => 'faq_section',
    'hide_empty' => false,
    'meta_key' => '_section_order',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
));

// Fallback to alphabetical order if no meta order is set
if (empty($sections) || is_wp_error($sections)) {
    $sections = get_terms(array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ));
}

// Copy link icon
$copy_icon = file_get_contents(get_stylesheet_directory() . '/includes/assets/icons/general/icon_general_link.svg');

// Success icon
$success_icon = file_get_contents(get_stylesheet_directory() . '/includes/assets/icons/general/icon_general_success.svg');

// Check URL for FAQ item ID (e.g., ?faq=123)
$id_in_url = isset($_GET['faq']) ? intval($_GET['faq']) : 0;
?>

<div class="wp-block-dki-wiki-faq-display">
    <?php foreach ($sections as $section): ?>
        <?php
        // Get FAQ items for this section ordered by '_faq_order'
        $faqs = get_posts(array(
            'post_type' => 'faq',
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'faq_section',
                    'field' => 'term_id',
                    'terms' => $section->term_id,
                ),
            ),
            'meta_key' => '_faq_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        ));

        // Fallback to alphabetical order if no meta order is set
        if (empty($faqs)) {
            $faqs = get_posts(array(
                'post_type' => 'faq',
                'numberposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'faq_section',
                        'field' => 'term_id',
                        'terms' => $section->term_id,
                    ),
                ),
                'orderby' => 'title',
                'order' => 'ASC',
            ));
        }

        // Skip section if no FAQs found
        if (empty($faqs)) {
            continue;
        }
        ?>

        <div class="faq-section" data-section-id="<?php echo esc_attr($section->term_id); ?>">
            <h2 class="faq-section-title"><?php echo esc_html($section->name); ?></h2>
            
            <div class="faq-accordion" role="region" aria-label="<?php printf(__('FAQs für %s', 'dki-wiki'), esc_attr($section->name)); ?>">
                <?php foreach ($faqs as $faq): ?>
                    <?php
                    $faq_id = $faq->ID;
                    $faq_title = $faq->post_title;
                    $faq_content = apply_filters('the_content', $faq->post_content);
                    $faq_content_hidden = ($id_in_url !== $faq_id) ? 'hidden' : '';
                    ?>
                    
                    <div class="faq-item">
                        <!-- Accordion Header/Heading with Button -->
                        <h3 class="faq-question-heading">
                            <button 
                                id="faq-question-<?php echo esc_attr($faq_id); ?>"
                                class="faq-question-button<?php echo ($id_in_url === $faq_id) ? ' is-open' : ''; ?>"
                                aria-expanded="false"
                                aria-controls="faq-content-<?php echo esc_attr($faq_id); ?>"
                            >
                                <span class="faq-question-text"><?php echo esc_html($faq_title); ?></span>
                                <span class="faq-toggle-icon" aria-hidden="true"></span>
                            </button>
                        </h3>
                        
                        <!-- Accordion Content Panel -->
                        <div 
                            id="faq-content-<?php echo esc_attr($faq_id); ?>"
                            class="faq-answer"
                            role="region"
                            aria-labelledby="faq-question-<?php echo esc_attr($faq_id); ?>"
                            <?php echo $faq_content_hidden; ?>
                        >
                            <button class="faq-copy-link-button" data-faq-id="<?php echo esc_attr($faq_id); ?>">
                                <span class="faq-copy-link-icon default">
                                    <?php echo $copy_icon; ?>
                                </span>
                                <span class="faq-copy-link-icon success">
                                    <?php echo $success_icon; ?>
                                </span>
                                <span class="screen-reader-text"><?php _e('Link in Zwischenablage kopieren', 'dki-wiki'); ?></span>
                            </button>
                            <div class="faq-answer-content">
                                <?php echo $faq_content; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>