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
            //if (isset($animal_metas['_thumbnail_id']) && !empty($animal_metas['_thumbnail_id'][0])) $args['thumbnail_id'] = $animal_metas['_thumbnail_id'][0];
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
            //require_once(ABSPATH . 'wp-admin/includes/image.php');
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
        if (isset($_GET['animal_name']) && !empty($_GET['animal_name'])) $query_args['s'] = trim($_GET['animal_name']);
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
            if (isset($_GET[$meta_key]) && !empty($_GET[$meta_key])) {
                $meta_query[$meta_key] = array(
                    'key'   => $meta_key,
                    'value' => trim($_GET[$meta_key])
                );
            }
        }

        if (
            (isset($_REQUEST['cityLat']) && !empty($_REQUEST['cityLat'])) &&
            (isset($_REQUEST['cityLng']) && !empty($_REQUEST['cityLat']))
        ) {
            $query_args['atbdp_geo_query'] = array(
                'lat_field' => 'manual_lat',
                'lng_field' => 'manual_lng',
                'latitude'  => sanitize_text_field($_REQUEST['cityLat']),
                'longitude' => sanitize_text_field($_REQUEST['cityLng']),
                'distance'  => '5',
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
}

new Pet_Adoption;
