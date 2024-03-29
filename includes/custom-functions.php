<?php
// Add your own custom functions here

add_filter('woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart');

function bbloomer_redirect_checkout_add_cart()
{
    return wc_get_checkout_url();
}

// Remove "Returning customer? Click here to login" From Checkout Page
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);

// Remove "category? From product Page
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

//add_filter('bp_core_signup_send_activation_key', 'ps_disable_activation_email');
function ps_disable_activation_email()
{
    return false;
}

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// Custom Code by Alam - Starts Here

// MPP Custom Thumbnail Loop Code

function mypetsprofile_loop_get_the_thumbnail($class = '')
{
    $image_alt = '';
    $default_image_src = MPP_SITE_URL . "/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

    $id = get_the_ID();
    $image_quality     = get_directorist_option('preview_image_quality', 'large');
    $listing_prv_img   = get_post_meta($id, '_listing_prv_img', true);
    $listing_img       = get_post_meta($id, '_listing_img', true);

    if (is_array($listing_img) && !empty($listing_img)) {
        $thumbnail_img = atbdp_get_image_source($listing_img[0], $image_quality);
        $thumbnail_id = $listing_img[0];
    }

    if (!empty($listing_prv_img)) {
        $thumbnail_img = atbdp_get_image_source($listing_prv_img, $image_quality);
        $thumbnail_id = $listing_prv_img;
    }

    if (!empty($img_src)) {
        $thumbnail_img = $img_src;
        $thumbnail_id = 0;
    }

    if (empty($thumbnail_img)) {

        $thumbnail_img = $default_image_src;
        $thumbnail_id = 0;

        // GET CATEGORY IMAGE

        $category_list = get_the_terms($id, ATBDP_CATEGORY);
        if ($category_list && !is_wp_error($category_list) && count($category_list) > 0) {
            $category_img = get_term_meta($category_list[0]->term_id, 'image', true);
            if ($category_img) {
                $cat_img_url = wp_get_attachment_image_url($category_img);
                if ($cat_img_url) {
                    $thumbnail_img = $cat_img_url;
                    $thumbnail_id = $category_img;
                    $class .= ' image-from-category';
                }
            }
        }
    }

    // CHECK the unit and get image from the apartments
    $mpp_housing = get_post_meta(get_the_ID(), '_mpp-housing', true);
    if ($mpp_housing && !empty($mpp_housing)) {
        $mpp_images = get_post_meta($mpp_housing, '_mpp_photos', true);
        if (!empty($mpp_images) && count($mpp_images) > 0) {
            $mpp_image = $mpp_images[array_rand($mpp_images)];
            $thumbnail_img = $mpp_image->thumbnailUrl;
            $image_src = get_the_title();
        }
    }
    // CHECK the unit and get image from the apartments


    $image_src    = $thumbnail_img;
    if (empty($image_alt)) {
        $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        $image_alt = (!empty($image_alt)) ? esc_attr($image_alt) : esc_html(get_the_title($thumbnail_id));
        $image_alt = (!empty($image_alt)) ? $image_alt : esc_html(get_the_title());
    }

    return "<img src='$image_src' alt='$image_alt' class='$class' />";
}

// MPP Custom Profile Image

function mypetsprofile_listing_get_the_thumbnail($class = '')
{
    $default_image_src = MPP_SITE_URL . "/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

    $id = get_the_ID();
    $image_quality     = get_directorist_option('preview_image_quality', 'large');
    $listing_prv_img   = get_post_meta($id, '_listing_prv_img', true);
    $listing_img       = get_post_meta($id, '_listing_img', true);

    if (is_array($listing_img) && !empty($listing_img)) {
        $thumbnail_img = atbdp_get_image_source($listing_img[0], $image_quality);
        $thumbnail_id = $listing_img[0];
    }

    if (!empty($listing_prv_img)) {
        $thumbnail_img = atbdp_get_image_source($listing_prv_img, $image_quality);
        $thumbnail_id = $listing_prv_img;
    }

    if (!empty($img_src)) {
        $thumbnail_img = $img_src;
        $thumbnail_id = 0;
    }

    if (empty($thumbnail_img)) {

        $thumbnail_img = $default_image_src;
        $thumbnail_id = 0;

        // GET CATEGORY IMAGE

        $category_list = get_the_terms($id, ATBDP_CATEGORY);
        if ($category_list && !is_wp_error($category_list) && count($category_list) > 0) {
            $category_img = get_term_meta($category_list[0]->term_id, 'image', true);
            if ($category_img) {
                $cat_img_url = wp_get_attachment_image_url($category_img);
                if ($cat_img_url) {
                    $thumbnail_img = $cat_img_url;
                    $thumbnail_id = $category_img;
                    $class .= ' image-from-category';
                }
            }
        }
    }

    $image_src    = $thumbnail_img;
    $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $image_alt = (!empty($image_alt)) ? esc_attr($image_alt) : esc_html(get_the_title($thumbnail_id));
    $image_alt = (!empty($image_alt)) ? $image_alt : esc_html(get_the_title());

    return array('image_src' => $image_src, 'image_alt' => $image_alt, 'image_class' => $class);
}

// Add custom fields in Import
add_action('init', function () {
    $tools = ATBDP()->tools;
    $tools->importable_fields['directorist_listing_rating'] = "Average Rating";
    $tools->importable_fields['social_facebook'] = "Facebook Url";
});

// function mpp_bbd_inspect_scripts()
// {
//     if (!is_singular('at_biz_dir') && !is_admin()) wp_dequeue_script('directorist-google-map');
//     wp_dequeue_script('directorist-google-map');
//     wp_deregister_script('directorist-google-map');
// }
//add_action('wp_print_scripts', 'mpp_bbd_inspect_scripts');

function bbd_get_option_data()
{
    $options = [];
    $options['script_debugging'] = get_directorist_option('script_debugging', DIRECTORIST_LOAD_MIN_FILES, true);
    return $options;
}


// Woocommerce Pricing Plan
function directorist_wc_active_orders_without_listing($plan_id = '')
{
    $status = ["wc-completed"];
    /*
    if (directoirst_wc_plan_auto_renewal($plan_id)) {
        $plan_id = directoirst_wc_plan_auto_renewal($plan_id);
        $subscription = true;
        $status = ["wc-completed", "wc-processing"];
    }
    */
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => $status,
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_fm_plan_ordered',
                'value'   => $plan_id,
                'compare' => '=',
            ],
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => '_listing_id',
                    'value'   => '0',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],
        ],
    ];

    $active_plan = new WP_Query($args);

    if ($active_plan->have_posts()) {
        return true;
    } else {
        return false;
    }
}

// MPP Check if user has the permission to claim listing.

// Woocommerce Pricing Plan
function directorist_wc_mpp_user_can_claim()
{
    $status = ["wc-completed"];
    /*
    if (directoirst_wc_plan_auto_renewal($plan_id)) {
        $plan_id = directoirst_wc_plan_auto_renewal($plan_id);
        $subscription = true;
        $status = ["wc-completed", "wc-processing"];
    }
    */
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => $status,
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_fm_plan_ordered',
                'value'   => 18952,
                'compare' => '=',
            ],
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => '_listing_id',
                    'value'   => '0',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],
        ],
    ];

    $active_plan = new WP_Query($args);

    if ($active_plan->have_posts()) {
        return true;
    } else {
        return false;
    }
}

// GET pricing plan ID from the Order ID
function mpp_get_pricing_plan_from_the_order($order_id)
{
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_key => $item) :
        $item_id = $item->get_product_id();
        $plan_id = get_post_meta($item_id, 'linked_pricing_plan', true) ? get_post_meta($item_id, 'linked_pricing_plan', true) : $item_id;
        $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $plan_id;
        return $plan_id;
    endforeach;
    return false;
}

// Check if Event ID
function mpp_event_id_in_the_order($order_id)
{
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_key => $item) :
        $item_id = $item->get_product_id();
        if ($item_id == 18236) return true;
    endforeach;
    return false;
}

// GET acive pricing plan from all orders list
function mpp_get_active_pricing_plans_from_all_orders()
{
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => ["wc-completed"],
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => '_listing_id',
                    'value'   => '0',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ],
    ];

    $pricing_plans = array();

    $active_orders = new WP_Query($args);

    if ($active_orders) {
        if (isset($active_orders->posts) && count($active_orders->posts) > 0) {
            foreach ($active_orders->posts as $order_id) {
                $order = wc_get_order($order_id);
                foreach ($order->get_items() as $item_key => $item) :
                    $item_id = $item->get_product_id();
                    // Exceptions
                    if ($item_id == 20139) $pricing_plans[] = 20140;
                    // Subcsription check else use plan ID
                    $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $item_id;
                    if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans' && !in_array($plan_id, array(18059, 18242)))  $pricing_plans[] = $plan_id;
                endforeach;
            }
        }
    }

    return $pricing_plans;
}

