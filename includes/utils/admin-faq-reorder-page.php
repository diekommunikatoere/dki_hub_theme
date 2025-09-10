<?php
/**
 * Admin page for FAQ and section reordering
 *
 * @package DKI Wiki Theme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Callback for FAQ reorder subpage
 */
function dki_wiki_faq_reorder_page() {
    // Verify permissions
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'dki-wiki' ) );
    }

    // Nonce for AJAX
    $nonce = wp_create_nonce( 'faq_reorder_nonce' );

    // Get sections ordered by meta
    $sections = get_terms( array(
        'taxonomy' => 'faq_section',
        'hide_empty' => false,
        'orderby' => 'meta_value_num',
        'meta_key' => '_section_order',
        'order' => 'ASC'
    ) );

    ?>
    <div class="wrap dki-wiki-faq-reorder">
        <h1><?php _e( 'FAQs und Abschnitte neu anordnen', 'dki-wiki' ); ?></h1>
        <p><?php _e( 'Ziehen Sie Abschnitte und FAQs per Drag-and-Drop, um die Reihenfolge zu ändern. Klicken Sie auf "Speichern", um Änderungen zu übernehmen.', 'dki-wiki' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'faq_reorder_page', 'faq_reorder_page_nonce' ); ?>
            <ul id="sections-sortable" class="sortable-sections">
                <?php if ( ! empty( $sections ) && ! is_wp_error( $sections ) ) : ?>
                    <?php foreach ( $sections as $section ) : ?>
                        <li class="section-item" data-term-id="<?php echo esc_attr( $section->term_id ); ?>">
                            <div class="section-header">
                                <span class="dashicons dashicons-move section-drag-handle"></span>
                                <strong><?php echo esc_html( $section->name ); ?></strong>
                                <span class="section-order"><?php echo get_term_meta( $section->term_id, '_section_order', true ) ?: __( 'N/A', 'dki-wiki' ); ?></span>
                            </div>
                            <ul class="faqs-sortable sortable-faqs" data-section-id="<?php echo esc_attr( $section->term_id ); ?>">
                                <?php
                                $faqs_query = new WP_Query( array(
                                    'post_type' => 'faq',
                                    'posts_per_page' => -1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'faq_section',
                                            'field' => 'term_id',
                                            'terms' => $section->term_id,
                                        ),
                                    ),
                                    'orderby' => 'meta_value_num',
                                    'meta_key' => '_faq_order',
                                    'order' => 'ASC',
                                    'post_status' => 'publish'
                                ) );
                                if ( $faqs_query->have_posts() ) :
                                    while ( $faqs_query->have_posts() ) : $faqs_query->the_post();
                                ?>
                                    <li class="faq-item" data-post-id="<?php the_ID(); ?>">
                                        <span class="dashicons dashicons-move faq-drag-handle"></span>
                                        <strong><?php the_title(); ?></strong>
                                        <span class="faq-order"><?php echo get_post_meta( get_the_ID(), '_faq_order', true ) ?: __( 'N/A', 'dki-wiki' ); ?></span>
                                    </li>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    echo '<li>' . __( 'Keine FAQs in diesem Abschnitt.', 'dki-wiki' ) . '</li>';
                                endif;
                                ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li><?php _e( 'Keine Abschnitte gefunden. Erstellen Sie Abschnitte im Admin.', 'dki-wiki' ); ?></li>
                <?php endif; ?>
            </ul>
            <p class="submit">
                <input type="button" id="save-faq-reorder" class="button-primary" value="<?php _e( 'Reihenfolge speichern', 'dki-wiki' ); ?>" />
                <span id="reorder-message"></span>
            </p>
            <script>
                var faqReorderNonce = '<?php echo $nonce; ?>';
                var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
            </script>
        </form>
    </div>
    <?php
}