<?php
/**
 * Name: Author Profile Widget List
 * Version: 1.0.2
 * Author: inc2734
 * Created : July 1, 2014
 * Modified: July 16, 2014
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class Author_Profile_Widget_List extends Author_Profile_Widget_Base {

	public $defaults = array();
	protected $orderby = array();
	protected $order = array();

	/**
	 * construct
	 */
	public function __construct() {
		parent::__construct();

		$this->defaults = array(
			'title'    => __( 'Authors List', $this->domain ),
			'size'     => '45',
			'orderby'  => 'login',
			'order'    => 'ASC',
			'excludes' => '',
		);

		$this->orderby = array(
			'login'        => __( 'login ID', $this->domain ),
			'email'        => __( 'E-mail', $this->domain ),
			'url'          => __( 'URL', $this->domain ),
			'registered'   => __( 'Registerd', $this->domain ),
			'display_name' => __( 'Display name', $this->domain ),
			'post_count'   => __( 'Post count', $this->domain ),
			'orderby'      => __( 'Order', $this->domain ),
		);

		$this->order = array(
			'ASC'  => __( 'Ascending', $this->domain ),
			'DESC' => __( 'Descending', $this->domain ),
		);

		$this->WP_Widget( 'author-profile-widget-list', __( 'Authors List', $this->domain ), array(
			'classname'   => 'author-profile-widget-list',
			'description' => __( 'Authors List', $this->domain )
		) );
	}

	/**
	 * form
	 * @param array $instance
	 */
	public function form( $instance ) {
		$instance = $this->_parse_options( $instance );
		?>
		<p>
			<?php
			$title_id = $this->get_field_id( 'title' );
			$title_name = $this->get_field_name( 'title' );
			?>
			<label for="<?php echo esc_attr( $title_id ); ?>"><?php _e( 'Title', $this->domain ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" id="<?php echo esc_attr( $title_id ); ?>" name="<?php echo esc_attr( $title_name ); ?>" class="widefat" />
		</p>
		<p>
			<?php
			$size_id = $this->get_field_id( 'size' );
			$size_name = $this->get_field_name( 'size' );
			?>
			<label for="<?php echo esc_attr( $size_id ); ?>"><?php _e( 'Size', $this->domain ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $instance['size'] ); ?>" id="<?php echo esc_attr( $size_id ); ?>" name="<?php echo esc_attr( $size_name ); ?>" size="4" maxlength="4" />px
		</p>
		<p>
			<?php
			$orderby_id = $this->get_field_id( 'orderby' );
			$orderby_name = $this->get_field_name( 'orderby' );
			?>
			<label for="<?php echo esc_attr( $orderby_id ); ?>"><?php _e( 'Order by', $this->domain ); ?>:</label>
			<select id="<?php echo esc_attr( $orderby_id ); ?>" name="<?php echo esc_attr( $orderby_name ); ?>">
				<?php foreach ( $this->orderby as $orderby => $val ) : ?>
				<option value="<?php echo esc_attr( $orderby ); ?>"<?php selected( $orderby, $instance['orderby'] ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<?php
			$order_id = $this->get_field_id( 'order' );
			$order_name = $this->get_field_name( 'order' );
			?>
			<label for="<?php echo esc_attr( $order_id ); ?>"><?php _e( 'Order', $this->domain ); ?>:</label>
			<select id="<?php echo esc_attr( $order_id ); ?>" name="<?php echo esc_attr( $order_name ); ?>">
				<?php foreach ( $this->order as $order => $val ) : ?>
				<option value="<?php echo esc_attr( $order ); ?>"<?php selected( $order, $instance['order'] ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<?php
			$excludes_id = $this->get_field_id( 'excludes' );
			$excludes_name = $this->get_field_name( 'excludes' );
			?>
			<label for="<?php echo esc_attr( $excludes_id ); ?>"><?php _e( 'Exclude IDs ( Comma separated value )', $this->domain ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $instance['excludes'] ); ?>" id="<?php echo esc_attr( $excludes_id ); ?>" name="<?php echo esc_attr( $excludes_name ); ?>" class="widefat" />
		</p>
		<?php
	}

	/**
	 * update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args( $new_instance, $this->defaults );
		// size
		$new_instance['size'] = mb_convert_kana( $new_instance['size'], 'a', 'utf-8' );
		if ( !preg_match( '/^\d+$/', $new_instance['size'] ) ) {
			$new_instance['size'] = $this->defaults['size'];
		}
		// orderby
		if ( !array_key_exists( $new_instance['orderby'], $this->orderby ) ) {
			$new_instance['orderby'] = $this->defaults['orderby'];
		}
		// order
		if ( !array_key_exists( $new_instance['order'], $this->order ) ) {
			$new_instance['order'] = $this->defaults['order'];
		}
		// excludes
		if ( !preg_match( '/^(\d+?,?)+$/', $new_instance['excludes'] ) ) {
			$new_instance['excludes'] = $this->defaults['excludes'];
		}
		$instance = wp_parse_args( $new_instance, $old_instance );
		return $instance;
	}

	/**
	 * widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$instance = $this->_parse_options( $instance );
		echo $args['before_widget'];
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		echo do_shortcode( sprintf ( 
			'[apw-list size="%d" orderby="%s" order="%s" excludes="%s"]',
			$instance['size'],
			$instance['orderby'],
			$instance['order'],
			$instance['excludes']
		) );
		echo $args['after_widget'];
	}
}