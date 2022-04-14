/* This is your custom Javascript */
jQuery(document).ready(function ($) {
  if ($("body").find(".become-an-affiliate").length > 0) {
    $(".sms-instruction-msg").hide();
  }
  // Set floating menu to SELF
  $(".floating-menu-link a").attr("target", "_self");
});
