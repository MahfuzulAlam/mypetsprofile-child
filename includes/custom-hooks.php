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
    }

    // Change the pricing plan url for mobile
    public function atbdp_pricing_plan_to_checkout_url($url, $plan_id)
    {
        e_var_dump($_SERVER['HTTP_USER_AGENT']);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false) {
            $iap_plan_id = 0;
            switch ($plan_id) {
                case 12172:
                    $iap_plan_id = 1;
                    break;
                case 4183:
                    $iap_plan_id = 3;
                    break;
                case 4182:
                    $iap_plan_id = 4;
                    break;
            }
            if ($iap_plan_id !== 0) $url = 'https://communityportal.mypetsprofile.com/bbapp/products/' . $iap_plan_id;
        }
        return $url;
    }
}

new MPP_Child_Hooks;
