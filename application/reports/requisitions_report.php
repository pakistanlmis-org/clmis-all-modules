<?php
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

//echo '<pre>';print_r($_SESSION);exit;

//this list is for district level data entry user
$where = 'WHERE 1=1';
$is_provincial_user = false;
$req_num = '';
//requisition number
$requisitionNum = '';
//selected district
$sel_dist = '';
$sel_prov='';
$stkId="";
//status
$status = '';

//if form sumitted
if (isset($_REQUEST['submit'])) {
 
    
    $date_to = $_REQUEST['date_to'];
    $date_to1 = $date_to."-01";
    $months = array();
    $months[] = $date_to1;
    $last_month =  date("Y-m-t",strtotime($date_to1));
    for ($i = 1; $i < 12; $i++) {
        $m = date("Y-m-01", strtotime( date("Y-m-01",strtotime($date_to1))." -$i months"));
        $months[] = $m;
        
        $first_month = $m;
    }
    krsort($months);
    
    $where_clause =$where_clause2= '';
    if (isset($_REQUEST['province']) && !empty($_REQUEST['province'])) {
        //get selected province
        $sel_prov = $_REQUEST['province'];
        $where_clause .= " AND tbl_warehouse.prov_id = $sel_prov";
    }
    //check district
    if (isset($_REQUEST['districts']) && !empty($_REQUEST['districts']) && $_REQUEST['districts']!='all') {
        //get selected district
        $sel_dist = $_REQUEST['districts'];
        $where_clause .= " AND tbl_warehouse.dist_id = $sel_dist";
    }
    if (isset($_REQUEST['stk_sel']) && !empty($_REQUEST['stk_sel']) ) {
        //get selected district
        $stkId = $_REQUEST['stk_sel'];
        if($_REQUEST['stk_sel']!='all')
            $where_clause .= " AND clr_master.stk_id = $stkId";
    }

    //check status
    if (isset($_REQUEST['status']) && !empty($_REQUEST['status']) && $_REQUEST['status'] != 'All') {
        //get status
        $status = $_REQUEST['status'];
        $where_clause .= " AND clr_master.approval_status = '$status'";
    }
    
    if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id']) && $_REQUEST['item_id'] != 'All') {
        //get status
        $item_id = $_REQUEST['item_id'];
        $where_clause2 .= " AND cd.itm_id = '$item_id'";
    }

    $where_clause .= " AND clr_master.date_to BETWEEN '".$first_month."' AND '".$last_month."' ";
    
    //echo '<pre>';print_r($months);exit;
    
    if (!empty ($_SESSION['user_level']) && $_SESSION['user_level'] > 1) {
        //$where_clause .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']. " "; 
    }
