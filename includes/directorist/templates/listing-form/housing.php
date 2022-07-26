<?php

/**
 * @author  wpWax
 * @since   1.10
 * @version 1.10
 */

$placeholder = !empty($data['placeholder']) ? $data['placeholder'] : '';
e_var_dump($data);
?>

<div class="directorist-form-group directorist-form-categories-field">

    <?php \Directorist\Directorist_Listing_Form::instance()->field_label_template($data); ?>

    <select name="<?php echo $data['field-key']; ?>" id="<?php echo $data['field-type']; ?>" class="directorist-form-element" data-placeholder="<?php echo esc_attr($placeholder); ?>" <?php \Directorist\Directorist_Listing_Form::instance()->required($data); ?>>

        <?php
        echo '<option value="">' . esc_attr($placeholder) . '</option>';
        ?>

    </select>

    <?php \Directorist\Directorist_Listing_Form::instance()->field_description_template($data); ?>

</div>