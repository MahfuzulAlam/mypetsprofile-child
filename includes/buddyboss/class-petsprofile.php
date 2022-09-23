<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class MPP_Petsprofile
{
    public function __construct()
    {
        add_shortcode('mypetsprofile-id-qrcode', array($this, 'mypetsprofile_id_qrcode'));
        add_shortcode('mypetsprofile-id-display', array($this, 'mypetsprofile_id_display'));

        add_action('wp_ajax_mypetsprofile_registration', array($this, 'ajax_mypetsprofile_registration'));
        add_action('wp_ajax_nopriv_mypetsprofile_registration', array($this, 'ajax_mypetsprofile_registration'));
    }

    /**
     * GLOBAL VARIABLES
     */
    private $petsprofile_id = 'mypetsprofile-id';
    private $petsprofile_housing_id = 'petsprofile-housing-id';
    private $petsprofile_travel_id = 'petsprofile-travel-id';
    private $petsprofile_points_id = 'petsprofile-points-id';
    private $petsprofile_health_id = 'petsprofile-health-id';
    private $petsprofile_license_id = 'petsprofile-license-id';
    private $petsprofile_service_id = 'petsprofile-service-animal-id';

    /**
     * MYPETPROFILE ID QRCODE
     */
    public function mypetsprofile_id_qrcode($atts)
    {
        $args = $this->get_qrcode_args($atts);

        ob_start();
        if (is_user_logged_in()) :
            $this->get_mypetsprofile_header();
            $title = isset($atts['title']) ? $atts['title'] : 'PetsProfile ID';
            $this->get_profile_title($atts);
            echo do_shortcode('[kaya_qrcode content="' . $args['link'] . '" align="aligncenter" title_align="aligncenter" size="400"]');
            echo '<div class="mypetsprofile-action"><a class="button update-info" href="' . $args['update_link'] . '" style="">Update Information</a><br>';
            echo '<a class="button mpp-copy-link" data-qrcode="' . $args['link'] . '" href="#">Share Link</a> <br><span class="mpp-copy-link-status"></span></div>';
        endif;
        return ob_get_clean();
    }

    /**
     * GET QRCODE ARGS
     */
    public function get_qrcode_args($atts = [])
    {
        $type = isset($atts['type']) && !empty($atts['type']) ? $atts['type'] : 'general';
        $args = [];
        switch ($type) {
            case 'general':
                $args['title'] = 'MyPetsProfile ID';
                $args['link'] = home_url('/' . $this->petsprofile_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_id . '/?action=update');
                break;
            case 'housing':
                $args['title'] = 'MyPetsProfile Housing ID';
                $args['link'] = home_url('/' . $this->petsprofile_housing_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_housing_id . '/?action=update');
                break;
            case 'travel':
                $args['title'] = 'MyPetsProfile Travel ID';
                $args['link'] = home_url('/' . $this->petsprofile_travel_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_travel_id . '/?action=update');
                break;
            case 'points':
                $args['title'] = 'MyPetsProfile Points ID';
                $args['link'] = home_url('/' . $this->petsprofile_points_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_points_id . '/?action=update');
                break;
            case 'health':
                $args['title'] = 'MyPetsProfile Health ID';
                $args['link'] = home_url('/' . $this->petsprofile_health_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_health_id . '/?action=update');
                break;
            case 'license':
                $args['title'] = 'MyPetsProfile License ID';
                $args['link'] = home_url('/' . $this->petsprofile_license_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_license_id . '/?action=update');
                break;
            case 'service':
                $args['title'] = 'MyPetsProfile Service Animal ID';
                $args['link'] = home_url('/' . $this->petsprofile_service_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_service_id . '/?action=update');
                break;
            default:
                $args['title'] = 'MyPetsProfile ID';
                $args['link'] = home_url('/' . $this->petsprofile_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
                $args['update_link'] = home_url('/' . $this->petsprofile_id . '/?action=update');
                break;
        }
        return $args;
    }

    /**
     * MYPETSMROFILE ID DISPLAY
     */
    public function mypetsprofile_id_display($atts = [])
    {
        ob_start();
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
            $this->mypetsprofile_update_form($atts);
        } else {
            $this->mypetsprofile_display_information($atts);
        }
        return ob_get_clean();
    }

    /**
     * MYPETSPROFILE UPDATE FORM
     */
    public function mypetsprofile_update_form($atts = [])
    {
        $member_id = is_user_logged_in() ? get_current_user_id() : 0;
        $field_group = [];
        if (isset($atts['fields']) && !empty($atts['fields'])) {
            $field_group = explode(',', $atts['fields']);
        } else {
            $field_group = [1, 2, 3, 4];
        }

        // UPDATE DATA
        $this->mypetsprofile_update_form_data();

?>
        <form id="mpp_profile_box" class="mpp_profile_box" method="post">
            <h2 class="form_title"><?php echo isset($atts['title']) ? $atts['title'] : ''; ?></h2>
            <?php
            if ($member_id || current_user_can('administrator')) {
                foreach ($field_group as $field_id) {
                    $field = xprofile_get_field($field_id, $member_id);
                    $field_value = in_array($field->type, array("telephone", "url", "email")) ? BP_XProfile_ProfileData::get_value_byid($field->id, $member_id) : xprofile_get_field_data($field->id, $member_id);
                    $visibility_level =  xprofile_get_field_visibility_level($field->id, $member_id);
            ?>
                    <div class="mpp-profile-field">
                        <div class="mpp-profile-header">
                            <h5><?php echo $field->alternate_name ? $field->alternate_name : $field->name; ?></h5>
                            <?php if ($field_id !== 1) : ?>
                                <a class="mpp-change-visibility mpp-change-visibility-<?php echo $field->id; ?>" href="#" data-field="<?php echo $field->id; ?>" data_user="<?php echo $member_id; ?>" data-visibility="<?php echo $visibility_level; ?>">
                                    <span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="mpp-profile-body">
                            <div class="mpp-profile-field-description">
                                <?php echo $field->description; ?>
                            </div>
                            <?php
                            mpp_profile_field_html($field, $field_value, $member_id);
                            ?>
                            <span class="mpp-profile-field-visibility"><span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span> <?php echo mpp_profile_field_visibility_label($visibility_level); ?></span>
                            <input type="hidden" class="mpp_visibility_input_value mpp_visibility_<?php echo $field->id; ?>" name="mpp_visibility_<?php echo $field->id; ?>" value="" />
                        </div>
                    </div>
                <?php } ?>
                <div class="dna_form_submit_buttons">
                    <input type="submit" class="button mpp_dna_form_submitted" value="Update" name="mypetsprofile_form_submitted_sava_data" />
                </div>
            <?php
            }
            ?>
        </form>
    <?php
    }

    /**
     * SAVE MYPETSPROFILE DATA
     */
    public function mypetsprofile_update_form_data()
    {
        if (is_user_logged_in()) {
            // Export CSV From DNA PROFILE
            if (isset($_POST['mypetsprofile_form_submitted_sava_data']) && !empty($_POST['mypetsprofile_form_submitted_sava_data'])) {
                $member_id = get_current_user_id();
                if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
                    $profile_fields = $_POST['mpp_profile_box'];
                    foreach ($profile_fields as $field_id => $field_value) {
                        $field_options = xprofile_get_field($field_id, $member_id);
                        if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';

                        // Save User Data
                        if (get_current_user_id() == $member_id) {
                            xprofile_set_field_data($field_id, $member_id, $field_value);
                            if (isset($_POST['mpp_visibility_' . $field_id]) && !empty($_POST['mpp_visibility_' . $field_id])) {
                                xprofile_set_field_visibility_level($field_id, $member_id, $_POST['mpp_visibility_' . $field_id]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * MYPETSPROFILE DISPLAY INFORMATION
     */

    public function mypetsprofile_display_information($atts = [])
    {
        $registered = isset($_REQUEST['registered']) && !empty($_REQUEST['registered']) ? $_REQUEST['registered'] : '';
        if ($registered == 'yes' || is_user_logged_in()) {
            $this->mypetsprofile_display_information_html($atts);
        } else {
            $this->mypetsprofile_display_information_registration($atts);
        }
    }

    public function mypetsprofile_display_information_html($atts = [])
    {
        $member_id = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        if (!$member_id) return;
        $secret_key = isset($_REQUEST['secret']) && !empty($_REQUEST['secret']) ? $_REQUEST['secret'] : 0;
        if (!$secret_key) return;
        $mpp_user_secret = get_user_meta($member_id, 'mpp_user_secret', true);
        if (!$mpp_user_secret || empty($mpp_user_secret)) return;
        if ($mpp_user_secret !== $secret_key) return;

        $field_group = [];
        if (isset($atts['fields']) && !empty($atts['fields'])) {
            $field_group = explode(',', $atts['fields']);
        } else {
            $field_group = [1, 2, 3, 4];
        }

    ?>
        <div class="mypetsprofile-display-info">
            <?php
            $this->get_mypetsprofile_header();
            $this->get_profile_title($atts);
            ?>

            <?php
            foreach ($field_group as $field_id) {
                $field = xprofile_get_field($field_id, $member_id);
                $field_value = in_array($field->type, array("telephone", "url", "email")) ? BP_XProfile_ProfileData::get_value_byid($field->id, $member_id) : xprofile_get_field_data($field->id, $member_id);
                $visibility_level =  xprofile_get_field_visibility_level($field->id, $member_id);
                //public loggedin friends adminsonly
                $visibility = false;
                if ($visibility_level == 'adminsonly') {
                    if (is_user_logged_in() && $member_id == get_current_user_id()) $visibility = true;
                } elseif ($visibility_level == 'friends') {
                    if (is_user_logged_in()) {
                        if ($member_id  == get_current_user_id() || friends_check_friendship($member_id, get_current_user_id())) {
                            $visibility = true;
                        }
                    }
                } elseif ($visibility_level == 'loggedin') {
                    if (is_user_logged_in()) $visibility = true;
                } else {
                    $visibility = true;
                }

                //e_var_dump($visibility_level);

                if ($visibility) {
            ?>
                    <div class="info-block">
                        <label class="info-label"><?php echo $field->alternate_name ? $field->alternate_name : $field->name; ?></label>
                        <p class="info-value"><?php echo !empty($field_value) ? $field_value : '-'; ?></p>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    <?php
    }

    public function mypetsprofile_display_information_registration($atts = [])
    {
    ?>
        <div class="mypetsprofile_registration_form">
            <h3 class="info-title"><?php echo isset($atts['title']) ? $atts['title'] : ''; ?></h3>
            <p class="info-detail">
                Welcome to MyPetsProfile™️<br>
                To view the pet profile information provided, please enter your email address for immediate access.<br>
                Thank you
            </p>
            <div class="mypetsprofile_registration_field">
                <input type="email" name="email" class="email" id="email_address" placeholder="Email Address" />
                <span class="description">Please enter your email address.</span>
            </div>
            <div class="mypetsprofile_registration_field">
                <input type="email" name="confirm-email" class="email" id="confirm_email" placeholder="Confirm Email Address" />
                <span class="description">Please confirm your email address.</span>
            </div>
            <div class="mypetsprofile_registration_field">
                <span class="description error-message" id="error_message"></span>
            </div>
            <div class="mypetsprofile_registration_field">
                <span class="description success-message" id="success_message"></span>
            </div>
            <div class="mypetsprofile_registration_field">
                <a class="button" id="submit_email" href="#">Submit Email</a>
            </div>
        </div>
    <?php
    }

    /**
     * MYPETSPROFILE HEADER
     */
    public function get_mypetsprofile_header()
    {
        $user_id = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : 0;

        if (!$user_id && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) return;

        $avatar_image = bp_core_fetch_avatar(
            array(
                'item_id' => $user_id, // id of user for desired avatar
                'type'        => 'full',
                'html'       => FALSE,     // FALSE = return url, TRUE (default) = return img html
            )
        );

        // $cover_image = bp_attachments_get_attachment('url', array(
        //     'object_dir' => 'members',
        //     'item_id'    => $user_id,
        //     'type'       => 'cover-image',
        // ));

        $user = get_userdata($user_id);
        $username = $user->nickname;
    ?>
        <div class="mypetsprofile-user-header">
            <div class="mypetsprofile-user-avatar">
                <img src="<?php echo $avatar_image; ?>" />
            </div>
            <h3><?php echo $username; ?></h3>
            <h4><?php echo '@' . $username; ?></h4>
        </div>
<?php
    }

    /**
     * AJAX MYPETSPROFILE REGISTRATION
     */
    public function ajax_mypetsprofile_registration()
    {
        $email = isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : '';
        $response = ["result" => false];
        if (!empty($email)) {
            if (email_exists($email)) {
                $response['result'] = true;
                $response['registration'] = false;
                $response['status'] = 'exists';
            } else {
                $username = $this->mpp_generate_username('', '', $email);
                $password = wp_generate_password(8);
                $meta['user_pass'] = wp_hash_password($password);
                $user_id = bp_core_signup_user($username, $password, $email, $meta);
                if ($user_id) {
                    $response['result'] = true;
                    $response['registration'] = true;
                    $response['status'] = 'registered';
                }
            }
        }
        echo json_encode($response);
        die();
    }

    /**
     * MPP GENERATE USERNAME
     */
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

    /**
     * MPP SLUGIFY TEXT
     */
    public function mpp_slugify_text($text, $divider = '-')
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

    /**
     * GET THE TITLE
     */
    public function get_profile_title($atts = [])
    {
        $type = isset($atts['type']) && !empty($atts['type']) ? $atts['type'] : 'general';
        $title = isset($atts['title']) && !empty($atts['title']) ? $atts['title'] : '';
        $logo = '';
        switch ($type) {
            case 'health':
                $logo = get_stylesheet_directory_uri() . '/assets/img/petsprofile-ids/health.png';
                break;
            case 'license':
                $logo = get_stylesheet_directory_uri() . '/assets/img/petsprofile-ids/license.png';
                break;
            case 'pasport':
                $logo = get_stylesheet_directory_uri() . '/assets/img/petsprofile-ids/passport.png';
                break;
            case 'points':
                $logo = get_stylesheet_directory_uri() . '/assets/img/petsprofile-ids/points.png';
                break;
            case 'travel':
                $logo = get_stylesheet_directory_uri() . '/assets/img/petsprofile-ids/travel.png';
                break;
            default:
                $logo = '';
                break;
        }

        if (!empty($logo)) {
            echo '<div class="mypetsprofile-id-image-wrapper"><img src="' . $logo . '" class="mypetsprofile-id-logo"/></div>';
        } else {
            echo '<h3 class="mypetsprofile-id-title">' . $title . '</h3>';
        }
    }

    /**
     * GET/GENERATE USER SECRET KEY
     */
    public function get_user_secret_key()
    {
        if (is_user_logged_in()) {
            $mpp_user_secret = get_user_meta(get_current_user_id(), 'mpp_user_secret', true);
            if (!$mpp_user_secret || empty($mpp_user_secret)) {
                $mpp_user_secret = rand(10000, 99999);
                update_user_meta(get_current_user_id(), 'mpp_user_secret', $mpp_user_secret);
            }
            return $mpp_user_secret;
        }
    }
}

new MPP_Petsprofile;
