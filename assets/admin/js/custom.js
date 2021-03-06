/* This is your custom Javascript */
jQuery(document).ready(function ($) {
    function atbdp_render_media_uploader_for_app(page) {
        var file_frame;
        var image_data;
        var json; // If an instance of file_frame already exists, then we can open it rather than creating a new instance

        if (undefined !== file_frame) {
            file_frame.open();
            return;
        } // Here, use the wp.media library to define the settings of the media uploader


        file_frame = wp.media.frames.file_frame = wp.media({
            frame: 'post',
            state: 'insert',
            multiple: false
        }); // Setup an event handler for what to do when an image has been selected

        file_frame.on('insert', function () {
            // Read the JSON data returned from the media uploader
            json = file_frame.state().get('selection').first().toJSON(); // First, make sure that we have the URL of an image to display

            if ($.trim(json.url.length) < 0) {
                return;
            } // After that, set the properties of the image and display it


            if (page == 'listings') {
                var html = "".concat('<tr class="atbdp-image-row">' + '<td class="atbdp-handle"><span class="dashicons dashicons-screenoptions"></span></td>' + '<td class="atbdp-image">' + '<img src="').concat(json.url, "\" />") + "<input type=\"hidden\" name=\"images[]\" value=\"".concat(json.id, "\" />") + "</td>" + "<td>".concat(json.url, "<br />") + "<a href=\"post.php?post=".concat(json.id, "&action=edit\" target=\"_blank\">").concat(atbdp.edit, "</a> | ") + "<a href=\"javascript:;\" class=\"atbdp-delete-image\" data-attachment_id=\"".concat(json.id, "\">").concat(atbdp.delete_permanently, "</a>") + "</td>" + "</tr>";
                $('#atbdp-images').append(html);
            } else {
                $('#atbdp-categories-app-image-id').val(json.id);
                $('#atbdp-categories-app-image-wrapper').html("<img src=\"".concat(json.url, "\" /><a href=\"\" class=\"remove_cat_img\"><span class=\"fa fa-times\" title=\"Remove it\"></span></a>"));
            }
        }); // Now display the actual file_frame

        file_frame.open();
    } // Display the media uploader when "Upload Image" button clicked in the custom taxonomy "atbdp_categories"


    $('#atbdp-categories-upload-app-image').on('click', function (e) {
        e.preventDefault();
        atbdp_render_media_uploader_for_app('categories');
    });

    $(document).on('click', '.remove_cat_app_img', function (e) {
        e.preventDefault();
        $(this).hide();
        $(this).prev('img').remove();
        $('#atbdp-categories-app-image-id').attr('value', '');
    });
});