<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if (!defined('ABSPATH')) exit;

$loop_fields = $listings->loop['list_fields']['template_data']['list_view_with_thumbnail'];

$claimed = false;
$claimed_by_admin = get_post_meta(get_the_ID(), '_claimed_by_admin', true);
$claim_fee = get_post_meta(get_the_ID(), '_claim_fee', true);
if ($claimed_by_admin || ('claim_approved' === $claim_fee)) $claimed = true;
$directory_type = get_post_meta(get_the_ID(), '_directory_type', true);
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
				<ul><?php $listings->render_loop_fields($loop_fields['body']['bottom'], '<li>', '</li>'); ?></ul>
			</div>
			<div class="directorist-listing-single__info--excerpt"><?php $listings->render_loop_fields($loop_fields['body']['excerpt']); ?></div>
			<?php if (!$claimed && $directory_type != 1418) echo '<div><span class="claim-this-listing">Claim this listing</span></div>'; ?>
			<div class="directorist-listing-single__info--right"><?php $listings->render_loop_fields($loop_fields['body']['right']); ?></div>
		</div>

		<div class="directorist-listing-single__meta">
			<div class="directorist-listing-single__meta--left"><?php $listings->render_loop_fields($loop_fields['footer']['left']); ?></div>
			<div class="directorist-listing-single__meta--right"><?php $listings->render_loop_fields($loop_fields['footer']['right']); ?></div>
		</div>

	</div>

</div>