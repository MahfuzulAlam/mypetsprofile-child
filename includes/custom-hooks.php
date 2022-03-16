<?php

/**
 * Integrations: MPP CHild Theme Custom Hooks
 */

class MPP_Child_Hooks
{
    public function __construct()
    {
        // Change the pricing plan url for mobile
        add_filter('atbdp_pricing_plan_to_checkout_url', array($this, 'atbdp_pricing_plan_to_checkout_url'), 10, 2);
        // Custom import hooks
        add_action('directorist_after_import_listing', array($this, 'directorist_after_import_listing'), 10, 3);
        // Default Group Avatar For Web
        add_filter('bp_get_group_avatar', array($this, 'bp_get_group_avatar'));
        // Default Group Avatar for App
        add_filter('bp_rest_groups_prepare_value', array($this, 'bp_rest_groups_prepare_value'), 10, 3);
    }

    // Change the pricing plan url for mobile
    public function atbdp_pricing_plan_to_checkout_url($url, $plan_id)
    {
        if (
            strpos($_SERVER['HTTP_USER_AGENT'], 'wv') !== false || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false &&
                (strpos($_SERVER['HTTP_USER_AGENT'], 'chrome') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'safari') == false))
        ) {
            $iap_plan_id = 0;
            switch ($plan_id) {
                case 12172:
                    $iap_plan_id = 1;
                    break;
                case 4183:
                    $iap_plan_id = 3;
                    break;
                case 4182:
                    $iap_plan_id = 4;
                    break;
            }
            if ($iap_plan_id !== 0) $url = 'https://communityportal.mypetsprofile.com/bbapp/products/' . $iap_plan_id;
        }
        return $url;
    }

    //Custom Import Hook
    public function directorist_after_import_listing($post_id, $post, $tax_inputs)
    {
        update_post_meta($post_id, '_fm_plans_by_admin', 1);
        update_post_meta($post_id, '_fm_plans', 4337);
        update_post_meta($post_id, '_never_expire', 1);

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
                update_post_meta($post_id, '_bb_group_id', $bb_group_id);
                // Update BB Group Type like listing Category

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

    // Default Group Avatar For Web
    public function bp_get_group_avatar($avatar)
    {
        global $bp, $groups_template;

        if (strpos($avatar, 'group-avatars')) {
            return $avatar;
        } else {
            $custom_avatar = get_stylesheet_directory_uri() . '/assets/img/default-group.png';

            $directorist_category = groups_get_groupmeta($groups_template->group->id, 'directorist_category', true);

            if ($directorist_category) {
                $category_image = get_term_meta($directorist_category,  'image', true);
                if ($category_image) {
                    $custom_avatar = wp_get_attachment_image_url($category_image);
                }
            }

            if ($bp->current_action == "")
                return '<img class="avatar" src="' . $custom_avatar . '" alt="' . attribute_escape($groups_template->group->name) . '" width="' . BP_AVATAR_THUMB_WIDTH . '" height="' . BP_AVATAR_THUMB_HEIGHT . '" />';
            else
                return '<img class="avatar" src="' . $custom_avatar . '" alt="' . attribute_escape($groups_template->group->name) . '" width="' . BP_AVATAR_FULL_WIDTH . '" height="' . BP_AVATAR_FULL_HEIGHT . '" />';
        }
    }

    // Default Group Avatar for App
    function bp_rest_groups_prepare_value($response, $request, $item)
    {
        $custom_avatar = get_stylesheet_directory_uri() . '/assets/img/default-group.png';
        $directorist_category = groups_get_groupmeta($item->id, 'directorist_category', true);
        if ($directorist_category) {
            $category_image = get_term_meta($directorist_category,  'image', true);
            if ($category_image) {
                $custom_avatar = wp_get_attachment_image_url($category_image);
            }
        }
        $response->data['avatar_urls']['thumb'] = $custom_avatar;
        $response->data['avatar_urls']['full'] = $custom_avatar;

        return $response;
    }
}

new MPP_Child_Hooks;
