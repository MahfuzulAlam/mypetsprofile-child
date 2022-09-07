<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Directorist_Shortcodes
{

    public function __construct()
    {
        // EXTEND THE SEARCH DIRECTORY SHORTCODE
        add_shortcode('mpp-search-directory', array($this, 'mpp_search_directory'));
    }

    /**
     * EXTEND THE SEARCH DIRECTORY SHORTCODE
     */
    public function mpp_search_directory($atts = [])
    {
        $default_directory_type = isset($_REQUEST['default_directory_type']) && !empty($_REQUEST['default_directory_type']) ? $_REQUEST['default_directory_type'] : '';
        if (!empty($default_directory_type)) $atts['default_directory_type'] = $default_directory_type;

        $html = '';

        if ($atts && count($atts) > 0) {
            foreach ($atts as $key => $value) {
                $html .= sprintf(' %s="%s"', $key, esc_html($value));
            }
        }

        $html = sprintf('[%s%s]', 'directorist_search_listing', $html);
        ob_start();
        echo do_shortcode($html);
        return ob_get_clean();
    }
}

new MPP_Directorist_Shortcodes;
