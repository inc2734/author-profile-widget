<?php
/**
 * Plugin Name: Author Profile Widget
 * Plugin URI: https://github.com/inc2734/author-profile-widget
 * Description: Author Profile Widget add authors list widget and profile widget.
 * Version: 0.4.0
 * Author: inc2734
 * Author URI: http://2inc.org
 * Created : July 1, 2014
 * Modified:
 * Text Domain: author-profile-widget
 * Domain Path: /languages/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
$Author_Profile_Widget = new Author_Profile_Widget();

class Author_Profile_Widget {
	protected $name;
	protected $domain;

	/**
	 * __construct
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		include_once( plugin_dir_path( __FILE__ ) . 'system/config.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/widget.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/profile.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/list.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'system/shortcodes.php' );
	}

	/**
	 * plugins_loaded
	 */
	public function plugins_loaded() {
		$this->domain = Author_Profile_Widget_Config::DOMAIN;
		load_plugin_textdomain( $this->domain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'personal_options_update', array( $this, 'edit_user_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );
		add_action( 'widgets_init', array( $this, 'widget' ) );
		add_action( 'pre_user_query', array(  $this, 'extended_user_search' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );
		$Author_Profile_Widget_Shortcodes = new Author_Profile_Widget_Shortcodes();
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
