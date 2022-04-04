<?php if (current_user_can('manage_affiliates')) : ?>
    <p class="no-access"><?php _e('To see the Affiliate Area, log in as an existing affiliate, or add your account as an affiliate.', 'affiliate-wp'); ?></p>
<?php else : ?>
    <div class="no-access">
        <p>Hello,</p>
        <p>You’ve selected an area that is exclusive to “Elite Members”.</p>
        <p>Please click the following button to learn how you can become an “MPP Elite Member”.</p>
    </div>
    <a class="btn btn-primary button" href="https://communityportal.mypetsprofile.com/bbapp/products/1">MPP Elite Membership</a>
<?php endif; ?>