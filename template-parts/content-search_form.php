<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

if (!defined('ABSPATH')) exit;
?>

<form method="get" name="search_animal" class="search-animal" action="/adoption-results/">
    <!-- <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_name">Animal Name</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_name" name="animal_name" class="mpp-profile-field-html" />
        </div>
    </div> -->

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_color_2">Search by Location</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="searchAnimalMap" name="address" class="mpp-profile-field-html" placeholder="Enter city or country" />
            <input type="hidden" id="cityLat" name="cityLat" value="" />
            <input type="hidden" id="cityLng" name="cityLng" value="" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_type">Animal type</label></div>
        <div class="mpp-profile-body">
            <select id="animal_type" name="animal_type" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Bird">Bird</option>
                <option value="Horse">Horse</option>
                <option value="Fish">Fish</option>
                <option value="Reptile">Reptile</option>
                <option value="Others">Others</option>
            </select>
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_gender">Male Or Female</label></div>
        <div class="mpp-profile-body">
            <select id="animal_gender" name="animal_gender" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_age_group">Adult Or Young</label></div>
        <div class="mpp-profile-body">
            <select id="animal_age_group" name="animal_age_group" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="Young">Young</option>
                <option value="Adult">Adult </option>
            </select>
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label>Spayed Or Neutered</label></div>
        <div class="mpp-profile-body">
            <div class="bp-radio-wrap">
                <input type="radio" name="spayed_neutered" id="spayed_neutered_yes" value="Yes" class="bs-styled-radio">
                <label for="spayed_neutered_yes" class="option-label">Yes</label>
            </div>
            <div class="bp-radio-wrap">
                <input type="radio" name="spayed_neutered" id="spayed_neutered_no" value="No" class="bs-styled-radio">
                <label for="spayed_neutered_no" class="option-label">No</label>
            </div>
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_weight">Weight (in Lbs)</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_weight" name="animal_weight" class="mpp-profile-field-html" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_main_breed">Main Breed</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_main_breed" name="animal_main_breed" class="mpp-profile-field-html" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_breed_2">Breed 2</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_breed_2" name="animal_breed_2" class="mpp-profile-field-html" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_main_color">Main Color</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_main_color" name="animal_main_color" class="mpp-profile-field-html" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_color_2">Color 2</label></div>
        <div class="mpp-profile-body">
            <input type="text" id="animal_color_2" name="animal_color_2" class="mpp-profile-field-html" />
        </div>
    </div>

    <div class="mpp-profile-field">
        <div class="mpp-profile-header"><label for="animal_adoption_status">Adopted</label></div>
        <div class="mpp-profile-body">
            <select id="animal_adoption_status" name="animal_adoption_status" class="mpp-profile-field-html">
                <option value="0">Choose</option>
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
        </div>
    </div>

    <div class="mpp-profile-field animal-submit">
        <div class="mpp-profile-body"><input type="submit" id="animal_submit" class="button" value="Search" /></div>
    </div>

</form>