// GET acive pricing plan from all orders
function mpp_get_active_pricing_plan_from_all_orders()
{
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => ["wc-completed"],
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => '_listing_id',
                    'value'   => '0',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ],
    ];

    $active_orders = new WP_Query($args);

    while ($active_orders->have_posts()) : $active_orders->the_post();
        $order = wc_get_order(get_the_ID());
        foreach ($order->get_items() as $item_key => $item) :
            $item_id = $item->get_product_id();
            // Exceptions
            if ($item_id == 20139) return 20140;

            $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $item_id;
            if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans' && !in_array($plan_id, array(18059, 18242)))  return $plan_id;
        endforeach;
    endwhile;

    return false;
}

// GET acive apartment pricing plan from all orders
function mpp_get_active_apartment_pricing_plan_from_all_orders()
{
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => ["wc-completed"],
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
        ],
    ];

    $active_orders = new WP_Query($args);

    //e_var_dump($active_orders);

    while ($active_orders->have_posts()) : $active_orders->the_post();
        $order = wc_get_order(get_the_ID());
        foreach ($order->get_items() as $item_key => $item) :
            $item_id = $item->get_product_id();
            if ($item_id == 598) {
                $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $item_id;
                if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans')  return $plan_id;
            }
        endforeach;
    endwhile;

    return false;
}

//add_action('woocommerce_payment_complete', 'wpp_assign_role_after_payment_complete');

function wpp_assign_role_after_payment_complete($order_id)
{

    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_key => $item) :
        $item_id = $item->get_product_id();

        if (in_array($item_id, array(422, 18053))) :

            $user = $order->get_user();
            if ($user) {
                $u = new WP_User($user->ID);

                // Remove role
                //$u->remove_role('subscriber');

                // Add role
                $u->add_role('editor');
            }

        endif;

    endforeach;
}

//add_action('woocommerce_order_status_changed', 'user_role_change_on_order_complete', 10, 4);

function user_role_change_on_order_complete($order_id, $from, $to, $order)
{
    if ($to == 'completed') :
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item) :
            $item_id = $item->get_product_id();

            if (in_array($item_id, array(422, 18053))) : // Insert Pricing Plan IDs here

                $user = $order->get_user();
                if ($user) {
                    $u = new WP_User($user->ID);

                    // Remove role
                    //$u->remove_role('subscriber');

                    // Add role
                    $u->add_role('editor');
                }

            endif;

        endforeach;
    endif;
}

add_action('mec_save_event_data', function ($post_id) {
    update_user_meta(get_current_user_id(), 'mec_event_status', 'used');
    $u = new WP_User(get_current_user_id());
    $u->add_role('event_creator');
});


// NEW ROLE
function mec_custom_new_role()
{
    //add the new user role
    add_role(
        'event_creator',
        'Event Creator',
        array(
            'read'          => true,
            'edit_posts'     => true,
        )
    );
}
add_action('admin_init', 'mec_custom_new_role');


// bbapp_is_active_biz_plan
function bbapp_is_active_biz_plan($plans)
{
    $is_active = true;

    if (
        strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'chrome') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'safari') == false))
    ) {

        $is_active = false;

        foreach ($plans as $key => $value) {
            $plan_id = $value->ID;
            if (is_user_logged_in()) {
                $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $plan_id);
            } else {
                $active_plan = false;
            }

            $fresh_active_order = directorist_wc_active_orders_without_listing($plan_id);

            if ('package' === package_or_PPL($plan_id) && $active_plan) {
                $is_active = true;
            }

            if ('package' !== package_or_PPL($plan_id) && $fresh_active_order && $active_plan) {
                $is_active = true;
            }
        }
    }

    return $is_active;
}

add_action('wp_footer', function () {
    if (bp_is_user_profile_edit()) :

?>
        <script type="text/javascript">
            jQuery('document').ready(function() {
                $('.download-user-info').on('click', function() {
                    startPdfProcessing();
                });
            });
        </script>
    <?php
    endif;
});

// Move to template redirect
add_action('wp_footer', function () {
    //e_var_dump(mpp_is_user_mpp_elite_member());
    if (is_page('pet-housing-applicaiton')) {
        $link = bp_members_edit_profile_url('', get_current_user_id());

    ?>
        <script type="text/javascript">
            window.location.href = "<?php echo $link; ?>";
        </script>
    <?php
    }
});


function mpp_is_user_mpp_elite_member($user_id = 0)
{
    $user_id = $user_id ? $user_id : get_current_user_id();
    return wc_memberships_is_user_member($user_id, 516);
}


// Work on it
// add_action('init', function () {
//     //groups_is_user_mod or groups_is_user_member
//     groups_join_group(184, 4);
//     groups_promote_member(4, 184, 'mod');
// });


function mpp_get_funnies_activities($group_id = 0)
{
    if (!$group_id) return false;

    $year = date('Y');
    $month = date('n');
    $day = date('j');
    $activity_query = bp_activity_get(array(
        'page' => 1,
        'filter_query' => array(
            array(
                'column' => 'component',
                'value' => 'groups',
            ),
            array(
                'column' => 'item_id',
                'value' => $group_id,
            ),
            array(
                'column' => 'type',
                'value' => 'activity_update',
            )
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                array(
                    'key' => 'bp_media_ids',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => 'bp_media_ids',
                    'value' => '',
                    'compare' => '!=',
                )
            ),
            'activity_count' => array(
                'key' => 'favorite_count',
                'compare' => 'EXISTS',
            )
        ),
        'date_query' => array(
            array(
                'before'    => array(
                    'year'  => $year,
                    'month' => $month,
                    'day'   => 1,
                ),
                'before'    => array(
                    'year'  => $year,
                    'month' => $month,
                    'day'   => $day,
                ),
                'inclusive' => true,
            ),
        ),
    ));

    if ($activity_query && count($activity_query['activities']) > 0) {
        $activities = $activity_query['activities'];
        foreach ($activities as $activity) {
            $activity->favorite_count = bp_activity_get_meta($activity->id, 'favorite_count', true);
        }
        usort($activities, fn ($a, $b) => $b->favorite_count <=> $a->favorite_count);
        $activities = array_slice($activities, 0, 10);
        foreach ($activities as $activity) {
            $activity->comment_count = mpp_count_activity_comments($activity->id);
        }
        return $activities;
    }

    return false;
}

function mpp_count_activity_comments($activity_id = 0)
{
    if ($activity_id) {
        $comment_query = bp_activity_get(
            array(
                'page' => 1,
                'filter_query' => array(
                    array(
                        'column' => 'component',
                        'value' => 'activity',
                    ),
                    array(
                        'column' => 'item_id',
                        'value' => $activity_id,
                    ),
                    array(
                        'column' => 'type',
                        'value' => 'activity_comment',
                    ),
                ),
                'display_comments' => true
            )
        );

        if ($comment_query && count($comment_query['activities']) > 0) {
            return count($comment_query['activities']);
        }
    }
    return 0;
}

// MPP check if it is app
function mpp_is_android_or_ios()
{
    if (
        strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'chrome') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'safari') == false))
    ) {
        return true;
    } else {
        return false;
    }
}

// REDIRECT PRICING PLAN PAGE

add_action('atbdp_before_plan_page_loaded', function () {
    $active_plan = mpp_get_active_pricing_plan_from_all_orders();
    //$active_apartment_plan = mpp_get_active_apartment_pricing_plan_from_all_orders();

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
});

/*
add_action('wp_footer', function () {
    e_var_dump(date('Y-m-d H:i:s', strtotime('+1 year')));
});
*/

add_filter('directorist_custom_field_meta_key_field_args', function ($args) {
    $args['type'] = 'text';
    return $args;
});

// add_action('wp_footer', function () {
// });

/*
add_filter('woocommerce_email_recipient_customer_completed_order', 'mpp_disable_customer_order_email_if_free', 10, 2);

function mpp_disable_customer_order_email_if_free($recipient, $order)
{
    $page = $_GET['page'] = isset($_GET['page']) ? $_GET['page'] : '';
    if ('wc-settings' === $page) {
        //return $recipient;
    }

    //if ((float) $order->get_total() === '0.00') $recipient = '';

    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
        if (in_array($product_id, array(18053, 403))) return '';
        if ($product_id == 403) return '';
    }

    $recipient = '';

    return $recipient;
}
*/

