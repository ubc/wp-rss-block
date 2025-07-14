<?php
/**
 * Register blocks
 *
 * @package ctlt-rss-block
 */

namespace UBC\CTLT\Block\RSS;

if ( ! class_exists( '\UBC\CTLT\Block\APIInnerBlocks\APIInnerBlocks' ) ) {
	return;
}

use UBC\CTLT\Block\APIInnerBlocks\APIInnerBlocks;

/*************************** Event Template Block */
APIInnerBlocks::register_block(
	'resource-template',
	'ctlt/api-rss-template',
	array(
		'title'       => 'UBC RSS Template',
		'description' => 'Display the rss template',
		'parent'      => 'ubc/ctlt-rss',
	)
);
/*************************** End Event Template Block */

APIInnerBlocks::register_block(
	'resource-title',
	'ctlt/api-rss-title',
	array(
		'title'        => 'RSS Title',
		'description'  => 'Display the title of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-title' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);

APIInnerBlocks::register_block(
	'resource-excerpt',
	'ctlt/api-rss-excerpt',
	array(
		'title'        => 'RSS Excerpt',
		'description'  => 'Display the excerpt of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-excerpt' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);

APIInnerBlocks::register_block(
	'resource-datetime',
	'ctlt/api-rss-dates',
	array(
		'title'        => 'RSS Dates',
		'description'  => 'Display the dates of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-dates' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);

APIInnerBlocks::register_block(
	'resource-content',
	'ctlt/api-rss-description',
	array(
		'title'        => 'RSS Description',
		'description'  => 'Display the description of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-description' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);

APIInnerBlocks::register_block(
	'resource-terms',
	'ctlt/api-rss-categories',
	array(
		'title'        => 'RSS Categories',
		'description'  => 'Display the categories of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-categories' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);

APIInnerBlocks::register_block(
	'resource-image',
	'ctlt/api-rss-image',
	array(
		'title'        => 'RSS Image',
		'description'  => 'Display the image of the RSS feed',
		'uses_context' => array( 'ctlt/api-rss-image' ),
		'ancestor'     => array( 'ctlt/api-rss-template' ),
	)
);
