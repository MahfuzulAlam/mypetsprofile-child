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
        // REGISTRATION FORM
        add_shortcode('pooprints-registration-form', array($this, 'pooprint_registration_form'));
    }

    /**
     * POOPRINTS REGISTRATION FROM
     */
    public function pooprint_registration_form()
    {
        ob_start();
        $listing_id = isset($_REQUEST['listing']) && !empty($_REQUEST['listing']) ? $_REQUEST['listing'] : 0;
        if (!$listing_id) {
            echo '<p>Sorry! This page is not available right now.</p>';
            return ob_get_clean();
        }
        // Create Account
        $user = $this->create_account();
        if ($user) $this->redirect_to_pooprint_link();

        $args = [];
        get_template_part('template-parts/content', 'pooprint_registration_form', $args);
        return ob_get_clean();
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
        $mpp_building = isset($_POST['listing']) && !empty($_POST['listing']) ? $_POST['listing'] : 0;

        if (!empty($email) && !empty($password)) {

            $user_name = $this->mpp_generate_username($first_name, $last_name, $email);
            $user_id = wp_create_user($user_name, $password, $email);

            if ($user_id) {
                // Add FirstName, LastName, Phone
                if (!empty($firstname)) update_user_meta($user_id, 'first_name', $firstname);
                if (!empty($lastname)) update_user_meta($user_id, 'last_name', $lastname);
                if (!empty($mpp_building)) update_user_meta($user_id, 'mpp_building', $mpp_building);

                $this->update_user_role($user_id);

                return $user_id;
            }
        }

        return false;
    }

    /**
     * UPDATE USER ROLE
     */
    public function update_user_role($user_id = 0)
    {
        if (!$user_id) return;
        // update member type
        if (function_exists('bp_set_member_type')) bp_set_member_type($user_id, 'pet-owner');
        // update user role
        if ($this->user_role_exists('petowner')) {
            $user = new \WP_User($user_id);
            $user->set_role('petowner');
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
?>
            <p>Redirecting to the PooPrints Registration Page ... </p>
            <script type="text/javascript">
                window.location.href = "<?php echo $redirect_url; ?>";
            </script>
<?php
        }
    }
}

new MPP_Registration;
