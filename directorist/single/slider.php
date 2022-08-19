<?php

/**
 * @author  wpWax
 * @since   6.7
 * @version 6.7
 */

if (!defined('ABSPATH')) exit;

if (!$has_slider) {
	return;
}

$default_image = $data['images'];
$data['images'] = [];
$mpp_photos = get_post_meta(get_the_ID(), '_mpp_photos', true);

// Apartment Photos

if ($mpp_photos && !empty($mpp_photos)) :

	$new_photos = array();

	foreach ($mpp_photos as $key => $mpp_photo) {
		$new_photos[$key]['alt'] = $mpp_photo->id;
		$new_photos[$key]['src'] = $mpp_photo->url; // thumbnailUrl
	}

	if ($new_photos && count($new_photos) > 0) $data['images'] = $new_photos;

endif;

// UNIT IMAGE

$directory_type = get_the_terms(get_the_ID(), ATBDP_DIRECTORY_TYPE);
if ($directory_type && $directory_type[0]->slug == 'units') {
	$floor_plan = get_post_meta(get_the_ID(), '_floor_plan', true);
	if ($floor_plan) {
		$data['images'][0]['alt'] = get_the_title();
		$data['images'][0]['src'] = $floor_plan;
	} else {
		$building_id = get_post_meta(get_the_ID(), '_mpp-housing', true);
		if ($building_id) {
			$building_image_id = get_post_meta($building_id, '_listing_prv_img', true);
			if ($building_image_id) {
				$building_image_url = wp_get_attachment_image_url($building_image_id, 'full');
				$data['images'][0]['alt'] = get_the_title();
				$data['images'][0]['src'] = $building_image_url;
			}
		}
	}
}

// UNIT IMAGE

// CATEGORY IMAGE

if (empty($data['images']) || count($data['images']) < 1) :
	$category_list = get_the_terms(get_the_ID(), ATBDP_CATEGORY);
	if ($category_list && !is_wp_error($category_list) && count($category_list) > 0) {
		$category_img = get_term_meta($category_list[0]->term_id, 'image', true);
		if ($category_img) {
			$cat_img_url = wp_get_attachment_image_url($category_img);
			if ($cat_img_url) {
				$data['images'][0]['alt'] = get_the_title();
				$data['images'][0]['src'] = $cat_img_url;
			}
		}
	}
endif;

// CATEGORY IMAGE

// SET DEFAULT IMAGES
if (empty($data['images']) || count($data['images']) < 1) :
	$data['images'] =  $default_image;
endif;
// SET DEFAULT IMAGES

// IF NO IMAGE AVAILABLE


$img_size_class = ('contain' === $data['background-size']) ? '' : ' plasmaSlider__cover';
?>
<div id="directorist-single-listing-slider" class="plasmaSlider" data-width="<?php echo esc_attr($data['width']); ?>" data-height="<?php echo esc_attr($data['height']); ?>" data-rtl="<?php echo esc_attr($data['rtl']); ?>" data-show-thumbnails="<?php echo esc_attr($data['show-thumbnails']); ?>" data-background-size="<?php echo esc_attr($data['background-size']); ?>" data-blur-background="<?php echo esc_attr($data['blur-background']); ?>" data-background-color="<?php echo esc_attr($data['background-color']); ?>" data-thumbnail-background-color="<?php echo esc_attr($data['thumbnail-bg-color']); ?>">

	<div class="plasmaSliderTempImage" style="padding-top: <?php echo $data['padding-top'] . "%;" ?>">
		<?php

		$image = $data['images'][0];

		if (!empty($image)) :
			$img_src = $image['image_src'];
			$img_alt = $image['image_alt'];
			if ('contain' === $data['background-size'] && $data['blur-background']) {
				echo "<img class='plasmaSliderTempImgBlur' src='{$img_src}' alt='{$img_alt}'>";
			}

			echo "<img class='plasmaSliderTempImg {$img_size_class}' src='{$img_src}' alt='{$img_alt}'/>";
		endif; ?>
	</div>

	<div class="plasmaSliderImagess">
		<?php
		//if (count($data['images']) < 2) $data['images'] = array($image);
		if (!empty($data['images'])) :
			foreach ($data['images'] as $image) {
				$img_src = $image['src'];
				$img_alt = $image['alt'];
				echo "<span class='plasmaSliderImageItem' data-src='{$img_src}' data-alt='{$img_alt}'></span>" . "\n";
			}
		endif;

		?>
	</div>

</div>