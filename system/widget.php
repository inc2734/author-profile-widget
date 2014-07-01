<?php
/**
 * Name: Author Profile Widget Base
 * Version: 1.0.0
 * Author: inc2734
 * Created : March 25, 2014
 * Modified:
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