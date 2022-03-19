<?php

/**
 * 
 * MPP Child Theme Custom Shortcodes
 * 
 */


class MPP_Child_Shortcode
{
    public function __construct()
    {
        // Change the pricing plan url for mobile
        add_shortcode('bb-group-link-on-listing-page', array($this, 'buddyboss_group_link_on_listing_page'));
    }

    // BuddyBoss Group Link on Linsting Page
    public function buddyboss_group_link_on_listing_page()
    {
        global $post;
        $bb_group_id = get_post_meta($post->ID, '_bb_group_id', true);
        if ($bb_group_id && !empty($bb_group_id)) {
            $group = groups_get_group(array('group_id' => $bb_group_id));
            $group_link = bp_get_group_permalink($group);
            if (!empty($group_link)) {
                echo '<a class="directorist-btn directorist-btn-primary" href="' . $group_link . '">' . $post->post_title . '</a>';
            }
        }
    }
}

new MPP_Child_Shortcode;