// add_action('wp_footer', function () {
//     $user_id = 2;
//     $address = array(
//         'first_name' => get_user_meta($user_id, 'first_name', true),
//         'last_name'  => get_user_meta($user_id, 'last_name', true),
//         'company'    => get_user_meta($user_id, 'billing_company', true),
//         'email'      => get_user_meta($user_id, 'billing_email', true),
//         'phone'      => get_user_meta($user_id, 'billing_phone', true),
//         'address_1'  => get_user_meta($user_id, 'billing_address_1', true) . get_user_meta($user_id, 'billing_address_2', true),
//         'address_2'  => 'IAP',
//         'city'       => get_user_meta($user_id, 'billing_city', true),
//         'state'      => get_user_meta($user_id, 'billing_state', true),
//         'postcode'   => get_user_meta($user_id, 'billing_postcode', true),
//         'country'    => get_user_meta($user_id, 'billing_country', true),
//     );

//     // Now we create the order
//     $order = wc_create_order();

//     // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
//     $order->add_product(get_product(403), 1);

//     $order->set_address($address, 'billing');

//     $order->calculate_totals();
//     $order->update_status("wc-completed", "IAP order", TRUE);
// });


//Lets add Open Graph Meta Info

add_filter("rank_math/opengraph/facebook/image", "mpp_custom_og_image");
add_filter("rank_math/opengraph/twitter/image", "mpp_custom_og_image");

function mpp_custom_og_image($attachment_url)
{
    // SINGLE PRODUCT PAGE
    if (is_singular('product')) :
        $og_image = get_post_meta(get_the_ID(), 'og_image', true);
        if ($og_image) :
            $image = wp_get_attachment_image_src($og_image, 'full');
            if ($image) :
                return $image[0];
            endif;
        endif;
    endif;
    // HOME PAGE
    if (is_front_page() && is_home()) :
        $image = wp_get_attachment_image_src(18850, 'full');
        return $image[0];
    endif;
    return $attachment_url;
}


add_action('wp_head', function () {
    if (mpp_is_android_or_ios()) {
    ?>
        <style>
            .directorist-claim-listing-wrapper {
                display: none
            }
        </style>
    <?php
    }
    if (is_page('register')) echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    echo '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> ';
});


/**  Add Reff on signup Buddyboss  **/

add_action('bp_core_signup_user', function ($user_id) {
    if (isset($_COOKIE['affwp_ref']) && !empty($_COOKIE['affwp_ref'])) {
        $campaign = isset($_COOKIE['affwp_campaign']) && !empty($_COOKIE['affwp_campaign']) ? $_COOKIE['affwp_campaign'] : '';
        affwp_add_referral(array(
            'affiliate_id' => $_COOKIE['affwp_ref'],
            'type'  => 'lead',
            'amount'    => 0,
            'description' => 'User Registration',
            'reference' => 'lead_' . $user_id,
            'campaign' => $campaign,
            'status' => 'unpaid'
        ));
    }
});


// Shortcode User Field
// adminsonly, loggedin, friends, public
add_shortcode('bb-user-field-input', function ($atts) {
    $atts = shortcode_atts(array(
        'group' => '1',
        'title' => 'Details'
    ), $atts);
    //$member_id = bbp_get_user_id();
    $member_id = get_current_user_id();
    $field_groups = bp_profile_get_field_groups();
    $field_info = array();

    if (isset($_POST['mpp_form_submitted']) && !empty($_POST['mpp_form_submitted'])) {
        if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
            $profile_fields = $_POST['mpp_profile_box'];
            foreach ($profile_fields as $field_id => $field_value) {
                $field_options = xprofile_get_field($field_id, $member_id);
                if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';
                xprofile_set_field_data($field_id, $member_id, $field_value);
                if (isset($_POST['mpp_visibility_' . $field_id]) && !empty($_POST['mpp_visibility_' . $field_id])) {
                    xprofile_set_field_visibility_level($field_id, $member_id, $_POST['mpp_visibility_' . $field_id]);
                }
                $field_info[$field_id]['key'] = $field_options->description ? $field_options->description : $field_options->name;
                $field_info[$field_id]['value'] = $field_value;
            }
        }

    ?>
        <script type="text/javascript">
            startQnaProcessing('<?php echo json_encode($field_info); ?>', '<?php bp_loggedin_user_avatar('html=false'); ?>');
        </script>
        <?php
    }

    ob_start();

    foreach ($field_groups as $field_group) {
        if ($field_group->id == $atts['group']) {
        ?>
            <form id="mpp_profile_box" method="post">
                <?php
                foreach ($field_group->fields as $field) {
                    if ($field->alternate_name == 'Telephone #:') $field->type = 'telephone';
                ?>
                    <div class="mpp-profile-field">
                        <?php
                        $visibility_level =  xprofile_get_field_visibility_level($field->id, $member_id);
                        $field_value = in_array($field->type, array("telephone", "url", "email")) ? BP_XProfile_ProfileData::get_value_byid($field->id, $member_id) : xprofile_get_field_data($field->id, $member_id);
                        ?>
                        <div class="mpp-profile-header">
                            <h5><?php echo $field->alternate_name ? $field->alternate_name : $field->name; ?></h5>
                            <a class="mpp-change-visibility mpp-change-visibility-<?php echo $field->id; ?>" href="#" data-field="<?php echo $field->id; ?>" data_user="<?php echo $member_id; ?>" data-visibility="<?php echo $visibility_level; ?>">
                                <span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span>
                            </a>
                        </div>
                        <div class="mpp-profile-body">
                            <div class="mpp-profile-field-description">
                                <?php echo $field->description; ?>
                            </div>
                            <?php
                            mpp_profile_field_html($field, $field_value, $member_id);
                            ?>
                            <span class="mpp-profile-field-visibility"><span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span> <?php echo mpp_profile_field_visibility_label($visibility_level); ?></span>
                            <input type="hidden" class="mpp_visibility_input_value mpp_visibility_<?php echo $field->id; ?>" name="mpp_visibility_<?php echo $field->id; ?>" value="" />
                        </div>
                    </div>
                <?php
                }
                if (get_current_user_id()) {
                ?>
                    <div class="mpp-profile-updating">Updating...</div>
                    <input type="submit" class="button mpp_form_submit_button" value="Download PDF" name="mpp_form_submitted" />
                <?php
                }
                ?>
            </form>
    <?php
        }
    }
    ?>
<?php
    return ob_get_clean();
});

function mpp_profile_field_visibility_label($visibility_level = 'public')
{
    $label = 'Public';
    switch ($visibility_level) {
        case 'adminsonly':
            $label = 'Only Me';
            break;
        case 'loggedin':
            $label = 'All Members';
            break;
        case 'friends':
            $label = 'My Connections';
            break;
        case 'public':
            $label = 'Public';
            break;
    }
    return $label;
}

function get_mpp_visibolity_icon($visibility = 'public')
{
    $icon = 'bb-icon-globe';
    switch ($visibility) {
        case 'public':
            $icon = 'bb-icon-globe';
            break;
        case 'loggedin':
            $icon = 'bb-icon-all-members';
            break;
        case 'friends':
            $icon = 'bb-icon-connections';
            break;
        case 'adminsonly':
            $icon = 'bb-icon-lock';
            break;
    }
    return $icon;
}

// JAVASCRIPT

