<?php
/**
 * Render Events Template Block on the serverside.
 *
 * @package ctlt_events_block
 */

namespace UBC\CTLT\Block\RSS;

if ( empty( $attributes['source'] ) ) {
	return;
}

$source      = esc_url( $attributes['source'] );
$per_page    = absint( $attributes['postPerPage'] );
$offset      = absint( $attributes['offset'] );
$instance_id = absint( $attributes['instanceId'] );

/** Current page is from URL search param. Set by the pagination block. */
$current_page = isset( $_GET[ $instance_id . '-paged' ] ) ? (int) $_GET[ $instance_id . '-paged' ] : 1;

$source   = apply_filters( 'wp_rss_block_fetch_source', $source, $attributes );
$response = fetch_rss( $source, $per_page, $current_page, $offset );

$total_pages = ceil( absint( $response['maxitems'] ) / $per_page );
$items       = $response['items'];

/**
 * Set up block attributes including attributes required for WP Interactivity API.
 */
$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                 => 'ctlt-rss-block',
		'data-wp-interactive'   => 'ubc-wp-api-innerblocks',
		'data-wp-router-region' => 'ubc-wp-api-innerblocks-router-region-rss-' . $instance_id,
	)
);

$content = '';

$block_instance = $block->parsed_block;

/**
 * IMPORTANT: This is use to provide context to the inner blocks on the PHP side.
 *
 * 1. The name of the context should match what's included in InnerBlocks block.json file.
 * 2. The value of the context need to formatted to what the innerblocks expects. Eg, terms and datetimes.
 */
$filter_block_context = static function ( $context, $parsed_block ) use ( $items, $instance_id, $total_pages ) {

	// The contexts are only passed to Template block and all the innerblocks within Template block.
	if ( 'ctlt/api-rss-template' === $parsed_block['blockName'] || 'ubc/api-no-results' === $parsed_block['blockName'] ) {
		$context['resources'] = $items;
	}

	/**
	 * Pagination required contexts. Only nessasory for server side, since client side pagination in the editor is fake.
	 */
	if ( 'ubc/api-pagination' === $parsed_block['blockName'] ) {
		$context['instance_id'] = $instance_id;
		$context['total_pages'] = $total_pages;
	}

	return $context;
};

// Use an early priority to so that other 'render_block_context' filters have access to the values.
add_filter( 'render_block_context', $filter_block_context, 10, 2 );
// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
// `render_callback` and ensure that no wrapper markup is included.
$block_content = ( new \WP_Block( $block_instance ) )->render( array( 'dynamic' => false ) );
remove_filter( 'render_block_context', $filter_block_context, 10, 2 );

printf(
	'<div %1$s %2$s>%3$s</div>',
	// phpcs:ignore
	$wrapper_attributes,
	wp_interactivity_data_wp_context(
		array(
			'currentPage' => $current_page,
			'instanceId'  => $instance_id,
		),
	),
	// phpcs:ignore
	$block_content
);
