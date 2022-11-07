<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Registration
{

    public function __construct()
    {
        // LISTING SELECTION FORM
        add_shortcode('pooprints-listing-selection-form', array($this, 'pooprints_listing_selection_form'));

        // REGISTRATION FORM
        add_shortcode('pooprints-registration-form', array($this, 'pooprint_registration_form'));

        // LISTING DNA PROPERTY SELECTION FORM
        add_shortcode('pooprints-dna-property-selection-form', array($this, 'pooprints_dna_property_selection_form'));

        // DIRECTORIST - SET COOKIE
        add_action('init', array($this, 'set_listing_as_cookie'));

        // CREATE USER
        add_action('template_redirect', array($this, 'create_user_process'));
    }

    /**
     * LISTING SELECTION FORM
     */
    public function pooprints_listing_selection_form($atts = [])
    {
        ob_start();
        get_template_part('template-parts/content', 'pooprint_listing_selection_form', $atts);
        return ob_get_clean();
    }

    /**
     * LISTING SELECTION FORM
     * pooprints_dna_property_selection_form
     */
    public function pooprints_dna_property_selection_form()
    {
        ob_start();
        $args = [];
        get_template_part('template-parts/content', 'pooprints_dna_property_selection_form', $args);
        return ob_get_clean();
    }

    /**
     * POOPRINTS REGISTRATION FROM
     */
    public function pooprint_registration_form($atts = [])
    {
        ob_start();
        $listing_id = isset($_REQUEST['listing']) && !empty($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;
        $form_type = isset($atts['form_type']) && !empty($atts['form_type']) ? $atts['form_type'] : 'pooprints';

        if ((!$listing_id && $form_type != 'apartment-registration') || is_user_logged_in()) {
            echo '<p>Sorry! This page is not available right now.</p>';
            return ob_get_clean();
        }
        // Create Account
        //$user = $this->create_account();

        // Redirection
        // if ($user) {
        //     if ($form_type == 'pooprints') {
        //         $this->redirect_to_pooprint_link();
        //     } else if ($form_type == 'pet-profile-registration') {
        //         $this->redirect_to_pet_profile_link();
        //     } else if ($form_type == 'apartment-registration') {
        //         wp_set_current_user($user);
        //         wp_set_auth_cookie($user);
        //     }
        //     return ob_get_clean();
        // }

        if ($form_type == 'pooprints') {
            get_template_part('template-parts/content', 'pooprint_registration_form', $atts);
        } else if ($form_type == 'apartment-registration') {
            get_template_part('template-parts/content', 'apartment_registration_form', $atts);
        } else if ($form_type == 'pet-profile-registration') {
            get_template_part('template-parts/content', 'petprofile_registration_form', $atts);
        }

        return ob_get_clean();
    }

    /**
     * CRETAE USER PROCESS
     */

    public function create_user_process()
    {
        if (
            is_page('apartment-registration') ||
            is_page('pet-profile-registration-form') ||
            is_page('pooprints-registration')
        ) {
            $form_type = isset($_POST['form_type']) && !empty($_POST['form_type']) ? $_POST['form_type'] : '';
            // Create Account
            $user = $this->create_account();

            // Redirection
            if ($user) {
                if ($form_type == 'pooprints') {
                    $this->redirect_to_pooprint_link();
                } else if ($form_type == 'pet-profile-registration') {
                    $this->redirect_to_pet_profile_link();
                } else if ($form_type == 'apartment') {
                    wp_set_current_user($user);
                    wp_set_auth_cookie($user);
                    $this->redirect_to_the_apartment_form();
                }
                return ob_get_clean();
            }
        }
    }


    /**
     * CREATE ACCOUNT
     */
    public function create_account()
    {
        $email = isset($_POST['signup_email']) && !empty($_POST['signup_email']) ? $_POST['signup_email'] : '';
        $password = isset($_POST['signup_password']) && !empty($_POST['signup_password']) ? $_POST['signup_password'] : '';
        $first_name = isset($_POST['first_name']) && !empty($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) && !empty($_POST['last_name']) ? $_POST['last_name'] : '';
        $pet_name = isset($_POST['pet_name']) && !empty($_POST['pet_name']) ? $_POST['pet_name'] : '';
        $mpp_building = isset($_POST['listing']) && !empty($_POST['listing']) ? $_POST['listing'] : 0;
        $form_type = isset($_POST['form_type']) && !empty($_POST['form_type']) ? $_POST['form_type'] : '';

        if (!empty($email) && !empty($password)) {

            $user_name = $this->mpp_generate_username($first_name, $last_name, $email);
            $user_id = wp_create_user($user_name, $password, $email);

            if ($user_id) {
                // Add FirstName, LastName, Phone
                if (!empty($firstname)) update_user_meta($user_id, 'first_name', $firstname);
                if (!empty($lastname)) update_user_meta($user_id, 'last_name', $lastname);
                if (!empty($mpp_building)) update_user_meta($user_id, 'mpp_building', $mpp_building);

                if (!empty($pet_name)) {
                    xprofile_set_field_data(100, $user_id, $pet_name);
                }

                if ($form_type == 'apartment') {
                    $this->update_user_role($user_id, 'pet_friendly_biz', 'pet-friendly-biz-member');
                } else {
                    $this->update_user_role($user_id, 'petowner', 'pet-owner');
                }

                return $user_id;
            }
        }

        return false;
    }

    /**
     * UPDATE USER ROLE
     */
    public function update_user_role($user_id = 0, $user_role = 'petowner', $member_type = 'pet-owner')
    {
        if (!$user_id) return;
        // update member type
        if (function_exists('bp_set_member_type')) bp_set_member_type($user_id, $member_type);
        // update user role
        if ($this->user_role_exists($user_role)) {
            $user = new \WP_User($user_id);
            $user->set_role($user_role);
        }
    }

    /**
     * USER ROLE EXISTS
     */
    public function user_role_exists($role)
    {
        if (!empty($role)) {
            return $GLOBALS['wp_roles']->is_role($role);
        }
        return false;
    }

    /**
     * MPP GENERATE USERNAME
     */
    public function mpp_generate_username($firstname = '', $lastname = '', $email = '')
    {
        $fullname = !empty($lastname) ? trim($firstname . '_' . $lastname) : trim($firstname);
        $username = !empty($fullname) ? $this->mpp_slugify_text($fullname, '_') : '';
        if (!$username || empty($username)) {
            $username = implode('@', explode('@', $email, -1));
        }
        $x = 1;
        $y = 3;
        while ($x <= $y) {
            if (username_exists($username)) {
                $username = $username . $x;
            } else {
                return $username;
            }
            $x++;
            $y++;
        }
    }

    /**
     * MPP SLUGIFY TEXT
     */
    public function mpp_slugify_text($text, $divider = '-')
    {
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

        if (empty($text)) {
            return false;
        }

        return $text;
    }

    /**
     * REDIRECT TO THE POOPRINT PAGE
     */
    public function redirect_to_pooprint_link()
    {
        $redirect_url = '';
        $mpp_building = isset($_POST['listing']) && !empty($_POST['listing']) ? $_POST['listing'] : 0;
        if ($mpp_building) {
            $redirect_url = get_post_meta($mpp_building, '_pooprints_link', true);
        }
        if (!empty($redirect_url)) {
            wp_redirect($redirect_url);
            exit();
?>
            <p>Redirecting to the PooPrints Registration Page ... </p>
            <script type="text/javascript">
                //window.location.href = "<?php echo $redirect_url; ?>";
            </script>
        <?php
        }
    }


    /**
     * REDIRECT TO THE PETPROFILE
     */
    public function redirect_to_pet_profile_link()
    {
        wp_redirect(home_url() . '/members/me/');
        exit();
        ?>
        <script type="text/javascript">
            //window.location.href = "<?php echo home_url(); ?>/members/me/";
        </script>
<?php
    }

    //https://mypetsprofile.com/add-listing/?directory_type=1414&plan=30047
    /**
     * REDIRECT TO THE APARMENT FORM
     */
    public function redirect_to_the_apartment_form()
    {
        wp_redirect(home_url() . '/add-listing/?directory_type=1414&plan=30047');
        exit();
    }

    /**
     * SET LISTING AS COOKIE
     */
    public function set_listing_as_cookie()
    {
        if (!is_admin()) {
            $mpp_building = isset($_REQUEST['mpp_building']) && !empty($_REQUEST['mpp_building']) ? $_REQUEST['mpp_building'] : '';
            if (!empty($mpp_building)) {
                // set a cookie for 1 year
                setcookie('mpp_building', $mpp_building, time() + 90000, '/');
            }
        }
    }
}

new MPP_Registration;
