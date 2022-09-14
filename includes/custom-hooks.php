<?php

/**
 * Integrations: MPP CHild Theme Custom Hooks
 */

class MPP_Child_Hooks
{
    public function __construct()
    {
        // Change the pricing plan url for mobile
        add_filter('atbdp_pricing_plan_to_checkout_url', array($this, 'atbdp_pricing_plan_to_checkout_url'), 10, 2);
        // Custom import hooks
        add_action('directorist_listing_imported', array($this, 'directorist_after_import_listing'), 10, 2);
        // Default Group Avatar For Web
        add_filter('bp_get_group_avatar', array($this, 'bp_get_group_avatar'));
        // Default Group Avatar for App
        add_filter('bp_rest_groups_prepare_value', array($this, 'bp_rest_groups_prepare_value'), 10, 3);
        // Add Custom Field on Category Form
        add_action(ATBDP_CATEGORY . '_edit_form_fields', array($this, 'edit_category_icon_field'), 10, 2);
        // Update App Image Meta
        add_action('edited_' . ATBDP_CATEGORY, array($this, 'update_category_app_image'), 10, 2);
        // Create Order Create a WooMembership
        add_action('wc_memberships_user_membership_created', array($this, 'wc_memberships_user_membership_created'), 10, 2);
        // MPP Calculate Funnies Contest CRON Job
        add_action('mpp_calculate_funnies_contest', array($this, 'mpp_calculate_funnies_contest'));
        // Claim listing after the payment
        add_action('atbdp_order_completed', array($this, 'atbdp_order_completed'), 10, 2);
        // Tempate Redirects
        add_action('template_redirect', array($this, 'template_redirect'));
        // Remove All Pricing Plan from the Product List Shop Page
        add_filter('pre_get_posts', array($this, 'remove_pricing_plans_from_shop_page'));
        // Change the price label of the sale product
        add_filter('woocommerce_subscriptions_product_price_string', array($this, 'woocommerce_subscriptions_product_price_string'), 10, 3);
        // WooCommerce Quick Action Custom Field Display
        add_action('woocommerce_product_quick_edit_start', array($this, 'mpp_custom_field_bulk_edit_input'));
        // WooCommerce Quick Action Custom Field Save
        add_action('woocommerce_product_quick_edit_save', array($this, 'mpp_custom_field_bulk_edit_save'));
        // Auto Add Coupon
        add_action('woocommerce_before_cart', array($this, 'mpp_apply_coupon_if_specific_product'));
        add_action('woocommerce_before_checkout_form', array($this, 'mpp_apply_coupon_if_specific_product'));
        // PRODUCT PAGE FIELD
        add_action('woocommerce_product_options_general_product_data', array($this, 'mpp_product_page_coupon_field'));
        // Save Fields
        add_action('woocommerce_process_product_meta', array($this, 'woocommerce_product_custom_fields_save'));
        // WOOCOMMERCE PAYMENT COMPLETE - woocommerce_checkout_order_processed
        add_action('woocommerce_checkout_order_processed', array($this, 'mpp_wc_payment_complete'));
    }

