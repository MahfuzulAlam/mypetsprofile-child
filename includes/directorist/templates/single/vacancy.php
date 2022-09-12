<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

$values = isset($data['value']) && !empty($data['value']) ? json_decode($data['value']) : array();
if (empty($values)) return;
?>

<div class="directorist-single-info directorist-single-info-checkbox <?php echo $data['form_data']['class']; ?>">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon($data['icon']); ?></span>
        <span class="directorist-single-info__label--text"><?php echo esc_html($data['form_data']['label']); ?></span>
    </div>

    <div class="directorist-single-info__value">
        <div class="directorist-facilities-list">
            <?php foreach ($values as $key => $value) : ?>
                <div class="directorist-facility-single">
                    <span class="label-icon"><i class="las la-check"></i></span>
                    <span class="label-value"><?php echo mpp_get_vacancy_option_name($key, $data['options']) . ' (' . $value . ' units)'; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>