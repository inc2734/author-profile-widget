<?php
/**
 * Name: Author Profile Widget Shortcodes
 * Version: 1.0.0
 * Author: inc2734
 * Created : July 16, 2014
 * Modified:
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
Class Author_Profile_Widget_Shortcodes {

	/**
	 * __construct
	 * @param array $atts ショートコードの属性値
	 */
	public function __construct() {
		add_shortcode( 'apw-list', array( $this, 'list_shortcode' ) );
	}

	/**
	 * list_shortcode
	 * @param array $atts ショートコードの属性値
	 */
	public function list_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'size'     => '45',
			'orderby'  => 'login',
			'order'    => 'ASC',
			'excludes' => '',
		), $atts );

		$users = get_users( array(
			'exclude' => array( $atts['excludes'] ),
			'orderby' => $atts['orderby'],
			'order' => $atts['order'],
		) );
		?>
		<ul class="authors-list">
			<?php foreach ( $users as $user ) : ?>
			<li><a href="<?php echo esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ); ?>" title="<?php echo esc_attr( $user->display_name ); ?>"><?php echo get_avatar( $user->ID, $atts['size'], '', $user->display_name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}