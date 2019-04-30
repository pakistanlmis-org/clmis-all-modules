<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$userid = $_SESSION['user_id'];
$wh_id = $_SESSION['user_warehouse'];
$stk_id = $_SESSION['user_stakeholder1'];
@$dist_id = $_REQUEST['dist'];
$dist_name = '';
$to_be_dist_arr=array();    
@$month = $_REQUEST['month'];   


$qry = "SELECT
          max(tbl_hf_data.reporting_date) as reporting_date
        FROM
            tbl_hf_data
            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
            INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        WHERE
            tbl_warehouse.dist_id = '".$dist_id."' AND
            tbl_warehouse.stkid = '".$stk_id."'
";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$total_amc_arr = array();
$row = mysql_fetch_assoc($qryRes);
$last_month = $row['reporting_date'];

if(empty($last_month))
    $last_month = date('Y-m-01',strtotime("-1 month"));


$replenishment_arr = $calculated_replenishment_arr =  array();

$qry = "SELECT
            tbl_hf_data.pk_id,
            tbl_hf_data.warehouse_id,
            tbl_hf_data.item_id,
            tbl_hf_data.closing_balance,
            tbl_hf_data.avg_consumption,
            tbl_hf_data.reporting_date,
            tbl_locations.LocName as dist_name
        FROM
            tbl_hf_data
            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
            INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        WHERE
            tbl_hf_data.reporting_date = '".$last_month."' AND
            tbl_warehouse.dist_id = '".$dist_id."' AND
            tbl_warehouse.stkid = '".$stk_id."'

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$total_amc_arr = array();
while($row = mysql_fetch_assoc($qryRes))
{
   $replenishment_arr[$row['warehouse_id']][$row['item_id']]['cb']        = $row['closing_balance'];
   $replenishment_arr[$row['warehouse_id']][$row['item_id']]['amc']       = $row['avg_consumption'];
   @$total_amc_arr[$row['item_id']]   += $row['avg_consumption'];
   $dist_name = $row['dist_name'];
}


foreach($replenishment_arr as $wh_id => $wh_data){
    foreach($wh_data as $itm_id => $itm_data){
        @$est_soh                                        = ( $itm_data['cb'] + $issued_arr[$wh_id][$itm_id] ) - $itm_data['amc'];
        @$calculated_replenishment_arr[$wh_id][$itm_id]  = ($itm_data['amc'] * 3) - $est_soh;
    }    
}

//echo '<pre>';print_r($replenishment_arr);
//echo '<pre>';print_r($calculated_replenishment_arr);exit;


$qry = " SELECT
            itminfo_tab.itm_id,
           itminfo_tab.itm_name,
           stakeholder.stkname
           FROM
           itminfo_tab
           INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
           INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
           WHERE
               itminfo_tab.itm_category = ".(($stk_id=='145')?'5':'1')." AND
               itminfo_tab.method_type IS NOT NULL AND
           stakeholder_item.stkid = $stk_id
           ORDER BY
               itminfo_tab.method_rank ASC
";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$itm_arr =   array();
while($row = mysql_fetch_assoc($qryRes))
{
   $itm_arr[$row['itm_id']]       = $row['itm_name'];
   $stakeholder_name = $row['stkname'];
}
       

$qry = "SELECT
    itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.qty_carton,
                SUM(stock_batch.Qty) AS qty,
                tbl_itemunits.UnitType
        FROM
                stock_batch
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
        WHERE
                stock_batch.`wh_id` = '" . $_SESSION['user_warehouse'] . "'
        GROUP BY
                itminfo_tab.itm_id
        ORDER BY
                itminfo_tab.frmindex";

//query result
//echo $qry;exit;
$qryRes = mysql_query($qry);
$stock_arr =   array();
while($row = mysql_fetch_assoc($qryRes))
{
   $stock_arr[$row['itm_id']]       = $row['qty'];
}
//echo '<pre>';print_r($calculated_replenishment_arr);exit;

