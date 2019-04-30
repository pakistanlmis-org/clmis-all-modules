<?php
ini_set('max_execution_time', 0);
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$rptId = 'sdp_batches';
$html_row = '';
$lvl = 7;

$show='';
if(!empty($_REQUEST['lvl'])) $lvl = $_REQUEST['lvl'];


$stakeholder = (!empty($_SESSION['user_stakeholder1']) ? $_SESSION['user_stakeholder1'] : '1');
if (isset($_REQUEST['submit'])) {
    $show = isset($_REQUEST['show_all'])?$_REQUEST['show_all']:'s';
    $stakeholder = mysql_real_escape_string($_REQUEST['stakeholder']);
    $selProv = mysql_real_escape_string($_REQUEST['prov_sel']);
    $districtId = mysql_real_escape_string($_REQUEST['dist_id']);
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
                        <h3 class="page-title row-br-b-wp">District Contraceptive Stock Report</h3>
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
                                <td class="col-md-2 filter1">
                                   <label class="control-label">Level</label>
                                    <select name="lvl" id="lvl" class="form-control input-sm">
                                        <option value="7" <?=(($lvl=='7')?' selected ':'')?>>SDP</option>
                                        <option value="3" <?=(($lvl=='3')?' selected ':'')?>>District Stores</option>
                                    </select>
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
                                        AND stakeholder.stkid in (".(($_SESSION['user_stakeholder1']=='145')?'2,73':$_SESSION['user_stakeholder1']).")
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
                                    <td class="col-md-2 filter1" id="td_dist" style=" <?=(($lvl=='7')?'  ':' display:none;')?>"><label class="sb1NormalFont">District:</label>
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
                                                    if ($district == $rowDist['PkLocID'] ) {
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
                                        <div class="form-group" id="checkboxDiv">
                                            <br><br>
                                            <div class="form-group">
                                                <input name="show_all" id="show_all" type="checkbox" <?php  if($show=="on") {echo 'checked';} else{ echo "";}?>> Show All Warehouses/SDPs 


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
                    $where = '';
                    if (!empty($districtId) && $lvl =='7' ) 
                    {

                        $where = "AND tbl_warehouse.dist_id=$districtId";
                    }
                    $qry = " SELECT
                                stakeholder.stkid,
                                stakeholder.stkname AS stakeholder,
                                tbl_locations.LocName AS districts, 
                                tbl_warehouse.dist_id,
                                tbl_warehouse.wh_name AS warehouse,
                                 stock_batch.wh_id,
                                stock_batch.item_id,
                                stock_batch.batch_no,
                                stock_batch.batch_id,
                                stock_batch.Qty

                                FROM
                                stakeholder
                                INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
                                INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_warehouse.dist_id
                                INNER JOIN stakeholder AS st ON tbl_warehouse.stkofficeid = st.stkid
                                INNER JOIN stock_batch ON tbl_warehouse.wh_id = stock_batch.wh_id
                                WHERE

                                st.lvl = $lvl 
                                AND tbl_warehouse.prov_id = $selProv
                                AND
                                tbl_warehouse.stkid=$stakeholder $where

                                ORDER BY
                                warehouse ASC
                                ";
                     //print_r($qry); 
                    $qryPro = "SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name
                                FROM
                                itminfo_tab
                                WHERE
                                itminfo_tab.itm_category IN(" . (($_SESSION['user_stakeholder1'] == 145) ? '5' : '1,4') . ")

                                 AND
                                itminfo_tab.method_type IS NOT NULL

                                ";
                                //                    print_r($qryPro);exit;

                    $query_w = "SELECT 
                                tbl_warehouse.wh_name AS warehouse,
                                 tbl_warehouse.wh_id

                                FROM
                                tbl_warehouse


                                INNER JOIN stakeholder AS st ON tbl_warehouse.stkofficeid = st.stkid
                                WHERE st.lvl = $lvl 
                                AND tbl_warehouse.prov_id = $selProv    
                                AND 
                                tbl_warehouse.stkid=$stakeholder $where

                                ORDER BY
                                warehouse ASC";
                    //query result
                    //print_r($query_w);exit;
                    $qryRes = mysql_query($qry);
                    $qryProRes = mysql_query($qryPro);
                    $result_w = mysql_query($query_w);
                    $products = array();
                    $data_arr = array();

                    //$count=0;
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        //print_r($row);
                        $pop = 'onclick="window.open(\'batch_history.php?id=' . $row['batch_id'] . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                        @$data_arr[$row ['wh_id']][$row ['item_id']] .= "<a class='alert-link' " . $pop . " ></br><span style=\"color:blue;\"><b>" . $row['batch_no'] . "</b></span><span style=\"color:black;\">[" . $row['Qty'] . "]" . "</span></a>";
                    }
                    //print_r($data_arr);
                    if (mysql_num_rows($qryRes) > 0) {
                        ?>
                    <input type="checkbox" id="hide_cols" name="hide_cols">Hide products with no data
                    <table id="myTable" class="table table-bordered table-condensed" style="width:100%;">

                            <th>
                                Facility Name

                            </th>
                            <?php
                            while ($rowPro = mysql_fetch_array($qryProRes)) {
                                ?>
                            <th class="prod_head prod_head_<?=$rowPro['itm_id']?>" data-itm="<?=$rowPro['itm_id']?>"><?php echo $rowPro['itm_name'];
                                ?></th> 
                                <?php
                                $products[$rowPro['itm_id']] = $rowPro['itm_id'];

                            }
                            ?>
                                
                            <?php 
                            if(isset($_REQUEST['show_all'])){
                                while ($row = mysql_fetch_array($result_w)) {
                                
                                ?>
                                        <tr>

                                        <td>

                                            <?php
                                            echo $row['warehouse'];
                                            ?>
                                        </td>
                                        <?php foreach ($products as $key => $value) { ?>
                                        <td class="prod_<?=$key?>" data-status="<?=((isset($data_arr[$row['wh_id']][$key]))?'full':'empty')?>" data-itm="<?=$key?>">
                                                <?php
                                                if ((isset($data_arr[$row['wh_id']][$key]))) {
                                                    echo $data_arr[$row['wh_id']][$key];
                                                }  
                                                ?>
                                            </td>

                                        <?php } ?>
                                    </tr>
                                    <?php  

                                } 
                            }
                            else{
                            while ($row = mysql_fetch_array($result_w)) {
                                
                                if(array_key_exists($row['wh_id'], $data_arr))
                                {?>
                                    <tr>

                                    <td>

                                        <?php
                                        echo $row['warehouse'];
                                        ?>
                                    </td>
                                    <?php foreach ($products as $key => $value) { ?>
                                        <td class="prod_<?=$key?>" data-status="<?=((isset($data_arr[$row['wh_id']][$key]))?'full':'empty')?>" data-itm="<?=$key?>">
                                            <?php
                                            if ((isset($data_arr[$row['wh_id']][$key]))) {
                                                echo $data_arr[$row['wh_id']][$key];
                                            }  
                                            ?>
                                        </td>

                                    <?php } ?>
                                </tr>
                                <?php }
                                
                            }}
                            ?></table><?php
                    } else {
                        echo 'No data found';
                    }
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
        showDistricts();
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
                        //$(this).html('').html(hide);
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
                    var prod = $(this).data('itm');
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