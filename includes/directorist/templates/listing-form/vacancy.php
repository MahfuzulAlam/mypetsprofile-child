<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

$old_data = !isset($data['value']) || !empty($data['value']) ? $data['value']  : '';

$data['value'] = !isset($data['value']) || !empty($data['value']) ? json_decode($data['value'])  : array();

function check_vacancy_value($option, $saved_data)
{
    foreach ($saved_data as $data_key => $data_value) {
        if ($data_key == $option) return true;
    }
    return false;
}

function check_vacancy_number($option, $saved_data)
{
    foreach ($saved_data as $data_key => $data_value) {
        if ($data_key == $option) return $data_value;
    }
    return 1;
}
?>

<div class="directorist-form-group directorist-custom-field-checkbox <?php echo esc_attr($data['class']) ?>">

    <?php \Directorist\Directorist_Listing_Form::instance()->field_label_template($data); ?>
    <div class="directorist-vacancy-oprions">
        <?php if (!empty($data['options'])) : ?>

            <?php foreach ($data['options'] as $option) : ?>

                <?php $option_class = isset($option['option_class']) && !empty($option['option_class']) ? $option['option_class'] : $option['option_value']; ?>

                <?php $uniqid = $option['option_value'] . '-' . wp_rand();  ?>

                <?php $checked = check_vacancy_value($option['option_value'], $data['value']); ?>

                <div class="directorist-checkbox directorist-mb-10 vacancy-input-wrapper <?php echo trim($option_class); ?>">
                    <div>
                        <input type="checkbox" id="<?php echo esc_attr($uniqid); ?>" name="vacancy[]" value="<?php echo esc_attr($option['option_value']); ?>" <?php echo $checked ? 'checked="checked"' : ''; ?>>
                        <label for="<?php echo esc_attr($uniqid); ?>" class="directorist-checkbox__label"><?php echo esc_html($option['option_label']); ?></label>
                    </div>
                    <?php if ($option['option_value'] != 'none') : ?>
                        <div class="vacancy-number-wrapper" style="<?php echo $checked ? '' : 'display:none'; ?>">
                            <input type="number" value="<?php echo check_vacancy_number($option['option_value'], $data['value']) ?>" min="1" max="1000" />
                        </div>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>

            <a href="#" class="directorist-custom-field-btn-more"><?php esc_html_e('See More', 'directorist'); ?></a>

        <?php endif; ?>
        <input type="hidden" name="<?php echo esc_attr($data['field_key']); ?>" value='<?php echo $old_data; ?>' />
    </div>
    <?php \Directorist\Directorist_Listing_Form::instance()->field_description_template($data); ?>

</div>