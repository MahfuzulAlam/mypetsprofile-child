<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;

$listings = new WP_Query(
    array(
        'post_type' => ATBDP_POST_TYPE,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_pooprints_link',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_pooprints_link',
                'value' => '',
                'compare' => '!='
            )
        ),
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => ATBDP_DIRECTORY_TYPE,
                'field' => 'slug',
                'terms' => 'pets-community'
            ),
        )
    )
);

?>

<?php if ($listings->have_posts()) : ?>

    <div class="pooprint_select_listing_wrapper">
        <select id="pooprint_select_listing">

            <option value="0">Select Building</option>

            <!-- the loop -->
            <?php while ($listings->have_posts()) : $listings->the_post(); ?>
                <option value="<?php echo get_the_ID(); ?>" data-pooprint-link="<?php echo get_post_meta(get_the_ID(), '_pooprints_link', true) ?>">
                    <?php echo get_the_title(); ?>
                </option>
            <?php endwhile; ?>
            <!-- end of the loop -->

            <?php wp_reset_postdata(); ?>
        </select>
    </div>

<?php else : ?>
    <p><?php _e('Sorry, no building found!'); ?></p>
<?php endif; ?>
<input type="hidden" id="pooprint_page_link" value="<?php echo home_url('/pooprints-registration/'); ?>" />
<input type="hidden" id="pooprint_form_link" value="" />
<a href="#" class="button" id="pooprint_dna_property_button">Register with PooPrints</a>
<p id="pooprint_select_listing_msg"></p>