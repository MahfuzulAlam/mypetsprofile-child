<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Referral_Notification
{

    public function __construct()
    {
        add_action( 'bp_setup_globals', array($this, 'mypetsprofile_register_custom_bp_notifications') );
        add_action( 'after_inserting_referral', array($this, 'mpp_after_inserting_referral'), 10, 2 );
    }

    /**
     * Register Custom BP Notifications
     * Inform BuddyPress about our custom myCRED related notifications.
     * @since 1.0
     * @version 1.0
     */
    public function mypetsprofile_register_custom_bp_notifications() {
        buddypress()->mpp_referral_notifications = new stdClass;
        buddypress()->mpp_referral_notifications->notification_callback = array($this, 'mpp_referral_notification_callback');
        buddypress()->active_components['mpp_referral_notifications'] = 1;
    }

    /**
     * Capture Mypetsprofile Event
     * Whenever we add to the log we add a notification.
     * @since 1.0
     * @version 1.0
     */
    public function mpp_after_inserting_referral( $insert_id, $user_id ) {
        if ( function_exists( 'bp_notifications_add_notification' ) )
        {
            bp_notifications_add_notification( array(
                'user_id'           => $user_id,
                'item_id'           => $insert_id,
                'secondary_item_id' => get_current_user_id(),
                'component_name'    => 'mpp_referral_notifications',
                'component_action'  => 'spokesperson_request'
            ) );
        }
    }

    /**
     * Render Notification
     * Help BuddyPress out by rendering the log entry into something it can understand.
     * @since 1.0
     * @version 1.0
     */
    public function mpp_referral_notification_callback( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $id = 0 ) {

        $return = false;

        if ( $action == 'spokesperson_request' ) {

            $spokesperson = get_userdata($secondary_item_id);
            $link = home_url('/dashboard-2/');
            $text = "New spokesperson request from ". $spokesperson->display_name;
            $title = "Spokesperson Request";

        }

        if ( 'string' == $format ) {
            $return = '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $text ) . '</a>';
        } else {
            $return = array(
                'text' => $text,
                'link' => $link
            );
        }

        return $return;

    }

}

new MPP_Referral_Notification;