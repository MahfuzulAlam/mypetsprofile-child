<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Referral_Email
{

    public function __construct()
    {
        add_action('after_inserting_referral', array($this, 'after_inserting_referral'), 10, 2);
    }

    // MPP AFTER INSERTSING REFERRAL
    public function after_inserting_referral($insert_id, $user_id)
    {
        $this->send_email($user_id, 'spokesperson_request');
    }

    // GET EMAIL ADDRESS
    public function get_email($recipient_id)
    {
        $user_email = '';
        $user_info = get_userdata($recipient_id);
        if ($user_info) $user_email = $user_info->user_email;
        return $user_email;
    }

    // PREPARE SUBJECT
    public function prepare_subject($type = '')
    {
        $html = '';

        switch ($type) {
            case 'spokesperson_request':
                $html .= 'New Spokesperson Request';
                break;
        }

        return $html;
    }

    // PREPARE BODY
    public function prepare_body($recipient_id = 0, $type = '')
    {
        $recipient = get_userdata($recipient_id);
        $html = '<p>Hello ' . $recipient->display_name . '</p>';

        switch ($type) {
            case 'spokesperson_request':
                $user_id = get_current_user_id();
                $spokesperson = get_userdata($user_id);
                $html .= 'New spokesperson request from ' . $spokesperson->display_name . '</br>';
                $html .= 'Please click on this link to take actions.</br>';
                $html .= '<a href="' . home_url('/owner-dashboard/') . '">Click Here</a>';
                break;
        }

        $html .= '<p>Best Regards</p>';
        $html .= '<p>MyPetsProfile™️ Team</br>';
        $html .= 'Hello@MyPetsProfile.com</p>';


        return $html;
    }

    // SEND EMAIL
    public function send_email($recipient_id = 0, $type = '')
    {
        if (empty($recipient_id) || empty($type)) return;

        $to = $this->get_email($recipient_id);
        $subject = $this->prepare_subject($type);
        $body = $this->prepare_body($recipient_id, $type);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($to, $subject, $body, $headers);
    }
}

new MPP_Referral_Email;
