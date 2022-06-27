<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class Pet_Adoption
{

    public function __construct()
    {
        // CUSTOM POST TYPE ANIMAL
        add_action('init', array($this, 'custom_post_type_animal'), 0);
        // DISABLE GUTENBERG
        add_filter('use_block_editor_for_post_type', array($this, 'animal_disable_gutenberg'), 10, 2);
        // Add New Animal SHORTCODE
        add_shortcode('add-new-animal', array($this, 'add_new_animal'));
        // ADOPTION PET LIST
        add_shortcode('adoption-pet-list', array($this, 'adoption_pet_list'));
        // ADOPTION SEARCH FORM
        add_shortcode('adoption-search-form', array($this, 'adoption_search_form'));
        // ADOPTION SEARCH RESULTS
        add_shortcode('adoption-search-results', array($this, 'adoption_search_results'));
        // DELETE ANIMAL AJAX CALL
        add_action('wp_ajax_mpp_delete_animal', array($this, 'mpp_delete_animal'));
        // ADOPION CSV IMPORT
        add_shortcode('adoption-csv-import', array($this, 'adoption_csv_import'));
    }

    public function custom_post_type_animal()
    {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x('Animals', 'Post Type General Name', 'buddyboss-theme'),
            'singular_name'       => _x('Animal', 'Post Type Singular Name', 'buddyboss-theme'),
            'menu_name'           => __('Animals', 'buddyboss-theme'),
            'parent_item_colon'   => __('Parent Animal', 'buddyboss-theme'),
            'all_items'           => __('All Animals', 'buddyboss-theme'),
            'view_item'           => __('View Animal', 'buddyboss-theme'),
            'add_new_item'        => __('Add New Animal', 'buddyboss-theme'),
            'add_new'             => __('Add New', 'buddyboss-theme'),
            'edit_item'           => __('Edit Animal', 'buddyboss-theme'),
            'update_item'         => __('Update Animal', 'buddyboss-theme'),
            'search_items'        => __('Search Animal', 'buddyboss-theme'),
            'not_found'           => __('Not Found', 'buddyboss-theme'),
            'not_found_in_trash'  => __('Not found in Trash', 'buddyboss-theme'),
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => __('animals', 'buddyboss-theme'),
            'description'         => __('Animal Information', 'buddyboss-theme'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
            'taxonomies'          => array('genres'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-pets',
        );

        // Registering your Custom Post Type
        register_post_type('animal', $args);
    }

    // Disable Gutenberg
    public function animal_disable_gutenberg($current_status, $post_type)
    {
        if (in_array($post_type, array('animal'))) {
            return false;
        }
        return $current_status;
    }

    // Add New Animal Shortcode
    public function add_new_animal()
    {
        ob_start();
        if (isset($_POST['animal_submit'])) $this->add_new_animal_process();
        $args = $this->edit_animal_information();
        get_template_part('template-parts/content', 'add_new_animal', $args);
        return ob_get_clean();
    }

    // EDIT ANIMAL INFO
    public function edit_animal_information()
    {
        $args = array();
        $animal = isset($_GET['animal']) && !empty($_GET['animal']) ? $_GET['animal'] : 0;
        if ($animal) {
            $animal_info = get_post($animal, 'ARRAY_A');
            $args = array(
                'animal_id' => $animal,
                'animal_name' => $animal_info['post_title'],
                'animal_description' => $animal_info['post_content']
            );
            $animal_metas = get_post_meta($animal);
            if (isset($animal_metas['animal_gender']) && !empty($animal_metas['animal_gender'][0])) $args['animal_gender'] = $animal_metas['animal_gender'][0];
            if (isset($animal_metas['animal_type']) && !empty($animal_metas['animal_type'][0])) $args['animal_type'] = $animal_metas['animal_type'][0];
            if (isset($animal_metas['animal_age_group']) && !empty($animal_metas['animal_age_group'][0])) $args['animal_age_group'] = $animal_metas['animal_age_group'][0];
            if (isset($animal_metas['spayed_neutered']) && !empty($animal_metas['spayed_neutered'][0])) $args['spayed_neutered'] = $animal_metas['spayed_neutered'][0];
            if (isset($animal_metas['animal_weight']) && !empty($animal_metas['animal_weight'][0])) $args['animal_weight'] = $animal_metas['animal_weight'][0];
            if (isset($animal_metas['animal_main_breed']) && !empty($animal_metas['animal_main_breed'][0])) $args['animal_main_breed'] = $animal_metas['animal_main_breed'][0];
            if (isset($animal_metas['animal_breed_2']) && !empty($animal_metas['animal_breed_2'][0])) $args['animal_breed_2'] = $animal_metas['animal_breed_2'][0];
            if (isset($animal_metas['animal_main_color']) && !empty($animal_metas['animal_main_color'][0])) $args['animal_main_color'] = $animal_metas['animal_main_color'][0];
            if (isset($animal_metas['animal_color_2']) && !empty($animal_metas['animal_color_2'][0])) $args['animal_color_2'] = $animal_metas['animal_color_2'][0];
            if (isset($animal_metas['animal_adoption_status']) && !empty($animal_metas['animal_adoption_status'][0])) $args['animal_adoption_status'] = $animal_metas['animal_adoption_status'][0];
        }

        // MAP INFO
        $listings = groups_get_groupmeta(bp_get_current_group_id(), 'directorist_listings_ids', true);
        if ($listings && count($listings)) {
            $listing = $listings[0];
            $args['animal_address'] = get_post_meta($listing, '_address', true) ? get_post_meta($listing, '_address', true) : '';
            $args['cityLat'] = get_post_meta($listing, '_manual_lat', true) ? get_post_meta($listing, '_manual_lat', true) : '';
            $args['cityLng'] = get_post_meta($listing, '_manual_lng', true) ? get_post_meta($listing, '_manual_lng', true) : '';
        }

        return $args;
    }

    // ADD NEW ANIMAL PROCESS
    public function add_new_animal_process()
    {
        if (isset($_POST['animal_name']) && !empty($_POST['animal_name'])) {
            // Meta Input
            $meta_input = array();
            if (bp_get_current_group_id())  $meta_input['bb_group'] = bp_get_current_group_id();
            if (isset($_POST['animal_gender']) && !empty($_POST['animal_gender'])) $meta_input['animal_gender'] = trim($_POST['animal_gender']);
            if (isset($_POST['animal_type']) && !empty($_POST['animal_type'])) $meta_input['animal_type'] = trim($_POST['animal_type']);
            if (isset($_POST['animal_age_group']) && !empty($_POST['animal_age_group'])) $meta_input['animal_age_group'] = trim($_POST['animal_age_group']);
            if (isset($_POST['spayed_neutered']) && !empty($_POST['spayed_neutered'])) $meta_input['spayed_neutered'] = trim($_POST['spayed_neutered']);
            if (isset($_POST['animal_weight']) && !empty($_POST['animal_weight'])) $meta_input['animal_weight'] = trim($_POST['animal_weight']);
            if (isset($_POST['animal_main_breed']) && !empty($_POST['animal_main_breed'])) $meta_input['animal_main_breed'] = trim($_POST['animal_main_breed']);
            if (isset($_POST['animal_breed_2']) && !empty($_POST['animal_breed_2'])) $meta_input['animal_breed_2'] = trim($_POST['animal_breed_2']);
            if (isset($_POST['animal_main_color']) && !empty($_POST['animal_main_color'])) $meta_input['animal_main_color'] = trim($_POST['animal_main_color']);
            if (isset($_POST['animal_color_2']) && !empty($_POST['animal_color_2'])) $meta_input['animal_color_2'] = trim($_POST['animal_color_2']);
            if (isset($_POST['animal_adoption_status']) && !empty($_POST['animal_adoption_status'])) $meta_input['animal_adoption_status'] = trim($_POST['animal_adoption_status']);
            if (isset($_POST['animal_address']) && !empty($_POST['animal_address'])) $meta_input['animal_address'] = trim($_POST['animal_address']);
            if (isset($_POST['cityLat']) && !empty($_POST['cityLat'])) $meta_input['cityLat'] = trim($_POST['cityLat']);
            if (isset($_POST['cityLng']) && !empty($_POST['cityLng'])) $meta_input['cityLng'] = trim($_POST['cityLng']);
            // Meta Input

            $animal_args = array(
                "post_author"   =>  get_current_user_id(),
                "post_title"    =>  $_POST['animal_name'],
                "post_content"  =>  isset($_POST['animal_description']) && !empty($_POST['animal_description']) ? $_POST['animal_description'] : "",
                "post_status"   =>  "publish",
                "post_type"     =>  "animal",
                "meta_input"    =>  $meta_input
            );

            if (isset($_GET['action']) && $_GET['action'] == 'edit') {
                if (isset($_GET['animal']) && !empty($_GET['animal'])) {
                    $animal_args["ID"] = $_GET['animal'];
                }
            }

            $animal = wp_insert_post($animal_args);

            if ($animal) {
                if (!empty($_FILES)) {
                    foreach ($_FILES as $file) {
                        if (is_array($file)) {
                            $allowed = array('jpg', 'png', 'jpeg');
                            $filename = $file['name'];
                            $ext = pathinfo($filename, PATHINFO_EXTENSION);
                            if ($ext && !in_array($ext, $allowed)) {
                                echo "<P>Wrong file type!</p>";
                            } else {
                                $attachment_id = $this->mpp_upload_user_file($file);
                                if ($attachment_id) {
                                    set_post_thumbnail($animal, $attachment_id);
                                }
                            }
                        }
                    }
                }
                if (isset($_GET['action']) && $_GET['action'] == 'edit') {
                    echo "<P>Animal has been updated successfully!</p>";
                } else {
                    echo "<P>New Animal has been inserted successfully!</p>";
                }
            }
        }
    }

    // MPP UPLOAD USER FILE
    function mpp_upload_user_file($file = array())
    {
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $file_return = wp_handle_upload($file, array('test_form' => false));
        if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
            return false;
        } else {
            $filename = $file_return['file'];
            $attachment = array(
                'post_mime_type' => $file_return['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit',
                'guid' => $file_return['url']
            );
            $attachment_id = wp_insert_attachment($attachment, $file_return['url']);
            // Include Image, File, Media
            require_once ABSPATH . 'wp-admin' . '/includes/image.php';
            require_once ABSPATH . 'wp-admin' . '/includes/file.php';
            require_once ABSPATH . 'wp-admin' . '/includes/media.php';
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            if (0 < intval($attachment_id)) {
                return $attachment_id;
            }
        }
        return false;
    }

    // Adoption Pet List Shortcode
    public function adoption_pet_list()
    {
        $animals = new WP_Query(
            array(
                'post_type' => 'animal',
                'posts_per_page' => -1,
                'post_status'   => 'publish',
                'meta_key'      => 'bb_group',
                'meta_value'    => bp_get_current_group_id()
            )
        );
        $args = array('animals' => $animals);
        ob_start();
        get_template_part('template-parts/content', 'animals', $args);
        return ob_get_clean();
    }

    // Adoption Search Form
    public function adoption_search_form()
    {
        ob_start();
        get_template_part('template-parts/content', 'search_form');
        return ob_get_clean();
    }

    // Adomtion Search Results
    public function adoption_search_results()
    {
        $query_args = array(
            'post_type' => 'animal',
            'posts_per_page' => -1,
            'post_status'   => 'publish',
        );
        if (isset($_REQUEST['animal_name']) && !empty($_REQUEST['animal_name'])) $query_args['s'] = trim($_REQUEST['animal_name']);
        // Meta Query
        $meta_query = array();
        $metas = array(
            'animal_type',
            'animal_gender',
            'animal_age_group',
            'spayed_neutered',
            'animal_weight',
            'animal_main_breed',
            'animal_breed_2',
            'animal_main_color',
            'animal_adoption_status'
        );

        foreach ($metas as $meta_key) {
            if (isset($_REQUEST[$meta_key]) && !empty($_REQUEST[$meta_key])) {
                $meta_query[$meta_key] = array(
                    'key'   => $meta_key,
                    'value' => trim($_REQUEST[$meta_key])
                );
            }
        }

        if (
            (isset($_REQUEST['cityLat']) && !empty($_REQUEST['cityLat'])) &&
            (isset($_REQUEST['cityLng']) && !empty($_REQUEST['cityLat']))
        ) {
            $query_args['atbdp_geo_query'] = array(
                'lat_field' => 'cityLat',
                'lng_field' => 'cityLng',
                'latitude'  => sanitize_text_field($_REQUEST['cityLat']),
                'longitude' => sanitize_text_field($_REQUEST['cityLng']),
                'distance'  => '100',
                'units'     => 'miles'
            );
        }

        if (count($meta_query) > 0) {
            $meta_query['relation'] = 'AND';
            $query_args['meta_query'] = $meta_query;
        }
        // Meta Query

        $animals = new WP_Query($query_args);
        $args = array('animals' => $animals);
        ob_start();
        get_template_part('template-parts/content', 'animals', $args);
        return ob_get_clean();
    }

    // Animal Delete Ajax Call
    public function mpp_delete_animal()
    {
        $result = array('type' => false);
        $animal = isset($_REQUEST['animal']) && !empty($_REQUEST['animal']) ? $_REQUEST['animal'] : false;
        if ($animal) {
            $trash = wp_trash_post($animal);
            if ($trash) {
                $result['type'] = true;
            }
        }
        echo json_encode($result);
        die();
    }

    // Adoption CSV Import
    public function adoption_csv_import()
    {
        $this->process_adoption_csv_file();
        ob_start();
?>
        <form name="adoption_import" method="post" enctype='multipart/form-data'>
            <p><input type="file" name="csv_import" id="csv_import" accept=".csv"></p>
            <p><input type="submit" class="btn button" name="csv_submit"></p>
            <p style="font-size: 14px;line-height: 18px;">For your convenience we’ve provided a sample CSV that you can download as an example of the Information to provide for multiple pet adoption uploads.<br>
                Thank you for using MyPetsProfile™️</p>
            <p style="font-size: 16px;"><a style="padding: 20px 0;" href="<?php echo get_stylesheet_directory_uri() . '/assets/file/csv_import_adoption.csv'; ?>">Download Sample CSV</a></p>
        </form>
<?php
        return ob_get_clean();
    }

    // Process Adoption CSV File
    public function process_adoption_csv_file()
    {

        if (isset($_POST["csv_submit"])) {
            $animal_import = false;
            $extension = pathinfo($_FILES['csv_import']['name'], PATHINFO_EXTENSION);

            if (!empty($_FILES['csv_import']['name']) && $extension == 'csv') {

                //if there was an error uploading the file
                if ($_FILES["csv_import"]["error"] > 0) {
                    echo "Return Code: " . $_FILES["csv_import"]["error"] . "<br />";
                } else {
                    $csvFile = fopen($_FILES['csv_import']['tmp_name'], 'r');
                    $x = 0;
                    $label = array();
                    while (($data = fgetcsv($csvFile)) !== FALSE) {
                        if (!empty($data) && count($data) > 0) {
                            if ($x == 0) {
                                $label = $data;
                            } else {
                                $tax_data = array();
                                foreach ($data as $key => $value) {
                                    $tax_data[$label[$key]] = $value;
                                }
                                // Import Proccess
                                $animal = $this->insert_animal_from_csv_row($tax_data);
                                if ($animal) {
                                    $animal_import = true;
                                } else {
                                    $animal_import = false;
                                }
                            }
                        }
                        $x++;
                    }
                }
            } else {
                echo "No file selected <br />";
            }
            if ($animal_import) echo "<p>CSV inserted Successfully!</p>";
        }
    }

    // Insert Animal From CSV
    public function insert_animal_from_csv_row($data)
    {
        $meta_input = array();
        $image_url = '';

        // Handling Location
        if (bp_get_current_group_id()) {
            $listings = groups_get_groupmeta(bp_get_current_group_id(), 'directorist_listings_ids', true);
            if ($listings && count($listings)) {
                $listing = $listings[0];
                $meta_input['animal_address'] = get_post_meta($listing, '_address', true) ? get_post_meta($listing, '_address', true) : '';
                $meta_input['cityLat'] = get_post_meta($listing, '_manual_lat', true) ? get_post_meta($listing, '_manual_lat', true) : '';
                $meta_input['cityLng'] = get_post_meta($listing, '_manual_lng', true) ? get_post_meta($listing, '_manual_lng', true) : '';
            }
        }
        // Handling Location

        if (bp_get_current_group_id())  $meta_input['bb_group'] = bp_get_current_group_id();
        if (isset($data['animal_gender']) && !empty($data['animal_gender'])) $meta_input['animal_gender'] = trim($data['animal_gender']);
        if (isset($data['animal_type']) && !empty($data['animal_type'])) $meta_input['animal_type'] = trim($data['animal_type']);
        if (isset($data['animal_age_group']) && !empty($data['animal_age_group'])) $meta_input['animal_age_group'] = trim($data['animal_age_group']);
        if (isset($data['spayed_neutered']) && !empty($data['spayed_neutered'])) $meta_input['spayed_neutered'] = trim($data['spayed_neutered']);
        if (isset($data['animal_weight']) && !empty($data['animal_weight'])) $meta_input['animal_weight'] = trim($data['animal_weight']);
        if (isset($data['animal_main_breed']) && !empty($data['animal_main_breed'])) $meta_input['animal_main_breed'] = trim($data['animal_main_breed']);
        if (isset($data['animal_breed_2']) && !empty($data['animal_breed_2'])) $meta_input['animal_breed_2'] = trim($data['animal_breed_2']);
        if (isset($data['animal_main_color']) && !empty($data['animal_main_color'])) $meta_input['animal_main_color'] = trim($data['animal_main_color']);
        if (isset($data['animal_color_2']) && !empty($data['animal_color_2'])) $meta_input['animal_color_2'] = trim($data['animal_color_2']);
        if (isset($data['animal_adoption_status']) && !empty($data['animal_adoption_status'])) $meta_input['animal_adoption_status'] = trim($data['animal_adoption_status']);
        if (isset($data['animal_address']) && !empty($data['animal_address'])) $meta_input['animal_address'] = trim($data['animal_address']);
        if (isset($data['cityLat']) && !empty($data['cityLat'])) $meta_input['cityLat'] = trim($data['cityLat']);
        if (isset($data['cityLng']) && !empty($data['cityLng'])) $meta_input['cityLng'] = trim($data['cityLng']);
        if (isset($data['image_url']) && !empty($data['image_url'])) $image_url = trim($data['image_url']);
        // Meta Input

        $animal_args = array(
            "post_author"   =>  get_current_user_id(),
            "post_title"    =>  $data['animal_name'],
            "post_content"  =>  isset($data['animal_description']) && !empty($data['animal_description']) ? $data['animal_description'] : "",
            "post_status"   =>  "publish",
            "post_type"     =>  "animal",
            "meta_input"    =>  $meta_input
        );

        $animal = wp_insert_post($animal_args);
        if ($animal && !empty($image_url)) {
            $attachment_id = $this->mpp_insert_attachment_from_url($image_url, $animal);
            set_post_thumbnail($animal, $attachment_id);
        }
        return $animal;
    }

    // IMPORT IMAGE FROM URL
    public function mpp_insert_attachment_from_url($file_url)
    {
        if (!filter_var($file_url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $contents = $this->url_get_contents($file_url);

        if ($contents === false) {
            return false;
        }
        $upload = wp_upload_bits(basename($file_url), null, $contents);
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
        require_once ABSPATH . 'wp-admin' . '/includes/image.php';
        $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
        $id = wp_insert_attachment($attachment, $upload['file']);
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
        return $id;
    }

    // URL GET CONTENT
    public function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

new Pet_Adoption;
