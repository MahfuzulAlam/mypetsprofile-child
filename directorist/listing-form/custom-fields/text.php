<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if (!defined('ABSPATH')) exit;
?>

<div class="directorist-form-group directorist-custom-field-text">

    <?php $listing_form->field_label_template($data); ?>
    <?php if (isset($data['field_key']) && strpos($data['field_key'], 'phone') !== false) : ?>
        <input type="tel" name="<?php echo esc_attr($data['field_key']); ?>" id="<?php echo esc_attr($data['field_key']); ?>" class="directorist-form-element" value="<?php echo esc_attr($data['value']); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php $listing_form->required($data); ?> pattern="[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}">
    <?php elseif (isset($data['field_key']) && strpos($data['field_key'], 'email') !== false) : ?>
        <input type="email" name="<?php echo esc_attr($data['field_key']); ?>" id="<?php echo esc_attr($data['field_key']); ?>" class="directorist-form-element" value="<?php echo esc_attr($data['value']); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php $listing_form->required($data); ?>>
    <?php else : ?>
        <input type="text" name="<?php echo esc_attr($data['field_key']); ?>" id="<?php echo esc_attr($data['field_key']); ?>" class="directorist-form-element" value="<?php echo esc_attr($data['value']); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php $listing_form->required($data); ?>>
    <?php endif; ?>
    <?php $listing_form->field_description_template($data); ?>

</div>