<?php
$listing_id = get_the_ID();
if (!get_directorist_option('enable_claim_listing', 1)) return; // vail if the business hour is not enabled
$claim_header = get_directorist_option('claim_widget_title', esc_html__('Is this your business?', 'directorist-claim-listing'));
$claim_description = get_directorist_option('claim_widget_description', esc_html__('Claim listing is the best way to manage and protect your business.', 'directorist-claim-listing'));
$claim_now = get_directorist_option('claim_now', esc_html__('Claim Now!', 'directorist-claim-listing'));
$claimed_by_admin = get_post_meta($listing_id, '_claimed_by_admin', true);
$claim_fee = get_post_meta($listing_id, '_claim_fee', true);
if ($claimed_by_admin || ('claim_approved' === $claim_fee)) return;
?>
<div class="directorist-claim-listing-wrapper">
    <?php if (is_user_logged_in()) { ?>
        <div class="directorist-card directorist-claim-listing">
            <div class="directorist-card__header">
                <h4 class="directorist-card__header--title">
                    <span class="<?php atbdp_icon_type(true); ?>-edit"></span><?php _e(' Claim', 'directorist-claim-listing') ?>
                </h4>
            </div>
            <div class="directorist-card__body">
                <h4 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing') ?></h4>
                <div class="directorist-claim-listing__description">
                    <p>Connect with more local pet parents.</br>
                        MyPetsProfile™ is a subscription-based, community social platform for pet parents.</br>
                        The MyPetsProfile™ community gives businesses the opportunity to communicate directly with pet parents, and present a compelling solution for their pet(s) needs, as viewed by them.</br>
                        Let them know the pet-friendly benefits your business offers, and how to buy. Kick-start a loyal pet-friendly community following, with the MPP platform tools to grow your business.</br>
                        Please note: The sale of pet(s) on MPP Platform is strictly prohibited.</br>
                        Benefits:
                    </p>
                    <ul>
                        <li>Get MyPetsProfile™ Customer Communication Portal</li>
                        <li>Easily link to existing customer web/software and social platforms</li>
                        <li>Review customer's MyPetsProfile™</li>
                        <li>List pet amenities and pet policies</li>
                        <li>List Biz features and pet parent benefits</li>
                        <li>Set private or public pet customer communications</li>
                        <li>Send messages, notices and discussions</li>
                        <li>Add and promote biz photos, videos and events</li>
                        <li>Add value with forums, blogs and posts</li>
                        <li>Give customers a pet concierge-like access</li>
                        <li>Customers get access to local pet biz' and events</li>
                    </ul>
                    <p style="font-size: 16px"><b>Claim this business for $99./year</b></p>
                </div>
                <a href="#" class=" directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js" data-directorist_target="directorist-claim-listing-modal"><?php _e("$claim_now", 'directorist-claim-listing') ?></a>
            </div>
        </div>
    <?php } else { ?>
        <div class="directorist-card directorist-claim-listing">
            <div class="directorist-card__header">
                <h4 class="directorist-card__header--title">
                    <span class="<?php atbdp_icon_type(true); ?>-edit"></span><?php _e(" $claim_now", 'directorist-claim-listing') ?>
                </h4>
            </div>

            <div class="directorist-card__body">
                <h4 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing') ?></h4>
                <p class="directorist-claim-listing__description"><?php _e("$claim_description", 'directorist-claim-listing') ?></p>
                <a href="#" class="directorist-claim-listing__login-alert  directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js"><?php _e("$claim_now", 'directorist-claim-listing') ?></a>
                <div class="directorist-claim-listing__login-notice directorist_notice directorist-alert directorist-alert-info" role="alert">
                    <span class="fa fa-info-circle" aria-hidden="true"></span>
                    <?php
                    // get the custom registration page id from the db and create a permalink
                    $reg_link_custom = ATBDP_Permalink::get_registration_page_link();
                    //if we have custom registration page, use it, else use the default registration url.
                    $reg_link = !empty($reg_link_custom) ? $reg_link_custom : wp_registration_url();

                    $login_url = '<a href="' . ATBDP_Permalink::get_login_page_link() . '">' . __('Login', 'directorist-claim-listing') . '</a>';
                    $register_url = '<a href="' . esc_url($reg_link) . '">' . __('Register', 'directorist-claim-listing') . '</a>';

                    printf(__('You need to %s or %s to claim this listing', 'directorist-claim-listing'), $login_url, $register_url);
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <input type="hidden" id="directorist__post-id" value="<?php echo get_the_ID(); ?>" />
</div>
<div class="directorist-modal directorist-modal-js directorist-fade directorist-claim-listing-modal directorist-claimer">
    <div class="directorist-modal__dialog directorist-modal__dialog-lg">
        <div class="directorist-modal__content">
            <form id="directorist-claimer__form" class="directorist-claimer__form">
                <div class="directorist-modal__header">
                    <h3 class="directorist-modal-title" id="directorist-claim-label"><?php _e('Claim This Business', 'directorist-claim-listing'); ?></h3>
                    <a href="#" class="directorist-modal-close directorist-modal-close-js"><span aria-hidden="true">&times;</span></a>
                </div>
                <div class="directorist-modal__body">
                    <div class="directorist-form-group">
                        <label for="directorist-claimer__name" class="directorist-claimer__name"><?php _e('Full Name', 'directorist-claim-listing'); ?> <span class="directorist-claimer__star-red">*</span></label>
                        <input type="text" class="directorist-form-element" id="directorist-claimer__name" placeholder="<?php _e('Full Name', 'directorist-claim-listing'); ?>" required>
                    </div>
                    <div class="directorist-form-group">
                        <label for="directorist-claimer__phone" class="directorist-claimer__phone"><?php _e('Phone', 'directorist-claim-listing'); ?> <span class="directorist-claimer__star-red">*</span></label>
                        <input type="tel" class="directorist-form-element" id="directorist-claimer__phone" placeholder="<?php _e('111-111-235', 'directorist-claim-listing'); ?>" required>
                    </div>
                    <div class="directorist-form-group">
                        <?php
                        $claim_charge_by = get_directorist_option('claim_charge_by');
                        $charged_by = get_post_meta($listing_id, '_claim_fee', true);
                        $directory_type = get_post_meta($listing_id, '_directory_type', true);
                        $charged_by = ($charged_by !== '') ? $charged_by : $claim_charge_by;
                        $has_plans = is_pricing_plans_active();
                        if (!empty($has_plans) && ('pricing_plan' === $charged_by)) {
                            if (class_exists('ATBDP_Pricing_Plans')) {
                                $args = array(
                                    'post_type' => 'atbdp_pricing_plans',
                                    'posts_per_page' => -1,
                                    'status' => 'publish',
                                );

                                $metas = [];
                                $metas['exclude'] = [
                                    'relation' => 'OR',
                                    array(
                                        'key'       => '_hide_from_plans',
                                        'compare'   => 'NOT EXISTS',
                                    ),
                                    array(
                                        'key'       => '_hide_from_plans',
                                        'value'     => 1,
                                        'compare'   => '!=',
                                    ),
                                ];

                                if (!empty($directory_type)) {
                                    $metas['directory'] = [
                                        'key'       => '_assign_to_directory',
                                        'value'     => $directory_type,
                                        'compare'   => '=',
                                    ];
                                }

                                $args['meta_query'] = array_merge(array('relation' => 'AND'), $metas);


                                $atbdp_query = new WP_Query($args);

                                if ($atbdp_query->have_posts()) {
                                    global $post;

                                    $plans = $atbdp_query->posts;
                                    printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                    printf('<select id="directorist-claimer__plan">');
                                    printf('<option>%s</option>', __('Select Plan', 'directorist-claim-listing'));
                                    foreach ($plans as $key => $value) {
                                        $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                                        $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                        printf('<option %s value="%s">%s %s</option>', (!empty($active_plan) && ('package' === $plan_type)) ? 'class="directorist__active-plan"' : '', $value->ID, $value->post_title, !empty($active_plan) && ('package' === $plan_type) ? '<span class="atbd_badge">' . __('- Active', 'directorist-claim-listing') . '</span>' : '');
                                    }
                                    printf('</select>');

                        ?>
                                    <div id="directorist__plan-allowances" data-author_id="<?php echo get_current_user_id(); ?>">
                                    </div>
                                <?php

                                    printf('<a target="_blank" href="%s" class="directorist__plans">%s</a>', esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __('Show plan details', 'directorist-claim-listing'));
                                }
                            } else {
                                global $product;
                                $query_args = array(
                                    'post_type' => 'product',
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'product_type',
                                            'field' => 'slug',
                                            'terms' => 'listing_pricing_plans',
                                        ),
                                    ),
                                );

                                $metas = [];
                                $metas['exclude'] = [
                                    'relation' => 'OR',
                                    array(
                                        'key'       => '_hide_from_plans',
                                        'compare'   => 'NOT EXISTS',
                                    ),
                                    array(
                                        'key'       => '_hide_from_plans',
                                        'value'     => 1,
                                        'compare'   => '!=',
                                    ),
                                ];

                                if (!empty($directory_type)) {
                                    $metas['directory'] = [
                                        'key'       => '_assign_to_directory',
                                        'value'     => $directory_type,
                                        'compare'   => '=',
                                    ];
                                }

                                $query_args['meta_query'] = array_merge(array('relation' => 'AND'), $metas);


                                $atbdp_query = new WP_Query($query_args);

                                if ($atbdp_query->have_posts()) {
                                    global $post;
                                    $plans = $atbdp_query->posts;
                                    printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                    printf('<select id="directorist-claimer__plan">');
                                    printf('<option>%s</option>', __('Select Plan', 'directorist-claim-listing'));
                                    foreach ($plans as $key => $value) {
                                        $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                                        $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                        printf('<option %s value="%s">%s %s</option>', (!empty($active_plan) && ('package' === $plan_type)) ? 'class="directorist__active-plan"' : '', $value->ID, $value->post_title, !empty($active_plan) && ('package' === $plan_type) ? '<span class="atbd_badge">' . __('- Active', 'directorist-claim-listing') . '</span>' : '');
                                    }
                                    printf('</select>');
                                ?>
                                    <div id="directorist__plan-allowances" data-author_id="<?php echo get_current_user_id(); ?>">
                                    </div>
                        <?php
                                    printf('<a target="_blank" href="%s">%s</a>', esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __(' Show plan details', 'directorist-claim-listing'));
                                }
                            }
                        }
                        ?>
                    </div>
                    <div id="directorist-claimer__submit-notification"></div>
                    <div id="directorist-claimer__warning-notification"></div>
                </div>

                <div class="directorist-modal__footer">
                    <button type="submit" class="directorist-btn directorist-btn-primary"><?php esc_html_e('Submit', 'directorist-claim-listing'); ?></button>
                    <span><i class="<?php atbdp_icon_type(true); ?>-lock"></i><?php esc_html_e('Secure Claim Process', 'directorist-claim-listing'); ?></span>
                </div>
            </form>
        </div>
    </div>
</div>