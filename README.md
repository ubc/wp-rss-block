# WP RSS Block

Contributors:      Kelvin Xu, Richard Tape\
Tags:              block\
Tested up to:      6.5\
Stable tag:        1.0.0\
License:           GPL-2.0-or-later\
License URI:       https://www.gnu.org/licenses/gpl-2.0.html\

## Description
The core RSS block allows users to display content from an external RSS feed with pre-defined template. The difference in this plugin is that it allows users to define the template themselvies by using the similiar technique as the Query Loop block where innerblocks can be added within the Resource Template block.

## How to use
1. Install and activate the <a href="https://github.com/ubc/WP-API-Innerblocks" target="_blank">WP API Innerblocks</a> plugin.
2. Install and activate the WP RSS Block plugin.
3. With a post or page, add WP RSS Block.


## Extensions
- <a href="https://github.com/ubc/wp-rss-block--multifeed" target="_blank">Multifeed Extension</a> - This extension allows users to display content from multiple feeds. The contents will be merged and sort by time and date.
- <a href="https://github.com/ubc/wp-rss-block--additional-tags" target="_blank">Additional Extension</a> - This extension adds post feature image and custom fields to the RSS feed, and the content can be displayed using the RSS feed plugin with the same extension. For example, if you would like to display content(feature image, custom fields) from site B to site A. Make sure the WP RSS Block and the Additional extension are both installed and activated on both sites. Then use the WP RSS Block on site A to show feeds from site B.

## Changelog
v1.0.1
- Add support for Resource No Results block.