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
    var $class_name = Math.random() * 11;

    $("#messenger_message").val("");
    $("section.discussion").append(
      "<div class='bubble sender " + $class_name + "'>" + message + "</div>"
    );

    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: { action: "mpp_insert_message_row", message: message, info: info },
      success: function (response) {
        if (response.result == true) {
          console.log("success");
          // SCROLL TO BOTTOM
          if (!$("body").hasClass("single-at_biz_dir")) {
            $(".chat-history").animate(
              {
                scrollTop: $(".chat-history").get(0).scrollHeight,
              },
              1500
            );
          }
          // SCROLL TO BOTTOM
        } else {
          $("#messenger_warning").text("Cannot sent!");
          console.log("cannot sent");
          $class_name.hide();
        }
      },
      error: function (e, error) {
        console.log(e);
        console.log(error);
        $class_name.hide();
      },
    });
    // AJAX CALL
  });

  // LOAD CHAT IN EVERY 30 SEC
  if (
    $("body").hasClass("single-at_biz_dir") ||
    $("body").hasClass("page-id-797") ||
    $("body").hasClass("page-id-21348")
  ) {
    // REMOVE ACTIVE
    if ($("#mpp_chat_module").hasClass("owner-module"))
      $(".messenger-container").removeClass("active");

    // SCROLL TO BOTTOM
    if (!$("body").hasClass("single-at_biz_dir")) {
      $(".chat-history").animate(
        {
          scrollTop: $(".chat-history").get(0).scrollHeight,
        },
        1500
      );
    }
    // SCROLL TO BOTTOM

    // INTERVAL VARIABLES
    var intervalTime = parseInt(mppChild.chatInterval); // 10 sec
    var intervalCounter = 0;
    var intervalEnd = 1000 * 60 * 30; // 30 min
    var intervalMid = 1000 * 60 * 10; // 10 min
    var midIntervalTime = 1000 * 60; // 1 min

    var intervalMidStatic = intervalMid;

    // SET INTERVAL
    interval = setInterval(intervalFunction, intervalTime);

    function intervalFunction() {
      if ($(".messenger-container").hasClass("active")) {
        var $this = $(".messenger-container");

        var info = $("#msg_info").val();
        console.log(info);

        // COUNTER
        intervalCounter = intervalCounter + intervalTime;
        console.log(intervalCounter);

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
              console.log(intervalCounter);
              console.log(intervalMid);
              $("#messenger_message").val("");
              $.each(response.messages, function (key, message) {
                $("section.discussion").append(
                  "<div class='bubble recipient'>" + message.message + "</div>"
                );
              });
              // RESET INTERVAL
              if (intervalCounter > intervalMidStatic) {
                clearInterval(interval);
                intervalTime = parseInt(mppChild.chatInterval);
                interval = setInterval(intervalFunction, intervalTime);
                intervalMid = intervalMidStatic;
              }
              intervalCounter = 0;
              // RESET INTERVAL
            } else {
              $("#messenger_warning").text("Cannot sent!");
              console.log("cannot sent");
              // CLEAR INTERVAL
              if (intervalCounter > intervalEnd) {
                clearInterval(interval);
              } else if (intervalCounter > intervalMid) {
                clearInterval(interval);
                intervalTime = midIntervalTime;
                interval = setInterval(intervalFunction, intervalTime);
                intervalMid = intervalEnd + 1;
              }
              // CLEAR INTERVAL
            }
          },
          error: function (e, error) {
            console.log(e);
            console.log(error);
            // CLEAR INTERVAL
            if (intervalCounter > intervalEnd) {
              clearInterval(interval);
            } else if (intervalCounter > intervalMid) {
              clearInterval(interval);
              intervalTime = midIntervalTime;
              interval = setInterval(intervalFunction, intervalTime);
              intervalMid = intervalEnd + 1;
            }
            // CLEAR INTERVAL
          },
        });
        // AJAX CALL
      }
    }
  }
  // LOAD CHAT IN EVERY 30 SEC

  // MPP START CHATTING
  $(".mpp-start-chatting").on("click", function () {
    var tr = $(this).parents("tr");
    var $sender = $(this).data("sender");
    var recipient = $(this).data("recipient");
    var listing = $(this).data("listing");
    var username = tr.find("td.username").text();
    if ($sender != "") {
      var info = {
        sender: $sender,
        recipient: recipient,
        listing: listing,
      };
      //console.log('retrive start');
      $("#msg_info").val(JSON.stringify(info));
      $(".messenger-container .discussion").html("");
      $(".messenger-container").show();
      $(".messenger-header h4").text("Chat with " + username);
      //console.log(info);

      // Loading Show
      $("#loading_messages").show();

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
          //console.log(response);
          if (response.result == true) {
            $(".messenger-container .discussion").html(
              mpp_prepare_messages(
                response.messages,
                $sender,
                response.profile_images
              )
            );
            $(".messenger-container").addClass("active");

            // SCROLL TO BOTTOM
            $(".messenger-body").animate(
              {
                scrollTop: $(".messenger-body").get(0).scrollHeight,
              },
              1500
            );
          } else {
            $("#messenger_warning").text("Cannot retrive!");
            console.log("cannot retrive");
          }
        },
        error: function (e, error) {
          console.log(e);
          console.log(error);
        },
        complete: function () {
          // Loading Hide
          $("#loading_messages").hide();
        },
      });
      // AJAX CALL
    }
  });

  // MPP START CHATTING ADMIN
  $(".people-block").on("click", function () {
    // CHECK ACTIVE
    if ($(this).hasClass("active")) {
      if ($(window).width() < 768) {
        $("#plist").css({ left: "-400px" });
        $("#plist").removeClass("plist-showed").addClass("plist-hidden");
        return;
      }
    }

    // CHANGE STATUS
    $(".people-block").removeClass("active");
    $(this).addClass("active");
    // CHANGE STATUS

    var $this = $(this);

    var blockInfo = $this.attr("data-info");
    blockInfo = JSON.parse(blockInfo);

    var info = {
      sender: blockInfo.sender,
      recipient: blockInfo.recipient,
      listing: blockInfo.listing,
    };

    console.log(info);

    if (info != "") {
      $("#msg_info").val(JSON.stringify(info));
      $(".messenger-container .discussion").html("");
      $(".messenger-container").show();
      $(".chat .chat-header img").attr("src", blockInfo.avatar);
      $(".chat .chat-header .chat-about h6").text(blockInfo.name);
      $(".chat .chat-header .chat-about small").text(
        $this.find("div.status").text()
      );
      $(".messenger-container").removeClass("active");

      // Loading Show
      $("#loading_messages").show();

      // Toggle
      if ($(window).width() < 768) {
        $("#plist").css({ left: "-400px" });
        $("#plist").removeClass("plist-showed").addClass("plist-hidden");
      }

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
          //console.log(response);
          if (response.result == true) {
            $(".messenger-container .discussion").html(
              mpp_prepare_messages(
                response.messages,
                blockInfo.sender,
                response.profile_images
              )
            );

            // SCROLL TO BOTTOM
            $(".chat-history").animate(
              {
                scrollTop: $(".chat-history").get(0).scrollHeight,
              },
              1500
            );

            if (!$("#mpp_chat_module").hasClass("owner-module"))
              $(".messenger-container").addClass("active");
          } else {
            $("#messenger_warning").text("Cannot retrive!");
            console.log("cannot retrive");
          }
        },
        error: function (e, error) {
          console.log(e);
          console.log(error);
        },
        complete: function () {
          // Loading Hide
          $("#loading_messages").hide();
          if (!$("#mpp_chat_module").hasClass("owner-module"))
            $(".messenger-container").addClass("active");
        },
      });
      // AJAX CALL
    }
  });

  // Prepare Messages
  function mpp_prepare_messages(
    messages = [],
    current_user = 0,
    profile_images = []
  ) {
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

        var profile_image = profile_images[owner];
        if (message_position == "first" || message_position == "single") {
          html +=
            '<img src="' +
            profile_image +
            '" class="bubble-image ' +
            owner +
            '"/>';
        }

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

  $(".spokespersons-property-name, #mpp_housing").select2({
    ajax: {
      url: mppChild.ajaxurl,
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          action: "mpp_property_search",
          q: params.term,
        };
      },
      processResults: function (data) {
        var options = [];
        if (data) {
          // data is the array of arrays, and each of them contains ID and the Label of the option
          $.each(data, function (index, text) {
            // do not forget that "index" is just auto incremented value
            options.push({ id: text[0], text: text[1] });
          });
        }
        return {
          results: options,
        };
      },
      cache: true,
    },
    placeholder: "Search for a property",
    minimumInputLength: 3,
    // templateResult: formatRepo,
    // templateSelection: formatRepoSelection
  });

  function formatRepo(repo) {
    if (repo.loading) {
      return repo.text;
    }

    var $container = $(
      "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__avatar'><img src='" +
        repo.owner.avatar_url +
        "' /></div>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'></div>" +
        "<div class='select2-result-repository__description'></div>" +
        "<div class='select2-result-repository__statistics'>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
        "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> </div>" +
        "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> </div>" +
        "</div>" +
        "</div>" +
        "</div>"
    );

    $container.find(".select2-result-repository__title").text(repo.full_name);
    $container
      .find(".select2-result-repository__description")
      .text(repo.description);
    $container
      .find(".select2-result-repository__forks")
      .append(repo.forks_count + " Forks");
    $container
      .find(".select2-result-repository__stargazers")
      .append(repo.stargazers_count + " Stars");
    $container
      .find(".select2-result-repository__watchers")
      .append(repo.watchers_count + " Watchers");

    return $container;
  }

  function formatRepoSelection(repo) {
    return repo.full_name || repo.text;
  }

  // ACCEPT/REJECT SPEAKER - LISTING
  // Referral Approval
  $(".mpp-referral-approval-listing").on("click", function (e) {
    e.preventDefault();
    var row = $(this).parents("tr");
    var listing = $(this).data("listing");
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
            data: {
              action: "mpp_accept_referral_listing",
              listing: listing,
              user: user,
            },
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
            data: {
              action: "mpp_reject_referral_listing",
              listing: listing,
              user: user,
            },
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

  // Referral Remove
  $(".mpp-referral-remove").on("click", function (e) {
    e.preventDefault();
    var row = $(this).parents("tr");
    var listing = $(this).data("listing");
    var user = $(this).data("user");

    // REMOVE
    Swal.fire({
      title: "Do you want to remove this member?",
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
          data: {
            action: "mpp_remove_referral_listing",
            listing: listing,
            user: user,
          },
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
  });

  // MPP REFERRAL CHANGE STATUS
  $(".mpp-referral-status").on("click", function (e) {
    e.preventDefault();
    var $this = $(this);
    var listing = $(this).data("listing");
    var user = $(this).data("user");
    var type = $(this).attr("data-type");

    var wrapper = $this.parents(".spokesperson-wrapper");
    var $status = wrapper.find(".spokesperson-status");

    if (type == "activate") {
      // APROVED
      Swal.fire({
        title: "Do you want to activate the Spokesperson?",
        showCancelButton: true,
        confirmButtonText: "Activate",
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          // AJAX CALL
          $.ajax({
            type: "post",
            dataType: "json",
            url: mppChild.ajaxurl,
            data: {
              action: "mpp_referral_listing_change_status",
              listing: listing,
              user: user,
              status: "active",
            },
            success: function (response) {
              console.log(response);
              if (response.result == true) {
                Swal.fire("Confirmed!", "", "success");
                $status.text("Active");
                $this.text("Deactivate");
                $this.attr("data-type", "deactivate");
              } else {
                Swal.fire("Sorry, could not change!", "", "error");
              }
            },
          });
          // AJAX CALL
        }
      });
    } else {
      // REJECTED
      Swal.fire({
        title: "Do you want to deactivate this Spokesperson?",
        showCancelButton: true,
        confirmButtonText: "Deactivate",
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          // AJAX CALL
          $.ajax({
            type: "post",
            dataType: "json",
            url: mppChild.ajaxurl,
            data: {
              action: "mpp_referral_listing_change_status",
              listing: listing,
              user: user,
              status: "inactive",
            },
            success: function (response) {
              console.log(response);
              if (response.result == true) {
                Swal.fire("Confirmed!", "", "success");
                $status.text("Inactive");
                $this.text("Activate");
                $this.attr("data-type", "activate");
              } else {
                Swal.fire("Sorry, could not change!", "", "error");
              }
            },
          });
          // AJAX CALL
        }
      });
    }
  });

  // PROPERTY - REFERRAL PROGRAM CHANGE
  $(document).on("change", "select.spokesperson_ref_system", function () {
    console.log($(this).val());
    var status = $(this).val();
    var listing = $(this).attr("data-listing");
    console.log(listing);
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "mpp_spokesperson_ref_system",
        listing: listing,
        status: status,
      },
      success: function (response) {
        console.log(response);
        if (response.result == true) {
          //Swal.fire("Updated!", "", "success");
          console.log("Updated!");
        } else {
          //Swal.fire("Sorry, could not update!", "", "error");
          console.log("Sorry, could not update!");
        }
      },
    });
    // AJAX CALL
  });

  // PROPERTY - REFERRAL REGISTRATION STATUS
  $(document).on("change", "select.spokesperson_reg_status", function () {
    console.log($(this).val());
    var status = $(this).val();
    var listing = $(this).attr("data-listing");
    console.log(listing);
    // AJAX CALL
    $.ajax({
      type: "post",
      dataType: "json",
      url: mppChild.ajaxurl,
      data: {
        action: "mpp_spokesperson_reg_status",
        listing: listing,
        status: status,
      },
      success: function (response) {
        console.log(response);
        if (response.result == true) {
          //Swal.fire("Updated!", "", "success");
          console.log("Updated!");
        } else {
          //Swal.fire("Sorry, could not update!", "", "error");
          console.log("Sorry, could not update!");
        }
      },
    });
    // AJAX CALL
  });

  // TOGGLE
  // $("#show-hide-people").toggle(
  //   function(){$("#plist").css({"left": 0});},
  //   function(){$("#plist").css({"color": "-400px"});
  // });

  $("#show-hide-people").on("click", function (e) {
    e.preventDefault();
    if ($("#plist").hasClass("plist-hidden")) {
      $("#plist").css({ left: 0 });
      $("#plist").removeClass("plist-hidden").addClass("plist-showed");
      return;
    }
    if ($("#plist").hasClass("plist-showed")) {
      $("#plist").css({ left: "-400px" });
      $("#plist").removeClass("plist-showed").addClass("plist-hidden");
      return;
    }
  });

  // INPUT MASK
  $("#phone").inputmask({ mask: "(999)-999-9999" });

  // CHECKBOX VCANCY
  $("input[name='vacancy[]").on("change", function () {
    var vacancy_list = [];
    if ($(this).val() == "none") {
      if ($(this).is(":checked")) {
        // DESELECT ALL
        $("input[name='vacancy[]']").each(function () {
          if ($(this).val() != "none") {
            $(this).prop("checked", false);
            var parent_checkbox = $(this).parents(".directorist-checkbox");
            parent_checkbox.hide();
            parent_checkbox.find(".vacancy-number-wrapper").hide();
          }
        });
      } else {
        // SHOW ALL
        $("input[name='vacancy[]']").each(function () {
          $(this).parents(".directorist-checkbox").show();
        });
      }
    } else {
      var parent = $(this).parents(".directorist-checkbox");
      if ($(this).is(":checked")) {
        parent.find(".vacancy-number-wrapper").show();
      } else {
        parent.find(".vacancy-number-wrapper").hide();
      }
    }

    var vacancy = {};
    $("input[name='vacancy[]']:checked").each(function () {
      var parent = $(this).parents(".directorist-checkbox");
      var option_name = $(this).val();
      var option_number = parent.find('input[type="number"]').val();
      if (option_number > 0) vacancy[option_name] = parseInt(option_number);
      if (option_name == "none") vacancy[option_name] = 0;
    });
    if ($.isEmptyObject(vacancy)) {
      $('input[name="mpp-vacancy"]').val("");
    } else {
      $('input[name="mpp-vacancy"]').val(JSON.stringify(vacancy));
    }
    console.log(vacancy);
  });

  $(".vacancy-input-wrapper input[type='number']").on("change", function () {
    var vacancy = {};
    $("input[name='vacancy[]']:checked").each(function () {
      var parent = $(this).parents(".directorist-checkbox");
      var option_name = $(this).val();
      var option_number = parent.find('input[type="number"]').val();
      if (option_number > 0) vacancy[option_name] = parseInt(option_number);
      if (option_name == "none") vacancy[option_name] = 0;
    });
    if ($.isEmptyObject(vacancy)) {
      $('input[name="mpp-vacancy"]').val("");
    } else {
      $('input[name="mpp-vacancy"]').val(JSON.stringify(vacancy));
    }

    console.log(vacancy);
  });

  // CHECKBOX VACANCY

  // COPY LISTING LINK
  $(".copy_listing_link").on("click", function (e) {
    e.preventDefault();
    var link = $(this).attr("data-listing");
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();
    $(".copy_listing_link_msg").text("Link Copied!");
  });

  // SELECT 2 - pooprint_select_listing
  $("#pooprint_select_listing").select2();

  // SET POOPRINT FORM LINK ON CHANGE
  // $("#pooprint_select_listing").on('change', function(e){
  //   e.preventDefault();

  // });

  // POOPRINTS LISTING SELECTION BUTTONS
  $(".pooprint_select_listing_button").on("click", function (e) {
    e.preventDefault();

    var $form_holder = $(this).parents(".mpp-custom-registration-form-holder");
    var type = $form_holder.find(".selection_type").val();

    var pooprint_select_listing = $form_holder.find(".pooprint_select_listing");
    var pooprint_select_listing_msg = $form_holder.find(
      ".pooprint_select_listing_msg"
    );

    if (type == "pooprints") {
      var listing = $form_holder.find(".pooprint_select_listing").val();
      var link = $form_holder.find(".pooprint_page_link").val();

      var formLink = pooprint_select_listing
        .find(":selected")
        .attr("data-pooprint-link");
      if (listing != "" && listing != 0) {
        pooprint_select_listing_msg.text("Loading Registration Page ...");
        if (mppChild.isLogin) {
          if (formLink != "") window.location.href = formLink;
        } else {
          window.location.href = link + "?listing=" + listing;
        }
      } else {
        pooprint_select_listing_msg.text("Please select a listing first");
      }
    } else if (type == "pet-profile-community") {
      var formUrl = $form_holder.find(".selection_type_link").val();
      var listing = $form_holder.find(".pooprint_select_listing").val();

      var access_type = pooprint_select_listing
        .find(":selected")
        .attr("data-access-type");

      if (formUrl != "" && listing != 0) {
        pooprint_select_listing_msg.text("Loading Registration Page ...");
        if (access_type == "free") {
          window.location.href = formUrl + "?listing=" + listing;
        } else {
          var mpp_product_url = $form_holder.find(".mpp_product_url").val();
          if (mpp_product_url && mpp_product_url != "")
            window.location.href = mpp_product_url + "?mpp_building=" + listing;
        }
      } else {
        pooprint_select_listing_msg.text("Please select a listing first");
      }
    }
  });

  // POOPRINTS DNA PROPERTY BUTTONS
  $("#pooprint_dna_property_button").on("click", function (e) {
    e.preventDefault();
    var formLink = $("#pooprint_select_listing")
      .find(":selected")
      .attr("data-pooprint-link");
    if (mppChild.isLogin) {
      $("#pooprint_select_listing_msg").text(
        "Loading PooPrint Registration Page"
      );
      if (formLink != "") window.location.href = formLink;
    } else {
      $("#pooprint_select_listing_msg").text("Sorry, you are not loggedin.");
    }
  });

  // END SCRIPT
});