add_action('wp_footer', function () {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            $('.mpp-change-visibility').on('click', async function(e) {
                e.preventDefault();
                var field = $(this).data('field');
                var visibility = $(this).data('visibility');
                var list_html = '';

                const visibility_types = {
                    public: 'Public',
                    loggedin: 'All Members',
                    friends: 'My Connections',
                    adminsonly: 'Only Me'
                };

                Object.entries(visibility_types).forEach(([key, val]) => {
                    const mpp_icon_hide = (key == visibility) ? '' : 'mpp-icon-hide';
                    list_html += '<li class="' + key + '" data-field="' + field + '" data-visibility="' + key + '" data-title="' + val + '"><div class="mpp-option-title"><span class="mpp-icon ' + get_mpp_visibolity_icon(key) + '"></span> <span>' + val + '</span></div><span class="bb-icon-check ' + mpp_icon_hide + '"></span></li>';
                });

                Swal.fire({
                    title: 'Visibility',
                    html: '<ul class="mpp_visibility_list">' + list_html + '</ul>',
                    showConfirmButton: false
                });
            });

            $(document).on('click', '.mpp_visibility_list li', function() {
                var mpp_visibility_list = $(this).parents('.mpp_visibility_list');
                var field = $(this).data('field');
                mpp_visibility_list.find('.bb-icon-check').addClass('mpp-icon-hide');
                $(this).find('.bb-icon-check').removeClass('mpp-icon-hide');
                var icon = get_mpp_visibolity_icon($(this).data('visibility'));
                //SET DATA
                $(".mpp_visibility_" + field).val($(this).data('visibility'));
                $(".mpp_visibility_" + field).siblings('.mpp-profile-field-visibility').html('<span class="mpp-icon ' + icon + '"></span> ' + $(this).data('title'));

                $(".mpp-change-visibility-" + field).data('visibility', $(this).data('visibility'));
                $(".mpp-change-visibility-" + field).html('<span class="mpp-icon ' + icon + '"></span>');
                Swal.close();
            });

            function get_mpp_visibolity_icon(visibility) {
                var icon = 'bb-icon-globe';
                switch (visibility) {
                    case 'public':
                        icon = 'bb-icon-globe';
                        break;
                    case 'loggedin':
                        icon = 'bb-icon-all-members';
                        break;
                    case 'friends':
                        icon = 'bb-icon-connections';
                        break;
                    case 'adminsonly':
                        icon = 'bb-icon-lock';
                        break;
                }
                return icon;
            }

        });
    </script>
    <?php
});

function mpp_profile_field_html($field, $field_value, $member_id)
{
    $required = $field->is_required ? 'required' : '';
    switch ($field->type) {
        case 'selectbox':
            $field_options = xprofile_get_field($field->id, $member_id);
            $options = $field_options->get_children();
    ?>
            <div><select name="mpp_profile_box[<?php echo $field->id; ?>]" data-field="<?php echo $field->id; ?>" class="mpp-profile-field-html" <?php echo $required; ?>>
                    <?php foreach ($options as $option) : ?>
                        <option value="<?php echo $option->name; ?>" <?php selected($field_value, $option->name, true); ?>><?php echo $option->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php
            break;
        case 'gender':
            $field_options = xprofile_get_field($field->id, $member_id);
            $options = $field_options->get_children();
        ?>
            <div><select name="mpp_profile_box[<?php echo $field->id; ?>]" data-field="<?php echo $field->id; ?>" class="mpp-profile-field-html" <?php echo $required; ?>>
                    <?php foreach ($options as $option) : ?>
                        <option value="<?php echo $option->name; ?>" <?php selected($field_value, $option->name, true); ?>><?php echo $option->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php
            break;
        case 'radio':
            $field_options = xprofile_get_field($field->id, $member_id);
            $options = $field_options->get_children();
        ?>
            <div class="input-options radio-button-options mpp-input-radio-button">
                <?php foreach ($options as $option) : ?>
                    <div class="bp-radio-wrap">
                        <input type="radio" name="mpp_profile_box[<?php echo $field->id; ?>]" id="option_<?php echo $option->id; ?>" value="<?php echo $option->name; ?>" class="bs-styled-radio" <?php checked($field_value, $option->name, true); ?> />
                        <label for="option_<?php echo $option->id; ?>" class="option-label"><?php echo $option->name; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
            break;
        case 'checkbox':
            $field_options = xprofile_get_field($field->id, $member_id);
            $options = $field_options->get_children();
        ?>
            <div class="input-options radio-button-options mpp-input-radio-button">
                <?php foreach ($options as $option) : ?>
                    <?php
                    $checked = '';
                    if (in_array($option->name, $field_value)) $checked = 'checked="checked"';
                    ?>
                    <div class="bp-radio-wrap">
                        <input type="checkbox" name="mpp_profile_box[<?php echo $field->id; ?>][]" id="option_<?php echo $option->id; ?>" value="<?php echo $option->name; ?>" class="bs-styled-checkbox" <?php echo $checked; ?> <?php echo $required; ?> />
                        <label for="option_<?php echo $option->id; ?>" class="option-label"><?php echo $option->name; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
            break;
        case 'textbox':
        ?>
            <div><input class="mpp-profile-field-html" type="text" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
        <?php
            break;
        case 'textarea':
        ?>
            <div><textarea class="mpp-profile-field-html" type="text" name="mpp_profile_box[<?php echo $field->id; ?>]" data-field="<?php echo $field->id; ?>" cols="50" <?php echo $required; ?>><?php echo $field_value; ?></textarea></div>
        <?php
            break;
        case 'email':
        ?>
            <div><input class="mpp-profile-field-html" type="email" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
        <?php
            break;
        case 'datebox':
            $datetime = strtotime($field_value);
            $field_value = date('Y-m-d', $datetime);
        ?>
            <div class="mpp-field-datebox"><?php echo $field_value; ?></div>
            <div><input class="mpp-profile-field-html" type="date" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
        <?php
            break;
        case 'telephone':
        ?>
            <div><input class="mpp-profile-field-html" type="tel" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" pattern="[(][0-9]{3}[)] [0-9]{3}-[0-9]{4}" placeholder="(###) ###-####" <?php echo $required; ?> /></div>
        <?php
            break;
        case 'url':
        ?>
            <div><input class="mpp-profile-field-html" type="url" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
        <?php
            break;
        case 'number':
        ?>
            <div><input class="mpp-profile-field-html" type="number" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
            <?php
            break;
        case 'socialnetworks':
            $field_options = xprofile_get_field($field->id, $member_id);
            $options = $field_options->get_children();
            if (count($options) > 0) {
                foreach ($options as $key => $option) {
            ?>
                    <label class="mpp_social_label"><?php echo $option->name; ?></label>
                    <div><input class="mpp-profile-field-html" type="text" name="mpp_profile_box[<?php echo $field->id; ?>][<?php echo $option->name; ?>]" value="<?php echo $field_value[$key]; ?>" data-field="<?php echo $field->id; ?>" /></div>
            <?php
                }
            }
            break;
        default:
            ?>
            <div><input class="mpp-profile-field-html" type="text" name="mpp_profile_box[<?php echo $field->id; ?>]" value="<?php echo $field_value; ?>" data-field="<?php echo $field->id; ?>" <?php echo $required; ?> /></div>
        <?php
            break;
    }
}

add_shortcode('bb-user-field-group', function ($atts) {
    $atts = shortcode_atts(array(
        'group' => '1',
        'title' => 'Details'
    ), $atts);

    $member_id = bbp_get_user_id();
    $field_groups = bp_profile_get_field_groups();

    ob_start();
    if (get_current_user_id()) {
        echo do_shortcode('[bb-user-field-input group=' . $atts['group'] . ']');
        ?>
        <script type="text/javascript">
            //window.location.replace("<?php echo bbp_get_user_profile_url(); ?>/profile/edit/group/<?php echo $atts['group']; ?>/");
        </script>
        <?php
    } else {
        foreach ($field_groups as $field_group) {
            if ($field_group->id == $atts['group']) {
                foreach ($field_group->fields as $field) {
                    $field_value = bp_get_profile_field_data(array('user_id' => $member_id, 'field' => $field->id));
        ?>
                    <h5><?php echo $field->name; ?></h5>
                    <?php
                    if ($field_value && !empty($field_value)) {
                    ?>
                        <p><?php echo $field_value; ?></p>
                    <?php
                    } else {
                    ?>
                        <p>-</p>
    <?php
                    }
                }
            }
        }
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            console.log(10);
            $('body').addClass('mpp-custom-profile-fields');
        });
    </script>
    <?php
    return ob_get_clean();
});

// STYLE FOR IOS AND ANDROID

add_action('wp_head', function () {
    if (mpp_is_android_or_ios()) {
    ?>
        <style>
            .bbp-user-page .profile-header,
            .bbp-user-page .bp-profile-wrapper .bp-subnavs,
            .bbp-user-page .bp-profile-wrapper .button-tabs {
                display: none
            }

            .mpp-custom-profile-fields.bbp-user-page .bp-profile-wrapper,
            .mpp-custom-profile-fields.bbp-user-page .site-content {
                background-color: #6ec1e4
            }

            .bbp-user-page .bs-bp-container {
                padding: 0
            }

            .bp-profile-content {
                padding: 0
            }

            #buddypress #profile-edit-form fieldset {
                margin-bottom: 10px;
            }

            #buddypress #profile-edit-form legend:not(.bp-screen-reader-text) {
                text-transform: uppercase;
                color: #000;
            }

            #buddypress #profile-edit-form input[type=text] {
                border-radius: 5px;
            }

            .bbp-user-page .bp-profile-wrapper {
                border: none;
            }
        </style>
    <?php
    }
});

// PET PDF UPLOAD

