<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */


class Event_Calendar_Listing_Creation
{
    public function __construct()
    {
        if (!$this->plugin_exists()) return;

        // Create an Event on create a Event listing
        add_filter('atbdp_listing_form_submission_info', array($this, 'atbdp_listing_form_submission_info'));
    }

    // Create an Event on create a Event listing
    public function atbdp_listing_form_submission_info($data)
    {
        $dir_type = get_post_meta($data['id'], '_directory_type', true);
        $get_event_dir_type = get_term_by('slug', 'mpp-event', ATBDP_DIRECTORY_TYPE);

        if ($get_event_dir_type && $dir_type == $get_event_dir_type->term_id) {

            $listing = get_post($data['id']);

            $related_event = get_post_meta($data['id'], '_event_calendar', true);
            $post_title = $listing->post_title;
            $post_content = $listing->post_content;
            $post_status = $listing->post_status;
            $post_author = $listing->post_author;

            $excerpt = get_post_meta($data['id'], '_excerpt', true);
            $price = get_post_meta($data['id'], '_price', true);

            // Dates
            $start_date = get_post_meta($data['id'], '_event_start_date', true);
            $start_time = get_post_meta($data['id'], '_event_start_time', true);
            $end_date = get_post_meta($data['id'], '_event_end_date', true);
            $end_time = get_post_meta($data['id'], '_event_end_time', true);
            $all_day_event = get_post_meta($data['id'], '_all_day_event', true);

            // Contact
            $website = get_post_meta($data['id'], '_website', true);

            // Images
            $image = get_post_meta($data['id'], '_listing_prv_img', true);

            // Taxonomies
            $location = get_the_terms($data['id'], ATBDP_LOCATION) ? get_the_terms($data['id'], ATBDP_LOCATION)[0]->name : '';
            $category = get_the_terms($data['id'], ATBDP_CATEGORY) ? get_the_terms($data['id'], ATBDP_CATEGORY)[0]->name : '';

            // Tags
            $listing_tags = get_the_terms($data['id'], ATBDP_TAGS);
            $tag_names = [];
            if ($listing_tags && count($listing_tags) > 0) {
                foreach ($listing_tags as $tag) {
                    $tag_names[] = $tag->name;
                }
            }

            // Venue
            $v_title = get_post_meta($data['id'], '_venue_title', true);
            $address = get_post_meta($data['id'], '_address', true);
            $phone = get_post_meta($data['id'], '_phone', true);
            $zip = get_post_meta($data['id'], '_zip', true);
            $city = get_post_meta($data['id'], '_venue_city', true);
            $country = get_post_meta($data['id'], '_venue_country', true);
            $province = get_post_meta($data['id'], '_venue_province', true);

            if ($related_event) {
                $venue = get_post_meta($related_event, '_EventVenueID', true);
            } else {
                $venue = tribe_create_venue(array(
                    'Venue' => $v_title,
                    'Country' => $country,
                    'Address' => $address,
                    'City' => $city,
                    'State' => $location,
                    'Province' => $province,
                    'Zip' => $zip,
                    'Phone' => $phone,
                ));
            }

            // Organizer
            $org_name = get_post_meta($data['id'], '_organizer', true);
            $org_email = get_post_meta($data['id'], '_email', true);
            $org_website = get_post_meta($data['id'], '_organizer_website', true);
            $org_phone = get_post_meta($data['id'], '_phone2', true);

            if ($related_event) {
                $organizer = get_post_meta($related_event, '_EventOrganizerID', true);
            } else {
                $organizer = tribe_create_venue(array(
                    'Organizer' => $org_name,
                    'Email' => $org_email,
                    'Website' => $org_website,
                    'Phone' => $org_phone,
                ));
            }

            $args = array(
                'post_author' => $post_author,
                'post_title' => $post_title,
                'post_content' => $post_content,
                'post_excerpt' => $excerpt,
                'post_status' => $post_status,
                'tags_input' => $tag_names,
                'EventAllDay' => $all_day_event,
                'EventShowMapLink' => true,
                'EventHideFromUpcoming' => false,
                'EventShowMap' => true,
                'EventCost' => $price,
                'EventURL' => $website,
                'FeaturedImage' => $image,
            );

            if ($related_event) $args['ID'] = $related_event;

            $event_id = tribe_create_event($args);

            if ($event_id) {
                update_post_meta($event_id, '_EventCurrencySymbol', 'CAD');
                update_post_meta($event_id, '_EventCurrencyCode', "CAD");
                update_post_meta($event_id, '_EventCurrencyPosition', "prefix");
                update_post_meta($event_id, '_EventVenueID', $venue);
                update_post_meta($event_id, '_EventOrganizerID', $organizer);

                // Category Update
                $this->manage_category($category, $event_id);

                // DateTime
                if ($start_date && $start_time) update_post_meta($event_id, '_EventStartDate', $start_date . ' ' . $start_time . ':00');
                if ($end_date && $end_time) update_post_meta($event_id, '_EventEndDate', $end_date . ' ' . $end_time . ':00');

                // Update Listing
                update_post_meta($data['id'], '_event_calendar', $event_id);
            }
        }
        return $data;
    }

    // Manage Event Category
    public function manage_category($category, $event_id)
    {
        if (!empty($category)) {
            $event_category = get_term_by('name', $category, 'tribe_events_cat');
            if ($event_category) {
                wp_set_post_terms($event_id, array($event_category->term_id), 'tribe_events_cat', false);
            } else {
                $new_category = wp_insert_term($category, 'tribe_events_cat');
                if (!is_wp_error($new_category)) {
                    wp_set_post_terms($event_id, array($new_category['term_id']), 'tribe_events_cat', false);
                }
            }
        }
    }

    // Is Plugin Active
    public function plugin_exists()
    {
        return is_plugin_active('the-events-calendar/the-events-calendar.php') ? true : false;
    }
}

new Event_Calendar_Listing_Creation;
