<?php

/**
 * Integrations: Directorist Pricing Plan
 */

/**
 * Implements an integration for Gravity Forms.
 *
 * @since 1.2
 *
 * @see Affiliate_WP_Base
 */
class Affiliate_Directorist_Pricting_Plan extends Affiliate_WP_Base
{

    /**
     * The context for referrals. This refers to the integration that is being used.
     *
     * @access  public
     * @since   1.2
     */
    public $context = 'pricingplan';

    /**
     * Register hooks for this integration
     *
     * @access public
     */
    public function __construct()
    {
        add_action('after_bb_atpp_gifting_plan', array($this, 'add_pending_referral'), 10, 1);
        //add_action('gform_post_payment_completed', array($this, 'mark_referral_complete'), 10, 2);
        //add_action('gform_post_payment_refunded', array($this, 'revoke_referral_on_refund'), 10, 2);
    }

    /**
     * Add pending referral
     */
    public function add_pending_referral($order_info)
    {
        //e_var_dump($order_info);
        $reference    = $order_info['order_id'];
        $affiliate_id = $this->get_affiliate_id($reference);

        // Block referral if not referred or affiliate ID is empty.
        if (!$this->was_referred() && empty($affiliate_id)) {
            return; // Referral not created because affiliate not referred and not a coupon.
        }

        // create draft referral.
        $referral_id = $this->insert_draft_referral(
            $affiliate_id,
            array(
                'reference'          => $reference,
                'description'        => 'description',
            )
        );
        if (!$referral_id) {
            $this->log('Draft referral creation failed.');
            return;
        }

        // Get the referral type we are creating.
        $type = empty($order_info['ref_type']) ? 'sale' : $order_info['ref_type'];

        $this->referral_type = $type;

        $referral_total = $this->calculate_referral_amount($order_info['price'], $order_info['order_id']);

        $this->hydrate_referral(
            $referral_id,
            array(
                'status'      => 'pending',
                'amount'      => $referral_total
            )
        );

        $this->log(sprintf('Referral #%d updated successfully.', $referral_id));

        /* if (empty($total)) {
            $this->mark_referral_complete($order_info, array());
        } */
    }

    /**
     * Mark referral as complete
     *
     * @access public
     * @uses GFFormsModel::add_note()
     *
     * @param array $entry
     * @param array $action
     */

    public function mark_referral_complete($order_info, $action)
    {

        $this->complete_referral($order_info['order_id']);

        $referral = affwp_get_referral_by('reference', $order_info['order_id'], $this->context);

        if (!is_wp_error($referral)) {
            $amount = affwp_currency_filter(affwp_format_amount($referral->amount));
            $name   = affiliate_wp()->affiliates->get_affiliate_name($referral->affiliate_id);
            $note   = sprintf(
                __('Referral #%1$d for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp'),
                $referral->referral_id,
                $amount,
                $name,
                $referral->affiliate_id
            );
        } else {
            affiliate_wp()->utils->log('mark_referral_complete: The referral could not be found.', $referral);
        }
    }

    /**
     * Revoke referral on refund
     *
     * @access public
     * @uses GFFormsModel::add_note()
     *
     * @param array $entry
     * @param array $action
     */
    /*
    public function revoke_referral_on_refund($entry, $action)
    {

        $this->reject_referral($entry['id']);

        $referral = affwp_get_referral_by('reference', $entry['id'], $this->context);

        if (!is_wp_error($referral)) {
            $amount = affwp_currency_filter(affwp_format_amount($referral->amount));
            $name   = affiliate_wp()->affiliates->get_affiliate_name($referral->affiliate_id);
            $note   = sprintf(__('Referral #%d for %s for %s rejected', 'affiliate-wp'), $referral->referral_id, $amount, $name);

            GFFormsModel::add_note($entry["id"], 0, 'AffiliateWP', $note);
        } else {
            affiliate_wp()->utils->log('revoke_referral_on_refund: The referral could not be found.', $referral);
        }
    }
    */

    /**
     * Runs the check necessary to confirm this plugin is active.
     *
     * @since 2.5
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    function plugin_is_active()
    {
        return class_exists('ATBDP_Pricing_Plans');
    }
}

new Affiliate_Directorist_Pricting_Plan;
