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

function bbd_inspect_scripts()
{
    global $wp_scripts;
    wp_dequeue_script('directorist-global-script');
}
//add_action('wp_print_scripts', 'bbd_inspect_scripts');

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
        $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $item_id;
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
            ],
        ],
    ];

    $active_orders = new WP_Query($args);

    while ($active_orders->have_posts()) : $active_orders->the_post();
        $order = wc_get_order(get_the_ID());
        foreach ($order->get_items() as $item_key => $item) :
            $item_id = $item->get_product_id();
            $plan_id = get_post_meta($item_id, '_linked_pricing_plan', true) ? get_post_meta($item_id, '_linked_pricing_plan', true) : $item_id;
            if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans' && !in_array($plan_id, array(18059, 18242)))  return $plan_id;
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
    if ($active_plan) :
        $url = MPP_SITE_URL . '/add-listing/?directory_type=' . default_directory_type() . '&plan=' . $active_plan;
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

/* add_filter('directorist_custom_field_meta_key_field_args', function ($args) {
    $args['type'] = 'text';
    return $args;
}); */

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

function mpp_insert_fb_in_head()
{
    echo '<meta name="image" property="og:image" content="https://cdn.mypetsprofile.com/wp-content/uploads/2022/04/16144822/featured-image1-1.png"/>';
}
add_action('wp_head', 'mpp_insert_fb_in_head', 2);


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

// add_action('wp_footer', function () {
//     e_var_dump($_COOKIE);
// });


add_action('wp_footer', function () {
    if (!is_page('test-shortcode') && !is_page(19392)) return;
    $multiple_recipients = array(
        'recipient1@example.com',
        'recipient2@foo.example.com'
    );
    $subj = 'The email subject';
    $body = 'This is the body of the email';

    require(get_stylesheet_directory() . '/assets/fpdf/fpdf.php');
    $pdf = new FPDF('P', 'pt', array(500, 233));
    // $pdf->AddFont('Georgiai', '', 'georgiai.php');
    $pdf->AddPage();
    //$pdf->Image(get_stylesheet_directory() . '/assets/fpdf/image.jpg', 0, 0, 500);
    //$pdf->SetFont('georgiai', '', 16);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Hello World!');

    // email stuff (change data below)
    $to = "a_mahfuzul@yahoo.com";
    $from = "a_mahfuzul@yahoo.com";
    $subject = "send email with pdf attachment";
    $message = "<p>Please see the attachment.</p>";

    // a random hash will be necessary to send mixed content
    $separator = md5(time());

    // carriage return type (we use a PHP end of line constant)
    $eol = PHP_EOL;

    // attachment name
    $filename = "test.pdf";

    // encode data (puts attachment in proper format)
    $pdfdoc = $pdf->Output("", "S");
    $attachment = chunk_split(base64_encode($pdfdoc));

    // main header
    $headers  = "From: " . $from . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"";

    // no more headers after this, we start the body! //

    $body = "--" . $separator . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
    $body .= "This is a MIME encoded message." . $eol;

    // message
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    $body .= $message . $eol;

    // attachment
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol . $eol;
    $body .= $attachment . $eol;
    $body .= "--" . $separator . "--";

    // send message
    mail($to, $subject, $body, $headers);
    wp_mail('asayeedalam@gmail.com', $subject, $body, $headers);
});
