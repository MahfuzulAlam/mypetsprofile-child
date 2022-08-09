<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Rentsync
{
    /**
     * VARIABLES
     */

    private $properties = array();
    private $units = array();
    private $field = array();
    private $submission_form_fields = 'submission_form_fields';
    private $unit_meta_list = [];
    private $property_meta_list = [];
    private $property_location_list = [];
    private $property_contact_list = [];

    private $author_id = 0;

    private $building_types = []; // Property
    private $amenities = []; // Property
    private $utilities = []; // Property
    private $unit_types = []; // Units

    private $locations = [];

    private $source = 'rentsync';
    private $company_name = '';
    private $company_id = '';
    private $company_email = '';
    private $company_phone = '';
    private $company_website = '';
    private $company_logo = '';

    private $property_category = 'apartments';
    private $directory_type = 'pets-community';

    private $unit_category = 'apartment-unit';
    private $directory_type_unit = 'units';

    private $pricing_plan_property = 23181; //20379 //23181
    private $pricing_plan_unit = 23183; //20381 //23183

    private $apiUrl = '';
    private $localUrl = '';

    private $adminPage = '';

    /**
     * CONSTRUCT FUNCTION
     */
    public function __construct()
    {
        //$this->setup();
        add_shortcode('rentsync', array($this, 'rentsync_shortcode'));

        // AVAILABLE UNITS SHORTCODE
        add_shortcode('mpp-apartment-units', array($this, 'rentsync_mpp_apartment_units'));

        // CONTACT UNIT OWNER
        add_shortcode('contact-unit-owner', array($this, 'contact_unit_owner'));

        // AVAILABLE UNITS SHORTCODE
        //add_shortcode('mpp-apartment-units-connection', array($this, 'rentsync_mpp_apartment_units_connection'));

        // RENTSYNC API SETUP
        //add_action('mpp_rentsync_setup_api_data', array($this, 'mpp_rentsync_setup_api_data'));

        // ADMIN MENU LOAD THE API
        add_action('admin_menu', array($this, 'rentsync_admin_import_from_api'));
        // WP AJAX - rentsync_import_all_properties
        add_action('wp_ajax_rentsync_import_all_properties', array($this, 'rentsync_import_all_properties'));
        // WP AJAX - rentsync_count_properties
        add_action('wp_ajax_rentsync_count_properties', array($this, 'rentsync_count_properties'));
        // WP AJAX - rentsync_download_properties
        add_action('wp_ajax_rentsync_download_properties', array($this, 'rentsync_download_properties'));
    }

    /**
     * SETUP BEFORE SAVE API DATA
     */

    public function before_save_api_setup()
    {
        $this->set_remote_api_url();
        $this->set_local_file_location();
    }

    /**
     * SETUP AFTER SAVE API DATA
     */

    public function after_save_api_setup()
    {
        if (!file_exists($this->localUrl)) return;
        $this->set_unit_meta_list();
        $this->set_property_meta_list();
        $this->set_property_contact_list();
        $this->set_property_location_list();
        $this->process_data();
        $this->set_all_information();
        $this->set_author_id();
        //$this->update_all_field_options();
    }


    /**
     * mpp_rentsync_setup_api_data
     */

    public function mpp_rentsync_setup_api_data()
    {
        $this->before_save_api_setup();
        //$this->save_api_info_to_local();
        $this->after_save_api_setup();
        $this->insert_all_properties_units();
    }

    /**
     * INSERT ALL PROPERTIES AND UNITS
     */

    public function insert_all_properties_units()
    {
        // if ($this->properties && count($this->properties) > 0) {
        //     foreach ($this->properties as $property) {
        //         $this->create_property_units($property);
        //     }
        // }
        $this->create_property_units($this->properties[0]);
    }

    /**
     * INSERT PROPERTY AND UNITS ALLTOGATHER
     */

    public function create_property_units($property_info)
    {
        if (!isset($property_info->id) || empty($property_info->id)) return;
        $property_id = $this->create_property($property_info);
        if ($property_id) {
            if (isset($property_info->suites) && count($property_info->suites) > 0) {
                foreach ($property_info->suites as $unit_key => $unit_data) {
                    $unit_data->mpp_property_id = $property_id;
                    $unit_data->locations = $this->get_property_locations($property_info);
                    $unit_data->address = isset($property_info->location->address) ? $property_info->location->address : '';
                    $unit_data->latitude = isset($property_info->location->latitude) ? $property_info->location->latitude : '';
                    $unit_data->longitude = isset($property_info->location->longitude) ? $property_info->location->longitude : '';
                    // SET DEFAULT IMAGE
                    $unit_data->property_image = get_post_meta($property_id, '_listing_prv_img', true) ? get_post_meta($property_id, '_listing_prv_img', true) : '';
                    $this->create_unit($unit_data);
                }
            }
        }
    }

    /**
     * SET UNIT META LIST
     */
    public function set_unit_meta_list()
    {
        $this->unit_meta_list = array(
            "id" => "_unit_id",
            "propertyId" => "_property_id",
            "typeName" => "_unit_type",
            "number" => "_unit_number",
            "bathrooms" => "_bathrooms",
            "bedrooms" => "_bedrooms",
            "squareFeet" => "_unit_size",
            "rate" => "_price",
            "deposit" => '_deposit',
            "available" => "_available",
            "availabilityDate" => '_availability_date',
            "floorplans" => '_floor_plan',
            "virtualTours" => '_virtual_tour',
            "address" => "_address",
            "latitude"  => "_manual_lat",
            "longitude"  => "_manual_lng",
        );
    }


    /**
     * PREPARE UNIT ARGUMENTS
     */
    public function prepare_unit_args($unit_info, $is_unit_available = false)
    {
        $args = [];
        $args['post_title'] = isset($unit_info->typeName) && !empty($unit_info->typeName) ? $unit_info->typeName : 'Unit';
        $args['post_content'] = isset($unit_info->description) && !empty($unit_info->description) ? $unit_info->description : '';
        $args['post_type'] = ATBDP_POST_TYPE;
        $args['post_status'] = 'publish';
        $args['post_author'] = $this->author_id;

        // ADD UNIT NUMBER
        if (isset($unit_info->number) && !empty($unit_info->number)) {
            $args['post_title'] .= " - " . $unit_info->number;
        }

        // DIRECTORY TYPE
        $args['tax_input'] = array(
            ATBDP_DIRECTORY_TYPE => $this->directory_type_unit,
            ATBDP_CATEGORY => $this->get_unit_categories(),
            ATBDP_LOCATION => $unit_info->locations,
        );
        // DIRECTORY TYPE

        // SETUP METADATA
        $args['meta_input'] = $this->prepare_unit_metadata($unit_info, $is_unit_available);
        // SETUP METADATA
        return array_filter($args);
    }

    /**
     * PREPARE UNIT METADATA
     */
    public function prepare_unit_metadata($values, $is_unit_available = false)
    {
        if (empty($values)) return;

        $meta_list = array();
        $meta_args = array();

        // PRICING PLANS
        $meta_args['_fm_plans'] = $this->pricing_plan_unit;
        $meta_args['_fm_plans_by_admin'] = 1;
        // PRICING PLANS

        // POST STATUS
        $meta_args['_listing_status'] = 'post_status';
        $meta_args['_never_expire'] = 1;
        $meta_args['_directory_type'] = $this->get_directory_id_by('slug', $this->directory_type_unit);
        // POST STATUS

        // MPP HOUSING
        if (isset($values->mpp_property_id)) {
            $meta_args['_mpp-housing'] = $values->mpp_property_id;
        }
        // MPP HOUSING

        // SOURCE
        $meta_args['_source'] = 'rentsync';
        $meta_args['_source_company'] = $this->company_name;
        $meta_args['_source_company_id'] = $this->company_id;
        // SOURCE

        // PRICING
        $meta_args['_atbd_listing_pricing'] = 'price';

        $meta_list = $this->unit_meta_list;

        if (empty($meta_list)) return;

        foreach ($meta_list as $meta_key => $meta_value) {
            if ($meta_key == 'floorplans') {
                $meta_args[$meta_value] = $this->get_floor_plan($values);
            } elseif ($meta_key == 'virtualTours') {
                $meta_args[$meta_value] = $this->get_virtual_tour($values);
            } elseif ($meta_key == 'typeName') {
                $meta_args['_unit_title'] = trim($values->$meta_key);
                $meta_args[$meta_value] = $this->get_option_key($this->unit_types, $values->$meta_key);
            } else {
                $meta_args[$meta_value] = trim($values->$meta_key);
            }
        }

        return array_filter($meta_args);
    }

    /**
     * GET UNIT CATEGORIES
     */
    public function get_unit_categories()
    {
        $categories = [];
        $category_id = $this->retrive_create_taxonomy($this->unit_category, ATBDP_CATEGORY, 'slug');
        if ($category_id) {
            $categories[] = $category_id;
            return $categories;
        }
        return '';
    }

    /**
     * CREATE NEW UNIT
     */
    public function create_unit($unit_data)
    {
        if (!isset($unit_data->id) || empty($unit_data->id)) return;

        $is_unit_available = $this->is_unit_available($unit_data->id);

        $args = $this->prepare_unit_args($unit_data, $is_unit_available);

        if ($is_unit_available) {
            $args['ID'] = $is_unit_available;

            // $listing_id = wp_update_post($args);

            // if ($listing_id && !is_wp_error($listing_id)) {
            //     return $listing_id;
            // }
            $this->update_unit($args);
            return $is_unit_available;
        }

        if (!$is_unit_available) {

            $listing_id = wp_insert_post($args);

            if ($listing_id && !is_wp_error($listing_id)) {
                return $listing_id;
            }
        }

        return false;
    }

    /**
     * Update Units
     */
    public function update_unit($unit_info)
    {
        if (!isset($unit_info['ID'])) return;
        // check and update the metas
        if (isset($unit_info['meta_input']) && !empty($unit_info['meta_input'])) :
            foreach ($unit_info['meta_input'] as $meta_key => $meta_value) :
                if ($meta_key == '_available') update_post_meta($unit_info['ID'], '_available', $meta_value);
                if ($meta_key == '_price') update_post_meta($unit_info['ID'], '_price', $meta_value);
                if ($meta_key == '_deposit') update_post_meta($unit_info['ID'], '_deposit', $meta_value);
                if ($meta_key == '_availability_date') update_post_meta($unit_info['ID'], '_availability_date', $meta_value);
                if ($meta_key == '_listing_prv_img') update_post_meta($unit_info['ID'], '_listing_prv_img', $meta_value);
                if ($meta_key == '_manual_lat') update_post_meta($unit_info['ID'], '_manual_lat', $meta_value);
                if ($meta_key == '_manual_lng') update_post_meta($unit_info['ID'], '_manual_lng', $meta_value);
            endforeach;
        endif;
    }

    /**
     * SET PROPERTY META LIST
     */
    public function set_property_meta_list()
    {
        $this->property_meta_list = array(
            "id" => "_property_id",
            "url" => "_website",
            "tagline" => "_tagline",
            "buildingType" => "_building_type",
            "officeHours" => "_office-hours",
            "parkingDetails" => "_parking-details",
            "petFriendly" => "_pet-friendly",
            "petFriendlyCats" => "_pet-friendly-cats",
            "petFriendlyLargeDogs" => "_pet-friendly-large-dogs",
            "petFriendlySmallDogs" => "_pet-friendly-small-dogs",
            "petFriendlyNotAllowed" => "_pets-not-allowed",
            "photos" => "_mpp_photos",
            "videos" => "_mpp_videos",
            "amenities" => "_amenities",
            "utilities" => "_utilities",
            "promotions" => "_promotions",
            "virtualTours" => "_virtual_tours",
        );
    }

    /**
     * SET PROPERTY LOCATION INFORMATION
     */

    public function set_property_location_list()
    {
        $this->property_location_list = array(
            "address" => "_address",
            "city" => "_ca-city",
            "province" => "_ca-province",
            "provinceCode" => "_ca-province-code",
            "country" => "_ca-country",
            "countryCode" => "_ca_country-code",
            "postalCode" => "_zip",
            "intersection" => "_intersection",
            "neighbourhood" => "_neighbourhood",
            "latitude" => "_manual_lat",
            "longitude" => "_manual_lng",
        );
    }

    /**
     * SET PROPERTY CONTACT INFROMATION
     */

    public function set_property_contact_list()
    {
        $this->property_contact_list = array(
            "name" => "_ci-comm-name",
            "email" => "_ci-email",
            "phone" => "_ci-phone",
            "phoneExtension" => "_ci-phone-ext",
            "altPhone" => "_ci-alt-phone",
            "altPhoneExtension" => "_ci-alt-phone-ext",
            "fax" => "_fax",
        );
    }

    /**
     * INSERT PROPERTY INTO DB
     */
    public function create_property($property_data)
    {
        if (!isset($property_data->id) || empty($property_data->id)) return;
        $args = $this->prepare_property_args($property_data);

        $is_property_available = $this->is_property_available($property_data->id);

        if ($is_property_available) {
            return $is_property_available;
            // $args['ID'] = $is_property_available;
            // $listing_id = wp_update_post($args);

            // if ($listing_id && !is_wp_error($listing_id)) {
            //     do_action('atbdp_after_created_listing', $listing_id);
            //     return $listing_id;
            // }
        }

        if (!$is_property_available) {
            $listing_id = wp_insert_post($args);

            if ($listing_id && !is_wp_error($listing_id)) {
                do_action('atbdp_after_created_listing', $listing_id);
                return $listing_id;
            }
        }

        return false;
    }

    /**
     * CHECK AVAILABLE PROPERTY
     */
    public function is_property_available($id = 0)
    {
        if ($id) {

            $query = new WP_Query(
                array(
                    'post_type' => ATBDP_POST_TYPE,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => '_source',
                            'value' => 'rentsync',
                            'compare' => '='
                        ),
                        array(
                            'key' => '_property_id',
                            'value' => $id,
                            'compare' => '='
                        ),
                    ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => ATBDP_DIRECTORY_TYPE,
                            'field'    => 'slug',
                            'terms'    => $this->directory_type,
                        ),
                    ),
                    'fields' => 'ids'
                )
            );

            if ($query) {
                if (isset($query->posts) && count($query->posts) > 0) return $query->posts[0];
            }
        }
        return false;
    }

    /**
     * CHECK AVAILABLE UNIT
     */
    public function is_unit_available($id = 0)
    {
        if ($id) {

            $query = new WP_Query(
                array(
                    'post_type' => ATBDP_POST_TYPE,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => '_source',
                            'value' => 'rentsync',
                            'compare' => '='
                        ),
                        array(
                            'key' => '_unit_id',
                            'value' => $id,
                            'compare' => '='
                        ),
                    ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => ATBDP_DIRECTORY_TYPE,
                            'field'    => 'slug',
                            'terms'    => $this->directory_type_unit,
                        ),
                    ),
                    'fields' => 'ids'
                )
            );

            if ($query) {
                if (isset($query->posts) && count($query->posts) > 0) return $query->posts[0];
            }
        }
        return false;
    }

    /**
     * PREPARE PROPERTY ARGUMENTS
     */
    public function prepare_property_args($property_info)
    {
        $args = [];
        $args['post_title'] = isset($property_info->name) && !empty($property_info->name) ? $property_info->name : '';
        $args['post_content'] = isset($property_info->buildingDescription) && !empty($property_info->buildingDescription) ? $property_info->buildingDescription : '';
        $args['post_type'] = ATBDP_POST_TYPE;
        $args['post_status'] = 'publish';
        $args['post_author'] = $this->author_id;

        // DIRECTORY TYPE
        //$directory_type_term = get_term_by('slug', 'units', ATBDP_DIRECTORY_TYPE);
        // $args['tax_input'] = array(
        //     ATBDP_DIRECTORY_TYPE => $this->directory_type,,
        //     ATBDP_LOCATION => $this->get_property_location($property_info),
        //     ATBDP_CATEGORY => $this->property_category,
        // );
        $args['tax_input'] = array(
            ATBDP_DIRECTORY_TYPE => $this->directory_type,
            ATBDP_LOCATION => $this->get_property_locations($property_info),
            ATBDP_CATEGORY => $this->get_property_categories(),
        );
        // DIRECTORY TYPE

        // SETUP METADATA
        $args['meta_input'] = $this->prepare_property_metadata($property_info, 999);
        // SETUP METADATA
        return array_filter($args);
    }

    /**
     * GET PROPERTY LOCATION
     */
    public function get_property_locations($property)
    {
        $locations = [];
        if (isset($property->location)) {
            if (isset($property->location->city) && !empty($property->location->city)) {
                $locations[] = $this->retrive_create_taxonomy($property->location->city, ATBDP_LOCATION);
            }
            if (isset($property->location->province) && !empty($property->location->province)) {
                $locations[] = $this->retrive_create_taxonomy($property->location->province, ATBDP_LOCATION);
            }
        }
        if (count($locations) > 0) {
            return array_filter($locations);
        }
        return '';
    }

    /**
     * GET PROPERTY CATEGORIES
     */
    public function get_property_categories()
    {
        $categories = [];
        $category_id = $this->retrive_create_taxonomy($this->property_category, ATBDP_CATEGORY, 'slug');
        if ($category_id) {
            $categories[] = $category_id;
            return $categories;
        }
        return '';
    }

    /**
     * SET LOCATION LIST
     */
    public function set_locations()
    {
        $locations = array();
        if ($this->properties && count($this->properties) > 0) {
            foreach ($this->properties as $property) {
                if (isset($property->location->city) && !empty($property->location->city) && !in_array($property->location->city, $locations)) {
                    $locations[] = $property->location->city;
                }
                if (isset($property->location->province) && !empty($property->location->province) && !in_array($property->location->province, $locations)) {
                    $locations[] = $property->location->province;
                }
            }
        }
        e_var_dump($locations);
        if (count($locations) > 0) $this->locations = $locations;
    }

    /**
     * INSERT/UPDATE LOCATION TAXONOMY
     */
    public function update_location_taxonomy()
    {
        $dir_types = array(200, 1414, 1445, 1418);
        $this->set_locations();
        if (count($this->locations) < 1) return;

        foreach ($this->locations as $location) {
            $term = term_exists($location, ATBDP_LOCATION);
            if ($term && !empty($term['term_id'])) {
                // UPDATE LOCATION FOR ALL DIR TYPE
                update_term_meta($term['term_id'], '_directory_type', $dir_types);
                e_var_dump($term['term_id']);
            } else {
                // ADD LOCATION FOR ALL DIR TYPE
                $new_term = wp_insert_term($location, ATBDP_LOCATION);
                if ($new_term) update_term_meta($new_term, '_directory_type', $dir_types);
                e_var_dump($new_term);
            }
        }
    }

    /**
     * RETRIVE/CREATE TAXONOMY
     */
    public function retrive_create_taxonomy($term_name = '', $taxonomy = '', $field = 'title')
    {
        if (empty($term_name) || empty($taxonomy)) return;
        $term = get_term_by($field, $term_name, $taxonomy);
        if ($term) {
            return $term->term_id;
        } else {
            $new_term = wp_create_term($term_name, $taxonomy);
            if ($new_term && !is_wp_error($new_term)) return $new_term['term_id'];
        }
        return '';
    }

    /**
     * PREPARE PROPERTY METADATA
     */
    public function prepare_property_metadata($values, $mpp_housing = 0)
    {
        if (empty($values)) return;

        $meta_list = array();
        $meta_args = array();

        // MPP HOUSING
        if ($mpp_housing) {
            $meta_args['_mpp-housing'] = $mpp_housing;
        }
        // MPP HOUSING

        // PRICING PLANS
        $meta_args['_fm_plans'] = $this->pricing_plan_property;
        $meta_args['_fm_plans_by_admin'] = 1;
        // PRICING PLANS

        // POST STATUS
        $meta_args['_listing_status'] = 'post_status';
        $meta_args['_never_expire'] = 1;
        $meta_args['_directory_type'] = $this->get_directory_id_by('slug', $this->directory_type);
        // POST STATUS

        // SOURCE
        $meta_args['_source'] = 'rentsync';
        $meta_args['_source_company'] = $this->company_name;
        $meta_args['_source_company_id'] = $this->company_id;
        // SOURCE

        $meta_list = $this->property_meta_list;
        $location_list = $this->property_location_list;
        $contact_list = $this->property_contact_list;

        // META LIST
        if (count($meta_list) > 0) {
            foreach ($meta_list as $meta_key => $meta_value) {
                if ($meta_key == 'amenities') {
                    if (isset($values->$meta_key)) $meta_args[$meta_value] = $this->get_amenities($values->$meta_key);
                } else if ($meta_key == 'utilities') {
                    if (isset($values->$meta_key)) $meta_args[$meta_value] = $this->get_utilities($values->$meta_key);
                } else {
                    if (isset($values->$meta_key)) $meta_args[$meta_value] = $values->$meta_key;
                }
            }
        }

        // LOCATION LIST
        if (count($location_list) > 0) {
            foreach ($location_list as $meta_key => $meta_value) {
                if (isset($values->location->$meta_key)) $meta_args[$meta_value] = $values->location->$meta_key;
            }
        }

        // CONTACT LIST
        if (count($contact_list) > 0) {
            foreach ($contact_list as $meta_key => $meta_value) {
                if (isset($values->contactInformation->$meta_key)) $meta_args[$meta_value] = $values->contactInformation->$meta_key;
            }
        }

        // LISTING IMAGE
        if (isset($values->photos) && count($values->photos) > 0) {
            $listing_image = $this->import_image($values->photos[0]->url);
            if ($listing_image) $meta_args['_listing_prv_img'] = $listing_image;
        }

        return array_filter($meta_args);
    }
    // PREPARE METADATA

    // GET FLOOR PLAN
    public function get_floor_plan($values)
    {
        $floorplans = isset($values->floorplans) && !empty($values->floorplans) ? $values->floorplans : array();
        if ($floorplans && count($floorplans) > 0) {
            $floorplan = $floorplans[0];
            if (isset($floorplan->image) && !empty($floorplan->image)) return $floorplan->image;
        }
        return false;
    }
    // GET FLOOR PLAN

    // GET VIRTUAL TOUR
    public function get_virtual_tour($values)
    {
        $virtualtours = isset($values->virtualTours) && !empty($values->virtualTours) ? $values->virtualTours : array();
        if ($virtualtours && count($virtualtours) > 0) {
            $virtualtour = $virtualtours[0];
            if (isset($virtualtour->url) && !empty($virtualtour->url)) return $virtualtour->url;
        }
        return '';
    }
    // GTE VIRTUAL TOUR

    public function rentsync_shortcode()
    {
        if (!is_user_logged_in() || !current_user_can('administrator')) return;
        $result = false;
        $property_key = isset($_REQUEST['property']) && !empty($_REQUEST['property']) ? $_REQUEST['property'] : 0;
        $taxonomy_update = isset($_REQUEST['taxonomy']) && !empty($_REQUEST['taxonomy']) ? $_REQUEST['taxonomy'] : '';

        if ($taxonomy_update && !empty($taxonomy_update)) {
            $this->before_save_api_setup();
            if (!file_exists($this->localUrl)) $this->save_api_info_to_local();
            if (file_exists($this->localUrl)) {
                $this->after_save_api_setup();
                if ($taxonomy_update == 'location') $this->update_location_taxonomy();
            }
        } else {
            if (!$property_key || empty($property_key)) return;
            $limit = isset($_REQUEST['limit']) && !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
            $range = isset($_REQUEST['range']) && !empty($_REQUEST['range']) ? $_REQUEST['range'] : 1;
            $this->before_save_api_setup();
            if (!file_exists($this->localUrl)) $this->save_api_info_to_local();
            if (file_exists($this->localUrl)) {
                $this->after_save_api_setup();
                if ($property_key) :
                    for ($i = 0; $i < $range; $i++) {
                        if ($property_key <= $limit) {
                            if (isset($this->properties[$property_key])) {
                                if ($property_key == 1) $this->create_property_units($this->properties[0]);
                                $this->create_property_units($this->properties[$property_key]);
                                if ($range > 1) $property_key = $property_key + 1;
                                $result = true;
                            }
                        } else {
                            $result = false;
                        }
                    }
                endif;
            }
        }

        ob_start();

        if ($result) {
            echo '<p>Imported Successfully - ' . $property_key . '</p>';

            if ($property_key <= $limit) {
                if ($range == 1) $property_key = $property_key + 1;
                $redirect_url = home_url('/rentsync-import/') . '?property=' . $property_key . '&limit=' . $limit . '&range=' . $range;
?>
                <script type="text/javascript">
                    window.location.href = "<?php echo $redirect_url; ?>";
                </script>
<?php
            }
        }



        return ob_get_clean();
    }


    // PROCESS DATA
    public function process_data()
    {
        $url = $this->localUrl;
        $data = $this->call_api_with_fgc($url);

        if ($data && isset($data->company->properties)) {
            $this->company_id = $data->company->id;
            $this->company_name = $data->company->name;
            $this->company_email = $data->company->email;
            $this->company_phone = $data->company->phone;
            $this->company_website = $data->company->website;
            $this->company_logo = $data->company->logo;
            $this->properties = $data->company->properties;
        }
    }

    // PROCESS ALL INFORMATION
    public function set_all_information()
    {
        if (!$this->properties || count($this->properties) < 1) return;

        $amenity_list = [];
        $utility_list = [];
        $building_types = [];
        $unit_types = [];

        foreach ($this->properties as $property) {

            // BUILDING TYPES
            if (!in_array($property->buildingType, $building_types))  $building_types[$property->buildingType] = $this->mpp_slugify_text($property->buildingType, '-', 'alt');

            // AMENITIES
            if (isset($property->amenities) && !empty($property->amenities)) {
                foreach ($property->amenities as $aminity) {
                    if (!in_array($aminity->name, $amenity_list))  $amenity_list[$this->mpp_slugify_text($aminity->name)] = $aminity->name;
                }
            }

            // UTILITIES
            if (isset($property->utilities) && !empty($property->utilities)) {
                foreach ($property->utilities as $utility) {
                    if (!in_array($utility->name, $utility_list))  $utility_list[$this->mpp_slugify_text($utility->name)] = $utility->name;
                }
            }

            // UNIT TYPES
            if (isset($property->suites) && !empty($property->suites)) {
                foreach ($property->suites as $suite) {
                    if (!in_array($suite->typeName, $unit_types))  $unit_types[$this->mpp_slugify_text($suite->typeName)] = $suite->typeName;
                }
            }
        }

        $this->amenities = $amenity_list;
        $this->utilities = $utility_list;
        $this->building_types = $building_types;
        $this->unit_types = $unit_types;
    }

    /**
     * SET LOCAL FILE LOCATION
     */
    public function set_local_file_location()
    {
        $this->localUrl = MPP_CHILD_FILE_DIR . '/rentsync.json';
    }

    /**
     * SET REMOTE API URL
     */
    public function set_remote_api_url()
    {
        $this->apiUrl = 'https://api.theliftsystem.com/v2/feeds/direct/my_pets_profile?auth_token=x2zQ4xmp5ALojxD61ZsA&company=avenueliving';
    }

    // GET UNITS OF A FLAT
    public function get_units($property_id)
    {
        if (!$this->properties || count($this->properties) < 1) return;

        foreach ($this->properties as $property) {
            if ($property->id == $property_id) {
                $this->units = $property->suites;
                return;
            }
        }
    }

    // GET THE FIELDS
    public function get_field($field_name)
    {
        if (!$this->properties || count($this->properties) < 1) return;

        $field = array();

        foreach ($this->properties as $property) {
            //if (isset($property->$field_name) && !empty($property->$field_name)) {
            if ($field_name == 'amenities' || $field_name == 'utilities') {
                $field_values = $property->$field_name;
                foreach ($field_values as $field_value) {
                    if (!in_array($field_value->name, $field))  $field[] = $field_value->name;
                }
            } elseif ($field_name == 'suitTypeName') {
                $field_values = $property->suites;
                foreach ($field_values as $field_value) {
                    if (!in_array($field_value->typeName, $field))  $field[$this->mpp_slugify_text($field_value->typeName)] = $field_value->typeName;
                }
            } else {
                $field_value = $property->$field_name;
                if (!in_array($field_value, $field))  $field[] = $property->$field_name;
            }
            //}
        }

        if (count($field) > 0) $this->field = $field;
    }

    // CALL API
    public function call_api($url = "")
    {
        // create & initialize a curl session
        $curl = curl_init();

        // set our url with curl_setopt()
        curl_setopt($curl, CURLOPT_URL, $url);

        // return the transfer as a string, also with setopt()
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux Centos 7;) Chrome/74.0.3729.169 Safari/537.36");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        // curl_exec() executes the started curl session
        // $output contains the output string
        $output = curl_exec($curl);

        // close curl resource to free up system resources
        // (deletes the variable made by curl_init)
        curl_close($curl);

        if ($output)
            $output = json_decode($output);

        return $output;
    }

    /**
     * CALL API
     */
    public function call_api_with_fgc($url)
    {
        $response = file_get_contents($url);
        if ($response) $response = json_decode($response);
        return $response;
    }

    /**
     * SAVE API INFO TO LOCAL FILE
     */
    public function save_api_info_to_local()
    {
        if (!empty($this->apiUrl)) {
            $response = file_get_contents($this->apiUrl);
            if ($response && !empty($response)) {
                file_put_contents($this->localUrl, $response);
            }
        }
    }

    // UPDATE UNIT TYPE
    public function update_unit_type()
    {
        $this->get_field('suitTypeName');
    }

    /**
     * GET LISTING FORM FIELD DATA
     */
    public function get_form_field_data($listing_type = '', $field_key = '')
    {
        if (empty($listing_type) || empty($field_key)) return;
        $dir_type = get_term_by('slug', $listing_type, ATBDP_DIRECTORY_TYPE);
        if ($dir_type && !empty($field_key)) {
            $field_info = get_term_meta($dir_type->term_id, $this->submission_form_fields, true);
            if ($field_info && !empty($field_info)) {
                foreach ($field_info['fields'] as $field) {
                    if ($field['field_key'] == $field_key) return $field;
                }
            }
        }
        return false;
    }

    /**
     * UPDATE LISTING FORM FIELD DATA
     */
    public function update_form_field_data($listing_type = '', $field_key = '', $field_value)
    {
        if (empty($listing_type) || empty($field_key) || empty($field_value)) return;
        $dir_type = get_term_by('slug', $listing_type, ATBDP_DIRECTORY_TYPE);
        if ($dir_type && !empty($field_key)) {
            $field_info = get_term_meta($dir_type->term_id, $this->submission_form_fields, true);
            if ($field_info && !empty($field_info)) {
                foreach ($field_info['fields'] as $key => $field) {
                    if ($field['field_key'] == $field_key) {
                        $field_info['fields'][$key] = $field_value;
                        break;
                    }
                }
            }
            update_term_meta($dir_type->term_id, $this->submission_form_fields, $field_info);
        }
        return false;
    }

    /**
     * INSERT A VALUE TO THE OPTION OF A LISTING FORM FILED
     */
    public function insert_option_to_listing_form_field($listing_type = '', $field_key = '', $options = [])
    {
        if (empty($listing_type) || empty($field_key) || empty($options)) return;
        $field_data = $this->get_form_field_data($listing_type, $field_key);
        if (!$field_data) return;
        $option_data = isset($field_data['options']) && !empty($field_data['options']) ? $field_data['options'] : array();
        if (!$option_data || empty($option_data)) return;
        $new_option_data = $option_data;
        foreach ($options as $key => $option) {
            if (!$this->is_option_available($key, $new_option_data)) {
                $new_option_data[] = array(
                    'option_value' => $key,
                    'option_label' => $option
                );
            }
        }
        $field_data['options'] = $new_option_data;
        $this->update_form_field_data($listing_type, $field_key, $field_data);
    }

    /**
     * IS OPTION AVAILABLE
     */

    public function is_option_available($key = '', $option_data = [])
    {
        if (empty($key) || empty($option_data)) return false;
        foreach ($option_data as $option) {
            if ($option['option_value'] == $key) return true;
        }
        return false;
    }

    /**
     * UPDATE ALL FIELD OPTIONS
     */
    public function update_all_field_options()
    {
        $this->insert_option_to_listing_form_field($this->directory_type_unit, 'unit_type', $this->unit_types);
        $this->insert_option_to_listing_form_field($this->directory_type_unit, 'amenities', $this->amenities);
        $this->insert_option_to_listing_form_field($this->directory_type, 'building_type', $this->building_types);
        $this->insert_option_to_listing_form_field($this->directory_type, 'amenities', $this->amenities);
        $this->insert_option_to_listing_form_field($this->directory_type, 'utilities', $this->utilities);
    }

    // GET OPTIONKEY
    public function get_option_key($options, $value)
    {
        return array_search($value, $options);
    }

    // GET AMENITIES
    public function get_amenities($amenities)
    {
        $list = [];
        if (isset($amenities) && count($amenities) > 0) {
            foreach ($amenities as $amenity) {
                $list[] = $this->get_option_key($this->amenities, $amenity->name);
            }
        }
        return $list;
    }

    // GET UTILITIES
    public function get_utilities($utilities)
    {
        $list = [];
        if (isset($utilities) && count($utilities) > 0) {
            foreach ($utilities as $utility) {
                $list[] = $this->get_option_key($this->utilities, $utility->name);
            }
        }
        return $list;
    }

    /**
     * GET DIRECTORY TYPE ID FROM SLUG/NAME
     */
    public function get_directory_id_by($field = 'slug', $value = '')
    {
        if (!empty($value)) {
            $term = get_term_by($field, $value, ATBDP_DIRECTORY_TYPE);
            if ($term) return $term->term_id;
        }
    }

    /**
     * CHECK IF AUTHOR EXISTS
     */
    public function set_author_id()
    {
        if (!empty($this->company_email)) {
            $this->author_id = $this->get_author_id($this->company_email);
        }
        if (empty($this->author_id)) $this->author_id = get_current_user_id();
    }

    /**
     * GET AUTHOR ID
     */
    public function get_author_id($email = '')
    {
        if (empty($email)) return;
        $author = get_user_by('email', $email);
        if ($author) {
            return $author->ID;
        }
        return '';
    }

    /**
     * IMPORT IMAGE INTO THE DB
     */

    public function import_image($image_url = '', $image_id = 0)
    {
        if (empty($image_url)) return;
        if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $contents = @file_get_contents($image_url);
        if ($contents === false) {
            return false;
        }
        $upload = wp_upload_bits(basename($image_url), null, $contents);
        if (isset($upload['error']) && $upload['error']) {
            return false;
        }
        $type = '';
        if (!empty($upload['type'])) {
            $type = $upload['type'];
        } else {
            $mime = wp_check_filetype($upload['file']);
            if ($mime) {
                $type = $mime['type'];
            }
        }
        $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
        $id = wp_insert_attachment($attachment, $upload['file']);
        if ($id && !is_wp_error($id)) {
            wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
            if ($image_id) update_post_meta($id, '_asset_id', $image_id);
            return $id;
        } else {
            return false;
        }
    }

    // SLUGIFY TEXT
    public function mpp_slugify_text($text, string $divider = '_', string $type = '')
    {
        if ($type == 'alt') {
            $text = str_replace($divider, " ", $text);
            $text = ucwords($text);
        } else if ($type == 'phone') {
            $text = str_replace($divider, "", $text);
            $text = ucwords($text);
        } else {
            // replace non letter or digits by divider
            $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

            // transliterate
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            // trim
            $text = trim($text, $divider);

            // remove duplicate divider
            $text = preg_replace('~-+~', $divider, $text);

            // lowercase
            $text = strtolower($text);
        }

        if (empty($text)) {
            return false;
        }

        return $text;
    }

    /**
     * RENTSYNC IMPORT FROM API OPTIONS
     */
    public function rentsync_admin_import_from_api()
    {
        add_submenu_page(
            'edit.php?post_type=at_biz_dir',
            __('RentSync Import', 'buddyboss-theme'),
            __('RentSync Import', 'buddyboss-theme'),
            'manage_options',
            'rentsync-api',
            array($this, 'rentsync_admin_import_from_api_template')
        );
    }

    /**
     * RENTSYNC IMPORT FROM API OPTIONS TEMPLATE
     */
    public function rentsync_admin_import_from_api_template()
    {
        get_template_part('template-parts/admin/content', 'rentsync_api');
    }

    /**
     * RENTSYNC AJAX CALL - rentsync_count_properties
     */
    public function rentsync_count_properties()
    {
        $status = false;
        $count = 0;
        $this->before_save_api_setup();
        if (!file_exists($this->localUrl)) $this->save_api_info_to_local();
        if (file_exists($this->localUrl)) {
            $this->after_save_api_setup();
            $count = count($this->properties);
            if ($count > 0) $status = true;
        }
        echo json_encode(array('status' => $status, 'count' => $count));
        die();
    }

    /**
     * RENTSYNC AJAX CALL - rentsync_import_all_properties
     */
    public function rentsync_import_all_properties()
    {
        $result = false;
        $property_key = isset($_REQUEST['property_key']) ? $_REQUEST['property_key'] : 'none';
        $this->before_save_api_setup();
        if (!file_exists($this->localUrl)) $this->save_api_info_to_local();
        if (file_exists($this->localUrl)) {
            $this->after_save_api_setup();
            if ($property_key != 'none') :
                $this->create_property_units($this->properties[$property_key]);
                $result = true;
            endif;
        }
        echo json_encode(array('result' => $result));
        die();
    }

    /**
     * RENTSYNC AJAX CALL - rentsync_download_properties
     */
    public function rentsync_download_properties()
    {
        $result = false;
        $this->before_save_api_setup();
        $this->save_api_info_to_local();
        $this->after_save_api_setup();
        $this->update_all_field_options();
        $result = true;
        echo json_encode(array('result' => $result));
        die();
    }

    /**
     * RENTSYNC SHORTCODE - rentsync_mpp_apartment_units
     */
    public function rentsync_mpp_apartment_units()
    {
        $data = $this->get_units_by_property(get_the_ID());
        ob_start();
        if ($data) :
            require(get_stylesheet_directory() . '/includes/directorist/templates/single/rentsync_units.php');
        else :
            echo '<p>Sorry, there are not available units.</p>';
        endif;
        return ob_get_clean();
    }

    /**
     * RENTSYNC SHORTCODE - rentsync_mpp_apartment_units_connection
     */
    public function rentsync_mpp_apartment_units_connection()
    {
        ob_start();
        require(get_stylesheet_directory() . '/includes/directorist/templates/single/rentsync_units_connection.php');
        return ob_get_clean();
    }

    /**
     * GET UNITS OF A PROPERTY
     */
    public function get_units_by_property($property_id = 0)
    {
        if (!$property_id) return;
        $units = new wp_query(array(
            'post_type' => ATBDP_POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_source',
                    'value' => 'rentsync',
                    'compare' => '='
                ),
                array(
                    'key' => '_mpp-housing',
                    'value' => $property_id,
                    'compare' => '='
                ),
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => ATBDP_DIRECTORY_TYPE,
                    'field'    => 'slug',
                    'terms'    => $this->directory_type_unit,
                ),
            ),
        ));
        return isset($units->posts) ? $units->posts : false;
    }

    /**
     * SHORTCODE - contact_unit_owner
     */
    public function contact_unit_owner()
    {
        ob_start();
        $phone = '';
        $building  = get_post_meta(get_the_ID(), '_mpp-housing', true);
        if ($building) $phone = get_post_meta($building, '_ci-phone', true);
        if (!empty($phone)) :
            $phone = $this->mpp_slugify_text($phone, '-', 'phone');
            $email = get_post_meta($building, '_ci-email', true);
            require(get_stylesheet_directory() . '/includes/directorist/templates/single/rentsync_units_contact.php');
        endif;
        return ob_get_clean();
    }
}

new MPP_Rentsync;
