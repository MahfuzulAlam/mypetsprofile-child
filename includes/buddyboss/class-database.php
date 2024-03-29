<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class MPP_Database
{
    public function __construct()
    {
        // Variables
        $this->date_sent = bp_core_current_time();
        $this->sender_id = bp_loggedin_user_id();
        // Hooks
        //add_action("after_switch_theme", array($this, "mpp_create_messenger_table"));
    }

    public function mpp_create_messenger_table()
    {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        //get the database table prefix to create my new table

        $table_name = $wpdb->prefix . "mpp_messenger";

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            sender_id bigint(20) NOT NULL,
            recipient_id bigint(20) NOT NULL,
            listing_id bigint(20) NOT NULL,
            message longtext NOT NULL,
            status int(2) NOT NULL,
            date_sent datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY Index_2 (sender_id),
            KEY Index_3 (recipient_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        dbDelta($sql);
    }

    public function insert_message($args = array())
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "mpp_messenger";

        $defaults = array(
            'sender_id' => $this->sender_id,
            'recipient_id'  => 0,
            'listing_id'  => 0,
            'message'   => '',
            'status'    => 1,
            'date_sent' => $this->date_sent
        );
        $args = wp_parse_args($args, $defaults);

        if (empty($args['sender_id'])) return false;
        if (empty($args['recipient_id'])) return false;
        if (empty($args['listing_id'])) return false;
        if (empty($args['message'])) return false;
        if (empty($args['status'])) return false;
        if (empty($args['date_sent'])) return false;

        if (!$wpdb->query($wpdb->prepare("INSERT INTO {$table_name} ( sender_id, recipient_id, listing_id, message, status, date_sent ) VALUES ( %d, %d, %d, %s, %d, %s )", $args['sender_id'], $args['recipient_id'], $args['listing_id'], $args['message'], $args['status'], $args['date_sent']))) {
            return false;
        }

        return true;
    }

    // CHANGE STATUS
    public function update_status($id = 0, $status = 0)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "mpp_messenger";

        if (empty($id)) return false;
        if (empty($status)) return false;

        if (!$wpdb->update($table_name, ['status' => $status], ['id' => $id])) {
            return false;
        }

        return true;
    }

    // RERIVE CHAT MESSAGES
    public function retrieve_messages($sender_id = 0, $recipient_id = 0, $listing_id = 0)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "mpp_messenger";

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} 
            WHERE 
                (( sender_id=%d AND recipient_id=%d ) 
                OR ( sender_id=%d AND recipient_id=%d))
                -- AND status = (1,2)
                AND listing_id = %d
            ORDER BY date_sent ASC", $sender_id, $recipient_id, $recipient_id, $sender_id, $listing_id)
        );

        return $results;
    }

    // RERIVE CHAT MESSAGES
    public function retrieve_unread_messages($sender_id = 0, $recipient_id = 0, $listing_id = 0)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "mpp_messenger";

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} 
            WHERE sender_id=%d
            AND recipient_id=%d
            AND listing_id=%d
            AND status='1'
            ORDER BY date_sent ASC", $recipient_id, $sender_id, $listing_id)
        );

        return $results;
    }

    // GET CHAT PEOPLE LIST
    public function retrieve_people_list($sender_id = 0)
    {
        global $wpdb;

        $people = array();

        $table_name = $wpdb->prefix . "mpp_messenger";

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} 
            WHERE 
            ( sender_id=%d
            OR recipient_id=%d )
            GROUP BY recipient_id, sender_id, listing_id
            ORDER BY date_sent DESC", $sender_id, $sender_id)
        );

        if ($results && count($results) > 0) {
            foreach ($results as $row) {
                if ($row->sender_id == $sender_id) {
                    $people_id = $row->recipient_id;
                } else {
                    $people_id = $row->sender_id;
                }

                $search_result = $this->search($people, array('people' => $people_id, 'listing_id' => $row->listing_id));
                if (empty($search_result)) array_push($people, $row);
            }
        }

        return $people;
    }

    // GET REFERRAL UNREAD MESSAGE LIST
    public function get_unread_messages_receiver_list()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "mpp_messenger";

        $status = 1;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table_name} 
            WHERE `status` = %d
            GROUP BY recipient_id, sender_id
            ORDER BY date_sent DESC", $status)
        );

        return $results;
    }

    // ARRAY SEARCH
    public function search($array, $search_list)
    {

        // Create the result array
        $result = array();

        // Iterate over each array element
        foreach ($array as $key => $value) {

            if ($value->recipient_id == $search_list['people'] || $value->sender_id == $search_list['people']) {
                if ($value->listing_id == $search_list['listing_id']) {
                    $result[] = $value;
                }
            }
        }

        // Return result 
        return $result;
    }
}

new MPP_Database;
