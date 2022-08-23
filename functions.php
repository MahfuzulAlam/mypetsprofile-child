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
 * DECLARE GLOBAL VARIABLES
 */

// Global Fixed Valiable - Constants
if (!defined('MPP_SITE_URL')) {
	define('MPP_SITE_URL', get_site_url());
}

if (!defined('MPP_VERSION')) {
	define('MPP_VERSION', '1.1.17');
}

if (!defined('MPP_MAP_VERSION')) {
	define('MPP_MAP_VERSION', '1.0.2');
}

if (!defined('MPP_ADMIN_VERSION')) {
	define('MPP_ADMIN_VERSION', '1.0.4');
}

if (!defined('MPP_CHILD_FILE_DIR')) {
	define('MPP_CHILD_FILE_DIR', get_stylesheet_directory() . '/assets/file');
}

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
	wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', MPP_VERSION);
	wp_enqueue_style('buddyboss-child-map-css', get_stylesheet_directory_uri() . '/assets/css/map.css', '', MPP_MAP_VERSION);

	// Javascript
	if ((function_exists('bp_is_user') && bp_is_user()) ||
		(function_exists('bp_is_group') && bp_is_group())
	) {
		wp_enqueue_script('pdf-lib', get_stylesheet_directory_uri() . '/assets/js/pdf-lib.js');
		wp_enqueue_script('download', get_stylesheet_directory_uri() . '/assets/js/download.js');
		wp_enqueue_script('pdf-gen', get_stylesheet_directory_uri() . '/assets/js/pdf-gen.js', array('jquery', 'pdf-lib', 'download'), '1.0.0');
	}
	wp_enqueue_script('input-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js', array('jquery'));
	wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), MPP_VERSION);
	wp_localize_script('buddyboss-child-js', 'mppChild', array('ajaxurl' => admin_url('admin-ajax.php'), 'chatInterval' => 20000));
}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);

/********************* THEME SUPPORTS ******************************/
add_theme_support('post-thumbnails');
add_image_size('bb-app-group-avatar', 150, 150, true);

/****************************** CUSTOM ENQUEUES ******************************/

function mpp_custom_google_map_scripts()
{
	//wp_enqueue_style('custom-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');
	wp_enqueue_script('bbd-custom-google', get_stylesheet_directory_uri() . '/assets/js/custom-google.js', array('jQuery'), MPP_MAP_VERSION, true);
	wp_localize_script('bbd-custom-google', 'directorist_options', bbd_get_option_data());
}
add_action('wp_enqueue_scripts', 'mpp_custom_google_map_scripts', 0);

/**************************** CUSTOM ADMIN ENQUEUES ****************************/

function mpp_custom_admin_enqueue_scripts()
{
	wp_enqueue_style('mpp-admin-css', get_stylesheet_directory_uri() . '/assets/admin/css/custom.css', '', MPP_ADMIN_VERSION);
	wp_enqueue_script('mpp-admin-js', get_stylesheet_directory_uri() . '/assets/admin/js/custom.js', array('jquery'), MPP_ADMIN_VERSION);
	wp_localize_script('mpp-admin-js', 'mppChild', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'mpp_custom_admin_enqueue_scripts');

if (directorist_is_plugin_active('directorist/directorist-base.php')) :

	/******************************** INCLUDE FILES *******************************/

	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-group.php');

	/**
	 * Load Custom Automated Push Notification
	 */
	function mpp_bbapp_custom_work_init()
	{
		if (class_exists('bbapp')) {
			require_once(get_stylesheet_directory() . '/includes/buddyboss/class-push-notification.php');
			BuddyBossApp\Custom\PetAlertNotification::instance();
		}
	}
	//add_action('plugins_loaded', 'mpp_bbapp_custom_work_init');

	/****************************** CUSTOM FUNCTIONS ******************************/

	// CUSTOMS
	require_once(get_stylesheet_directory() . '/includes/custom-functions.php');
	require_once(get_stylesheet_directory() . '/includes/custom-hooks.php');
	require_once(get_stylesheet_directory() . '/includes/custom-shortcodes.php');

	// BUDDYBOSS
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-adoption.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-qrcode.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-petalert.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-coauthors.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-profile-forms.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-database.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-messenger.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-referral-notification.php');
	require_once(get_stylesheet_directory() . '/includes/buddyboss/class-referral-email.php');

	// DIRECTORIST
	require_once(get_stylesheet_directory() . '/includes/directorist/class-fields.php');
	require_once(get_stylesheet_directory() . '/includes/directorist/class-import.php');

	// RENTSYNC
	require_once(get_stylesheet_directory() . '/includes/directorist/class-rentsync.php');

endif;
