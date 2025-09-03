<?php

/**
 * Single Post Template 
 * 
 * Template Name: Single Post
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$post_id = get_the_ID();
$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

// Add meta keyword from post tags to the head
function add_meta_keywords() {
    $tags = get_the_tags();
    if ( $tags ) {
        $tag_names = array();
        foreach ( $tags as $tag ) {
            $tag_names[] = $tag->name;
        }
        $meta_keywords = implode( ', ', $tag_names );
        echo '<meta name="keywords" content="' . $meta_keywords . '">';
    }
}
add_action( 'wp_head', 'add_meta_keywords' );

// OpenGraph meta tags
function add_opengraph_meta_tags() {

    // Return if in editor
    if ( is_admin() ) {
        return;
    }

    $post_id = get_the_ID();
    $elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

    if ( isset( $elementor_page_settings['featured_image_socialmedia'] ) ) {
        $featured_image_socialmedia = $elementor_page_settings['featured_image_socialmedia'];
    } else {
        $featured_image_socialmedia = "";
    }
    $post_tags = get_the_tags();
    $tag_names = array();
    if ( $post_tags ) {
        foreach ( $post_tags as $tag ) {
            $tag_names[] = $tag->name;
        }
    }
    $meta_keywords = implode( ', ', $tag_names );
    echo '<head>';
    echo '<meta property="og:title" content="' . get_the_title() . '">';
    echo '<meta property="og:description" content="' . get_the_excerpt() . '">';
    if ( isset( $elementor_page_settings['featured_image_socialmedia'] ) ) {
        echo '<meta property="og:image" content="' . $featured_image_socialmedia['url'] . '">';
    } else {
        echo '<meta property="og:image" content="">';
    }
    echo '<meta property="og:url" content="' . get_the_permalink() . '">';
    echo '<meta property="og:type" content="article">';
    echo '<meta property="article:published_time" content="' . get_the_date( 'c' ) . '">';
    echo '<meta property="article:modified_time" content="' . get_the_modified_date( 'c' ) . '">';
    echo '<meta property="article:tag" content="' . $meta_keywords . '">';
    echo '</head>';
}
add_action( 'wp_head', 'add_opengraph_meta_tags' );

// Theme header
// Add any additional meta tags to the head above this through the add_action() function
get_header();
?>



<article id="content" <?php post_class( 'site-main' ); ?>>
    
    <header class="post-header">
        
        <?php 
            // Get the category
            $post_category = get_the_category();
            if ( $post_category ) {
                echo '<div class="post-category">';
                foreach ( $post_category as $category ) {
                    echo '<a href="' . get_category_link( $category->term_id ) . '" class="category-' . $category->slug . '">' . $category->name . '</a>';
                }
                echo '</div>';
            }
        ?>

        <?php
        // Get the title
        $post_title = get_the_title();
        if ( isset( $post_title ) ) {
            echo '<h1 class="post-title
            ">' . $post_title . '</h1>';
        }
        ?>
        
        <?php
        // Get the subtitle
        if ( isset( $elementor_page_settings['subtitle'] ) ) {
            $subtitle = $elementor_page_settings['subtitle'];
            echo '<h2 class="post-subtitle">' . $subtitle . '</h2>';
        }
        ?>

        <?php
        // Get the intro text
        if ( isset( $elementor_page_settings['intro'] ) ) {
            $post_intro = $elementor_page_settings['intro'];
            echo '<p class="post-intro">';
            echo do_shortcode("[post_excerpt]");
            echo '</p>';
        }
        ?>
        
        
        <?php
        // Get the featured image
        // Insert the featured image with different sizes
        // 
        if ( isset( $elementor_page_settings['featured_image_desktop'] ) || isset( $elementor_page_settings['featured_image_tablet'] ) || isset( $elementor_page_settings['featured_image_mobile'] ) || isset( $elementor_page_settings['featured_image_socialmedia'] ) ) {
            $featured_image_desktop = $elementor_page_settings['featured_image_desktop'];
            $featured_image_tablet = $elementor_page_settings['featured_image_tablet'];
            $featured_image_mobile = $elementor_page_settings['featured_image_mobile'];
            $featured_image_socialmedia = $elementor_page_settings['featured_image_socialmedia'];
            echo '
            <picture>
                <source media="(max-width: 767px)" srcset="' . $featured_image_mobile['url'] . '">
                <source media="(max-width: 1024px)" srcset="' . $featured_image_tablet['url'] . '">
                <img class="post-featured-image" src="' . $featured_image_desktop['url'] . '" alt="' . get_the_title() . '" fetchpriority="high">
            </picture>';
        } else {
            echo '<img class="post-featured-image" src="https://dev.frauengesundheit.digital/wp-content/uploads/2024/02/placeholder-5-1024x683.png" alt="' . get_the_title() . '" fetchpriority="high">';
        }
        ?>

        <?php
        // Social media share, email and print buttons
        // Open new window for social media share links
        echo '<div class="social-media-share">';
            echo '<p>Diesen Beitrag teilen:</p>';
            echo '<div class="social-media-share__button-container">';
                echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Beitrag auf Facebook teilen"><i class="icon brands-facebook"></i></a>';
                echo '<a href="https://twitter.com/intent/tweet?url=' . urlencode(get_the_permalink()) . '&text=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Beitrag auf X teilen"><i class="icon brands-x"></i></a>';
                echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><i class="icon brands-linkedin"></i></a>';
                if (wp_is_mobile()) {
                    echo '<a href="whatsapp://send?text=*' . urlencode(get_the_title()) . '* ' . urlencode(get_the_excerpt()) . ': ' . urlencode(get_the_permalink()) .  '" data-action="share/whatsapp/share" aria-label="Beitrag auf WhatsApp teilen"><i class="icon brands-whatsapp"></i></a>';
                } else {
                    echo '<a href="https://wa.me/?text=*' . urlencode(get_the_title()) . '* ' . urlencode(get_the_excerpt()) . ': ' . urlencode(get_the_permalink()) .  '" data-action="share/whatsapp/share" target="_blank" rel="noopener" aria-label="Beitrag auf WhatsApp teilen"><i class="icon brands-whatsapp"></i></a>';
                }
                echo '<a href="mailto:?subject=' . urlencode(get_the_title()) . '&body=' . urlencode(get_the_permalink()) . '" aria-label="Beitrag als E-Mail teilen"><i class="icon icon-email"></i></a>';
                echo '<a href="javascript:window.print()" aria-label="Beitrag drucken"><i class="icon icon-local_printshop"></i></a>';
            echo '</div>';
        echo '</div>';
        ?>

    </header>
    
    <main class="post-content">
        <?php the_content(); ?>
    </main>

    <footer class="post-footer">

        <?php 
        // Get the category
        $post_category = get_the_category();
        if ( $post_category ) {
            echo '<div class="post-category">';
            foreach ( $post_category as $category ) {
                echo '<a href="' . get_category_link( $category->term_id ) . '" class="category-' . $category->slug . '">' . $category->name . '</a>';
            }
            echo '</div>';
        }
        ?>

        <?php
        // Get the tags
        $post_tags = get_the_tags();
        if ( $post_tags ) {
            echo '<div class="post-tags__container">';
                echo '<h3>Tags</h3>';
                echo '<div class="post-tags">';
                    foreach ( $post_tags as $tag ) {
                        echo '<a href="' . get_tag_link( $tag->term_id ) . '" class=" post-tag tag-' . $tag->slug . '">' . $tag->name . '</a>';
                    }
                echo '</div>';
            echo '</div>';
        }
        ?>

        <?php
        // Social media share, email and print buttons
        // Open new window for social media share links
        echo '<div class="social-media-share">';
            echo '<p>Diesen Beitrag teilen:</p>';
            echo '<div class="social-media-share__button-container">';
                echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Beitrag auf Facebook teilen"><i class="icon brands-facebook"></i></a>';
                echo '<a href="https://twitter.com/intent/tweet?url=' . urlencode(get_the_permalink()) . '&text=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Beitrag auf X teilen"><i class="icon brands-x"></i></a>';
                echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><i class="icon brands-linkedin"></i></a>';
                if (wp_is_mobile()) {
                    echo '<a href="whatsapp://send?text=*' . urlencode(get_the_title()) . '* ' . urlencode(get_the_excerpt()) . ': ' . urlencode(get_the_permalink()) .  '" data-action="share/whatsapp/share" aria-label="Beitrag auf WhatsApp teilen"><i class="icon brands-whatsapp"></i></a>';
                } else {
                    echo '<a href="https://wa.me/?text=*' . urlencode(get_the_title()) . '* ' . urlencode(get_the_excerpt()) . ': ' . urlencode(get_the_permalink()) .  '" data-action="share/whatsapp/share" aria-label="Beitrag auf WhatsApp teilen"><i class="icon brands-whatsapp"></i></a>';
                }
                echo '<a href="mailto:?subject=' . urlencode(get_the_title()) . '&body=' . urlencode(get_the_permalink()) . '" aria-label="Beitrag als E-Mail teilen"><i class="icon icon-email"></i></a>';
                echo '<a href="javascript:window.print()" aria-label="Beitrag drucken"><i class="icon icon-local_printshop"></i></a>';
            echo '</div>';
        echo '</div>';
        ?>

        <?php
        // Disclaimer
        echo '<div class="post-disclaimer">';
            echo '<section class="post-disclaimer-accordion" id="disclaimer">';
                echo '<h3 class="post-disclaimer-accordion--title">Disclaimer<i class="icon icon-add"></i></h3>';
                echo '<div class="post-disclaimer-accordion--content">';
                    echo '<p>Die Tipps auf dieser Website dienen der allgemeinen Information und ersetzen keine medizinische Beratung. Bei gesundheitlichen Problemen, wie wiederkehrenden oder schweren Symptomen oder bei gesundheitlichen Fragen wende Dich bitte an Deine Ärzt*in.</p>';
                    echo '<a class="button-primary" href="' . get_permalink( get_page_by_path( 'disclaimer' ) ) . '">Weitere Informationen</a>';
            echo '</section>';
        echo '</div>';
        ?>

</article>

<?php
// Post footer
get_footer();