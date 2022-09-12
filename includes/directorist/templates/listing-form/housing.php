<?php

/**
 * @author  wpWax
 * @since   1.10
 * @version 1.10
 */

$placeholder = !empty($data['placeholder']) ? $data['placeholder'] : '';

$value = isset($data['value']) && !empty($data['value']) ? $data['value'] : '';
$listing_title = !empty($value) ? get_the_title($value) : '';

?>

<div class="directorist-form-group directorist-form-categories-field">

    <?php \Directorist\Directorist_Listing_Form::instance()->field_label_template($data); ?>

    <select name="<?php echo $data['field_key']; ?>" id="<?php echo $data['type']; ?>" class="directorist-form-element" data-placeholder="<?php echo esc_attr($placeholder); ?>" <?php \Directorist\Directorist_Listing_Form::instance()->required($data); ?>>

        <?php
        if (!empty($value) && !empty($listing_title))
            echo '<option value="' . $value . '" selected>' . esc_attr($listing_title) . '</option>';
        ?>

    </select>

    <?php \Directorist\Directorist_Listing_Form::instance()->field_description_template($data); ?>

</div>