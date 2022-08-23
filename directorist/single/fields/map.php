<?php

/**
 * @author  wpWax
 * @since   6.7
 * @version 7.3.0
 */

if (!defined('ABSPATH')) exit;


$cat_image_url = '';
$categories = get_the_terms(get_the_ID(), ATBDP_CATEGORY);
if ($categories && count($categories) > 0) {
    $category = $categories[0];
    $cat_image = get_term_meta($category->term_id, 'image', true);
    if ($cat_image) $cat_image_url = wp_get_attachment_image_url($cat_image);
}


?>

<div class="directorist-single-map" data-iconimage="<?php echo $cat_image_url; ?>" data-map="<?php echo esc_attr($listing->map_data()); ?>"></div>