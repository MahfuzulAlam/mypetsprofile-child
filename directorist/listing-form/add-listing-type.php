<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

use \Directorist\Helper;

if (!defined('ABSPATH')) exit;

$active_plans = mpp_get_active_pricing_plans_from_all_orders();
if ($active_plans) :

    if (count($active_plans) > 1) {
        //_assign_to_directory
?>
        <div class="directorist-add-listing-types directorist-w-100">
            <div class="<?php Helper::directorist_container_fluid(); ?>">
                <div class="<?php Helper::directorist_row(); ?> directorist-justify-content-center ">

                    <?php foreach ($active_plans as $plan) : ?>

                        <?php
                        $directory_type = get_post_meta($plan, '_assign_to_directory', true) ? get_post_meta($plan, '_assign_to_directory', true) : default_directory_type();
                        $url = MPP_SITE_URL . '/add-listing/?directory_type=' . $directory_type . '&plan=' . $plan;

                        $config = get_term_meta($directory_type, 'general_config', true);
                        $icon = $config && isset($config['icon']) ? $config['icon'] : '';
                        $term = get_term_by('id', $directory_type, ATBDP_DIRECTORY_TYPE);

                        //$term = get_term
                        ?>

                        <div class="<?php Helper::directorist_column(['lg-3', 'md-4', 'sm-6']); ?>">
                            <div class="directorist-add-listing-types__single">

                                <a href="<?php echo esc_url($url); ?>" class="directorist-add-listing-types__single__link">
                                    <i class="<?php echo esc_html($icon); ?>"></i>
                                    <span><?php echo esc_html($term->name ? $term->name : ''); ?></span>
                                </a>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    <?php
    } else {
        $directory_type = get_post_meta($active_plans[0], '_assign_to_directory', true) ? get_post_meta($active_plans[0], '_assign_to_directory', true) : default_directory_type();
        $url = MPP_SITE_URL . '/add-listing/?directory_type=' . $directory_type . '&plan=' . $active_plans[0];
    ?>
        <script type="text/javascript">
            window.location.replace("<?php echo $url; ?>");
        </script>
    <?php
    }

else :
    ?>
    <div class="no-access-pricing-plan">
        <p>Hello,</p>
        <p>You’ve selected an area that is exclusive to Members only.</p>
        <p>Please click the following button to learn how you can become an Member.</p>
    </div>
    <?php if (mpp_is_android_or_ios()) : ?>
        <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/screen/iap_products/">Membership Plans</a>
        <a class="button" target="_self" href="<?php echo MPP_SITE_URL; ?>/add-listing/"><span class="fa fa-redo"></span></a>
    <?php else : ?>
        <a class="button" href="<?php echo MPP_SITE_URL; ?>/mpp-memberships/">Membership Plans</a>
        <a href="" class="button" onclick="location.reload();"><span class="fa fa-redo"></span> Refresh</a>
    <?php endif; ?>
<?php
endif;
