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
});
