<?php
/*
Plugin Name: Disable WooCommerce Notification
Plugin URI: http://github.com/patilswapnilv/disable-woo-notices
Description: Disable WooCommerce admin notices
Version: 0.1
Author: patilswapnilv	
Author URI: http://swapnilpatil.in
License: GPL2
*/
/*
Copyright 2014 Finding Simple (email : plugins@findingsimple.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  1-1301  USA
*/

/**
 * Init the class after the plugins are loaded.
 */
function init_woo_disable_notices() {
	$Woo_Disable_Notices = new Woo_Disable_Notices();
}
add_action( 'plugins_loaded', 'init_woo_disable_notices');

/**
 * Plugin Main Class.
 *
 */
class Woo_Disable_Notices {

	/**
	 * Initialise
	 */
	public function Woo_Disable_Notices() {

		if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) {

			/**
			 * Woothemes updater notice (pre WC 2.2) - it plays nice and can be removed now
			 */
			remove_action( 'admin_notices', 'woothemes_updater_notice') ;

			/**
			 * Remove actions on the init hook
			 */
			add_action( 'init', array( __CLASS__ , 'remove_on_init' ), 99);

			/**
			 * Remove actions on the admin_print_styles hook
			 */
			//add_action( 'admin_print_styles', array( __CLASS__ , 'remove_on_admin_print_styles' ), 99);

		}

	}


	/**
	 * Hooks into the init action to remove actions
	 * 
	 * It removes all the admin notices in one go
	 */
	public function remove_on_init() {

		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_print_styles', 'WC_Admin_Notices', 'add_notices' );

	}


	/**
	 * Hooks into the admin_print_styles action to remove actions
	 *
	 * Alternate function that can be used if finer control over the notices is needed
	 */
	public function remove_on_admin_print_styles() {

		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_notices', 'WC_Admin_Notices', 'install_notice' );
		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_notices', 'WC_Admin_Notices', 'theme_check_notice' );
		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_notices', 'WC_Admin_Notices', 'template_file_check_notice' );
		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_notices', 'WC_Admin_Notices', 'translation_upgrade_notice' );
		Woo_Disable_Notices::remove_filters_for_anonymous_class( 'admin_notices', 'WC_Admin_Notices', 'mijireh_notice' );

	}


	/**
	 * Remove a hook when a class method is used and the class doesn't have variable, but you know the class name
	 *
	 * This is needed because the WC_Admin_Notices class isn't assigned to a variable
	 *
	 * Props: https://github.com/herewithme/wp-filters-extras
	 */
	public function remove_filters_for_anonymous_class( $hook_name = '', $class_name ='', $method_name = '', $priority = 10 ) {
		global $wp_filter;
		
		// Take only filters on right hook name and priority
		if ( !isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority]) )
			return false;
		
		// Loop on filters registered
		foreach( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method)
			if ( isset($filter_array['function']) && is_array($filter_array['function']) ) {
				// Test if object is a class, class and method is equal to param !
				if ( is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && get_class($filter_array['function'][0]) == $class_name && $filter_array['function'][1] == $method_name ) {
					unset($wp_filter[$hook_name][$priority][$unique_id]);
				}
			}
			
		}
		
		return false;
	}

}