    // Change the pricing plan url for mobile
    public function atbdp_pricing_plan_to_checkout_url($url, $plan_id)
    {
        if (
            strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false &&
                (strpos($_SERVER['HTTP_USER_AGENT'], 'chrome') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'safari') == false))
        ) {
            $iap_plan_id = 0;
            switch ($plan_id) {
                case 18059:
                    $iap_plan_id = 1;
                    break;
                case 18064:
                    $iap_plan_id = 4;
                    break;
                case 18065:
                    $iap_plan_id = 3;
                    break;
                case 18066:
                    $iap_plan_id = 7;
                    break;
                case 18242:
                    $iap_plan_id = 8;
                    break;
                case 18243:
                    $iap_plan_id = 9;
                    break;
                case 18244:
                    $iap_plan_id = 10;
                    break;
            }
            if ($iap_plan_id !== 0) $url = MPP_SITE_URL . '/bbapp/products/' . $iap_plan_id;
        }
        return $url;
    }

    // Custom Listing Import Hook
    public function directorist_after_import_listing($post_id, $post)
    {
        // Assign Pricing Plan
        update_post_meta($post_id, '_fm_plans_by_admin', 1);
        update_post_meta($post_id, '_fm_plans', 18060);
        update_post_meta($post_id, '_never_expire', 1);

        // Update post status to publish
        wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
    }

    // Default Group Avatar For Web
    public function bp_get_group_avatar($avatar)
    {
        global $bp, $groups_template;

        if (strpos($avatar, 'group-avatars')) {
            return $avatar;
        } else {
            $custom_avatar = get_stylesheet_directory_uri() . '/assets/img/default-group.png';

            $directorist_category = groups_get_groupmeta($groups_template->group->id, 'directorist_category', true);

            if ($directorist_category) {
                $category_image = get_term_meta($directorist_category,  'image', true);
                if ($category_image) {
                    $custom_avatar = wp_get_attachment_image_url($category_image);
                }
            }

            $custom_avatar_fetch = $this->bp_process_group_icon($groups_template->group->id);
            $custom_avatar = isset($custom_avatar_fetch['full']) && !empty($custom_avatar_fetch['full']) ? $custom_avatar_fetch['full'] : $custom_avatar;

            if ($bp->current_action == "")
                return '<img class="avatar" src="' . $custom_avatar . '" alt="' . esc_attr($groups_template->group->name) . '" width="' . BP_AVATAR_THUMB_WIDTH . '" height="' . BP_AVATAR_THUMB_HEIGHT . '" />';
            else
                return '<img class="avatar" src="' . $custom_avatar . '" alt="' . esc_attr($groups_template->group->name) . '" width="' . BP_AVATAR_FULL_WIDTH . '" height="' . BP_AVATAR_FULL_HEIGHT . '" />';
        }
    }

    // Default Group Avatar for App
    public function bp_rest_groups_prepare_value($response, $request, $item)
    {
        $custom_avatars = $this->bp_process_group_icon($item->id, 'app_image');
        if (!empty($custom_avatars)) {
            $response->data['avatar_urls']['thumb'] = $custom_avatars['full'];
            $response->data['avatar_urls']['full'] = $custom_avatars['full'];
            $response->data['avatar_urls']['is_default'] = false;
        }

        // Custom Cover
        $custom_cover = $this->bp_process_group_cover($item->id);
        if (!empty($custom_cover)) {
            $response->data['cover_url'] = $custom_cover;
            $response->data['cover_is_default'] = false;
        }

        return $response;
    }

    // Get/Process Group Icon
    public function bp_process_group_icon($group_id = 0, $image_type = 'image')
    {
        $custom_avatars = array();
        if ($group_id == 0) return $custom_avatars;
        $directorist_category = groups_get_groupmeta($group_id, 'directorist_category', true);
        if (!$directorist_category || empty($directorist_category)) {
            $group_type = bp_groups_get_group_type($group_id);
            $group_type_obj = bp_groups_get_group_type_object($group_type);
            if ($group_type_obj) {
                $category_obj = get_term_by('name', $group_type_obj->labels['name'], ATBDP_CATEGORY);
                if (!is_wp_error($category_obj)) {
                    $directorist_category = $category_obj->term_id;
                    groups_update_groupmeta($group_id, 'directorist_category', $category_obj->term_id);
                }
            }
        }
        if ($directorist_category) {
            $category_image = get_term_meta($directorist_category,  $image_type, true);
            if ($category_image) {
                $custom_avatars['thumb'] = wp_get_attachment_image_url($category_image, 'bb-app-group-avatar');
                $custom_avatars['full'] = wp_get_attachment_image_url($category_image, 'full');
            }
        }
        return $custom_avatars;
    }

    /**
     * BP PROCESS GROUP COVER
     */
    public function bp_process_group_cover($group_id = 0)
    {
        $cover_url = '';
        if ($group_id == 0) return $cover_url;

        $mpp_listings = groups_get_groupmeta($group_id, 'directorist_listings_ids', true);

        if ($mpp_listings && count($mpp_listings) > 0) {
            $listing_prv_img   = get_post_meta($mpp_listings[0], '_listing_prv_img', true);
            $listing_img       = get_post_meta($mpp_listings[0], '_listing_img', true);

            if (is_array($listing_img) && !empty($listing_img)) {
                $cover_url = atbdp_get_image_source($listing_img[0], 'large');
            }

            if (!empty($listing_prv_img)) {
                $cover_url = atbdp_get_image_source($listing_prv_img, 'large');
            }
        }

        return $cover_url;
    }

    // Edit Custom Category Fields
    public function edit_category_icon_field($term, $taxonomy)
    {
        // App Image
        $image_id = get_term_meta($term->term_id, 'app_image', true);
        $image_src = ($image_id) ? wp_get_attachment_url((int)$image_id) : '';

        // App Image Cover
        $image_cover_id = get_term_meta($term->term_id, 'app_image_cover', true);
        $image_cover_src = ($image_cover_id) ? wp_get_attachment_url((int)$image_cover_id) : '';

        // APP Url
        $cat_app_url = get_term_meta($term->term_id, 'cat_app_url', true);
?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="atbdp-categories-app-image-id"><?php _e('App Image Icon', 'directorist'); ?></label>
            </th>
            <td>
                <input type="hidden" id="atbdp-categories-app-image-id" name="app_image" value="<?php echo $image_id; ?>" />
                <div id="atbdp-categories-app-image-wrapper">
                    <?php
                    if ($image_src) : ?>
                        <img src="<?php echo $image_src; ?>" />
                        <a href="" class="remove_cat_app_img"><span class="fa fa-times" title="Remove it"></span></a>
                    <?php endif; ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary" id="atbdp-categories-upload-app-image" value="<?php _e('Add Image', 'directorist'); ?>" />
                </p>
            </td>
        </tr>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="atbdp-categories-app-image-cover-id"><?php _e('App Image Cover', 'directorist'); ?></label>
            </th>
            <td>
                <input type="hidden" id="atbdp-categories-app-image-cover-id" name="app_image_cover" value="<?php echo $image_cover_id; ?>" />
                <div id="atbdp-categories-app-image-cover-wrapper">
                    <?php
                    if ($image_cover_src) : ?>
                        <img src="<?php echo $image_cover_src; ?>" />
                        <a href="" class="remove_cat_app_img_cover"><span class="fa fa-times" title="Remove it"></span></a>
                    <?php endif; ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary" id="atbdp-categories-upload-app-image-cover" value="<?php _e('Add Image', 'directorist'); ?>" />
                </p>
            </td>
        </tr>
        <tr class="form-field term-cat-url-wrap">
            <th scope="row"><label for="cat_app_url">APP Custom URL</label></th>
            <td>
                <input name="cat_app_url" id="cat_app_url" type="text" value="<?php echo $cat_app_url; ?>">
                <p class="description">Enter the custom URL to redirect in the APP.</p>
            </td>
        </tr>
    <?php
    }

    // Save Category App Image Meta
    public function update_category_app_image($term_id, $tt_id)
    {
        //UPDATED CATEGORY IMAGE
        if (isset($_POST['app_image']) && '' !== $_POST['app_image']) {
            update_term_meta($term_id, 'app_image', (int)$_POST['app_image']);
        } else {
            update_term_meta($term_id, 'app_image', '');
        }

        //UPDATED CATEGORY COVER IMAGE
        if (isset($_POST['app_image_cover']) && '' !== $_POST['app_image_cover']) {
            update_term_meta($term_id, 'app_image_cover', (int)$_POST['app_image_cover']);
        } else {
            update_term_meta($term_id, 'app_image_cover', '');
        }

        //UPDATED CATEGORY APP URL
        if (isset($_POST['cat_app_url']) && '' !== $_POST['cat_app_url']) {
            update_term_meta($term_id, 'cat_app_url', $_POST['cat_app_url']);
        } else {
            update_term_meta($term_id, 'cat_app_url', '');
        }
    }

    public function wc_user_membership_created_by_order($user_membership = 'UNDEFINED')
    {
        $orders = get_posts(
            array(
                'post_type' => 'shop_order',
                'post_status' => 'any',
                'numberposts' => 1,
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => '_user_membership_id',
                        'value' => $user_membership
                    ),
                    array(
                        'key' => '_wc_memberships_access_granted',
                        'value' => $user_membership,
                        'compare' => 'LIKE'
                    )
                )
            )
        );
        if ($orders && count($orders) > 0) return true;
        return false;
    }

    // Create Order Create a WooMembership
    public function wc_memberships_user_membership_created($membership_plan, $data)
    {
        $product_ids = get_post_meta($membership_plan->id, '_product_ids', true);

        if ($product_ids && count($product_ids) > 0) {
            foreach ($product_ids as $product) {
                // Add/Activate Affiliate
                $this->add_affiliate_member($product, $data['user_id']);
                // Event Activate Order
                $this->on_activate_event_plan($product, $data['user_id']);
            }
        }

        if ($this->wc_user_membership_created_by_order($data['user_membership_id'])) return;

        if ($product_ids && count($product_ids) > 0) {
            $this->add_woocommerce_order_on_create_membership($data, $product_ids);

            // SEND ADD LISTING LINK
            // if (!empty($data['user_id'])) {
            //     $user_membership = wc_memberships_get_user_membership($data['user_id'], $membership_plan->id);
            //     $user_membership->add_note('Please go to the following link and add your listing - <a href="https://communityportal.mypetsprofile.com/add-listing/">Add Biz/Event</a>', true);
            // }
        }
    }

    // Create an Order on create a membership
    public function add_woocommerce_order_on_create_membership($user, $product_ids)
    {
        $user_id = $user['user_id'];
        $user_membership_id = $user['user_membership_id'];

        /* USER EMAIL */
        global $current_user;
        $user_email = $current_user->user_email;

        if (!empty($user_id)) {

            $address = array(
                'first_name' => get_user_meta($user_id, 'first_name', true),
                'last_name'  => get_user_meta($user_id, 'last_name', true),
                'company'    => get_user_meta($user_id, 'billing_company', true),
                'email'      => $user_email,
                'phone'      => get_user_meta($user_id, 'billing_phone', true),
                'address_1'  => get_user_meta($user_id, 'billing_address_1', true) . get_user_meta($user_id, 'billing_address_2', true),
                'address_2'  => 'IAP',
                'city'       => get_user_meta($user_id, 'billing_city', true),
                'state'      => get_user_meta($user_id, 'billing_state', true),
                'postcode'   => get_user_meta($user_id, 'billing_postcode', true),
                'country'    => get_user_meta($user_id, 'billing_country', true),
            );

            // Now we create the order
            $order = wc_create_order();

            // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
            if ($product_ids && count($product_ids) > 0) {
                foreach ($product_ids as $product) {
                    $order->add_product(get_product($product), 1); // This is an existing SIMPLE product
                }
            }

            $order->set_address($address, 'billing');

            $order->calculate_totals();
            $order->update_status("wc-completed", "IAP order", TRUE);

            $order_id = $order->get_id();

            // save required data as order post meta
            update_post_meta($order_id, '_fm_plan_ordered', $product_ids[0]);
            update_post_meta($order_id, '_user_membership_id', $user_membership_id);
            update_post_meta($order_id, '_customer_user', $user_id);
            update_post_meta($order_id, '_listing_id', '');
            update_post_meta($order_id, '_order_status', 'exit');
        }
    }

    // ADD Affiliate Member
    public function add_affiliate_member($product_id, $user_id)
    {
        if (function_exists("affwp_get_affiliate_id") && $product_id == 18053) {
            $affiliate_id = affwp_get_affiliate_id($user_id);
            if ($affiliate_id) {
                affwp_set_affiliate_status($affiliate_id, 'active');
            } else {
                affwp_add_affiliate(array('user_id' => $user_id));
            }
        }
    }

    // On Activate Events
    public function on_activate_event_plan($product_id, $user_id)
    {
        if ($product_id == 18236) {
            update_user_meta($user_id, 'mec_active_plan', 8);
            update_user_meta($user_id, 'mec_event_status', 'active');
        }
    }

    // MPP Funnies Contest calculation CRON Job
    public function mpp_calculate_funnies_contest()
    {
        $group_id = get_option('mpp_funnies_group') ? get_option('mpp_funnies_group') : 0;
        update_option('mpp_funnies_contest', mpp_get_funnies_activities($group_id));
    }

    // Claim listing automatically afetr the payment
    public function atbdp_order_completed($order_id, $listing_id)
    {
        // Publish the listing
        $my_post = array();
        $my_post['ID'] = $listing_id;
        $my_post['post_status'] = 'publish';
        $my_post['post_author'] = get_current_user_id();
        wp_update_post($my_post);

        // Approve the claim
        $claim_posts = get_posts(
            array(
                'post_type' => 'dcl_claim_listing',
                'numberposts' => 1,
                'meta_key' => '_claimed_listing',
                'meta_value' => $listing_id,
                'fields' => 'ids'
            )
        );

        if ($claim_posts && count($claim_posts) > 0) {
            update_post_meta($claim_posts[0], '_claim_status', 'approved');
            update_post_meta($listing_id, '_claimed_by_admin', 1);
            update_post_meta($listing_id, '_claim_fee', 'claim_approved');
            update_post_meta($listing_id, '_never_expire', 0);
            update_post_meta($listing_id, '_expiry_date', date('Y-m-d H:i:s', strtotime('+1 year')));
        }
    }

    /**
     * Wocommerce Payment Complete Hook
     */
    public function mpp_wc_payment_complete($order_id)
    {
        // ADD AUTO ORDER
        $cart_items = array();
        if ($order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item_key => $item) :
                $cart_items[] = $item->get_product_id();
            endforeach;
        }

        if (in_array(19554, $cart_items)) $this->mpp_another_order_on_order_complete(27261);
        if (in_array(27265, $cart_items)) $this->mpp_another_order_on_order_complete(27262);
        // ADD AUTO ORDER
    }

    /**
     * AUTO ORDER
     */
    public function mpp_another_order_on_order_complete($plan_id = 0)
    {
        $user_id = get_current_user_id();

        if (!empty($user_id)) {

            $address = array(
                'first_name' => get_user_meta($user_id, 'first_name', true),
                'last_name'  => get_user_meta($user_id, 'last_name', true),
                'company'    => get_user_meta($user_id, 'billing_company', true),
                'email'      => get_user_meta($user_id, 'billing_email', true) ? get_user_meta($user_id, 'billing_email', true) : $iap_order->user_email,
                'phone'      => get_user_meta($user_id, 'billing_phone', true),
                'address_1'  => get_user_meta($user_id, 'billing_address_1', true),
                'address_2'  => get_user_meta($user_id, 'billing_address_2', true),
                'city'       => get_user_meta($user_id, 'billing_city', true),
                'state'      => get_user_meta($user_id, 'billing_state', true),
                'postcode'   => get_user_meta($user_id, 'billing_postcode', true),
                'country'    => get_user_meta($user_id, 'billing_country', true),
            );

            // Now we create the order
            $order = wc_create_order();

            // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
            $order->add_product(get_product($plan_id), 1); // This is an existing SIMPLE product
            $order->set_address($address, 'billing');

            $order->calculate_totals();
            $order->update_status("wc-completed", "Automatic Process", TRUE);

            $order_id = $order->get_id();

            // save required data as order post meta
            update_post_meta($order_id, '_fm_plan_ordered', $plan_id);
            update_post_meta($order_id, '_customer_user', $user_id);
            update_post_meta($order_id, '_listing_id', '');
        }
    }

    // Template Redirects
    public function template_redirect()
    {
        $this->redirect_frontpage();
        $this->redirect_claimed_user();
        $this->redirect_to_add_listing_page_after_purchase();
    }

    // Redirect template if user is in Homepage ( For logged out user )
    public function redirect_frontpage()
    {
        if (is_user_logged_in() && (is_home() || is_front_page())) {
            if (!current_user_can('editor') && !current_user_can('administrator')) {
                exit(wp_redirect(home_url('/news-feed/')));
            }
        }
    }

    // Redirect user to the edit listing page after claiming a listing
    public function redirect_claimed_user()
    {
        if (is_user_logged_in() && is_page('payment-receipt')) {
            $action = get_query_var('atbdp_action');
            $order_id = get_query_var('atbdp_order_id');
            if ($action == 'order' && !empty($order_id)) {
                $listing_id = get_post_meta($order_id, '_listing_id', true);
                if ($listing_id) exit(wp_redirect(home_url('/add-listing/edit/' . $listing_id)));
            }
        }
    }

    // Redirect to add listing page after completing an order in woocommerce
    public function redirect_to_add_listing_page_after_purchase()
    {
        global $wp;
        if (is_checkout()) {
            if (isset($wp->query_vars['order-received']) && !empty($wp->query_vars['order-received'])) {
                $order_id = $wp->query_vars['order-received'];
                // PET PROFILE REDIRECTION
                $this->pet_profile_redirection($order_id);
                // DNA KIT
                $this->dna_kit_redirection($order_id);
                // EVENT
                if (mpp_event_id_in_the_order($order_id)) {
                    exit(wp_redirect(MPP_SITE_URL . '/add-edit-pet-friendly-event'));
                }
                // PLANS
                $order = new WC_Order($order_id);
                $order->update_status('wc-completed');
                $plan_id = mpp_get_pricing_plan_from_the_order($order_id);
                if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans') {
                    if ($plan_id == 18059) {
                        exit(wp_redirect(MPP_SITE_URL . '/affiliate-area'));
                    } else {
                        $directory_type = get_post_meta($plan_id, '_assign_to_directory', true) ? get_post_meta($plan_id, '_assign_to_directory', true) : default_directory_type();
                        exit(wp_redirect(MPP_SITE_URL . '/add-listing/?directory_type=' . $directory_type . '&plan=' . $plan_id));
                    }
                }
            }
        }
    }

    /**
     * PET PROFILE PRODUCT REDIRECTION
     */
    public function pet_profile_redirection($order_id = 0)
    {
        if ($order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) :
                if ($item->get_product_id() == 403) {
                    if (isset($_COOKIE['mpp_building']) && !empty($_COOKIE['mpp_building'])) {
                        update_user_meta(get_current_user_id(), 'mpp_building', $_COOKIE['mpp_building']);
                    }
                    exit(wp_redirect(MPP_SITE_URL . '/me'));
                }
            endforeach;
            return false;
        }
    }

    /**
     * DNA KIT REDIRECTION
     */
    public function dna_kit_redirection($order_id = 0)
    {
        if ($order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) :
                if ($item->get_product_id() == 28323) exit(wp_redirect(MPP_SITE_URL . '/pooprint-dna-property-selection'));
            endforeach;
            return false;
        }
    }

    // Remove all pricing plans from shop page
    public function remove_pricing_plans_from_shop_page($query)
    {
        if (!is_admin() && is_post_type_archive('product') && $query->query_vars['post_type'] == 'product') {
            $tax_query = $query->query_vars['tax_query'];
            $tax_query[] = array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => array('listing_pricing_plans'),
                'operator'  => 'NOT IN'
            );
            $query->set('tax_query', $tax_query);
            $query->set('posts_per_page', -1);
            $query->set('meta_query', array(
                'relation' => 'OR',
                'rank_position' => array(
                    'key' => 'rank_position',
                    'compare' => 'EXISTS',
                ),
                'rank_position_not' => array(
                    'key' => 'rank_position',
                    'compare' => 'NOT EXISTS',
                )
            ));
            $query->set('post__not_in', array(19368, 18062, 18063));
            $query->set('orderby', array('rank_position' => 'ASC'));
        }
        return $query;
    }

    // Change the Price of the Sale products
    public function woocommerce_subscriptions_product_price_string($string, $product, $include)
    {
        $sign_up_fee = get_post_meta($product->get_id(), '_subscription_sign_up_fee', true);
        if ($sign_up_fee && $sign_up_fee > 0) {
            if ($product->get_id() == 20139) {
                return '<span class="price"><ins><bdi><span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span><span class="woocommerce-Price-amount amount">' . $sign_up_fee . '</span></bdi></ins><span> +Tax</span></span>';
            }
            return '<span class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' . get_woocommerce_currency_symbol() . $product->get_price() . '<span class="woocommerce-Price-currencySymbol"></span></bdi></span></del> <ins><span class="woocommerce-Price-amount amount"><bdi>' . get_woocommerce_currency_symbol() . $sign_up_fee . '<span class="woocommerce-Price-currencySymbol"></span></bdi></span></ins> <span class="subscription-details"><small class="woocommerce-price-suffix"> +Tax</small> / year</span></span>';
        }

        $trial_length = get_post_meta($product->get_id(), '_subscription_trial_length', true);
        $trial_period = get_post_meta($product->get_id(), '_subscription_trial_period', true);

        if ($trial_length && $trial_period) {
            $mpp_coupon_offer = get_post_meta($product->get_id(), 'mpp_coupon_offer', true);
            if ($mpp_coupon_offer) {
                $string = '<p class="price"><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' . get_woocommerce_currency_symbol() . $product->get_price() . '<span class="woocommerce-Price-currencySymbol"></span></bdi></span></del> <ins><span class="woocommerce-Price-amount amount"><bdi>' . get_woocommerce_currency_symbol() . $mpp_coupon_offer . '<span class="woocommerce-Price-currencySymbol"></span></bdi></span></ins> <span class="subscription-details"><small class="woocommerce-price-suffix"> +Tax</small> / year</span>';
                $string .= ' <span>with ' . $trial_length . ' ' . $trial_period . 's free trial</span></p>';
            }
        }

        return $string;
    }

    // WooCommerce Quick Action Custom Field Display
    function mpp_custom_field_bulk_edit_input()
    {
    ?>
        <label>
            <span class="title"><?php esc_html_e('Rank', 'woocommerce'); ?></span>
            <span class="input-text-wrap">
                <input type="text" name="rank_position" class="text" value="">
            </span>
        </label>
        <br class="clear" />
<?php
    }

    // WooCommerce Quick Action Custom Field Save
    function mpp_custom_field_bulk_edit_save($product)
    {
        $post_id = $product->get_id();
        if (isset($_REQUEST['rank_position'])) {
            $rank_position = $_REQUEST['rank_position'];
            update_post_meta($post_id, 'rank_position', wc_clean($rank_position));
        }
    }

    /**
     * ADD COUPON AUTOMATICALLY
     */
    public function mpp_apply_coupon_if_specific_product()
    {
        // foreach (WC()->cart->get_coupons() as $code => $coupon) {
        //     WC()->cart->remove_coupon($code);
        // }

        foreach (WC()->cart->get_cart() as $product) {
            $coupon_code = get_post_meta($product['product_id'], 'mpp_coupon', true) ? get_post_meta($product['product_id'], 'mpp_coupon', true) : '';
            if (!empty($coupon_code)) {
                if ($coupon_code != 'none') {
                    WC()->cart->apply_coupon($coupon_code);
                }
            } else {
                WC()->cart->apply_coupon('100discount');
            }
        }
        // if product in the cart
        // if (in_array($product_id, array_column(WC()->cart->get_cart(), 'product_id'))) {
        //     if (!WC()->cart->has_discount($coupon_code)) {
        //         WC()->cart->apply_coupon($coupon_code);
        //     }
        // } else { // if product removed from cart we remove the coupon
        //     WC()->cart->remove_coupon($coupon_code);
        //     WC()->cart->calculate_totals();
        // }
    }

    /**
     * PRODUCT PAGE COUPON FIELD
     */
    public function mpp_product_page_coupon_field()
    {
        global $woocommerce, $post;
        woocommerce_wp_text_input(
            array(
                'id' => 'mpp_coupon',
                'placeholder' => 'Enter related coupon code',
                'label' => __('Related Coupon', 'woocommerce'),
                'desc_tip' => 'true'
            )
        );
        woocommerce_wp_text_input(
            array(
                'id' => 'mpp_coupon_offer',
                'placeholder' => 'Enter the offer amount',
                'label' => __('Offer Amount', 'woocommerce'),
                'desc_tip' => 'true'
            )
        );
    }

    public function woocommerce_product_custom_fields_save($post_id)
    {
        // Custom Product Text Field
        $mpp_coupon = isset($_POST['mpp_coupon']) && !empty($_POST['mpp_coupon']) ? $_POST['mpp_coupon'] : "";
        $mpp_coupon_offer = isset($_POST['mpp_coupon_offer']) && !empty($_POST['mpp_coupon_offer']) ? $_POST['mpp_coupon_offer'] : "";
        update_post_meta($post_id, 'mpp_coupon', esc_attr($mpp_coupon));
        update_post_meta($post_id, 'mpp_coupon_offer', esc_attr($mpp_coupon_offer));
    }
}

new MPP_Child_Hooks;
