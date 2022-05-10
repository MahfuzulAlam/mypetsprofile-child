<?php

/**
 * @author  wpWax
 * @since   6.6
 * @version 6.7
 */

if (!defined('ABSPATH')) exit;

$active_plan = mpp_get_active_pricing_plan_from_all_orders();
if ($active_plan) :
    $directory_type = get_post_meta($active_plan, '_assign_to_directory', true) ? get_post_meta($active_plan, '_assign_to_directory', true) : default_directory_type();
    $url = MPP_SITE_URL . '/add-listing/?directory_type=' . $directory_type . '&plan=' . $active_plan;
?>
    <script type="text/javascript">
        window.location.replace("<?php echo $url; ?>");
    </script>
<?php
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
