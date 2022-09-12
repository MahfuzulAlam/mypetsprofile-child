<?php

/**
 * Register custom automatic push notification.
 */

namespace BuddyBossApp\Custom;

use BuddyBossApp\Jobs;
use BuddyBossApp\Notification\IntegrationAbstract;

/**
 * Class BookPublishNotification
 * @package BuddyBossApp\Custom
 */
class PetAlertNotification extends IntegrationAbstract
{

    /**
     *
     */
    public function load()
    {

        $this->hooks();

        /**
         * Register subscription group for notification.
         * Within one group you can add multiple subscription types.
         *
         * @param string $name     Group unique list
         * @param String $label    Group name.
         * @param array  $settings subscription type list which we want to bind with this group.
         *
         */
        $this->register_subscription_group('petsalert', __("PetsAlert", "buddyboss-app"), array(
            'pet_missing',
        ));

        /**
         * Register subscription type for notification.
         *
         * @param string $name        Subscription type name
         * @param string $label       Subscription label. This label will be visible on Push Notifications manage tab for user in app.
         * @param string $admin_label Subscription label for admin. This label will be visible on Push notifications manage setting page for admin.
         */
        $this->register_subscription_type('pet_missing', __("A pet is missing.", "buddyboss-app"), __("A pet is missing", "buddyboss-app"));
    }

    /**
     * register hooks for sending notification.
     */
    public function hooks()
    {
        add_action('mpp_pet_missing', array($this, 'send_pet_missing_notification'), 999, 2);
        add_action('bbapp_queue_task_pet_missing', array($this, 'handle_pet_missing_job'), 999);
    }

    /**
     * @param $post_id
     * @param $post
     */
    public function send_pet_missing_notification($user_id, $info)
    {
        /**
         * If you sending a notification to large then you can't send notification to all users together due to PHP or server limited.
         * Instead of sending notification to all user you need to do divided all users into the batch for better performance.
         * you can create batch using out class \BuddyBossApp\Jobs
         */

        $jobs = Jobs::instance();
        $jobs->add('pet_missing', array('user_id' => $user_id, 'paged' => 1, 'timestamp' => time()));
        $jobs->start();
    }

    /**
     * @param $task
     */
    public function handle_pet_missing_job($task)
    {

        $task_data      = maybe_unserialize($task->data);
        $user_info = get_userdata($task_data['user_id']);
        $username = $user_info->user_login;
        $primary_text   = __("A pet is missing!");
        $secondary_text = sprintf(__("%s"), $username);

        // $users = get_users(array(
        //     'fields' => 'ids',
        //     'number' => 200,
        //     'paged'  => $task_data['paged'],
        //     'role' => 'Administrator'
        // ));

        $users = array(1);

        if (!empty($users)) {
            $this->send_push(
                array(
                    'primary_text'             => $primary_text,
                    'secondary_text'           => $secondary_text,
                    'user_ids'                 => $users,
                    'data'                     => array(),
                    'push_data'                => array(
                        'link' => bbp_get_user_profile_url($task_data['user_id']),
                    ),
                    'subscription_type'        => 'pet_missing',
                    'normal_notification'      => true,
                    'normal_notification_data' => array(
                        'component_name'    => 'petsalert',
                        'component_action'  => 'lesson_available',
                        'item_id'           => $task_data['user_id'],
                        'secondary_item_id' => $username,
                    )
                )
            );

            $jobs = Jobs::instance();
            $jobs->add('pet_missing', array(
                'user_id'   => $task_data['user_id'],
                'paged'     => ($task_data['paged'] + 1),
                'timestamp' => time(),
            ));
            $jobs->start();
        }
    }


    /**
     * @param $component_name
     * @param $component_action
     * @param $item_id
     * @param $secondary_item_id
     * @param $notification_id
     *
     * @return array|void
     */
    public function format_notification($component_name, $component_action, $item_id, $secondary_item_id, $notification_id)
    {
        /**
         * This will help to update notification content for web.
         * You need to return data in following structure
         *  array(
         *      'text' => Notification text/html,
         *      'link' => link of content,
         *  )
         */
    }
}
