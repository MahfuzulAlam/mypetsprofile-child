<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;

$animals = $args['animals'];

?>

<div class="mpp-animals">
    <?php if ($animals->have_posts()) : ?>

        <!-- pagination here -->

        <!-- the loop -->
        <?php while ($animals->have_posts()) : $animals->the_post(); ?>
            <?php
            $animal_id = get_the_ID();
            $image_url = get_the_post_thumbnail_url();
            $metas = get_post_meta($animal_id);
            $animal_meta = array();
            if (isset($metas['animal_age_group']) && !empty($metas['animal_age_group'][0])) $animal_meta[] = $metas['animal_age_group'][0];
            if (isset($metas['animal_main_breed']) && !empty($metas['animal_main_breed'][0])) $animal_meta[] = $metas['animal_main_breed'][0];
            $bb_group = get_post_meta($animal_id, 'bb_group', true);
            $bb_group_url = $bb_group && !empty($bb_group) ? bp_get_group_permalink(groups_get_group($bb_group)) : '';
            //bp_is_group() ? bp_get_group_permalink(groups_get_current_group())
            ?>
            <div class="animal-holder animal_holder_<?php echo $animal_id; ?>" data-metas='<?php echo json_encode($metas); ?>' data-title="<?php echo get_the_title(); ?>" data-img="<?php echo $image_url; ?>" data-admin="<?php echo bp_is_group() ? mpp_is_group_admin() : 'user'; ?>" data-group_url="<?php echo $bb_group_url; ?>" data-animal="<?php echo $animal_id; ?>">
                <div class="animal-image" style="background-image:url(<?php echo $image_url; ?>)"></div>
                <div class="animal-info">
                    <div class="animal-name"><?php echo get_the_title(); ?></div>
                    <div class="animal-meta">
                        <?php echo count($animal_meta) > 0 ? implode(" • ", $animal_meta) : ''; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        <!-- end of the loop -->

        <!-- pagination here -->

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <p><?php _e('Sorry, no animal found.'); ?></p>
    <?php endif; ?>
</div>