<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Rentsync
{

    public function __construct()
    {
        $this->setup();
        add_shortcode('rentsync', array($this, 'rentsync'));
    }

    private $properties = array();
    private $units = array();
    private $field = array();
    private $submission_form_fields = 'submission_form_fields';
    private $unit_meta_list = [];
    private $property_meta_list = [];
    private $property_location_list = [];
    private $property_contact_list = [];

    private $building_types = [];
    private $amenities = [];
    private $utilities = [];
    private $unit_types = [];

    private $source = 'rentsync';
    private $company_name = '';
    private $company_id = '';
    private $company_email = '';
    private $company_phone = '';
    private $company_website = '';
    private $company_logo = '';

    private $property_category = 'apartments';
    private $directory_type = 'pets-community';

    private $apiUrl = 'https://api.theliftsystem.com/v2/feeds/direct/my_pets_profile?auth_token=x2zQ4xmp5ALojxD61ZsA&company=avenueliving';
    private $localUrl = __DIR__ . '/listings.json';


    public function setup()
    {
        $this->set_unit_meta_list();
        $this->set_property_meta_list();
        $this->set_property_contact_list();
        $this->set_property_location_list();
        $this->process_data();
        $this->set_all_information();
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
        );
    }


    /**
     * PREPARE UNIT ARGUMENTS
     */
    public function prepare_unit_args($unit_info)
    {
        $args = [];
        $args['post_title'] = isset($unit_info->typeName) && !empty($unit_info->typeName) ? $unit_info->typeName . ' - ' . $unit_info->id : $unit_info->id;
        $args['post_content'] = isset($unit_info->description) && !empty($unit_info->description) ? $unit_info->description : '';
        $args['post_type'] = ATBDP_POST_TYPE;
        $args['post_status'] = 'publish';

        // DIRECTORY TYPE
        //$directory_type_term = get_term_by('slug', 'units', ATBDP_DIRECTORY_TYPE);
        $args['tax_input'] = array(
            ATBDP_DIRECTORY_TYPE => 'units'
        );
        // DIRECTORY TYPE

        // SETUP METADATA
        $args['meta_input'] = $this->prepare_unit_metadata($unit_info, 999);
        // SETUP METADATA
        return array_filter($args);
    }

    /**
     * PREPARE UNIT METADATA
     * */
    public function prepare_unit_metadata($values, $mpp_housing = 0)
    {
        if (empty($values)) return;

        $meta_list = array();
        $meta_args = array();

        // MPP HOUSING
        if ($mpp_housing) {
            $meta_args['_mpp-housing'] = $mpp_housing;
        }
        // MPP HOUSING

        // SOURCE
        $meta_args['_source'] = 'rentsync';
        $meta_args['_source_company'] = $this->company_name;
        $meta_args['_source_company_id'] = $this->company_id;
        // SOURCE

        // LISTING IMAGE
        if (isset($values->floorplans) && count($values->floorplans) > 0) {
            $meta_args['_atbdp_listing_images'] = $values->floorplans[0]->url;
        }
        // LISTING IMAGE

        $meta_list = $this->unit_meta_list;

        if (empty($meta_list)) return;

        foreach ($meta_list as $meta_key => $meta_value) {
            if ($meta_key == 'floorplans') {
                $meta_args[$meta_value] = $this->get_floor_plan($values);
            } elseif ($meta_key == 'virtualTours') {
                $meta_args[$meta_value] = $this->get_virtual_tour($values);
            } elseif ($meta_key == 'typeName') {
                $meta_args[$meta_value] = $this->get_option_key($this->unit_types, $values->$meta_key);
            } else {
                $meta_args[$meta_value] = $values->$meta_key;
            }
        }

        return array_filter($meta_args);
    }
    // PREPARE METADATA


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
            "address" => "_address_line_1",
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
    public function insert_property($property_data)
    {
        if (!isset($property_data->id) || empty($property_data->id)) return;
        $args = $this->prepare_property_args($property_data);

        if (!$this->is_listing_available($property_data->id)) {
            $listing_id = wp_insert_post($args);
            return $listing_id;
        }

        return false;
    }

    /**
     * CHECK AVAILABLE LISTING
     */
    public function is_listing_available($id = 0)
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
                    'fields' => 'id'
                )
            );

            if ($query) {
                if (isset($query->posts) && count($query->posts) > 0) return true;
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
        $args['post_author'] = 1;

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
        $meta_args['_fm_plans'] = 785;
        $meta_args['_fm_plans_by_admin'] = 1;
        // PRICING PLANS

        // POST STATUS
        $meta_args['_listing_status'] = 'post_status';
        $meta_args['_never_expire'] = 1;
        $meta_args['_directory_type'] = $this->directory_type;
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
            $meta_args['_atbdp_listing_images'] = $values->photos[0]->url;
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
        return '';
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

    public function rentsync()
    {

        //$properties = $this->properties;
        //$this->get_units(693);
        //$this->get_field('suitTypeName');
        //$dir = $this->get_form_field_info('units', 'unit_type');
        ob_start();

        e_var_dump($this->get_property_locations($this->properties[15]));
        e_var_dump($this->get_property_categories());
        e_var_dump($this->insert_property($this->properties[15]));


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

    public function call_api_with_fgc($url)
    {
        $response = file_get_contents($url);
        if ($response) $response = json_decode($response);
        return $response;
    }

    // UPDATE UNIT TYPE
    public function update_unit_type()
    {
        $this->get_field('suitTypeName');
    }

    // GET LISTING FIELD INFO
    public function get_form_field_info($listing_type = '', $field_key = '')
    {
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

    // SLUGIFY TEXT
    public function mpp_slugify_text($text, string $divider = '_', string $type = '')
    {
        if ($type == 'alt') {
            $text = str_replace($divider, " ", $text);
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
}

new MPP_Rentsync;
