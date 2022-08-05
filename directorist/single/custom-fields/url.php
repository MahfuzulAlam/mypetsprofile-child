<?php

/**
 * @author  wpWax
 * @since   6.7
 * @version 7.0.5.2
 */

if (!defined('ABSPATH')) exit;
?>

<div class="directorist-single-info directorist-single-info-url directorist-single-info-<?php echo $data['field_key']; ?>">

    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><?php directorist_icon($icon); ?></span>
        <span class="directorist-single-info__label--text"><?php echo esc_html($data['label']); ?></span>
    </div>
    <?php if ($data['field_key'] == 'floor_plan') : ?>
        <div class="directorist-single-info__value">
            <img src="<?php echo esc_html($value); ?>" alt="<?php echo get_the_title(); ?>" style="width:100%" />
        </div>
    <?php else : ?>
        <div class="directorist-single-info__value"><a target="_blank" href="<?php echo esc_url($value); ?>" <?php echo !empty($data['use_nofollow']) ? 'rel="nofollow"' : ''; ?>><?php echo esc_html($value); ?></a></div>
    <?php endif; ?>
</div>