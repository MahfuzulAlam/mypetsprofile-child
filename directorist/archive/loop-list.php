<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if (!defined('ABSPATH')) exit;

$loop_fields = $listings->loop['list_fields']['template_data']['list_view_with_thumbnail'];
$listing_id = get_the_ID();

$claimed = false;
$claimed_by_admin = get_post_meta($listing_id, '_claimed_by_admin', true);
$claim_fee = get_post_meta($listing_id, '_claim_fee', true);
if ($claimed_by_admin || ('claim_approved' === $claim_fee)) $claimed = true;
$directory_type = get_post_meta($listing_id, '_directory_type', true);
?>

<div class="directorist-listing-single directorist-listing-list directorist-listing-has-thumb <?php echo esc_attr($listings->loop_wrapper_class()); ?>">

	<figure class="directorist-listing-single__thumb">
		<?php $listings->loop_thumb_card_template(); ?>
		<div class="directorist-thumb-top-right"><?php $listings->render_loop_fields($loop_fields['thumbnail']['top_right']); ?></div>
	</figure>

	<div class="directorist-listing-single__content">

		<div class="directorist-listing-single__info">
			<div class="directorist-listing-single__info--top"><?php $listings->render_loop_fields($loop_fields['body']['top']); ?></div>
			<div class="directorist-listing-single__info--list">
				<div class="apartment-address custom-loop-field">
					<?php
					$address_line_1 = get_post_meta($listing_id, '_address_line_1', true);
					if ($address_line_1) echo $address_line_1;
					?>
				</div>
				<ul><?php $listings->render_loop_fields($loop_fields['body']['bottom'], '<li>', '</li>'); ?></ul>
				<div class="unit-apartment-info custom-loop-field">
					<?php
					//$unit_title = get_post_meta($listing_id, '_unit_title', true);
					//if ($unit_title) echo '<span>' . $unit_title . '</span><br>';
					$mpp_housing = get_post_meta($listing_id, '_mpp-housing', true);
					if ($mpp_housing && $mpp_housing != 999) {
						$apartment_title = get_the_title($mpp_housing);
						if ($apartment_title) echo '<span>' . $apartment_title . '</span><br>';
					}
					$source_company = get_post_meta($listing_id, '_source_company', true);
					if ($source_company) echo '<span>' . $source_company . '</span><br>';
					?>
				</div>
				<div class="unit-rooms custom-loop-field">
					<?php
					$bathrooms = get_post_meta($listing_id, '_bathrooms', true);
					$bedrooms = get_post_meta($listing_id, '_bedrooms', true);
					$unit_size = get_post_meta($listing_id, '_unit_size', true);
					if ($bathrooms) {
						echo $bathrooms;
						echo $bathrooms < 2 ? ' Bath ' : ' Baths ';
					}
					if ($bedrooms) {
						echo $bedrooms;
						echo $bedrooms < 2 ? ' Bed ' : ' Beds ';
					}
					if ($unit_size) echo $unit_size . ' Sq ft';
					?>
				</div>
				<div class="apartment-amenities custom-loop-field">
					<?php
					// $amenities = get_post_meta($listing_id, '_amenities', true);
					// if ($amenities && is_array($amenities)) {
					// 	$s_amenities = implode(', ', $amenities);
					// 	echo $s_amenities;
					// }
					?>
				</div>
			</div>
			<div class="directorist-listing-single__info--excerpt"><?php $listings->render_loop_fields($loop_fields['body']['excerpt']); ?></div>
			<?php //if (!$claimed && $directory_type != 1418) echo '<div><span class="claim-this-listing">Claim this listing</span></div>'; 
			?>
			<div class="directorist-listing-single__info--right"><?php $listings->render_loop_fields($loop_fields['body']['right']); ?></div>
		</div>

		<div class="directorist-listing-single__meta">
			<div class="directorist-listing-single__meta--left"><?php $listings->render_loop_fields($loop_fields['footer']['left']); ?></div>
			<div class="directorist-listing-single__meta--right"><?php $listings->render_loop_fields($loop_fields['footer']['right']); ?></div>
		</div>

	</div>

</div>