/* This is your custom Javascript */
jQuery(document).ready(function ($) {
  if ($("body").find(".become-an-affiliate").length > 0) {
    $(".sms-instruction-msg").hide();
  }
  // Set floating menu to SELF
  $(".floating-menu-link a").attr("target", "_self");

  // CHECK RECAPTCHA
  $(document).on("click", "#signup_submit", function (e) {
    if (!$(this).hasClass("notprevent")) {
      e.preventDefault();
      if ($("#g-recaptcha-response").val() != "")
        $(this).addClass("notprevent").trigger("click");
    }
  });

  //Animal
  $(document).on("click", ".animal-holder", function () {
    var metas = $(this).data("metas");
    var title = $(this).data("title");
    var image = $(this).data("img");
    var admin = $(this).data("admin");
    var groupUrl = $(this).data("group_url");
    var animal = $(this).data("animal");

    var animal_description = '<div class="animal-description">';

    // Image
    if (image != "") {
      animal_description =
        '<div class="animal-modal-image"><img src="' + image + '"/></div>';
    }

    // GENDER
    if (metas.animal_gender != undefined && metas.animal_gender[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Male or Female</div>
        <div class="animal-info-value">` +
        metas.animal_gender[0] +
        `</div></div>`;
    }

    // ANIMAL TYPE
    if (metas.animal_type != undefined && metas.animal_type[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Type</div>
        <div class="animal-info-value">` +
        metas.animal_type[0] +
        `</div></div>`;
    }

    // AGE GROUP
    if (
      metas.animal_age_group != undefined &&
      metas.animal_age_group[0] != ""
    ) {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Age Group</div>
        <div class="animal-info-value">` +
        metas.animal_age_group[0] +
        `</div></div>`;
    }

    // WEIGHT
    if (metas.animal_weight != undefined && metas.animal_weight[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Weight (Lbs)</div>
        <div class="animal-info-value">` +
        metas.animal_weight[0] +
        `</div></div>`;
    }

    // spayed_neutered
    if (metas.spayed_neutered != undefined && metas.spayed_neutered[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Spayed Neutered</div>
        <div class="animal-info-value">` +
        metas.spayed_neutered[0] +
        `</div></div>`;
    }

    // MAIN BREED
    if (
      metas.animal_main_breed != undefined &&
      metas.animal_main_breed[0] != ""
    ) {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Main Breed</div>
        <div class="animal-info-value">` +
        metas.animal_main_breed[0] +
        `</div></div>`;
    }

    // BREED 2
    if (metas.animal_breed_2 != undefined && metas.animal_breed_2[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Breed 2</div>
        <div class="animal-info-value">` +
        metas.animal_breed_2[0] +
        `</div></div>`;
    }

    // MAIN COLOR
    if (
      metas.animal_main_color != undefined &&
      metas.animal_main_color[0] != ""
    ) {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Main Color</div>
        <div class="animal-info-value">` +
        metas.animal_main_color[0] +
        `</div></div>`;
    }

    // COLOR 2
    if (metas.animal_color_2 != undefined && metas.animal_color_2[0] != "") {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Color 2</div>
        <div class="animal-info-value">` +
        metas.animal_color_2[0] +
        `</div></div>`;
    }

    // ADOPTION STATUS
    if (
      metas.animal_adoption_status != undefined &&
      metas.animal_adoption_status[0] != ""
    ) {
      animal_description +=
        `<div class="animal-info-modal">
        <div class="animal-info-label">Adopted</div>
        <div class="animal-info-value">` +
        metas.animal_adoption_status[0] +
        `</div></div>`;
    }

    animal_description +=
      `<a href="` + groupUrl + `" class="button">Contact</a> `;

    if (admin == "admin") {
      animal_description +=
        `<a href="#" class="button delete_animal" data-animal="` +
        animal +
        `">Delete</a>
      <a href="` +
        groupUrl +
        `/pet-adoption/add-new-animal/?action=edit&animal=` +
        animal +
        `" class="button">Edit</a>`;
    }

    animal_description += "</div>";
    Swal.fire({
      title: title,
      html: animal_description,
      showConfirmButton: false,
      showCloseButton: true,
    });
  });

  // Delete Animal
  $(document).on("click", ".delete_animal", function (e) {
    e.preventDefault();
    var $animal = $(this).data("animal");
    Swal.fire({
      title: "Do you want to delete this animal?",
      showCancelButton: true,
      confirmButtonText: "Delete",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        // AJAX CALL
        $.ajax({
          type: "post",
          dataType: "json",
          url: bbpCommonJsData.ajax_url,
          data: { action: "mpp_delete_animal", animal: $animal },
          success: function (response) {
            if (response.type == true) {
              Swal.fire("Deleted!", "", "success");
              $(".animal_holder_" + $animal).hide();
            }
          },
        });
        // AJAX CALL
      }
    });
  });

  // GOOGLE ADDRESS AUTOCOMPLETE
  function google_ac_initialize() {
    var input = document.getElementById("searchAnimalMap");
    var mpp_ac = new google.maps.places.Autocomplete(input, {
      types: ["geocode"],
    });
    mpp_ac.addListener("place_changed", function () {
      var place = mpp_ac.getPlace();
      //console.log("okay");
      //console.log(place);
      //document.getElementById('city2').value = place.name;
      document.getElementById("cityLat").value = place.geometry.location.lat();
      document.getElementById("cityLng").value = place.geometry.location.lng();
    });
  }
  google_ac_initialize();
  // GOOGLE ADDRESS AUTOCOMPLETE

  // MyPetsAlert

  $("#mpp_pets_alert").on("submit", function (e) {
    e.preventDefault();

    console.log("clicked");

    var message = $("#mpp_alert_message").val();

    if (message != "") {
      $("#mpp_loading").show();
      $("#mpp_warning").text("");
    } else {
      $("#mpp_warning").text("Please Enter Message.");
      return;
    }

    $("#mpp_pets_alert_submitted").prop("disabled", true);

    var nonce = $("#mpp_alert_nonce").val();

    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: { action: "send_mpp_pets_alert", message: message, nonce: nonce },
      success: function (response) {
        if (response.success == true) {
          Swal.fire("MPP Pet Alert Sent!", "", "success");
        }
      },
      complete: function () {
        $("#mpp_loading").hide();
        $("#mpp_pets_alert_submitted").prop("disabled", false);
      },
    });
    // AJAX CALL
  });

  // MyPetsAlert

  // Referral Messenger

  $("#apply_as_referral").on("click", function (e) {
    e.preventDefault();
    var group = $(this).data("group");
    var user = $(this).data("user");
    Swal.fire({
      title: "Do you want to join the referral program?",
      showCancelButton: true,
      confirmButtonText: "Join",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        // AJAX CALL
        $.ajax({
          type: "post",
          dataType: "json",
          url: mppChild.ajaxurl,
          data: { action: "mpp_apply_referral", group: group, user: user },
          success: function (response) {
            console.log(response);
            if (response.result == true) {
              Swal.fire("Joined!", "", "success");
            } else {
              Swal.fire("Sorry, could not join!", "", "error");
            }
          },
        });
        // AJAX CALL
      }
    });
  });

  // Referral Messenger

  // Referral Approval
  $(".mpp-referral-approval").on("click", function (e) {
    e.preventDefault();
    var row = $(this).parents("tr");
    var group = $(this).data("group");
    var user = $(this).data("user");
    var type = $(this).data("type");

    if (type == "accept") {
      // APROVED
      Swal.fire({
        title: "Do you want to acccept this member?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          // AJAX CALL
          $.ajax({
            type: "post",
            dataType: "json",
            url: mppChild.ajaxurl,
            data: { action: "mpp_accept_referral", group: group, user: user },
            success: function (response) {
              console.log(response);
              if (response.result == true) {
                Swal.fire("Confirmed!", "", "success");
                row.hide();
              } else {
                Swal.fire("Sorry, could not confirm!", "", "error");
              }
            },
          });
          // AJAX CALL
        }
      });
    } else {
      // REJECTED
      Swal.fire({
        title: "Do you want to reject the member?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          // AJAX CALL
          $.ajax({
            type: "post",
            dataType: "json",
            url: mppChild.ajaxurl,
            data: { action: "mpp_reject_referral", group: group, user: user },
            success: function (response) {
              console.log(response);
              if (response.result == true) {
                Swal.fire("Confirmed!", "", "success");
                row.hide();
              } else {
                Swal.fire("Sorry, could not confirm!", "", "error");
              }
            },
          });
          // AJAX CALL
        }
      });
    }
  });

  // Referral Approval

  // MPP MESSENGER FORM SUBMIT
  $("#mpp_messenger_form").submit(function (e) {
    e.preventDefault();

    if ($("#messenger_message").val() == "") return;

    var message = $("#messenger_message").val();
    var info = $("#msg_info").val();

    $("#messenger_message").val("");

    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: { action: "mpp_insert_message_row", message: message, info: info },
      success: function (response) {
        if (response.result == true) {
          $("#messenger_message").val("");
          $("section.discussion").append(
            "<div class='bubble sender'>" + message + "</div>"
          );
        } else {
          $("#messenger_warning").text("Cannot sent!");
          console.log("cannot sent");
        }
      },
      error: function (e, error) {
        console.log(e);
        console.log(error);
      },
    });
    // AJAX CALL
  });

  // LOAD CHAT IN EVERY 30 SEC
  if ($("body").hasClass("single-at_biz_dir")) {
    setInterval(function () {
      if ($(".messenger-container").hasClass("active")) {
        var $this = $(".messenger-container");
        var info = $this.find("#msg_info").val();
        console.log(info);

        // AJAX CALL
        $.ajax({
          type: "post",
          dataType: "json",
          url: mppChild.ajaxurl,
          data: {
            action: "mpp_get_unread_message",
            info: info,
          },
          success: function (response) {
            console.log(response);
            if (response.result == true) {
              $("#messenger_message").val("");
              $("section.discussion").append(
                "<div class='bubble recipient'>" +
                  response.messages[0].message +
                  "</div>"
              );
            } else {
              $("#messenger_warning").text("Cannot sent!");
              console.log("cannot sent");
            }
          },
          error: function (e, error) {
            console.log(e);
            console.log(error);
          },
        });
        // AJAX CALL
      }
    }, 10000);
  }
  // LOAD CHAT IN EVERY 30 SEC

  // MPP START CHATTING
  $(".mpp-start-chatting").on("click", function () {
    var $sender = $(this).data("sender");
    var recipient = $(this).data("recipient");
    var group = $(this).data("group");
    if ($sender != "") {
      var info = {
        sender: $sender,
        recipient: recipient,
        group: group,
      };
      console.log(info);
      $("#msg_info").val(JSON.stringify(info));
      $(".messenger-container .discussion").html("");
      $(".messenger-container").show();

      // AJAX CALL
      $.ajax({
        type: "post",
        dataType: "json",
        url: mppChild.ajaxurl,
        data: {
          action: "mpp_retrive_messages",
          info: info,
        },
        success: function (response) {
          console.log(response);
          if (response.result == true) {
            $(".messenger-container .discussion").html(
              mpp_prepare_messages(response.messages, $sender)
            );
            $(".messenger-container").addClass("active");
          } else {
            $("#messenger_warning").text("Cannot sent!");
            console.log("cannot sent");
          }
        },
        error: function (e, error) {
          console.log(e);
          console.log(error);
        },
      });
      // AJAX CALL
    }
  });

  // Prepare Messages
  function mpp_prepare_messages(messages = [], current_user = 0) {
    var html = "";
    var prev_sender = 0;
    var next_sender = 0;
    if (messages.length > 0) {
      $.each(messages, function (key, message) {
        prev_sender = key > 0 ? messages[key - 1].sender_id : 0;
        next_sender =
          key < messages.length - 1 ? messages[key + 1].sender_id : 0;

        var owner = message.sender_id == current_user ? "sender" : "recipient";
        message_position = "";
        if (prev_sender !== message.sender_id) message_position = "first";
        if (prev_sender == message.sender_id) message_position = "middle";
        if (next_sender !== message.sender_id) message_position = "last";
        if (
          next_sender !== message.sender_id &&
          prev_sender !== message.sender_id
        )
          message_position = "single";
        html +=
          '<div class="bubble ' +
          message_position +
          " " +
          owner +
          '">' +
          message.message +
          "</div>";
      });
    }
    return html;
  }
  // Prepare Messages
});
