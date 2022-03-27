<?php

use ElementorPro\Modules\Woocommerce\Widgets\Categories;

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
        // Affiliate WP Link through SMS
        add_shortcode('affiliatewp-link-through-sms', array($this, 'affiliatewp_link_through_sms'));
        // Listing to group Migration
        add_shortcode('bb-listing-to-group-migration', array($this, 'buddyboss_listing_to_group_migration'));
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

    // Affiliate WP Link through SMS
    public function affiliatewp_link_through_sms()
    {

        $affiliate_id = affwp_get_affiliate_id(get_current_user_id());
        if ($affiliate_id) :
            $msg = "Check out the MyPetsProfile app to find local pet-friendly businesses and meet pet-minded friends";
            $msg .= " - https://communityportal.mypetsprofile.com/elite-affiliate-program/?ref=" . $affiliate_id;
            $encoded_sms = rawurlencode($msg);
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
                $sms_string = 'sms://?&amp;';
            } else {
                $sms_string = 'sms:?';
            }
            echo '<a class="button" href="' . $sms_string . 'body=' . $encoded_sms . '">Send Refferan link through SMS</a>';
        else :
            echo '<p>You donot have an affiliate account yet!</p>';
        endif;

        //e_var_dump($_SERVER['HTTP_USER_AGENT']);
    }

    // Buddyboss Listing to Group Migration
    public function buddyboss_listing_to_group_migration()
    {
        /* $listings_query = new WP_Query(
            array(
                'post_type' => 'at_biz_dir',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'OR',
                    '1' => array(
                        'key' => '_bb_group_id',
                        'compare' => 'NOT EXISTS'
                    ),
                    '2' => array(
                        'key' => '_bb_group_id',
                        'value' => '',
                        'compare' => '='
                    )
                )
            )
        );
        if (!is_wp_error($listings_query)) {
            while ($listings_query->have_posts()) : $listings_query->the_post();
                global $post;
                //e_var_dump($post->post_title);
                $group_exists = groups_get_groups(array('search_terms' => $post->post_title));
                //e_var_dump($group_exists);
                if ($group_exists && $group_exists['total'] > 0) {
                    foreach ($group_exists['groups'] as $group) {
                        if ($post->post_title === $group->name) {
                            echo $group->name . '<br>';
                            //break;
                        }
                    }
                }
            endwhile;
            wp_reset_query();
        } */
        /* $group_list = groups_get_groups(array('per_page' => '-1'));
        $list = [];
        foreach ($group_list['groups'] as $group) {
            $group_type = bp_groups_get_group_type($group->id);
            if (!$group_type || empty($group_type)) {
                $listing = get_page_by_title($group->name, OBJECT, 'at_biz_dir');
                if ($listing && isset($listing->ID) && !empty($listing->ID)) {
                    //e_var_dump($listing->post_title);
                    $categories = get_the_terms($listing->ID, ATBDP_CATEGORY);
                    if (!$categories) $list[$listing->ID] = $listing->post_title;
                }
            }
            $listings = groups_get_groupmeta($group->id, 'directorist_listings_ids', true);
            if (!$listings || count($listings) < 1) {
                $listing = get_page_by_title($group->name, OBJECT, 'at_biz_dir');
                if ($listing && isset($listing->ID) && !empty($listing->ID)) {
                    update_post_meta($listing->ID, '_bb_group_id', $group->id);
                    groups_update_groupmeta($group->id, 'directorist_listings_enabled', 1);
                    groups_update_groupmeta($group->id, 'directorist_listings_ids', array($listing->ID));
                }
            }
        }
        e_var_dump($list); */
    }
}

new MPP_Child_Shortcode;
