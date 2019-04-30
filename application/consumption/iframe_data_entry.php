<?php
/**
 * iframe_data_entry
 * @package consumption
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */
//Start session
session_start();
$_SESSION['user_id'] = '0';
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Including header file
//include(PUBLIC_PATH . "html/header.php");
//Including top_im file
//include PUBLIC_PATH . "html/top_im.php";
//Checking if data is saved
if (isset($_REQUEST['e']) && $_REQUEST['e'] == 'ok') {
    //Display message
    echo "Data has been successfully saved. ";
    exit;
}
?>
    <style>

        body {
            font: Verdana, Arial, Helvetica, sans-serif;
            font-family: Verdana, Arial, Helvetica, sans-serif;
            width:100%;
            margin:0px;
            padding:0;

        }

        #contenttext1{
            width:800px;
            background-color: #d1b2d1;;;
            border:solid 1px #5c005c;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            min-height:200px;
            float:left;
        }

        #pagetitle{
            position:relative;
            width:98%;
            height:88px;
            background-color:#f7f7f7;
            border-right:solid 1px #5c005c;
            border-left:solid 1px #5c005c;
        }
        #pagetitle1{
            width:800px;
            height:85px;
            background-color:#f7f7f7;
            border-right:solid 1px #5c005c;
            border-left:solid 1px #5c005c;
        }
        #title1{
            position:relative;
            width:800px;
            font-size:36px;
            font-family:"Times New Roman", Times, serif;
            color:#5c005c;

        }

        .loginuser {
            font: 0.7em Tahoma, sans-serif;
            font-size: 11px;
            color:  #853385;
            position:absolute;
            right:3px;
            top:0px;
        }

        .smalltext {
            font: 0.7em Tahoma, sans-serif;
            font-size:12px;
            font-weight:bold;
            color: #222222;
        }

        input, select, textarea {

            background-color:#foe6fo;

            border:#5c005c1px solid;
            border-radius:5px;

            font: tahoma,geneva,Arial, Verdana, Helvetica, sans-serif;

            font-family: tahoma,geneva,Arial, Verdana, Helvetica, sans-serif;

            font-size: 12px;

            color: #000000;#5c005c;
            border:1px solid #5c005c;

        }

        input:focus, select:focus, textarea:focus {

            background-color: #c299c2;;

        }
        tr:nth-child(even) {

            background:  #c299c2;;

            font: 12px tahoma,Arial, Verdana, Helvetica, sans-serif;

            color:black;

        }

        tr:nth-child(odd) {

            background:  #d1b2d1;;

            font: 12px tahoma,Arial, Verdana, Helvetica, sans-serif;

            color:black;
        }

        th {

            background: #5c005c;
            border-radius:8px;
            font: bold 12px tahoma,Arial, Verdana, Helvetica, sans-serif;
            padding-left:4px;
            padding-right:4px;
            color: #EEEEEE;

        }
        .subheading-new {

            background: #853385;

            font: bold 12px tahoma,Arial, Verdana, Helvetica, sans-serif;

            color:  #6c196c; ;
        }
        tr:nth-last-child(1) {

            background:  #853385;

            font: 12px tahoma,Arial, Verdana, Helvetica, sans-serif;

            color:#000000;

        }
        table
        {
            background-color:#foe6fo;
            border:2px solid #5c005c;
            border-radius:8px;

        }

        .login
        {
            background-color:#foe6fo;
            border:2px solid #5c005c;
            border-radius:8px;
            padding:7px,7px;
            padding-left:8px;
            padding-right:8px;
            padding-top:8px;
            padding-bottom:8px;
            box-shadow: 10px 10px 5px #888888;
        }
        .login td
        {
            padding-bottom:1px;
            padding-top:1px;
        }

        .login tr
        {
            background-color:#foe6fo;
        }
        .text
        {
            font-size:12px;
            color:#000000;
        }
        #contenttext{

            width:100%;
            background-color: #fff;
            border:solid 0px #5c005c;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            min-height:100px;
            float: left;
        }
        A:visited {color: #5c005c;}
        A:hover {color: red;}

        &lt;!--.menu{
                 border-left:10px solid #DFDFDF;
                 border-right:10px solid #DFDFDF;
                 font-family:"Times New Roman", Times, serif;
                 font-size:14px;
                 font-weight:bold;
                 width:940px;
                 color:#FFFFFF;
                 margin-left:150px;
                 margin-right:150px;
             }
        .menu ul{
            background: ;;
            height:25px;
            list-style:none;
            margin:0;
            padding:0;
            color:#FFFFFF;
        }
        .menu li{
            float:left;
            padding:0px;
            color:#FFFFFF;
        }
        .menu li a{
            background: ;

            color:#FFFFFF;
            display:block;
            font-weight:normal;
            /*border-left:solid 1px ;
            border-right:solid 1px ;*/
            line-height:25px;
            margin:0px;
            padding:0px 15px;
            text-align:center;
            text-decoration:none;
        }
        .menu li a:hover, .menu ul li:hover a{
            background:  ;
            color:#FFFFFF;
            text-decoration:none;
        }
        .menu li ul{
            background: ;;
            display:none;
            height:auto;
            padding:0px;
            margin:0px;
            border:0px;
            position:absolute;
            width:181px;
            z-index:200;
            /*top:1em;
            /*left:0;*/
            color:#FFFFFF;
        }
        .menu li:hover ul{
            display:block;
            color:#FFFFFF;
        }
        .menu li li {
            color:#FFFFFF;
            display:block;
            float:none;
            margin:0px;
            padding:0px;
            width:180px;
        }
        .menu li:hover li a{
            background:none;
            color:#FFFFFF;
        }
        .menu li ul a{
            display:block;
            height:25px;
            font-size:14px;
            font-style:normal;
            margin:0px;
            padding:0px 10px 0px 15px;
            text-align:left;
            border-bottom:solid 1px ;
            color:#FFFFFF;
        }
        .menu li ul a:hover, .menu li ul li:hover a{
            background: ;
            text-decoration:none;
            color:#FFFFFF;
        }

        /*
        Template Name: Internet Business
        File: Navigation CSS
        Author: OS Templates
        Author URI: http://www.os-templates.com/
        Licence: &lt;a href="http://www.os-templates.com/template-terms"&gt;Website Template Licence&lt;/a&gt;
        */

        #topnav{
            display:inline;
            float:left;
            width:970px;
            margin-bottom:20px;
            list-style:none;
            font-size:12px;
            font-weight:normal;
            font-family:Verdana, Arial, Helvetica, sans-serif;
            color:#FFFFFF;
            background-color:#333333;
        }

        #topnav ul{
            float:left;
            list-style:none;
            margin:0;
            padding:0;
        }
        #topnav ul li{
            float:left;
            list-style:none;
            margin:0;
            padding:0;
        }


        #topnav li a:link, #topnav li a:visited, #topnav li a:hover{
            display:block;
            margin:0;
            padding:16px 10px;
            color:#FFFFFF;
            background-color:#333333;
        }
        #topnav ul ul li a:link, #topnav ul ul li a:visited{
            border:none;
        }

        #topnav li.last a{
            margin-right:0;
        }

        #topnav li a:hover, #topnav ul li.active a{
            color:#383838;
            background-color:#f0d343;
        }

        #topnav li li a:link, #topnav li li a:visited{
            width:160px;
            float:none;
            margin:0;
            padding:7px 10px;
            font-size:12px;
            font-weight:normal;
            color:#FFFFFF;
            background-color:#333333;
        }

        #topnav li li a:hover{
            color:#333333;
            background-color:#f0d343;
        }

        #topnav li ul{
            background:#FFFFFF;
            z-index:9999;
            position:absolute;
            left:-999em;
            height:auto;
            width:100px;
        }
        --&gt;
        #topnav li ul a{width:140px;}

        #topnav li ul ul{margin:-32px 0 0 0;}

        #topnav li:hover ul ul{left:-999em;}

        #topnav li:hover ul, #topnav li li:hover ul{left:auto;}

        #topnav li:hover{position:static;}

        #topnav li.last a{margin-right:0;}


    </style>

                <?php
                $wh_id = "";
                if (isset($_REQUEST['do']) && !empty($_REQUEST['do'])) {
                    list($hfCode, $reportingDate, $dataViewType) = explode("|", base64_decode($_REQUEST['do']));
                    //Report Date
                    $RptDate = trim($reportingDate) . '-01';
                    list($yyy,$mmm) = explode("-",$reportingDate);
                    if($yyy > date("Y") || ($yyy == date("Y") && $mmm > date("m"))){
                        exit('You can\'t enter forward months data!');
                    }
                    //if value=1 then new report
                    $isNewRpt = $dataViewType;
                    $date = explode("-", $RptDate);
                    //Reprot year
                    $yy = $date[0];
                    //report Month
                    $mm = $date[1];

                    if (strlen($reportingDate) != 7 || !checkdate($mm, 1, $yy)) {
                        exit('Invalid date format.');
                    }
                    //***********************************
                    // Check if the facility exists
                    //***********************************
                    //Gets
                    //num
                    //wh_id
                    //wh_name
                    //dhis_code
                    $checkHF = "SELECT
								COUNT(tbl_warehouse.wh_id) AS num,
								tbl_warehouse.wh_id,
								tbl_warehouse.wh_name,
								tbl_warehouse.dhis_code
							FROM
								tbl_warehouse
							WHERE
								tbl_warehouse.dhis_code = '$hfCode' ";
                    $checkHFRes = mysql_fetch_array(mysql_query($checkHF));
                    if ($checkHFRes['num'] > 0) {
                        $wh_id = $checkHFRes['wh_id'];
                    } else {
                        //***********************************
                        // Add Health Facility
                        //***********************************
                        $url = "http://mnch.pk/cmw_db/getcmwinfo.php?cmwcode=$hfCode&month=$reportingDate";
                        //***********************************
                        //Checking curl installation
                        //***********************************
                        if (!function_exists('curl_init')) {
                            //Display error
                            die('Sorry CURL is not installed.');
                        }
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        $output = curl_exec($ch);
                        curl_close($ch);
                        //Decode from json
                        $cmwInfo = json_decode($output);
                        //cmw name
                        $hfName = trim($cmwInfo->cmwname);
                        //district code
                        $districtId = $cmwInfo->districtcode;
                        //province code
                        $provinceId = $cmwInfo->provincecode;
                        //reporting status
                        $reportingStatus = $cmwInfo->reportingstatus;
                        //Insert query
                        $qry = "INSERT INTO tbl_warehouse
						SET	 
							 tbl_warehouse.wh_name = '" . $hfName . "',
							 tbl_warehouse.dist_id = REPgetLmisLocationCode($districtId),
							 tbl_warehouse.prov_id = REPgetLmisLocationCode($provinceId),
							 tbl_warehouse.stkid = 73,
							 tbl_warehouse.locid = REPgetLmisLocationCode($districtId),
							 tbl_warehouse.stkofficeid = 111,
							 tbl_warehouse.hf_type_id = 19,
							 tbl_warehouse.dhis_code = '$hfCode'";
                        mysql_query($qry);
                        $wh_id = mysql_insert_id();
                        // Get User ID
                        $qry = "SELECT
								wh_user.sysusrrec_id
							FROM
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
							WHERE
								tbl_warehouse.stkid = 73
							AND tbl_warehouse.dist_id = REPgetLmisLocationCode($districtId)
								LIMIT 1";
                        $qryRes = mysql_fetch_array(mysql_query($qry));
                        $userId = $qryRes['sysusrrec_id'];
                        //***********************************
                        // Assign warehosue to the user
                        //***********************************
                        $qry = "INSERT INTO wh_user
							SET
								wh_user.sysusrrec_id = $userId,
								wh_user.wh_id = $wh_id";
                        mysql_query($qry);
                    }
                    if (!empty($wh_id)) {
                        //***********************************************************************
                        // If 1st data entry month then open Opening Balance Field else Lock it
                        //***********************************************************************
                        //Gets reporting_date
                        $checkData = "SELECT
									tbl_hf_data.reporting_date
								FROM
									tbl_hf_data
								WHERE
									tbl_hf_data.warehouse_id = $wh_id
								ORDER BY
									tbl_hf_data.reporting_date ASC
								LIMIT 1";
                        $checkDataRes = mysql_fetch_array(mysql_query($checkData));
                        $openOB = ($checkDataRes['reporting_date'] == $RptDate) ? '' : $checkDataRes['reporting_date'];

                        $month = date('M', mktime(0, 0, 0, $mm, 1));

                        //****************************************************************************
                        $objwarehouse->m_npkId = $wh_id;
                        //Get Stakeholder ID By WH Id
                        $stkid = $objwarehouse->GetStkIDByWHId($wh_id);
                        //Get Warehouse Name By Id
                        $whName = $objwarehouse->GetWarehouseNameById($wh_id);
                        echo "<h3 style='height:10px; margin-top:0px;' class=\"page-title row-br-b-wp\">" . $whName . " <span class=\"green-clr-txt\">(" . $month . ' ' . $yy . ")</span> </h3>";
                        //If new report
                        if ($isNewRpt == 1) {
                            //Get Previous Month Report Date
                            $PrevMonthDate = $objReports->GetPreviousMonthReportDate($RptDate);
                        } else {
                            $PrevMonthDate = $RptDate;
                        }

                        $redirectURL = 'iframe_data_entry.php';
                        //Including file
                        include('data_entry_common.php');
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/dataentry.js"></script>
    <script>
        function get_browser_info() {
            var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
            if (/trident/i.test(M[1])) {
                tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                return {name: 'IE', version: (tem[1] || '')};
            }
            if (M[1] === 'Chrome') {
                tem = ua.match(/\bOPR\/(\d+)/)
                if (tem != null) {
                    return {name: 'Opera', version: tem[1]};
                }
            }
            M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
            if ((tem = ua.match(/version\/(\d+)/i)) != null) {
                M.splice(1, 1, tem[1]);
            }
            return {
                name: M[0],
                version: M[1]
            };
        }
        var browser = get_browser_info();
        //alert(browser.name + ' - ' + browser.version);
        if (browser.name == 'Firefox' && browser.version < 30)
        {
            alert('You are using an outdated version of the Mozilla Firefox. Please update your browser for data entry.');
            window.close();
        }
        else if (browser.name == 'Chrome' && browser.version < 35)
        {
            alert('You are using an outdated version of the Chrome. Please update your browser for data entry.');
            window.close();
        }
        else if (browser.name == 'Opera' && browser.version < 28)
        {
            alert('You are using an outdated version of the Opera. Please update your browser for data entry.');
            window.close();
        }
        else if (browser.name == 'MSIE')
        {
            alert('Please use Mozilla Firefox, Chrome or Opera for data entry.');
            window.close();
        }
    </script>