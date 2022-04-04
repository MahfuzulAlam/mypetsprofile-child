<?php

namespace BuddyBossApp\Custom;

if (!defined('ABSPATH')) {
    exit();
}

use BuddyBossApp\InAppPurchases\IntegrationAbstract;
use BuddyBossApp\InAppPurchases\Orders;
use WP_Query;

// Directorist Listing Integration for BuddyBossApp InAppPurchases.
final class IAP extends IntegrationAbstract
{

    private static $instance = null;

    /**
     * LearnDashGroupIntegration constructor.
     */
    private function __construct()
    {
        // ... leave empty, see Singleton below
    }

    /**
     * Get the instance of this class.
     * @return IAP|null
     */
    public static function instance()
    {
        if (null === self::$instance) {
            $className      = __CLASS__;
            self::$instance = new $className;
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Setup IAP integration
     */
    public function register()
    {

        $this->integration_slug  = 'woocommerce-plan';
        $this->integration_type  = 'woocommerce-plan';
        $this->integration_label = __('Woocommerce Plan', 'buddyboss-app');
        $this->item_label        = __('Woocommerce Plan', 'buddyboss-app');;

        // Register 3rd party plugin for iap
        bbapp_iap()->integrations[$this->integration_slug] = array(
            'type'    => $this->integration_type,
            'label'   => $this->integration_label,
            'enabled' => true,
            'class'   => self::class,
        );

        parent::set_up($this->integration_type, $this->integration_label);

        // Register Instance
        bbapp_iap()->integration[$this->integration_type] = $this::instance();
    }

    /**
     * Below function get triggers when(hook) order is completed.
     *
     * @param $item_ids
     * @param $order
     *
     * @return mixed
     */
    public function on_order_completed($item_ids, $order)
    {

        foreach ($item_ids as $item_identifier) {

            $split    = explode(':', $item_identifier);
            $plan_id = $split[0];

            $this->bb_atwc_gifting_plan($order, $plan_id);

            // Add/Activate Affiliate
            if (function_exists("affwp_get_affiliate_id") && $order->bbapp_product_id == 1) {
                $affiliate_id = affwp_get_affiliate_id($order->user_id);
                if ($affiliate_id) {
                    affwp_set_affiliate_status($affiliate_id, 'active');
                } else {
                    affwp_add_affiliate(array('user_id' => $order->user_id));
                }
            }

            // Activate Order
            if ($order->bbapp_product_id == 8) {
                update_user_meta($order->user_id, 'mec_active_plan', 8);
                update_user_meta($order->user_id, 'mec_event_status', 'active');
            }
        }
    }

    /**
     * Below function get triggers when(hook) order is activated.
     *
     * @param $item_ids
     * @param $order
     *
     * @return mixed
     */
    public function on_order_activate($item_ids, $order)
    {
        // NOTE : Similar to onOrderCompleted($order) until something needs to be changed?
        return $this->on_order_completed($item_ids, $order);
    }

    /**
     * Below function get triggers when(hook) order is expired.
     *
     * @param $item_ids
     * @param $order
     *
     * @return mixed
     */
    public function on_order_expired($item_ids, $order)
    {
        // NOTE : Similar to onOrderCancelled($order) until something needs to be changed?
        $this->on_order_cancelled($item_ids, $order);
    }

    /**
     * Below function get triggers when(hook) order is cancelled.
     *
     * @param $item_ids
     * @param $order
     *
     * @return mixed
     */
    public function on_order_cancelled($item_ids, $order)
    {

        foreach ($item_ids as $item_identifier) {
            $split    = explode(':', $item_identifier);
            $plan_id = $split[0];

            $this->bb_atwc_cancelled_plan($order->id, $order->user_id, $plan_id);
        }

        // Deactivate Affiliate Acount
        if (function_exists("affwp_get_affiliate_id") && $order->bbapp_product_id == 1) {
            $affiliate_id = affwp_get_affiliate_id($order->user_id);
            if ($affiliate_id) affwp_set_affiliate_status($affiliate_id, 'inactive'); // rejected, active, inactive
        }

        // Deactivate Order
        if ($order->bbapp_product_id == 8) {
            update_user_meta($order->user_id, 'mec_active_plan', 8);
            update_user_meta($order->user_id, 'mec_event_status', 'inactive');
        }
    }

    // Helper Functions

    public function get_order_by_iap($iap_order_id)
    {
        $args = array(
            'meta_key'      =>      '_iap_order_id',
            'meta_value'    =>      $iap_order_id,
            'post_type'     =>      'shop_order',
            'fields'        =>      'ids'
        );

        $orders = new WP_Query($args);

        if ($orders && count($orders->posts) > 0) {
            return $orders->posts[0];
        } else {
            return false;
        }
    }

    public function iap_linking_options($results)
    {

        $plans = $this->iap_get_woocommerce_pricing_plans();

        return $plans;
    }

    public function iap_get_woocommerce_pricing_plans()
    {
        $plans = array();
        $args = array(
            'type' => 'listing_pricing_plans',
            'limit' => -1,
            'status' => 'publish',
        );

        // The Query
        $woo_plans = wc_get_products($args);

        if ($woo_plans && count($woo_plans) > 0) {
            foreach ($woo_plans as $product) {
                $plan = $product->get_data();
                $plans[] = array(
                    'id' => $plan['id'],
                    'text' => $plan['name']
                );
            }
        }
        return $plans;
    }

    // Assign/Create Plan to woocommerce
    public function bb_atwc_gifting_plan($iap_order, $plan_id = 0)
    {
        $iap_order_id = $iap_order->id;
        $user_id = $iap_order->user_id;

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
            $order->update_status("wc-completed", "IAP order", TRUE);

            $order_id = $order->get_id();

            // save required data as order post meta
            update_post_meta($order_id, '_fm_plan_ordered', $plan_id);
            update_post_meta($order_id, '_iap_order_id', $iap_order_id);
            update_post_meta($order_id, '_iap_order_info', $iap_order);
            update_post_meta($order_id, '_customer_user', $user_id);
            update_post_meta($order_id, '_listing_id', '');
            //update_post_meta($order_id, '_order_status', 'exit');

            do_action('after_bb_atwc_created_plan', $iap_order_id);
        }
    }

    // On Cancel the plan
    public function bb_atwc_cancelled_plan($iap_order_id = 0, $user_id = 0, $plan_id = 0)
    {
        $order_id = $this->get_order_by_iap($iap_order_id);
        // Change order status to cancel
        wp_update_post(array('ID' => $order_id, 'post_status' => 'wc-cancelled'));

        do_action('after_bb_atwc_cancelled_plan', $iap_order_id);
    }

    function iap_integration_ids($results, $integration_ids)
    {

        foreach ($integration_ids as $key => $integration_id) {
            $results[$key]['id']   = $integration_id;
            $results[$key]['text'] = get_the_title($integration_id);
        }

        return $results;
    }

    function item_id_permalink($link, $item_id)
    {
        return "post.php?post=$item_id&action=edit";
    }

    function is_purchase_available($is_available, $item_id, $integration_item_id)
    {
        return learndash_group_has_course($integration_item_id, $item_id);
    }

    /**
     * Check given integration item has access.
     *
     * @param $item_ids
     * @param $order
     *
     * @return false
     */
    function has_access($item_ids, $order)
    {
        $has_access = false;

        /* foreach ($item_ids as $item_identifier) {
            $split    = explode(':', $item_identifier);
            $plan_id = $split[0];
            if ('publish' == get_post_status($plan_id)) {
                $has_access = true;
                break;
            }
        } */

        return $has_access;
    }
}
