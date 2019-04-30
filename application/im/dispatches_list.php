<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

if(!isset($_SESSION['integrated_stakeholder']))
{
        $qry2 = "  SELECT
                        integrated_stakeholders.main_stk_id,
                        integrated_stakeholders.sub_stk_id,
                        stakeholder.stkname
                    FROM
                    integrated_stakeholders
                    INNER JOIN stakeholder ON integrated_stakeholders.sub_stk_id = stakeholder.stkid
                    WHERE
                    integrated_stakeholders.province_id = ".$_SESSION['user_province1']." AND
                    integrated_stakeholders.main_stk_id = ".$_SESSION['user_stakeholder1']."  ";

        $qryRes3 = mysql_query($qry2);
        $num3 = mysql_num_rows($qryRes3);
        if($num3>0)
        {
                while($row=mysql_fetch_assoc($qryRes3))
                {
                    $_SESSION['integrated_stakeholder']='yes';
                    $_SESSION['sub_stakeholders'][$row['sub_stk_id']]=$row['stkname'];
                }
        }
        else
        {
                $_SESSION['integrated_stakeholder']='no';
        }
}
if($_SESSION['integrated_stakeholder'] == 'yes')
{
    redirect('dispatches_list2.php');  
}




if(!empty($_REQUEST['year_sel']))
    $selYear = $_REQUEST['year_sel'];
else
    $selYear = date('Y'); 
//creating temp array for months of year
$mon_year_arr=array();
for($i=1;$i<=12;$i++)
{
    $mon_year_arr[$i]=$i.'-'.$selYear;
}
//echo '<pre>';print_r($_SESSION);exit;


$q1 = "select * from tbl_locations where PkLocID = '".$_SESSION['user_province1']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_province_name = $row['LocName'];

$objuserstk->m_npkId = $_SESSION['user_id'];
//$requisition_stk = $objuserstk->GetStkByUserId();
$requisition_stk= array();
$requisition_stk[$_SESSION['user_stakeholder1']] = $_SESSION['user_stakeholder1'];

