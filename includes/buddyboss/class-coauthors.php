<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class Co_Authors
{

    public function __construct()
    {
        add_action('wp_head', array($this, 'mpp_assign_coauthors_add_listing_page'));
        add_action('wp_footer', array($this, 'mpp_coauthors_javascript'));

        add_action('wp_ajax_nopriv_get_author_info', array($this, 'mpp_get_author_info'));
        add_action('wp_ajax_get_author_info', array($this, 'mpp_get_author_info'));

        add_filter('atbdp_form_custom_widgets', array($this, 'atbdp_form_custom_widgets'));

        add_action('atbdp_listing_updated', array($this, 'add_update_coauthors_with_listing'));
        add_action('atbdp_after_created_listing', array($this, 'add_update_coauthors_with_listing'));

        //add_filter('wp_new_user_notification_email', array($this, 'wp_new_user_notification_email'));
    }

    // Coauthor Plus
    public function mpp_assign_coauthors_add_listing_page()
    {
        if (!is_page('add-listing') && !is_singular('at_biz_dir')) return;

        $listing_id = 0;

        if (is_page('add-listing')) $listing_id = get_query_var('atbdp_listing_id', false);

        if (is_singular('at_biz_dir')) {
            $listing_id = get_the_ID();
        }

        if (!$listing_id) return;

        /* Get All Authors */
        $author_list = array();
        if ($this->coauthors_plugin_active()) {
            $coauthors = get_coauthors($listing_id);
            foreach ($coauthors as $authorInfo) {
                $author_list[] = $authorInfo->ID;
            }

            $current_user = get_current_user_id();
            $current_user_key = array_search($current_user, $author_list);

            if (!$current_user_key || $current_user_key == 0) return;
            if (count($author_list) < 2) return;

            // ReSort

            $temp_user = $author_list[0];
            $author_list[0] = $current_user;
            $author_list[$current_user_key] = $temp_user;

            global $coauthors_plus;
            $coauthors_plus->add_coauthors(
                $listing_id,
                $author_list,
                false,
                'id'
            );
        }
    }

    /**
     * JAVASCRIPT COAUTHOR
     */

    public function mpp_coauthors_javascript()
    {
        if (is_page('add-listing')) :
?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {

                    // SHOW ADMIN FIELDS
                    if ($('input[name="add_admin_info[]"]').is(":checked")) {
                        $('.custom_text_aai-name, .custom_text_aai-last-name, .directorist-form-email-field, .directorist-form-phone-field').show();
                    } else {
                        $('.custom_text_aai-name, .custom_text_aai-last-name, .directorist-form-email-field, .directorist-form-phone-field').hide();
                    }

                    // SHOW ADMIN FIELDS
                    if ($('input[name="add_sec_admin[]"]').is(":checked")) {
                        $('.custom_text_sac-email, .custom_text_sac-name, .custom_text_sac-last-name, .directorist-form-phone2-field').show();
                    } else {
                        $('.custom_text_sac-email, .custom_text_sac-name, .custom_text_sac-last-name, .directorist-form-phone2-field').hide();
                    }


                    // Toggle
                    $('input[name="add_admin_info[]"]').change(function() {
                        if ($(this).is(":checked")) {
                            $('.custom_text_aai-name, .custom_text_aai-last-name, .directorist-form-email-field, .directorist-form-phone-field').show();
                        } else {
                            $('.custom_text_aai-name, .custom_text_aai-last-name, .directorist-form-email-field, .directorist-form-phone-field').hide();
                        }
                    });
                    $('input[name="add_sec_admin[]"]').change(function() {
                        if ($(this).is(":checked")) {
                            $('.custom_text_sac-email, .custom_text_sac-name, .custom_text_sac-last-name, .directorist-form-phone2-field').show();
                        } else {
                            $('.custom_text_sac-email, .custom_text_sac-name, .custom_text_sac-last-name, .directorist-form-phone2-field').hide();
                        }
                    });
                    // Toggle

                    // AJAX REQUEST
                    $('#email, #sac-email').change(function() {
                        var $field_name = $(this).attr('name');
                        //console.log($field_name);
                        var email = $(this).val();
                        if (email != '') {
                            //console.log(email);
                            $.ajax({
                                type: 'post',
                                url: mppChild.ajaxurl,
                                data: {
                                    'action': 'get_author_info',
                                    'email': email
                                },
                                dataType: 'json',
                                success: function(data) {
                                    if (data.success) {
                                        if ($field_name == 'email') {
                                            $('.custom_text_aai-name input').val(data.user.first_name);
                                            $('.custom_text_aai-last-name input').val(data.user.last_name);
                                            $('.directorist-form-phone-field input').val(data.user.phone);
                                        } else {
                                            $('.custom_text_sac-name input').val(data.user.first_name);
                                            $('.custom_text_sac-last-name input').val(data.user.last_name);
                                            $('.directorist-form-phone2-field input').val(data.user.phone);
                                        }
                                    } else {
                                        if ($field_name == 'email') {
                                            $('.custom_text_aai-name input').val('');
                                            $('.custom_text_aai-last-name input').val('');
                                            $('.directorist-form-phone-field input').val('');
                                        } else {
                                            $('.custom_text_sac-name input').val('');
                                            $('.custom_text_sac-last-name input').val('');
                                            $('.directorist-form-phone2-field input').val('');
                                        }
                                    }
                                }
                            });
                        }
                    });
                    // AJAX REQUEST
                });
            </script>
<?php
        endif;
    }

    public function mpp_get_author_info()
    {
        $success = false;
        $user_info = array();
        $email = $_REQUEST['email'];
        $user = get_user_by('email', $email);
        if ($user) {
            $user_info['first_name'] = get_user_meta($user->ID, 'first_name', true);
            $user_info['last_name'] = get_user_meta($user->ID, 'last_name', true);
            $user_info['phone'] = get_user_meta($user->ID, 'billing_phone', true);
            $success = true;
        }
        echo json_encode(array('success' => $success, 'user' => $user_info));
        die();
    }


    public function atbdp_form_custom_widgets($widgets)
    {
        $widgets['checkbox']['options']['class'] = array(
            'type'  => 'text',
            'label' => __('Custom Class', 'directorist'),
            'value' => 'class',
        );
        return $widgets;
    }

    //atbdp_after_created_listing
    public function add_update_coauthors_with_listing($listing_id)
    {

        $authors = array(get_current_user_id());

        // ADD ADMIN INFO
        $add_admin_info = isset($_POST['add_admin_info']) && !empty($_POST['add_admin_info']) ? $_POST['add_admin_info'][0] : 'no';
        if ($add_admin_info == 'yes') {
            // IF YES
            // ADMIN
            $email = isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : '';
            if ($email != '') {
                $admin = get_user_by('email', $email);
                if ($admin) {
                    $authors[] = $admin->ID;
                } else {
                    // CREATE A USER SEND A EMAIL
                    $firstname = trim(isset($_POST['aai-name']) ? $_POST['aai-name'] : "");
                    $lastname = trim(isset($_POST['aai-last-name']) ? $_POST['aai-last-name'] : "");
                    $phone = trim(isset($_POST['phone']) ? $_POST['phone'] : "");
                    $user_id = $this->mpp_add_new_user($email, $firstname, $lastname, $phone, $listing_id);
                    if ($user_id) $authors[] = $user_id;
                }
            }
        }

        // ADD SAC ADMIN INFO
        $add_sec_admin = isset($_POST['add_sec_admin']) && !empty($_POST['add_sec_admin']) ? $_POST['add_sec_admin'][0] : 'no';
        if ($add_sec_admin == 'yes') {
            // IF YES
            // ADMIN
            $sac_email = isset($_POST['sac-email']) && !empty($_POST['sac-email']) ? $_POST['sac-email'] : '';
            if ($sac_email != '') {
                $sac_admin = get_user_by('email', $sac_email);
                if ($sac_admin) {
                    $authors[] = $sac_admin->ID;
                    $this->update_user_metas($sac_admin->ID, $firstname, $lastname, $phone);
                } else {
                    // CREATE A USER SEND A EMAIL
                    $firstname = trim(isset($_POST['sac-name']) ? $_POST['sac-name'] : "");
                    $lastname = trim(isset($_POST['sac-last-name']) ? $_POST['sac-last-name'] : "");
                    $phone = trim(isset($_POST['phone2']) ? $_POST['phone2'] : "");
                    $user_id = $this->mpp_add_new_user($sac_email, $firstname, $lastname, $phone, $listing_id);
                    if ($user_id) $authors[] = $user_id;
                }
            }
        }

        // ADD COAUTHORS
        if ($this->coauthors_plugin_active() && count($authors) > 0) {
            global $coauthors_plus;
            $coauthors_plus->add_coauthors(
                $listing_id,
                $authors,
                false,
                'id'
            );
        }

        //file_put_contents(dirname(__FILE__) . '/file.json', json_encode(array($authors, $_POST)));
    }


    public function mpp_add_new_user($email = '', $firstname = '', $lastname = '', $phone = '', $listing_id = 0)
    {
        $user_name = $this->mpp_generate_username($firstname, $lastname, $email);
        if (!empty($user_name)) {
            $random_password = wp_generate_password(12, false);
            $user_id = wp_create_user($user_name, $random_password, $email);
            if ($user_id) {
                // Add FirstName, LastName, Phone
                update_user_meta($user_id, 'first_name', $firstname);
                update_user_meta($user_id, 'last_name', $lastname);
                update_user_meta($user_id, 'billing_phone', $phone);
                // Send Notification
                //wp_new_user_notification($user_id, null, 'user');
                //update_user_meta($user_id, 'activation_key', $random_password);
                //bp_core_activation_signup_user_notification($user_name, $email, $random_password, array());
                // Return User ID
                $this->send_password_reset_email($email, $random_password, $listing_id);
                return $user_id;
            }
        }
        return false;
    }

    public function update_user_metas($user_id, $firstname = '', $lastname = '', $phone = '')
    {
        $firstname = trim(isset($_POST['sac-name']) ? $_POST['sac-name'] : "");
        $lastname = trim(isset($_POST['sac-last-name']) ? $_POST['sac-last-name'] : "");
        $phone = trim(isset($_POST['phone2']) ? $_POST['phone2'] : "");

        if (!empty($firstname)) update_user_meta($user_id, 'first_name', $firstname);
        if (!empty($lastname)) update_user_meta($user_id, 'last_name', $lastname);
        if (!empty($phone)) update_user_meta($user_id, 'billing_phone', $phone);
    }

    public function mpp_generate_username($firstname = '', $lastname = '', $email = '')
    {
        $fullname = !empty($lastname) ? trim($firstname . '_' . $lastname) : trim($firstname);
        $username = !empty($fullname) ? $this->mpp_slugify_text($fullname, '_') : '';
        if (!$username || empty($username)) {
            $username = implode('@', explode('@', $email, -1));
        }
        $x = 1;
        $y = 3;
        while ($x <= $y) {
            if (username_exists($username)) {
                $username = $username . $x;
            } else {
                return $username;
            }
            $x++;
            $y++;
        }
    }

    public function mpp_slugify_text($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return false;
        }

        return $text;
    }

    // COAUTHORS ACTIVATE
    public function coauthors_plugin_active()
    {
        if (is_plugin_active('co-authors-plus/co-authors-plus.php')) {
            return true;
        } else {
            return false;
        }
    }

    // Overwrite Email
    public function wp_new_user_notification_email($notification_email)
    {
        $new_email_message = '<p>Welcome to MyPetsProfile™️ Admin set-up.</p>';
        $new_email_message .= '<p>Please click the link below to access your admin account and create a password. Enjoy all the benefits of communicating with residents with pets.</p>';
        $new_email_message .= '<p>Thank you</p><p>The MyPetsProfile™️ Team</p>';
        $new_email_message .= $notification_email['message'];
        $notification_email['message'] = $new_email_message;
        return $notification_email;
    }

    public function send_password_reset_email($email = '', $password = '', $listing_id = 0)
    {
        $user = get_user_by('email', $email);
        $key = get_password_reset_key($user);
        $username = $user->user_login;


        //$reset_link = wp_login_url() . '?action=rp&key=' . $key . '&login=' . $username;
        $message = '';
        $message .= '<p>Your PooPrints Canada Program has provided a community listing on its MyPetsProfile&trade; Web and App Pet Platform at no cost.</p>';
        $message .= '<p>Welcome to your Admin set-up for the pet-friendly community listing of <a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>.</p>';
        $message .= '<p>Please click the link below to access your private and secure admin account and create a password. </p>';
        $message .= '<h4>' . __('Proceed to login using the below username and password: ', 'mypetsprofile') . '</h4>';
        $message .= '<p><strong>Login URL:</strong> <a href="' . get_permalink($listing_id) . '">Click here to login</a></p>';
        $message .= '<p><strong>Username:</strong> ' . $email . '</p>';
        $message .= '<p><strong>Password:</strong> ' . $password . '</p>';
        $message .= '<p><strong>Note:</strong> Don’t forget to download the App banner at the top of your screen after login, to download the free App version.</p>';
        /*
        $message .= '<h2>' . __('Proceed to reset password : ', 'my_slug') . '</h2><br />' .
            __('To set your password Click here.', 'mypetsprofile') .
            '</br><a href="' . esc_url($reset_link) . '" title="' . __('Reset your password link', 'mypetsprofile') . '" >Reset your password</a>';
        */
        $message .= '<p>Your admin access allows you to manage and edit your pet-friendly community listing with photos, amenities and more.</p>';
        $message .= '<p>If you choose, you can receive inquiries from new pet parents interested in securing a pet-friendly home in your community.</p>';
        $message .= '<p>You can also share your community listing with existing residents with pets, and communicate directly or in a group. (Your communications group is attached to your listing)</p>';
        $message .= '<p>Enjoy all the benefits of your pet-friendly community listing.</p>';
        $message .= '<p>Please let us know if you have any questions?</p>';
        $message .= '<p>Thank you</p>';
        $message .= '<p>The MyPetsProfile&trade; Directory</br >Team</br >Hello@MyPetsProfile.com</p>';
        $message .= '<p>https://mypetsprofile.com/</p>';

        wp_mail($email, 'Welcome to MyPetsProfile™', stripslashes($message), "Content-Type: text/html; charset=UTF-8");
    }
}

new Co_Authors;
