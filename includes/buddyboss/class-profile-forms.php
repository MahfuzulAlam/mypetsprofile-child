<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class ProfileForms
{

    public function __construct()
    {
        // DNA FORM SHORTCODE
        add_shortcode('dna-form-export', array($this, 'dna_form_export'));
        // DNA CSV EXPORT
        add_action('init', array($this, 'dna_csv_export'));
        // DNA SAVE DATA
        add_action('init', array($this, 'dna_save_data'));
    }

    // DNA FORM EXPORT
    public function dna_form_export()
    {
        // Export PDF From DNA PROFILE
        $this->mpp_export_dna_pdf();
        // Export PDF From DNA PROFILE

        ob_start();
        //$member_id = bbp_get_user_id();
        $member_id = get_current_user_id();
        //$field_group = [1, 2, 3, 4, 12];
        $field_group = [1, 2, 3, 1312, 1311, 355, 356, 251, 384, 388, 392, 204, 205];

?>
        <form id="mpp_profile_box" method="post">
            <?php
            if (get_current_user_id() == $member_id || current_user_can('administrator')) {
                foreach ($field_group as $field_id) {
                    $field = xprofile_get_field($field_id, $member_id);
                    $field_value = in_array($field->type, array("telephone", "url", "email")) ? BP_XProfile_ProfileData::get_value_byid($field->id, $member_id) : xprofile_get_field_data($field->id, $member_id);
                    $visibility_level =  xprofile_get_field_visibility_level($field->id, $member_id);
            ?>
                    <div class="mpp-profile-field">
                        <div class="mpp-profile-header">
                            <h5><?php echo $field->alternate_name ? $field->alternate_name : $field->name; ?></h5>
                            <?php if ($field_id !== 1) : ?>
                                <a class="mpp-change-visibility mpp-change-visibility-<?php echo $field->id; ?>" href="#" data-field="<?php echo $field->id; ?>" data_user="<?php echo $member_id; ?>" data-visibility="<?php echo $visibility_level; ?>">
                                    <span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="mpp-profile-body">
                            <div class="mpp-profile-field-description">
                                <?php echo $field->description; ?>
                            </div>
                            <?php
                            mpp_profile_field_html($field, $field_value, $member_id);
                            ?>
                            <span class="mpp-profile-field-visibility"><span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level); ?>"></span> <?php echo mpp_profile_field_visibility_label($visibility_level); ?></span>
                            <input type="hidden" class="mpp_visibility_input_value mpp_visibility_<?php echo $field->id; ?>" name="mpp_visibility_<?php echo $field->id; ?>" value="" />
                        </div>
                    </div>
                <?php } ?>
                <?php
                $billing_address = get_user_meta($member_id, 'billing_address_1', true);
                $billing_city = get_user_meta($member_id, 'billing_city', true);
                $billing_zip = get_user_meta($member_id, 'billing_postcode', true);
                $billing_unit = get_user_meta($member_id, 'billing_unit', true);
                $billing_state = get_user_meta($member_id, 'billing_state', true);
                $billing_country = get_user_meta($member_id, 'billing_country', true);
                $visibility_level_address = get_user_meta($member_id, 'billing_address_visibility', true);

                $billing_country = !empty($billing_country) ? $billing_country : 'CA';
                $billing_state = !empty($billing_state) ? $billing_state : 'ON';
                ?>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-header">
                        <h5>Address</h5>
                        <a class="mpp-change-visibility mpp-change-visibility-address" href="#" data-field="address" data_user="<?php echo $member_id; ?>" data-visibility="<?php echo $visibility_level_address; ?>">
                            <span class="mpp-icon <?php echo get_mpp_visibolity_icon($visibility_level_address); ?>"></span>
                        </a>
                    </div>
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">Address Line</span>
                        <div><input class="mpp-profile-field-html" type="text" name="mpp_address_box[address]" value="<?php echo $billing_address; ?>" placeholder="Address Line" /></div>
                        <input type="hidden" class="mpp_visibility_input_value mpp_visibility_address" name="mpp_visibility_address" value="" />
                    </div>
                </div>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">City</span>
                        <div><input class="mpp-profile-field-html" type="text" name="mpp_address_box[city]" value="<?php echo $billing_city; ?>" placeholder="City" /></div>
                    </div>
                </div>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">Zip</span>
                        <div><input class="mpp-profile-field-html" type="text" name="mpp_address_box[zip]" value="<?php echo $billing_zip; ?>" placeholder="Zip" /></div>
                    </div>
                </div>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">Unit</span>
                        <div><input class="mpp-profile-field-html" type="text" name="mpp_address_box[unit]" value="<?php echo $billing_unit; ?>" placeholder="Unit" /></div>
                    </div>
                </div>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">State</span>
                        <div>
                            <select class="mpp-profile-field-html" id="mpp_state_field" name="mpp_address_box[state]">
                                <option value="0">Select a State</option>
                                <?php if ($billing_country) : ?>
                                    <?php foreach (WC()->countries->states[$billing_country] as $id => $state) : ?>
                                        <option value="<?php echo $id; ?>" <?php selected($billing_state, $id, true); ?>><?php echo $state; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mpp-profile-field mpp-address-field">
                    <div class="mpp-profile-body">
                        <span class="mpp-profile-field-visibility">Country</span>
                        <div>
                            <select class="mpp-profile-field-html" id="mpp_country_field" name="mpp_address_box[country]">
                                <option value="0">Select a Country</option>
                                <?php foreach (WC()->countries->countries as $id => $country) : ?>
                                    <option value="<?php echo $id; ?>" <?php selected($billing_country, $id, true); ?>><?php echo $country; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" id="wc_states" value='<?php echo json_encode(WC()->countries->states); ?>' />
                    </div>
                </div>
                <div class="dna_form_submit_buttons">
                    <input type="submit" class="button mpp_dna_form_submitted" value="Update" name="mpp_dna_form_submitted_sava_data" />
                    <input type="submit" class="button mpp_dna_form_submitted" value="Download CSV" name="mpp_dna_form_submitted" />
                    <input type="submit" class="button mpp_dna_form_submitted" value="Download PDF" name="mpp_dna_form_submitted_pdf" />
                </div>
            <?php
            }
            ?>
        </form>
        <?php
        return ob_get_clean();
    }

    // CSV EXPORT
    public function dna_csv_export()
    {
        if (is_user_logged_in()) {
            // Export CSV From DNA PROFILE
            if (isset($_POST['mpp_dna_form_submitted']) && !empty($_POST['mpp_dna_form_submitted'])) {
                //$member_id = bbp_get_user_id();
                $member_id = get_current_user_id();
                $exported_fields = array();
                if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
                    $profile_fields = $_POST['mpp_profile_box'];
                    foreach ($profile_fields as $field_id => $field_value) {
                        $field_options = xprofile_get_field($field_id, $member_id);
                        $exported_fields['header'][] = $field_options->name;
                        $exported_fields['data'][] = $field_value;
                        if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';

                        // Save User Data
                        if (get_current_user_id() == $member_id) {
                            xprofile_set_field_data($field_id, $member_id, $field_value);
                            if (isset($_POST['mpp_visibility_' . $field_id]) && !empty($_POST['mpp_visibility_' . $field_id])) {
                                xprofile_set_field_visibility_level($field_id, $member_id, $_POST['mpp_visibility_' . $field_id]);
                            }
                        }
                    }
                }
                //e_var_dump($exported_fields);
                if (isset($_POST['mpp_address_box']) && count($_POST['mpp_address_box']) > 0) {
                    if (isset($_POST['mpp_address_box']) && count($_POST['mpp_address_box']) > 0) {
                        $address_fields = $_POST['mpp_address_box'];
                        foreach ($address_fields as $field_id => $field_value) {
                            $exported_fields['header'][] = ucfirst($field_id);
                            if ($field_id == 'country') {
                                $exported_fields['data'][] = WC()->countries->countries[$field_value];
                            } else if ($field_id == 'state') {
                                if (isset($address_fields['country']) && !empty($address_fields['country']))
                                    $exported_fields['data'][] = WC()->countries->states[$address_fields['country']][$field_value];
                            } else {
                                $exported_fields['data'][] = $field_value;
                            }
                            // Save Address Later
                        }
                    }
                }
                //Export CSV
                if (count($exported_fields) > 0) $this->mpp_export_dna_csv($exported_fields);
            }
        }
    }

    // SAVE DNA DATA
    public function dna_save_data()
    {
        if (is_user_logged_in()) {
            // Export CSV From DNA PROFILE
            if (isset($_POST['mpp_dna_form_submitted_sava_data']) && !empty($_POST['mpp_dna_form_submitted_sava_data'])) {
                $member_id = get_current_user_id();
                if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
                    $profile_fields = $_POST['mpp_profile_box'];
                    foreach ($profile_fields as $field_id => $field_value) {
                        $field_options = xprofile_get_field($field_id, $member_id);
                        if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';

                        // Save User Data
                        if (get_current_user_id() == $member_id) {
                            xprofile_set_field_data($field_id, $member_id, $field_value);
                            if (isset($_POST['mpp_visibility_' . $field_id]) && !empty($_POST['mpp_visibility_' . $field_id])) {
                                xprofile_set_field_visibility_level($field_id, $member_id, $_POST['mpp_visibility_' . $field_id]);
                            }
                        }
                    }
                }
                // SAVE ADDRESS
                if (isset($_POST['mpp_address_box']) && count($_POST['mpp_address_box']) > 0) {
                    if (isset($_POST['mpp_address_box']) && count($_POST['mpp_address_box']) > 0) {
                        $address_fields = $_POST['mpp_address_box'];
                        foreach ($address_fields as $field_id => $field_value) {
                            switch ($field_id) {
                                case 'address':
                                    update_user_meta($member_id, 'billing_address_1', $field_value);
                                    break;
                                case 'city':
                                    update_user_meta($member_id, 'billing_city', $field_value);
                                    break;
                                case 'zip':
                                    update_user_meta($member_id, 'billing_postcode', $field_value);
                                    break;
                                case 'unit':
                                    update_user_meta($member_id, 'billing_unit', $field_value);
                                    break;
                                case 'state':
                                    update_user_meta($member_id, 'billing_state', $field_value);
                                    break;
                                case 'country':
                                    update_user_meta($member_id, 'billing_country', $field_value);
                                    break;
                            }
                        }
                    }
                    // ADDRESS VISIBILITY
                    if (isset($_POST['mpp_visibility_address']) && !empty($_POST['mpp_visibility_address'])) {
                        // billing_address_visibility
                        update_user_meta($member_id, 'billing_address_visibility', $_POST['mpp_visibility_address']);
                    }
                }
            }
        }
    }

    // EXPORT DNA CSV FUNCTION
    public function mpp_export_dna_csv($exported_fields)
    {
        $header_row = $exported_fields['header'];
        ob_start();
        $filename = 'profile_info.csv';
        $fh = @fopen('php://output', 'w');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");
        header('Expires: 0');
        header('Pragma: public');

        fputcsv($fh, $header_row);
        fputcsv($fh, $exported_fields['data']);

        fclose($fh);

        ob_end_flush();
        die();
    }

    // EXPORT DNA PDF FUNCTION
    public function mpp_export_dna_pdf()
    {
        // Export PDF From DNA PROFILE
        if (isset($_POST['mpp_dna_form_submitted_pdf']) && !empty($_POST['mpp_dna_form_submitted_pdf'])) {
            //$member_id = bbp_get_user_id();
            $member_id = get_current_user_id();
            $field_info = array();
            if (isset($_POST['mpp_profile_box']) && count($_POST['mpp_profile_box']) > 0) {
                // Profile
                $profile_fields = $_POST['mpp_profile_box'];
                foreach ($profile_fields as $field_id => $field_value) {
                    $field_options = xprofile_get_field($field_id, $member_id);
                    if ($field_options->type == 'datebox') $field_value = $field_value . ' 00:00:00';
                    $field_info[$field_id]['key'] = $field_options->description ? $field_options->description : $field_options->name;
                    $field_info[$field_id]['value'] = $field_value;

                    //Save User Data
                    if (get_current_user_id() == $member_id) {
                        xprofile_set_field_data($field_id, $member_id, $field_value);
                        if (isset($_POST['mpp_visibility_' . $field_id]) && !empty($_POST['mpp_visibility_' . $field_id])) {
                            xprofile_set_field_visibility_level($field_id, $member_id, $_POST['mpp_visibility_' . $field_id]);
                        }
                    }
                }
                // Address
                if (isset($_POST['mpp_address_box']) && count($_POST['mpp_address_box']) > 0) {
                    $address_fields = $_POST['mpp_address_box'];
                    foreach ($address_fields as $field_id => $field_value) {
                        $field_info[$field_id]['key'] = ucfirst($field_id);
                        if ($field_id == 'country') {
                            $field_info[$field_id]['value'] = WC()->countries->countries[$field_value];
                        } else if ($field_id == 'state') {
                            if (isset($address_fields['country']) && !empty($address_fields['country']))
                                $field_info[$field_id]['value'] = WC()->countries->states[$address_fields['country']][$field_value];
                        } else {
                            $field_info[$field_id]['value'] = $field_value;
                        }
                        // Save Address Later
                    }
                }
                //e_var_dump($field_info);
        ?>
                <script type="text/javascript">
                    startDnaProcessing('<?php echo json_encode($field_info); ?>', '<?php bp_loggedin_user_avatar('html=false'); ?>');
                </script>
<?php
            }
        }
    }
}

new ProfileForms;
