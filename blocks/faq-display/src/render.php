<?php
/**
 * FAQ Display Block - Server-side rendering
 * Renders nested accordions for FAQ sections and items
 */

// Extract attributes with defaults
$show_sections = isset( $attributes['showSections'] ) ? (bool) $attributes['showSections'] : true;
$accordion_style = isset( $attributes['accordionStyle'] ) ? $attributes['accordionStyle'] : 'default';

// Generate unique ID for this block instance
$block_id = 'faq-display-' . wp_unique_id();

// Icons
$icon_chevron_down='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M297.4 470.6C309.9 483.1 330.2 483.1 342.7 470.6L534.7 278.6C547.2 266.1 547.2 245.8 534.7 233.3C522.2 220.8 501.9 220.8 489.4 233.3L320 402.7L150.6 233.4C138.1 220.9 117.8 220.9 105.3 233.4C92.8 245.9 92.8 266.2 105.3 278.7L297.3 470.7z"/></svg>';
$icon_chevron_up='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M297.4 169.4C309.9 156.9 330.2 156.9 342.7 169.4L534.7 361.4C547.2 373.9 547.2 394.2 534.7 406.7C522.2 419.2 501.9 419.2 489.4 406.7L320 237.3L150.6 406.6C138.1 419.1 117.8 419.1 105.3 406.6C92.8 394.1 92.8 373.8 105.3 361.3L297.3 169.3z"/></svg>';
$icon_plus='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg>';
$icon_minus='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320z"/></svg>';


if ( $show_sections ) {
	// Query FAQ sections (terms)
	$sections = get_terms( array(
		'taxonomy' => 'faq_section',
		'hide_empty' => true,
		'orderby' => 'meta_value_num',
		'meta_key' => '_section_order',
		'order' => 'ASC',
	) );

	if ( ! empty( $sections ) && ! is_wp_error( $sections ) ) {
		?>
		<div <?php echo get_block_wrapper_attributes( array( 'class' => 'faq-display-wrapper faq-accordion-' . esc_attr( $accordion_style ) ) ); ?> id="<?php echo esc_attr( $block_id ); ?>">
			<?php foreach ( $sections as $section ) : ?>
				<div class="faq-section-accordion">
					<details class="faq-section-details">
						<summary class="faq-section-summary">
							<h3 class="faq-section-title"><?php echo esc_html( $section->name ); ?></h3>
							<span class="faq-toggle-icon" aria-hidden="true">+</span>
						</summary>
						<div class="faq-section-content">
							<?php
							// Query FAQs in this section
							$faqs = new WP_Query( array(
								'post_type' => 'faq',
								'posts_per_page' => -1,
								'tax_query' => array(
									array(
										'taxonomy' => 'faq_section',
										'field' => 'term_id',
										'terms' => $section->term_id,
									),
								),
								'meta_key' => '_faq_order',
								'orderby' => array( 'meta_value_num' => 'ASC', 'title' => 'ASC' ),
								'order' => 'ASC',
							) );

							if ( $faqs->have_posts() ) :
								while ( $faqs->have_posts() ) : $faqs->the_post();
									$question = get_the_title();
									$answer = get_the_content();
									$excerpt = get_the_excerpt();
									?>
									<div class="faq-item-accordion">
										<details class="faq-item-details">
											<summary class="faq-item-summary">
												<div class="faq-question"><?php echo esc_html( $question ); ?></div>
												<span class="faq-toggle-icon" aria-hidden="true">+</span>
											</summary>
											<div class="faq-item-content">
												<div class="faq-answer">
													<?php echo apply_filters( 'the_content', $answer ); ?>
												</div>
												<?php if ( $excerpt ) : ?>
													<div class="faq-excerpt"><?php echo esc_html( $excerpt ); ?></div>
												<?php endif; ?>
											</div>
										</details>
									</div>
								<?php endwhile; ?>
							<?php endif; wp_reset_postdata(); ?>
						</div>
					</details>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	} else {
		// Fallback if no sections
		echo '<p>' . __( 'Keine FAQ-Abschnitte gefunden. Erstellen Sie Abschnitte im Admin-Bereich.', 'faq-display' ) . '</p>';
	}
} else {
	// If not showing sections, query all FAQs directly
	$faqs = new WP_Query( array(
		'post_type' => 'faq',
		'posts_per_page' => -1,
		'meta_key' => '_faq_order',
		'orderby' => array( 'meta_value_num' => 'ASC', 'title' => 'ASC' ),
		'order' => 'ASC',
	) );

	if ( $faqs->have_posts() ) :
		?>
		<div <?php echo get_block_wrapper_attributes( array( 'class' => 'faq-display-wrapper faq-accordion-' . esc_attr( $accordion_style ) ) ); ?> id="<?php echo esc_attr( $block_id ); ?>">
			<?php while ( $faqs->have_posts() ) : $faqs->the_post(); ?>
				<div class="faq-item-accordion">
					<details class="faq-item-details">
						<summary class="faq-item-summary">
							<div class="faq-question"><?php echo esc_html( get_the_title() ); ?></div>
							<span class="faq-toggle-icon" aria-hidden="true">+</span>
						</summary>
						<div class="faq-item-content">
							<div class="faq-answer">
								<?php the_content(); ?>
							</div>
							<?php if ( $excerpt = get_the_excerpt() ) : ?>
								<div class="faq-excerpt"><?php echo esc_html( $excerpt ); ?></div>
							<?php endif; ?>
						</div>
					</details>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php
	else :
		echo '<p>' . __( 'Keine FAQs gefunden. Erstellen Sie FAQs im Admin-Bereich.', 'faq-display' ) . '</p>';
	endif;
}
?>