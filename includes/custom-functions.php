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
    $default_image_src = "https://communityportal.mypetsprofile.com/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

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
    $default_image_src = "https://communityportal.mypetsprofile.com/wp-content/uploads/2020/12/MPP-Transparent-logo-product.jpg";

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


// NOTE: Of course change 3 to the appropriate user ID


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


/* add_filter('directorist_custom_field_meta_key_field_args', function ($args) {
    $args['type'] = 'text';
    return $args;
}); */

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

/*
add_action('wp_footer', function () {
    if (is_page(553)) {
        wc_memberships_create_user_membership(array(
            'user_id' => 2,
            'plan_id' => 516,
        ), 'create');
    }
});
*/

/*
add_action('wp_footer', function () {
    $plan_id = isset($_GET['plan']) && !empty($_GET['plan']) ? $_GET['plan'] : 0;
    // CHECK IF USER HAS OWN A PLAN ALREADY
    if ($plan_id) {
        $iap_plan = 0;
        switch ($plan_id) {
            case '18535':
                $iap_plan = 17;
                break;
            case '18531':
                $iap_plan = 13;
                break;
            default:
                $iap_plan = 1;
                break;
        }
        e_var_dump($iap_plan);
?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var plan = <?php echo $iap_plan; ?>;
                if ($('body').hasClass('page-id-3911')) {
                    window.location.replace("https://communityportal.mypetsprofile.com/bbapp/products/" + plan);
                    //window.location.href = "https://communityportal.mypetsprofile.com/bbapp/products/13";
                }
            });
        </script>
<?php
    }
});
*/

// require(get_stylesheet_directory() . '/assets/fpdf/fpdf.php');

// add_action('init', function () {
//     $pdf = new FPDF();
//     $pdf->AddPage();
//     $pdf->SetFont('Arial', 'B', 16);
//     $pdf->Cell(40, 10, 'Hello World!');
//     $pdf->Output('D', 'filename.pdf');
// });

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
