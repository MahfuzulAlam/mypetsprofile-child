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
        add_action( 'mpp_accept_spokespersor_application', array($this, 'mpp_accept_spokespersor_application'), 10, 2);
        add_action( 'mpp_reject_spokespersor_application', array($this, 'mpp_reject_spokespersor_application'), 10, 2);
        add_action( 'mpp_spokesperson_removed', array($this, 'mpp_spokesperson_removed'), 10, 2);
        add_action( 'mpp_spokespersor_status_changed', array($this, 'mpp_spokespersor_status_changed'), 10, 3);
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
                'component_action'  => 'spokesperson_request',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
                'allow_duplicate'   => true,
            ) );
        }
    }

    public function mpp_accept_spokespersor_application( $user_id, $listing_id )
    {
        if ( function_exists( 'bp_notifications_add_notification' ) )
        {
            $post = get_post( $listing_id );
            bp_notifications_add_notification( array(
                'user_id'           => $user_id,
                'item_id'           => $listing_id,
                'secondary_item_id' => $post->post_author,
                'component_name'    => 'mpp_referral_notifications',
                'component_action'  => 'spokesperson_accept',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
                'allow_duplicate'   => true,
            ) );
        }
    }

    public function mpp_reject_spokespersor_application( $user_id, $listing_id )
    {
        if ( function_exists( 'bp_notifications_add_notification' ) )
        {
            $post = get_post( $listing_id );
            bp_notifications_add_notification( array(
                'user_id'           => $user_id,
                'item_id'           => $listing_id,
                'secondary_item_id' => $post->post_author,
                'component_name'    => 'mpp_referral_notifications',
                'component_action'  => 'spokesperson_reject',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
                'allow_duplicate'   => true,
            ) );
        }
    }

    // SPOKESPERSON REMOVED

    public function mpp_spokesperson_removed( $user_id, $listing_id )
    {
        if ( function_exists( 'bp_notifications_add_notification' ) )
        {
            $post = get_post( $listing_id );
            bp_notifications_add_notification( array(
                'user_id'           => $user_id,
                'item_id'           => $listing_id,
                'secondary_item_id' => $post->post_author,
                'component_name'    => 'mpp_referral_notifications',
                'component_action'  => 'spokesperson_removed',
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
                'allow_duplicate'   => true,
            ) );
        }
    }

    // STATUS CHANGED

    public function mpp_spokespersor_status_changed( $user_id, $listing_id, $status )
    {
        if ( function_exists( 'bp_notifications_add_notification' ) )
        {
            $post = get_post( $listing_id );
            bp_notifications_add_notification( array(
                'user_id'           => $user_id,
                'item_id'           => $listing_id,
                'secondary_item_id' => $post->post_author,
                'component_name'    => 'mpp_referral_notifications',
                'component_action'  => 'spokesperson_status_changed_'. $status,
                'date_notified'     => bp_core_current_time(),
                'is_new'            => 1,
                'allow_duplicate'   => true,
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
            $link = home_url('/owner-dashboard/');
            $text = "New spokesperson request from ". $spokesperson->display_name;
            $title = "Spokesperson Request";

        }
        elseif( $action == 'spokesperson_accept')
        {
            $listing = get_the_title($item_id);
            $link = get_permalink($item_id);
            $text = "You are approved as a Spokesperson of ". $listing;
            $title = "Spokesperson Accept";
        }
        elseif( $action == 'spokesperson_reject')
        {
            $listing = get_the_title($item_id);
            $link = get_permalink($item_id);
            $text = "You are rejected as a Spokesperson of ". $listing;
            $title = "Spokesperson Accept";
        }
        elseif( $action == 'spokesperson_status_changed_active')
        {
            $listing = get_the_title($item_id);
            $link = get_permalink($item_id);
            $text = "Your status as a Spokesperson of ". $listing . " has been activated.";
            $title = "Spokesperson Active";
        }
        elseif( $action == 'spokesperson_status_changed_inactive')
        {
            $listing = get_the_title($item_id);
            $link = get_permalink($item_id);
            $text = "Your status as a Spokesperson of ". $listing . " has been deactivated.";
            $title = "Spokesperson Inactive";
        }
        elseif( $action == 'spokesperson_removed')
        {
            $listing = get_the_title($item_id);
            $link = get_permalink($item_id);
            $text = "Your have been removed as a Spokesperson of ". $listing . "";
            $title = "Spokesperson Inactive";
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