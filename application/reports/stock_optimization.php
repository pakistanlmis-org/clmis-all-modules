<?php
ini_set('max_execution_time', 0);
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");


$mos_required = 3;
$min_date = date("Y-m-01", strtotime("-2 months"));


$show='';
if(!empty($_REQUEST['lvl'])) 
    $lvl = $_REQUEST['lvl'];

$stakeholder = (!empty($_SESSION['user_stakeholder1']) ? $_SESSION['user_stakeholder1'] : '1');
if (isset($_REQUEST['submit'])) {
    $d_1 = '2018-07-01';
    if($_SERVER['SERVER_ADDR']=='::1')$d_1 = '2018-01-01';
    $date           = (!empty($_REQUEST['date'])?$_REQUEST['date']:$d_1);
    $date = date('Y-m-01',strtotime($date));
    $stakeholder    = mysql_real_escape_string($_REQUEST['stakeholder']);
    $selProv        = mysql_real_escape_string($_REQUEST['prov_sel']);
    $districtId     = mysql_real_escape_string($_REQUEST['dist_id']);
    $itm_id         = mysql_real_escape_string($_REQUEST['product']);
}
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"><?=(isset($_REQUEST['submit'])?'(1) List of SDPs with stock status ('.date('M-Y',strtotime($date)).')':'Stock Optimization Analysis')?></h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //include('sub_dist_form.php');
                                ?>
                            </div>
                            <form id="frm">
                                
                            <table>
                                
                                <td class="col-md-2">
                                    <label class="control-label">Month</label>
                                    <input class="form-control input-sm" type="date" min="<?=$min_date?>" name="date" id="date" value="<?=$date?>">
                                </td>
                                <td class="col-md-2">
                                    <label class="control-label">Stakeholder</label>
                                    <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                <option value="">Select</option>
                                <?php

                                $querys = "SELECT
                                        stakeholder.stkid,
                                        stakeholder.stkname
                                        FROM
                                        stakeholder
                                        WHERE
                                        stakeholder.ParentID IS NULL
                                        AND stakeholder.stk_type_id IN (0, 1) AND
                                        stakeholder.is_reporting = 1
                                        
                                        ORDER BY
                                        stakeholder.stkorder ASC";
                                $rsprov = mysql_query($querys) or die();
                                $stk_name = '';
                                while ($rowp = mysql_fetch_array($rsprov)) {
                                    if ($stakeholder == $rowp['stkid']) {
                                        $sel = "selected='selected'";
                                        $stk_name = $rowp['stkname'];
                                    } else {
                                        $sel = "";
                                    }
                                    ?>
                                    <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                                </td>
                                <td class="col-md-2">
                                                <label class="control-label">Province</label>
                                                <select name="prov_sel" id="prov_sel" onchange="showDistricts()" required="required" class="form-control input-sm">
                                                 <option>Select</option>
                                                               <?php

                                                                $queryprov = "SELECT
                                                                                    tbl_locations.PkLocID AS prov_id,
                                                                                    tbl_locations.LocName AS prov_title
                                                                                FROM
                                                                                    tbl_locations
                                                                                WHERE
                                                                                    LocLvl = 2
                                                                                    AND parentid IS NOT NULL
                                                                                    AND tbl_locations.LocType = 2";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();
                                                                $prov_name='Punjab';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($selProv == $rowprov['prov_id']) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name=$rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }

                                                                    //if($_SESSION['user_level'] > 1 && isset($_SESSION['user_province1']) && $_SESSION['user_province1'] == $rowprov['prov_id'] ) 
                                                                        echo '<option value="'.$rowprov['prov_id'].'" '.$sel.'> '.$rowprov['prov_title'].'</option>';

                                                                    }

                                                            ?>
                                                    </select>
                                    </td>
                                    <td class="col-md-2 filter1" id="td_dist" ><label class="sb1NormalFont">District:</label>
                                        <select name="dist_id" id="dist_id" class="form-control input-sm">
                                                <?php
                                                $queryDist = "SELECT
                                                                    tbl_locations.PkLocID,
                                                                    tbl_locations.LocName
                                                            FROM
                                                                    tbl_locations
                                                            WHERE
                                                                    tbl_locations.LocLvl = 3
                                                            AND tbl_locations.parentid = '" . $selProv . "'
                                                            ORDER BY
                                                                    tbl_locations.LocName ASC";
                                                //query result
                                                $rsDist = mysql_query($queryDist) or die();
                                                //fetch result
                                                $dist_name = "Attock";
                                                while ($rowDist = mysql_fetch_array($rsDist)) {
                                                    if ($districtId == $rowDist['PkLocID'] ) {
                                                        $sel = "selected='selected'";
                                                        $dist_name=$rowDist['LocName'];
                                                    } else {
                                                        $sel = "";
                                                    }

                                                    //if($_SESSION['user_level'] == 3 && isset($_SESSION['user_district']) && $_SESSION['user_district'] == $rowDist['PkLocID'] ) 
                                                        echo '<option value="'.$rowDist['PkLocID'].'" '.$sel.'>'.$rowDist['LocName'].'</option>';
                                                }
                                                ?>
                                        </select>
                                    </td>
                                    <td class="col-md-2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Product</label>
                                                    <select required  name="product" id="product" class=" form-control input-sm" >
                                                        <?php
                                                        $itm_name='';
                                                        $queryprov = "SELECT
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
                                                        $rsprov = mysql_query($queryprov) or die();

                                                        while ($rowprov = mysql_fetch_array($rsprov)) {
                                                            if ($rowprov['itm_id'] == $itm_id) {
                                                                $sel = "selected='selected'";
                                                                $itm_name=$rowprov['itm_name'];
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                                <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                     <?php
                                                            }

                                                    ?>
                                                    </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary input-sm" value="Submit">Go</button>
                                            </div>
                                        </div>
                                    </td>
                            </table></form>
                        </div>

                    </div>
                </div>
                <div>
                <?php
                if (isset($_REQUEST['submit'])) {
                    
                    $qry = "SELECT
                                    tbl_hf_data.avg_consumption,
                                    tbl_hf_data.closing_balance,
                                    round(tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption,2) AS mos,
                                    tbl_warehouse.wh_name,
                                    tbl_hf_data.reporting_date,
                                    tbl_warehouse.wh_id
                                FROM
                                    tbl_hf_data
                                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                WHERE
                                    tbl_warehouse.dist_id = $districtId AND
                                    tbl_hf_data.reporting_date = '".$date."' AND
                                    tbl_hf_data.item_id = $itm_id AND
                                    tbl_warehouse.stkid = $stakeholder AND
                                    tbl_warehouse.hf_type_id NOT IN ( 3, 9, 6, 7, 8, 12, 10, 11)
                                order by mos desc
                                ";
                     
                    //print_r($query_w);exit;
                    $qryRes = mysql_query($qry);
                    $data_arr = array();

                    echo '<a class="btn btn-sm green" href="stock_optimization.php?date='.$date.'&stakeholder='.$stakeholder.'&prov_sel='.$selProv.'&dist_id='.$districtId.'&product='.$itm_id.'&submit=Submit">(1) List of Stock Status at SDP Level</a>';
                    echo ' <i class="fa fa-arrow-right " style="color:black !important;"></i> <a class="btn btn-sm grey" href="stock_optimization_2.php?date='.$date.'&stakeholder='.$stakeholder.'&prov_sel='.$selProv.'&dist_id='.$districtId.'&product='.$itm_id.'&submit=Submit">(2) Filter SDPs with unusual stock</a>';
                    echo ' <i class="fa fa-arrow-right " style="color:black !important;"></i> <a class="btn btn-sm dark" disabled href="">(3)  Analyze Stock</a>';
                    echo '<table id="myTable" class="table table-bordered table-hover table-condensed" style="width:100%;">
                            <tr>
                                <th>#</th>
                                <th>Facility Name</th>
                                <th>SOH</th>
                                <th>AMC</th>
                                <th>MOS</th>
                                <th>MOS Required</th>
                                <th>Status</th>
                                <th>Transferrable</th>
                            </tr>';
                    $c= 1;
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        $status='-';
                        
                        if(!isset($row['avg_consumption']) || $row['avg_consumption'] <= 0 ){
                            $status = 'Unknown';
                        }
                        elseif($row['mos'] <= 0.5){
                            $status = '<span style="color:red"><b>Stock Out</b></span>';
                        }elseif($row['mos'] <= 0.99){
                            $status = 'Under Stock';
                        }elseif($row['mos'] <= 2.99){
                            $status = 'Satisfactory';
                        }else{
                            $status = '<span style="color:green"><b>Over Stock</b></span>';
                        }
                        
                        $transferrable=0;
                        if($row['mos'] > $mos_required)
                        {
                            $per_month = $row['closing_balance'] / $row['mos'];
                            $extra_months = $row['mos'] - $mos_required;
                            $transferrable = $extra_months * $per_month;
                        }
                        echo '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$row['wh_name'].'</td>
                                <td align="right">'.number_format($row['closing_balance']).'</td>
                                <td align="right">'.number_format($row['avg_consumption']).'</td>
                                <td align="right">'.number_format($row['mos'],2).'</td>
                                <td align="right">'.$mos_required.'</td>
                                <td>'.$status.'</td>
                                <td align="right">'.number_format($transferrable).'</td>
                            </tr>';
                    }
                    echo '</table>';
                        ?>
                </div>
                </div>
            </div>  
            <?php
        }

