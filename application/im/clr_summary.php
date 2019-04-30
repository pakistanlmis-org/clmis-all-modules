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
$year = isset($_REQUEST['year']) ? mysql_real_escape_string($_REQUEST['year']) : date('Y');





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
    $itm_name_id=$products =array();
    //print_r($_SESSION);
    while($row= mysql_fetch_assoc($res))
    {
        $itm_name_id[$row['itm_name']] = $row['itm_id'];
        $products[$row['itm_id']] = $row['itm_name'];
    }

    
    $qry = "SELECT
                stakeholder.stkname,
                clr_master.pk_id,
                clr_master.requisition_num,
                clr_master.wh_id,
                clr_master.fk_stock_id,
                clr_master.approval_status,
                MONTH (clr_master.date_to) AS clrMonth,
                YEAR (clr_master.date_to) AS clrYear,
                
                DATE_FORMAT(clr_master.date_from, '%b-%Y') as date_from,
                DATE_FORMAT(clr_master.date_to, '%b-%Y') as date_to,
                tbl_warehouse.wh_type_id,
                tbl_warehouse.wh_name,
                tbl_locations.LocName,
                CONCAT(DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y'), ' ', TIME_FORMAT(clr_master.requested_on, '%h:%i:%s %p')) AS requested_on,
                (select sum(qty_req_dist_lvl1)  from clr_details cd where cd.pk_master_id=clr_master.pk_id) as qty_total
        FROM
                clr_master
        INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
        $where
        GROUP BY
                clr_master.requisition_num
        ORDER BY
                clr_master.requisition_num DESC,
                tbl_locations.LocName ASC,
                tbl_warehouse.wh_name ASC";
//echo $qry;
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);

?>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="">
     
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        //include PUBLIC_PATH . "html/top.php";
//include tio_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper1">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row hide">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">New Requisition (3 Months)</h3>
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
                                                            <select name="year" id="year" required="required" class="form-control input-small">
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
                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-condensed">
                            <tr>
                                <td>Sr No</td>
                                <td>Products</td>
                                <?php
                                for($i=1;$i<=12;$i++)
                                {
                                    echo '<td>'.date('M', strtotime('2017-'.$i.'-01')).'</td>';
                                }
                                ?>
                            </tr>
                            
                            <?php
                            foreach($products as $itm_id =>$itm_name)
                            {
                                echo '<tr>';
                                echo '<td></td>';
                                echo '<td>'.$itm_name.'</td>';
                                for($i=1;$i<=12;$i++)
                                {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>

</body>
<!-- END BODY -->
</html>