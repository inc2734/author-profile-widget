<?php
/**
 * Name: Author Profile Widget Shortcodes
 * Version: 1.0.1
 * Author: inc2734
 * Created : July 16, 2014
 * Modified: September 3, 2014
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
Class Author_Profile_Widget_Shortcodes {

	/**
	 * __construct
	 * @param array $atts ショートコードの属性値
	 */
	public function __construct() {
		add_shortcode( 'apw-profile', array( $this, 'profile_shortcode' ) );
		add_shortcode( 'apw-list', array( $this, 'list_shortcode' ) );
	}

	/**
	 * profile_shortcode
	 * @param array $atts ショートコードの属性値
	 */
	public function profile_shortcode( $atts, $template = '' ) {
		if ( empty( $template ) ) {
			$template = '
<dl class="author-profile vcard">
<dt class="avatar">{avatar 100}</dt>
<dt class="fn">{display_name}</dt>
<dd>{description}</dd>
</dl>';
		}
		return preg_replace_callback( '/\{(.+?)\}/',
			array( $this, 'parse_profile_template' ),
			$template
		);
	}

	/**
	 * parse_profile_template
	 * profile ショートコードの template を解析・置換
	 * @param array $matches
	 * @return string
	 */
	private function parse_profile_template( $matches ) {
		$user_id = get_the_author_meta( 'ID' );
		if ( is_author() ) {
			global $author;
			$user_id = $author;
		}
		if ( preg_match( '/^avatar ?(\d+)?$/', $matches[1], $reg ) ) {
			$size = '';
			if ( !empty( $reg[1] ) ) {
				$size = $reg[1];
			}
			return get_avatar( $user_id, $size );
		}
		return get_the_author_meta( $matches[1], $user_id );
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

		$items = '';
		foreach ( $users as $user ) {
			$items .= sprintf(
				'<li><a href="%s" title="%s">%s</a></li>',
				esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
				esc_attr( $user->display_name ),
				get_avatar( $user->ID, $atts['size'], '', $user->display_name )
			);
		}
		return sprintf(
			'<ul class="authors-list">%s</ul>',
			$items
		);
	}
}
