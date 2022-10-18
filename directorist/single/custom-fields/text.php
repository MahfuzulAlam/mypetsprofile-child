<?php

/**
 * @author  wpWax
 * @since   6.7
 * @version 7.0.5.2
 */

if (!defined('ABSPATH')) exit;
?>

<div class="directorist-single-info directorist-single-info-text">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon($icon); ?></span>
        <span class="directorist-single-info__label--text"><?php echo esc_html($data['label']); ?></span>
    </div>
    <?php if ($data['field_key'] == 'ci-phone') : ?>
        <div class="directorist-single-info__value">
            <a href="tel:<?php echo esc_html($value); ?>" target="_blank"><?php echo esc_html($value); ?></a>
        </div>
    <?php elseif ($data['field_key'] == 'ci-email') : ?>
        <div class="directorist-single-info__value">
            <a href="mailto:<?php echo esc_html($value); ?>" target="_blank"><?php echo esc_html($value); ?></a>
        </div>
    <?php else : ?>
        <div class="directorist-single-info__value"><?php echo esc_html($value); ?></div>
    <?php endif; ?>

</div>