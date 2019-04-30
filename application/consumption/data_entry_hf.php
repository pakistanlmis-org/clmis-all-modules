<?php
/**
 * data_entry_hf
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/dataentry.js"></script>
<?php
include PUBLIC_PATH . "html/top_im.php";
//Checking user_id
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    //Get ProvinceId By Idc
    $result_province = $objwharehouse_user->GetProvinceIdByIdc();
} else {
    //Display error message
    echo "user not login or timeout";
}

if (isset($_GET['e']) && $_GET['e'] == 'ok') {
    ?>
    <script type="text/javascript">
        function RefreshParent() {
            if (window.opener != null && !window.opener.closed) {
                window.opener.location.reload();
            }
        }
        window.close();
        RefreshParent();
        //window.onbeforeunload = RefreshParent;
    </script>
    <?php
    exit;
}

?>
<link href="<?php echo PUBLIC_URL; ?>css/styles.css" rel="stylesheet" type="text/css"/>
</head><body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important">
<?php
$wh_id = "";
//Checking Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
    $temp = urldecode($_REQUEST['Do']);
    $tmpStr = substr($temp, 1, strlen($temp) - 1);
    $temp = explode("|", $tmpStr);

    //****************************************************************************
    // Warehouse ID
    $wh_id = $temp[0] - 77000;
    //Report Date
    $RptDate = $temp[1];
    //if value=1 then new report
    $isNewRpt = $temp[2];
    $tt = explode("-", $RptDate);
    //Reprot year
    $yy = $tt[0];
    //report Month
    $mm = $tt[1];

    $RptDate = $temp[1];
    $readonly_for_im = false;
    if ($_SESSION['is_allowed_im'] == 1 &&  $RptDate >= $_SESSION['im_start_month']) {
        $isReadOnly = 'readonly="readonly"';
        $style = 'style="background:#CCC"';
        $readonly_for_im = true;
    } else {
        $isReadOnly = '';
        $style = '';
        $readonly_for_im = false;
    }
    
    
    // Check level
    $qryLvl = mysql_fetch_array(mysql_query("SELECT
															stakeholder.lvl,
															tbl_warehouse.hf_type_id,
															tbl_warehouse.prov_id
														FROM
															tbl_warehouse
														INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
														WHERE
															tbl_warehouse.wh_id = $wh_id"));
    $hfTypeId = $qryLvl['hf_type_id'];
    $wh_lvl = $qryLvl['lvl'];

    // Check if its 1st Month of Data Entry 
    $whProvId = $qryLvl['prov_id'];
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
    //Checking user_stakeholder and user_province1
    if ($_SESSION['user_stakeholder'] == 73 && $_SESSION['user_province1'] == 1) {
        $openOB = '';
    }

    $month = date('M', mktime(0, 0, 0, $mm, 1));

    //****************************************************************************
    $objwarehouse->m_npkId = $wh_id;
    //Get Stk ID By WH Id
    $stkid = $objwarehouse->GetStkIDByWHId($wh_id);
    //Get Warehouse Name By Id
    $whName = $objwarehouse->GetWarehouseNameById($wh_id);
    echo "<h3 class=\"page-title row-br-b-wp\">" . $whName . " <span class=\"green-clr-txt\">(" . $month . ' ' . $yy . ")</span> </h3>";
    include("stock_sources.php");   
    
//If new report
    if ($isNewRpt == 1) {
        //Get Previous Month Report Date
        $PrevMonthDate = $objReports->GetPreviousMonthReportDate($RptDate);
    } else {
        $PrevMonthDate = $RptDate;
    }
    //Including file
    include('data_entry_common.php');
}
?>       	
            </div>
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script> 
    
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
</body>
</html>