add_shortcode('mpp_submit_pdf', function () {
    $uploaded = false;
    $file_type_error = false;
    if (isset($_POST['submit_pdf'])) {
        if (!empty($_FILES)) {
            foreach ($_FILES as $file) {
                if (is_array($file)) {
                    $allowed = array('pdf', 'docx', 'doc');
                    $filename = $file['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $file_type_error = true;
                    } else {
                        $attachment_id = mpp_upload_user_file($file);
                        if ($attachment_id) {
                            $first_name = xprofile_get_field_data(1, get_current_user_id());
                            $last_name = xprofile_get_field_data(2, get_current_user_id());

                            $doc = bp_document_add(
                                array(
                                    'attachment_id' => $attachment_id,
                                    'group_id' => bp_get_current_group_id(),
                                    'blog_id' => 1,
                                    'title' => 'Pet-Profile-' . $first_name . '-' . $last_name,
                                )
                            );

                            if ($doc) {
                                bp_document_update_meta($doc, 'file_name', 'Pet-Profile-' . $first_name . '-' . $last_name);
                                bp_document_update_meta($doc, 'extension', 'pdf');
                                $uploaded = true;
                            }
                        }
                    }
                }
            }
        }
    }
    ob_start();
    if ($uploaded) {
    ?>
        <p>Your pet profile has been updated successfully.</p>
    <?php
    }
    if ($file_type_error) {
    ?>
        <p>Please upload allowed file type (.pdf, .doc, .docx).</p>
    <?php
    }
    ?>
    <form id="mpp_submit_pdf" method="post" enctype="multipart/form-data">
        <label for="myfile">Select a file:</label><br>
        <input type="file" id="myfile" name="myfile">
        <br><br>
        <input type="submit" name="submit_pdf" value="Upload" class="button" />
    </form>
    <?php
    return ob_get_clean();
});

function mpp_upload_user_file($file = array())
{
    require_once(ABSPATH . 'wp-admin/includes/admin.php');
    $file_return = wp_handle_upload($file, array('test_form' => false));
    if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
        return false;
    } else {
        $filename = $file_return['file'];
        $attachment = array(
            'post_mime_type' => $file_return['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $file_return['url']
        );
        $attachment_id = wp_insert_attachment($attachment, $file_return['url']);
        //require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once ABSPATH . 'wp-admin' . '/includes/image.php';
        require_once ABSPATH . 'wp-admin' . '/includes/file.php';
        require_once ABSPATH . 'wp-admin' . '/includes/media.php';
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        if (0 < intval($attachment_id)) {
            return $attachment_id;
        }
    }
    return false;
}

function mpp_remove_group_documents_tab()
{
    if (!groups_is_user_admin(get_current_user_id(), bp_get_current_group_id()))
        bp_core_remove_subnav_item(bp_get_current_group_slug(), 'documents');
}
add_action('bp_setup_nav', 'mpp_remove_group_documents_tab', 15);

// MPP QnA PDF Generator
add_shortcode('mpp-qna-pdf-gen', function () {
    $member_id = bbp_get_user_id();
    $field_info = array();
    $field_groups = bp_profile_get_field_groups();

    if (isset($_POST['mpp_form_submitted']) && !empty($_POST['mpp_form_submitted'])) {
        if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
            $profile_fields = $_POST['mpp_profile_box'];
            foreach ($profile_fields as $field_id => $field_value) {
                $field_options = xprofile_get_field($field_id, $member_id);
                if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';
                xprofile_set_field_data($field_id, $member_id, $field_value);
                $field_info[$field_id]['key'] = $field_options->description ? $field_options->description : $field_options->name;
                $field_info[$field_id]['value'] = $field_value;
            }
    ?>
            <script type="text/javascript">
                startQnaProcessing('<?php echo json_encode($field_info); ?>', '<?php bp_loggedin_user_avatar('html=false'); ?>');
            </script>
        <?php
        }
    }
    $field_description = array();
    ob_start();
    foreach ($field_groups as $field_group) {
        if ($field_group->id == 6) {

            //e_var_dump($field_group->fields);
        ?>
            <form id="mpp_profile_box" method="post">
                <?php
                foreach ($field_group->fields as $field) {
                    $field_description['mpp_profile_box[' . $field->id . ']'] = $field->description ? $field->description : $field->name;
                    if (get_current_user_id() == $member_id) {
                        $field_value = in_array($field->type, array("telephone", "url", "email")) ? BP_XProfile_ProfileData::get_value_byid($field->id, $member_id) : xprofile_get_field_data($field->id, $member_id);
                ?>
                        <h5><?php echo $field->description ? $field->description : $field->name; ?></h5>
                    <?php
                        mpp_profile_field_html($field, $field_value, $member_id);
                    } else {
                        $field_value = bp_get_profile_field_data(array('user_id' => $member_id, 'field' => $field->id));
                    ?>
                        <h5><?php echo $field->name; ?></h5>
                        <?php
                        if ($field_value && !empty($field_value)) {
                        ?>
                            <p><?php echo $field_value; ?></p>
                        <?php
                        } else {
                        ?>
                            <p>-</p>
                    <?php
                        }
                    }
                }
                if (get_current_user_id() == $member_id) {
                    ?>
                    <input type="submit" class="button" value="Update" name="mpp_form_submitted" id="mpp_form_submitted" data-description='<?php echo json_encode($field_description); ?>' data-avatar="<?php bp_loggedin_user_avatar('html=false'); ?>" />
                <?php
                }
                ?>
            </form>
    <?php
        }
    }
    return ob_get_clean();
});

add_action('wp_footer', function () {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            $("#mpp_form_submitted").on('click', function(e) {
                e.preventDefault();
                var field_description = $(this).attr('data-description');
                var avatar_image = $(this).attr('data-avatar');
                var fieldset = {};
                field_description = JSON.parse(field_description);
                var formdata = $('#mpp_profile_box').serializeArray().reduce(function(obj, item) {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
                $.each(formdata, function(key, value) {
                    fieldset[key] = {
                        label: field_description[key],
                        data: value
                    };
                });
                createQnaPdfNew(fieldset, avatar_image);
            });

            async function createQnaPdfNew(info, pp) {
                // Create a new PDFDocument
                const pdfDoc = await PDFDocument.create();

                // Embed the Times Roman font
                const timesRomanFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

                // Add a blank page to the document
                const page = pdfDoc.addPage();
                const page2 = pdfDoc.addPage();
                //const page2 = pdfDoc.addPage();

                // Get the width and height of the page
                const {
                    width,
                    height
                } = page.getSize();

                // Image Handling
                //https://communityportal.mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png
                const emblemUrl =
                    "https://mypetsprofile.com/wp-content/uploads/2020/06/MPP-Transparent-logo.png";
                const emblemImageBytes = await fetch(emblemUrl).then((res) =>
                    res.arrayBuffer()
                );
                const emblemImage = await pdfDoc.embedPng(emblemImageBytes);
                const pngDims = emblemImage.scale(0.5);
                // Draw the PNG image near the lower right corner of the JPG image
                page.drawImage(emblemImage, {
                    x: page.getWidth() / 2 - 50,
                    y: height - 70,
                    width: 100,
                    height: (pngDims.height / pngDims.width) * 100,
                });

                // USER AVATAR
                const avatarUrl = pp.replace('cdn.', '');
                console.log(avatarUrl);
                const avatarImageBytes = await fetch(avatarUrl).then((res) =>
                    res.arrayBuffer()
                );
                const avatarImage = await pdfDoc.embedJpg(avatarImageBytes);
                // Draw the PNG image near the lower right corner of the JPG image
                page.drawImage(avatarImage, {
                    x: page.getWidth() - 150,
                    y: height - 200,
                    width: 100,
                    height: 100,
                });

                // Draw a string of text toward the top of the page
                const fontSize = 12;

                page.drawText("Pet Community Q and A", {
                    x: 50,
                    y: height - 120,
                    size: 14,
                    font: timesRomanFont,
                    color: rgb(0, 0.53, 0.71),
                });

                var field_height = 150;
                var text_length = 0;
                var cpage = page;

                for (const prop in info) {
                    var field = info[prop];
                    //console.log(field.key);
                    //console.log(field.value);
                    if (field_height > 700) {
                        field_height = 100;
                        cpage = page2;
                    }
                    cpage.drawText(field.label, {
                        x: 50,
                        y: height - field_height,
                        size: fontSize,
                        font: timesRomanFont,
                        color: rgb(0.6, 0.6, 0.6),
                        maxWidth: width - 100,
                        lineHeight: 14,
                    });

                    text_length = field.label.length;
                    console.log(text_length / 100);
                    field_height += 20 + Math.floor(text_length / 100) * 12;
                    //console.log(Math.ceil(text_length / 100) * 10);

                    cpage.drawText(field.data, {
                        x: 50,
                        y: height - field_height,
                        size: fontSize,
                        font: timesRomanFont,
                        color: rgb(0, 0, 0),
                        maxWidth: width - 100,
                        lineHeight: 14,
                    });

                    field_height += 20;
                }

                // Serialize the PDFDocument to bytes (a Uint8Array)
                const pdfBytes = await pdfDoc.save();

                //console.log(pdfBytes);
                // Trigger the browser to download the PDF document
                download(pdfBytes, "pet-community-qna.pdf", "application/pdf");
                // });
            }
        });
    </script>
    <?php
});


