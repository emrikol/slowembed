=== slowEmbed ===
Contributors: emrikol
Tags: oembed
Requires at least: 4.7.1
Tested up to: 4.7.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

If you need to debug oEmbed data, I recommed using these two filters to stop oEmbed caching:

```
add_filter( 'oembed_ttl', function() { return 0; } );
add_filter( 'wp', function() { global $post; $GLOBALS['wp_embed']->delete_oembed_caches( $post->ID ); } );
```