<?php
/**
 * Plugin Name: Copier Hooks for Custom Post Widget
 * Plugin URI: http://www.vanderwijk.com/wordpress/wordpress-custom-post-widget/
 */

/**
 * Copy the CPW Widget Settings.
 * 
 * We need to remap the post ID selected in the widget if we have copied all
 * Custom Post Types before
 * 
 */
function copier_hooks_set_custom_post_widget_settings( $source_blog_id, $user_id, $copier_options ) {
	// 1. Get the source blog widget settings
	$source_settings = get_blog_option( $source_blog_id, 'widget_custom_post_widget' );

	if ( empty( $source_settings ) )
		return;

	// 2. Get the posts mapping array
	$posts_map = isset( $copier_options['posts_mapping'] ) ? $copier_options['posts_mapping'] : array();

	// 3. Replace the Source Post ID in the widget settings for the new post ID (if exist)
	$new_widget_settings = $source_settings;
	foreach ( $source_settings as $widget_id => $widget_settings ) {
		$source_post_id = $widget_settings['custom_post_id'];
		if ( isset ( $posts_map[ $source_post_id ] ) ) {
			// We have found the post mapped! Just replace the ID for the new one
			$new_post_id = $posts_map[ $source_post_id ];
			$new_widget_settings[ $widget_id ]['custom_post_id'] = $new_post_id;
		}
	}

	// 4. And update the settings in the new blog (the current one)
	update_option( 'widget_custom_post_widget', $new_widget_settings );
}
add_action( 'wpmudev_copier-copy-after_copying', 'copier_hooks_set_custom_post_widget_settings', 10, 3 );


/**
 * Replace the source posts IDs in the CPW Shortcode
 */
function copier_hooks_replace_custom_post_widget_shortcode( $source_blog_id, $user_id, $copier_options ) {
	
	// Posts mapping array
	$posts_map = $copier_options['posts_mapping'];

	if ( empty( $posts_map ) )
		return;

	// Get all posts that may have the content block shortcode in them
	$all_posts = get_posts( array(
	    'post_type' => 'any',
	    'posts_per_page' => -1,
	    'ignore_sticky_posts' => true,
	    's' => 'content_block'
	) );


	// Shortcode patterns
	$shortcode_pattern = get_shortcode_regex();

	foreach ( $all_posts as $post ) {
		$_post = (array)$post;

		// Search for shortcodes in the post content
		if ( 
	        preg_match_all( '/'. $shortcode_pattern .'/s', $_post['post_content'], $matches ) 
	        && array_key_exists( 2, $matches )
	        && in_array( 'content_block', $matches[2] )
	    ) {
	    	$do_replace = false;
			foreach ( $matches[2] as $key => $shortcode_type ) {

				if ( 'content_block' == $shortcode_type ) {
					// Yeah! We have found the shortcode in this post, let's replace the ID if we can

		    		// Get the shortcode attributes
	                $atts = shortcode_parse_atts( $matches[3][ $key ] );

	                if ( isset( $atts['id'] ) ) {
	                	// There is an ID attribute, let's replace it
	                	$source_post_id = absint( $atts['id'] );

	                	if ( ! isset( $posts_map[ $source_post_id ] ) ) {
	                		// There's not such post ID mapped in the array, let's continue
	                		continue;
	                	}

	                	$new_post_id = $posts_map[ $source_post_id ];

	                	// Get the original full shortcode
	                	$full_shortcode = $matches[0][ $key ];

	                	// Replace the ID
	                	$new_atts_ids = str_replace( (string)$source_post_id, $new_post_id, $atts['id'] );

	                	// Now replace the attributes in the source shortcode
	                    $new_full_shortcode = str_replace( $atts['id'], $new_atts_ids, $full_shortcode );

	                    // And finally replace the source shortcode for the new one in the post content
	                    $_post['post_content'] = str_replace( $full_shortcode, $new_full_shortcode, $_post['post_content'] );

	                    // So we have found a replacement to make, haven't we?
	                    $do_replace = true;

	                }

		    	}
	    	}

	    	if ( $do_replace ) {
	            // Update the post!
	            $postarr = array(
	                'ID' => $_post['ID'],
	                'post_content' => $_post['post_content']
	            );

	            wp_update_post( $postarr );
	        }
	    }
	}
	

}

add_action( 'wpmudev_copier-copy-after_copying', 'copier_hooks_replace_custom_post_widget_shortcode', 10, 3 );