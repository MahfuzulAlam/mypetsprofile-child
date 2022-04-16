<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
/**
 * Fire before pricing plan loaded
 */
// update_post_meta('213', '_listing_id', '' );

// e_var_dump( [
//     'subscription' => get_post_meta( 357, '_listing_id', true ),
//     'order' => get_post_meta( 356, '_listing_id', true ),
//     'directorist_plan_lifetime' => directorist_plan_lifetime(50),
// ] );

// BitLogger::get_instance()->add_log('testsdfsd', 'sdfsdfds');


do_action('atbdp_before_plan_page_loaded');
$atts = !empty($args['atts']) ? $args['atts'] : '';
$atts = shortcode_atts(
    array(
        'id' => '',
        'columns' => 3,
    ),
    $atts
);
$plans_id = !empty($atts['id']) ? explode(',', $atts['id']) : '';
$shortcode_id = (!empty($atts['id']) && 1 == count($plans_id))  ? $atts['id'] : '';
$columns = !empty($atts['columns']) ? $atts['columns'] : 3;
$private_plan = !empty($shortcode_id) ? 'EXISTS' : 'NOT EXISTS';
$listing_type = isset($_GET['directory_type']) ? sanitize_text_field($_GET['directory_type']) : '';
$submission_form_fields = [];
$listing_type = !empty($listing_type) ? $listing_type : default_directory_type();
$term = get_term_by(is_numeric($listing_type) ? 'id' : 'slug', $listing_type, 'atbdp_listing_types');
if ($listing_type && $term) {
    $submission_form = get_term_meta($term->term_id, 'submission_form_fields', true);
    $submission_form_fields = $submission_form['fields'];
    $submission_form_fields = swbd_pricing_plan__include_additional_submission_fields(['submission_form_fields' => $submission_form_fields]);
}
?>
<div id="fm_plans_container" <?php do_action('atbdp_plans_container_div_attribute'); ?>>

    <?php
    $types = directory_types();
    if (directorist_multi_directory() && count($types) > 1) {
        DWPP_Pricing_Plans()->load_template('directory_types', ['directory_types' => $types]);
    }
    ?>

    <div class="atbd_plans_row">
        <?php

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'listing_pricing_plans',
                ),
            ),
        );
        $meta_queries = array();
        $meta_queries[] = array(
            'relation' => 'OR',
            array(
                'key' => '_hide_from_plans',
                'compare' => $private_plan,
            ),
            array(
                'key' => '_hide_from_plans',
                'value' => 1,
                'compare' => '!=',
            ),
        );

        if (!empty($listing_type)) {
            $meta_queries['directory_type'] = [
                'key' => '_assign_to_directory',
                'value' => $term->term_id,
                'compare' => '=',
            ];
        }

        $meta_queries = apply_filters('atbdp_plan_meta_query', $meta_queries);
        $count_meta_queries = count($meta_queries);
        if ($count_meta_queries) {
            $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
        }

        /*
        if (empty($plans_id)) {
            $plans_id = [18064, 18065, 18066];
        }

        $args['post__in'] = $plans_id;
        $args['orderby']    = 'post__in';
        */

        $atbdp_query = new WP_Query($args);
        $has_plan = $atbdp_query->have_posts();
        $plans = $atbdp_query->posts;
        if ($has_plan && $plans) {
            $bbapp_is_active_biz_plan = bbapp_is_active_biz_plan($plans);
            if ($bbapp_is_active_biz_plan) {
                foreach ($plans as $key => $value) {
                    $plan_id = $value->ID;
                    $plan_id = !empty($shortcode_id) ? $shortcode_id : $plan_id;
                    $plan_metas = get_post_meta($plan_id);
                    $unl = __('Unlimited', 'directorist-woocommerce-pricing-plans');
                    $fm_price = !empty($plan_metas['_sale_price'][0]) ? esc_attr($plan_metas['_sale_price'][0]) : '';
                    $hide_recurring = !empty($plan_metas['_hide_subscription'][0]) ? esc_attr($plan_metas['_hide_subscription'][0]) : '';
                    $recurring = !empty($plan_metas['_enable_subscription'][0]) ? esc_attr($plan_metas['_enable_subscription'][0]) : '';
                    $linked_subscription = !empty($plan_metas['_linked_subscription'][0]) ? esc_attr($plan_metas['_linked_subscription'][0]) : '';
                    $regular_price = !empty($plan_metas['_regular_price'][0]) ? esc_attr($plan_metas['_regular_price'][0]) : '';

                    $fm_length = !empty($plan_metas['fm_length'][0]) ? esc_attr($plan_metas['fm_length'][0]) : '';
                    $fm_length_unl = !empty($plan_metas['fm_length_unl'][0]) ? esc_attr($plan_metas['fm_length_unl'][0]) : '';

                    $price_text = ($fm_length > 1) ? $fm_length . ' ' . __('days', 'directorist-woocommerce-pricing-plans') : __('day', 'directorist-woocommerce-pricing-plans');

                    if ($fm_length_unl) {
                        $price_text = __('Lifetime', 'directorist-woocommerce-pricing-plans');
                    }

                    if (class_exists('WC_Subscriptions_Product') && $recurring && $linked_subscription) {

                        $length                 = get_post_meta($linked_subscription, '_subscription_period_interval', true);
                        $subscription_period    = get_post_meta($linked_subscription, '_subscription_period', true);

                        switch ($subscription_period) {
                            case 'day';
                                $price_text = ($length > 1) ? $length . ' ' . __('days', 'directorist-woocommerce-pricing-plans') : __('day', 'directorist-woocommerce-pricing-plans');
                                break;

                            case 'week';
                                $price_text = ($length > 1) ? $length . ' ' . __('weeks', 'directorist-woocommerce-pricing-plans') : __('week', 'directorist-woocommerce-pricing-plans');
                                break;

                            case 'month';
                                $price_text = ($length > 1) ? $length . ' ' . __('months', 'directorist-woocommerce-pricing-plans') : __('month', 'directorist-woocommerce-pricing-plans');
                                break;

                            case 'year';
                                $price_text = ($length > 1) ? $length . ' ' . __('years', 'directorist-woocommerce-pricing-plans') : __('year', 'directorist-woocommerce-pricing-plans');
                                break;
                        }
                        $fm_length      = directorist_plan_lifetime($linked_subscription);
                        $fm_price       = get_post_meta($linked_subscription, '_sale_price', true);
                        $regular_price  = get_post_meta($linked_subscription, '_subscription_price', true);
                        $signup_fee     = get_post_meta($linked_subscription, '_subscription_sign_up_fee', true);
                        $expire_after   = get_post_meta($linked_subscription, '_subscription_length', true);
                        $trail          = \WC_Subscriptions_Product::get_trial_length($linked_subscription);
                        $trail_period   = \WC_Subscriptions_Product::get_trial_period($linked_subscription);
                    }

                    $num_regular = !empty($plan_metas['num_regular'][0]) ? esc_attr($plan_metas['num_regular'][0]) : '';
                    $num_regular_unl = !empty($plan_metas['num_regular_unl'][0]) ? esc_attr($plan_metas['num_regular_unl'][0]) : '';
                    $num_featured = !empty($plan_metas['num_featured'][0]) ? esc_attr($plan_metas['num_featured'][0]) : '';
                    $num_featured_unl = !empty($plan_metas['num_featured_unl'][0]) ? esc_attr($plan_metas['num_featured_unl'][0]) : '';
                    $fm_cs_review = !empty($plan_metas['fm_cs_review'][0]) ? esc_attr($plan_metas['fm_cs_review'][0]) : '';
                    $fm_claim = !empty($plan_metas['_fm_claim'][0]) ? esc_attr($plan_metas['_fm_claim'][0]) : '';
                    $hide_claim = !empty($plan_metas['_dwpp_hide_claim'][0]) ? esc_attr($plan_metas['_dwpp_hide_claim'][0]) : '';
                    $default_pln = !empty($plan_metas['default_pln'][0]) ? esc_attr($plan_metas['default_pln'][0]) : '';
                    $plan_type = !empty($plan_metas['plan_type'][0]) ? esc_attr($plan_metas['plan_type'][0]) : '';
                    $cf_owner          = isset($plan_metas['cf_owner'][0]) ? esc_attr($plan_metas['cf_owner'][0]) : '';


                    // Booking
                    $fm_booking = isset($plan_metas['_fm_booking'][0]) ? esc_attr($plan_metas['_fm_booking'][0]) : '';
                    $hide_booking = isset($plan_metas['_dwpp_hide_booking'][0]) ? esc_attr($plan_metas['_dwpp_hide_booking'][0]) : '';

                    // Live Chat
                    $fm_live_chat = isset($plan_metas['_fm_live_chat'][0]) ? esc_attr($plan_metas['_fm_live_chat'][0]) : '';
                    $hide_live_chat = isset($plan_metas['_dwpp_hide_live_chat'][0]) ? esc_attr($plan_metas['_dwpp_hide_live_chat'][0]) : '';

                    // Mark as sold
                    $fm_mark_as_sold = isset($plan_metas['_fm_mark_as_sold'][0]) ? esc_attr($plan_metas['_fm_mark_as_sold'][0]) : '';
                    $hide_mark_as_sold = isset($plan_metas['_dwpp_hide_mark_as_sold'][0]) ? esc_attr($plan_metas['_dwpp_hide_mark_as_sold'][0]) : '';

                    if (is_user_logged_in()) {
                        $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $plan_id);
                    } else {
                        $active_plan = false;
                    }

                    $fresh_active_order = directorist_wc_active_orders_without_listing($plan_id);

                    // var_dump($fresh_active_order);
                    $is_active         = false;

                    if ('package' === package_or_PPL($plan_id) && $active_plan) {
                        $is_active = true;
                    }

                    if ('package' !== package_or_PPL($plan_id) && $fresh_active_order && $active_plan) {
                        $is_active = true;
                    }

                    $c_position = get_option('woocommerce_currency_pos');
                    $symbol = get_woocommerce_currency_symbol();
                    $before = '';
                    $after = '';
                    if ('right' === $c_position) :
                        $after = $symbol;
                    endif;
                    if ('right_space' === $c_position) :
                        $after = ' ' . $symbol;
                    endif;
                    if ('left_space' == $c_position) :
                        $before = $symbol . ' ';
                    endif;
                    if ('left' === $c_position) :
                        $before = $symbol;
                    endif;
                    $columns_class = 'atbd_plan_col atbd_plan_col' . $columns . ' dwpp_' . strtolower($value->post_title);;
                    $discount_percentage = round(100 - ((int) $fm_price / (int) $regular_price * 100));
                    do_action('atbdp_after_start_plans_loop', $plan_id);
        ?>
                    <div class="<?php echo $columns_class; ?>">
                        <div class="pricing pricing--1 <?php echo !empty($plan_metas['default_pln'][0]) ? 'atbd_pricing_special' : ''; ?> shadow-lg-2">
                            <?php echo !empty($plan_metas['default_pln'][0]) ? ' <span class="atbd_popular_badge">' . __('Recommended', 'directorist-woocommerce-pricing-plans') . '</span>' : ''; ?>
                            <div class="pricing__title">
                                <h4><?php echo $value->post_title; ?>
                                    <?php echo $is_active ? __(' <span class="atbd_plan-active">Active</span>', 'directorist-woocommerce-pricing-plans') : ''; ?>
                                </h4>
                            </div>
                            <div class="pricing__price rounded">
                                <div class="pricing-onsale">
                                    <?php
                                    if (!empty($regular_price) && !empty($fm_price)) {
                                        echo '<span class="pricing-prev"><sup>' . $before . '</sup>' . $regular_price, $after . '</span>
                                    <span class="pricing-onsale-badge">' . '- ' . $discount_percentage . '%' . '</span>
                                    ';
                                    }
                                    ?>
                                </div>
                                <div class="pricing-part">
                                    <p class="pricing_value">
                                        <sup><?php echo $before; ?></sup><?php echo empty($fm_price) ? (!empty($regular_price) ? $regular_price : '0') : $fm_price; ?>
                                        <sup><?php echo $after; ?></sup>
                                    </p>
                                    <span>/ <?php echo $price_text; ?></span>
                                </div>
                                <p class="pricing_subtitle"><?php echo ($plan_type == 'pay_per_listng') ? __('Per Listing', 'directorist-woocommerce-pricing-plans') : __('Per Package', 'directorist-woocommerce-pricing-plans') ?></p>
                                <?php if (!empty($trail)) {
                                    $day_text = $trail_period;
                                ?>
                                    <small class="pricing_description">
                                        <?php echo esc_attr($trail) . sprintf(' ' . __('%s %s Trial', 'directorist-woocommerce-pricing-plans'), $day_text, '<strong>' . __('FREE', 'directorist-woocommerce-pricing-plans') . '</strong>'); ?>
                                    </small>
                                <?php } ?>
                                <div class="pricing__price--valid-fee">
                                    <?php if (!empty($signup_fee)) { ?>
                                        <small class="pricing_description">
                                            <?php echo $before . $signup_fee, $after  . ' ' . __('Signup Fee', 'directorist-woocommerce-pricing-plans'); ?>
                                        </small>
                                    <?php } ?>
                                    <?php if (!empty($linked_subscription) && !empty($expire_after)) { ?>
                                        <small class="pricing_description">
                                            <?php
                                            echo __('Valid for', 'directorist-woocommerce-pricing-plans') . ' ' . $expire_after . ' ' . $price_text;
                                            ?>
                                        </small>
                                    <?php } ?>
                                </div>

                                <?php
                                if (!empty($value->post_excerpt) && $plan_id !== 18059) {
                                ?>
                                    <p class="pricing_description"><?php echo !empty($value->post_excerpt) ? $value->post_excerpt : ''; ?></p>
                                <?php } ?>
                            </div>
                            <div class="pricing__features">
                                <ul>
                                    <?php if ($plan_id == 18059) : ?>
                                        <li><span class="fa fa-check"></span>All MPP Platform Access</li>
                                        <li><span class="fa fa-check"></span>MPP Elite ID and Private Members Portal</li>
                                        <li><span class="fa fa-check"></span>Get Today's Top Pet Funnies</li>
                                        <li><span class="fa fa-check"></span>Get Today's Top Pet Events and Photos</li>
                                        <li><span class="fa fa-check"></span>Win Cash and Prizes - MPP Funnies</li>
                                        <li><span class="fa fa-check"></span>10% Commissions on all paid referrals</li>
                                        <li><span class="fa fa-check"></span>Win Cash and Prizes - MPP Referral Contests</li>
                                        <li><span class="fa fa-check"></span>Unlimited Access to Profiles, Groups, Listings and Forums</li>
                                        <li><span class="fa fa-check"></span>Unlimited Access to Pet Training Portal</li>
                                        <li><span class="fa fa-check"></span>Unlimited Messaging, SMS and Email</li>
                                        <li><span class="fa fa-check"></span>Public or Private Pet Social Group</li>
                                    <?php else : ?>
                                        <?php
                                        if (!$hide_recurring) { ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($recurring) ? 'check' : 'times'; ?>"> </span><?php _e('Auto renewing', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }
                                        if (($plan_type == 'pay_per_listng') && empty(apply_filters('atbdp_plan_featured_compare', $plan_metas['_dwpp_hide_listing_featured'][0]))) { ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($plan_metas['is_featured_listing'][0]) ? 'check' : 'times'; ?>"> </span><?php _e('Listing as featured', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }
                                        if ($plan_type != 'pay_per_listng') { ?>
                                            <?php if (empty($plan_metas['_dwpp_hide_listings'][0])) { ?>
                                                <li><span class="fa fa-<?php if (($num_regular > 0) || $num_regular_unl) {
                                                                            echo 'check';
                                                                        } else {
                                                                            echo 'times';
                                                                        } ?>"></span><?php echo $num_regular_unl ? '<span class="atbd_color-success">' . $unl . '</span>' . __(' Regular Listings', 'directorist-woocommerce-pricing-plans') . '' : $num_regular . __(' Regular Listings', 'directorist-woocommerce-pricing-plans'); ?>
                                                </li>
                                            <?php }
                                            if (empty(apply_filters('atbdp_plan_featured_compare', $plan_metas['_dwpp_hide_featured'][0]))) { ?>
                                                <li><span class="fa fa-<?php if (($num_featured > 0) || $num_featured_unl) {
                                                                            echo 'check';
                                                                        } else {
                                                                            echo 'times';
                                                                        } ?>"></span><?php echo $num_featured_unl ? '<span class="atbd_color-success">' . $unl . '</span>' . __(' Featured Listings', 'directorist-woocommerce-pricing-plans') . '' : $num_featured . __(' Featured Listings', 'directorist-woocommerce-pricing-plans'); ?>
                                                </li>
                                                <?php }
                                        }

                                        if ($submission_form_fields) {
                                            foreach ($submission_form_fields as $field) {
                                                $label       = !empty($field['label']) ? $field['label'] : '';
                                                $field_key   = !empty($field['field_key']) ? $field['field_key'] : '';
                                                $widget_name = !empty($field['widget_name']) ? $field['widget_name'] : '';

                                                if ('tax_input[at_biz_dir-location][]'  == $field_key) {
                                                    $field_key = 'location';
                                                }
                                                if ('admin_category_select[]'           == $field_key) {
                                                    $field_key = 'category';
                                                }
                                                if ('tax_input[at_biz_dir-tags][]'      == $field_key) {
                                                    $field_key = 'tag';
                                                }

                                                //if( 'faqs' == $widget_name ) continue;
                                                if ('booking' == $widget_name) continue;
                                                if ('listing_type' == $widget_name) continue;

                                                if ('pricing' == $widget_name) {
                                                    $field_key  = 'pricing';
                                                }

                                                $field_allow    = get_post_meta($plan_id, '_' . $field_key, true);
                                                $hide           = get_post_meta($plan_id, '_hide_' . $field_key, true);
                                                $max            = get_post_meta($plan_id, '_max_' . $field_key, true);
                                                $unlimited      = get_post_meta($plan_id, '_unlimited_' . $field_key, true);

                                                if (!$hide) { ?>
                                                    <li>
                                                        <span class="fa fa-<?php echo !empty($field_allow) ? 'check' : 'times'; ?>"> </span>

                                                        <?php
                                                        echo esc_attr($label);
                                                        if (!empty($max) || !empty($unlimited)) {  ?>
                                                            <small><?php
                                                                    if (!empty($unlimited)) {
                                                                        echo __('(Unlimited ', 'directorist-woocommerce-pricing-plans') . $label . ')';
                                                                    } else {
                                                                        echo __('(Maximum of ', 'directorist-woocommerce-pricing-plans') . $max . ')';
                                                                    } ?>
                                                            </small>
                                                        <?php } ?>

                                                    </li>
                                            <?php
                                                }
                                            }
                                        }

                                        if (empty(apply_filters('atbdp_plan_contact_owner_compare', $plan_metas['_dwpp_hide_cl_owner'][0]))) {
                                            ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($cf_owner) ? 'check' : 'times'; ?>"> </span><?php _e('Contact Owner', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }
                                        if (empty(apply_filters('atbdp_plan_review_compare', $plan_metas['_dwpp_hide_customer_review'][0]))) {
                                        ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($fm_cs_review) ? 'check' : 'times'; ?>"> </span><?php _e('Allow Customer Review', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }
                                        if (empty($plan_metas['_dwpp_hide_category'][0])) {
                                            $is_cat = selected_plan_meta($plan_id, 'exclude_cat');
                                        ?>
                                            <li><span class="fa fa-<?php if (empty($is_cat)) {
                                                                        echo 'check';
                                                                    } else {
                                                                        echo 'times';
                                                                    } ?>"> </span><?php _e('All Categories', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }
                                        if (empty(apply_filters('atbdp_plan_claim_compare', $hide_claim))) {
                                        ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($fm_claim) ? 'check' : 'times'; ?>"> </span><?php _e('Claim Badge Included', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }

                                        // Booking
                                        if (empty(apply_filters('atbdp_plan_booking_compare', $hide_booking))) { ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($fm_booking) ? 'check' : 'times'; ?>"> </span><?php _e('Booking Included', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }

                                        // Live Chat
                                        if (empty(apply_filters('atbdp_plan_live_chat_compare', $hide_live_chat))) { ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($fm_live_chat) ? 'check' : 'times'; ?>"> </span><?php _e('Live Chat Included', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }

                                        // Mark as sold
                                        if (empty(apply_filters('atbdp_plan_mark_as_sold_compare', $hide_mark_as_sold))) { ?>
                                            <li>
                                                <span class="fa fa-<?php echo !empty($fm_mark_as_sold) ? 'check' : 'times'; ?>"> </span><?php _e('Mark as Sold Included', 'directorist-woocommerce-pricing-plans') ?>
                                            </li>
                                        <?php }

                                        /*
                                 * @since 1.0.0
                                 * Fires in plan compare page
                                 * hook for future dev
                                 */
                                        do_action('atpp_after_pricing_plans_compare_fields', $value->ID);
                                        $used_free_plan = true;
                                        if (package_or_PPL($value->ID) === 'pay_per_listng') {
                                            $used_free_plan = dwpp_get_used_free_plan($value->ID, get_current_user_id());
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                                <div class="directorist-pricing__action">
                                    <?php
                                    $url = apply_filters('atbdp_pricing_plan_to_checkout_url', dwpp_add_listing_page_link_with_plan($value->ID, $active_plan, $is_active), $value->ID);
                                    $web_class = '';
                                    if (
                                        strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false &&
                                            (strpos($_SERVER['HTTP_USER_AGENT'], 'chrome') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'safari') == false))
                                    ) {
                                        $web_class = 'app-directorist-pricing__action--btn';
                                    } else {
                                        $web_class = 'directorist-pricing__action--btn';
                                    }
                                    ?>
                                    <input id="fee_plans[<?php echo $value->ID; ?>]" type="hidden" value="<?php echo $value->ID; ?>" name="fm_plans">
                                    <label for="fee_plans[<?php echo $value->ID; ?>]"><a href="<?php echo esc_url($url); ?>" onclick="return <?php echo !$used_free_plan ? 'false' : 'true' ?>;" class="btn btn-block <?php echo $web_class; ?>" data-active_status="<?php echo $is_active ? 'yes' : ''; ?>"><?php !$used_free_plan ? _e('Already Used!', 'directorist-woocommerce-pricing-plans') : _e('Continue', 'directorist-woocommerce-pricing-plans') ?></a></label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="iap-redirect">
                    <p>Please buy a Pet-friendly Biz Bundle to continue.</p>
                    <!-- <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/13">Pet-friendly Housing</a>
                    <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/17">Health and Wellness</a>
                    <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/16">Pet Product and Services</a>
                    <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/15">Entertainment and Publications</a>
                    <a class="button" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/14">Travel and Tourism</a> -->
                </div>
            <?php
            }
        } else {
            ?>
            <div class="col-md-12">
                <div class="atbd_pricing_status">
                    <?php printf('<p>%s</p>', __('There is no Plan available right now. Please contact with administrator.', 'directorist-woocommerce-pricing-plans')); ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <!--ends. row-->
</div>
<!--ends. fm_plans_container-->