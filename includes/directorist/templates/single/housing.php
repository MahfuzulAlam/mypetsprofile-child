<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

$value = isset($data['value']) && !empty($data['value']) ? $data['value'] : '';

if (empty($value)) return;

$listing_title = get_the_title($value);
$listing_url = get_the_permalink($value);

if (!$listing_title || empty($listing_title)) return;

?>

<div class="directorist-single-info directorist-single-info-checkbox <?php echo $data['form_data']['class']; ?>">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon($data['icon']); ?></span>
        <span class="directorist-single-info__label--text"><?php echo esc_html($data['form_data']['label']); ?></span>
    </div>

    <div class="directorist-single-info__value">
        <a href="<?php echo $listing_url; ?>"><?php echo $listing_title; ?></a>
    </div>

</div>