$sel_dist = '';
$sel_prov='';
$stkId="";
$date_from="";
$date_to="";
$where_clause  ="";
if (isset($_REQUEST['province']) && !empty($_REQUEST['province'])) {
        //get selected province
        $sel_prov = $_REQUEST['province'];
        //$where_clause .= " AND tbl_warehouse.prov_id = $sel_prov";
    }
    //check district
    if (isset($_REQUEST['districts']) && !empty($_REQUEST['districts']) && $_REQUEST['districts']!='all') {
        //get selected district
        $sel_dist = $_REQUEST['districts'];
        $where_clause .= " AND tbl_warehouse.dist_id = $sel_dist";
    }
    if (isset($_REQUEST['stk_sel']) && !empty($_REQUEST['stk_sel']) && $_REQUEST['stk_sel']!='all') {
        //get selected district
        $stkId = $_REQUEST['stk_sel'];
       $where_clause .= " AND clr_master.stk_id = $stkId";
    }

    //check status
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from']))  {
        //get status
        $date_from = $_REQUEST['date_from'];
        //$where_clause .= " AND clr_master.approval_status = '$status'";
    }
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to']) ) {
        //get status
        $date_to = $_REQUEST['date_to'];
        //$where_clause .= " AND clr_master.approval_status = '$status'";
    }
    
    if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id']) && $_REQUEST['item_id'] != 'All') {
        //get status
        $item_id = $_REQUEST['item_id'];
        //$where_clause2 .= " AND cd.itm_id = '$item_id'";
    }
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <form name="frm" id="frm" action="" method="get">
                        <div class="col-md-12">
                            <div class="widget hide" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter By</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3" >
                                                <div class="control-group ">
                                                    <label class="control-label">Year</label>
                                                    <div class="controls">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm" required>
                                                            <?php
                                                            if (empty($selYear)) $selYear= date('Y'); 
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
                                            <div class="col-md-4 right">
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" value="Search" class="btn btn-primary" />
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                            
                  <div class="row">
                    <form name="frm" id="frm" action="" method="get">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter By</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3" >
                                                <div class="control-group ">
                                                    <label class="control-label">Province</label>
                                                    <div class="controls">
                                                        <select name="province" id="province" class="form-control input-medium" <?=((!empty($_SESSION['user_level']) && $_SESSION['user_level']>1)?'readonly':'') ?> >
                                                            
                                                            <?php
                                                            $where="";
                                                            if((!empty($_SESSION['user_level'])) && $_SESSION['user_level']>1)
                                                            $where = " AND tbl_locations.PkLocID= ".$_SESSION['user_province1']." ";
                                                            
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                            $where
                                                                        AND parentid IS NOT NULL";
                                                            //query result
                                                            $rsprov = mysql_query($queryprov) or die();
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($rsprov)) {
                                                                if ($sel_prov == $row['prov_id']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate province combo
                                                                ?>
                                                                <option value="<?php echo $row['prov_id']; ?>" <?php echo $sel; ?>><?php echo $row['prov_title']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            
                                            if(empty( $_SESSION['user_level']) || $_SESSION['user_level']==2)
                                            {
                                                $where1 = " ParentID = ".$_SESSION['user_province1']." AND LocLvl = '3' ";
                                                $readonly ="  ";
                                            }
                                            else if($_SESSION['user_level']==3)
                                            {
                                                $where1 = " PkLocID = ".$_SESSION['user_district']." ";
                                                $readonly =" readonly ";
                                                
                                                $sel_dist = $_SESSION['user_district'];
                                            }
                                            else if(isset($_SESSION['user_province1']))
                                            {
                                               $where1 = " ParentID = ".$_SESSION['user_province1']." AND LocLvl = '3' ";
                                                $readonly ="  ";
                                            }
                                            else
                                            {
                                                $where1 = "  LocLvl = '3' ";
                                                $readonly ="  ";
                                            }
                                            
                                            ?>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">District</label>
                                                    <div class="controls" id="districtsCol">
                                                        <select name="districts" id="districts" class="form-control input-medium" <?=$readonly?> >
                                                            
                                                           <?php
                                                               if ($_SESSION['user_level'] != 3) {
                                                                    echo '<option value="">All</option>';
                                                                 }
                                                                 $qry  = "SELECT
                                                                                       PkLocID,
                                                                                       LocName
                                                                               FROM
                                                                                       tbl_locations
                                                                               WHERE
                                                                                      ".$where1." 
                                                                                         
                                                                               ";
                                                               $rsfd = mysql_query($qry) or die(mysql_error());
                                                               while($row = mysql_fetch_array($rsfd)){
                                                                       $sel = ($_REQUEST['districts'] == $row['PkLocID']) ? 'selected="selected"' : '';
                                                                       //echo "<option value=\"".$row['PkLocID']."\" $sel>".$row['LocName']."</option>";
                                                               }	
                                                               ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                       
                                                           <label class="control-label">Stakeholder</label>
                                                            <select name="stk_sel" id="stk_sel" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <option value="all" <?php echo ($stkId == 'all') ? 'selected' : ''; ?>>All</option>
                                                                <?php
                                                                $querystk = "SELECT stkid,stkname FROM stakeholder Where ParentID is null AND stakeholder.stk_type_id =0 AND  stakeholder.is_reporting = 1 AND  stakeholder.lvl = 1 order by stkorder";
                                                                $rsstk = mysql_query($querystk) or die();
                                                                while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                    if($stkId == $rowstk['stkid']) $user_stakeholder_name = $rowstk['stkname'];
                                                                    ?>
                                                                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($stkId == $rowstk['stkid']) ? 'selected="selected"' : ''; ?>><?php echo $rowstk['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                      
                                                </div>
                                            </div>
                                        
                                                <div class="col-md-3 hide">
                                                    <label class="control-label">Product</label>
                                                    <select name="item_id" id="item_id" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            
                                            
                                            <div class="col-md-1 right">
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" value="Go" class="btn btn-primary" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3 hide">
                                                <div class="control-group ">
                                                    <label class="control-label">From</label>
                                                    <div class="controls">
                                                        <input type="text" name="date_from" id="date_from" value="<?php echo $date_from; ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <div class="control-group ">
                                                    <label class="control-label">To</label>
                                                    <div class="controls">
                                                        <input type="text" name="date_to" id="date_to" value="<?php echo $date_to; ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                        
                                </div>
                            </div>
                        
                    </form>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading"> Dispatches List - <?php echo (!empty($user_province_name)?$user_province_name.'':'').(!empty($user_stakeholder_name)?' - '.$user_stakeholder_name.'':'')?></h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div style="margin-left:0px !important; width:100% !important;">
                                        <style>
                                            body {
                                                margin: 0px !important;
                                                font-family: Arial, Helvetica, sans-serif;
                                            }

                                            table#myTable {
                                                margin-top: 20px;
                                                border-collapse: collapse;
                                                border-spacing: 0;
                                            }

                                            table#myTable tr td, table#myTable tr th {
                                                font-size: 11px;
                                                padding-left: 5px;
                                                text-align: left;
                                                border: 1px solid #999;
                                            }

                                            table#myTable tr td.TAR {
                                                text-align: right;
                                                padding: 5px;
                                                width: 50px !important;
                                            }

                                            .sb1NormalFont {
                                                color: #444444;
                                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                                font-size: 11px;
                                                font-weight: bold;
                                                text-decoration: none;
                                            }

                                            p {
                                                margin-bottom: 5px;
                                                font-size: 11px !important;
                                                line-height: 1 !important;
                                                padding: 0 !important;
                                            }

                                            table#headerTable tr td {
                                                font-size: 11px;
                                            }

                                            /* Print styles */
                                            @media only print {
                                                table#myTable tr td, table#myTable tr th {
                                                    font-size: 8px;
                                                    padding-left: 2 !important;
                                                    text-align: left;
                                                    border: 1px solid #999;
                                                }

                                                #doNotPrint {
                                                    display: none !important;
                                                }
                                            }
                                        </style>
                                        
                                        <div style="clear:both;"></div>
                                        <table width="100%" id="myTable"  class="requisitions table table-striped table-bordered table-condensed" cellspacing="0" align="center">
                                            
                                            
                                            <thead>
                                                <tr>
                                                    <td style="text-align:center;" >S. No.</td>
                                                    <td style="text-align:center;" >Distribution Plan Number</td>
                                                    <td style="text-align:center;" >Issued Voucher Number</td>
                                                    <td style="text-align:center;" >Issued On</td>
                                                    <td style="text-align:center;" >Status</td>
                                                </tr>
                                            </thead>
                                            
                                            
                                            <tbody>
                                                <?php
                                                        $qry = "SELECT
                                                                        DISTINCT tbl_stock_master.TranNo,
                                                                        tbl_stock_master.PKStockId,
                                                                        tbl_stock_detail.IsReceived,
                                                                        clr_distribution_plans.pk_id as plan_id,
                                                                        clr_distribution_plans.plan_number,
                                                                        clr_master.pk_id,
                                                                        clr_master.approval_status,
                                                                        tbl_stock_master.CreatedOn,
                                                                        DATEDIFF(CURDATE(),tbl_stock_master.CreatedOn) as in_transit_days
                                                                        FROM
                                                                        clr_distribution_plans
                                                                        INNER JOIN clr_master ON clr_distribution_plans.pk_id = clr_master.distribution_plan_id
                                                                        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                                                                        INNER JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
                                                                        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                                        INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans_stk.plan_id = clr_distribution_plans.pk_id
                                                                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                                                                        WHERE
                                                                        clr_master.approval_status IN ( 'Issued','Issue in Process') AND
                                                                        
                                                                        clr_distribution_plans.prov_id =  ".$_SESSION['user_province1']." 
                                                                            
                                                                        $where_clause
                                                                ";
                                                        //echo $qry;            
                                                        $res = mysql_query($qry);

                                                        $disp_data=$total_data=array();
                                                        if( mysql_num_rows($res) > 0)
                                                        {
                                                         
                                                            $c=1;
                                                            while($row = mysql_fetch_assoc($res))
                                                            {
                                                                echo '<tr>';
                                                                echo '<td style="text-align:center;">'.$c++.'</td>';
                                                                echo '<td style="text-align:center;"><a  target="_blank" href="distribution_plan_view.php?plan_id='.$row['plan_id'].'&plan_num='.$row['plan_number'].'" >'.$row['plan_number'].'</a></td>';
                                                                echo "<td style=\"text-align:center;\"><a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $row['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $row['TranNo'] . "</a></td>";
                                                                echo '<td style="text-align:center;">'.$row['CreatedOn'].'</td>';
                                                                if($row['IsReceived'] == 1)
                                                                    echo '<td style="text-align:center;"><a href="clr7_view.php?issue_no='.$row['TranNo'].'&search=true">Received</a></td>';
                                                                else
                                                                    echo '<td style="text-align:center;">'.$row['in_transit_days'].' days in transit</td>';
                                                                echo '</tr>';
                                                            }
                                                        }
                                                        else
                                                        {
                                                            echo 'No Dispatches are In-Transit.';
                                                        }
                                                        ?>
                                                
                                                
                                            </tbody>
                                        </table>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <!-- END JAVASCRIPTS -->
    <script>
        $(function() {
            $("#date_to,#date_from").datepicker({
                dateFormat: 'yy-mm',
                constrainInput: false,
                changeMonth: true,
                changeYear: true
            });
        })

        $(function() {
           
            showDistricts();
            $('#province').change(function(e) {
                showDistricts();
            });
            
        })
        $(function() {
            $('#stk_sel').change(function(e) {
                $('#item_id').html('<option value="">Select</option>');
                showProducts('');
            });
        })
        
        function showDistricts() {
            
            var pid = $('#province').val();
            
            
            if (pid != '')
            {
                $.ajax({
                    url: 'fetchDistricts.php',
                    type: 'POST',
                    data: {pid: pid, distId: '<?php echo $sel_dist; ?>', user_level: '<?php echo (!empty($_SESSION['user_level'])?$_SESSION['user_level']:''); ?>'},
                    success: function(data) {
                        $('#districtsCol').html(data);
                        //var dists = $('#districts').html();
                        //$('#districts').html(dists);
                    }
                })
            }
        }
        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            $.ajax({
                url: '<?php echo APP_URL; ?>reports/ajax_calls.php',
                type: 'POST',
                data: {stakeholder_id: stk, productId: pid},
                success: function(data) {
                    $('#item_id').html(data);
                }
            })
        }
        
        <?php
if (isset($item_id) && !empty($item_id)) {
    ?>
            showProducts('<?php echo $item_id; ?>');
    <?php
}
?>
    </script>
</body>
<!-- END BODY -->
</html>