// Auto coupon apply
function mpp_after_applied_a_coupon($coupon_code)
{
    if ('adminpromo2022' === $coupon_code) {
        WC()->cart->apply_coupon('adminpromo2022rc');
    }
    //PooPrints2022
    if ('pooprints2022' === $coupon_code) {
        WC()->cart->apply_coupon('pooprints2022rc');
    }
    //90daysfreetrial
    if ('90daytrial' === $coupon_code) {
        WC()->cart->apply_coupon('90daytrialrc');
    }
}
add_action('woocommerce_applied_coupon', 'mpp_after_applied_a_coupon');


/*
add_action('init', function(){
	$listing_id = 20067;
	$group_id = 12369;
	update_post_meta($listing_id, '_bb_group_id', $group_id);
    groups_update_groupmeta($group_id, 'directorist_listings_enabled', 1);
    groups_update_groupmeta($group_id, 'directorist_listings_ids', array($listing_id));
});
*/


add_action('wp_head', function () {
    if (bp_is_user()) :
    ?>
        <style>
            .single-product div.product div.images img {
                padding: 50px;
            }

            .directorist-claim-listing__description li {
                font-size: 15px;
            }

            #mpp_profile_box .bs-styled-radio:checked+label:after {
                top: 14px !important;
                left: 14px;
            }

            #mpp_profile_box .bs-styled-checkbox:checked+label:after {
                top: 13px;
                left: 13px;
            }

            .post-type-archive-product .woocommerce-notices-wrapper {
                display: none;
            }

            /* MPP Profile Style */
            .mpp-profile-header {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                padding: 10px 0;
                margin-top: 10px;
            }

            .mpp-profile-header h5 {
                font-size: 18px;
                text-transform: capitalize;
                margin: 0px !important;
            }

            .mpp-profile-header .mpp-icon {
                font-size: 20px;
            }

            .mpp-profile-field-html {
                margin: 0 !important;
                width: 100%;
            }

            .mpp-profile-field-visibility {
                font-size: 12px;
            }

            .mpp-field-datebox {
                font-size: 12px;
            }

            .mpp_form_submit_button {
                margin: 20px 0;
            }

            .mpp-input-radio-button .option-label {
                font-size: 16px;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                padding: 10px;
                border-bottom: 1px solid #444;
            }

            .mpp-input-radio-button .bp-radio-wrap:nth-last-child(1) .option-label {
                border: none;
            }

            .mpp-change-visibility,
            .mpp-change-visibility:active,
            .mpp-change-visibility:focus {
                color: #4d5c6d;
            }

            .mpp_visibility_list {
                margin: 0;
                padding: 0;
                list-style: none;
                text-align: left;
            }

            .mpp_visibility_list li {
                padding: 10px 0;
                border-bottom: 1px solid #eee;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .mpp-option-title {
                display: flex;
                align-items: center;
            }

            .mpp-option-title span.mpp-icon {
                font-size: 16px;
                margin-right: 10px;
            }

            .mpp-icon-hide {
                display: none;
            }

            .mpp_visibility_list .bb-icon-check {
                color: green;
                font-size: 20px;
            }

            .mpp-profile-field-description {
                font-size: 14px;
                line-height: 18px;
                margin-bottom: 15px;
            }

            .mpp-profile-updating {
                margin-top: 20px;
                display: none;
            }

            .mpp_social_label {
                text-transform: capitalize;
                font-size: 14px;
                margin-bottom: 10px;
            }

            .mpp-address-field {
                margin-bottom: 10px
            }

            .mpp_dna_form_submitted {
                margin-top: 20px;
            }
        </style>
    <?php
    endif;
});

// POPUP SETTINGS STARTS
add_action('wp_footer', function () {
    if (!is_user_logged_in()) :
    ?>
        <div id="contact-us-float-button">
            <a href="#" id="contact-form-open">
                <span class="bb-icon-envelope mpp-contact-icon"></span>
            </a>
        </div>
    <?php
    endif;
});

add_action('wp_head', function () {
    ?>
    <style>
        #contact-us-float-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #e75126;
            border-radius: 50%;
        }

        #contact-us-float-button a {
            padding: 10px;
        }

        #contact-us-float-button .mpp-contact-icon {
            font-size: 2rem;
            color: #fff;
            line-height: 3.25rem;
        }
    </style>
    <?php
});
// POPUP SETTINGS ENDS

// Change State with Country
add_action('wp_footer', function () {
    if (bp_is_user()) :
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var $el = $("#mpp_state_field");
                var states = <?php echo json_encode(WC()->countries->states); ?>;
                $('#mpp_country_field').on('change', function() {
                    $el.empty(); // remove old options
                    var country = $(this).val();
                    if (states[country] != undefined && Object.keys(states[country]).length > 0) {
                        $.each(states[country], function(key, value) {
                            $el.append($("<option></option>")
                                .attr("value", key).text(value));
                        });
                    }
                });
            });
        </script>
    <?php
    endif;
});


