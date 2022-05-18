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
    console.log(metas);
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
        <div class="animal-info-label">Weight</div>
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

    animal_description += "</div>";
    Swal.fire({
      title: title,
      html: animal_description,
      showConfirmButton: false,
    });
  });
});
