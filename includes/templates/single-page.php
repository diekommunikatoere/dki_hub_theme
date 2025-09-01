<?php

/**
 * Single Page Template 
 * 
 * Template Name: Single Page
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Post header
get_header();

?>

<main id="content" <?php post_class( 'site-main' ); ?>>
	<div class="page-content">
		<?php the_content(); ?>
	</div>
</main>

<?php
// Post footer
get_footer();