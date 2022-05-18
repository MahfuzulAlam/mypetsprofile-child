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
            <?php $image_url = get_the_post_thumbnail_url(); ?>
            <?php
            $metas = get_post_meta(get_the_ID());
            $animal_meta = array();
            if (isset($metas['animal_age_group']) && !empty($metas['animal_age_group'][0])) $animal_meta[] = $metas['animal_age_group'][0];
            if (isset($metas['animal_main_breed']) && !empty($metas['animal_main_breed'][0])) $animal_meta[] = $metas['animal_main_breed'][0];
            ?>
            <div class="animal-holder" data-metas='<?php echo json_encode($metas); ?>' data-title="<?php echo get_the_title(); ?>" data-img="<?php echo $image_url; ?>">
                <div class="animal-image" style="background-image:url(<?php echo $image_url; ?>)">
                </div>
                <div class="animal-info">
                    <div class="animal-name"><?php the_title(); ?></div>
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