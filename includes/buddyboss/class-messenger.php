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
        // REFERRAL APPROVAL
        add_shortcode('refferal-approval', array($this, 'shortcode_refferal_approval'));
        // SHOW AVAILABLE SPOKEPERSON
        add_shortcode('available-spokepersion', array($this, 'shortcode_available_spokepersion'));

        // AJAX CALLS
        // APPLY AS REFERRAL
        add_action('wp_ajax_mpp_apply_referral', array($this, 'mpp_ajax_apply_referral'));
        // ACCEPT REFERRAL
        add_action('wp_ajax_mpp_accept_referral', array($this, 'mpp_ajax_accept_referral'));
        // REJECT REFERRAL
        add_action('wp_ajax_mpp_reject_referral', array($this, 'mpp_ajax_reject_referral'));
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

    // REFERAL APPROVAL
    public function shortcode_refferal_approval()
    {
        $group_id = bp_get_current_group_id();
        if (!$group_id || empty($group_id)) return;
        $applied_speakers = groups_get_groupmeta($group_id, 'mpp_applied_speakers', true);
        ob_start();
        if ($applied_speakers && !empty($applied_speakers)) :
        ?>
            <table class="mpp-applied-speaker">
                <?php
                foreach ($applied_speakers as $user_id) :
                    $user = get_user_by('id', $user_id);
                ?>
                    <tr>
                        <td>
                            <?php echo bp_core_fetch_avatar(
                                array(
                                    'item_id' => $user_id, // id of user for desired avatar
                                    'type'    => 'thumb',
                                    'html'   => true     // FALSE = return url, TRUE (default) = return img html
                                )
                            ); ?>
                        </td>
                        <td><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")";
                            ?></td>
                        <td>
                            <a class="btn button mpp-referral-approval" data-user="<?php echo $user_id; ?>" data-group="<?php echo $group_id; ?>" data-type="accept">Accept</a>
                            <a class="btn button mpp-referral-approval" data-user="<?php echo $user_id; ?>" data-group="<?php echo $group_id; ?>" data-type="reject">Reject</a>
                        </td>
                    </tr>
                <?php
                endforeach;
                ?>
            </table>
        <?php
        endif;
        return ob_get_clean();
    }

    // AVAIALLE SPOKEPERSION
    public function shortcode_available_spokepersion()
    {
        $listing_id = get_the_ID();
        if (empty($listing_id)) return;
        $group_id = get_post_meta($listing_id, "_bb_group_id", true);
        if (!$group_id || empty($group_id)) return;
        $spokeperson = groups_get_groupmeta($group_id, 'mpp_spokeperson', true);
        if (!$spokeperson || empty($spokeperson)) return;

        ob_start();
        ?>
        <table class="mpp-applied-speaker">
            <?php
            foreach ($spokeperson as $user_id) :
                $user = get_user_by('id', $user_id);
            ?>
                <tr>
                    <td>
                        <?php echo bp_core_fetch_avatar(
                            array(
                                'item_id' => $user_id, // id of user for desired avatar
                                'type'    => 'thumb',
                                'html'   => true     // FALSE = return url, TRUE (default) = return img html
                            )
                        ); ?>
                    </td>
                    <td><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")";
                        ?></td>
                    <td>
                        <a class="btn button mpp-start-chatting" data-user="<?php echo $user_id; ?>" data-group="<?php echo $group_id; ?>" data-type="accept">Start Chatting</a>
                    </td>
                </tr>
            <?php
            endforeach;
            ?>
        </table>
        <div class="messenger-container">
            <div class="messenger-header">
                <h4>Chat with Username</h4>
            </div>
            <div class="messenger-body">
                <div class="chat-line sender">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
                <div class="chat-line sender">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
                <div class="chat-line receiver">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
                <div class="chat-line sender">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
                <div class="chat-line sender">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
                <div class="chat-line receiver">
                    <div class="chat-name">Name</div>
                    <div class="chat-message"><span>Hello</span></div>
                </div>
            </div>
            <div class="messenger-footer">
                <input type="text" id="messenger_message" />
            </div>
        </div>
<?php
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

    // AJAX ACCEPT REFERRAL
    public function mpp_ajax_accept_referral()
    {
        $result = false;
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $group_id = isset($_REQUEST['group']) ? $_REQUEST['group'] : 0;
        $applied_speakers = groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) ? groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) : array();
        if ($applied_speakers && !empty($applied_speakers)) {
            if (in_array($user_id, $applied_speakers)) {
                // Remove form application list
                $applied_key = array_search($user_id, $applied_speakers, true);
                if ($applied_key !== false) {
                    unset($applied_speakers[$applied_key]);
                }
                groups_update_groupmeta($group_id, 'mpp_applied_speakers', $applied_speakers);

                // ADD to the existing list
                $spokesperson = groups_get_groupmeta($group_id, 'mpp_spokeperson', true);
                $spokesperson = $spokesperson && !empty($spokeperson) ? $spokeperson : array();

                if (empty($spokeperson) || !in_array($user_id, $spokesperson)) {
                    array_push($spokesperson, $user_id);
                    groups_update_groupmeta($group_id, 'mpp_spokeperson', $spokesperson);
                    $result = true;
                }
            }
        }
        echo json_encode(array('result' => $result, 'speakers' => $group_id));
        die();
    }

    // AJAX REJECT REFERRAL
    public function mpp_ajax_reject_referral()
    {
        $result = false;
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $group_id = isset($_REQUEST['group']) ? $_REQUEST['group'] : 0;
        $applied_speakers = groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) ? groups_get_groupmeta($group_id, 'mpp_applied_speakers', true) : array();
        if ($applied_speakers && !empty($applied_speakers)) {
            if (in_array($user_id, $applied_speakers)) {
                // Remove form application list
                $applied_key = array_search($user_id, $applied_speakers, true);
                if ($applied_key !== false) {
                    unset($applied_speakers[$applied_key]);
                }
                groups_update_groupmeta($group_id, 'mpp_applied_speakers', $applied_speakers);
                $result = true;
            }
        }
        echo json_encode(array('result' => $result, 'speakers' => $group_id));
        die();
    }
}

new Referral_Messenger;
