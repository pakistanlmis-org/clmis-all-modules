<?php
/**
 * 
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

include(PUBLIC_PATH . "html/header.php");

if (isset($_REQUEST['submit'])) {
    $selPro = $_REQUEST['prov_sel'];
    $selStk = $_REQUEST['stk_sel'];

    
$qry = " SELECT
            requisition_module_actions.action_name,
            submit_to.action_name AS must_submit_to,
            requisition_module_flow.prov_id,
            requisition_module_flow.stk_id,
            stakeholder.stkname,
            tbl_locations.LocName,
            requisition_module_flow.is_active,
            requisition_module_flow.action_id,
            requisition_module_flow.can_submit_to,
            requisition_module_flow.pk_id
        FROM
            requisition_module_flow
            INNER JOIN requisition_module_actions ON requisition_module_flow.action_id = requisition_module_actions.pk_id
            INNER JOIN stakeholder ON requisition_module_flow.stk_id = stakeholder.stkid
            INNER JOIN tbl_locations ON requisition_module_flow.prov_id = tbl_locations.PkLocID
            INNER JOIN requisition_module_actions as submit_to ON requisition_module_flow.can_submit_to = submit_to.pk_id
        WHERE
            requisition_module_flow.prov_id = $selPro AND
            requisition_module_flow.stk_id = $selStk
        order by
            requisition_module_flow.prov_id,
            requisition_module_flow.stk_id,
            requisition_module_flow.action_id
        ";
//query result
$qryRes = mysql_query($qry);
//num of record
$num = mysql_num_rows($qryRes);
//fetch results
$flow_arr = array();
while ($row = mysql_fetch_assoc($qryRes)) {
    $flow_arr[] = $row;
}

//echo '<pre>';print_r($flow_arr);exit;
    
} else {
    $selPro = '';
    $selStk = '';
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
//include top_im 
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Manage Requisitions Flow</h3>
                        
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Manage Flow - Filter</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
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
                                                                                AND stakeholder.stk_type_id = 0
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
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                    <div class="controls">
                                                        <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                           <?php
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                            AND LocType=2
                                                                        AND parentid IS NOT NULL";
                                                            $rsprov = mysql_query($queryprov) or die();
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
                <?php
                if(!empty($flow_arr))
                {
                ?>
                <form id="frm1" name="frm1" action="manage_requisition_flow_action.php" method="post">
                <div class="row">
                    <div class="col-md-12">
                      
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Manage Flow - Enable/ Disable</h3>
                            </div>
                            
                            <div class="widget-body">
                                <table class="table table-condensed table-hover">
                                    <tr>
                                        <th>Province</th>
                                        <th>Stakeholder</th>
                                        <!--<th>Action</th>-->
                                        <th>Action</th>
                                        <th>Enable</th>
                                    </tr>
                                <?php

                                foreach($flow_arr as $k => $row)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$row['LocName'].'</td>';
                                    echo '<td>'.$row['stkname'].'</td>';
                                    //echo '<td>'.$row['action_name'].'</td>';
                                    echo '<td>'.$row['must_submit_to'].'</td>';
                                    echo '<td><input name="enable['.$row['stk_id'].'_'.$row['prov_id'].'_'.$row['action_id'].'_'.$row['can_submit_to'].']" type="checkbox" '.(($row['is_active']==1)?' checked':'').'></td>';
                                    echo '</tr>';
                                }

                                ?>
                                </table>
                                <div class="control-group">
                                    <label>&nbsp;</label>
                                    <div class="controls">
                                        <input type="submit" name="submit" id="go" value="Save" class="btn btn-primary input-sm" />
                                    </div>
                                </div>
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
                    <input type="hidden" name="stk_id"      value="<?=$selStk?>">
                    <input type="hidden" name="prov_id"     value="<?=$selPro?>">
                </form>
                    <?php
                    }
                    ?>
            </div>
        </div>
    </div>

    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    
</body>
<!-- END BODY -->
</html>