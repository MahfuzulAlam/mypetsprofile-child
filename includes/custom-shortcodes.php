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
        $group_list = groups_get_groups(array('fields' => 'ids', 'per_page' => '-1'));
        $count = 0;
        foreach ($group_list['groups'] as $group) {
            $listings = groups_get_groupmeta($group, 'directorist_listings_ids', true);
            if (!$listings || count($listings) < 1) {
                $count++;
            }
        }
        e_var_dump($count);
    }
}

new MPP_Child_Shortcode;
