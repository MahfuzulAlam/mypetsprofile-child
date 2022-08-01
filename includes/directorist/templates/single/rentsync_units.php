<?php

/**
 * RENTSYNC UNITS
 */

if (!defined('ABSPATH')) exit;
//e_var_dump($data);
if (count($data) < 1) return;
?>

<div class="mpp-avaialble-units">
    <?php foreach ($data as $unit) : ?>
        <?php
        $price = get_post_meta($unit->ID, '_price', true);
        $bathrooms = get_post_meta($unit->ID, '_bathrooms', true);
        $bedrooms = get_post_meta($unit->ID, '_bedrooms', true);
        $size = get_post_meta($unit->ID, '_unit_size', true);
        $title = get_post_meta($unit->ID, '_unit_title', true);
        $available = get_post_meta($unit->ID, '_available', true);
        $availability_date = get_post_meta($unit->ID, '_availability_date', true);
        $floor_plan = get_post_meta($unit->ID, '_floor_plan', true);
        $virtual_tour = get_post_meta($unit->ID, '_virtual_tour', true);
        ?>
        <div class="mpp-unit">
            <div class="mpp-unit-info">
                <div class="mpp-unit-name"><?php echo $unit->post_title; ?></div>
                <div class="mpp-unit-price">$<?php echo $price; ?></div>
                <div class="mpp-unit-title"><?php echo $title; ?></div>
                <div class="mpp-unit-features">
                    <?php echo $bedrooms; ?> Bedrooms,
                    <?php echo $bathrooms; ?> 1 Bathrooms,
                    <?php echo $size; ?> sq ft
                </div>
                <div class="mpp-unit-available">
                    <?php
                    if ($available == 'yes') {
                        echo 'Available Now';
                    }
                    ?>
                </div>
                <div class="mpp-unit-available-date">
                    <?php
                    if ($availability_date) {
                        echo $availability_date;
                    }
                    ?>
                </div>
            </div>
            <div class="mpp-unit-map">
                <?php if (!empty($floor_plan)) : ?>
                    <img src="<?php echo $floor_plan ?>" alt="<?php echo $unit->post_title; ?>" />
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>