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
        add_action('directorist_listing_imported', array($this, 'directorist_after_import_listing'), 10, 2);
        // Default Group Avatar For Web
        add_filter('bp_get_group_avatar', array($this, 'bp_get_group_avatar'));
        // Default Group Avatar for App
        add_filter('bp_rest_groups_prepare_value', array($this, 'bp_rest_groups_prepare_value'), 10, 3);
        // Add Custom Field on Category Form
        add_action(ATBDP_CATEGORY . '_edit_form_fields', array($this, 'edit_category_icon_field'), 10, 2);
        // Update App Image Meta
        add_action('edited_' . ATBDP_CATEGORY, array($this, 'update_category_app_image'), 10, 2);
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

    // Custom Listing Import Hook
    public function directorist_after_import_listing($post_id, $post)
    {
        // Assign Pricing Plan
        update_post_meta($post_id, '_fm_plans_by_admin', 1);
        update_post_meta($post_id, '_fm_plans', 4337);
        update_post_meta($post_id, '_never_expire', 1);

        // Update post status to publish
        wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
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
    public function bp_rest_groups_prepare_value($response, $request, $item)
    {
        $custom_avatar = get_stylesheet_directory_uri() . '/assets/img/default-group.png';
        $directorist_category = groups_get_groupmeta($item->id, 'directorist_category', true);
        if ($directorist_category) {
            $category_image = get_term_meta($directorist_category,  'app_image', true);
            if ($category_image) {
                $custom_avatar = wp_get_attachment_image_url($category_image);
            }
        }
        $response->data['avatar_urls']['thumb'] = $custom_avatar;
        $response->data['avatar_urls']['full'] = $custom_avatar;

        return $response;
    }

    // Edit Custom Category Fields
    public function edit_category_icon_field($term, $taxonomy)
    {
        $image_id = get_term_meta($term->term_id, 'app_image', true);
        $image_src = ($image_id) ? wp_get_attachment_url((int)$image_id) : '';
?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="atbdp-categories-app-image-id"><?php _e('App Image', 'directorist'); ?></label>
            </th>
            <td>
                <input type="hidden" id="atbdp-categories-app-image-id" name="app_image" value="<?php echo $image_id; ?>" />
                <div id="atbdp-categories-app-image-wrapper">
                    <?php
                    if ($image_src) : ?>
                        <img src="<?php echo $image_src; ?>" />
                        <a href="" class="remove_cat_app_img"><span class="fa fa-times" title="Remove it"></span></a>
                    <?php endif; ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary" id="atbdp-categories-upload-app-image" value="<?php _e('Add Image', 'directorist'); ?>" />
                </p>
            </td>
        </tr>
<?php
    }

    // Save Category App Image Meta
    public function update_category_app_image($term_id, $tt_id)
    {
        //UPDATED CATEGORY IMAGE
        if (isset($_POST['app_image']) && '' !== $_POST['app_image']) {
            update_term_meta($term_id, 'app_image', (int)$_POST['app_image']);
        } else {
            update_term_meta($term_id, 'app_image', '');
        }
    }
}

new MPP_Child_Hooks;
