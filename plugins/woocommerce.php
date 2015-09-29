<?php

add_filter( 'wpmudev_copier_get_source_posts_args', 'copier_woocommerce_order_status' );
/**
 * Add WooCommerce Order statuses to get_posts arguments so they are cloned too.
 *
 * @param Array $args
 *
 * @return Array
 */
function copier_woocommerce_order_status( $args ) {
	if ( ! function_exists( 'WC' ) )
		return $args;

	if ( ! in_array( 'shop_order', $args['post_type'] ) )
		return $args;

	$args['post_status'] = array_merge( $args['post_status'], array(
		'wc-pending',
		'wc-processing',
		'wc-on-hold',
		'wc-completed',
		'wc-cancelled',
		'wc-refunded',
		'wc-failed'
	) );

	return $args;
}