// Unset variables
        unset( $ob, $cb, $rcv, $issue, $data, $hfTypes, $itemIds, $product);
        ?>
    </div>
</div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
//include ('combos.php');
?>
<script>
    function showDistricts() {
            $.ajax({
                    type: "POST",
                    url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                    data: {lvl: 3, prov_id: $('#prov_sel').val()},
                    success: function(data) {
                            $("#td_dist").html(data);
                    }
            });
    }
    $(function () {
        //showDistricts();
        $(document).on('click', '#hide_cols', function () {
            if($(this).is(":checked"))
            {
                $('.prod_head').each(function(){
                    var prod = $(this).data('itm');
                    var hide = 'true';
                    $('.prod_'+prod).each(function(){
                        if($(this).data('status') == 'full'){
                            //$(this).css('background-color', 'red');
                            hide = 'false';
                        }
                    });
                    if(hide=='true'){
                        $(this).html('').html(hide);
                        //$(this).css('background-color', 'red');
                        //$('.prod_'+prod).css('background-color', 'red');
                        $('.prod_'+prod).hide(500);
                        $(this).hide(500);
                    }
                });
                console.log('clicked ');
            }
            else
            {
                console.log(' off ');
                $('.prod_head').each(function(){
                    $('.prod_'+prod).show(300);
                    $(this).show(300);
                });
            }
                
        });
        
        $(document).on('change', '#prov_sel', function () {
            console.log('b');
            var prov = $(this).val();
            console.log('val:' + prov);



            $("#dist_id").children("option[value='']").wrap('<span/>');
            $("#dist_id").attr('required', true);
            console.log('d');


        });
        
        $(document).on('change', '#lvl', function () {
            var lvl = $(this).val();
            if(lvl == '3')
            {
                $("#td_dist").hide(100);
            }
            else
            {
                $("#td_dist").show(100);
            }
        });

    });
     
</script>
</body>

</html>