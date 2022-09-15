<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;

$selection_type = isset($args['type']) && !empty($args['type']) ? $args['type'] : 'pooprints';
$selection_type_link = '';

if ($selection_type == 'pet-profile-community') {
    $selection_type_link = home_url('/pet-profile-registration/');
}

$pets_community = array('pooprints', 'pet-profile-community');

$query_args = array(
    'post_type' => ATBDP_POST_TYPE,
    'posts_per_page' => -1,
    'post_status' => 'publish',
);

if (in_array($selection_type, $pets_community)) {
    $query_args['tax_query'] = array(
        'relation' => 'AND',
        array(
            'taxonomy' => ATBDP_DIRECTORY_TYPE,
            'field' => 'slug',
            'terms' => 'pets-community'
        ),
    );
}

if ($selection_type == 'pooprints') {
    $query_args['meta_query'] = array(
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
    );
}

$listings = new WP_Query($query_args);

?>

<div class="mpp-custom-registration-form-holder">
    <?php if ($listings->have_posts()) : ?>

        <div class="pooprint_select_listing_wrapper">
            <select id="pooprint_select_listing" class="pooprint_select_listing">

                <option value="0">Select Building</option>

                <!-- the loop -->
                <?php while ($listings->have_posts()) : $listings->the_post(); ?>
                    <?php $access_type = get_post_meta(get_the_ID(), '_access_type', true) ? get_post_meta(get_the_ID(), '_access_type', true) : 'free'; ?>
                    <option value="<?php echo get_the_ID(); ?>" data-pooprint-link="<?php echo get_post_meta(get_the_ID(), '_pooprints_link', true) ?>" data-access-type="<?php echo $access_type; ?>">
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
    <input type="hidden" id="pooprint_page_link" class="pooprint_page_link" value="<?php echo home_url('/pooprints-registration/'); ?>" />
    <input type="hidden" id="pooprint_form_link" class="pooprint_form_link" value="" />
    <input type="hidden" id="selection_type" class="selection_type" value="<?php echo $selection_type; ?>" />
    <input type="hidden" id="selection_type_link" class="selection_type_link" value="<?php echo $selection_type_link; ?>" />
    <input type="hidden" class="mpp_product_url" value="<?php echo get_the_permalink(28824); ?>" /> <!-- 28824/6300 -->
    <a href="#" class="button pooprint_select_listing_button" id="pooprint_select_listing_button">Register with PooPrints</a>
    <p id="pooprint_select_listing_msg" class="pooprint_select_listing_msg"></p>
</div>