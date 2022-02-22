<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

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
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css', '', '1.0.0' );
   wp_enqueue_style( 'buddyboss-child-map-css', get_stylesheet_directory_uri().'/assets/css/map.css', '', '1.0.0' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js', '', '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );

/******************************** INCLUDE FILES *******************************/

require_once(get_stylesheet_directory() . '/includes/buddyboss/class-group.php');


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

add_filter( 'woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart' );
 
function bbloomer_redirect_checkout_add_cart() {
   return wc_get_checkout_url();
}

// Remove "Returning customer? Click here to login" From Checkout Page
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

// Remove "category? From product Page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_filter( 'bp_core_signup_send_activation_key', 'ps_disable_activation_email' );
function ps_disable_activation_email() {
	return false;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Custom Code by Alam - Starts Here
function custom_google_map_scripts()
{
	wp_enqueue_style('custom-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');
	wp_enqueue_script('bbd-custom-google', get_stylesheet_directory_uri() . '/assets/js/custom-google.js', array('directorist-google-map'), '1.0.0', true);
	wp_localize_script('bbd-custom-google', 'directorist_options', bbd_get_option_data());
}
add_action('wp_enqueue_scripts', 'custom_google_map_scripts');

add_action('wp_footer', function () {
?>
	<script type="text/javascript">
		jQuery(window).load(function($) {
			//$('.atbdp-body').removeClass('atbdp-map').addClass('atbdp-map-updated');
			/*
			setTimeout(function(){
			    console.log('Hello');
			    jQuery('.directorist-range-slider-wrap .atbd-active1').css({left: '6.41273px'});
			    jQuery('.directorist-range-slider-wrap .atbd-child').css({width: '6.41273px'});
			    jQuery('.directorist-range-slider-wrap .atbd-current-value').html('<span>50</span>');
			}, 2000);
			*/
		});
	</script>
<?php
}, 100);


function mypetsprofile_loop_get_the_thumbnail($class = '')
{
	$default_image_src = "https://communityportal.mypetsprofile.com/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

	$id = get_the_ID();
	$image_quality     = get_directorist_option('preview_image_quality', 'large');
	$listing_prv_img   = get_post_meta($id, '_listing_prv_img', true);
	$listing_img       = get_post_meta($id, '_listing_img', true);

	if (is_array($listing_img) && !empty($listing_img)) {
		$thumbnail_img = atbdp_get_image_source($listing_img[0], $image_quality);
		$thumbnail_id = $listing_img[0];
	}

	if (!empty($listing_prv_img)) {
		$thumbnail_img = atbdp_get_image_source($listing_prv_img, $image_quality);
		$thumbnail_id = $listing_prv_img;
	}

	if (!empty($img_src)) {
		$thumbnail_img = $img_src;
		$thumbnail_id = 0;
	}

	if (empty($thumbnail_img)) {

		$thumbnail_img = $default_image_src;
		$thumbnail_id = 0;

		// GET CATEGORY IMAGE
		
		$category_list = get_the_terms($id, ATBDP_CATEGORY);
		if ($category_list && !is_wp_error($category_list) && count($category_list) > 0) {
			$category_img = get_term_meta($category_list[0]->term_id, 'image', true);
			if ($category_img) {
				$cat_img_url = wp_get_attachment_image_url($category_img);
				if ($cat_img_url) {
					$thumbnail_img = $cat_img_url;
					$thumbnail_id = $category_img;
					$class .= ' image-from-category';
				}
			}
		}
		
	}

	$image_src    = $thumbnail_img;
	$image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
	$image_alt = (!empty($image_alt)) ? esc_attr($image_alt) : esc_html(get_the_title($thumbnail_id));
	$image_alt = (!empty($image_alt)) ? $image_alt : esc_html(get_the_title());

	return "<img src='$image_src' alt='$image_alt' class='$class' />";
}

// Profile Image

function mypetsprofile_listing_get_the_thumbnail($class = '')
{
	$default_image_src = "https://communityportal.mypetsprofile.com/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

	$id = get_the_ID();
	$image_quality     = get_directorist_option('preview_image_quality', 'large');
	$listing_prv_img   = get_post_meta($id, '_listing_prv_img', true);
	$listing_img       = get_post_meta($id, '_listing_img', true);

	if (is_array($listing_img) && !empty($listing_img)) {
		$thumbnail_img = atbdp_get_image_source($listing_img[0], $image_quality);
		$thumbnail_id = $listing_img[0];
	}

	if (!empty($listing_prv_img)) {
		$thumbnail_img = atbdp_get_image_source($listing_prv_img, $image_quality);
		$thumbnail_id = $listing_prv_img;
	}

	if (!empty($img_src)) {
		$thumbnail_img = $img_src;
		$thumbnail_id = 0;
	}

	if (empty($thumbnail_img)) {

		$thumbnail_img = $default_image_src;
		$thumbnail_id = 0;

		// GET CATEGORY IMAGE
		
		$category_list = get_the_terms($id, ATBDP_CATEGORY);
		if ($category_list && !is_wp_error($category_list) && count($category_list) > 0) {
			$category_img = get_term_meta($category_list[0]->term_id, 'image', true);
			if ($category_img) {
				$cat_img_url = wp_get_attachment_image_url($category_img);
				if ($cat_img_url) {
					$thumbnail_img = $cat_img_url;
					$thumbnail_id = $category_img;
					$class .= ' image-from-category';
				}
			}
		}
		
	}

	$image_src    = $thumbnail_img;
	$image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
	$image_alt = (!empty($image_alt)) ? esc_attr($image_alt) : esc_html(get_the_title($thumbnail_id));
	$image_alt = (!empty($image_alt)) ? $image_alt : esc_html(get_the_title());

	return array( 'image_src' => $image_src, 'image_alt' => $image_alt, 'image_class' => $class );
}

add_action('admin_head', function(){
?>
    <style>
        /* ADMIN */
        .atbdp-importer-mapping-table-name code
        {
        	line-break: anywhere;
        }
    </style>
<?php
});

// Add custom fields in Import

add_action('init', function(){
    $tools = ATBDP()->tools;
    $tools->importable_fields['directorist_listing_rating'] = "Average Rating";
    $tools->importable_fields['social_facebook'] = "Facebook Url";
});

add_shortcode('show-bb-group', function () {
    $group_avatar = bp_is_active( 'groups' ) ? bp_get_group_avatar_url( groups_get_group( 57 ) ) : '';
    e_var_dump( $group_avatar );
    //e_var_dump( get_post_meta( 14 ) );
    return '--';
});

function bbd_inspect_scripts()
{
	global $wp_scripts;
	wp_dequeue_script('directorist-global-script');
}
//add_action('wp_print_scripts', 'bbd_inspect_scripts');

function bbd_get_option_data()
{
	$options = [];
	$options['script_debugging'] = get_directorist_option('script_debugging', DIRECTORIST_LOAD_MIN_FILES, true);
	return $options;
}
// Custom Code by Alam - Ends Here
