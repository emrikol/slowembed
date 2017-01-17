<?php
/**
 * Plugin Name: slowembed
 * Plugin URI: https://github.com/emrikol/slowembed/
 * Description: Faux oEmbeds using Open Graph data.  Creates an extra remote request for each url.  Very slow :)
 * Version: 0.1.0
 * Text Domain: slowembed
 * Author: Derrick Tennant
 * Author URI: https://emrikol.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * GitHub Plugin URI: https://github.com/emrikol/slowembed/
 *
 * @package WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function slowembed_custom_oembeds( $pre = null, $url, $args ) {
	global $post;
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$wp_oembed = _wp_oembed_get_object();
	$provider = $wp_oembed->get_provider( $url, $args );

	if ( ! $provider || false === $data = $this->fetch( $provider, $url, $args ) ) {
		// Hook into this here, search for OG data, and return it as the result instead.  Or false :)
		$request_args = apply_filters( 'oembed_remote_get_args', $request_args, $url );
		$request = wp_safe_remote_get( $url, $request_args );
		if ( $html = wp_remote_retrieve_body( $request ) ) {
			require_once( __DIR__ . '/Opengraph/Meta.php' );
			require_once( __DIR__ . '/Opengraph/Opengraph.php' );
			require_once( __DIR__ . '/Opengraph/Reader.php' );

			$reader = new Opengraph\Reader();
			$reader->parse( $html );

			$og_data = $reader->getArrayCopy();
			if ( false !== $og_data ) :
				ob_start();
			?>
			<div class="slowembed-preview slowembed-preview__article">
				<div>
					<?php if ( ! empty( $og_data['og:image'] ) ) : ?>
					<div>
						<a href="<?php echo esc_url( $og_data['og:url'] ); ?>" title="<?php echo esc_attr( $og_data['og:title'] ); ?>">
							<img class='slowembed-img-preview' src="<?php echo esc_url( jetpack_photon_url( $og_data['og:image'][0]['og:image:url'], array( 'lb' => '600x315' ) ) ); ?>" width="600" height="315" />
						</a>
					</div>
					<?php endif; ?>
					<div class="slowembed-preview__body">
						<div class="slowembed-preview__title">
							<a href="<?php echo esc_url( $og_data['og:url'] ); ?>" title="<?php echo esc_attr( $og_data['og:title'] ); ?>">
								<?php echo esc_html( trim( $og_data['og:title'] ) ); ?>
							</a>
						</div>
						<div class="slowembed-preview__description">
							<?php echo esc_html( $og_data['og:description'] ); ?>
						</div>
						<div class="slowembed-preview__url"><?php echo esc_html( parse_url( $og_data['og:url'], PHP_URL_HOST ) ); // @codingStandardsIgnoreLine. ?> | <?php echo esc_html( $og_data['og:site_name'] ); ?></div>
					</div>
				</div>
			</div>
			<?php
			// Strip unnecessary whitspace so WordPress doesn't add <p> and <br> tags.
			$og_output = str_replace( array( "\r", "\n", "\t" ), '', ob_get_contents() );
			ob_end_clean();
			endif;

			update_post_meta( $post->ID, '_slowembed', true );
			return $og_output;
		}

		return false;
	}
	/**
	 * Filters the HTML returned by the oEmbed provider.
	 *
	 * @since 2.9.0
	 *
	 * @param string $data The returned oEmbed HTML.
	 * @param string $url  URL of the content to be embedded.
	 * @param array  $args Optional arguments, usually passed from a shortcode.
	 */
	return apply_filters( 'oembed_result', $this->data2html( $data, $url ), $url, $args );
}
add_filter( 'pre_oembed_result', 'slowembed_custom_oembeds', 10, 3 );

function slowembed_eneuque_css() {
	global $post;

	$has_slowembed = get_post_meta( $post->ID, '_slowembed', true );

	if ( $has_slowembed ) {
		wp_enqueue_style( 'slowembed', plugins_url( '/slowembed.css', __FILE__ ), array(), '0.1.0' );
	}
}
add_action( 'wp', 'slowembed_eneuque_css' );
