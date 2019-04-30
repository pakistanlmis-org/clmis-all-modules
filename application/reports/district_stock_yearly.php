<?php
/**
 * district_stock_yearly
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//echo '<pre>';print_r($_REQUEST);exit;
//include AllClasses
ini_set('max_execution_time', 300);
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "STOCK";
//initialize variables
$selStk = $selPro = $type = $sector = $indicatorLvl = $rptType = $stkFilter = $selDist = '';
//if submitted
if (isset($_REQUEST['submit'])) {
    //selected year
    $selYear = $_REQUEST['year_sel'];
    //selected month
    $selMonth = $_REQUEST['ending_month'];
    //selected item
    $selItem = $_REQUEST['item_id'];
    //selected province
    $selPro = $_REQUEST['prov_sel'];
    //selected stakeholder
    $selStk = $_REQUEST['stk_sel'];
    //type
    $type = $_REQUEST['type'];
    //sector
    $sector = $_REQUEST['sector'];
    $selDist = !empty($_REQUEST['district']) ? $_REQUEST['district'] : '';
    //indicator level
    $indicatorLvl = $_REQUEST['indicator_lvl'];
} else {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //selected month
    $selMonth = date('m', strtotime($date));
    //selected year
    $selYear = date('Y', strtotime($date));
    //indicator level
    $indicatorLvl = 'all';
    //selected item
    $selItem = "IT-001";
    //get selected province
    $selPro = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set selected province
    $selPro = ($selPro != 10) ? $selPro : 'all';
    //check user stakeholder type
    if ($_SESSION['user_stakeholder_type'] == 0) {
        $sector = 'public';
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        $sector = 'private';
    }
    //selected stakeholder
    $selStk = $_SESSION['user_stakeholder1'];
    //type
    $type = 4;
    //indicator level
    $indicatorLvl = 'all';
}
//check selected province
if ($selPro != 'all') {
    //province filter
    $provFilter = " AND tbl_locations.ParentID = $selPro";
    //province filter1
    $provFilter1 = " AND summary_district.province_id = $selPro";
}
$distFilter  ="";
//check selected $selDist
$grp_by1= $grp_by2 = '';
if (!empty(($selDist)) && $selDist != 'all') {
//    echo 'a';exit;
    //province filter
    $distFilter = " AND tbl_locations.PkLocID = $selDist";
    $grp_by1 = " ,tbl_wh_data.wh_id ";
    $grp_by2 = " ,tbl_warehouse.wh_id ";
}
//echo 'b';exit;
// Check selected Product
if ($selItem != '') {
    //province filter
    $productFilter = " AND tbl_wh_data.item_id = '" . $selItem . "' ";
    $productFilter3 = " AND itminfo_tab.itmrec_id = '" . $selItem . "' ";
    //query result
    $itmQry = mysql_fetch_array(mysql_query("SELECT
				itminfo_tab.itm_name
			FROM
				itminfo_tab
			WHERE
				itminfo_tab.itmrec_id = '$selItem'"));
    $proName = $itmQry['itm_name'];
} else {
    $proName = 'All Products';
}


if ($type == 1) {
    // type text
    $typeText = "Issue";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up';
    $colName2 = 'tbl_hf_data.issue_balance';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 3';
} else if ($type == '2') {
    // type text
    $typeText = "Receive";
    //set col name
    $colName = 'tbl_wh_data.wh_received';
    $colName2 = 'tbl_hf_data.received_balance';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 3';
} else if ($type == '3') {
    // type text
    $typeText = "Consumption";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up';
    $colName2 = 'tbl_hf_data.issue_balance';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 4';
} else if ($type == '4') {
    // type text
    $typeText = "Stock on Hand";
    //set col name
    $colName = 'tbl_wh_data.wh_cbl_a';
    $colName2 = 'tbl_hf_data.closing_balance';
    if ($indicatorLvl == 'all') {
        //set level filter
        $lvlFilter = ' AND stakeholder.lvl IN(3, 4)';
    } else {
        //set level filter
        $lvlFilter = " AND stakeholder.lvl  = $indicatorLvl";
    }
} else if ($type == '5') {
    // type text
    $typeText = "CYP";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up * itminfo_tab.extra';
    $colName2 = 'tbl_hf_data.issue_balance * itminfo_tab.extra';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 4';
}

if ($selPro == 'all') {
    //set province filter
    $provFilter = '';
    //set province name
    $provinceName = 'All';
} else {
    //set province filter
    $provFilter = "AND tbl_warehouse.prov_id = '" . $selPro . "' ";
    //select query
    //gets
    //province name
    $provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName FROM tbl_locations WHERE PkLocID = '" . $selPro . "' "));
    $provinceName = "\'$provinceQryRes[LocName]\'";
}


if ($selDist == '') {
    //set province filter
    $distFilter = '';
    //set province name
    $distName = 'All';
} else {
    //set province filter
    $distFilter = "AND tbl_warehouse.dist_id = '" . $selDist . "' ";
    //select query
    //gets
    //province name
    $provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName FROM tbl_locations WHERE PkLocID = '" . $selDist . "' "));
    $distName = "\'$provinceQryRes[LocName]\'";
}

//check sector
if ($sector == 'All') {
    //set report type
    $rptType = 'All';
} else {
    //set report type
    $rptType = $sector;
}
if (!empty($selStk) && $selStk != 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.MainStakeholder = '" . $selStk . "'";
} else if ($rptType == 'public' && $selStk == 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.stk_type_id = 0";
} else if ($rptType == 'private' && $selStk == 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.stk_type_id = 0";
}

$dataArr = array();

//header
$header = 'District Id, Sr. No., District, Stakeholder';
//width
$width = '10,40,200,100,80,80,80,80,80,80,80,80,80,80,80,80';
//row
$ro = 'ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro';
//count
$count = 2;
//get period
//month array
//header
$to_date = $selYear .'-'.$selMonth.'-01';
//$to_date = '2017-01-01';

$months = array();
$months[] = $to_date;
$last_month = $to_date;
for ($i = 1; $i < 12; $i++) {
    $m = date("Y-m-01", strtotime( date("Y-m-01",strtotime($to_date))." -$i months"));
    $months[] = $m;
    $first_month = $m;
}
//echo $first_month;exit;
krsort($months);
//echo '<pre>';print_r($months);exit;
//width



if (isset($_REQUEST['submit']))
{
    if (!empty(($selDist)) && $selDist != 'all')
    {
        $newQry ="";
        if($type ==4 && $indicatorLvl == '3' || $indicatorLvl == 'all' )
        {
            $newQry = " 
                ( SELECT
                        tbl_locations.PkLocID AS DistrictID,
                        tbl_locations.LocName AS DistrictName,
                        SUM($colName) AS total,
                        MainStk.stkname,
                        tbl_warehouse.stkid,
                        tbl_warehouse.wh_name,
                        tbl_wh_data.RptDate as r_date
                FROM
                        tbl_warehouse
                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
                INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
                INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                WHERE
                        tbl_wh_data.RptDate BETWEEN '".$first_month."' AND '" . $to_date . "'
                        AND itminfo_tab.itm_category = 1
                        $productFilter
                        $provFilter
                        $distFilter
                        $stkFilter
                        $lvlFilter
                GROUP BY
                        tbl_warehouse.wh_id
                        ,tbl_warehouse.stkid,
                        tbl_wh_data.RptDate
                ORDER BY
                        tbl_warehouse.wh_name 
               )
            ";
        }
        if( $indicatorLvl == 'all' )
        {
            $newQry .= " UNION ";
        }
        if($indicatorLvl == '4' || $indicatorLvl == 'all' )
        {
            $newQry .= "    
               (
                SELECT
                        tbl_locations.PkLocID AS DistrictID,
                        tbl_locations.LocName AS DistrictName,
                        Sum($colName2) AS total,
                        MainStk.stkname,
                        tbl_warehouse.stkid,
                        tbl_warehouse.wh_name,
                        tbl_hf_data.reporting_date as r_date
                FROM
                    tbl_warehouse
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                    tbl_hf_data.reporting_date BETWEEN '".$first_month."' AND '" . $to_date . "'
                    AND itminfo_tab.itm_category = 1

                    $productFilter3
                    $provFilter
                    $distFilter
                    $stkFilter

                GROUP BY              
                    tbl_warehouse.wh_id
                    ,tbl_warehouse.stkid  ,
                    tbl_hf_data.reporting_date
                ORDER BY
                    tbl_warehouse.wh_name
              )
            ";
        }
    }
    else
    {
        $newQry = " 
                SELECT
                        tbl_locations.PkLocID AS DistrictID,
                        tbl_locations.LocName AS DistrictName,
                        SUM($colName) AS total,
                        MainStk.stkname,
                        tbl_warehouse.stkid,
                        tbl_warehouse.wh_name,
                        tbl_wh_data.RptDate as r_date
                FROM
                        tbl_warehouse
                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
                INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
                INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                WHERE
                        tbl_wh_data.RptDate BETWEEN '".$first_month."' AND '" . $to_date . "'
                        AND itminfo_tab.itm_category = 1
                        $productFilter
                        $provFilter
                        $distFilter
                        $stkFilter
                        $lvlFilter
                GROUP BY
                        tbl_warehouse.wh_id
                        ,tbl_warehouse.stkid,
                        tbl_wh_data.RptDate
                ORDER BY
                        tbl_warehouse.wh_name
        ";
    }
     
    //query result
    //echo $newQry;
    //exit;

    $qryRes = mysql_query($newQry);
    $num = mysql_num_rows($qryRes);
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        //data array
        //if (!empty(($selDist)) && $selDist != 'all')
          
        $dataArr[$row['wh_name']]['wh_name']  = $row['wh_name'];
        $dataArr[$row['wh_name']]['stkname']  = $row['stkname'];
        
        $dataArr[$row['wh_name']]['totals'][$row['r_date']]    = $row['total'];
        
//        foreach($months as $k=>$v)
//        {
//            $dataArr[$row['wh_name']]['totals'][$v]    = $row['total'];
//        }
    }
    $count++;
}
foreach($months as $k=>$v)
{
    $header .= ',<span>' . date("M-y",strtotime($v)) . '</span>';
}
//echo '<pre>';print_r($dataArr);exit;
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
//sum array
$sumArr = array();
$i = 1;
foreach ($dataArr as $dist_name => $subArr) {
    $xmlstore .= "<row>";

    //district id
    $xmlstore .= "<cell> </cell>";
    $xmlstore .= "<cell style=\"text-align:center\">" . $i++ . "</cell>";
    $xmlstore .= "<cell>$dist_name</cell>";
    $xmlstore .= "<cell>".$subArr['stkname']."</cell>";

    foreach ($months as $k=> $m) {
        //echo '>>'.$m;
    
        if (!empty($subArr['totals'][$m]) && $subArr['totals'][$m]!='') {
            $xmlstore .= "<cell style=\"text-align:right;\" >" . number_format($subArr['totals'][$m]) . "</cell>";
        } else {
            $xmlstore .= "<cell style=\"text-align:center;color:#EE2000\" >NR</cell>";
        }
    }
    $xmlstore .= "</row>";
}
//$xmlstore .= "<row>";
//$xmlstore .= "<cell></cell>";
//$xmlstore .= "<cell></cell>";
//$xmlstore .= "<cell></cell>";
//$xmlstore .= "<cell style=\"text-align:right\">Total</cell>";
//foreach ($sumArr as $key => $value) {
//    if ($key > 1) {
//        $xmlstore .= "<cell style=\"text-align:right\">" . number_format($value) . "</cell>";
//    }
//}
//$xmlstore .= "</row>";
$xmlstore .= "</rows>";
//check selected stakeholder
if ($selStk == 'all') {
    //set stakeholder name
    $stkName = "\'All\'";
} else {
    //select query
    //gets
    //stakeholder
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $selStk . "' "));
    //stakeholder name
    $stkName = "\'$stakeNameQryRes[stkname]\'";
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im 
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Stock Yearly Report</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>

                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Ending Month</label>
                                                    <div class="controls">
                                                        <select name="ending_month" id="ending_month" class="form-control input-sm">
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate ending_month combo
                                                                ?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate year_sel combo
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Sector</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="sector" name="sector">
                                                            <option <?php echo ($rptType == 'all') ? 'selected="selected"' : ''; ?> value="all">All</option>
                                                            <option <?php echo ($rptType == 'public') ? 'selected="selected"' : ''; ?> value="public">Public</option>
                                                            <option <?php echo ($rptType == 'private') ? 'selected="selected"' : ''; ?> value="private">Private</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($selStk == 'all') ? "selected='selected'" : ""; ?>>All</option>
                                                            <?php
                                                            $querystk = "SELECT DISTINCT
																			stakeholder.stkid,
																			stakeholder.stkname
																		FROM
																			tbl_warehouse
																		INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																		WHERE
																			tbl_warehouse.is_active = 1
																		ORDER BY
																			stakeholder.stk_type_id ASC,
																			stakeholder.stkorder ASC";
                                                            $rsstk = mysql_query($querystk) or die();
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                if ($selStk == $rowstk['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowstk['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowstk['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                    <div class="controls">
                                                        <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($selPro == 'all') ? "selected='selected'" : ""; ?>>All</option>
                                                            <?php
//select query
//gets
//province id
//province title
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL";
//query result
                                                            $rsprov = mysql_query($queryprov) or die();
//fetch result
                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                if ($selPro == $rowprov['prov_id']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate prov_sel
                                                                ?>
                                                                <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                            <div class="control-group" id="districtsCol">
                                                <label>District</label>
                                                <div class="controls">
                                                    <select name="district" id="district"  class="form-control input-sm">
                                                        <?php
                                                        //select query
                                                        $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $selPro . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                        //query result
                                                        $rsDist = mysql_query($queryDist) or die();
                                                        //fetch result
                                                        while ($rowDist = mysql_fetch_array($rsDist)) {
                                                            if ($selDist == $rowDist['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            //populate district combo
                                                            ?>
                                                            <option value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Indicator</label>
                                                    <div class="controls">
                                                        <select name="type" id="type" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="1" <?php echo ($type == 1) ? "selected='selected'" : ""; ?>>Issue</option>
                                                            <option value="2" <?php echo ($type == 2) ? "selected='selected'" : ""; ?>>Receive</option>
                                                            <option value="3" <?php echo ($type == 3) ? "selected='selected'" : ""; ?>>Consumption</option>
                                                            <option value="5" <?php echo ($type == 5) ? "selected='selected'" : ""; ?>>CYP</option>
                                                            <option value="4" <?php echo ($type == 4) ? "selected='selected'" : ""; ?>>Stock on Hand</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="indicator_col" style="display:none;">
                                                <div class="control-group">
                                                    <label>Indicator Level</label>
                                                    <div class="controls">
                                                        <select name="indicator_lvl" id="indicator_lvl" required class="form-control input-sm">
                                                            <option value="all" <?php echo ($indicatorLvl == 'all') ? "selected='selected'" : ""; ?>>District and Field</option>
                                                            <option value="3" <?php echo ($indicatorLvl == 3) ? "selected='selected'" : ""; ?>>District</option>
                                                            <option value="4" <?php echo ($indicatorLvl == 4) ? "selected='selected'" : ""; ?>>Field</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Product</label>
                                                    <div class="controls">
                                                        <select name="item_id" id="item_id" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($num > 0 && isset($_REQUEST['submit'])) {
                            ?>
                            <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="mygrid_container" style="width:100%; height:390px;"></div>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            echo '<h6>No record found.</h6>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("#cspan,<div style='text-align:center;'><?php echo "District " . ucwords($typeText) . " Yearly Report for Sector = '" . ucwords($rptType) . "' Stakeholder(s) = $stkName Province/Region = $provinceName And Product = '$proName'"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("<?php echo $header; ?>");
            mygrid.attachFooter(",<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("<?php echo $width; ?>");
            mygrid.setColTypes("<?php echo $ro; ?>");
            mygrid.setColumnHidden(0, true);
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.enableCollSpan(true);
            mygrid.setSkin("light");
            mygrid.init();
            //mygrid.loadXML("xml/stock.xml");
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function (data) {
                    $('#stk_sel').html(data);
                    showProducts('<?php echo (!empty($selItem)) ? $selItem : ''; ?>');
                }
            })
        }

        $(function () {
            showIndLvl('<?php echo $type; ?>');
            $('#type').change(function (e) {
                showIndLvl($(this).val());
            });
            $('#stk_sel').change(function (e) {
                showProducts('');
                $('#prov_sel').html('<option value="">Select</option>');
                showProvinces('');
            });

            $('#sector').change(function (e) {
                $('#item_id').html('<option>Select</option>');
                var val = $('#sector').val();
                getStakeholder(val, '');
            });

            $('#type').change(function (e) {
                showAllProducts($(this).val());
                $("#item_id").val('');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $selStk; ?>');
            setTimeout(
                    function ()
                    {
                        showAllProducts('<?php echo $type; ?>');
                    }, 1000);
        })
<?php
if (isset($selItem) && !empty($selItem)) {
    ?>
            showProducts('<?php echo $selItem; ?>');
            showProvinces('<?php echo $selPro; ?>');
    <?php
}
?>
        function showAllProducts(indVal) {
            if (indVal == 5) {
                $("#item_id option[value='']").text('All');
                $("#item_id").removeAttr('required');
            } else {
                $("#item_id option[value='']").text('Select');
                $("#item_id").attr('required', 'required');
            }
        }
        function showIndLvl(val) {
            if (val == 4) {
                $('#indicator_col').show();
            } else {
                $('#indicator_col').hide();
            }
        }
        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function (data) {
                    $('#item_id').html(data);
                }
            })
        }
        function showProvinces(pid) {
            var stk = $('#stk_sel').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    type: 'POST',
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                    success: function (data) {
                        $('#prov_sel').html(data);
                    }
                })
            }
        }
        
        
        $(function () {
            showDistricts('<?php echo $selDist; ?>');
            $('#prov_sel').change(function (e) {
                showDistricts('');
            });
        })
        function showDistricts(dId) {
            var provinceId = $('#prov_sel').val();
            if (provinceId != '') {
                $.ajax({
                    url: 'ajax_calls.php',
                    data: {provinceId: provinceId, dId: dId,validate:'no', stkId: $('#stk_sel').val()},
                    type: 'POST',
                    success: function (data) {
                        $('#districtsCol').html(data);
                    }
                })
            }
        }
    </script>
</body>
<!-- END BODY -->
</html>