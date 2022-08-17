<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class Directorist_Import
{
    public function __construct()
    {
        // AFTER IMPORT A LISTING HOOK
        add_action('directorist_listing_imported', array($this, 'mpp_directorist_listing_imported'), 10, 2);
        // AFTER IMPORT A LISTING HOOK
        add_action('directorist_listing_imported', array($this, 'mpp_directorist_listing_imported_send_email'), 30, 2);
    }

    /**
     * AFTER IMPORT A LISTING HOOK
     * Update Author
     * Update Listing Type
     */
    public function mpp_directorist_listing_imported($post_id, $post)
    {
        // update pricing plan
        if (isset($post['fm_plans']) && !empty($post['fm_plans'])) $this->update_pricing_plan($post_id, $post['fm_plans']);

        // update author
        if (isset($post['email']) && !empty($post['email'])) $this->update_author($post_id, $post);

        // update directory type
        if (isset($post['directory_type']) && !empty($post['directory_type'])) $this->update_directory_type($post_id, $post['directory_type']);
    }

    /**
     * UPDATE TERM
     */
    public function update_directory_type($post_id = 0, $directory_type = 0)
    {
        if (!$post_id || !$directory_type) return;
        $directory_type = intval($directory_type);
        $exists = term_exists($directory_type, ATBDP_DIRECTORY_TYPE);
        if ($exists) {
            wp_set_post_terms($post_id, array($directory_type), ATBDP_DIRECTORY_TYPE);
            update_post_meta($post_id, '_directory_type', $directory_type);
        }
    }

    /**
     * UPDATE PRICING PLAN
     */
    public function update_pricing_plan($post_id = 0, $pricing_plan = 0)
    {
        if (!$post_id || !$pricing_plan) return;
        update_post_meta($post_id, '_fm_plans', $pricing_plan);
    }

    /**
     * UPDATE AUTHOR
     */
    public function update_author($post_id = 0, $post = [])
    {
        if (!$post_id) return;
        $author = $this->create_author($post_id, $post);
        if ($author) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_author' => $author
            ));
        }
    }

    /**
     * CREATE AUTHOR
     */
    public function create_author($post_id = 0, $post = [])
    {
        $email = isset($post['email']) && !empty($post['email']) ? $post['email'] : '';
        if ($email != '') {
            $author_exists = get_user_by('email', $email);
            if ($author_exists) {
                update_post_meta($post_id, '_registration_detail', '');
                update_post_meta($post_id, '_new_registration', 0);
                return $author_exists->ID;
            } else {
                // CREATE A USER SEND A EMAIL
                $firstname = trim(isset($post['aai-name']) ? $post['aai-name'] : "");
                $lastname = trim(isset($post['aai-last-name']) ? $post['aai-last-name'] : "");
                $phone = trim(isset($post['phone']) ? $post['phone'] : "");
                $user_id = $this->mpp_add_new_user($email, $firstname, $lastname, $phone, $post_id);
                if ($user_id) return $user_id;
            }
        }
        return false;
    }

    /**
     * ADD NEW USER
     */
    public function mpp_add_new_user($email = '', $firstname = '', $lastname = '', $phone = '', $listing = 0)
    {
        $user_name = $this->mpp_generate_username($firstname, $lastname, $email);
        if (!empty($user_name)) {
            $random_password = wp_generate_password(12, false);
            $user_id = wp_create_user($user_name, $random_password, $email);
            if ($user_id) {
                // Add FirstName, LastName, Phone
                if (!empty($firstname)) update_user_meta($user_id, 'first_name', $firstname);
                if (!empty($lastname)) update_user_meta($user_id, 'last_name', $lastname);
                if (!empty($phone)) update_user_meta($user_id, 'billing_phone', $phone);
                // SEND EMAIL
                $email_info = array(
                    'email' => $email,
                    'password' => $random_password,
                    'firstname' => $firstname,
                    'listing' => $listing
                );
                update_post_meta($listing, '_registration_detail', $email_info);
                update_post_meta($listing, '_new_registration', 1);

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
        if (function_exists('bp_set_member_type')) bp_set_member_type($user_id, 'pet-friendly-biz-member');
        // update user role
        if ($this->user_role_exists('pet_friendly_biz')) {
            $user = new \WP_User($user_id);
            $user->set_role('pet_friendly_biz');
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
     * SEND REGISTRATION EMAIL
     */
    public function mpp_directorist_listing_imported_send_email($post_id, $post)
    {
        if (!$post_id) return;
        $new = get_post_meta($post_id, '_new_registration', true);
        if (!$new) return;
        $info = get_post_meta($post_id, '_registration_detail', true);
        if (!$info || empty($info)) return;
        // ...
        $email = isset($info['email']) & !empty($info['email']) ? $info['email'] : '';
        $title = 'Welcome PooPrints Registered Community';
        ob_start();
        require(get_stylesheet_directory() . '/includes/directorist/templates/email/user-registration.php');
        $content = ob_get_clean();
        // ...
        if (!empty($email)) wp_mail($email, $title, $content, "Content-Type: text/html; charset=UTF-8");
    }

    /**
     * MPP SLUGIFY TEXT
     */
    public function mpp_slugify_text($text, string $divider = '-')
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
}

new Directorist_Import;
