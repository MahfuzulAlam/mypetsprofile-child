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
//e_var_dump($data['images']);

$mpp_photos = get_post_meta(get_the_ID(), '_mpp_photos', true);
//e_var_dump($mpp_photos);

if ($mpp_photos && !empty($mpp_photos)) :

	$new_photos = array();

	foreach ($mpp_photos as $key => $mpp_photo) {
		$new_photos[$key]['alt'] = $mpp_photo->id;
		$new_photos[$key]['src'] = $mpp_photo->thumbnailUrl; // url
	}

	if ($new_photos && count($new_photos) > 0) $data['images'] = $new_photos;

endif;

//e_var_dump($data['images']);

$img_size_class = ('contain' === $data['background-size']) ? '' : ' plasmaSlider__cover';
?>
<div id="directorist-single-listing-slider" class="plasmaSlider" data-width="<?php echo esc_attr($data['width']); ?>" data-height="<?php echo esc_attr($data['height']); ?>" data-rtl="<?php echo esc_attr($data['rtl']); ?>" data-show-thumbnails="<?php echo esc_attr($data['show-thumbnails']); ?>" data-background-size="<?php echo esc_attr($data['background-size']); ?>" data-blur-background="<?php echo esc_attr($data['blur-background']); ?>" data-background-color="<?php echo esc_attr($data['background-color']); ?>" data-thumbnail-background-color="<?php echo esc_attr($data['thumbnail-bg-color']); ?>">

	<div class="plasmaSliderTempImage" style="padding-top: <?php echo $data['padding-top'] . "%;" ?>">
		<?php

		$image = mypetsprofile_listing_get_the_thumbnail();

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