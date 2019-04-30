<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * new_clr
 * @package im
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
include(PUBLIC_PATH . "html/header.php");

//requisition Number
$requisitionNum = 'TEMP';
$disabled = (isset($_GET['view']) && $_GET['view'] == 1) ? 'disabled="disabled"' : '';
//year
$year = isset($_REQUEST['year']) ? mysql_real_escape_string($_REQUEST['year']) : '';
//month
$month = isset($_REQUEST['month']) ? mysql_real_escape_string($_REQUEST['month']) : '';
//requisition To 
$requisitionTo = isset($_REQUEST['wh_to']) ? mysql_real_escape_string($_REQUEST['wh_to']) : '';
//requisition From 
$requisitionFrom = isset($_REQUEST['wh_id']) ? mysql_real_escape_string($_REQUEST['wh_id']) : '';
$consumptionArr = array();
if (isset($_REQUEST['year']) && isset($_REQUEST['month'])) {
    //year
    $year = mysql_real_escape_string($_REQUEST['year']);
    //month
    $month = mysql_real_escape_string($_REQUEST['month']);
    //requisition To 
    $requisitionTo = mysql_real_escape_string($_REQUEST['wh_to']);
    //duration From 
    $durationFrom = date('Y-m-d', strtotime("+1 month", strtotime($year . '-' . $month . '-01')));
    //duration to
    $durationTo = date('Y-m-d', strtotime("-1 day", strtotime("+3 month", strtotime($durationFrom))));
    //duration
    $duration = date('M-Y', strtotime($durationFrom)) . ' to ' . date('M-Y', strtotime($durationTo));
}
if (isset($_POST['submit'])) {
    
   //echo '<pre>';print_r($_REQUEST);exit;
   if(empty($_POST['chk'])){
        $url = 'new_clr_by_prov.php?month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'&wh_to='.$_REQUEST['wh_id'];
        echo "<script>window.location='$url&err=2'</script>";
   }
    //select query
    //gets
    // Requisition Number
    $qry = mysql_fetch_array(mysql_query("SELECT
                                                MAX(clr_master.requisition_num) AS requisition_num
                                        FROM
                                                clr_master"));
    if (empty($qry['requisition_num'])) {
        $requisitionNum = 'RQ' . date('ym') . str_pad(1, 4, 0, STR_PAD_LEFT);
    } else {
        $requisitionNum = 'RQ' . date('ym') . str_pad((substr($qry['requisition_num'], 6) + 1), 4, 0, STR_PAD_LEFT);
    }
    
    $checked_items = implode(',',$_POST['chk']);
    //select query
    //Check if CLR-6 is already saved
    $check_q= "SELECT
                        COUNT(clr_master.requisition_num) AS Num
                FROM
                        clr_master
                INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                WHERE
                        clr_master.wh_id = " . $requisitionFrom . "
                AND clr_master.date_to = '" . $_POST['date_to'] . "' 
                AND clr_master.approval_status='Pending'  
                AND clr_details.itm_id IN ($checked_items) ";
    //echo $check_q;exit;
    $qry = mysql_fetch_array(mysql_query($check_q));
    
    if ($qry['Num'] == 0) {
        //insert query
        //inserts
        //requisition num
        //requisition to
        //warehouse id
        //date from
        //date to
        //requested by
        //requested on
        $qry = "INSERT INTO clr_master
                SET
                        requisition_num = '" . $requisitionNum . "',
                        requisition_to = '" . $_POST['requisition_to'] . "',
                        wh_id = '" . $_POST['wh_id'] . "',
                        stk_id = '" . $_POST['stkId'] . "',
                        date_from = '" . $_POST['date_from'] . "',
                        date_to = '" . $_POST['date_to'] . "',
                        requested_by = '" . $_POST['requested_by'] . "',
                        requested_on = NOW()";
        mysql_query($qry);
        $lastInsId = mysql_insert_id();
        
        //inserting in clr_master_log
        $qry2 = "INSERT INTO clr_master_log
                SET
                        master_id = '" . $lastInsId . "',
                        requisition_to = '" . $_POST['requisition_to'] . "',
                        wh_id = '" . $_POST['wh_id'] . "',
                        requested_by = '" . $_POST['requested_by'] . "',
                        log_timestamp = NOW(),
                        approval_status = 'Pending',
                        user_id = '" . $_SESSION['user_id'] . "',
                        approval_level = 'dist_lvl1' ";
        mysql_query($qry2);
       

        for ($i = 0; $i < count($_POST['itm_id']); $i++) {
            
            //insert query
            //inserts
            //pk master id
            //item id
            //avg consumption
            //soh district
            //soh field
            //total stock
            //desired stock
            //replenishment
            //qty_req_dist_lvl1
            //remarks_dist_lvl1
            //
            if(in_array($_POST['itm_id'][$i], $_POST['chk']))
            {
                
            
                 $qry = "INSERT INTO clr_details
                    SET
                            pk_master_id = '" . $lastInsId . "',
                            itm_id = '" . $_POST['itm_id'][$i] . "',
                            avg_consumption = '" . ((!empty($_POST['avg_consumption'][$i]))?$_POST['avg_consumption'][$i]:'0') . "',
                            soh_dist = '" . ((!empty($_POST['soh_dist'][$i])?$_POST['soh_dist'][$i]:'0')) . "',
                            soh_field = '" . ((!empty($_POST['soh_field'][$i])?$_POST['soh_field'][$i]:'0')) . "',
                            total_stock = '" . ((!empty($_POST['total_stock'][$i])?$_POST['total_stock'][$i]:'0')) . "',
                            desired_stock = '" . ((!empty($_POST['desired_stock'][$i])?$_POST['desired_stock'][$i]:'0')) . "',
                            replenishment = '" . ((!empty($_POST['replenishment'][$i])?$_POST['replenishment'][$i]:'0')) . "',
                            qty_req_dist_lvl1 = '" . str_replace(',','',$_POST['quantity_requested'][$i]) . "',
                            sale_of_last_3_months = '" . str_replace(',','',((!empty($_POST['sale_of_last_3_months'][$i])?$_POST['sale_of_last_3_months'][$i]:'0'))) . "',
                            sale_of_last_month = '" . str_replace(',','',((!empty($_POST['sale_of_last_month'][$i]))?$_POST['sale_of_last_month'][$i]:'0')) . "',
                            remarks_dist_lvl1 = '" . $_POST['remarks'][$i] . "' ";
                mysql_query($qry);
            }
        }
        $temp_a = explode('-',$_POST['date_to']);
        $temp_y = $temp_a[0];
        $temp_m = $temp_a[1];
        if(!empty($_REQUEST['redirect_to'])) $page = $_REQUEST['redirect_to'];
        else $page = 'clr_all_district_approval';
        
        $url = ($_SESSION['user_level'] == 2) ? $page.'.php?month='.$temp_m.'&year='.$temp_y : 'clr6_list.php';
        echo "<script>window.location='$url&e=1'</script>";
    } else {
        
        $url = 'new_clr_by_prov.php?' . $_SERVER['QUERY_STRING'];
        echo "<script>window.location='$url&err=0'</script>";
    }
}

/*	
$itemIds[0] = 1;
$itemIds[1] = 2;
$itemIds[2] = 9;
$itemIds[3] = 3;
$itemIds[4] = 5;
$itemIds[5] = 4;
$itemIds[6] = 6;
$itemIds[7] = 7;
$itemIds[8] = 8;
$itemIds[9] = 13;
*/


//select query
//gets
//user district 
//and province
//main stakeholder
//if (isset($_REQUEST['year']) && isset($_REQUEST['month'])) {
if (isset($_REQUEST['district'])) {
    if ($_SESSION['user_level'] == 2) {
        $wh_id = $_REQUEST['district'];
    } else {
        $wh_id = $_SESSION['user_warehouse'];
    }
    $qry = "SELECT
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_locations.LocName,
				stakeholder.stkname AS MainStk
			FROM
				tbl_warehouse
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
			WHERE
				tbl_warehouse.wh_id = " . $wh_id . "
			LIMIT 1 ";
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //district id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder id
    $stkid = $qryRes['stkid'];
    //district name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];
}
$show_part_a = false;
if($stkid == 1 && $provId == 4){
    $show_part_a = true;
}
?>
<script>
    function printContents() {
        var w = 900;
        var h = screen.height;
        var left = Number((screen.width / 2) - (w / 2));
        var top = Number((screen.height / 2) - (h / 2));
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,left=" + left + ",top=" + top + ",width=" + w + ",height=" + h;
        var printingContents = document.getElementById("printing").innerHTML;
        var docprint = window.open("", "", dispSetting);
        docprint.document.open();
        docprint.document.write('<html><head><title>CLR6</title>');
        docprint.document.write('</head><body onLoad="self.print(); "><center>');
        docprint.document.write(printingContents);
        docprint.document.write('</center></body></html>');
        docprint.document.close();
        docprint.focus();
    }
</script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="init()">
    <div id="loading" style="position:absolute; width:100%; text-align:center; top:300px;"> <img src="../../plmis_img/ajax-loader1.gif" border=3></div>
    <script>
        var ld = (document.all);
        var ns4 = document.layers;
        var ns6 = document.getElementById && !document.all;
        var ie4 = document.all;
        if (ns4)
            ld = document.loading;
        else if (ns6)
            ld = document.getElementById("loading").style;
        else if (ie4)
            ld = document.all.loading.style;

        function init() {
            if (ns4)
            {
                ld.visibility = "hidden";
            } else if (ns6 || ie4)
                ld.display = "none";
        }
    </script> 
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include tio_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">New Requisition</h3>
                                </div>
                                <div class="widget-body">
                                    <form name="frm" id="frm" action="" method="get">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php if ($_SESSION['user_level'] == 2) { ?>
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label>District</label>
                                                            <div class="controls">
                                                                <select name="district" id="district" required="required" class="form-control input-medium">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    $qry = "SELECT DISTINCT
                                                                                    tbl_warehouse.wh_id,
                                                                                    tbl_locations.LocName
                                                                            FROM
                                                                                    tbl_locations
                                                                            INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.dist_id
                                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                            WHERE
                                                                                    tbl_locations.ParentID = " . $_SESSION['user_province1'] . "
                                                                            AND tbl_warehouse.stkid = " . $_SESSION['user_stakeholder1'] . "
                                                                            AND stakeholder.lvl = 3
                                                                            ORDER BY
                                                                                    tbl_locations.LocName ASC";
                                                                    $qryRes = mysql_query($qry);
                                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                                        if ($wh_id == $row['wh_id']) {
                                                                            $sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        //populate month combo
                                                                        ?>
                                                                        <option value="<?php echo $row['wh_id']; ?>"<?php echo $sel; ?> ><?php echo $row['LocName']; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="col-md-2">
                                                    <div class="control-group">
                                                        <label>Year</label>
                                                        <div class="controls">
                                                            <select name="year" id="year" required="required" onchange="calc_to_month()"  class="form-control input-small">
                                                                <option value="">Select</option>
                                                                <?php
                                                                for ($i = date('Y'); $i >= 2016; $i--) {
                                                                    $sel = ($year == $i) ? 'selected="selected"' : '';
                                                                    //populate year year
                                                                    echo "<option value=\"$i\" $sel>$i</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2">
                                                    <div class="control-group">
                                                        <label>Ending Month</label>
                                                        <div class="controls">
                                                            <select name="month" id="month" required="required" onchange="calc_to_month()"  class="form-control input-small">
                                                                <option value="">Select</option>
                                                                <?php
                                                                for ($i = 1; $i <= 12; $i++) {
                                                                    if ($month == $i) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //populate month combo
                                                                    ?>
                                                                    <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 ">
                                                    <div class="control-group">
                                                        <label>Requisition Period</label>
                                                        <div id="to_month_div">
                                                            <?=(!empty($duration)?$duration:'')?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 hide">
                                                    <div class="control-group">
                                                        <label>Requisition To</label>
                                                        <div class="controls">
                                                            <select name="wh_to" id="wh_to" required="required" class="form-control input-medium">
                                                                <?php
//select query
//gets
//warehouse id 
//warehouse name
                                                                $qry = "SELECT
                                                                            tbl_warehouse.wh_id,
                                                                            tbl_warehouse.wh_name
                                                                        FROM
                                                                            stakeholder
                                                                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                                        WHERE
                                                                            stakeholder.ParentID IS NULL
                                                                        AND stakeholder.stk_type_id = 0
                                                                        AND stakeholder.lvl = 1
                                                                        AND tbl_warehouse.prov_id = 10
                                                                        AND tbl_warehouse.stkid = 1
                                                                        ORDER BY
                                                                            tbl_warehouse.wh_name ASC";
//query result
                                                                $qryRes = mysql_query($qry);
//fetch result
                                                                while ($row = mysql_fetch_array($qryRes)) {
                                                                    $sel = ($requisitionTo == $row['wh_id']) ? 'selected="selected"' : '';
                                                                    echo "<option value=\"$row[wh_id]\" $sel>$row[wh_name]</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if (!isset($_GET['view'])) {
                                                    ?>
                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label>&nbsp;</label>
                                                            <div class="controls">
                                                                <input type="submit" id="submit" value="Create" class="btn btn-primary" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="redirect_to" value="<?=(isset($_REQUEST['redirect_to'])?$_REQUEST['redirect_to']:'')?>">
                                    </form>
                                </div>
                            </div>
                            <?php
                            if (isset($_REQUEST['district']) ) {
                                //year
                                $year = mysql_real_escape_string($_REQUEST['year']);
                                //month
                                $month = mysql_real_escape_string($_REQUEST['month']);
                                //requisition To 
                                $requisitionTo = mysql_real_escape_string($_REQUEST['wh_to']);
                                //duration From 
                                $durationFrom = date('Y-m-d', strtotime("+1 month", strtotime($year . '-' . $month . '-01')));
                                //duration to
                                $durationTo = date('Y-m-d', strtotime("-1 day", strtotime("+3 month", strtotime($durationFrom))));
                                //duration
                                $duration = date('M-Y', strtotime($durationFrom)) . ' to ' . date('M-Y', strtotime($durationTo));
                                //reporting Date 
                                $reportingDate = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . '-01';
                                //select query
                                //gets
                                //item id,
                                //itmrec_id,
                                //item name,
                                //item type,
                                //method type,
                                //generic name,
                                //frm index,
                                //Consumption,
                                //SOH District,
                                //SOH Field
                                  $qry = "SELECT
                                                itminfo_tab.itm_id,
                                                itminfo_tab.itmrec_id,
                                                itminfo_tab.itm_name,
                                                itminfo_tab.itm_type,
                                                itminfo_tab.method_type,
                                                itminfo_tab.generic_name,
                                                itminfo_tab.frmindex,
                                                SUM(IF(stakeholder.lvl = 4 AND tbl_wh_data.RptDate = '$reportingDate',tbl_wh_data.wh_issue_up,0)) AS cons_last_mon,
                                                SUM(IF(stakeholder.lvl = 4, tbl_wh_data.wh_issue_up, 0)) AS Consumption,
                                                SUM(IF(stakeholder.lvl = 3 AND tbl_wh_data.RptDate = '$reportingDate', tbl_wh_data.wh_cbl_a, 0)) AS SOHDistrict,
                                                SUM(IF(stakeholder.lvl = 4 AND tbl_wh_data.RptDate = '$reportingDate', tbl_wh_data.wh_cbl_a, 0)) AS SOHField
                                        FROM
                                                tbl_warehouse
                                        INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
                                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                        INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                                        WHERE
                                                tbl_warehouse.dist_id = $distId
                                        AND tbl_warehouse.stkid = $stkid
                                        AND tbl_wh_data.RptDate BETWEEN DATE_ADD('$reportingDate', INTERVAL -2 MONTH) AND '$reportingDate'
                                        AND stakeholder.lvl IN (3, 4)
                                        AND itminfo_tab.itm_category = 1
                                        GROUP BY
                                                itminfo_tab.itm_id
                                        ORDER BY
                                                itminfo_tab.frmindex ASC";
                                //query result
                                  //echo $qry;
                                $qryRes = mysql_query($qry);
                                //number of record
                                $num = mysql_num_rows($qryRes);
                                //chech if record exists
                                if ($num > 0 || true) {
                                    //fetch results
                                    while ($row = mysql_fetch_array($qryRes)) {
                                        //item ids
                                        //$itemIds[] = $row['itm_id'];
                                        //$itm_name_id[$row['itm_name']] =  $row['itm_id'];
                                        //$product[$row['method_type']][] = $row['itm_name'];
                                        if ($row['itm_id'] == 8) {
                                            //consumption Array
                                            $consumptionArr[$row['itm_id']] = '';
                                            $cons_last_mon[$row['itm_id']] = '';
                                            
                                            //SOH District Array
                                            $SOHDistrictArr[$row['itm_id']] = '';
                                            //SOH Field Array
                                            $SOHFieldArr[$row['itm_id']] = '';
                                        } else {
                                            //consumption Array
                                            $consumptionArr[$row['itm_id']] = (!empty($row['Consumption'])) ? round($row['Consumption']) : 0;
                                            $cons_last_mon [$row['itm_id']] = (!empty($row['cons_last_mon'])) ? round($row['cons_last_mon']) : 0;;
                                            //SOH District Array
                                            $SOHDistrictArr[$row['itm_id']] = (!empty($row['SOHDistrict'])) ? round($row['SOHDistrict']) : 0;
                                            //SOH Field Array
                                            $SOHFieldArr[$row['itm_id']] = (!empty($row['SOHField'])) ? round($row['SOHField']) : 0;
                                        }

                                        if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
                                            $methodType[$row['method_type']]['rowspan'] = 2;
                                        } else {
                                            $genericName[$row['generic_name']][] = $row['itm_name'];
                                        }
                                    }
                                    
                                    $qry_itm = "SELECT
                                                itminfo_tab.itm_name,
                                                stakeholder_item.stkid,
                                                itminfo_tab.method_type,
                                                itminfo_tab.itm_id,
                                                itminfo_tab.itmrec_id
                                                FROM
                                                        stakeholder_item
                                                INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                        stakeholder_item.stkid = ".$_SESSION['user_stakeholder1']."
                                                            AND itm_category = 1
                                                ORDER BY
                                                        itminfo_tab.method_rank  ASC,
                                                        itminfo_tab.itm_id ASC
";
                                    $res= mysql_query($qry_itm);
                                    $itm_name_id=$product=$itemIds =array();
                                    //print_r($_SESSION);
                                    while($row= mysql_fetch_assoc($res))
                                    {
                                        $itm_name_id[$row['itm_name']] = $row['itm_id'];
                                        $product[$row['method_type']][] = $row['itm_name'];
                                        $itemIds[] = $row['itm_id'];
                                    }

                                    if(!empty($SOHFieldArr))
                                    ksort($SOHFieldArr);
                                    //echo '<pre>';print_r($itemIds);print_r($SOHFieldArr);print_r($itm_name_id);print_r($product);exit;
                                    ?>
                                    <br />
                                    <div id="printing" style="clear:both;margin-top:20px;">
                                        <div style="margin-left:0px !important; width:100% !important;">
                                            <style>
                                                table#myTable{margin-top:20px;border-collapse: collapse;border-spacing: 0; border:1px solid #999;}
                                                table#myTable tr td{font-size:11px;padding:3px; text-align:left; border:1px solid #999;}
                                                table#myTable tr th{font-size:11px;padding:3px; text-align:center; border:1px solid #999;}
                                                table#myTable tr td.TAR{text-align:right; padding:5px;width:50px !important;}
                                                .sb1NormalFont {
                                                    color: #444444;
                                                    font-size: 11px;
                                                    font-weight: bold;
                                                    text-decoration: none;
                                                }
                                               
                                                p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important;}
                                                table#headerTable tr td{ font-size:11px;}
                                                
                                                
                                                /* Print styles */
                                                @media only print
                                                {
                                                    table#myTable tr th{font-size:8px;padding:3px !important; text-align:center; border:1px solid #999;}
                                                    table#myTable tr td{font-size:8px;padding:3px !important; text-align:left; border:1px solid #999;}
                                                    .cls_print_input{width:inherit}
                                                    .remarks_box{width:inherit}
                                                    #desc{width:500px !important;}
                                                    #doNotPrint{display: none !important;}
                                                    
                                                }
                                            </style>
                                            <p style="color: #000000; font-size: 20px;text-align:center"><b><u>Contraceptive Requisition Form</u></b><span style="float:right; font-weight:normal;">CLR-6</span></p>
                                            <p style="text-align:center;margin-right:35px;">(<?php echo "For $mainStk District $distName"; ?>)</p>
                                            <table width="200" id="headerTable" align="right">
                                                <tr>
                                                    <td align="left"><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 20px;">For: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $duration; ?></span> </p></td>
                                                </tr>
                                                <tr>
                                                    <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 75px;">Requisition No: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requisitionNum; ?></span> </p></td>
                                                </tr>
                                                <tr>
                                                    <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 84px;">Requisition Date: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo date('d/m/Y'); ?></span> </p></td>
                                                </tr>
                                            </table>
                                            <div style="clear:both;"></div>
                                            <form name="frm" id="frm" method="post" action="">
                                                <table width="100%" id="myTable" cellspacing="0" align="center">
                                                    <thead>
                                                        <tr>
                                                            <td rowspan="2" width="2%" style="text-align:center;">S. No.</td>
                                                            <td rowspan="2" width="18%" id="desc">Description</td>
                                                            <?php
                                                            foreach ($product as $proType => $proNames) {
                                                                echo "<td width=\"6%\" style=\"text-align:center !important;\" colspan=" . sizeof($proNames) . ">$proType</td>";
                                                            }
                                                            ?>
                                                            <td rowspan="2" style="width:8%;">Remarks</td>
                                                        </tr>
                                                        <tr>
                                                            <?php
                                                            //echo '<pre>';print_r($itemIds);
                                                            //print_r($product);
                                                            $col = '';
                                                            $itm2 =array();
                                                            foreach ($product as $proType => $proNames) {
                                                                foreach ($proNames as $name) {
                                                                    $names[] = $name;
                                                                   
                                                                    echo '<td  class=" td_chk" data-itm-id="'.$itm_name_id[$name].'"  style="text-align:center"> <input type="checkbox" checked="checked" class="prod_chk" id="chk_'.$itm_name_id[$name].'" name="chk['.$itm_name_id[$name].']" value="'.$itm_name_id[$name].'"><br> '.$name.'</td>';
                                                                    $col .= "<td>&nbsp;</td>";
                                                                }
                                                            }
                                                            //echo '<pre>';print_r($names);exit;
                                                            ?>
                                                        </tr>
                                                    </thead>
                                                    <?php
                                                    foreach ($itemIds as $itemId) {
                                                        ?>
                                                        <input type="hidden" name="itm_id[]" value="<?php echo $itemId; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <tbody>
                                                        <?php
                                                        $colNum = 1;
                                                        if ($show_part_a) {
                                                            ?>
                                                            <tr height="30">
                                                                <td colspan="<?php echo count($itemIds) + 3; ?>">Part - A (District Population Welfare Office - DPWO)</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                                <td>Quarterly Sale on the basis of last 3 months consumption</td>
                                                                <?php
                                                                 foreach ($itemIds as $k => $itm) {
                                                                   
                                                                        $consumption = (!empty($consumptionArr[$itm])?$consumptionArr[$itm]:'');
                                                                        echo "<td class=\"TAR td_chk\" data-itm-id=\"$itm\" >" . ($consumption != 0 ? number_format($consumption) : '0') . "</td>";
                                                                        echo '<input type="hidden" name="sale_of_last_3_months[]" value="'.$consumption.'">';
                                                                    }
                                                                ?>
                                                                <td>&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                                <td>Sale/Use last month</td>
                                                                <?php
                                                                foreach ($itemIds as $k => $itm) {
                                                                        $consumption = (!empty($cons_last_mon[$itm])?$cons_last_mon[$itm]:'');
                                                                        echo "<td class=\"TAR td_chk\" data-itm-id=\"$itm\">" . ($consumption != 0 ? number_format($consumption) : '0') . "</td>";
                                                                        echo '<input type="hidden" name="sale_of_last_month[]" value="'.$consumption.'">';
                                                                    }
                                                                ?>
                                                                <td>&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                                <td>Amount of sales proceeds deposited in bank/treasury (Attached original paid challan)</td>
                                                                <td colspan="<?php echo count($itemIds) + 1; ?>">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                                <td>Bank/Treasury challan no. & Date</td>
                                                                <td colspan="<?php echo count($itemIds) + 1; ?>">&nbsp;</td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <tr height="30">
                                                            <td colspan="<?php echo count($itemIds) + 3; ?>">Part - <?php echo ($show_part_a) ? 'B' : 'A'; ?> (To be filled by Requester)</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;">A-1</td>
                                                            <td>Consumption During Last Quarter</td>
                                                            <?php
                                                            
                                                                foreach ($itemIds as $k => $itm) {
                                                                   
                                                                        $consumption = (!empty($consumptionArr[$itm])?$consumptionArr[$itm]:'');
                                                                echo "<td class=\"TAR td_chk\" data-itm-id=\"$itm\">" . ($consumption != 0 ? number_format($consumption) : '0') . "</td>";
                                                                ?>
                                                        <input type="hidden" name="avg_consumption[]" value="<?php echo $consumption; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-2</td>
                                                        <td>Stock at the end of last quarter at district Store</td>
                                                        <?php
                                                        
                                                                foreach ($itemIds as $k => $itm) {
                                                                   
                                                                        $SOHDistrict = (!empty($SOHDistrictArr[$itm])?$SOHDistrictArr[$itm]:'');
                                                            echo "<td class=\"TAR td_chk\" data-itm-id=\"$itm\">" . ($SOHDistrict != 0 ? number_format($SOHDistrict) : '0') . "</td>";
                                                            ?>
                                                        <input type="hidden" name="soh_dist[]" value="<?php echo $SOHDistrict; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-3</td>
                                                        <td>Stock at the end of last quarter at Service Delivery Points</td>
                                                        <?php
                                                        
                                                                foreach ($itemIds as $k => $itm) {
                                                                   
                                                                        $SOHField = (!empty($SOHFieldArr[$itm])?$SOHFieldArr[$itm]:'');
                                                            echo "<td class=\"TAR td_chk\" data-itm-id=\"$itm\">" . ($SOHField != 0 ? number_format($SOHField) : '0') . "</td>";
                                                            ?>
                                                        <input type="hidden" name="soh_field[]" value="<?php echo $SOHField; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-4</td>
                                                        <td>Total Stock Available (A2+A3)</td>
                                                        <?php
                                                        foreach ($itemIds as $itemId) {
                                                            if(empty($SOHFieldArr[$itemId]))    $SOHFieldArr[$itemId]=0;
                                                            if(empty($SOHDistrictArr[$itemId])) $SOHDistrictArr[$itemId] = 0;
                                                            
                                                            echo "<td class=\"TAR td_chk\" data-itm-id=\"$itemId\">" . ((strlen($SOHFieldArr[$itemId]) > 0) ? number_format($SOHFieldArr[$itemId] + $SOHDistrictArr[$itemId]) : '') . "</td>";
                                                            ?>
                                                        <input type="hidden" name="total_stock[]" value="<?php echo $SOHFieldArr[$itemId] + $SOHDistrictArr[$itemId]; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-5</td>
                                                        <td>Desired stock level for 2 quarters (A1x2)</td>
                                                        <?php
                                                        foreach ($itemIds as $itemId) {
                                                            $des_two_q = (!empty($consumptionArr[$itemId])?$consumptionArr[$itemId]:'');
                                                            echo "<td class=\"TAR td_chk\" data-itm-id=\"$itemId\">" . ( (strlen($des_two_q) > 0) ? number_format($des_two_q * 2) : '' ) . "</td>";
                                                            ?>
                                                        <input type="hidden" name="desired_stock[]" value="<?php echo $des_two_q * 2; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-6</td>
                                                        <td>Replenishment Requested (A5-A4)</td>
                                                        <?php
                                                        foreach ($itemIds as $itemId) {
                                                            $cons_a6=(!empty($consumptionArr[$itemId])?$consumptionArr[$itemId]:'0');
                                                            $a6 = ($cons_a6 * 2) - ($SOHFieldArr[$itemId] + $SOHDistrictArr[$itemId]);
                                                            $a6 = ($a6 > 0) ? $a6 : 0;
                                                            echo "<td class=\"TAR td_chk\" data-itm-id=\"$itemId\">" . ( (strlen($cons_a6) > 0) ? number_format($a6) : '' ) . "</td>";
                                                            ?>
                                                        <input type="hidden" name="replenishment[]" value="<?php echo $a6; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td style="text-align:center;">A-7</td>
                                                        <td>Quantity Actually Required</td>
                                                        <?php
                                                        foreach ($itemIds as $itemId) 
                                                        {
                                                            $cons_a7 = (!empty($consumptionArr[$itemId])?$consumptionArr[$itemId]:'0');
                                                            $a6 = ( $cons_a7 * 2) - ($SOHFieldArr[$itemId] + $SOHDistrictArr[$itemId]);
                                                            $a6 = ($a6 > 0) ? $a6 : 0;
                                                            echo '<td class="TAR td_chk\" data-itm-id=\"$itm\"><input type="" style="font-size: 11px;padding:1px 1px !important;" class="form-control input-sm qty cls_print_input" data-id="'.$itemId.'" step="1" min="0" name="quantity_requested[]" value="'.$a6.'" data-orig-val="'.$a6.'" /></td>';    
                                                        }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;">A-8</td>
                                                        <td>Remarks</td>
                                                        <?php
                                                        foreach ($itemIds as $itemId) 
                                                        {
                                                            echo '<td class="TAR td_chk cls_print_input\" data-itm-id=\"$itm\"><textarea rows="2" cols="10" class="form-control  input-sm remarks_box" data-id="'.$itemId.'" name="remarks[]"></textarea><span id="msg_'.$itemId.'" class="red"></span></td>';    
                                                        }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                    </tr>
                                                    
                                                    <tr height="30">
                                                        <td colspan="<?php echo count($itemIds) + 3; ?>">Part - <?php echo ($show_part_a) ? 'C' : 'B'; ?> (To be filled at warehouse)</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                        <td>Quantity Approved</td>
                                                        <?php echo $col; ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                        <td>Relevant Issue Voucher</td>
                                                        <?php echo $col; ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr id="doNotPrint">
                                                        <td colspan="<?php echo count($itemIds) + 3; ?>" style="text-align:right; border:none; padding-top:15px;"><input type="hidden"  name="date_from" value="<?php echo $durationFrom; ?>" />
                                                            <input type="hidden"  name="date_to" value="<?php echo $durationTo; ?>" />
                                                            <input type="hidden"  name="requisition_to" value="<?php echo $requisitionTo; ?>" />
                                                            <input type="hidden"  name="wh_id" value="<?php echo ($_SESSION['user_level'] == 2) ? $wh_id : $_SESSION['user_warehouse']; ?>" />
                                                            <input type="hidden"  name="requested_by" value="<?php echo $_SESSION['user_id']; ?>" />
                                                            <input type="hidden"  name="stkId" value="<?php echo $_SESSION['user_stakeholder1']; ?>" />
                                                            <input type="submit" name="submit" value="Save" class="btn btn-primary" />
                                                            <input type="button" onClick="printContents()" value="Print" class="btn btn-warning" /></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </form>
                                            <table width="100%">
                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;" width="10%" class="sb1NormalFont">Name:</td>
                                                    <td width="40%">__________________________</td>
                                                    <td width="30%" style="text-align:right;" class="sb1NormalFont">Signature:</td>
                                                    <td width="20%">__________________________</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;" class="sb1NormalFont">Designation:</td>
                                                    <td>__________________________</td>
                                                    <td style="text-align:right;" class="sb1NormalFont">Date:</td>
                                                    <td>__________________________</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo "No record found.";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END FOOTER -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <?php
    if (isset($_REQUEST['err']) && $_REQUEST['err'] == '0') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'CLR-6 of the items you selected already exists. Either edit OR delete the existing CLR',
                type: 'error',
                layout: self.data('layout')
            });
            
        </script>
    <?php }
    if (isset($_REQUEST['err']) && $_REQUEST['err'] == '2') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Please select atleast one product to create CLR-6.',
                type: 'error',
                layout: self.data('layout')
            });
            
        </script>
    <?php }
    ?>
 
        <script>
            
            $(function() {
                $('.qty').priceFormat({
                    prefix: '',
                    thousandsSeparator: ',',
                    suffix: '',
                    centsLimit: 0,
                    limit: 10,
                    clearOnEmpty: false
                });
            })
            
            $('.qty').change(function(){
                //to make the remarks required , when the quantity is changed
                var orig_val=$(this).data('orig-val');
                var now_val = $(this).val();
                var a=$(this).data('id');
                if(now_val != orig_val)
                {
                    $('.remarks_box[data-id='+a+']').attr('required','required');
                    $('#msg_'+a).html('Required');
                }
                else
                {
                    $('.remarks_box[data-id='+a+']').attr('required',false);
                    $('#msg_'+a).html('');
                }
            });
            
            $('.prod_chk').click(function(){
                var v = $(this).val();
                var ch = $(this).attr('checked');
                if(ch == 'checked'){
                    $('.td_chk[data-itm-id='+v+']').attr('bgcolor','');
                    
                    $('.qty[data-id='+v+']').attr('readonly',false);
                    $('.remarks_box[data-id='+v+']').attr('readonly',false);
                }
                else
                {
                    $('.td_chk[data-itm-id='+v+']').attr('bgcolor','#eeeeee');
                    
                    $('.qty[data-id='+v+']').attr('readonly',true);
                    $('.qty[data-id='+v+']').val('0');
                    $('.remarks_box[data-id='+v+']').attr('readonly',true);
                    $('.remarks_box[data-id='+v+']').attr('required',false);
                }
                //alert('value:'+v+',cehck:'+ch);
            });
            
            
            function calc_to_month(){
                
                var a = $('#year').val() + '-' +$('#month').val() +'-01';
                var d = new Date( a );
                var months = [ "January", "February", "March", "April", "May", "June", 
                             "July", "August", "September", "October", "November", "December" ];
                
                d.setMonth( d.getMonth( ) + 1 );
                var m2 = d.getMonth( ) ;
                var s2 = months[m2];
                var y = s2 + ' - ' + d.getFullYear( );
                
                d.setMonth( d.getMonth( ) + 2 );
                var m = d.getMonth( ) ;
              
                var s = months[m];
                var z = s + ' - ' + d.getFullYear( );
                
                console.log(y +' to '+ z);
                $('#to_month_div').html(y +' to '+ z);
            }
            
        </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>