//select query
 $qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname,
                clr_master.pk_id,
                clr_master.requisition_num,
                clr_master.wh_id,
                clr_master.fk_stock_id,
                clr_master.approval_status,
                MONTH (clr_master.date_to) AS clrMonth,
                YEAR (clr_master.date_to) AS clrYear,
                
                clr_master.date_from as date_from,
                clr_master.date_to as date_to,
                tbl_warehouse.wh_type_id,
                tbl_warehouse.wh_name,
                tbl_locations.LocName,
                CONCAT(DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y'), ' ', TIME_FORMAT(clr_master.requested_on, '%h:%i:%s %p')) AS requested_on,
                (select sum(qty_req_dist_lvl1)  from clr_details cd where cd.pk_master_id=clr_master.pk_id $where_clause2 ) as qty_total
        FROM
                clr_master
        INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
        WHERE 1=1
        $where_clause
        GROUP BY
                clr_master.requisition_num
        ORDER BY
                
                tbl_warehouse.wh_name ASC";
//echo $qry;
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
$disp_arr = array();

}
else 
{

    $date_to = date('Y-m');
    $date_to5 = date('Y/m/01');
    $date_to1 = dateToDbFormat($date_to5);
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
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
                                                                       echo "<option value=\"".$row['PkLocID']."\" $sel>".$row['LocName']."</option>";
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
                                                                <option value="all" <?php echo ($stkId == 'all') ? ' selected ' : ''; ?>>All</option>
                                                                <?php
                                                                $querystk = "SELECT stkid,stkname FROM stakeholder Where ParentID is null AND stakeholder.stk_type_id IN (0,1) order by stkorder";
                                                                $rsstk = mysql_query($querystk) or die();
                                                                while ($rowstk = mysql_fetch_array($rsstk)) {
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
                                        </div>
                                            

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Ending Month</label>
                                                    <div class="controls">
                                                        <input type="text" name="date_to" id="date_to" value="<?php echo $date_to; ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Status</label>
                                                    <div class="controls">
                                                        <select name="status" id="status" class="form-control input-medium">
                                                            <option value="All" <?php echo ($status == 'All') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <option value="Pending" <?php echo ($status == 'Pending') ? 'selected="selected"' : ''; ?>>Pending</option>

                                                            <option value="Prov_Approved" <?php echo ($status == 'Prov_Approved') ? 'selected="selected"' : ''; ?>>Approved by Province</option>

                                                            <option value="Issued" <?php echo ($status == 'Issued') ? 'selected="selected"' : ''; ?>>Issued</option>
                                                            <option value="Issue in Process" <?php echo ($status == 'Issue in Process') ? 'selected="selected"' : ''; ?>>Issue in Process</option>
                                                            <option value="Approved" <?php echo ($status == 'Approved') ? 'selected="selected"' : ''; ?>>Approved</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 right">
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
                            </div>
                        
                    </form>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Status of Requisitions</h3>
                            </div>
                            <div class="widget-body">
                                <table class="table table-condensed table-bordered" >
                               <?php
                               $submitted_arr = array();
                               if(isset($num) && $num>0)
                               {
                               while($row = mysql_fetch_assoc($qryRes))
                                {
                                    $to1 = date('Y-m', strtotime($row['date_to']));
                                    $wh_arr[$row['wh_id']]['disp_name'] = $row['wh_name'].'  - '.$row['stkname'];
                                    $wh_arr[$row['wh_id']]['stk_id']    = $row['stkid'];
                                    
                                    if(!isset($disp_arr[$row['wh_id']][$to1]))$disp_arr[$row['wh_id']][$to1]=0;
                                    $disp_arr[$row['wh_id']][$to1]+=$row['qty_total'];
                                    
                                    $submitted_arr[$row['wh_id']][$to1]= 'yes';
                                    $temp_to2 = date("Y-m", strtotime( date("Y-m-01",strtotime($to1))." -1 months"));
                                    $submitted_arr[$row['wh_id']][$temp_to2]= 'yes';
                                    $temp_to3 = date("Y-m", strtotime( date("Y-m-01",strtotime($to1))." -2 months"));
                                    $submitted_arr[$row['wh_id']][$temp_to3]= 'yes';
                                    
                                }
                                //echo '<pre>';print_r($submitted_arr);
                                
                                echo '<tr>';
                                    echo '<th>District</th>';
                                foreach ($months as $k =>$mon)
                                {
                                    echo '<th><b>'.date('M-y', strtotime($mon)).'</b></th>';
                                }
                                echo '</tr>';
                                
                                
                                foreach ($disp_arr as $wh_id=>$wh_data)
                                {
                                    echo '<tr>';
                                    echo '<td><b>'.$wh_arr[$wh_id]['disp_name'].'</b></td>';
                                    foreach ($months as $k =>$mon)
                                    {
                                        $yr3 = date('Y', strtotime($mon));
                                        $mon3 = date('m', strtotime($mon));
                                        $mon2 = date('Y-m', strtotime($mon));
                                        
                                        if(isset($wh_data[$mon2])) $qty = $wh_data[$mon2];
                                        else $qty =0;
                                        
                                        $cls = '';
                                        if(!empty($submitted_arr[$wh_id][$mon2]))
                                        $cls = 'success';
                                        echo '<td class="'.$cls.'" title="Click to view details" >';
                                        
                                        if(array_key_exists($mon2, $wh_data))
                                        //echo '<a style="color:#000" href="requisitions_report_detail.php?month='.$mon3.'&year='.$yr3.'&stk_id='.$wh_arr[$wh_id]['stk_id'].'&wh_id='.$wh_id.'" target="_blank">'.number_format($qty).'</a>';
                                        echo '<a style="color:#000" href="requisitions_report_detail.php?month='.$mon3.'&year='.$yr3.'&stk_id='.$wh_arr[$wh_id]['stk_id'].'&wh_id='.$wh_id.'" target="_blank">View</a>';
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                
                                
                               }
                               else
                               {
                                   if(isset($_REQUEST['submit']))
                                   echo '<div class="note note-warning">No data found</div>';
                               }
                               
                               ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script type="text/javascript">
        $(function() {
            $("#date_to").datepicker({
                dateFormat: 'yy-mm',
                constrainInput: false,
                changeMonth: true,
                changeYear: true
            });
        })
    </script>
    <script>


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
                        var dists = $('#districts').html();
                        $('#districts').html(dists);
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
   
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>