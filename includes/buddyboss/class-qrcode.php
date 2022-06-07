<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class MPP_QRCode
{

    public function __construct()
    {
        //PROFILE QR CODE
        add_shortcode('mpp-user-profile-qrcode', array($this, 'mpp_user_profile_qrcode'));
        //GROUP QR CODE
        add_shortcode('mpp-group-qrcode', array($this, 'mpp_group_qrcode'));
        //QR CODE READ AND PROCESS
        add_shortcode('mpp-process-qrcode', array($this, 'mpp_process_qrcode'));
        //QR CODE DISPLAY ON LISTINGS
        add_shortcode('mpp-listing-qrcode', array($this, 'mpp_listing_qrcode'));
        //GAMIPRESS ADD ACTIVITY TRIGGER
        add_filter('gamipress_activity_triggers', array($this, 'mypetsprofile_custom_activity_triggers'));
        //The listener should be hooked to the desired action through the WordPress function add_action()
        //add_action('init', array($this, 'mypetsprofile_custom_event_listener'));
        add_action('mpp_after_scan_qr_code', array($this, 'mypetsprofile_custom_event_listener'));
    }

    //PROFILE QR CODE
    public function mpp_user_profile_qrcode()
    {
        ob_start();
        $user_id = bbp_get_user_id();
        echo do_shortcode('[kaya_qrcode content="' . $user_id . '" size="400" align="aligncenter"]');
        return ob_get_clean();
    }

    //GROUP QR CODE
    public function mpp_group_qrcode()
    {
        ob_start();
        if (is_user_logged_in()) :
            $user_url = bbp_get_user_profile_url(get_current_user_id());
            if (bp_get_current_group_id()) :
                $link = $user_url . '/scan-qr-code/?type=biz&id=' . bp_get_current_group_id();
                echo do_shortcode('[kaya_qrcode title="' . bp_get_group_name() . '" content="' . $link . '" align="aligncenter" title_align="aligncenter" size="400"]');
            endif;
        endif;
        return ob_get_clean();
    }

    //LISTINGS QR CODE DISPLAY
    public function mpp_listing_qrcode()
    {
        global $post;
        ob_start();
        $bb_group_id = get_post_meta($post->ID, '_bb_group_id', true);
        if ($bb_group_id && !empty($bb_group_id)) :
            if (is_user_logged_in() && groups_is_user_admin(get_current_user_id(), $bb_group_id)) :
                $user_url = bbp_get_user_profile_url(get_current_user_id());
                $link = $user_url . '/scan-qr-code/?type=biz&id=' . $bb_group_id;
                echo do_shortcode('[kaya_qrcode content="' . $link . '" align="aligncenter" size="400"]');
            endif;
        endif;
        return ob_get_clean();
    }

    //QR CODE READ AND PROCESS
    public function mpp_process_qrcode()
    {
        ob_start();
        $type = isset($_REQUEST['type']) && !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';
        $id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if (!empty($type) && !empty($id)) {
            do_action('mpp_after_scan_qr_code', array('type' => $type, 'id' => $id));
        }
        return ob_get_clean();
    }

    //GAMIPRESS ADD ACTIVITY TRIGGER
    function mypetsprofile_custom_activity_triggers($triggers)
    {
        // The array key will be the group label
        $triggers['MyPetsProfile Events'] = array(
            'mpp_scan_biz_qr_code' => __('Scan a Biz QR Code', 'gamipress'),
        );
        return $triggers;
    }

    function mypetsprofile_custom_event_listener($args)
    {
        //$args = ['id' => bp_get_current_group_id(), 'type' => 'biz'];
        // Get Current User
        $user_id = get_current_user_id();
        $activity_exists = false;
        // Get User Information
        $mpp_biz_visit = get_user_meta($user_id, 'mpp_biz_visit', true);
        if (!$mpp_biz_visit || empty($mpp_biz_visit)) $mpp_biz_visit = array();
        // Activity Check
        if ($mpp_biz_visit && count($mpp_biz_visit) > 0) {
            $activity_exists = $this->multi_array_search($mpp_biz_visit, array('date' => date('Y-m-d'), 'prop_id' => $args['id']));
        }

        if (!$activity_exists || count($activity_exists) < 1) :
            // Call to the gamipress_trigger_event() function to let know GamiPress this event was happened
            // GamiPress will check if there is something to award automatically
            $event = gamipress_trigger_event(array(
                // Mandatory data, the event triggered and the user ID to be awarded
                'event' => 'mpp_scan_biz_qr_code',
                'user_id' => $user_id,
                'qr_type' => $args['type'],
                'prop_id' => $args['id'],
            ));
            if ($event) {
                // INSERT INFO TO THE USER
                $visit_info = array(
                    'qr_type' => $args['type'],
                    'prop_id' => $args['id'],
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                );
                array_push($mpp_biz_visit, $visit_info);
                update_user_meta($user_id, 'mpp_biz_visit', $mpp_biz_visit);
                // INSERT INFO TO THE GROUP
                if ($args['type'] == 'biz') {
                    $group_biz_visit = groups_get_groupmeta($args['id'], 'mpp_biz_visit', true);
                    $group_biz_visit = $group_biz_visit && !empty($group_biz_visit) ? $group_biz_visit : array();

                    $group_visit_info = array(
                        'user_id' => $user_id,
                        'date' => date('Y-m-d'),
                        'time' => date('H:i:s'),
                    );
                    array_push($group_biz_visit, $group_visit_info);
                    groups_update_groupmeta($args['id'], 'mpp_biz_visit', $group_biz_visit);
                }
            }
        endif;
    }

    // SEARCH IN A ARRAY
    public function multi_array_search($array, $search)
    {

        // Create the result array
        $result = array();

        // Iterate over each array element
        foreach ($array as $key => $value) {

            // Iterate over each search condition
            foreach ($search as $k => $v) {

                // If the array element does not meet the search condition then continue to the next element
                if (!isset($value[$k]) || $value[$k] != $v) {
                    continue 2;
                }
            }

            // Add the array element's key to the result array
            $result[] = $key;
        }

        // Return the result array
        return $result;
    }
}

new MPP_QRCode;
