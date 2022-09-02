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
      frame: "post",
      state: "insert",
      multiple: false,
    }); // Setup an event handler for what to do when an image has been selected

    file_frame.on("insert", function () {
      // Read the JSON data returned from the media uploader
      json = file_frame.state().get("selection").first().toJSON(); // First, make sure that we have the URL of an image to display

      if ($.trim(json.url.length) < 0) {
        return;
      } // After that, set the properties of the image and display it

      if (page == "listings") {
        var html =
          ""
            .concat(
              '<tr class="atbdp-image-row">' +
                '<td class="atbdp-handle"><span class="dashicons dashicons-screenoptions"></span></td>' +
                '<td class="atbdp-image">' +
                '<img src="'
            )
            .concat(json.url, '" />') +
          '<input type="hidden" name="images[]" value="'.concat(
            json.id,
            '" />'
          ) +
          "</td>" +
          "<td>".concat(json.url, "<br />") +
          '<a href="post.php?post='
            .concat(json.id, '&action=edit" target="_blank">')
            .concat(atbdp.edit, "</a> | ") +
          '<a href="javascript:;" class="atbdp-delete-image" data-attachment_id="'
            .concat(json.id, '">')
            .concat(atbdp.delete_permanently, "</a>") +
          "</td>" +
          "</tr>";
        $("#atbdp-images").append(html);
      } else {
        $("#atbdp-categories-app-image-id").val(json.id);
        $("#atbdp-categories-app-image-wrapper").html(
          '<img src="'.concat(
            json.url,
            '" /><a href="" class="remove_cat_img"><span class="fa fa-times" title="Remove it"></span></a>'
          )
        );
      }
    }); // Now display the actual file_frame

    file_frame.open();
  } // Display the media uploader when "Upload Image" button clicked in the custom taxonomy "atbdp_categories"

  $("#atbdp-categories-upload-app-image").on("click", function (e) {
    e.preventDefault();
    atbdp_render_media_uploader_for_app("categories");
  });

  $(document).on("click", ".remove_cat_app_img", function (e) {
    e.preventDefault();
    $(this).hide();
    $(this).prev("img").remove();
    $("#atbdp-categories-app-image-id").attr("value", "");
  });

  /**
   * RentSync Admin Options
   */

  $("#import_all_properties").on("click", function (e) {
    e.preventDefault();
    var count = rentsync_count_properties();

    //Progressbar
    /*
    var progressbar = $("#rentsync_progressbar");
    var progressbarWidth = 0;
    var count = 2;
    */
    // ADD PROPERTIES

    if (count > 0) {
      //var progressbarCluster = 100 / count;
      //count = 142;
      //var clusters = Math.ceil(count / 10);
      updateRentsyncStatus(
        "<span>Importing Data From Rentsync API</span> ....<br><span>This will take a while. Please do not interrupt.</span>"
      );
      var i = 0;
      var range = 10;
      var limit = count;

      var tid = setInterval(rentSyncAddProperty, 10000);

      function rentSyncAddProperty() {
        console.log(i);
        rentsync_add_property(i, limit, range);
        i = i + range;
        if (i >= limit) abortTimer();
      }

      function abortTimer() {
        // to be called when you want to stop the timer
        clearInterval(tid);
      }
    }
  });

  function updateRentsyncStatus(html) {
    //console.log(html);
    $("#rentsync_api_progress").append("<p>" + html + "</p>");
  }

  /**
   * RentSync - Download the api data
   */
  $("#download_all_properties").on("click", function (e) {
    e.preventDefault();
    console.log("working");
    updateRentsyncStatus("Download Started!");
    rentsync_download_properties();
  });
  /**
   * RentSync - Count Total Properties
   */
  function rentsync_count_properties() {
    var $count = 0;
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "rentsync_count_properties",
      },
      success: function (response) {
        console.log(response);
        if (response.status) {
          $count = response.count;
        } else {
          $count = 0;
        }
      },
      error: function () {
        $count = 0;
      },
      async: false,
    });
    // AJAX CALL
    return $count;
  }

  /**
   * RentSync - Add a Property
   */
  function rentsync_add_property(property_key = "", limit = 1, range = 1) {
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "rentsync_import_all_properties",
        property_key: property_key,
        range: range,
        limit: limit,
      },
      success: function (response) {
        console.log(response);
        if (response.result == true) {
          updateRentsyncStatus(
            "Imported ( from " + response.start + " to " + response.end + " )"
          );
          if (response.end >= limit) {
            updateRentsyncStatus(
              '<span class="success">DATA IMPORT COMPLETE</span>'
            );
          }
        } else {
          updateRentsyncStatus("Sorry, cannot import " + property_key);
        }
      },
    });
    // AJAX CALL
  }

  /**
   * RentSync - Download Properties
   */
  function rentsync_download_properties() {
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "rentsync_download_properties",
      },
      success: function (response) {
        if (response.result == true) {
          updateRentsyncStatus("Downloaded!");
          updateRentsyncStatus(
            "Total Properties Downloaded - " + response.count
          );
        } else {
          updateRentsyncStatus("Sorry, Cannot Downloaded!!");
        }
      },
    });
    // AJAX CALL
  }

  /**
   * Rentsync - Cleanup Properties
   */
  $("#cleanup_properties").on("click", function (e) {
    e.preventDefault();
    rentsync_cleanup_properties();
  });

  /**
   * Function - CLeanup Properties
   */
  function rentsync_cleanup_properties() {
    updateRentsyncStatus("Cleanup Started!");
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "rentsync_cleanup_properties",
      },
      success: function (response) {
        if (response.result == true) {
          console.log(response.props);
          updateRentsyncStatus("Cleanup Completed!");
        } else {
          updateRentsyncStatus("Sorry, Cannot Cleanup!!");
        }
      },
    });
    // AJAX CALL
  }
});
