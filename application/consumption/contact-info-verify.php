<?php
/**
 * changePassUser
 * @package default
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */
//Including files
require_once("../includes/classes/Configuration.inc.php");
require_once("../includes/classes/db.php");

$token = $_GET['t'];
$email = base64_decode(substr_replace(substr_replace($token, '', 0, 5), '', -3));
mysql_query("UPDATE email_verification SET is_verified=1  WHERE email_address = '$email'");
?>
<!DOCTYPE html>
<html>
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .bgimg {
            background-image: url('http://lmis.gov.pk/assets/img/bg/2.jpg');
            opacity: 10%;
            height: 100%;
            height: 100%;
            background-position: center;
            background-size: cover;
            position: relative;
            color: white;
            font-family: "Courier New", Courier, monospace;
            font-size: 25px;
        }

        .topleft {
            position: absolute;
            top: 0;
            left: 16px;
        }

        .bottomleft {
            position: absolute;
            bottom: 0;
            left: 16px;
        }

        .middle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        hr {
            margin: auto;
            width: 40%;
        }
    </style>
    <body>

        <div class="bgimg">
            <div class="topleft">
                <p>Logistics Management information System</p>
            </div>
            <div class="middle">
                <h1>Your email has been successfully verified</h1>
                <hr>
                <p>Thank you for verifying your email. We will use this email address to send all LMIS notification. Click on the button bellow to continue.</p>
                <p><a href="http://lmis.gov.pk"><img src="http://lmis.gov.pk/assets/img/landing-images/limsMain_logo.gif" style="border: double whitesmoke 5px"/></a></p>
            </div>
        </div>

    </body>
</html>