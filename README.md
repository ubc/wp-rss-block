# WP RSS Block

Contributors:      Kelvin Xu, Richard Tape\
Tags:              block\
Tested up to:      6.5\
Stable tag:        1.0.0\
License:           GPL-2.0-or-later\
License URI:       https://www.gnu.org/licenses/gpl-2.0.html\

## Description
The core RSS block enables users to display content from an external RSS feed using a pre-defined template. This RSS block plugin enhances functionality by allowing users to define their own templates, similar to the Query Loop block. Users can add inner blocks within the Resource Template block to customize the layout and style the elements without the use of custom CSS.

## How to use
1. Install and activate the <a href="https://github.com/ubc/WP-API-Innerblocks" target="_blank">WP API Innerblocks</a> plugin.
2. Install and activate the WP RSS Block plugin.
3. With a post or page, add WP RSS Block.
4. Enter RSS feed URL under settings and click update. By default, the feed results will be cached for 12 hours by WordPress, click the 'Reset Feed Cache' button to reset the cache and get the most recent content.


## Extensions
- <a href="https://github.com/ubc/wp-rss-block--multifeed" target="_blank">Multifeed Extension</a> - This extension allows users to display content from multiple feeds. The contents will be merged and sort by time and date.
- <a href="https://github.com/ubc/wp-rss-block--additional-tags" target="_blank">Additional Extension</a> - This extension adds post feature image and custom fields to the RSS feed, and the content can be displayed using the RSS feed plugin with the same extension. For example, if you would like to display content(feature image, custom fields) from site B to site A. Make sure the WP RSS Block and the Additional extension are both installed and activated on both sites. Then use the WP RSS Block on site A to show feeds from site B.

## Changelog

v2.0.0
- Broken change introduced. It will only work with WP InnerBlocks plugin 2.x. Reworked the plugin so that instead of directly using the resources innerblocks from the InnerBlocks plugin. The RSS block can now create its own specific blocks based on existing resource innerblocks. 

v1.0.1
- Add support for Resource No Results block.