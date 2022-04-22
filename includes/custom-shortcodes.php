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
        // BB Profile Search Form
        add_shortcode('mpp-profile-search-form', array($this, 'mpp_profile_search_form'));
        // MPP Funnies Contest
        add_shortcode('mpp-funnies-contest', array($this, 'mpp_funnies_contest'));
        // Claim Listing IAP
        add_shortcode('claim-listing-iap', array($this, 'claim_listing_iap'));
        // Test Shortcode
        add_shortcode('test-shortcode', array($this, 'test_shortcode'));
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
        ob_start();
        $affiliate_id = affwp_get_affiliate_id(get_current_user_id());
        if ($affiliate_id) :
            $msg = "Check out the MyPetsProfile™ app to find local pet-friendly businesses and meet pet-minded friends";
            $msg .= " " . MPP_SITE_URL . "/elite-affiliate-program/?ep=" . $affiliate_id;
            $encoded_sms = rawurlencode($msg);
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
                $sms_string = 'sms://?&amp;';
            } else {
                $sms_string = 'sms:?';
            }
?>
            <a class="button" href="<?php echo $sms_string; ?>body=<?php echo $encoded_sms; ?>">Send referral invites via Text</a>
            <a class="button" href="https://wa.me/?text=<?php echo $encoded_sms; ?>">Send referral invites via WhatsApp</a>
            <a class="button" href="mailto:abc@example.com?subject=MyPetsProfile™&body=<?php echo $encoded_sms; ?>">Send referral invites via Email</a>
        <?php
        else :
        ?>
            <div class="no-access">
                <p>Hello,</p>
                <p>You’ve selected an area that is exclusive to “Elite Members”.</p>
                <p>Please click the following button to learn how you can become an “MPP Elite Member”.</p>
            </div>
            <a class="button become-an-affiliate" href="<?php echo MPP_SITE_URL; ?>/bbapp/products/1">Become an Affiliate</a>
        <?php
        endif;
        return ob_get_clean();
    }

    public function mpp_profile_search_form()
    {
        ob_start();
        if (function_exists('bp_profile_search_escaped_form_data')) :
            $F = bp_profile_search_escaped_form_data();
        ?>

            <aside id="bp-profile-search-form-outer" class="bp-profile-search-widget">

                <h2 class="bps-form-title"><?php echo $F->title; ?></h2>

                <form action="<?php echo $link = site_url() . '/members/'; ?>" method="<?php echo $F->method; ?>" id="<?php echo $F->unique_id; ?>" class="bps-form standard-form">

                    <?php
                    if (isset($F->fields) && !empty($F->fields) && count($F->fields) > 1) {
                        foreach ($F->fields as $f) {
                            $id      = $f->unique_id;
                            $name    = $f->html_name;
                            $value   = $f->value;
                            $display = $f->display;

                            if ($display == 'none') {
                                continue;
                            }

                            if ($display == 'hidden') { ?>
                                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" /><?php
                                                                                                                    continue;
                                                                                                                }

                                                                                                                if ('heading_contains' == $f->code) { ?>
                                <div id="<?php echo $id; ?>_wrap" class="bp-field-wrap bp-heading-field-wrap bps-<?php echo $display; ?>">
                                    <strong><?php echo $f->label; ?></strong><br>
                                    <?php if (!empty($f->description)) : ?>
                                        <p class="bps-description"><?php echo stripslashes($f->description); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php
                                                                                                                    continue;
                                                                                                                }
                            ?>

                            <div id="<?php echo $id; ?>_wrap" class="bp-field-wrap bps-<?php echo $display; ?>">
                                <label for="<?php echo $id; ?>" class="bps-label"><?php echo $f->label; ?></label>
                                <?php
                                switch ($display) {
                                    case 'range': ?>
                                        <input type="text" id="<?php echo $id; ?>" name="<?php echo $name . '[min]'; ?>" value="<?php echo $value['min']; ?>" />
                                        <span> - </span>
                                        <input type="text" name="<?php echo $name . '[max]'; ?>" value="<?php echo $value['max']; ?>" />
                                    <?php
                                        break;

                                    case 'date_range': ?><span class="date-from date-label"><?php _e(
                                                                                                'From',
                                                                                                'buddyboss'
                                                                                            ); ?></span>
                                        <div class="date-wrapper">
                                            <select name="<?php echo $name . '[min][day]'; ?>">
                                                <?php
                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['min']['day'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Day', 'buddyboss')
                                                );

                                                for ($i = 1; $i < 32; ++$i) {
                                                    $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        $day,
                                                        selected($value['min']['day'], $day, false),
                                                        $i
                                                    );
                                                }
                                                ?>
                                            </select>

                                            <select name="<?php echo $name . '[min][month]'; ?>">
                                                <?php
                                                $months = array(
                                                    __('January', 'buddyboss'),
                                                    __('February', 'buddyboss'),
                                                    __('March', 'buddyboss'),
                                                    __('April', 'buddyboss'),
                                                    __('May', 'buddyboss'),
                                                    __('June', 'buddyboss'),
                                                    __('July', 'buddyboss'),
                                                    __('August', 'buddyboss'),
                                                    __('September', 'buddyboss'),
                                                    __('October', 'buddyboss'),
                                                    __('November', 'buddyboss'),
                                                    __('December', 'buddyboss'),
                                                );

                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['min']['month'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Month', 'buddyboss')
                                                );

                                                for ($i = 0; $i < 12; ++$i) {
                                                    $month = $i + 1;
                                                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        $month,
                                                        selected($value['min']['month'], $month, false),
                                                        $months[$i]
                                                    );
                                                }
                                                ?>
                                            </select>

                                            <select name="<?php echo $name . '[min][year]'; ?>">
                                                <?php
                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['min']['year'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Year', 'buddyboss')
                                                );

                                                $date_range_type = bp_xprofile_get_meta($f->id, 'field', 'range_type', true);

                                                if ('relative' === $date_range_type) {
                                                    $range_relative_start = bp_xprofile_get_meta($f->id, 'field', 'range_relative_start', true);
                                                    $range_relative_end   = bp_xprofile_get_meta($f->id, 'field', 'range_relative_end', true);
                                                    $start                = date('Y') - abs($range_relative_start);
                                                    $end                  = date('Y') + $range_relative_end;
                                                } elseif ('absolute' === $date_range_type) {
                                                    $start = bp_xprofile_get_meta($f->id, 'field', 'range_absolute_start', true);
                                                    $end   = bp_xprofile_get_meta($f->id, 'field', 'range_absolute_end', true);
                                                } else {
                                                    $start = date('Y') - 50; //50 years ago
                                                    $end   = date('Y') + 50; //50 years in future
                                                }

                                                for ($i = $end; $i >= $start; $i--) {
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        (int) $i,
                                                        selected($value['min']['year'], $i, false),
                                                        (int) $i
                                                    );
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <span class="date-to date-label"><?php _e(
                                                                                'To',
                                                                                'buddyboss'
                                                                            ); ?></span>
                                        <div class="date-wrapper">
                                            <select name="<?php echo $name . '[max][day]'; ?>">
                                                <?php
                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['max']['day'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Day', 'buddyboss')
                                                );

                                                for ($i = 1; $i < 32; ++$i) {
                                                    $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        $day,
                                                        selected($value['max']['day'], $day, false),
                                                        $i
                                                    );
                                                }
                                                ?>
                                            </select>

                                            <select name="<?php echo $name . '[max][month]'; ?>">
                                                <?php
                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['max']['month'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Month', 'buddyboss')
                                                );

                                                for ($i = 0; $i < 12; ++$i) {
                                                    $month = $i + 1;
                                                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        $month,
                                                        selected($value['max']['month'], $month, false),
                                                        $months[$i]
                                                    );
                                                }
                                                ?>
                                            </select>

                                            <select name="<?php echo $name . '[max][year]'; ?>">
                                                <?php
                                                printf(
                                                    '<option value="" %1$s>%2$s</option>',
                                                    selected($value['max']['year'], 0, false),
                                                    /* translators: no option picked in select box */
                                                    __('Select Year', 'buddyboss')
                                                );
                                                for ($i = $end; $i >= $start; $i--) {
                                                    printf(
                                                        '<option value="%1$s" %2$s>%3$s</option>',
                                                        (int) $i,
                                                        selected($value['max']['year'], $i, false),
                                                        (int) $i
                                                    );
                                                }
                                                ?>
                                            </select>
                                        </div> <?php
                                                break;

                                            case 'range-select': ?>
                                        <select id="<?php echo $id; ?>" name="<?php echo $name . '[min]'; ?>">
                                            <?php foreach ($f->options as $option) { ?>
                                                <option <?php selected(
                                                            $value['min'],
                                                            $option
                                                        ); ?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
                                            <?php } ?>
                                        </select> <span> - </span>
                                        <select name="<?php echo $name . '[max]'; ?>">
                                            <?php foreach ($f->options as $option) { ?>
                                                <option <?php selected(
                                                            $value['max'],
                                                            $option
                                                        ); ?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
                                            <?php } ?>
                                        </select> <?php
                                                    break;

                                                case 'textbox': ?>
                                        <input type="search" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
                                    <?php
                                                    break;

                                                case 'number': ?>
                                        <input type="number" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
                                    <?php
                                                    break;

                                                case 'distance':
                                                    $of          = __('of', 'buddyboss');
                                                    $km          = __('km', 'buddyboss');
                                                    $miles       = __('miles', 'buddyboss');
                                                    $placeholder = __('Start typing, then select a location', 'buddyboss');
                                                    $icon_url    = buddypress()->plugin_url  . 'bp-core/profile-search/templates/members/locator.png';
                                                    $icon_title  = __('get current location', 'buddyboss'); ?>

                                        <input type="number" min="1" name="<?php echo $name . '[distance]'; ?>" value="<?php echo $value['distance']; ?>" />

                                        <select name="<?php echo $name . '[units]'; ?>">
                                            <option value="km" <?php selected($value['units'], "km"); ?>><?php echo $km; ?></option>
                                            <option value="miles" <?php selected(
                                                                        $value['units'],
                                                                        "miles"
                                                                    ); ?>><?php echo $miles; ?></option>
                                        </select>

                                        <span><?php echo $of; ?></span>

                                        <input type="search" id="<?php echo $id; ?>" name="<?php echo $name . '[location]'; ?>" value="<?php echo $value['location']; ?>" placeholder="<?php echo $placeholder; ?>" />
                                        <img id="<?php echo $id; ?>_icon" src="<?php echo $icon_url; ?>" alt="<?php echo $icon_title; ?>" />

                                        <input type="hidden" id="<?php echo $id; ?>_lat" name="<?php echo $name . '[lat]'; ?>" value="<?php echo $value['lat']; ?>" />
                                        <input type="hidden" id="<?php echo $id; ?>_lng" name="<?php echo $name . '[lng]'; ?>" value="<?php echo $value['lng']; ?>" />

                                        <script>
                                            jQuery(function($) {
                                                bp_ps_autocomplete('<?php echo $id; ?>', '<?php echo $id; ?>_lat', '<?php echo $id; ?>_lng');
                                                $('#<?php echo $id; ?>_icon').click(function() {
                                                    bp_ps_locate('<?php echo $id; ?>', '<?php echo $id; ?>_lat', '<?php echo $id; ?>_lng')
                                                });
                                            });
                                        </script> <?php
                                                    break;

                                                case 'selectbox': ?>
                                        <select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
                                            <?php foreach ($f->options as $key => $label) { ?>
                                                <option <?php if ($key == $value) {
                                                            echo 'selected="selected"';
                                                        } ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                            <?php } ?>
                                        </select> <?php
                                                    break;

                                                case 'multiselectbox': ?>
                                        <select id="<?php echo $id; ?>" name="<?php echo $name . '[]'; ?>" multiple="multiple">
                                            <?php foreach ($f->options as $key => $label) { ?>
                                                <option <?php if (in_array($key, $f->values)) {
                                                            echo 'selected="selected"';
                                                        } ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                            <?php } ?>
                                        </select> <?php
                                                    break;

                                                case 'radio': ?><?php foreach ($f->options as $key => $label) { ?>
                                        <div class="bp-radio-wrap">
                                            <input class="bs-styled-radio" id="bb-search-<?php echo str_replace(' ', '', $key . '-' . $id); ?>" type="radio" <?php if ($key == $value) {
                                                                                                                                                                    echo 'checked="checked"';
                                                                                                                                                                } ?> name="<?php echo $name; ?>" value="<?php echo $key; ?>" />
                                            <label for="bb-search-<?php echo str_replace(' ', '', $key . '-' . $id); ?>"><?php echo $label; ?></label>
                                        </div><?php
                                                                }

                                                                break;

                                                            case 'checkbox': ?><?php foreach ($f->options as $key => $label) { ?>
                                        <div class="bp-checkbox-wrap">
                                            <input class="bs-styled-checkbox" id="bb-search-<?php echo str_replace(' ', '', $key . '-' . $id); ?>" type="checkbox" <?php if (in_array($key, $f->values)) {
                                                                                                                                                                        echo 'checked="checked"';
                                                                                                                                                                    } ?> name="<?php echo $name . '[]'; ?>" value="<?php echo $key; ?>" />
                                            <label for="bb-search-<?php echo str_replace(' ', '', $key . '-' . $id); ?>"><?php echo $label; ?></label>
                                        </div><?php
                                                                                }
                                                                                break;

                                                                            default: ?>
                                    <p class="bps-error"><?php echo "BP Profile Search: unknown display <em>$display</em> for field <em>$f->name</em>."; ?></p>
                            <?php
                                                                                break;
                                                                        } ?>

                            <?php if (!empty($f->description)) { ?>
                                <p class="bps-description"><?php echo $f->description; ?></p>
                            <?php } ?>
                            </div>
                        <?php
                        } ?>

                        <div class="submit-wrapper">
                            <p class="clear-from-wrap">
                                <a href='javascript:void(0);' onclick="return bp_ps_clear_form_elements(this);"><?php _e(
                                                                                                                    'Reset',
                                                                                                                    'buddyboss'
                                                                                                                ); ?></a>
                            </p>
                            <input style="backgroud: #E85126 !important; border: 1px solid #E85126 !important;" id="wrsp-submit-btn" type="submit" class="submit" value="<?php _e('Search', 'buddyboss'); ?>" />
                        </div>

                    <?php
                    } else {
                    ?>
                        <div class="submit-wrapper">
                            <span class="no-field"><?php _e('Please add fields to search members.', 'buddyboss'); ?></span>
                        </div>
                    <?php
                    }

                    ?>
                </form>
            </aside>
        <?php
        endif;
        return ob_get_clean();
    }

    //MPP Funnies Contest
    public function mpp_funnies_contest()
    {
        ob_start();
        //bp_activity_thumbnail_content_images
        $activities = get_option('mpp_funnies_contest');
        $count = 0;
        ?>
        <div class="mpp-funnies-contest-holder">
            <?php

            if ($activities) {
            ?>
                <div class="mpp-funnies-contest">
                    <div class="mfc-rank">Rank</div>
                    <div class="mfc-avatar">Pofile</div>
                    <div class="mfc-image">Image</div>
                    <div class="mfc-count">Likes</div>
                    <div class="mfc-comment">Comments</div>
                </div>

                <?php
                foreach ($activities as $activity) {
                    $count++;
                ?>
                    <div class="mpp-funnies-contest">
                        <div class="mfc-rank"><?php echo $count; ?></div>
                        <div class="mfc-avatar"><?php echo bp_core_fetch_avatar(array('item_id' => $activity->user_id, 'width' => 100)); ?></div>
                        <div class="mfc-image"><?php $this->activity_media_html($activity->id); ?></div>
                        <div class="mfc-count"><?php echo $activity->favorite_count; ?></div>
                        <div class="mfc-comment"><?php echo $activity->comment_count; ?></div>
                    </div>
                <?php
                }
            } else {
                ?>
                <p>No post found.</p>
            <?php
            }

            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function activity_media_html($activity_id)
    {
        if ($activity_id) {
            $media_ids = bp_activity_get_meta($activity_id, 'bp_media_ids', true);
            $media_ids = explode(",", $media_ids);
            if ($media_ids) {
                foreach ($media_ids as $media_id) {
                    $media          = new BP_Media($media_id);
                    //$attachment_url = wp_get_attachment_url($media->attachment_id);
                    $attachment_url = bp_media_get_preview_image_url($media->id, $media->attachment_id, 'bb-media-activity-image');
        ?>
                    <img src="<?php echo $attachment_url; ?>" width="100"></img>
                <?php
                }
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

        /* $list = [];
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
        } */
    }

    // Claim Listing IAP
    public function claim_listing_iap()
    {
        ob_start();
        if (mpp_is_android_or_ios()) :
            if (directorist_wc_mpp_user_can_claim()) :
                ?>
                <a href="#" class="claim-listing-iap-action directorist-btn directorist-btn-primary">Claim Listing</a>
            <?php
            else :
            ?>
                <p>Please buy a membership plan first to claim this Biz listing.</p>
                <a href="<?php echo MPP_SITE_URL; ?>/bbapp/products/18" class="claim-listing-iap-action directorist-btn directorist-btn-primary">Membership Plans</a>
                <a href="" class="mpp-refresh" onclick="location.reload();"><span class="fa fa-redo"></span> Refresh</a>
            <?php
            endif;
        endif;
        return ob_get_clean();
    }

    // Test Shortcode
    public function test_shortcode()
    {
        ob_start();
        if (current_user_can('administrator')) :
            ?>
            <a href="<?php echo MPP_SITE_URL; ?>" target="_blank">Download</a>
<?php
        endif;
        return ob_get_clean();
    }
}

new MPP_Child_Shortcode;
