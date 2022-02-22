<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class BuddyBoss_Group_Custom
{

    public function __construct()
    {
        // Create a group on create a listing with listing details
        add_action('atbdp_after_created_listing', array($this, 'buddyboss_group_on_directorist_listing_creation'));
        // Update a group on update a listing with listing details
        add_action('atbdp_listing_updated', array($this, 'buddyboss_group_on_directorist_listing_creation'));
    }

    public static function buddyboss_group_on_directorist_listing_creation($listing_id = 0)
    {
        if ($listing_id) {

            $args = array(
                'name'          => get_the_title($listing_id),
                'creator_id'    => get_current_user_id(),
                'description'   => get_post_meta($listing_id, '_excerpt', true),
                'enable_forum'  => true,
                'status'        => 'public'
            );

            // Check if the group is present or not
            $group_exists = get_post_meta($listing_id, '_bb_group_id', true);

            if ($group_exists) {
                $args['group_id'] = $group_exists;
            }

            if (function_exists('groups_create_group')) {
                $group_id = groups_create_group($args);
                if (!is_wp_error($group_id) && $group_id) {
                    self::save_categpry_name_as_group_type($_POST, $group_id);
                    update_post_meta($listing_id, '_bb_group_id', $group_id);
                }
            }

        }
    }
    
    public function save_categpry_name_as_group_type($post = array(), $group_id = 0)
    {
        $categories = isset($post['tax_input']['at_biz_dir-category']) ? $post['tax_input']['at_biz_dir-category'] : array();
        if ($categories && count($categories) > 0) $category = $categories[0];
        if (!empty($category)) {
            $category_title = get_term($category, ATBDP_CATEGORY)->name;
            $result = bp_groups_set_group_type($group_id, array($category_title), false);
            file_put_contents(dirname(__FILE__) . '/log.json',  json_encode(array($result, 'here')));
        }
    }

}

new BuddyBoss_Group_Custom;
