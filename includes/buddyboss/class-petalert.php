<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class MPP_Pet_Alert
{
    public function __construct()
    {
        // MPP ALERT SHORTCODE
        add_shortcode('my-pet-alert', array($this, 'my_pets_alert_shortcode'));
        // MPP ALERT AJAX CALL
        add_action('wp_ajax_send_mpp_pets_alert', array($this, 'send_mpp_pets_alert'));
    }

    // MyPetsAlert

    public function my_pets_alert_shortcode()
    {

        // if (isset($_POST['alert_message']) && !empty($_POST['alert_message'])) {
        //     $alert_message = $_POST['alert_message'];
        //     $this->mpp_send_pet_alert_message($alert_message);
        // }

        ob_start();
?>
        <form method="post" id="mpp_pets_alert">
            <div class="mpp-profile-field mpp-address-field">
                <div class="mpp-profile-body">
                    <span class="label">Message</span>
                    <textarea class="mpp-profile-field-html" id="mpp_alert_message" name="alert_message"></textarea>
                    <input type="hidden" id="mpp_alert_nonce" value="<?php echo wp_create_nonce('mpp_alert_nonce'); ?>">
                </div>
            </div>
            <div id="mpp_loading">Sending PetAlerts ...</div>
            <div id="mpp_warning"></div>
            <input type="submit" class="button" value="Send Alert" name="mpp_pets_alert_submitted" id="mpp_pets_alert_submitted" />
        </form>
    <?php
        return ob_get_clean();
    }

    // MPP SEND PET ALERT MESSAGES

    public function mpp_send_pet_alert_message($messages)
    {
        return $this->mpp_send_pet_alert_emails($messages);
        //do_action('mpp_pet_missing', get_current_user_id(), array());
    }


    // MPP SEND EMAILS TO THE USER - PET ALERT MESSAGE
    public function mpp_send_pet_alert_emails($messages)
    {
        //$field_id = 100;
        $field_id = 1073;
        $user_emails = array('hello@mypetsprofile.com');
        //$user_emails = array('a_mahfuzul@icloud.com');
        $user_city = BP_XProfile_ProfileData::get_value_byid($field_id, get_current_user_id());
        if ($user_city && !empty($user_city)) {
            $user_query = new BP_User_Query(
                array(
                    'per_page' => 0,
                    'xprofile_query' => array(
                        'relation' => 'AND',
                        array(
                            'field_id' => $field_id,
                            'value' => $user_city,
                            'compare' => 'LIKE',
                        ),
                    ),
                )
            );

            if ($user_query && $user_query->total_users) {
                $users = $user_query->results;
                foreach ($users as $user) {
                    $user_emails[] = $user->user_email;
                }
            }
        }
        if (count($user_emails)) {
            $user_emails = array_unique($user_emails);
            $to = $user_emails;
            $subject = 'MyPetsAlert';
            $body = $this->mpp_generate_petsalert_message($messages, get_current_user_id());
            //$body = $messages;
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: MyPetsProfile <hello@mypetsprofile.com>');

            $email_sent = wp_mail($to, $subject, $body, $headers);
            if ($email_sent) return true;
        }
        return false;
    }


    public function mpp_generate_petsalert_message($messages, $user_id)
    {
        $user_fields = $this->mpp_get_petsalert_fields($user_id);
        $pet_name = bp_core_get_username($user_id);
        $pet_link = bbp_get_user_profile_url($user_id);
        ob_start();
    ?>
        <div style="margin:0px; border: 2px solid gray; padding: 20px">
            <div style="text-align: center">
                <h1>PetAlert</h1>
                <h4>"<?php echo $pet_name; ?>" has gone missing near you! Please keep a watchful eye.</h4>
                <img src="<?php bp_loggedin_user_avatar('html=false'); ?>" style="height:150px" />
                <p>"<?php echo $messages; ?>"</p>
            </div>
            <?php if (count($user_fields) > 0) : ?>
                <h2>MyPetsProfile™️</h2>
                <!-- Put the Pet type to the top -->
                <?php foreach ($user_fields as $key => $field) : ?>
                    <p>
                        <span style="color: #000"><?php echo $key; ?></span>:
                        <span style="color: gray"><?php echo $field; ?></span>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>
            <p>
                <span style="color: #000">Profile Link</span>:
                <span style="color: gray"><a href="<?php echo $pet_link; ?>"><?php echo $pet_link; ?></a></span>
            </p>
        </div>
<?php
        return ob_get_clean();
    }

    public function mpp_get_petsalert_fields($user_id)
    {
        $mpp_fields = array();
        $field_groups = bp_profile_get_field_groups();
        foreach ($field_groups as $field_group) {
            //if ($field_group->id == 1 || $field_group->id == 4) {
            if ($field_group->id == 6 || $field_group->id == 24) {
                foreach ($field_group->fields as $field) {
                    $field_value = BP_XProfile_ProfileData::get_value_byid($field->id, $user_id);
                    if (!empty($field_value)) $mpp_fields[$field->name] = $field_value;
                }
            }
        }
        return $mpp_fields;
    }

    // AJAX CALL TO SEND MESSAGE
    public function send_mpp_pets_alert()
    {
        // Nonce Check
        if (!wp_verify_nonce($_REQUEST['nonce'], "mpp_alert_nonce")) {
            exit("Sorry, cant' access!!!");
        }

        $success = false;

        $message = isset($_REQUEST['message']) && !empty($_REQUEST['message']) ? $_REQUEST['message'] : '';

        if (!empty($message)) {
            $mpp_alert = $this->mpp_send_pet_alert_message($message);
            if ($mpp_alert) $success = true;
        }

        echo json_encode(array('success' => $success));

        die();
    }
}

new MPP_Pet_Alert;
