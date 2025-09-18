<?php

// https://developer.wordpress.org/reference/hooks/allowed_block_types_all/
function example_filter_allowed_block_types_when_post_provided( $allowed_block_types, $editor_context ) {
	$user = wp_get_current_user();
	$allowed_role = array( 'team', 'um_team' );
	if ( array_intersect( $allowed_role, $user->roles ) ) {
		if ( ! empty( $editor_context->post ) ) {
			return array( 
				'core/audio',
				'core/button',
				'core/buttons',
				'core/code',
				'core/column',
				'core/columns',
				'core/cover',
				'core/details',
				'core/embed',
				'core/file',
				'core/footnotes',
				'core/gallery',
				'core/group',
				'core/heading',
				'core/image',
				'core/list',
				'core/list-item',
				'core/media-text',
				'core/navigation-link',
				'core/paragraph',
				'core/preformatted',
				'core/pullquote',
				'core/quote',
				'core/separator',
				'core/spacer',
				'core/table',
				'core/verse',
				'core/video'
			);
		}
		return $allowed_block_types;
	}
}
add_filter( 'allowed_block_types_all', 'example_filter_allowed_block_types_when_post_provided', 10, 2 );