<?php
/**
 * sale+proceeds
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
ini_set('max_execution_time', 300);
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header 
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_SESSION);exit;
//flag is_provincial_user 
$is_provincial_user = false;
// If Provincial User
if ($_SESSION['user_level'] == 2) {
//set flag is_provincial_user true	
    $is_provincial_user = true;
    //set province id
    $prov_id = $_SESSION['user_province1'];
}
//if submitted
if (isset($_POST['submit'])) {
    $pricing = isset($_POST['pricing'])?$_POST['pricing']:'';
    //get selected month
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //get selected year
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //get reporting date 
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    //get prev month 
    $prevMonth = date('Y-m-d', strtotime('-1 Month', strtotime($reportingDate)));
    //get corr month 
    $corrMonth = date('Y-m-d', strtotime('-12 Month', strtotime($reportingDate)));
    //get stakeholder 
    $stakeholder = 1;
    //check district
    if (isset($_POST['district'])) {
        //get district Id 
        $districtId = mysql_real_escape_string($_POST['district']);
    } else {
        //get district Id 
        $districtId = $_SESSION['user_district'];
    }
    //select query
    // Get 
    // District 
    // and Province name
    $qry = "SELECT
				tbl_locations.PkLocID,
				tbl_locations.LocName,
				tbl_locations.ParentID AS prov_id
			FROM
				tbl_locations
			WHERE
				tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //province id
    $provId = $row['prov_id'];
    //distrct name 
    $distrctName = $row['LocName'];
    //file name 
    $fileName = 'Sale-Proceeds_' . $distrctName . '_for_' . date('M-Y', strtotime($reportingDate));
} else {
    if (date('d') > 10) {
        //set selected month
        $selMonth = date('m', strtotime("-1 month", strtotime(date('Y-m'))));
        //set selected year
        $selYear = date('Y', strtotime("-1 month", strtotime(date('Y-m'))));
    } else {
        //set selected month
        $selMonth = date('m', strtotime("-2 month", strtotime(date('Y-m'))));
        //set selected year
        $selYear = date('Y', strtotime("-2 month", strtotime(date('Y-m'))));
    }
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
////include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Sale Proceeds of Contraceptives</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="row">               
                                        <div class="col-md-12">
                                            <?php if ($is_provincial_user) { ?>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">District</label>
                                                        <div class="form-group">
                                                            <select name="district" id="district" class="form-control input-sm" required>
                                                                <option value="">Select</option>
                                                                <?php
                                                                $and = '';
                                                                if ($_SESSION['userdata'][6] != 'SG-020') {
                                                                    $and = " AND tbl_locations.ParentID = $prov_id";
                                                                }
                                                                //select query
                                                                //gets
                                                                //pk location id
                                                                //location name
                                                                $qry = "SELECT
                                                                                    tbl_locations.PkLocID,
                                                                                    tbl_locations.LocName
                                                                            FROM
                                                                                    tbl_locations
                                                                            WHERE
                                                                                    tbl_locations.LocLvl = 3
                                                                                    $and
                                                                            ORDER BY
                                                                                    tbl_locations.LocName ASC";
                                                                //query result
                                                                $qryRes = mysql_query($qry);
                                                                //fetch result
                                                                while ($row = mysql_fetch_array($qryRes)) {
                                                                    $sel = ($districtId == $row['PkLocID']) ? 'selected="selected"' : '';
                                                                    echo "<option value=\"$row[PkLocID]\" $sel>$row[LocName]</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="form-group">
                                                        <select name="month_sel" id="month_sel" class="form-control input-sm" required>
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
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
                                                <div class="form-group">
                                                    <label class="control-label">Year</label>
                                                    <div class="form-group">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm" required>
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
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Pricing Date</label>
                                                    <div class="form-group">
                                                        <select name="pricing" id="pricing" class="form-control input-sm" required>
                                                            <?php
                                                            $qry = "SELECT
                                                                        distinct item_price.date_from
                                                                    FROM
                                                                        item_price
                                                                    order by 
                                                                        item_price.date_from
                                                                    ";
                                                            //query result
                                                            $qryRes = mysql_query($qry);
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $sel = ($pricing == $row['date_from']) ? 'selected="selected"' : '';
                                                                echo '<option value="'.$row[date_from].'" '.$sel.'>'.date('jS M Y',strtotime($row[date_from])).'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
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
                <?php
                if (isset($_POST['submit'])) {
                    
                    $qry3 = "SELECT
                                item_price.date_from,
                                item_price.pk_id,
                                item_price.item_id,
                                item_price.stakeholder_id,
                                item_price.province_id,
                                item_price.price,
                                item_price.qty,
                                item_price.date_to
                            FROM
                                item_price
                            ORDER BY 
                                item_price.date_from,
                                item_price.province_id,
                                item_price.stakeholder_id,
                                item_price.item_id
                            ";
                    //query result
                    //echo $qry;exit;
                    $qryRes3 = mysql_query($qry3);
                    //if result exists
                    $pricing_arr = array();
                    while($row= mysql_fetch_assoc($qryRes3))
                    {
                        $pricing_arr[$row['date_from']][$row['province_id']][$row['stakeholder_id']][$row['item_id']] = $row['price'];
                    }
                    //echo '<pre>';print_r($pricing_arr);exit;
                    //exit;
                    //select query
                    //gets
                    //curr month
                    //prev month
                    //corr month
                    $qry = "SELECT
                                    tbl_hf_data.item_id,
                                    itminfo_tab.itm_name,
                                    SUM(tbl_hf_data.issue_balance) AS currMonth
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            WHERE
                                    tbl_warehouse.dist_id = $districtId
                                    AND tbl_warehouse.stkid = $stakeholder
                                    AND tbl_warehouse.prov_id = $provId
                                    AND tbl_hf_data.reporting_date = '$reportingDate'
                                    AND itminfo_tab.itm_category = 1
                            GROUP BY tbl_hf_data.item_id";
                    //query result
                    //echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    $display_arr = array();
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                        $display_arr[$row['item_id']]['itm_name'] = $row['itm_name'];
                        $display_arr[$row['item_id']]['currMonth'] = $row['currMonth'];
                    }
                    $qry = "SELECT
                                    tbl_hf_data.item_id,
                                    itminfo_tab.itm_name,
                                    SUM(tbl_hf_data.issue_balance) AS prevMonth
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            WHERE
                                    tbl_warehouse.dist_id = $districtId
                                    AND tbl_warehouse.stkid = $stakeholder
                                    AND tbl_warehouse.prov_id = $provId
                                    AND tbl_hf_data.reporting_date = '$prevMonth'
                                    AND itminfo_tab.itm_category = 1
                            GROUP BY tbl_hf_data.item_id";
                    //query result
                    //echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                        $display_arr[$row['item_id']]['prevMonth'] = $row['prevMonth'];
                    }
                    
                    $qry = "SELECT
                                    tbl_hf_data.item_id,
                                    itminfo_tab.itm_name,
                                    SUM(tbl_hf_data.issue_balance) AS corrMonth
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            WHERE
                                    tbl_warehouse.dist_id = $districtId
                                    AND tbl_warehouse.stkid = $stakeholder
                                    AND tbl_warehouse.prov_id = $provId
                                    AND tbl_hf_data.reporting_date = '$corrMonth'
                                    AND itminfo_tab.itm_category = 1
                            GROUP BY tbl_hf_data.item_id";
                    //query result
                    //echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                        $display_arr[$row['item_id']]['corrMonth'] = $row['corrMonth'];
                    }
                    //echo '<pre>';print_r($display_arr);exit;
                    //if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                            
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            Sale Proceeds of Contraceptives	<br>
                                            For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', District ' . $distrctName; ?>
                                        </h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table id="myTable" cellspacing="0" align="center" width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="20%">Product</th>
                                                    <th width="10%">Current Month(Rs)</th>
                                                    <th width="10%">Previous Month(Rs)</th>
                                                    <th width="10%">Corr. Month of Last Year(Rs)</th>
                                                    <th width="10%">Current Month Over Previous Month(%)</th>
                                                    <th width="10%">Current Month Over Corr. Month of Last Year(%)</th>
                                                    <th width="25%">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                <?php
                            
                                                $cur_total = 0;
                                                $prev_total = 0;
                                                $corr_total = 0;
                                                foreach($display_arr as $itm_id => $row)
                                                {
                                                //print_r($row);exit;
                                                    $currMonthSales = $row['currMonth'] * (!empty($pricing_arr[$pricing][$provId][$stakeholder][$itm_id])?$pricing_arr[$pricing][$provId][$stakeholder][$itm_id]:'0');
                                                    $prevMonthSales = $row['prevMonth'] * (!empty($pricing_arr[$pricing][$provId][$stakeholder][$itm_id])?$pricing_arr[$pricing][$provId][$stakeholder][$itm_id]:'0');
                                                    $corrMonthSales = $row['corrMonth'] * (!empty($pricing_arr[$pricing][$provId][$stakeholder][$itm_id])?$pricing_arr[$pricing][$provId][$stakeholder][$itm_id]:'0');
                                                    $currCorrPer = $currPrevPer = '';

                                                    $currPrevPer = ($prevMonthSales > 0) ? round((($currMonthSales / $prevMonthSales) - 1) * 100, 2) : '';
                                                    $currCorrPer = ($corrMonthSales > 0) ? round((($currMonthSales / $corrMonthSales) - 1) * 100, 2) : '';

                                                    $icon1=$icon2='';
                                                    if((double)$currPrevPer > 0) $icon1= '<i style="color:green !important;" class="fa fa-arrow-up"></i>';
                                                    if((double)$currPrevPer < 0) $icon1= '<i style="color:red !important;" class="fa fa-arrow-down"></i>';
                                                    if((double)$currCorrPer > 0) $icon2= '<i style="color:green !important;" class="fa fa-arrow-up"></i>';
                                                    if((double)$currCorrPer < 0) $icon2= '<i style="color:red !important;" class="fa fa-arrow-down"></i>';


                                                    echo '<tr>
                                                            <td class="left">'.($row['itm_name']).'</td>
                                                            <td class="right">'.number_format($currMonthSales, 2).'</td>
                                                            <td class="right">'.number_format($prevMonthSales, 2).'</td>
                                                            <td class="right">'.number_format($corrMonthSales, 2).'</td>
                                                            <td class="right">'.(($prevMonthSales > 0 && $currMonthSales) ? $currPrevPer . '%' : '').$icon1.'</td>
                                                            <td class="right">'.(($corrMonthSales > 0) ? $currCorrPer . '%' : '').$icon2.'</td>
                                                            <td>&nbsp;</td>
                                                        </tr>';

                                                    $cur_total += $currMonthSales;
                                                    $prev_total += $prevMonthSales;
                                                    $corr_total += $corrMonthSales;
                                                }
                                                
                                                
                                                $currCorrPer = $currPrevPer = '';

                                                $currPrevPer = ($prev_total > 0) ? round((($cur_total / $prev_total) - 1) * 100, 2) : '';
                                                $currCorrPer = ($corr_total > 0) ? round((($cur_total / $corr_total) - 1) * 100, 2) : '';

                                                $icon1=$icon2='';
                                                if((double)$currPrevPer > 0) $icon1= '<i style="color:green !important;" class="fa fa-arrow-up"></i>';
                                                if((double)$currPrevPer < 0) $icon1= '<i style="color:red !important;" class="fa fa-arrow-down"></i>';
                                                if((double)$currCorrPer > 0) $icon2= '<i style="color:green !important;" class="fa fa-arrow-up"></i>';
                                                if((double)$currCorrPer < 0) $icon2= '<i style="color:red !important;" class="fa fa-arrow-down"></i>';
                                                echo '<tr>
                                                        <td class="left">Total</td>
                                                        <td class="right">'.number_format($cur_total, 2).'</td>
                                                        <td class="right">'.number_format($prev_total, 2).'</td>
                                                        <td class="right">'.number_format($corr_total, 2).'</td>
                                                        <td class="right">'.(($prev_total > 0 && $cur_total) ? $currPrevPer . '%' : '').$icon1.'</td>
                                                        <td class="right">'.(($corr_total > 0) ? $currCorrPer . '%' : '').$icon2.'</td>
                                                        <td>&nbsp;</td>
                                                    </tr>';
                                                ?>
                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:100%; padding:18px;">
                            <div style="float:left; margin-top:50px;">
                                <b>District Population Welfare Officer<br><?php echo $distrctName; ?></b>
                            </div>
                            <div style="float:right; margin-top:50px; padding-right:10px;">
                                <b>District Demographer<br><?php echo $distrctName; ?></b>
                            </div>
                        </div>
                        <!--<div style="clear:both; float:right; margin-top:20px;" id="printButt">
                            <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printContents();" />
                        </div>-->
                    </div>
                    <?php
                } else {
                    echo "No record found";
                }
            }
// Unset varibles
            unset($data, $total, $whName, $method);
            ?>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
</body>
</html>