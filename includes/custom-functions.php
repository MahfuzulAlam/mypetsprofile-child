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

add_filter('bp_core_signup_send_activation_key', 'ps_disable_activation_email');
function ps_disable_activation_email()
{
    return false;
}

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// Custom Code by Alam - Starts Here

// Unused Code - can be removed later

/* add_action('wp_footer', function () {
?>
    <script type="text/javascript">
        jQuery(window).load(function($) {
            $('.atbdp-body').removeClass('atbdp-map').addClass('atbdp-map-updated');            
            setTimeout(function(){
                console.log('Hello');
                jQuery('.directorist-range-slider-wrap .atbd-active1').css({left: '6.41273px'});
                jQuery('.directorist-range-slider-wrap .atbd-child').css({width: '6.41273px'});
                jQuery('.directorist-range-slider-wrap .atbd-current-value').html('<span>50</span>');
            }, 2000);
            
        });
    </script>
<?php
}, 100); */

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

// Code snippet to solve import table style
add_action('admin_head', function () {
?>
    <style>
        /* ADMIN */
        .atbdp-importer-mapping-table-name code {
            line-break: anywhere;
        }
    </style>
<?php
});

// Add custom fields in Import
add_action('init', function () {
    $tools = ATBDP()->tools;
    $tools->importable_fields['directorist_listing_rating'] = "Average Rating";
    $tools->importable_fields['social_facebook'] = "Facebook Url";
});

// Need to work with this later
add_shortcode('show-bb-group', function () {
    $group_avatar = bp_is_active('groups') ? bp_get_group_avatar_url(groups_get_group(57)) : '';
    e_var_dump($group_avatar);
    //e_var_dump( get_post_meta( 14 ) );
    return '--';
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

// IAP Connection to the Pricing Plan

add_action('init', function () {
    if (class_exists('bbapp')) {
        require_once(get_stylesheet_directory() . '/includes/buddyboss/class-purchase.php');
        BuddyBossApp\Custom\IAP::instance();
    }
});


function iap_get_directorist_pricing_plans()
{
    $plans = array();
    $args = array(
        'post_type' => 'atbdp_pricing_plans',
        'posts_per_page' => -1,
        'status' => 'publish',
    );

    // The Query

    $atbdp_query = new WP_Query($args);
    $has_plan = $atbdp_query->have_posts();

    $plans_all = $atbdp_query->posts;

    if ($has_plan && $plans_all) {
        foreach ($plans_all as $value) {
            $plan_id           = $value->ID;
            $plan_title = $value->post_title;
            $plans[] = array(
                'id' => $plan_id,
                'text' => $plan_title
            );
        }
    }
    return $plans;
}


function bb_atpp_gifting_plan($iap_order_id = 0, $user_id = 0, $plan_id = 0)
{
    if (!empty($user_id)) {

        $order_id = wp_insert_post(array(
            'post_content' => '',
            'post_title' => sprintf('Order for the IAP Order ID #%d', $iap_order_id),
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'atbdp_orders',
            'comment_status' => false,
        ));

        if ($order_id) {
            $gateway = 'iap';
            // save required data as order post meta
            update_post_meta($order_id, '_transaction_id', wp_generate_password(15, false));
            update_post_meta($order_id, '_payment_gateway', $gateway);
            update_post_meta($order_id, '_payment_status', 'completed');
            update_post_meta($order_id, '_fm_plan_ordered', $plan_id);
            update_post_meta($order_id, '_iap_order_id', $iap_order_id);

            $order_info = array(
                'user_id' => $user_id,
                'order_id' => $order_id,
                'plan_id' => $plan_id,
                'iap_order_id' => $iap_order_id,
                'ref_type' => 'sale'
            );

            do_action('after_bb_atpp_gifting_plan', $order_info);
        }
    }
}


// Custom Code by Alam - Ends Here

// Test Code - Can be remove later

/* add_action('init', function () {
    $order_info = array(
        'user_id' => 1,
        'order_id' => 9001,
        'plan_id' => 120,
        'iap_order_id' => 2,
        'ref_type' => 'sale',
        'price' => 99
    );
    do_action('after_bb_atpp_gifting_plan', $order_info);
}); */

/* 
function bb_atpp_cancelled_plan($iap_order_id = 0, $user_id = 0, $plan_id = 0)
{
    $order_id = get_order_by_iap($iap_order_id);
    // Change order status to cancel
    update_post_meta($order_id, '_payment_status', 'cancelled');

    $order_info = [];

    do_action('after_bb_atpp_cancelled_plan', $order_info);
}

function get_order_by_iap($iap_order_id)
{
    $args = array(
        'meta_key'      =>      '_iap_order_id',
        'meta_value'    =>      $iap_order_id,
        'post_type'     =>      'atbdp_orders',
        'fields'        =>      'ids'
    );

    $orders = new WP_Query($args);
    e_var_dump($orders->posts);

    if ($orders && count($orders->posts) > 0) {
        return $orders->posts[0];
    } else {
        return false;
    }
}

add_action('init', function () {
    get_order_by_iap(2);
});
 */

/* add_action('init', function () {
    //bp_groups_set_group_type(84, array("cruise-ships-pet-friendly574"), false);
    $bb_group_type = get_page_by_title('Cruise Ships (Pet-friendly)', OBJECT, 'bp-group-type');
    if (!is_wp_error($bb_group_type)) {
        echo $bb_group_type_key = get_post_meta($bb_group_type->ID, '_bp_group_type_key', true);
        bp_groups_set_group_type(82, array($bb_group_type_key), false);
    }
}); */
