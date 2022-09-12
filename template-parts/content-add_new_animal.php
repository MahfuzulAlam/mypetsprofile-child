<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;
?>

<form method="post" name="add_animal" class="add-animal" enctype="multipart/form-data">
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_name">Animal Name *</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_name" name="animal_name" class="mpp-profile-field-html" value="<?php echo isset($args['animal_name']) ? $args['animal_name'] : ''; ?>" required="required" />
        </div>
    </div>
    <?php
    $animal_type = isset($args['animal_type']) ? $args['animal_type'] : '';
    $animal_types = array('Dog', 'Cat', 'Bird', 'Horse', 'Fish', 'Reptiles', 'Others');
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_type">Animal type</label></div>
        <div class="mpp-profile-body">
            <select id="animal_type" name="animal_type" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <?php
                foreach ($animal_types as $type) {
                ?>
                    <option value="<?php echo $type; ?>" <?php selected($animal_type, $type, true); ?>><?php echo $type; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <?php
    $animal_gender = isset($args['animal_gender']) ? $args['animal_gender'] : '';
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_gender">Male Or Female</label></div>
        <div class="mpp-profile-body">
            <select id="animal_gender" name="animal_gender" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="Male" <?php selected($animal_gender, 'Male', true); ?>>Male</option>
                <option value="Female" <?php selected($animal_gender, 'Female', true); ?>>Female</option>
            </select>
        </div>
    </div>
    <?php
    $animal_age_group = isset($args['animal_age_group']) ? $args['animal_age_group'] : '';
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_age_group">Adult Or Young</label></div>
        <div class="mpp-profile-body">
            <select id="animal_age_group" name="animal_age_group" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="Young" <?php selected($animal_age_group, 'Young', true); ?>>Young</option>
                <option value="Adult" <?php selected($animal_age_group, 'Adult', true); ?>>Adult </option>
            </select>
        </div>
    </div>
    <?php
    $spayed_neutered = isset($args['spayed_neutered']) ? $args['spayed_neutered'] : '';
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label>Spayed Or Neutered</label></div>
        <div class="mpp-profile-body">
            <div class="bp-radio-wrap">
                <input type="radio" name="spayed_neutered" id="spayed_neutered_yes" value="Yes" class="bs-styled-radio" <?php checked($spayed_neutered, 'Yes', true); ?>>
                <label for="spayed_neutered_yes" class="option-label">Yes</label>
            </div>
            <div class="bp-radio-wrap">
                <input type="radio" name="spayed_neutered" id="spayed_neutered_no" value="No" class="bs-styled-radio" <?php checked($spayed_neutered, 'No', true); ?>>
                <label for="spayed_neutered_no" class="option-label">No</label>
            </div>
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_weight">Weight (in Lbs)</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_weight" name="animal_weight" class="mpp-profile-field-html" value="<?php echo isset($args['animal_weight']) ? $args['animal_weight'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_main_breed">Main Breed</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_main_breed" name="animal_main_breed" class="mpp-profile-field-html" value="<?php echo isset($args['animal_main_breed']) ? $args['animal_main_breed'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_breed_2">Breed 2</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_breed_2" name="animal_breed_2" class="mpp-profile-field-html" value="<?php echo isset($args['animal_breed_2']) ? $args['animal_breed_2'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_main_color">Main Color</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_main_color" name="animal_main_color" class="mpp-profile-field-html" value="<?php echo isset($args['animal_main_color']) ? $args['animal_main_color'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_color_2">Color 2</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_color_2" name="animal_color_2" class="mpp-profile-field-html" value="<?php echo isset($args['animal_color_2']) ? $args['animal_color_2'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_address">Address</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="searchAnimalMap" name="animal_address" class="mpp-profile-field-html" value="<?php echo isset($args['animal_address']) ? $args['animal_address'] : ''; ?>" />
            <input type="hidden" id="cityLat" name="cityLat" class="mpp-profile-field-html" value="<?php echo isset($args['cityLat']) ? $args['cityLat'] : ''; ?>" />
            <input type="hidden" id="cityLng" name="cityLng" class="mpp-profile-field-html" value="<?php echo isset($args['cityLng']) ? $args['cityLng'] : ''; ?>" />
        </div>
    </div>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_description">Description</label></div>
        <div class="mpp-profile-body">
            <textarea id="animal_description" name="animal_description" class="mpp-profile-field-html"><?php echo isset($args['animal_description']) ? $args['animal_description'] : ''; ?></textarea>
        </div>
    </div>
    <?php
    $animal_id = isset($args['animal_id']) ? $args['animal_id'] : '';
    $image_url = get_the_post_thumbnail_url($animal_id, 'thumbnail');
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_image">Upload Image</label></div>
        <div class="mpp-profile-body">
            <input type="file" name="animal_image_upload" />
            <input type="hidden" name="animal_image" class="animal_image" id="animal_image" />
            <div class="animal-display-image"><img src="<?php echo $image_url; ?>" width="100" /></div>
        </div>
    </div>
    <?php
    $animal_adoption_status = isset($args['animal_adoption_status']) ? $args['animal_adoption_status'] : '';
    ?>
    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_adoption_status">Adopted</label></div>
        <div class="mpp-profile-body">
            <select id="animal_adoption_status" name="animal_adoption_status" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="No" <?php selected($animal_adoption_status, 'No', true); ?>>No</option>
                <option value="Yes" <?php selected($animal_adoption_status, 'Yes', true); ?>>Yes</option>
            </select>
        </div>
    </div>
    <div class="mpp-profile-field animal-submit">
        <div class="mpp-profile-body"><input type="submit" id="animal_submit" class="button" name="animal_submit" value="SUBMIT" /></div>
    </div>
</form>