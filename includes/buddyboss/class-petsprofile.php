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
    }

    /**
     * GLOBAL VARIABLES
     */
    private $petsprofile_id = 'mypetsprofile-id';

    /**
     * MYPETPROFILE ID QRCODE
     */
    public function mypetsprofile_id_qrcode()
    {
        ob_start();
        if (is_user_logged_in()) :
            $this->get_mypetsprofile_header();
            $link = home_url('/' . $this->petsprofile_id . '/?user=' . get_current_user_id() . '&secret=' . $this->get_user_secret_key());
            $update_link = home_url('/' . $this->petsprofile_id . '/?action=update');
            echo '<h3 class="mypetsprofile-title">MyPetsProfile ID</h3>';
            echo do_shortcode('[kaya_qrcode content="' . $link . '" align="aligncenter" title_align="aligncenter" size="400"]');
            echo '<div class="mypetsprofile-action"><a class="button update-info" href="' . $update_link . '" style="">Update Information</a><br>';
            echo '<a class="button mpp-copy-link" data-qrcode="' . $link . '" href="#">Copy Link</a> <br><span class="mpp-copy-link-status"></span></div>';
        endif;
        return ob_get_clean();
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
                    <input type="submit" class="button mpp_dna_form_submitted" value="Update" name="mpp_dna_form_submitted_sava_data" />
                </div>
            <?php
            }
            ?>
        </form>
    <?php
    }

    /**
     * MYPETSPROFILE DISPLAY INFORMATION
     */
    public function mypetsprofile_display_information($atts = [])
    {
        $member_id = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        if (!$member_id) return;

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
            ?>
            <h3 class="info-title"><?php echo isset($atts['title']) ? $atts['title'] : ''; ?></h3>

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
        $username = $user->user_login;
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
