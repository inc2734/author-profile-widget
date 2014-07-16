<?php
/**
 * Name: Author Profile Widget Profile
 * Version: 1.0.1
 * Author: inc2734
 * Created : March 25, 2014
 * Modified: July 16, 2014
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class Author_Profile_Widget_Profile extends Author_Profile_Widget_Base {

	public $defaults = array();

	/**
	 * construct
	 */
	public function __construct() {
		parent::__construct();

		$this->defaults = array(
			'title'    => __( 'Profile', $this->domain ),
			'template' => '
<dl class="author-profile vcard">
<dt class="avatar">{avatar 100}</dt>
<dt class="fn">{display_name}</dt>
<dd>{description}</dd>
</dl>',
		);

		$this->WP_Widget( 'author-profile-widget-profile', __( 'Profile', $this->domain ), array(
			'classname'   => 'author-profile-widget-profile',
			'description' => __( 'Author profile', $this->domain )
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
			$user_id = $this->get_field_id( 'template' );
			$user_name = $this->get_field_name( 'template' );
			?>
			<label for="<?php echo esc_attr( $user_id ); ?>"><?php _e( 'Template', $this->domain ); ?>:</label>
			<textarea id="<?php echo esc_attr( $user_id ); ?>" name="<?php echo esc_attr( $user_name ); ?>" class="widefat" rows="8"><?php echo $instance['template']; ?></textarea>
			<span class="description"><?php _e( 'you can use "<a href="http://codex.wordpress.org/Function_Reference/the_author_meta" target="?blank">the_author_meta</a>" field. e.g {user_login}', $this->domain ); ?></span><br />
			<span class="description"><?php _e( 'An avatar can also be displayed. e.g {avatar}, {avatar 95}', $this->domain ); ?></span>
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
		$instance = wp_parse_args( $new_instance, $old_instance );
		return $instance;
	}

	/**
	 * widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( is_singular() || is_author() ) {
			$instance = $this->_parse_options( $instance );
			echo $args['before_widget'];
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			echo preg_replace_callback( '/\{(.+)\}/',
				array( $this, '_parse_template' ),
				$instance['template']
			);
			echo $args['after_widget'];
		}
	}
	public function _parse_template( $matches ) {
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
}