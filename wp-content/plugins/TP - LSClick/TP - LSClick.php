<?php
/**
 * Plugin Name: TP - LSClick
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: A brief description of the Plugin.
 * Version: The Plugin's Version Number, e.g.: 1.0
 * Author: Name Of The Plugin Author
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: A "Slug" license name e.g. GPL2
 */
 
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/*
 * Hide "View As" interface for other user roles than Administrator.
 * Users need 'manage_options' capability to use and see "View As"
 */

 
 function trpc_lsclick_enqueue_scriptandstyle() {
 wp_enqueue_script('JScode', plugin_dir_url( __FILE__ ) . 'assets/js/JScode.js', array('jquery'), '1.0.0', true );
 
 wp_enqueue_style('CSSstyle', plugin_dir_url( __FILE__ ) . 'assets/css/CSSstyle.css', array(), '1.0.0' );
 }
 
 
add_action( 'wp_enqueue_scripts', 'trpc_lsclick_enqueue_scriptandstyle' );
