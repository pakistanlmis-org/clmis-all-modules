<?php

/**
 * completeness_of_reporting
 * @package maps
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");

startHtml($system_title." - Completeness Of Reporting");?>
<style>
.input_select {
	border: #D1D1D1 1px solid;
	color: #474747;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	height: 24px;
	max-width: 150px;
}
.input_button {
	border: #D1D1D1 1px solid;
	background-color: #006700;
	color: #FFFFFF;
	height: 25px;
	font-family: Arial, Helvetica, sans-serif;
	vertical-align: bottom;
	width: 60px;/*	font-size:11px;*/
}
</style>
<link href="<?php echo PUBLIC_URL;?>css/map.css" rel="stylesheet" />
<script src="<?php echo PUBLIC_URL;?>js/maps/html2canvas.js"></script>
<script src="<?php echo PUBLIC_URL;?>js/maps/download.js"></script>
<script src="<?php echo PUBLIC_URL;?>js/maps/symbology.js"></script>
<script src="<?php echo PUBLIC_URL;?>js/maps/completeness_of_reporting.js"></script>
<script src="<?php echo PUBLIC_URL;?>js/maps/refineLegend.js"></script>

<body text="#000000" bgColor="#FFFFFF" style="margin-left:0px;margin-top:0px;margin-right:0px;margin-bottom:0px; overflow:visible;">
<?php include PUBLIC_PATH."html/top.php";?>
<?php include PUBLIC_PATH."html/top_im.php";?>
<div class="body_sec">
    <div class="wrraper" style="height:auto; padding-left:5px">
        <div class="content">
            <?php include(APP_PATH."includes/maps/compeltenessForm.php"); ?>
            <table width="100%">
                <tr style="background:url(<?php echo PUBLIC_URL;?>plmis_img/grn-top-bg.jpg); background-repeat:repeat-x;">
                    <td style="width:30%" align="right"><div align="left" id="controls" class="olControlEditingToolbar"></div></td>
                    <td style="width:70%" align="right"><img id="image" src="<?php echo PUBLIC_URL;?>plmis_img/image.png"/> <img id="print"  style="cursor:pointer; margin-left:-5px;width:30px;height:30px" src="<?php echo PUBLIC_URL;?>plmis_img/print.png"/></td>
                </tr>
            </table>
            <div style="width:auto;height:auto">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><div id="map">
                                <div id="infoDiv">
                                    <h6 align="center"><b><font color="#008000">Click on the Feature to  View the <br/>
                                        information</font></b></h6>
                                    <div id="info"></div>
                                </div>
                                <img id="loader" src="<?php echo PUBLIC_URL;?>images/ajax-loader.gif"/> </div></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include PUBLIC_PATH."/html/footer.php";?>
</body>
</html>