<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class BuddyBoss_Login_Redirect
{

    public function __construct()
    {
        // MPP SEND MESSAGE ON LOGIN FIRST TIME
        add_action('wp_login', array($this, 'mpp_send_message_on_login_first_time'), 10, 2);

        // LOGIN REDIRECT TO SEARCH PAGE
        add_filter('login_redirect', array($this, 'mpp_login_redirect_first_time'), 20, 3);
    }

    /**
     * MPP SEND MESSAGE ON LOGIN FIRST TIME
     */
    public function mpp_send_message_on_login_first_time($user_login, $user)
    {
        if ($user && is_object($user) && is_a($user, 'WP_User')) {
            if (!$user->has_cap('administrator')) {
                $welcome_message = get_user_meta($user->ID, 'welcome_message', true);
                if (!$welcome_message || empty($welcome_message || $welcome_message != 'completed')) {
                    $message = '<p>Just a standard Welcome to MyPetsProfile&#x2122;&#xfe0f;. Please look around, meet other pet parents, upload your pets profile and more.</p><p>Enjoy.</p><p>Let us know if you have any questions?</p><p>Hello@mypetsprofile.com</p>';
                    $sent = messages_new_message(
                        array(
                            'sender_id'     =>  311,
                            'recipients'    =>  array($user->ID),
                            'subject'       =>  'Welcome',
                            'content'       =>  $message,
                        )
                    );
                    if ($sent && !is_wp_error($sent)) update_user_meta($user->ID, 'welcome_message', 'completed');
                }
            }
        }
    }

    /**
     * LOGIN REDIRECT TO SEARCH PAGE
     */
    public function mpp_login_redirect_first_time($url, $req, $user)
    {
        if ($user && is_object($user) && is_a($user, 'WP_User')) {
            if (!$user->has_cap('administrator')) {
                $first_login = get_user_meta($user->ID, 'first_login', true);
                if (!$first_login || empty($first_login)) {
                    update_user_meta($user->ID, 'first_login', 'completed');
                    return home_url('/search-directory/');
                }
            }
        }
        return $url;
    }
}

new BuddyBoss_Login_Redirect;