$issued_arr = $comments_arr = array();
if(isset($dist_id))
{
    $stk_in = $_SESSION['user_stakeholder1'];
    if($_SESSION['user_stakeholder1'] == 145)
        $stk_in = '2,73';
    
    $qry_1 = "SELECT DISTINCT
                tbl_stock_master.TranNo,
                tbl_stock_master.PkStockID,
                tbl_stock_detail.IsReceived,
                tbl_stock_detail.comments,
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                stock_batch.batch_no,
                stock_batch.item_id,
                sum(abs(tbl_stock_detail.Qty)) as qty_issued,
                stock_batch.batch_id as batch_id_from
            FROM
            tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
            WHERE
                tbl_stock_master.TranTypeID = 2 AND
                tbl_warehouse.dist_id =  " . $dist_id . " AND
                tbl_warehouse.stkid in (" . $stk_in . ") AND
                stakeholder.lvl = 7
            GROUP BY

                tbl_warehouse.wh_id,
                stock_batch.item_id,
                tbl_stock_master.TranNo
            ORDER BY
                tbl_stock_master.PkStockID ASC";
    //echo $qry_1;exit;
    $getStockIssues = mysql_query($qry_1) or die("Err get issued vouchers");

    while($row = mysql_fetch_assoc($getStockIssues)){
        $issued_arr[$row['wh_id']][$row['item_id']] =  $row['qty_issued'];
        $issued_arr[$row['wh_id']]['vouchers'][$row['TranNo']]    =  $row['PkStockID'];
        @$comments_arr[$row['wh_id']]    .=  $row['comments'];
    }
}
//echo '<pre>';print_r($calculated_replenishment_arr);exit;
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php 
        //include top
        //include top_im
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?=$stakeholder_name.' - '.$dist_name?> - District Distribution Plan</h3>
                            </div>
                            
                            <div class="widget-body">
                            <div class="row">
                            <div class="col-md-12">
                                
                                <div class="form-group col-md-4">
                                    <select name="dist_id" id="dist_id" class="form-control input-sm"  onchange="load_dist_data(this)">
                                            <option value="">Select</option>
                                                <?php
                                                $queryDist = "SELECT
                                                                    tbl_locations.PkLocID,
                                                                    tbl_locations.LocName
                                                            FROM
                                                                    tbl_locations
                                                            WHERE
                                                                    tbl_locations.LocLvl = 3
                                                            AND tbl_locations.parentid = '" . $_SESSION['user_province1'] . "'
                                                            ORDER BY
                                                                    tbl_locations.LocName ASC";
                                                //query result
                                                $rsDist = mysql_query($queryDist) or die();
                                                //fetch result
                                                $dist_name = "";
                                                while ($rowDist = mysql_fetch_array($rsDist)) {
                                                    if ($dist_id  == $rowDist['PkLocID'] ) {
                                                        $sel = "selected='selected'";
                                                        $dist_name=$rowDist['LocName'];
                                                    } else {
                                                        $sel = "";
                                                    }

                                                    
                                                        echo '<option value="'.$rowDist['PkLocID'].'" '.$sel.'>'.$rowDist['LocName'].'</option>';
                                                }
                                                ?>
                                        </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <select class="form-control hide" id="month" name="month"  onchange="getComboA(this)" >
                                        <option value="">Current Issuance</option>
                                        <?php
                                        for($i=1;$i<=12;$i++){
                                            
                                            $sel = "";
                                            $this_val = date('Y-m-01',strtotime("-$i month"));
                                            if(!empty($month) && $month == $this_val) $sel = " selected ";
                                            echo '<option value="'.$this_val.'" '.$sel.'>Issuance of '.date('Y M',strtotime("-$i month")).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <?php
                                if(isset($dist_id)){
                                    
                                
                                ?>
                                <table class="table table-hover table-condensed table-bordered">
                                    <tr>
                                        <td>S. No.</td>
                                        <td>SDP</td>
                                        <?php
                                        foreach($itm_arr as $itm_id => $itm_name){
                                            echo '<td>'.$itm_name.'</td>';
                                        }
                                        ?>
                                        <td>Vouchers</td>
                                        <td>Action</td>
                                        <td>Remarks</td>
                                    </tr>
                                    
                                    <?php
                                    
                                      $qry = "SELECT
                                                    tbl_warehouse.wh_id,
                                                    tbl_warehouse.wh_name,
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.dist_id,
                                                    tbl_warehouse.stkid,
                                                    stakeholder.stkname
                                                FROM
                                                    tbl_warehouse
                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                WHERE
                                                    stakeholder.lvl = 7 AND
                                                    tbl_warehouse.dist_id = $dist_id AND
                                                    tbl_warehouse.stkid in (".(($stk_id=='145')?'2,73':$stk_id).")
                                                        
                                                order by tbl_warehouse.stkid,
                                                tbl_warehouse.wh_name
                                        ";
                                        //echo $qry;exit;
                                        $qryRes = mysql_query($qry);
                                        $itm_arr2 =   array();
                                        $c=1;
                                        $last_stk = '';
                                        while($row = mysql_fetch_assoc($qryRes))
                                        {
                                            $params = '';
                                            $sdp_id = $row['wh_id'];
                                            //print_r($row);
                                            if($last_stk != $row['stkname'])
                                            {
                                                echo '<tr style="background-color:#bbabc9"><td colspan="99">'.$row['stkname'].'</td></tr>';
                                            }
                                            echo '<tr>
                                                    <td>'.$c++.'</td>
                                                    <td>'.$row['wh_name'].' ';
//                                            echo '<a href="../dashboard/sdp_issuance_trend.php?wh_id='.$sdp_id.'"><i class="fa fa-table font-blue"></i></a>';
                                            ?>
                                            <a onclick="window.open('../dashboard/sdp_issuance_trend.php?wh_id=<?=$sdp_id?>&itm=1', 'MsgWindow', 'width=900,height=400')"><i class="fa fa-table font-blue"></i></a>
                                            <?php
                                            echo '</td>';
                                                foreach($itm_arr as $itm_id => $itm_name){
                                                            $this_val = 0;
                                                            if(!empty($calculated_replenishment_arr[$sdp_id][$itm_id]) && $calculated_replenishment_arr[$sdp_id][$itm_id]>0)
                                                                $this_val = $calculated_replenishment_arr[$sdp_id][$itm_id];

                                                             $params .= '&itm_'.$itm_id.'='.$this_val;

                                                            $read = ' readonly ';
                                                            $st = ' ';
                                                            if (!empty($issued_arr[$sdp_id][$itm_id]) && $issued_arr[$sdp_id][$itm_id]>0) {
                                                                $this_val = $issued_arr[$sdp_id][$itm_id];
                                                                $read = ' readonly ';
                                                                $st = ' background-color:#aadd99 ';
                                                            }
                                                             
                                                            echo '<td><input class="form-control" '.$read.' name="input_'.$sdp_id.'_'.$itm_id.'" id="input_'.$sdp_id.'_'.$itm_id.'" data-itm-id='.$sdp_id.' data-itm-name="'.$itm_name.'" value="'.$this_val.'" style="text-align:right;'.$st.'"></td>';
                                                            @$to_be_dist_arr[$itm_id] += $this_val;
                                                        }
                                        
                                                        
                                            if (!empty($issued_arr[$sdp_id]['vouchers'])) {
                                                $issueVoucher = '';
                                                //fetch results
                                                foreach ($issued_arr[$sdp_id]['vouchers'] as $voucher => $pk_stock) {
                                                    $a = "<div> ";
                                                    $a .= " <a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $pk_stock . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $voucher . "</a>";

                                                    $a .= " </div>";
                                                    $issueVoucher[] = $a;
                                                }
                                                echo '<td>'.implode(' ', $issueVoucher).'</td>';
                                            } else {
                                                echo "<td></td>";
                                            }

                                            echo ' 
                                                        <td class="col-md-1 ">

                                                        <a class="btn btn-xs green" href="issue_to_wh.php?wh_id='.$sdp_id.$params.'&ref_page=prov_to_sdp_issue">Issue</a>
                                                        </td>
                                                        <td>'.(!empty($comments_arr[$sdp_id])?$comments_arr[$sdp_id]:'').'</td>
                                                    </tr>';
                                            $last_stk = $row['stkname'];
                                        }
                                         
                                    
                                    echo '<tr>';
                                    echo '<td colspan="2"><b>Total distribution:</b></td>';
                                    foreach($itm_arr as $itm_id => $itm_name){
                                        echo '<td align="right">'.number_format($to_be_dist_arr[$itm_id]).'</td>';
                                    } 
                                    echo '</tr>';   
                                    
                                    echo '<tr>';
                                    echo '<td colspan="2"><b>Stock at District Store</b></td>';
                                    foreach($itm_arr as $itm_id => $itm_name){
                                        if(!empty($stock_arr[$itm_id])) $val = $stock_arr[$itm_id];
                                        else $val = 0;
                                        echo '<td align="right">'.number_format($val).'</td>';
                                    } 
                                    echo '</tr>';
                                    
                                    echo '<tr>';
                                    echo '<td colspan="2"><b>Total AMC</b></td>';
                                    foreach($itm_arr as $itm_id => $itm_name){
                                        if(!empty($total_amc_arr[$itm_id])) $val = $total_amc_arr[$itm_id];
                                        else $val = 0;
                                        echo '<td align="right">'.number_format($val).'</td>';
                                    } 
                                    echo '</tr>';
                                    
                                    echo '<tr>';
                                    echo '<td colspan="2"><b>MOS</b></td>';
                                    foreach($itm_arr as $itm_id => $itm_name){
                                        $mos=0;
                                        if( !empty($total_amc_arr[$itm_id]) &&  $total_amc_arr[$itm_id] > 0)
                                        {
                                            if(!empty($stock_arr[$itm_id])) $val = $stock_arr[$itm_id];
                                             else $val = 0;
                                             $mos = $val / $total_amc_arr[$itm_id]; 

                                        }
                                        echo '<td align="right">'.number_format($mos,1).'</td>';
                                    } 
                                    echo '</tr>';

                                    ?>
                                    
                                </table>
                                
                                <?php
                                }
                                else
                                    echo 'Please choose a district';
                                ?>
                                
                            </div>
                            </div>
                            </div>
                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
//include footer
include PUBLIC_PATH . "/html/footer.php"; ?>
</div>
<script>
    
    function load_dist_data(selectObject) {
        var value = selectObject.value;  
        //alert(value);
        location.href = "prov_to_sdp_issue.php?dist="+value;
    }
    
    function getComboA(selectObject) {
        var value = selectObject.value;  
        //alert(value);
        location.href = "distribution_plan_district_level.php?month="+value;
    }
    
    </script>
</body>
<!-- END BODY -->
</html>