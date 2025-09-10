<?php
/**
 * FAQ Search Block - Server-side rendering
 * Outputs search input with localized FAQ data
 */

// Extract attributes with defaults
$target_display_id = isset( $attributes['targetDisplayId'] ) ? $attributes['targetDisplayId'] : '';
$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : __( 'Suchen Sie in den FAQs...', 'faq-search' );

// Generate unique ID for this block instance
$block_id = 'faq-search-' . wp_unique_id();

// Query all FAQs for search data
$faqs_query = new WP_Query( array(
	'post_type' => 'faq',
	'posts_per_page' => -1,
	'orderby' => 'title',
	'order' => 'ASC',
	'post_status' => 'publish',
) );

$faq_data = array();
if ( $faqs_query->have_posts() ) {
	while ( $faqs_query->have_posts() ) {
		$faqs_query->the_post();
		$faq_data[] = array(
			'id' => get_the_ID(),
			'title' => get_the_title(),
			'content' => wp_strip_all_tags( get_the_content() ),
			'excerpt' => get_the_excerpt(),
			'section' => wp_get_post_terms( get_the_ID(), 'faq_section', array( 'fields' => 'names' ) ),
		);
	}
}
wp_reset_postdata();

// Output JSON data for JS
$faq_json = wp_json_encode( $faq_data );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'faq-search-wrapper' ) ); ?> data-block-id="<?php echo esc_attr( $block_id ); ?>" data-target-display="<?php echo esc_attr( $target_display_id ); ?>" data-faq-data="<?php echo esc_attr( $faq_json ); ?>">
	<input 
		type="search" 
		class="faq-search-input" 
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		aria-label="<?php esc_attr_e( 'FAQ Suche', 'faq-search' ); ?>"
	/>
	<div class="faq-search-results-count" style="display: none;">
		<?php printf( __( '%d Ergebnisse', 'faq-search' ), 0 ); ?>
	</div>
</div>

<script>
	// Inline data for view.js if needed, but primarily use data attributes
	window.faqSearchData = window.faqSearchData || {};
	window.faqSearchData[<?php echo json_encode( $block_id ); ?>] = <?php echo $faq_json; ?>;
</script>