// WC ORDER PROCESSING
add_action('atbdp_after_created_listing', function ($listing_id = 0) {
    $args = [
        'post_type'   => 'shop_order',
        'post_status' => ["wc-completed"],
        'numberposts' => -1,
        'meta_query'  => [
            'relation' => 'AND',
            [
                'key'     => '_customer_user',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ],
            [
                'relation' => 'OR',
                [
                    'key'     => '_listing_id',
                    'value'   => '0',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ],
    ];

    $active_orders = new WP_Query($args);

    while ($active_orders->have_posts()) : $active_orders->the_post();
        $order = wc_get_order(get_the_ID());
        foreach ($order->get_items() as $item_key => $item) :
            $item_id = $item->get_product_id();
            // Exceptions
            if ($item_id == 20139) {
                update_post_meta(get_the_ID(), '_listing_id', $listing_id);
            }
        endforeach;
    endwhile;

    return false;
});

// Contact Form Email Confirmation
function mpp_dev_process_entry_save($fields, $entry, $form_id, $form_data)
{
    if ($form_id != 20106) return;

    $user_name = $fields[1]['value'];
    $user_email = $fields[2]['value'];

    $html = '';
    $html .= '<p>Hello ' . $user_name . '</p>';
    $html .= '<p>Thank you for your MyPetsProfle™️ inquiry. We’ll have a representative reach out to you shortly.</p>';
    $html .= '<p>Best Regards</p>';
    $html .= '<p>MyPetsProfile™️ Team</br>';
    $html .= 'Hello@MyPetsProfile.com</p>';

    // SEND EMAIL
    $to = $user_email;
    $subject = 'MyPetsProfile - Inquiry Received';
    $body = $html;
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $body, $headers);
}
add_action('wpforms_process_entry_save', 'mpp_dev_process_entry_save', 10, 4);


// CHECK IF THE USER IS ADMIN
function mpp_is_group_admin()
{
    $membership = groups_is_user_admin(get_current_user_id(), bp_get_current_group_id());
    if ($membership) {
        return 'admin';
    } else {
        return 'non-admin';
    }
}

// Custom API



// CUSTOM LISTING QUERIES

add_filter('atbdp_search_listings_meta_queries', 'mpp_directorist_remove_directory_type');
add_filter('atbdp_all_listings_meta_queries', 'mpp_directorist_remove_directory_type');

function mpp_directorist_remove_directory_type($args)
{
    /*
    if (isset($args['directory_type']) && !in_array(1418, $args['directory_type'])) {
        $args['directory_type'] = array(
            'key' => '_directory_type',
            'value' => array(200, 1414),
            'compare' => 'IN'
        );
    }
    */
    /*
    if (isset($args['directory_type'])) {
        $args['directory_type'] = array(
            'key' => '_directory_type',
            'value' => array(200, 1414),
            'compare' => 'IN'
        );
    }
    */
    if (bp_get_current_group_id()) :
        $args['bb_group'] = array(
            'key' => '_bb_group_id',
            'value' => bp_get_current_group_id(),
            'compare' => '='
        );
    endif;
    return $args;
}

// CUSTOM LISTING QUERIES

// BOOKING

add_action('wp_footer', function () {
    if (is_singular('at_biz_dir')) :
    ?>
        <script type="text/javascript">
            jQuery('.booking-content').prepend("<p>Welcome, please fill-out a convenient date and time you’re available, and click Request a Booking. Or send us an email directly.</p>");
        </script>
    <?php
    endif;
});

add_action('wp_footer', function () {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {

            $('body').on('click', 'a.directorist-book-now', function(e) {
                e.preventDefault();
                if ($(this).hasClass('loading')) return;
                var listing_id = $('#listing_id').val();
                if (!listing_id) return;
                var listing_name = $('.directorist-listing-details__listing-title').text();
                const startDate = moment(
                    $("#date-picker").data("daterangepicker").startDate,
                    ["MM/DD/YYYY"]
                ).format("YYYY-MM-DD");
                if (!startDate || startDate == '') return;
                const slot = $("input#slot").val();
                if (!slot || slot == '') return;
                const adults = $(".adults").text();

                const booking_data = {
                    listing_id: listing_id,
                    listing_name: listing_name,
                    start_date: startDate,
                    slot: slot,
                    adults: adults
                };

                var slotText = '';
                if (slot != '') {
                    slotText = JSON.parse(slot);
                }

                var book_html = '';
                book_html += '<div class="mpp-booking-details">';
                if (listing_name != '') book_html += '<b>' + listing_name + '</b></br>';
                if (startDate != '') book_html += startDate + '</br>';
                if (slotText != '') book_html += slotText[0] + '</br>';
                if (adults != '' && adults != 'Guest') book_html += adults + '</br>';
                book_html += '</div>';
                book_html += '<h4>Personal Detail</h4>';
                book_html += '<div class="booking-form-popup">';
                book_html += "<div class='booking-field'><input type='hidden' value='" + JSON.stringify(booking_data) + "' id='booking_data'/></div>";
                book_html += '<div class="booking-field"><label>Full Name*</label><input type="text" value="" id="booking_name"/></div>';
                book_html += '<div class="booking-field"><label>Email Address*</label><input type="email" value="" id="booking_email"/></div>';
                book_html += '<div class="booking-field"><label>Phone</label><input type="tel" value="" id="booking_phone"/></div>';
                book_html += '<div class="booking-field"><label>Message</label><textarea id="booking_message"></textarea></div>';
                book_html += '<div class="booking-field"><a href="#" class="button" id="booking-form-submit">Submit</a></div>';
                book_html += '</div>';

                Swal.fire({
                    title: 'Booking',
                    html: '<div class="mpp_booking_confirmation">' + book_html + '</div>',
                    showConfirmButton: false,
                    showCloseButton: true,
                });
            });

            $('body').on('click', 'a#booking-form-submit', function(e) {
                e.preventDefault();
                if ($(this).hasClass('loading')) return;

                var $this = $(this);
                var ajax_data = {};
                ajax_data.booking_data = $('.booking-form-popup #booking_data').val();
                ajax_data.booking_name = $('.booking-form-popup #booking_name').val();
                ajax_data.booking_email = $('.booking-form-popup #booking_email').val();
                ajax_data.booking_phone = $('.booking-form-popup #booking_phone').val();
                ajax_data.booking_message = $('.booking-form-popup #booking_message').val();

                if (!ajax_data.booking_name || ajax_data.booking_name == '') return;
                if (!ajax_data.booking_email || ajax_data.booking_email == '') return;
                if (!mppValidateEmail(ajax_data.booking_email)) return;

                $(this).addClass('loading');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: bdb_booking.ajax_url,
                    data: {
                        action: "guest_booking_mpp",
                        booking_data: ajax_data,
                    },
                    success(data) {
                        if (data.booking) {
                            Swal.fire({
                                icon: 'success',
                                text: 'Your booking request has been submitted successfully!'
                            });
                        } else {
                            console.log("failed!");
                        }
                    },
                    complete() {
                        $this.removeClass('loading');
                    }
                });
            });

            //Email Validation
            const mppValidateEmail = (email) => {
                return email.match(
                    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
            };
        });
    </script>
<?php
});

// AJAX CALL
add_action("wp_ajax_guest_booking_mpp", "guest_booking_mpp");
add_action("wp_ajax_nopriv_guest_booking_mppe", "guest_booking_mpp");

function guest_booking_mpp()
{
    $result = array();
    $form_data = isset($_REQUEST['booking_data']) ? $_REQUEST['booking_data'] : array();
    $booking_data = json_decode(wp_unslash($form_data['booking_data']));

    // PROCCESSING

    // SLOTS
    $slot = json_decode($booking_data->slot);
    $hours = explode('-', $slot[0]);
    $hour_start = date("H:i:s", strtotime($hours[0]));
    $hour_end = date("H:i:s", strtotime($hours[1]));
    $start_date = $booking_data->start_data;

    $price = Directorist_Booking_Database::calculate_price($booking_data->listing_id, $start_date, $start_date);

    // INSERT INTO DB
    $booking_id = Directorist_Booking_Database::insert_booking(
        array(
            'owner_id' => get_current_user_id(),
            'listing_id' => $booking_data->listing_id,
            'date_start' => $start_date . ' ' . $hour_start,
            'date_end' => $start_date . ' ' . $hour_end,
            'comment' => json_encode(array(
                'first_name' => $form_data['booking_name'],
                //'last_name' => $_POST['lastname'],
                'email' => $form_data['booking_email'],
                'phone' => $form_data['booking_phone'],
                //'childrens' => $data['childrens'],
                'adults' => !empty($booking_data->adults) ? $booking_data->adults : 0,
                'message' => $form_data['booking_message'],
                //'service' => $comment_services,
            )),
            'type' => 'reservation',
            'price' => $price,
        )
    );


    $status = apply_filters('bdb_service_slots_default_status', 'waiting');
    if (!empty($instant_booking)) {
        $status = 'confirmed';
    }

    $changed_status = Directorist_Booking_Database::set_booking_status($booking_id, $status);
    // INSERT INTO DB

    // PROCCESSING
    $result['booking'] = array($booking_id);

    echo json_encode($result);
    die();
}
// AJAX CALL

// BOOKING

// MPP LISTINGS SHORTCODE

add_shortcode('mpp-biz-listings', function () {
    ob_start();
    if (bp_get_current_group_id()) :
        $listings_ids = dbb_get_group_connected_listings_ids(bp_get_current_group_id());
        echo do_shortcode('[directorist_all_listing advanced_filter="no"]');
    endif;
    return ob_get_clean();
});

// Co Author Plugin
add_filter('coauthors_edit_author_cap', function ($cap) {
    return 'read';
});


// ALLOW USER TO EDIT FORM TEMP
/*
add_action('wp_head', function () {
    if (atbdp_is_page('add_listing')) {
        //to add capability to user
        $user = new WP_User(get_current_user_id());
        // Listing ID
        $url = $_SERVER['REQUEST_URI'];
        $pattern = "/edit\/(\d+)/i";
        $listing_id = preg_match($pattern, $url, $matches) ? (int) $matches[1] : '';

        // Check Co Authors
        $author_list = array();
        if (is_plugin_active('co-authors-plus/co-authors-plus.php')) {
            $coauthors = get_coauthors($listing_id);
            foreach ($coauthors as $authorInfo) {
                $author_list[] = $authorInfo->ID;
            }
        } else {
            $author_list[] = $listing->author_id;
        }
        if (!current_user_can('administrator')) {
            if (in_array(get_current_user_id(), $author_list)) {
                $user->add_cap('edit_others_at_biz_dirs');
            } else {
                $user->remove_cap('edit_others_at_biz_dirs');
            }
        }
    }
});
*/
// ALLOW USER TO EDIT FORM TEMP

