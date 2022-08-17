<?php

/**
 * User Registration Email
 */

$listing_url = '';
$group_url = '';
$listing = isset($info['listing']) && !empty($info['listing']) ? $info['listing'] : 0;
if ($listing) {
    $listing_url = get_the_permalink($listing);
    //group url
    $group_id = get_post_meta($listing, '_bb_group_id', true);
    if ($group_id && !empty($group_id)) {
        $group = groups_get_group(array('group_id' => $group_id));
        $group_url = bp_get_group_permalink($group);
    }
}

?>

<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Welcome PooPrints Registered Community</title>
    <style>
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important;
            }

            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }

            table.body .content {
                padding: 0 !important;
            }

            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table.body .btn table {
                width: 100% !important;
            }

            table.body .btn a {
                width: 100% !important;
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }
    </style>
</head>

<body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">Welcome PooPrints Registered Community.</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" width="100%" bgcolor="#f6f6f6">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
            <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;" width="580" valign="top">
                <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border-radius: 3px; width: 100%;" width="100%">

                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;" valign="top">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                                    <tr>
                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Welcome PooPrints Registered Community,</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You’re receiving this email as a new activation notice for your community listing on the MyPetsProfile&trade; platform.</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Your MyPetsProfile&trade; (MPP) platform listing is now directly linked to your PooPrints Pet Registration process. You control all aspects of your community listing, and group on the MPP Platform, with the admin access provided.</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Now, residents with pets have their own Personal Pet Profile with your community as they register for PooPrints.</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Download the Apple or Google App: <a href="www.MyPetsProfile.com" target="_blank">www.MyPetsProfile.com</a> (login with your user ID and Password).</p>

                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Username: <?php echo $info['email']; ?></p>

                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Password: <?php echo $info['password']; ?></p>

                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; width: 100%;" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;" valign="top">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                                                <tbody>
                                                                    <tr style="margin-bottom: 10px; display:block">
                                                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border-radius: 5px; text-align: center; background-color: #3498db;" valign="top" align="center" bgcolor="#3498db"> <a href="<?php echo $listing_url; ?>" target="_blank" style="border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #3498db; border-color: #3498db; color: #ffffff;">Community Link</a> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border-radius: 5px; text-align: center; background-color: #3498db;" valign="top" align="center" bgcolor="#3498db"> <a href="<?php echo $group_url; ?>" target="_blank" style="border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #3498db; border-color: #3498db; color: #ffffff;">Group Link</a> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><b>Resident Benefits</b></p>

                                            <ul class="ul1">
                                                <li class="li1">Pet Profile (private or public)</li>
                                                <li class="li1">Pet Photos and Videos</li>
                                                <li class="li1">Health and medical records</li>
                                                <li class="li1">MyPetsAlert – Lost n found services</li>
                                                <li class="li1">PetChatR&trade; with other pet parents</li>
                                                <li class="li1">MyPetsPoints&trade; - collect and redeem</li>
                                                <li class="li1">Find local pet products and services
                                                    <ul class="ul1">
                                                        <li class="li1">12,000+ listings and growing</li>
                                                        <li class="li1">PetChatR&trade; with existing customers</li>
                                                    </ul>
                                                </li>
                                                <li class="li1">Find Pet-friendly places</li>
                                                <li class="li1">Meet and share with like-minded pet parents</li>
                                                <li class="li1">And so much more…</li>
                                            </ul>

                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><b>Property Management Benefits</b></p>

                                            <ul class="ul1">
                                                <li class="li1">Easy pet registration onboarding</li>
                                                <li class="li1">Direct link to PooPrints Registration<span class="Apple-converted-space"> </span></li>
                                                <li class="li1">Immediate access to all pet profiles</li>
                                                <li class="li1">Photos and unit location of each pet</li>
                                                <li class="li1">Pet emergency contact info</li>
                                                <li class="li1">And more…</li>
                                            </ul>

                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">We thank everyone for your patience as we developed the MyPetsProfile&trade; Web and App Platform, and hope you and your residents enjoy it as much as we have enjoyed developing it.</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Please feel free to email me or give me a call if you have any questions?</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px; margin-top: 15px;">
                                                <span style="display:block">Best Regards</span>
                                                <span style="display:block">Garry Bradamore</span>
                                                <span style="display:block">Hello@MyPetsProfile.com</span>
                                                <span style="display:block">Tel: 647-202-9877</sp>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- END MAIN CONTENT AREA -->
                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->

                    <!-- START FOOTER -->
                    <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                            <tr>
                                <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;" valign="top" align="center">
                                    <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">62 South Bayou Rd, Fenelon Falls, K0M1N0, Ontorio, Canada</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="content-block powered-by" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;" valign="top" align="center">
                                    Powered by <a href="http://mypetsprofile.com" style="color: #999999; font-size: 12px; text-align: center; text-decoration: none;">MyPetsProfile&trade;</a>.
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END FOOTER -->

                </div>
            </td>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
        </tr>
    </table>
</body>

</html>