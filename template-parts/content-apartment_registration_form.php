<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;

$listing = isset($_REQUEST['listing']) && !empty($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;

?>

<div id="register-page" class="register-page pooprint-register-page">
    <form action="" name="signup_form" id="signup-form" class="standard-form signup-form clearfix" method="post" enctype="multipart/form-data">
        <div class="layout-wrap">
            <div class="register-section default-profile" id="basic-details-section">
                <div class="bb-signup-field signup_email">
                    <label for="signup_email">Email Address *</label>
                    <input type="email" name="signup_email" id="signup_email" value="" aria-required="true" required />
                    <div id="email-strength-result"></div>
                </div>
                <div class="bb-signup-field signup_password">
                    <label for="signup_password">Password *</label>
                    <div class="bb-password-wrap">
                        <a href="#" class="bb-toggle-password">
                            <i class="bb-icon-l bb-icon-eye"></i>
                        </a>
                        <input type="password" name="signup_password" id="signup_password" class="password-entry" value="" aria-required="true" spellcheck="false" autocomplete="off" required />
                    </div>
                    <div id="pass-strength-result"></div>
                </div>
            </div>

            <div class="register-section extended-profile" id="profile-details-section">
                <div class="editfield field_1 field_first-name field_order_0 required-field visibility-public field_type_textbox">
                    <fieldset>
                        <legend id="field_1-1">First Name </legend>
                        <input id="field_1" name="first_name" type="text" value="" aria-required="true" aria-labelledby="field_1-1" aria-describedby="field_1-3" />
                    </fieldset>
                </div>

                <div class="editfield field_2 field_last-name field_order_0 required-field visibility-public alt field_type_textbox">
                    <fieldset>
                        <legend id=" field_2-1">Last Name </legend>
                        <input id="field_2" name="last_name" type="text" value="" aria-required="true" aria-labelledby="field_2-1" aria-describedby="field_2-3" />
                    </fieldset>
                </div>

                <div class="editfield field_3 field_community-name field_order_0 visibility-public alt field_type_textbox">
                    <fieldset>
                        <legend id=" field_3-1">Community Name </legend>
                        <input id="field_3" name="community_name" type="text" value="" aria-required="true" aria-labelledby="field_2-1" aria-describedby="field_2-3" />
                    </fieldset>
                </div>

                <div class="editfield field_4 field_admin-telephone field_order_0 visibility-public alt field_type_textbox">
                    <fieldset>
                        <legend id=" field_4-1">Admin Telephone </legend>
                        <input id="field_4" name="admin_telephone" type="text" value="" aria-required="true" aria-labelledby="field_2-1" aria-describedby="field_2-3" />
                    </fieldset>
                </div>
            </div>
        </div>

        <p class="register-privacy-info">
            By creating an account you are agreeing to the
            <a href="/terms-and-conditions/">
                Terms of Service
            </a>
        </p>

        <div class="g-recaptcha" data-sitekey="6Lfhu-MdAAAAAD6z5terepkOMyqZM4NAesLDt3S_"></div>

        <div class="submit">
            <input type="submit" name="signup_submit" id="signup_submit" value="Create Account" />
        </div>
        <input type="hidden" id="listing" name="listing" value="<?php echo $listing; ?>" />
        <input type="hidden" id="form_type" name="form_type" value="apartment" />
    </form>
</div>