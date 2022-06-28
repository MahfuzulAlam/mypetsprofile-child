<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class Referral_Messenger
{

    public function __construct()
    {
        // APPLY AS REFFERAL SHORTCODE
        add_shortcode('apply-as-refferal', array($this, 'shortcode_apply_as_refferal'));


        // AJAX CALLS
        // APPLY AS REFERRAL
        add_action('wp_ajax_mpp_apply_referral', array($this, 'mpp_ajax_apply_referral'));
    }

    // APPLY AS REFFERAL SHORTCODE
    public function shortcode_apply_as_refferal()
    {
        $group_id = isset($_GET['group']) ? $_GET['group'] : false;
        if (!$group_id) return;

        $user_id = get_current_user_id();

        //if (groups_is_user_member($user_id, $group_id)) return;

        $spokesperson = groups_get_groupmeta($group_id, 'mpp_spokeperson', true);
        $spokesperson = $spokesperson && !empty($spokeperson) ? $spokeperson : array();
        $exist = false;

        if (in_array($user_id, $spokesperson)) {
            $exist = true;
        }

        $applied_speakers = groups_get_groupmeta($group_id, 'mpp_applied_speakers', true);
        $applied_speakers = $applied_speakers && !empty($applied_speakers) ? $applied_speakers : array();
        $applied = false;

        if (in_array($user_id, $applied_speakers)) {
            $applied = true;
        }

        $group = groups_get_group($group_id);

        ob_start();
?>
        <h2><?php echo $group->name; ?></h2>
        <?php
        if (!$exist && !$applied) :
        ?>
            <a class="btn button" id="apply_as_referral" data-user="<?php echo $user_id; ?>" data-group="<?php echo $group_id; ?>">Apply For Referral Program</a>
        <?php
        elseif (!$applied) :
        ?>
            <a class="btn button" id="leave_from_referral" data-user="<?php echo $user_id; ?>" data-group="<?php echo $group_id; ?>">Leave From Referral Program</a>
        <?php
        else :
        ?>
            <p>You have already applied to join the referral program.</p>
<?php
        endif;
        return ob_get_clean();
    }

    // AJAX APPLY AS REFERRAL
    public function mpp_ajax_apply_referral()
    {
        $result = false;
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $group_id = isset($_REQUEST['group']) ? $_REQUEST['group'] : 0;
        $applied_speakers = groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) ? groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) : array();
        if ($applied_speakers && !empty($applied_speakers)) {
            if (!in_array($user_id, $applied_speakers)) {
                array_push($applied_speakers, $user_id);
                groups_update_groupmeta($group_id, 'mpp_applied_speakers', $applied_speakers);
                $result = true;
            }
        } else {
            array_push($applied_speakers, $user_id);
            groups_update_groupmeta($group_id, 'mpp_applied_speakers', $applied_speakers);
            $result = true;
        }
        echo json_encode(array('result' => $result, 'speakers' => $group_id));
        die();
    }
}

new Referral_Messenger;