// Contact Form Email Confirmation
function mpp_dev_process_entry_save_mailchimp($fields, $entry, $form_id, $form_data)
{

    if ($form_id != 20886) return;

    // ADD EMAIL TO THE LIST
    $option_name = 'mpp_email_list';
    $email_list = get_option($option_name, array());

    $user_email = $fields[1]['value'];
    $email_list[] = $user_email;
    update_option($option_name, $email_list);

    // SEND EMAIL WITH CODE

    $html = '';

    $html .= '<p>Welcome to MyPetsProfile™️</p>';

    $html .= '<p>Your unique promo code is: <b>90dayTrial</b></p>';

    $html .= '<p>Where pet parents and neighbourhood pet business and services meet.</p>';

    $html .= '<p>Your 90 Day Trial begins today.</p>';
    $html .= '<p>Simple go to www.MyPetsProfile.com. Choose the category that best describes your pet business or service.</p>';
    $html .= '<p>Click the icon and fill-out the information to open your business.</p>';
    $html .= '<p>Apply the coupon/promotional code: <b>90dayTrial</b> and you will not be charged anything.</p>';
    $html .= '<p>We’ll send you a notice before your 90 days is up. You may choose to continue or cancel your business portal.</p>';
    $html .= '<p>We’re confident you’ll enjoy and value the platform experience and continue meeting new clients.</p>';

    $html .= '<p>Send us an email directly if you have any questions.</p>';

    $html .= '<p>Enjoy</p>';
    $html .= '<p>The MyPetsProfile™️ Team</p>';
    $html .= '<p>Hello@MyPetsProfile.com</p>';

    // SEND EMAIL
    $to = $user_email;
    $subject = 'MyPetsProfile™️ - 90 Day Trial Coupon';
    $body = $html;
    $headers = array();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'Cc: MyPetsProfile<mypetsprofileapp@gmail.com>';

    wp_mail($to, $subject, $body, $headers);
}
add_action('wpforms_process_entry_save', 'mpp_dev_process_entry_save_mailchimp', 10, 4);

// Woocommerce Remove All Items from Cart before Adding a new one

add_filter('woocommerce_add_cart_item_data', 'mpp_woo_custom_add_to_cart');

function mpp_woo_custom_add_to_cart($cart_item_data)
{

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}

function mpp_facility_option_list($options)
{
    $new_options = array();
    if ($options && count($options) > 0) {
        foreach ($options as $option) {
            //$new_options[$option['option_value']]['class'] = isset($option['option_class']) && !empty($option['option_class']) ? $option['option_class'] : '';
            //$new_options[$option['option_value']]['icon'] = isset($option['option_icon']) && !empty($option['option_icon']) ? $option['option_icon'] : 'las la-check';
            $new_options[$option['option_value']]['label'] = isset($option['option_label']) && !empty($option['option_label']) ? $option['option_label'] : '';
        }
    }
    return $new_options;
}

// APP MENU ISSUE

add_filter('bbapp_app_menu_filter', 'mpp_bbapp_app_menu_filter', 10, 2);

function mpp_bbapp_app_menu_filter($app_menu, $menu_type)
{
    if ($menu_type == 'tabbar') {
        $app_menu[] = array(
            'label' => 'More',
            'icon' =>
            array(
                'uri' => 'bbapp/list',
                'monochrome_setting' => '{\"icon_monochrome_checkbox\":\"yes\",\"monochrome_option\":\"default\",\"icon_monochrome_color\":\"#0e5073\"}',
            ),
            'original' => 'More',
            'id' => '6092b9c98b037',
            'object' => 'more',
            'data' =>
            array(
                'id' => 'more',
                'parent' => '',
            ),
            'type' => 'core',
        );
    }

    return $app_menu;
}

// APP MENU ISSUE

// add_action('init', function(){
//     do_action('after_inserting_referral', 1, 1);
// });


// Function::GET VACANCY OPTION NAME

function mpp_get_vacancy_option_name($key = '', $options = array())
{
    if (!empty($options) && !empty($key)) {
        foreach ($options as $option) {
            if ($option['option_value'] == $key) return $option['option_label'];
        }
    }
    return '';
}

// DIRECTORIST QUERY ARGS

/*
add_filter('atbdp_listing_search_query_argument', function ($args) {
    unset($args['meta_key']);
    unset($args['meta_query']['_featured']);

    $custom_fields = isset($_REQUEST['custom_field']) && !empty($_REQUEST['custom_field']) ? $_REQUEST['custom_field'] : array();

    //e_var_dump($custom_fields);

    if (count($custom_fields) > 0 && isset($custom_fields['custom-category'])) {
        unset($args['meta_query']['directory_type']);
        //e_var_dump($custom_fields);
        if ($custom_fields['custom-category'] == "apartments") {
            $args['meta_query']['directory_type'] = array(
                "key" => "_directory_type",
                "value" => array(1414),
                "compare" => "IN"
            );
        } elseif ($custom_fields['custom-category'] == "backyard") {
            $args['meta_query']['directory_type'] = array(
                "key" => "_directory_type",
                "value" => array(1414),
                "compare" => "IN"
            );
            $args['tax_query'] = array(
                array(
                    'taxonomy' => ATBDP_CATEGORY,
                    'field'    => 'slug',
                    'terms'    => 'backyard-dog-parks',
                ),
            );
        } elseif ($custom_fields['custom-category'] == "condos") {
            $args['meta_query']['directory_type'] = array(
                "key" => "_directory_type",
                "value" => array(1414),
                "compare" => "IN"
            );
            $args['tax_query'] = array(
                array(
                    'taxonomy' => ATBDP_CATEGORY,
                    'field'    => 'slug',
                    'terms'    => 'condos',
                ),
            );
        } elseif ($custom_fields['custom-category'] == "pooprint") {
            $args['meta_query']['directory_type'] = array(
                "key" => "_directory_type",
                "value" => array(1414),
                "compare" => "IN"
            );
            $args['tax_query'] = array(
                array(
                    'taxonomy' => ATBDP_CATEGORY,
                    'field'    => 'slug',
                    'terms'    => 'pooprints-community',
                ),
            );
        } else {
            $args['meta_query']['directory_type'] = array(
                "key" => "_directory_type",
                "value" => array(1445),
                "compare" => "IN"
            );
        }

        if ($args['meta_query'] && count($args['meta_query']) > 0) {
            foreach ($args['meta_query'] as $key => $meta_query) {
                if ($meta_query['key'] == '_custom-category') unset($args['meta_query'][$key]);
            }
        }
    }

    return $args;
});

*/

//atbdp_all_listings_query_arguments

add_filter('atbdp_all_listings_query_arguments', function ($args) {
    unset($args['meta_key']);
    unset($args['meta_query']['_featured']);
    unset($args['meta_query']['directory_type']);
    $args['meta_query']['directory_type'] = array(
        "key" => "_directory_type",
        "value" => array(200, 1418, 1414),
        "compare" => "IN"
    );

    //e_var_dump($args);
    return $args;
});


/**
 * CUSTOM FUNCTION - mpp_listing_directory_type
 */

if (!function_exists('mpp_listing_directory_type')) {
    function mpp_listing_directory_type($listing_id = 0, $return = 'obj')
    {
        if (!$listing_id) return;
        $terms = get_the_terms($listing_id, ATBDP_DIRECTORY_TYPE);
        if ($terms && count($terms) > 0) {
            if ($return == 'obj') return $terms[0];
            if (isset($terms[0]->{$return})) return $terms[0]->{$return};
        }
        return false;
    }
}


/**
 * QRCODE REDIRECT
 */

add_shortcode('mpp-app-qrcode-redirect', function () {
    //Detect special conditions devices
    /*
    $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
    $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
    
    $link = "";
    e_var_dump($_SERVER['HTTP_USER_AGENT']);
    if($iPod || $iPhone || $iPad){
        echo $link = "https://apps.apple.com/us/app/mypetsprofile/id1565456057";
    }else if($android){
        echo $link = "https://play.google.com/store/apps/details?id=com.mypetsprofile.mypetsprofile";
    }else{
        echo $link = "http://mypetsprofile.com";
    }
    */
    ob_start();

?>
    <p>Redirecting to other site...</p>
    <script type="text/javascript">
        var userAgent = window.navigator.userAgent,
            platform = window.navigator?.userAgentData?.platform || window.navigator.platform,
            macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
            windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
            iosPlatforms = ['iPhone', 'iPad', 'iPod'],
            link = "https://mypetsprofile.com";

        if (macosPlatforms.indexOf(platform) !== -1) {
            link = 'https://apps.apple.com/us/app/mypetsprofile/id1565456057';
        } else if (iosPlatforms.indexOf(platform) !== -1) {
            link = 'https://apps.apple.com/us/app/mypetsprofile/id1565456057';
        } else if (windowsPlatforms.indexOf(platform) !== -1) {
            link = 'https://mypetsprofile.com';
        } else if (/Android/.test(userAgent)) {
            link = 'https://play.google.com/store/apps/details?id=com.mypetsprofile.mypetsprofile';
        } else if (/Linux/.test(platform)) {
            link = 'https://mypetsprofile.com';
        }
        window.location = link;
    </script>
<?php
    return ob_get_clean();
});
