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

        $this->integration_slug  = 'directorist-plan';
        $this->integration_type  = 'directorist-plan';
        $this->integration_label = __('Directorist Plan', 'buddyboss-app');
        $this->item_label        = __('Directorist Plan', 'buddyboss-app');;

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

            bb_atpp_gifting_plan($order->id, $order->user_id, $plan_id);
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

            $this->bb_atpp_cancelled_plan($order->id, $order->user_id, $plan_id);
        }
    }

    /**
     * Helper function to update users group counts.
     *
     * @param        $group_id
     * @param        $user_id
     * @param string $action
     */
    public function bb_atpp_cancelled_plan($iap_order_id = 0, $user_id = 0, $plan_id = 0)
    {
        $order_id = $this->get_order_by_iap($iap_order_id);
        // Change order status to cancel
        update_post_meta($order_id, '_payment_status', 'cancelled');

        $order_info = array(
            'user_id' => $user_id,
            'order_id' => $order_id,
            'plan_id' => $plan_id,
            'iap_order_id' => $iap_order_id,
            'ref_type' => 'sale',
            'price' => 99
        );

        do_action('after_bb_atpp_cancelled_plan', $order_info);
    }

    public function get_order_by_iap($iap_order_id)
    {
        $args = array(
            'meta_key'      =>      '_iap_order_id',
            'meta_value'    =>      $iap_order_id,
            'post_type'     =>      'atbdp_orders',
            'fields'        =>      'ids'
        );

        $orders = new WP_Query($args);

        if ($orders && count($orders->posts) > 0) {
            return $orders->posts[0];
        } else {
            return false;
        }
    }

    function iap_linking_options($results)
    {

        $plans = iap_get_directorist_pricing_plans();

        return $plans;
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

        foreach ($item_ids as $item_identifier) {
            $split    = explode(':', $item_identifier);
            $group_id = $split[0];
            if (learndash_is_user_in_group($order->user_id, $group_id)) {
                $has_access = true;
                break;
            }
        }

        return $has_access;
    }
}
