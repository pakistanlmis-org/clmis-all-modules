<?php
/**
 * field_availability
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "FSAR";
$selYear = $selMonth = $selItem = $selPro = $districtId = $selStk = $type = $sector = $stkName = $provinceName = $proName = '';
//if submitted
if (isset($_REQUEST['submit'])) {
    //echo '<pre>';print_r($_REQUEST);
    //get selected year
    $selYear = $_REQUEST['year_sel'];
    //get selected month
    $selMonth = $_REQUEST['month'];
    //get selected item
    $selItem = implode(',',$_REQUEST['product']);
//    print_r($selItem);exit;
    $itm_arr_request=$_REQUEST['product'];
    //get selected province
    $selPro = $_REQUEST['prov_sel'];
    //get district Id 
    $districtId = $_REQUEST['district'];
    //get selected stakeholder
    $stk_arr=$_REQUEST['stk_sel'];
    $selStk = implode(',',$_REQUEST['stk_sel']);
    //get type
    $type = $_REQUEST['type'];
} else {
    if (date('d') > 10) {
        //set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        //set date
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //set selected month
    $selMonth = date('m', strtotime($date));
    //set selected year
    $selYear = date('Y', strtotime($date));
    //set selected item
    $selItem = '';
    //set selected province
    $selPro = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set selected province
    $selPro = ($selPro != 10) ? $selPro : 1;
    //set district Id 
    $districtId = $_SESSION['user_district'];
    //set selected stakeholder
    $selStk = (!empty($_SESSION['user_stakeholder1'])) ? $_SESSION['user_stakeholder1'] : 1;
    //set type
    $type = 'SAT';
}
$proFilter=$proFilter1='';
$districtId = (!empty($districtId)) ? $districtId : 'all';
$and = ($districtId != 'all') ? " AND tbl_warehouse.dist_id = $districtId" : '';
if ($selPro != 'all' && !empty($selPro)) {
    $proFilter = " AND tbl_warehouse.prov_id = $selPro ";
    $proFilter1 = " AND tbl_hf_type_rank.province_id = $selPro ";
}

//reporting Date
$reportingDate = $selYear . '-' . str_pad($selMonth, 2, 0, STR_PAD_LEFT) . '-01';

if (strtotime($reportingDate) < strtotime('2015-10-01')) {
    //set reporting Date1
    $reportingDate1 = '2015-10-01';
} elseif (strtotime(date('Y-m', strtotime($reportingDate))) >= strtotime(date('Y-m'))) {
    //select
    //Date Qry
    $getDateQry = "SELECT MAX(warehouses_by_month.reporting_date) AS reportingDate FROM warehouses_by_month";
    //result
    $getDateQry = mysql_fetch_array(mysql_query($getDateQry));
    //set reporting Date1
    $reportingDate1 = $getDateQry['reportingDate'];
} else {
    //set reporting Date1
    $reportingDate1 = $reportingDate;
}

$where = '';

//select query
//gets
//item name
//item id
//print_r($selItem); exit;
$itm_qry="SELECT
                                                itminfo_tab.itm_id,
                                                itminfo_tab.itm_name
                                        FROM
                                                itminfo_tab
                                        WHERE
                                                itminfo_tab.itm_id in ($selItem)";
//print_r($itm_qry);
$itm_result=mysql_query($itm_qry);
$proName=$proId=array();
while ($row1 = mysql_fetch_array($itm_result)) {
    $proName[]=$row1['itm_name'];
    $proId []= $row1['itm_id'];
}
//print_r($proId);
$product_id=implode(',',$proId);
//print_r($proName);exit;
//print_r($itm_qry);exit;
//$itmQry = mysql_fetch_array(mysql_query("SELECT
//                                                itminfo_tab.itm_id,
//                                                itminfo_tab.itm_name
//                                        FROM
//                                                itminfo_tab
//                                        WHERE
//                                                itminfo_tab.itmrec_id in ('$selItem')"));
//province name
//$proName = $itmQry['itm_name'];
//province id
 
//$proId = $itmQry['itm_id'];
$product_filter="";
if(!empty($selItem) && $selItem!='' )
{
    $product_filter = " AND tbl_hf_data.item_id IN ($product_id) ";
}
//select query
//gets
//stakeholder
$stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid IN( '" . $selStk . "') "));
//stakeholder name
$stkName = "\'$stakeNameQryRes[stkname]\'";
//select query
//gets
//province Name
$provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName FROM tbl_locations WHERE PkLocID = '" . $selPro . "' "));
//province Name
$provinceName = "\'$provinceQryRes[LocName]\'";
//select query
//gets
//total warehouse
  $totalWHQry = "SELECT
					*
				FROM
					(
						SELECT
							COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH
						FROM
							tbl_warehouse
						INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
						INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
						WHERE
							1=1
						$and
						AND	tbl_warehouse.wh_id NOT IN (
								SELECT
									warehouse_status_history.warehouse_id
								FROM
									warehouse_status_history
								INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
								WHERE
									warehouse_status_history.reporting_month = '$reportingDate1'
								AND warehouse_status_history.`status` = 0
								AND tbl_warehouse.stkid IN ($selStk)
							)
						AND tbl_warehouse.stkid IN( $selStk)
						$proFilter
						AND stakeholder.lvl = 7
					) A
				JOIN (
					SELECT
						COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH
					FROM
						tbl_warehouse
					INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
					WHERE
						1=1
					$and
					AND	tbl_warehouse.wh_id NOT IN (
								SELECT
									warehouse_status_history.warehouse_id
								FROM
									warehouse_status_history
								INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
								WHERE
									warehouse_status_history.reporting_month = '$reportingDate1'
								AND warehouse_status_history.`status` = 0
								AND tbl_warehouse.stkid in  ($selStk)
							)
					AND tbl_warehouse.stkid in ($selStk)
					$proFilter
					AND stakeholder.lvl = 7
					$product_filter
					AND tbl_hf_data.reporting_date = '$reportingDate'
				) B";
//echo $totalWHQry;exit;
//result
$totalWHQryRes = mysql_fetch_array(mysql_query($totalWHQry));
//total warehouse
$totalWH = $totalWHQryRes['totalWH'];
//reported warehouse
$reportedWH = $totalWHQryRes['reportedWH'];
//select
//query
//gets
//warehouse id
//warehouse name
//location name
//SOH
//AMC
//MOS
$where_in="";
if(!empty($selItem) && $selItem!='')
$where_in =  " AND itminfo_tab.itm_id IN ($product_id) ";

 $qry = "SELECT
			A.wh_id,
			A.wh_name,
			A.LocName,
                        A.itm_name,
                        A.itm_id,
                        A.itmrec_id,
                        A.itm_id,
                        A.stkid,
                        A.stkname,
			IFNULL(A.closing_balance,0) AS SOH,
			A.AMC AS AMC,
			IFNULL(ROUND((A.closing_balance / A.AMC), 2),'UNK') AS MOS
                        FROM
                            (
                                SELECT
                                        tbl_warehouse.wh_id,
                                        tbl_warehouse.wh_name,
                                        tbl_warehouse.stkid,
                                        tbl_locations.LocName,
                                        tbl_hf_type_rank.hf_type_rank,
                                        tbl_warehouse.wh_rank,
                                        IFNULL(tbl_hf_data.closing_balance,0) AS closing_balance,
                                        IFNULL(tbl_hf_data.avg_consumption, 0) AS AMC,
                                        itminfo_tab.itm_name,
                                        itminfo_tab.itm_id,
                                        stakeholder.stkname,
                                        itminfo_tab.itmrec_id
                                FROM
                                        tbl_warehouse
                                INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                WHERE
                                        stakeholder.lvl = 7
                                        AND itminfo_tab.itm_category = 1
                                $and
                                $proFilter
                                $proFilter1
                                AND tbl_hf_type_rank.stakeholder_id IN( $selStk)
                                AND tbl_warehouse.wh_id NOT IN (
                            SELECT
                                    warehouse_status_history.warehouse_id
                            FROM
                                    warehouse_status_history
                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                                    warehouse_status_history.reporting_month = '$reportingDate'
                            AND warehouse_status_history.`status` = 0
                            AND tbl_warehouse.stkid IN ($selStk)
                            )
				AND tbl_hf_data.reporting_date = '$reportingDate'
				$where_in
				
			) A
		$where
		GROUP BY
			A.wh_id,A.itm_name
		ORDER BY
			A.itm_name,A.LocName,
			IF (A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
			A.wh_rank,
			A.hf_type_rank ASC,
			A.wh_name ASC";

//query result
//echo $qry;exit;
$qryRes = mysql_query($qry);
//num of record
$num = mysql_num_rows(mysql_query($qry));
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$counter = 1;
$whInd =$AMC = $SOH = $total_mos= 0;
while ($row = mysql_fetch_array($qryRes)) {
    $whInd++;
    $xmlstore .= "<row>";
    //counter
    $xmlstore .= "<cell>" . $counter++ . "</cell>";
    //location name
    $xmlstore .= "<cell><![CDATA[" . $row['LocName'] . "]]></cell>";
    //warehouse name
    $xmlstore .= "<cell><![CDATA[" . $row['wh_name'] . "]]></cell>";
       $xmlstore .= "<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
    $xmlstore .= "<cell><![CDATA[" . $row['itm_name'] . "]]></cell>";
    //AMC
    $xmlstore .= "<cell>" . number_format($row['AMC']) . "</cell>";
    //SOH
    $xmlstore .= "<cell>" . number_format($row['SOH']) . "</cell>";
    //MOS
    $xmlstore .= "<cell>" . $row['MOS'] . "</cell>";
    
    $st_text = 'Unknown';
    if(!isset($row['MOS']) || $row['MOS']=='' || $row['MOS']== null  || $row['MOS']== 'UNK'){
        $st_text = 'Unknown';
    }elseif($row['MOS']<= 0.5){
        $st_text = 'Stock Out';
    }elseif($row['MOS']<= 0.99){
        $st_text = 'Under Stock';
    }elseif($row['MOS']<= 2.99){
        $st_text = 'Satisfactory';
    }elseif($row['MOS']> 3){
        $st_text = 'Over Stock';
    }
    
    //rs mos
    
    $mos_q = "SELECT getMosColor('" . $row['MOS'] . "', '" . $row['itmrec_id'] . "', '" . $row['stkid'] . "', 4)";
    //echo $mos_q;exit;
        $rs_mos = mysql_query($mos_q);
    $bgcolor = mysql_result($rs_mos, 0, 0);
    $xmlstore .= "<cell><![CDATA[<div style=\"width:10px; height:12px; background-color:$bgcolor;\"></div>]]></cell>";
    $xmlstore .= "<cell>".$st_text."</cell>";
    $xmlstore .= "</row>";
    $AMC += $row['AMC'];

    $SOH += $row['SOH'];
    $total_mos += $row['MOS'];
}
//print_r($whInd);exit;
$xmlstore .= "<row>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell  style=\"text-align:right;font-weight:bold;\">Total</cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell>" . number_format($SOH) . "</cell>";



$xmlstore .= "<cell></cell>";
$xmlstore .= "</row>";
$xmlstore .= "</rows>";
//end xml

//echo $xmlstore;exit;
?>
<style>
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }

    span.multiselect-native-select {
        position: relative
    }
    span.multiselect-native-select select {
        border: 0!important;
        clip: rect(0 0 0 0)!important;
        height: 1px!important;
        margin: -1px -1px -1px -3px!important;
        overflow: hidden!important;
        padding: 0!important;
        position: absolute!important;
        width: 1px!important;
        left: 50%;
        top: 30px
    }
    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }
    .multiselect-container .input-group {
        margin: 5px
    }
    .multiselect-container>li {
        padding: 0
    }
    .multiselect-container>li>a.multiselect-all label {
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group-clickable label {
        cursor: pointer
    }
    .multiselect-container>li>a {
        padding: 0
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 0 3px 30px
    }
    .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
        margin: 0
    }
    .multiselect-container>li>a>label>input[type=checkbox] {
        margin-bottom: 5px
    }


.panel-actions a {
  color:#333;
}
.panel-fullscreen {
    display: block;
    z-index: 9999;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    overflow: auto;
}

</style>
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
                        <h3 class="page-title row-br-b-wp">Consolidated Stock at SDPs</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <table id="myTable">
                                        <tr bgcolor="#FFFFFF">
                                            <td colspan="6" style="font-family: Arial, Verdana, Helvetica, sans-serif;font-size: 12px;"><?php echo stripslashes(getReportStockDescription($report_id)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2"><label class="control-label">Month</label>
                                                <select name="month" id="month" class="form-control input-sm">
                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                        if ($selMonth == $i) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <td class="col-md-2"><label class="control-label">Year</label>
                                                <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                    <?php
                                                    for ($j = date('Y'); $j >= 2010; $j--) {
                                                        if ($selYear == $j) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2"><label class="control-label ">Stakeholder</label>
                                                <select name="stk_sel[]" id="stk_sel" required class="form-control input-sm multiselect-ui" multiple>
                                                    <option value="">Select</option>
                                                    <?php
                                                    //select query
                                                    //gets
                                                    //stakeholder id
                                                    //stakeholder name
                                                    $querystk = "SELECT DISTINCT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                FROM
                                                                        tbl_warehouse
                                                                INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                INNER JOIN stakeholder AS subStk ON tbl_warehouse.stkofficeid = subStk.stkid
                                                                WHERE
                                                                        stakeholder.stk_type_id IN (0, 1)
                                                                AND tbl_warehouse.is_active = 1
                                                                AND subStk.lvl = 7
                                                                ORDER BY
                                                                        stakeholder.stk_type_id ASC,
                                                                        stakeholder.stkorder ASC";
                                                    //result
                                                    $rsstk = mysql_query($querystk) or die();
                                                    //fetch result
                                                   while ($rowprov = mysql_fetch_array($rsstk)) {
                                                                     
                                                                    if (in_array($rowprov['stkid'], $stk_arr)) {
                                                                        $sel = "selected='selected'";
                                                                        $stk_name[$rowprov['stkid']] = $rowprov['stkname'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                    ?>
                                                </select></td>
                                            <td class="col-md-2"><label class="control-label">Province</label>
                                                <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                    <option value="">Select</option>
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
                                                    //result
                                                    $rsprov = mysql_query($queryprov) or die();
                                                    //fetch result
                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                        if ($selPro == $rowprov['prov_id']) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <td class="col-md-2" id="districts"><label class="control-label">District</label>
                                                <select name="district_id" id="district_id" required class="form-control input-sm">
<!--                                                    <option value="">Select</option>-->
                                                    <option value="">All</option>
                                                </select></td>
                                            <td class="col-md-2"> 
                                                <label class="control-label">Product</label>
                                                            <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $queryprod = "SELECT
                                                                                itminfo_tab.itm_id,
                                                                                itminfo_tab.itm_name
                                                                                FROM
                                                                                itminfo_tab
                                                                                WHERE
                                                                                itminfo_tab.itm_category = 1 AND
                                                                                itminfo_tab.method_type IS NOT NULL
                                                                                ORDER BY
                                                                                itminfo_tab.method_rank ASC
                                                                        ";
//query result
                                                                $rsprod = mysql_query($queryprod) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprod)) {
                                            
                                                                    if (in_array($rowprov['itm_id'], $itm_arr_request)) {
                                                                        $sel = "selected='selected'";
                                                                        $itm_name[] = $rowprov['itm_name'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                            <td class="col-md-2 hide"><label class="control-label">Indicator</label>
                                                <select name="type" id="type" required class="form-control input-sm">
                                                    <option value="all" <?php echo ($type == 'all') ? "selected='selected'" : ""; ?>>All</option>
                                                </select></td>
                                            <td class="col-md-1" style="margin-left:20px; padding-top: 20px;" valign="middle"><input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($num > 0) {
                            ?>
                            <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                 <?php
                                    if(!empty($totalWH))
                                    {
                                    ?>
                                <tr class="hide">
                                    <td>
                                        <table width="100%">
                                           
                                            <tr>
                                                <td><h4>Total Facilities: <?php echo $totalWH; ?></h4></td>
                                                <td><h4>Reported Facilities: <?php echo $reportedWH; ?></h4></td>
                                                <td>
                                                    <?php
                                                    //check type
                                                    
                                                        ?>
                                                        <h4>Reporting Rate: <?php echo round(($reportedWH / $totalWH) * 100, 2) . '%'; ?></h4>
                                                        <?php
                                                    
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                        </table>
                                    </td>
                                </tr>
                                 <?php
                                    }
                                 ?>
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(0, false);
                                                mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                mygrid.setColumnHidden(0, true);
                                             " title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
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
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    //include reports_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    
    ?>
     <script src="<?= PUBLIC_URL ?>js/bootstrap_multiselect.js"></script>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Field Stock  Availibility Report for Stakeholder(s) = $stkName Province/Region = $provinceName And Product = '".implode(' / ',$proName)."' (" . date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ") "; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,District,Health Facility,Stakeholder,Product,Average Monthly Consumption,Stock on Hand,<div style='text-align: center;'>Month of Stock</sdiv>,#cspan,#cspan");
            //mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report does not inclulde (RHS-B;MSU;Social Mobilizer;PLDs;RMPS;Hakeems;Homopaths;DDPs;TBAs;Counters)</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("50,150,*,150,100,100,120,70,50,90");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.setColAlign("center,left,left,left,left,right,right,right,center,left");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
    <script>
        $(function () {
            showDistricts('<?php echo $districtId; ?>');

            $('#stk_sel').change(function (e) {
                showProducts('');
                $('#prov_sel').html('<option value="">Select</option>');
                showProvinces('');
            });

            $('#prov_sel').change(function (e) {
//                $('#district_id').html('<option value="">Select</option>');
                showDistricts('<?php echo $districtId; ?>');
            });
        })
<?php
//check sel item
if (true) {
    ?>
            showProducts('<?php echo $selItem; ?>');
            showProvinces('<?php echo $selPro; ?>');
    <?php
}
?>
    $(function(){
        $('.multiselect-ui').multiselect({
                includeSelectAllOption: true
            });
    })    
    
    function showDistricts(dId) {
            var provId = $('#prov_sel').val();
            var stk = $('#stk_sel').val();
            if (provId != '')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    type: 'POST',
                    data: {provinceId: provId, dId: dId, validate: 'yes', allOpt: 'yes', stkId: stk},
                    success: function (data) {
                        $('#districts').html(data);
                    }
                })
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
                    //$('#item_id option:contains(Select)').text('All');
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
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1, hfProvOnly: 1},
                    success: function (data) {
                        $('#prov_sel').html(data);
                    }
                })
            }
        }
    </script>
    
</body>
<!-- END BODY -->
</html>