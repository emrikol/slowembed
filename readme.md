# slowEmbed #

```
Contributors: emrikol
Donate link: http://wordpressfoundation.org/donate/
Tags: oembed, opengraph, embed
Requires at least: 4.7.1
Tested up to: 4.7.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
```

Many sites support oEmbeds and auto discovery, but many don't.  This plugin will fall back to Open Graph support if oEmbed is not available.

## Description ##

oEmbeds are amazing, but not supported everywhere.  Sometimes I just want to share a non-oEmbed URL and make it work like it does on Facebook/Twitter/etc.  That's why this plugin is here.

## Installation ##

1. Upload the plugin files to the `/wp-content/plugins/slowembed` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Place embeds [as usual](https://codex.wordpress.org/Embeds), but watch as previously unsupported sites now offer Open Graph "cards" with extra information.

## Changelog ##

### 0.1.7 ###

* Bugfix, removed some PHP notices

### 0.1.6 ###

* Bugfix, Photon should now letterbox images correctly

### 0.1.5 ###

* Bugfix, CSS now enqueues correctly on archives

### 0.1.4 ###

* Bugfix, add a CSS clear to make sure that the slowEmbed style isn't messed up.

### 0.1.3 ###

* Bugfix, if an `og:image` URL is available but empty, we will no longer show a large empty box.

### 0.1.2 ###

* Bugfix, calling wrong object for WP_oEmbed class
* Feature, added new filter `slowembed_oembed_provider` to filter oEmbed provider URL

### 0.1.1 ###

* Fixed bug where URLs without OG data would show a blank card.

### 0.1.0 ###

* First version.

## Hacking ##

If you want to play around with this plugin, you will probably fight with the core oEmbed caching.  Use the following code snippets to "disable" oEmbed caching.  I don't recommend doing this in a production enviornment.

```
add_filter( 'oembed_ttl', function() { return 0; } );
add_filter( 'wp', function() { global $post; $GLOBALS['wp_embed']->delete_oembed_caches( $post->ID ); } );
```

If you want to modify certain oEmbed providers, you can use the `slowembed_oembed_provider` filter:

```
// Do not use WordPress core oEmbeds, use slowEmbed OG data instead.
function slowembed_do_not_use_wp_oembeds( $provider ) {
    if ( false !== strpos( $provider, 'wp-json/oembed/1.0/embed' ) ) {
        return false;
    }
    return $provider;
}
add_filter( 'slowembed_oembed_provider', 'slowembed_do_not_use_wp_oembeds', 10, 1 );
```
