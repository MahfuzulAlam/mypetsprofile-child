<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.5.2
 */

if (!defined('ABSPATH')) exit;

//e_var_dump($data);

$value = $listing->get_custom_field_value('checkbox', $data);
$class = isset($data['form_data']['class']) && !empty($data['form_data']['class']) ? $data['form_data']['class'] : '';
$facilities_value = $facility_options = array();

if ($class == 'facilities') {
    $facilities_value = isset($data['value']) && count($data['value']) > 0 ? array_unique($data['value']) : array();
    $facility_options = mpp_facility_option_list($data['options']);
}

?>

<div class="directorist-single-info directorist-single-info-checkbox directorist-checkbox-<?php echo $class; ?>">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon($icon); ?></span>
        <span class="directorist-single-info__label--text"><?php echo esc_html($data['label']); ?></span>
    </div>

    <?php if ($class == 'facilities') : ?>
        <div class="directorist-single-info__value">
            <div class="directorist-facilities-list">
                <?php foreach ($facilities_value as $facility_value) : ?>
                    <div class="directorist-facility-single">
                        <span class="label-icon"><i class="las la-check"></i></span>
                        <span class="label-value"><?php echo $facility_options[$facility_value]['label']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="directorist-single-info__value"><?php echo esc_html($value); ?></div>
    <?php endif; ?>

</div>