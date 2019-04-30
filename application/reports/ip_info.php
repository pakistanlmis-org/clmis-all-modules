<?php

/**
 * ip_info
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//get ip
$ip = $_GET['ip'];
//unserialize
$ipInfo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
//latitude
$latitude = $ipInfo['geoplugin_latitude'];
//longitude
$longitude = $ipInfo['geoplugin_longitude'];
//city
$city = $ipInfo['geoplugin_city'];
//country
$country = $ipInfo['geoplugin_countryName'];
$location[] = $city;
$location[] = $country;
$locationInfo = implode(', ', array_filter($location));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP Information</title>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
<script src="../../plmis_js/locationpicker.jquery.js"></script>
<style>
body{
	margin:10px !important;font-family:Arial,Helvetica,sans-serif; font-size:12px;
	background: url("../../plmis_img/main_background_bg.jpg") repeat scroll 0 0 rgba(0, 0, 0, 0);
}

</style>
</head>

<body>
    <?php
	if ( !empty($latitude) && !empty($longitude) && !empty($city) )
	{
		$zoom = 16;
	}
	else
	{
		$zoom = 9;
	}
	?>
    <div style="width:100%; margin: 0 auto;">
        <div style="text-align:center;">
            <h3>Location Information</h3>
            <b>IP:</b> <?php echo $ip;?><br />
            <b>Location:</b> <?php echo $locationInfo;?></b>
        </div>
        <div id="somecomponent" style="width: 500px; height: 400px; border:2px solid #D3D3D3; margin:auto; margin-top:10px;"></div>
        <script>
            $('#somecomponent').locationpicker({
                location: {latitude: <?php echo $latitude;?>, longitude: <?php echo $longitude;?>},
                radius: 50,			
                locationName: "<?php echo $locationInfo;?>",
                zoom: <?php echo $zoom;?>,
            });
        </script>
        <div style="padding-top:10px; font-size:10px; text-align:center;">Disclaimer : Locations are mapped using a third party service any may not be accurate.</div>
    </div>
</body>
</html>