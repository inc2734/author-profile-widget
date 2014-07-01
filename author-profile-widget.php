<?php
/**
 * Plugin Name: Author Profile Widget
 * Plugin URI: https://github.com/inc2734/author-profile-widget
 * Description: Author Profile Widget add authors list widget and profile widget.
 * Version: 0.3.8
 * Author: inc2734
 * Author URI: http://2inc.org
 * Created : July 1, 2014
 * Modified:
 * Text Domain: author-profile-widget
 * Domain Path: /languages/
 * License: GPL2
 *
 * Copyright 2014 Takashi Kitajima (email : inc@2inc.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
$Author_Profile_Widget = new Author_Profile_Widget();

class Author_Profile_Widget {
	protected $name;
	protected $domain;

	/**
	 * __construct
	 */
	public function __construct() {
		include_once( plugin_dir_path( __FILE__ ) . 'system/config.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/widget.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/profile.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/list.php' );

		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'personal_options_update', array( $this, 'edit_user_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );
		add_action( 'widgets_init', array( $this, 'widget' ) );
		add_action( 'pre_user_query', array(  $this, 'extended_user_search' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );

		$this->domain = Author_Profile_Widget_Config::DOMAIN;
	}

	/**
	 * widget
	 */
	public function widget() {
		register_widget( 'Author_Profile_Widget_Profile' );
		register_widget( 'Author_Profile_Widget_List' );
	}

	/**
	 * edit_user_profile
	 */
	public function edit_user_profile( $profileuser ) {
		if ( !current_user_can( 'edit_users' ) )
			return;
		?>
		<h3><?php _e( 'Order', $this->domain ); ?></h3>
		<?php
		$orderbyncol = $this->domain . '_orderby';
		$orderby = get_user_meta( $profileuser->data->ID, $orderbyncol, true );
		$value = ( empty( $orderby ) ) ? 0 : $orderby;
		wp_nonce_field( $this->domain . '_nonce', '_' . $this->domain . '_nonce', false );
		?>
		<input type="text" name="<?php echo esc_attr( $this->domain ); ?>_orderby" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	/**
	 * edit_user_profile_update
	 */
	public function edit_user_profile_update( $user_id ) {
		if ( !current_user_can( 'edit_users' ) )
			return false;
		$nonce_name = '_' . $this->domain . '_nonce';
		if ( ! isset( $_POST[$nonce_name] ) || ! wp_verify_nonce( $_POST[$nonce_name], $this->domain . '_nonce' ) )
			return false;

		$post = array();
		$orderbyncol = $this->domain . '_orderby';
		$orderby = 0;
		if ( isset( $_POST[$orderbyncol] ) ) {
			if ( preg_match( '/^\d*$/', $_POST[$orderbyncol] ) ) {
				$orderby = $_POST[$orderbyncol];
			}
		}
		update_user_meta( $user_id, $orderbyncol, $orderby );
	}

	/**
	 * extended_user_search
	 */
	public function extended_user_search( $user_query ) {
		global $wpdb;
		$_user_query = $user_query;
		if ( isset( $user_query->query_vars['orderby'] ) && $user_query->query_vars['orderby'] === 'orderby' ) {
			$user_query->query_from .= '
				left join
					(
						SELECT user_id, meta_value FROM `' . _get_meta_table( 'user' ) . '`
							WHERE meta_key = "' . $this->domain . '_orderby"
					) as UD
				on
					`' . $wpdb->prefix . 'users`.ID = UD.user_id
			';
			if ( $user_query->query_vars['order'] == 'DESC' ) {
				$user_query->query_orderby = 'ORDER BY CAST( UD.meta_value AS SIGNED ) DESC';
			} else {
				$user_query->query_orderby = 'ORDER BY CAST( UD.meta_value AS SIGNED ) IS NULL ASC, CAST( UD.meta_value AS SIGNED ) ASC';
			}
		}
		return $user_query;
	}

	/**
	 * CSS適用
	 */
	public function wp_enqueue_script() {
		$url = plugin_dir_url( __FILE__ ) . './css';
		wp_enqueue_style( $this->domain . '-css', $url . '/author-profile-widget.css' );
	}
}
