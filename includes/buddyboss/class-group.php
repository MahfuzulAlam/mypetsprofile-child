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
        add_action('atbdp_after_created_listing', array($this, 'buddyboss_group_on_directorist_listing_creation'), 20);
        // Update a group on update a listing with listing details
        add_action('atbdp_listing_updated', array($this, 'buddyboss_group_on_directorist_listing_creation'), 20);
        // Custom import hooks
        add_action('directorist_listing_imported', array($this, 'buddyboss_create_group_after_import_listing'), 10, 2);
    }

    public function buddyboss_group_on_directorist_listing_creation($listing_id = 0)
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
                    $this->connect_with_directorist_listing($listing_id, $group_id);
                    $this->save_category_name_as_group_type($_POST, $group_id);
                }
            }

            $this->assign_coauthors_as_admins($listing_id, $group_id);
        }
    }

    public function assign_coauthors_as_admins($listing_id, $group_id)
    {
        if (is_plugin_active('co-authors-plus/co-authors-plus.php')) {
            $coauthors = get_coauthors($listing_id);
            if ($coauthors) {
                foreach ($coauthors as $authorInfo) {
                    $user_id = $authorInfo->ID;
                    //Add User to the group and add as admin
                    if (!groups_is_user_admin($user_id, $group_id)) {
                        groups_join_group($group_id, $user_id);
                        $this->assign_as_admin($group_id, $user_id);
                    }
                }
            }
        }
    }

    public function save_category_name_as_group_type($post = array(), $group_id = 0)
    {
        $categories = isset($post['tax_input']['at_biz_dir-category']) ? $post['tax_input']['at_biz_dir-category'] : array();
        if ($categories && count($categories) > 0) $category = $categories[0];
        if (!empty($category)) {
            $category_title = get_term($category, ATBDP_CATEGORY)->name;
            $this->set_bb_group_type_from_directorist_category($category_title, $group_id);
        }
    }

    //Custom Import Hook
    public function buddyboss_create_group_after_import_listing($post_id, $post)
    {
        // Create BuddyBoss Group Starts
        $bb_group_args = array(
            'name'          => get_the_title($post_id),
            'creator_id'    => get_current_user_id(),
            'description'   => get_post_meta($post_id, '_excerpt', true),
            'enable_forum'  => true,
            'status'        => 'public'
        );
        if (function_exists('groups_create_group')) {
            $bb_group_id = groups_create_group($bb_group_args);
            if (!is_wp_error($bb_group_id) && $bb_group_id) {

                // Connect Groups and Directorist Listings with each other
                $this->connect_with_directorist_listing($post_id, $bb_group_id);

                // Update BB Group Type like listing Category
                $tax_inputs    = isset($_POST['tax_input']) ? atbdp_sanitize_array($_POST['tax_input']) : array();
                $category_term = isset($tax_inputs['category']) ? $tax_inputs['category'] : '';
                $final_category = isset($post[$category_term]) ? $post[$category_term] : '';

                if (!empty($final_category)) {
                    $this->set_bb_group_type_from_directorist_category($final_category, $bb_group_id);
                }
                // Update BB Group Type like listing Category
            }
        }
        // Create BuddyBoss Group Ends
    }

    // Set BB Group type from Directorist Category
    public function set_bb_group_type_from_directorist_category($category = "", $bb_group_id = 0)
    {
        if (!empty($category) && !empty($bb_group_id)) {
            $bb_group_type = get_page_by_title($category, OBJECT, 'bp-group-type');
            if (!is_wp_error($bb_group_type)) {
                $bb_group_type_key = get_post_meta($bb_group_type->ID, '_bp_group_type_key', true);
                bp_groups_set_group_type($bb_group_id, array($bb_group_type_key), false);
                $this->set_category_id_as_bb_group_meta($category, $bb_group_id);
            }
        }
    }

    // Set category as BB Group Meta
    public function set_category_id_as_bb_group_meta($category = "", $group_id = 0)
    {
        if (!empty($category) && $group_id !== 0) {
            $category_obj = get_term_by('name', $category, ATBDP_CATEGORY);
            if (!is_wp_error($category_obj)) {
                groups_update_groupmeta($group_id, 'directorist_category', $category_obj->term_id);
            }
        }
    }

    // Create Connection with Directorist Listings
    public function connect_with_directorist_listing($listing_id = 0, $group_id = 0)
    {
        if ($listing_id !== 0 && $group_id !== 0) {
            update_post_meta($listing_id, '_bb_group_id', $group_id);
            groups_update_groupmeta($group_id, 'directorist_listings_enabled', 1);
            groups_update_groupmeta($group_id, 'directorist_listings_ids', array($listing_id));
        }
    }

    // Make Admins
    public function assign_as_admin($group_id, $user_id)
    {
        global $wpdb, $bp;
        $group_table = $bp->groups->table_name_members;
        // Assign Admin
        $wpdb->query($wpdb->prepare("UPDATE {$group_table} SET `is_admin`=1, `user_title`='Group Organizer' WHERE `group_id`=%d AND `user_id`=%d", $group_id, $user_id));
    }
}

new BuddyBoss_Group_Custom;
