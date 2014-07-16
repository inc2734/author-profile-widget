<?php
/**
 * Name: Author Profile Widget Base
 * Version: 1.0.1
 * Author: inc2734
 * Created : March 25, 2014
 * Modified: July 16, 2014
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class Author_Profile_Widget_Base extends WP_Widget {

	protected $domain;

	/**
	 * __construct
	 */
	public function __construct() {
		$this->domain = Author_Profile_Widget_Config::DOMAIN;
	}

	/**
	 * _parse_options
	 */
	protected function _parse_options( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		return $instance;
	}
}