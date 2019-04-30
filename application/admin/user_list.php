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
 
    
    $where_clause =$where_clause2= '';
    if (isset($_REQUEST['province']) && !empty($_REQUEST['province'])) {
        //get selected province
        $sel_prov = $_REQUEST['province'];
        $where_clause .= " AND sysuser_tab.province = $sel_prov";
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
            $where_clause .= " AND sysuser_tab.stkid = $stkId";
    }

    //echo '<pre>';print_r($months);exit;
    
    if (!empty ($_SESSION['user_level']) && $_SESSION['user_level'] > 1) {
        //$where_clause .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']. " "; 
    }
//select query
 $qry = "SELECT
                sysuser_tab.sysusr_type,
                sysuser_tab.user_level,
                sysuser_tab.whrec_id,
                sysuser_tab.usrlogin_id,
                sysuser_tab.sysusr_name,
                sysuser_tab.sysusr_email,
                sysuser_tab.province,
                sysuser_tab.stkid,
                sysuser_tab.UserID,
                sysuser_tab.sysusr_dept,
                sysuser_tab.sysusr_deg,
                sysuser_tab.sysusr_ph,
                roles.role_name,
                (SELECT tbl_user_login_log.login_time FROM tbl_user_login_log WHERE tbl_user_login_log.user_id = sysuser_tab.UserID ORDER BY pk_id DESC limit 1) as last_login_time,
                (SELECT tbl_user_login_log.ip_address FROM tbl_user_login_log WHERE tbl_user_login_log.user_id = sysuser_tab.UserID ORDER BY pk_id DESC limit 1) as last_login_ip

            FROM
                sysuser_tab
            LEFT JOIN tbl_warehouse ON sysuser_tab.whrec_id = tbl_warehouse.wh_id
            INNER JOIN roles ON sysuser_tab.sysusr_type = roles.pk_id
            WHERE
                sysuser_tab.user_level = 3
                $where_clause
";
//echo $qry;
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
$disp_arr = array();

}

?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php

        
        include $_SESSION['menu'];
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
                            </div>
                        
                    </form>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Users</h3>
                            </div>
                            <div class="widget-body">
                               
                                    <?php
                                        $fileName='users_log';
                                        //include sub_dist_reports
                                        include('sub_export_options.php');
                                    ?>
                                 <div class="col-md-12">
                                <table id="myTable" class="table table-condensed table-bordered" >
                               <?php
                               $submitted_arr = array();
                               $c=1;
                                echo '<tr>';
                                    echo '<th>Sr No</th>';
                                    echo '<th>ID</th>';
                                    echo '<th>Full Name</th>';
                                    echo '<th>Email</th>';
                                    echo '<th>Phone</th>';
                                    echo '<th>Department</th>';
                                    echo '<th>Designation</th>';
                                    echo '<th>Role</th>';
                                    echo '<th>Last Login</th>';
                                    echo '<th>Last IP</th>';
                                    echo '<th>Mail</th>';
                                    echo '<th>History</th>';
                                
                                echo '</tr>';
                                
                               
                               if(isset($num) && $num>0)
                               {
                               while($row = mysql_fetch_assoc($qryRes))
                                {
                                    echo '<tr>';
                                    echo '<td>'.$c++.'</td>';
                                    echo '<td>'.$row['usrlogin_id'].'</td>';
                                    echo '<td>'.$row['sysusr_name'].'</td>';
                                    if(!empty($row['sysusr_email']))
                                        echo '<td>'.$row['sysusr_email'].'</td>';
                                    else
                                        echo '<td class="danger">'.$row['sysusr_email'].'</td>';
                                    
                                    if(!empty($row['sysusr_ph']))
                                        echo '<td>'.$row['sysusr_ph'].'</td>';
                                    else
                                        echo '<td class="danger">'.$row['sysusr_ph'].'</td>';
                                    
                                    echo '<td>'.$row['sysusr_dept'].'</td>';
                                    echo '<td>'.$row['sysusr_deg'].'</td>';
                                    echo '<td>'.$row['role_name'].'</td>';
                                    echo '<td>'.$row['last_login_time'].'</td>';
                                    echo '<td>'.$row['last_login_ip'].'</td>';
                                    echo '<td>';
                                    if(!empty($row['sysusr_email']))
                                    {
                                        echo '<a onclick="window.open(\'../admin/send_email_popup.php?user_id='.$row['UserID'].'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-envelope" style="color:#000 !important;"></i></a>    ';
                                    }
                                    echo '</td>';
                                    
                                    echo '<td><a onclick="window.open(\'../admin/user_login_history.php?user_id='.$row['UserID'].'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-history" style="color:#000 !important;"></i></td>';
                                    
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
                    url: '../reports/fetchDistricts.php',
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