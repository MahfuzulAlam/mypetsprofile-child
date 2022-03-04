<?php

/**
 * @package MyPetsProfile Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since MyPetsProfile Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
	/**
	 * Makes child theme available for translation.
	 * Translations can be added into the /languages/ directory.
	 */

	// Translate text from the PARENT theme.
	load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

	// Translate text from the CHILD theme only.
	// Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
	// load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
	/**
	 * Scripts and Styles loaded by the parent theme can be unloaded if needed
	 * using wp_deregister_script or wp_deregister_style.
	 *
	 * See the WordPress Codex for more information about those functions:
	 * http://codex.wordpress.org/Function_Reference/wp_deregister_script
	 * http://codex.wordpress.org/Function_Reference/wp_deregister_style
	 **/

	// Styles
	wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', '1.0.0');
	wp_enqueue_style('buddyboss-child-map-css', get_stylesheet_directory_uri() . '/assets/css/map.css', '', '1.0.0');

	// Javascript
	wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', '', '1.0.0');
}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);

/****************************** CUSTOM ENQUEUES ******************************/

function mpp_custom_google_map_scripts()
{
	wp_enqueue_style('custom-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');
	wp_enqueue_script('bbd-custom-google', get_stylesheet_directory_uri() . '/assets/js/custom-google.js', array('directorist-google-map'), '1.0.0', true);
	wp_localize_script('bbd-custom-google', 'directorist_options', bbd_get_option_data());
}
add_action('wp_enqueue_scripts', 'mpp_custom_google_map_scripts');

/******************************** INCLUDE FILES *******************************/

require_once(get_stylesheet_directory() . '/includes/buddyboss/class-group.php');
require_once(get_stylesheet_directory() . '/includes/buddyboss/class-affiliatewp.php');

/****************************** CUSTOM FUNCTIONS ******************************/

require_once(get_stylesheet_directory() . '/includes/custom-functions.php');
