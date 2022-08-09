<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.0
 */

use \Directorist\Helper;

if (!defined('ABSPATH')) exit;
?>

<div class="directorist-search-contents" data-atts='<?php echo esc_attr($searchform->get_atts_data()); ?>' style="<?php echo $searchform->background_img_style(); ?>">

    <div class="<?php Helper::directorist_container_fluid(); ?>">

        <?php do_action('directorist_search_listing_before_title'); ?>

        <?php if ($searchform->show_title_subtitle && ($searchform->search_bar_title || $searchform->search_bar_sub_title)) : ?>

            <div class="directorist-search-top">

                <?php if ($searchform->search_bar_title) : ?>
                    <h2 class="directorist-search-top__title"><?php echo esc_html($searchform->search_bar_title); ?></h2>
                <?php endif; ?>

                <?php if ($searchform->search_bar_sub_title) : ?>
                    <p class="directorist-search-top__subtitle"><?php echo esc_html($searchform->search_bar_sub_title); ?></p>
                <?php endif; ?>

            </div>

        <?php endif; ?>

        <form action="<?php echo esc_url(ATBDP_Permalink::get_search_result_page_link()); ?>" class="directorist-search-form" data-atts="<?php echo esc_attr($searchform->get_atts_data()); ?>">

            <div class="directorist-search-form-wrap <?php echo esc_attr($searchform->border_class()); ?>">

                <?php $searchform->directory_type_nav_template(); ?>

                <input type="hidden" name="directory_type" class="listing_type" value="<?php echo esc_attr($searchform->listing_type_slug()); ?>">

                <div class="directorist-search-form-box-wrap">

                    <?php Helper::get_template('search-form/form-box', ['searchform' =>  $searchform]); ?>

                </div>

            </div>

        </form>

        <?php do_action('directorist_search_listing_after_search_bar'); ?>

        <?php

        $top_categories = [];

        $args = array(
            'type'          => ATBDP_POST_TYPE,
            'parent'        => 0,
            'orderby'       => 'count',
            'order'         => 'desc',
            'hide_empty'    => 1,
            'number'        => 5,
            'taxonomy'      => ATBDP_CATEGORY,
            'no_found_rows' => true,
        );

        $cats = get_categories($args);

        foreach ($cats as $cat) {
            $directory_type      = get_term_meta($cat->term_id, '_directory_type', true);
            $directory_type      = !empty($directory_type) ? $directory_type : array();
            $listing_type_id     = $searchform->listing_type;

            if (in_array($listing_type_id, $directory_type)) {
                $top_categories[] = $cat;
            }
        }

        if (!empty($top_categories)) {
            $title = get_directorist_option('popular_cat_title', __('Browse by popular categories', 'directorist'));
            $args = array(
                'searchform'      => $searchform,
                'top_categories'  => $top_categories,
                'title'           => $title,
            );
            Helper::get_template('search-form/top-cats', $args);
        }
        ?>

    </div>

</div>