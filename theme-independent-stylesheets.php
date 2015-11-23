<?php
/**
 * Plugin Name: Theme-Independent Stylesheets
 * Plugin URI: https://github.com/jshoptaw/theme-independent-stylesheets
 * Description: Allows for use of uploaded stylesheets (.css files) to be used alongside any theme
 * Version: 1.0.2
 * Author: Jakob Shoptaw
 * Text Domain: theme_independent_stylesheets_textdomain
 * Domain Path: /locale/
 * License: GPL2
 */

/*  Copyright 2015 Jakob Shoptaw  (email: jakob.shoptaw@gmail.com)

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

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Block direct access to this file

$plugin_basename = plugin_basename( __FILE__ );

require_once( plugin_dir_path( __FILE__ ) . 'inc/definitions.php' );

require_once( TISSHEETS_CLASSES . 'class-tissheets-plugin-functions.php' ); // Various plugin functions

if ( is_admin() ) {
    require_once( TISSHEETS_CLASSES . 'class-tissheets-settings.php' ); // Plugin settings page
    $tissheets_settings_page = new TISSheets_Settings();

    add_action( 'init', 'TISSheets_Plugin_Functions::load_translation_files' );
}

register_activation_hook( __FILE__, 'TISSheets_Plugin_Functions::tissheets_activate' );

add_filter( 'upload_mimes', 'TISSheets_Plugin_Functions::custom_upload_mimes' );

add_filter( 'plugin_action_links', 'TISSheets_Plugin_Functions::tissheets_plugin_links', 10, 2 );
   
add_action( 'wp_enqueue_scripts', 'TISSheets_Plugin_Functions::enqueue_external_styles', 11 );

add_action( 'wp_head', 'TISSheets_Plugin_Functions::print_inline_styles', 11 );
