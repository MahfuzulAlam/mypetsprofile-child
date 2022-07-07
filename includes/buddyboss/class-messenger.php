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
        add_shortcode('available-spokespersons', array($this, 'shortcode_available_spokespersons'));
        // SPOKEPERSON APPLICATION FORM
        add_shortcode('spokespersons-application-form', array($this, 'shortcode_spokespersons_application_form'));
        // PROPERTY OWNER DISPLAY
        add_shortcode('property-owner-dashboard', array($this, 'shortcode_property_owner_dashboard'));
        // DISPLAY CHAT MODULE
        add_shortcode('display-chat-module', array($this, 'shortcode_display_chat_module'));

        // AJAX CALLS
        // APPLY AS REFERRAL
        add_action('wp_ajax_mpp_apply_referral', array($this, 'mpp_ajax_apply_referral'));
        // ACCEPT REFERRAL
        add_action('wp_ajax_mpp_accept_referral', array($this, 'mpp_ajax_accept_referral'));
        // REJECT REFERRAL
        add_action('wp_ajax_mpp_reject_referral', array($this, 'mpp_ajax_reject_referral'));
        // Insert Message
        add_action('wp_ajax_mpp_insert_message_row', array($this, 'mpp_insert_message_row'));
        // GET UNREAD MESSAGE
        add_action('wp_ajax_mpp_get_unread_message', array($this, 'mpp_get_unread_message'));
        // DISPLAY ALL MESSAGES
        add_action('wp_ajax_mpp_retrive_messages', array($this, 'mpp_retrive_messages'));
        // PROPERTY SEARCH
        add_action('wp_ajax_mpp_property_search', array($this, 'mpp_property_search'));

        // APPLY AS REFERRAL LISTING
        //add_action('wp_ajax_mpp_apply_referral_listing', array($this, 'mpp_ajax_apply_referral_listing'));
        // ACCEPT REFERRAL LISTING
        add_action('wp_ajax_mpp_accept_referral_listing', array($this, 'mpp_ajax_accept_referral_listing'));
        // REJECT REFERRAL LISTING
        add_action('wp_ajax_mpp_reject_referral_listing', array($this, 'mpp_ajax_reject_referral_listing'));
        // REMOVE REFERRAL LISTING
        add_action('wp_ajax_mpp_remove_referral_listing', array($this, 'mpp_ajax_remove_referral_listing'));
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
        <div>
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
            ?>
        </div>
        <?php
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
    public function shortcode_available_spokespersons()
    {
        $listing_id = get_the_ID();
        if (empty($listing_id)) return;
        // $group_id = get_post_meta($listing_id, "_bb_group_id", true);
        // if (!$group_id || empty($group_id)) return;
        $spokespersons = get_post_meta($listing_id, 'mpp_spokespersons', true);
        if (!$spokespersons || empty($spokespersons)) return;

        ob_start();
        ?>
        <table class="mpp-applied-speaker">
            <?php
            foreach ($spokespersons as $spokeperson) :
                $user_id = $spokeperson['user_id'];
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
                    <td class="username"><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")";
                                            ?></td>
                    <td>
                        <a class="btn button mpp-start-chatting" data-sender="<?php echo bp_loggedin_user_id(); ?>" data-recipient="<?php echo $user_id; ?>" data-listing="<?php echo $listing_id; ?>" data-type="accept">Start Chatting</a>
                        <!-- <a class="btn button mpp-start-chatting" data-sender="<?php echo bp_loggedin_user_id(); ?>" data-recipient="5" data-listing="<?php echo $listing_id; ?>" data-type="accept">Start Chatting</a> -->
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
                <p id="loading_messages" style="margin: 15px;">Loading messages...</p>
                <section class="discussion">

                    <?php

                    $bd_message = new MPP_Database;
                    $messages = $bd_message->retrieve_messages(3, 1);

                    $prev_sender = $next_sender = 0;
                    if (!$messages) {
                        foreach ($messages as $key => $message) {
                            $prev_sender = $key > 0 ? $messages[$key - 1]->sender_id : 0;
                            $next_sender = $key < count($messages) - 1 ? $messages[$key + 1]->sender_id : 0;

                            $owner = $message->sender_id == bp_loggedin_user_id() ? 'sender' : 'recipient';
                            $message_position = '';
                            if ($prev_sender !== $message->sender_id) $message_position = 'first';
                            if ($prev_sender == $message->sender_id) $message_position = 'middle';
                            if ($next_sender !== $message->sender_id) $message_position = 'last';
                            if ($next_sender !== $message->sender_id && $prev_sender !== $message->sender_id) $message_position = 'single';
                    ?>
                            <div class="bubble <?php echo $owner; ?> <?php echo $message_position; ?>"><?php echo $message->message; ?></div>
                    <?php

                            // Change status
                            if ($message->status == 1 && $message->recipient_id == bp_loggedin_user_id()) {
                                //$bd_message->update_status($message->id, 2); // 2 = read
                            }
                        }
                    }

                    ?>


                    <!-- <div class="bubble sender first">Hello</div>
                    <div class="bubble sender last">This is a CSS demo of the Messenger chat bubbles, that merge when stacked together.</div>

                    <div class="bubble recipient first">Oh that's cool!</div>
                    <div class="bubble recipient last">Did you use JavaScript to perform that kind of effect?</div>

                    <div class="bubble sender first">No, that's full CSS3!</div>
                    <div class="bubble sender middle">(Take a look to the 'JS' section of this Pen... it's empty! 😃</div>
                    <div class="bubble sender last">And it's also really lightweight!</div>

                    <div class="bubble recipient">Dope!</div>

                    <div class="bubble sender first">Yeah, but I still didn't succeed to get rid of these stupid .first and .last classes.</div>
                    <div class="bubble sender middle">The only solution I see is using JS, or a &lt;div&gt; to group elements together, but I don't want to ...</div>
                    <div class="bubble sender last">I think it's more transparent and easier to group .bubble elements in the same parent.</div> -->

                </section>
            </div>
            <div class="messenger-footer">
                <form class="messenger-form" id="mpp_messenger_form">
                    <textarea id="messenger_message" name="messenger_message"></textarea>
                    <input type="hidden" name="msg_info" id="msg_info" value='<?php echo json_encode(array('sender' => bp_loggedin_user_id(), 'recipient' => 1, 'group' => 174)); ?>' />
                    <button type="submit"><i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    // SPOKESPERSONS APPLICATION FORM
    public function shortcode_spokespersons_application_form()
    {

        if (!is_user_logged_in()) return;

        if (isset($_POST['spokeperson_submitted'])) :
            $property_id = isset($_POST['property_id']) && !empty($_POST['property_id']) ? $_POST['property_id'] : '';
            $unit_number = isset($_POST['unit_number']) && !empty($_POST['unit_number']) ? $_POST['unit_number'] : '';
            $lease_start = isset($_POST['lease_start']) && !empty($_POST['lease_start']) ? $_POST['lease_start'] : '';
            $lease_end = isset($_POST['lease_end']) && !empty($_POST['lease_end']) ? $_POST['lease_end'] : '';
            $phone = isset($_POST['phone']) && !empty($_POST['phone']) ? $_POST['phone'] : '';

            if ($property_id && !empty($property_id)) {
                $args = array(
                    'user_id'   => get_current_user_id(),
                    'property_id' => $property_id,
                    'unit_number' => $unit_number,
                    'lease_start' => $lease_start,
                    'lease_end' => $lease_end,
                    'phone' => $phone
                );

                $inserted  = $this->mpp_apply_as_referral_listing($property_id, $args);
                if ($inserted) echo '<p>You have successfully submitted the application. We will inform you within 24 hours.<p>';
            } else {
                echo '<p>Please insert a Property first!</p>';
            }
        endif;

        ob_start();
    ?>
        <div class="spokesperson-application-form-wrapper">
            <form method="post" id="spokesperson_application_form">
                <div class="select-property directorist-fieldset">
                    <label>Property Name</label>
                    <select class="spokespersons-field spokespersons-property-name" name="property_id" id="property_id" style="width:100%"></select>
                </div>
                <div class="directorist-fieldset">
                    <label for="unit_number">Unit Number</label>
                    <input type="text" class="spokespersons-field" name="unit_number" id="unit_number" />
                </div>
                <div class="directorist-fieldset">
                    <label for="lease_start">Lease Starts</label>
                    <input type="date" class="spokespersons-field" name="lease_start" id="lease_start" />
                </div>
                <div class="directorist-fieldset">
                    <label for="lease_end">Lease Ends</label>
                    <input type="date" class="spokespersons-field" name="lease_end" id="lease_end" />
                </div>
                <div class="directorist-fieldset">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="directorist-fieldset submit-button">
                    <input type="submit" value="Submit" class="button" name="spokeperson_submitted" />
                </div>
            </form>
        </div>
    <?php
        return ob_get_clean();
    }

    // PROPERTY OWNER DASHBOARD
    public function shortcode_property_owner_dashboard()
    {
        $listings = new WP_Query(array(
            'post_type' => ATBDP_POST_TYPE,
            'post_status' => 'publish', // if you don't want drafts to be returned
            'posts_per_page' => -1, // how much to show at once
            'tax_query' => array(
                array(
                    'taxonomy' => ATBDP_DIRECTORY_TYPE,
                    'field'    => 'slug',
                    'terms'    => 'pets-community',
                ),
            ),
            'author' => get_current_user_id(),
        ));
        $properties = [];
        if ($listings->have_posts()) :
            while ($listings->have_posts()) : $listings->the_post();
                // shorten the title a little
                //$title = (mb_strlen($listings->post->post_title) > 50) ? mb_substr($listings->post->post_title, 0, 49) . '...' : $listings->post->post_title;
                $spokespersons = get_post_meta($listings->post->ID, 'mpp_spokespersons', true);
                $applied_speakers = get_post_meta($listings->post->ID, 'mpp_applied_speakers', true);
                if (!empty($spokespersons) || !empty($applied_speakers)) {
                    $properties[] = array(
                        'id' => $listings->post->ID,
                        'title' => $listings->post->post_title,
                        'spokespersons' => $spokespersons,
                        'applied_speakers' => $applied_speakers,
                    ); // array( Post ID, Post Title )
                }
            endwhile;
            wp_reset_postdata();
        endif;
        ob_start();
        if (empty($properties)) echo '<p>No pending application found.</p>';
    ?>
        <div class="property-owner-dashboard">
            <?php foreach ($properties as $property) : ?>
                <div class="mpp-property">
                    <h4 class="property-title"><?php echo $property['title']; ?></h4>
                    <div class="property-spokepersons">
                        <h5>Spokespersons</h5>
                        <div class="mpp-spokepersons">
                            <?php
                            if (!empty($property['spokespersons']) && count($property['spokespersons']) > 0) :
                                foreach ($property['spokespersons'] as $speaker) :
                            ?>
                                    <table class="mpp-applied-speaker">
                                        <?php
                                        $user = get_user_by('id', $speaker['user_id']);
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo bp_core_fetch_avatar(
                                                    array(
                                                        'item_id' => $speaker['user_id'], // id of user for desired avatar
                                                        'type'    => 'thumb',
                                                        'html'   => true     // FALSE = return url, TRUE (default) = return img html
                                                    )
                                                ); ?>
                                            </td>
                                            <td><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")";
                                                ?></td>
                                            <td>
                                                <a class="btn button mpp-referral-reports" href="<?php echo home_url('/chat-module/') . '?chatclient=' . $speaker['user_id']; ?>" data-user="<?php echo $speaker['user_id']; ?>" data-listing="<?php echo $speaker['property_id']; ?>" data-type="reports">Reports</a>
                                                <a class="btn button mpp-referral-remove" data-user="<?php echo $speaker['user_id']; ?>" data-listing="<?php echo $speaker['property_id']; ?>" data-type="remove">Remove</a>
                                            </td>
                                        </tr>
                                    </table>
                                <?php
                                endforeach;
                            else :
                                ?>
                                <p>No SpokesPerson Found</p>
                            <?php
                            endif;
                            ?>
                        </div>
                    </div>
                    <div class="property-applied-speakers">
                        <h5>Applied Speakers</h5>
                        <div class="mpp-applied-speakers">
                            <?php
                            if (!empty($property['applied_speakers']) && count($property['applied_speakers']) > 0) :
                                foreach ($property['applied_speakers'] as $speaker) :
                            ?>
                                    <table class="mpp-applied-speaker">
                                        <?php
                                        $user = get_user_by('id', $speaker['user_id']);
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo bp_core_fetch_avatar(
                                                    array(
                                                        'item_id' => $speaker['user_id'], // id of user for desired avatar
                                                        'type'    => 'thumb',
                                                        'html'   => true     // FALSE = return url, TRUE (default) = return img html
                                                    )
                                                ); ?>
                                            </td>
                                            <td><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")";
                                                ?></td>
                                            <td>
                                                <a class="btn button mpp-referral-approval-listing" data-user="<?php echo $speaker['user_id']; ?>" data-listing="<?php echo $speaker['property_id']; ?>" data-type="accept">Accept</a>
                                                <a class="btn button mpp-referral-approval-listing" data-user="<?php echo $speaker['user_id']; ?>" data-listing="<?php echo $speaker['property_id']; ?>" data-type="reject">Reject</a>
                                            </td>
                                        </tr>
                                    </table>
                                <?php
                                endforeach;
                            else :
                                ?>
                                <p>No pending application found.</p>
                            <?php
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
        return ob_get_clean();
    }

    // DISPLAY CHAT MODULE
    public function shortcode_display_chat_module()
    {
        $db = new MPP_Database;
        $chat_client_id = get_current_user_id();
        $isOwner = false;
        $owner_id = 0;
        $people_list = $db->retrieve_people_list($chat_client_id);
        $first_info = array();

        if (isset($_GET['chatclient']) && !empty($_GET['chatclient'])) {
            $chat_client_id = $_GET['chatclient'];
            $isOwner = true;
            $owner_id = get_current_user_id();
            // More Validation
        }

        ob_start();
    ?>
        <div class="mpp-chat-module <?php if ($isOwner) echo "owner-module"; ?>" id="mpp_chat_module">
            <a href="#" class="btn button" id="show-hide-people"><i class="fa fa-users" aria-hidden="true"></i></a>
            <div class="container">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card chat-app">
                            <div id="plist" class="people-list plist-hidden">
                                <ul class="list-unstyled chat-list mt-2 mb-0">
                                    <?php
                                    if ($people_list && count($people_list) > 0) {
                                        foreach ($people_list as $key => $people) {

                                            $listing_author_id = get_post_field('post_author', $people->listing_id);
                                            if ($isOwner && $owner_id !=  $listing_author_id) continue;
                                            $people_id = $people->sender_id != $chat_client_id ? $people->sender_id : $people->recipient_id;
                                            $user = get_user_by('id', $people_id);
                                            $avatar_url =  bp_core_fetch_avatar(
                                                array(
                                                    'item_id' => $people_id, // id of user for desired avatar
                                                    'type'    => 'thumb',
                                                    'html'   => false     // FALSE = return url, TRUE (default) = return img html
                                                )
                                            );
                                            $info = array(
                                                'name' => $user->data->display_name,
                                                'avatar' => $avatar_url,
                                                'sender' => $chat_client_id,
                                                'recipient' => $people_id,
                                                'listing' => $people->listing_id,
                                            );

                                            if (empty($first_info)) $first_info = $info;
                                    ?>
                                            <li class="clearfix people-block <?php echo $key == 0 ? 'active' : ''; ?>" data-info='<?php echo json_encode($info); ?>'>
                                                <img src="<?php echo $avatar_url; ?>" alt="avatar">
                                                <div class="about">
                                                    <div class="name"><?php echo $user->data->display_name . " (" . $user->data->user_nicename . ")"; ?></div>
                                                    <div class="status"><?php echo get_the_title($people->listing_id); ?></div>
                                                    <!-- <div class="status"> <i class="fa fa-circle offline"></i> left 7 mins ago </div> -->
                                                </div>
                                            </li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php if (!empty($first_info)) : ?>
                                <div class="chat">
                                    <div class="chat-header clearfix">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                                    <img src="<?php echo $first_info['avatar']; ?>" alt="avatar">
                                                </a>
                                                <div class="chat-about">
                                                    <h6 class="m-b-0"><?php echo $first_info['name']; ?></h6>
                                                    <small><?php echo get_the_title($first_info['listing']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-history messenger-container active">
                                        <p id="loading_messages">Loading messages...</p>
                                        <section class="discussion">

                                            <?php
                                            //e_var_dump($first_info);

                                            $bd_message = new MPP_Database;
                                            $messages = $bd_message->retrieve_messages($first_info['sender'], $first_info['recipient'], $first_info['listing']);

                                            //e_var_dump($messages);

                                            $prev_sender = $next_sender = 0;
                                            if ($messages) {
                                                foreach ($messages as $key => $message) {
                                                    $prev_sender = $key > 0 ? $messages[$key - 1]->sender_id : 0;
                                                    $next_sender = $key < count($messages) - 1 ? $messages[$key + 1]->sender_id : 0;

                                                    $owner = $message->sender_id == $chat_client_id ? 'sender' : 'recipient';
                                                    $message_position = '';
                                                    if ($prev_sender !== $message->sender_id) $message_position = 'first';
                                                    if ($prev_sender == $message->sender_id) $message_position = 'middle';
                                                    if ($next_sender !== $message->sender_id) $message_position = 'last';
                                                    if ($next_sender !== $message->sender_id && $prev_sender !== $message->sender_id) $message_position = 'single';
                                            ?>
                                                    <div class="bubble <?php echo $owner; ?> <?php echo $message_position; ?>"><?php echo $message->message; ?></div>
                                            <?php

                                                    // Change status
                                                    if ($message->status == 1 && $message->recipient_id == $chat_client_id) {
                                                        $bd_message->update_status($message->id, 2); // 2 = read
                                                    }
                                                }
                                            }


                                            ?>

                                        </section>
                                    </div>
                                    <?php if (!$isOwner) : ?>
                                        <div class="chat-message clearfix">
                                            <div class="input-group mb-0">
                                                <form class="messenger-form" id="mpp_messenger_form">
                                                    <textarea id="messenger_message" name="messenger_message"></textarea>
                                                    <input type="hidden" name="msg_info" id="msg_info" value='<?php echo json_encode(array('sender' => $first_info['sender'], 'recipient' => $first_info['recipient'], 'listing' => $first_info['listing'])); ?>' />
                                                    <button type="submit"><i class="fa fa-paper-plane"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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

    // AJAX INSERT MESSAGE ROW
    public function mpp_insert_message_row()
    {
        $result = false;
        $message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
        $info = isset($_REQUEST['info']) ? $_REQUEST['info'] : '';
        if (!empty($info)) {
            $info = stripslashes($info);
            $info = json_decode($info);
            $sender = $info->sender;
            $recipient = $info->recipient;
            $listing = $info->listing;
            //$result = $sender;
            if (!empty($message)) {
                $bd_message = new MPP_Database;
                $result = $bd_message->insert_message(array('sender_id' => $sender, 'recipient_id' => $recipient, 'listing_id' => $listing, 'message' => $message));
            }
        }
        echo json_encode(array('result' => $result));
        die();
    }

    // AJAX INSERT MESSAGE ROW
    public function mpp_get_unread_message()
    {
        $result = false;
        $info = isset($_REQUEST['info']) ? $_REQUEST['info'] : '';
        $messages = '';

        if (!empty($info)) {
            $info = stripslashes($info);
            $info = json_decode($info);
            $sender = $info->sender;
            $recipient = $info->recipient;
            $listing = $info->listing;

            if (!empty($sender) && !empty($recipient)) {
                $bd_message = new MPP_Database;
                $messages = $bd_message->retrieve_unread_messages($sender, $recipient, $listing);
                if ($messages && count($messages) > 0) {
                    foreach ($messages as $message) {
                        $bd_message->update_status($message->id, 2);
                    }
                    $result = true;
                }
            }
        }

        echo json_encode(array('result' => $result, 'messages' => $messages));
        die();
    }

    // RETRIVE MESSAGES
    function mpp_retrive_messages()
    {
        $result = false;
        $info = isset($_REQUEST['info']) ? $_REQUEST['info'] : '';
        $messages = '';

        if (!empty($info)) {
            $sender = $info['sender'];
            $recipient = $info['recipient'];
            $listing = $info['listing'];

            if (!empty($sender) && !empty($recipient)) {
                $bd_message = new MPP_Database;
                $messages = $bd_message->retrieve_messages($sender, $recipient, $listing);
                if ($messages && count($messages) > 0) {
                    foreach ($messages as $message) {
                        // Change status
                        if ($message->status == 1 && $message->recipient_id == $sender) {
                            $bd_message->update_status($message->id, 2);
                        }
                    }
                    $result = true;
                }
            }
        }
        echo json_encode(array('result' => $result, 'messages' => $messages));
        die();
    }

    // AJAX - MPP PROPERTY SEARCH
    public function mpp_property_search()
    {
        // we will pass post IDs and titles to this array
        $return = array();

        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new WP_Query(array(
            'post_type' => ATBDP_POST_TYPE,
            's' => $_GET['q'], // the search query
            'post_status' => 'publish', // if you don't want drafts to be returned
            'ignore_sticky_posts' => 1,
            'posts_per_page' => 20, // how much to show at once
            'tax_query' => array(
                array(
                    'taxonomy' => ATBDP_DIRECTORY_TYPE,
                    'field'    => 'slug',
                    'terms'    => 'pets-community',
                ),
            ),
        ));
        if ($search_results->have_posts()) :
            while ($search_results->have_posts()) : $search_results->the_post();
                // shorten the title a little
                $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                $return[] = array($search_results->post->ID, $title); // array( Post ID, Post Title )
            endwhile;
        endif;
        echo json_encode($return);

        die();
    }

    // MPP APPLY AS REFFERAL LISTING
    public function mpp_apply_as_referral_listing($listing_id = 0, $args = array())
    {
        $result = false;
        $user_id = get_current_user_id();
        $applied_speakers = get_post_meta($listing_id, 'mpp_applied_speakers', true) ? get_post_meta($listing_id, 'mpp_applied_speakers', true) : array();
        $mpp_spokespersons = get_post_meta($listing_id, 'mpp_spokespersons', true) ? get_post_meta($listing_id, 'mpp_spokespersons', true) : array();
        if (empty($mpp_spokespersons) || !array_key_exists($user_id, $mpp_spokespersons)) {
            if ($applied_speakers && !empty($applied_speakers)) {
                if (!array_key_exists($user_id, $applied_speakers)) {
                    $applied_speakers[$user_id] = $args;
                    update_post_meta($listing_id, 'mpp_applied_speakers', $applied_speakers);
                    $result = true;
                }
            } else {
                $applied_speakers[$user_id] = $args;
                update_post_meta($listing_id, 'mpp_applied_speakers', $applied_speakers);
                $result = true;
            }
        }
        return $result;
    }

    // AJAX ACCEPT REFERRAL LISTING
    public function mpp_ajax_accept_referral_listing()
    {
        $result = false;
        $args = array();
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $listing_id = isset($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;
        $applied_speakers = get_post_meta($listing_id, 'mpp_applied_speakers', true) ? get_post_meta($listing_id, 'mpp_applied_speakers', true) : array();
        if ($applied_speakers && !empty($applied_speakers)) {
            if (array_key_exists($user_id, $applied_speakers)) {
                $args = $applied_speakers[$user_id];
                unset($applied_speakers[$user_id]);
                update_post_meta($listing_id, 'mpp_applied_speakers', $applied_speakers);

                // ADD to the existing list
                $spokespersons = get_post_meta($listing_id, 'mpp_spokespersons', true);
                $spokespersons = $spokespersons && !empty($spokespersons) ? $spokespersons : array();

                if (empty($spokespersons) || !array_key_exists($user_id, $spokespersons)) {
                    $spokespersons[$user_id] = $args;
                    update_post_meta($listing_id, 'mpp_spokespersons', $spokespersons);
                    $result = true;
                }
            }
        }
        echo json_encode(array('result' => $result, 'speakers' => $listing_id));
        die();
    }

    // AJAX REJECT REFERRAL LISTING
    public function mpp_ajax_reject_referral_listing()
    {
        $result = false;
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $listing_id = isset($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;
        $applied_speakers = get_post_meta($listing_id, 'mpp_applied_speakers', true) ? get_post_meta($listing_id, 'mpp_applied_speakers', true) : array();
        if ($applied_speakers && !empty($applied_speakers)) {
            if (array_key_exists($user_id, $applied_speakers)) {
                unset($applied_speakers[$user_id]);
                update_post_meta($listing_id, 'mpp_applied_speakers', $applied_speakers);
                $result = true;
            }
        }
        echo json_encode(array('result' => $result, 'speakers' => $listing_id));
        die();
    }

    // AJAX REMOVE REFERRAL LISTING
    public function mpp_ajax_remove_referral_listing()
    {
        $result = false;
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : 0;
        $listing_id = isset($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;
        $spokespersons = get_post_meta($listing_id, 'mpp_spokespersons', true) ? get_post_meta($listing_id, 'mpp_spokespersons', true) : array();
        if ($spokespersons && !empty($spokespersons)) {
            if (array_key_exists($user_id, $spokespersons)) {
                unset($spokespersons[$user_id]);
                update_post_meta($listing_id, 'mpp_spokespersons', $spokespersons);
                $result = true;
            }
        }
        echo json_encode(array('result' => $result, 'speakers' => $listing_id));
        die();
    }
}

new Referral_Messenger;
