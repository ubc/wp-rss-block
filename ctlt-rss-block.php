<?php
/**
 * Plugin Name:       WP RSS Block
 * Description:       Allow users to display external RSS feed on a page.
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Version:           2.0.0
 * Author:            Kelvin Xu
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ctlt-rss-block
 * Requires Plugins:  ubc-wp-api-innerblocks
 *
 * @package           ctlt-rss-block
 */

namespace UBC\CTLT\Block\RSS;

define( 'CTLT_RSS_BLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function register_block() {
	require_once CTLT_RSS_BLOCK_PLUGIN_DIR . 'includes/register-blocks.php';

	/**
	 * Register the blocks within the plugin based on the directory structure.
	 * WordPress will search the blocks/build folder to locate the blocks.
	 */
	$blocks = array(
		'core'  => array(
			'rss' => array(
				'skip_inner_blocks' => true,
			),
		),
		'inner' => array(
			// Custom Inner Blocks belongs to the plugin.
		),
	);

	foreach ( $blocks as $block_type_dir => $block_type ) {
		foreach ( $block_type as $dir => $args ) {
			register_block_type_from_metadata( __DIR__ . '/blocks/build/' . $block_type_dir . '/' . $dir, $args );
		}
	}
}

/**
 * This function does couple of things.
 *
 * 1. Adds 'ubc/api-template' as an ancestor block to the innerblocks that you would like to activate.
 * 2. Adds 'ubc/ctlt-rss' as a parent block of the pagination block and template block.
 *
 * @param array $metadata The metadata of the block.
 * @return array The updated metadata with added ancestor block.
 */
function add_support_ubc_inner_blocks( $metadata ) {

	if ( 'ubc/api-pagination' === $metadata['name'] || 'ctlt/api-rss-template' === $metadata['name'] || 'ubc/api-no-results' === $metadata['name'] ) {
		$metadata['parent'][] = 'ubc/ctlt-rss';
	}

	return $metadata;
}


/**
 * Ajax handler to fetch RSS feed.
 */
function ajax_fetch_rss() {
	if ( ! isset( $_POST['source'] ) || ! isset( $_POST['per_page'] ) || ! isset( $_POST['current_page'] ) || ! isset( $_POST['offset'] ) ) {
		wp_send_json_error();
	}
	$source       = json_decode( wp_unslash( $_POST['source'] ), true );
	$per_page     = absint( $_POST['per_page'] );
	$current_page = absint( $_POST['current_page'] );
	$offset       = absint( $_POST['offset'] );

	$content = fetch_rss( $source, $per_page, $current_page, $offset );

	wp_send_json_success( $content );
}

/**
 * Ajax handler to clear transient cache generated by fetch_feed method.
 */
function ajax_reset_rss_caches() {
	if ( ! isset( $_POST['source'] ) ) {
		wp_send_json_error();
	}

	$source          = esc_url( $_POST['source'] );
	$md5_of_feed_url = md5( $source );

	delete_transient( 'feed_' . $md5_of_feed_url );
	delete_transient( 'feed_mod_' . $md5_of_feed_url );
	wp_send_json_success();
}//end ajax_reset_rss_caches()

/**
 * Initiate API request. Fetch and format RSS feed content for context needs.
 *
 * @param string $source The URL of the RSS feed.
 * @param int    $per_page The number of items to fetch per page.
 * @param int    $current_page The current page number.
 * @param int    $offset The number of items to skip.
 * @return array An array containing the maximum number of items and an array of items.
 */
function fetch_rss( $source, $per_page, $current_page, $offset ) {
	include_once ABSPATH . WPINC . '/feed.php';

	$feed = fetch_feed( $source );

	if ( is_wp_error( $feed ) ) {
		return array();
	}

	// Figure out how many total items there are.
	$maxitems = $feed->get_item_quantity() - $offset;

	// Build an array of all the items.
	$rss_items = $feed->get_items( ( $current_page - 1 ) * $per_page + $offset, $per_page );

	$rss_items = array_map(
		function ( $rss_item ) {
			return apply_filters(
				'wpapi_filter_item_context',
				array(
					'ctlt/api-rss-title'       => array(
						'title' => $rss_item->get_title(),
						'link'  => $rss_item->get_permalink(),
					),
					'ctlt/api-rss-excerpt'     => array(
						'excerpt' => $rss_item->get_description(),
						'link'    => $rss_item->get_permalink(),
					),
					'ctlt/api-rss-description' => array(
						'description' => $rss_item->get_content(),
					),
					'ctlt/api-rss-dates'       => array(
						'datetimes' => array(
							array(
								'label' => 'published_date',
								'value' => $rss_item->get_gmdate( 'Y-m-d H:i:s' ),
							),
						),
					),
					'ctlt/api-rss-categories'  => array(
						'terms' => format_terms( $rss_item->get_categories(), 'type' ),
					),
					'ctlt/api-rss-image'       => array(
						'images' => $rss_item->get_enclosure() ? array(
							array(
								'label' => 'feed_item_image',
								'src'   => $rss_item->get_enclosure()->get_link(),
							),
						) : array(),
					),
					'ctlt/api-rss-custom-field' => array(
						'custom' => array(),
					),
				),
				'ubc/ctlt-rss',
				$rss_item,
			);
		},
		$rss_items
	);

	return array(
		'maxitems' => $maxitems,
		'items'    => $rss_items,
	);
}

	/**
	 * Group terms based on taxonomy type. Format the terms based on context needs.
	 *
	 * @param array  $terms The array of terms to be formatted.
	 * @param string $field The field to group the terms by.
	 * @return array The formatted array of terms.
	 */
function format_terms( $terms, $field ) {

	// Group items by field.
	$grouped_items = array();
	foreach ( $terms as $term ) {
		$term      = (array) $term;
		$field_key = $term[ $field ];
		if ( ! isset( $grouped_items[ $field_key ] ) ) {
			$grouped_items[ $field_key ] = array();
		}
		$grouped_items[ $field_key ][] = $term;
	}

	// Convert the grouped items to an array of objects.
	$result = array();
	foreach ( $grouped_items as $field_key => $terms_array ) {
		$result[] = array(
			'taxonomy' => $field_key,
			'terms'    => $terms_array,
		);
	}

	// Map over the result to format the terms.
	$result = array_map(
		function ( $group ) {
			$group['terms'] = array_map(
				function ( $term, $index ) {
					return array(
						'id'   => $index,
						'name' => $term['term'],
						'link' => '',
					);
				},
				$group['terms'],
				array_keys( $group['terms'] )
			);
			return $group;
		},
		$result
	);

	return $result;
}


/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_fetch_rss', __NAMESPACE__ . '\\ajax_fetch_rss' );
add_action( 'wp_ajax_reset_rss_caches', __NAMESPACE__ . '\\ajax_reset_rss_caches' );
add_filter( 'block_type_metadata', __NAMESPACE__ . '\\add_support_ubc_inner_blocks' );
add_action( 'init', __NAMESPACE__ . '\\register_block' );
