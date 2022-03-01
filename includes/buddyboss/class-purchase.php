<?php

namespace BuddyBossApp\Custom;

if (!defined('ABSPATH')) {
    exit();
}

use BuddyBossApp\InAppPurchases\IntegrationAbstract;
use BuddyBossApp\InAppPurchases\Orders;

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

        //$item_ids = unserialize( Orders::instance()->get_meta( $order->id, "_learndash_course_ids" ) );

        foreach ($item_ids as $item_identifier) {
            $split    = explode(':', $item_identifier);
            $group_id = $split[0];

            $readable_item_ids[] = "<a href=\"post.php?post=$group_id&action=edit\" target='_blank'>$group_id</a>";

            // revoke the group access
            ld_update_group_access($order->user_id, $group_id, true);
            // update user group count.
            $this->user_update_count($group_id, $order->user_id, "minus");
        }
        $readable_item_ids = implode(', ', $readable_item_ids);

        Orders::instance()->add_history($order->id, 'info', sprintf(__("User un-enrolled in group(s), ID(s) are : %s ", 'buddyboss-app'), $readable_item_ids));
    }

    /**
     * Helper function to update users group counts.
     *
     * @param        $group_id
     * @param        $user_id
     * @param string $action
     */
    public function user_update_count($group_id, $user_id, $action = "plus")
    {

        $groups = get_user_meta($user_id, '_learndash_inapp_purchase_enrolled_group_access_counter', true);

        if (!empty($groups)) {
            $groups = maybe_unserialize($groups);
        } else {
            $groups = array();
        }

        if (isset($groups[$group_id])) {
            if ($action == "plus") {
                $groups[$group_id] += 1;
            } else {
                $groups[$group_id] -= 1;
            }
        } else {
            $groups[$group_id] = ($action == "plus") ? 1 : 0;
        }

        update_user_meta($user_id, '_learndash_inapp_purchase_enrolled_group_access_counter', $groups);
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
