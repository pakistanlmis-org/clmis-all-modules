<?php

/**
 * spr8
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
//include header
include(PUBLIC_PATH."html/header.php");
//report id
$rptId = 'sdp_p';
//user province id
$userProvId = $_SESSION['user_province1'];
//if submitted
if (isset($_POST['submit'])) {
    //get from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);
    $hfTypeId = $_POST['hf_type_sel'];
//select query
    // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //district name
    $distrctName = $row['LocName'];
    //file name
    $fileName = 'SPR8_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
    
    
    $qry = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.method_rank
            FROM
            itminfo_tab
            WHERE
            itminfo_tab.itm_category = 1 AND
            itminfo_tab.method_type IS NOT NULL
            ORDER BY
            itminfo_tab.method_rank";
    //query result
    $rs1 = mysql_query($qry);
    while($row = mysql_fetch_assoc($rs1)){
        $items[$row['itm_id']] = $row['itm_name'];
    }
}

$gTotal=array();
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
		<?php 
                //include top
                include PUBLIC_PATH."html/top.php";
        //include top_im
        include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Monthly Report of Family Planning Activities</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php 
                                //sub_dist_form
                                include('sub_dist_form.php'); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
                //check if submitted
                if (isset($_POST['submit']))
				{
                   
                	$qry = " 
                                SELECT
                                        tbl_warehouse.wh_id,
                                        tbl_warehouse.wh_name,
                                        tbl_hf_data.item_id,
                                        tbl_hf_data.issue_balance,
                                        itminfo_tab.itm_name,
                                        tbl_warehouse.hf_type_id,
                                        tbl_hf_type.hf_type
                                FROM
                                        tbl_warehouse
                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                WHERE
                                        DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                        AND tbl_warehouse.dist_id = $districtId
                                        AND tbl_warehouse.stkid = $stakeholder
                                        AND itminfo_tab.itm_category = 1
                        ";
                        //print_r($_REQUEST);
                        $hf_type_s = implode(',',$hfTypeId);
                        if(!empty($hf_type_s) && ($hf_type_s) != '0'){
                            $qry .= " AND tbl_warehouse.hf_type_id in (".$hf_type_s.") ";
                        }
                        $qry .= "                
                                GROUP BY
                                        tbl_warehouse.wh_id,
                                        tbl_hf_data.item_id
                                ORDER BY
                                    tbl_hf_type.hf_rank,
                                    tbl_hf_type.hf_type,
                                    tbl_warehouse.wh_name
                         ";
                        //query result
                        //echo $qry;exit;
                        $qryRes = mysql_query($qry);
			//check if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        
                            //include sub_dist_reports
                            include('sub_dist_reports.php'); ?>
                            <div class="col-md-12" style="overflow:auto;">
                                <?php
                                 //items
                                        
                                        //warehouse name
                                        $whName = '';
                                        //warehouse id
                                        $wh_id = '';
                                        //items
                                         $whName = array();
                                        //fetch results
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            //check item                                
                                            //$items[$row['item_id']] = $row['itm_name'];
                                            $whName[$row['wh_id']] = $row['wh_name'];
                                            $whType[$row['hf_type_id']] = $row['hf_type'];
                                            
                                            
                                            $data[$row['hf_type_id']][$row['wh_id']][$row['item_id']]    = $row['issue_balance'];
                                        
                                        }
                                        //echo '<pre>';
                                        //print_r($data);exit;
                                ?>
                                <table width="100%">
                                    <tr>
                                        <td align="center">
                                            <h4 class="center">
                                            <?php
                                            
                                                echo "SDP Wise Performance<br>";
                                                

                                                if( $fromDate != $toDate )
                                                {
                                                    //reportint period
                                                        $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                                }
                                                else
                                                {
                                                //reportint period
                                                    $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                                }
                                                ?>
                                            <?php echo $reportingPeriod . ', District ' . $distrctName; ?>,
                                            HF Types : <?=(!empty($hf_list)?implode(',',$hf_list):" All")?>
                                            </h4>
                                        </td>
                                       
                                    
                                    </tr>
                                </table>
                                        
                                <table id="myTable" width="100%" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th >S.No</th>
                                                    <th >Name of the Outlet</th>
                                                    <?php
                                                    foreach ($items as $name) {
                                                        echo "<th>$name</th>";
                                                    }
                                                    ?>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                <?php
                                                $counter = 1;
                                                $old_type=0;
                                                $prod_totals = array();
                                                foreach ($data as $type => $type_data) {
                                                        
                                                    $old_totals = array();
                                                    echo '<tr style="background-color:#bae2ba">';
                                                    echo '<td colspan="30">'.$whType[$type].'</td>';
                                                    echo '</tr>';
                                                    foreach ($type_data as $whId => $wh_data) {
                                                            $wh_name = $whName[$whId];
                                                            
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $wh_name; ?></td>
                                                            <?php
                                                            foreach ($items as $pid=>$methodName) {
                                                                $val = ( (!empty($data[$type][$whId][$pid])) ? $data[$type][$whId][$pid] : 0 ) ;
                                                                
                                                                if(empty($old_totals[$pid]))$old_totals[$pid]=0;
                                                                if(empty($prod_totals[$pid]))$prod_totals[$pid]=0;
                                                                $old_totals[$pid] += $val;
                                                                $prod_totals[$pid] += $val;
                                                                echo "<td class=\"right\">" . number_format($val) . "</td>";

                                                            }
                                                            ?>
                                                        </tr>    
                                                            <?php
                                                            
                                                    }
                                                    ?>
                                                        <tr>
                                                            <td colspan="2" align="center"><b><?php echo $whType[$type]; ?> Total : </b></td>
                                                            <?php
                                                            foreach ($items as $pid=>$methodName) {
                                                                echo "<td class=\"right\"><b>" . ( (!empty($old_totals[$pid])) ? number_format($old_totals[$pid]) : 0 ) . "</b></td>";

                                                            }
                                                            ?>
                                                        </tr>    
                                                    <?php
                                                }
                                                    ?>
                                                        
                                                <tr style="background-color:#c6c0ed">
                                                    <td colspan="2" align="center"><h4><b>Grand Total :</b></h4> </td>
                                                    <?php
                                                    foreach ($items as $pid=>$methodName) {
                                                        echo "<td class=\"right\"><h4><b>" . ( (!empty($prod_totals[$pid])) ? number_format($prod_totals[$pid]) : 0 ) . "</b></h4></td>";

                                                    }
                                                    ?>
                                                </tr>  
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>
                                        
                                       
                            </div>
                        </div>
                        <?php
                    } else {
                        echo "No record found";
                    }
                }
                // Unset varibles
                unset($items, $whName, $data, $total);
                ?>
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";
        //include combos
     include ('combos.php'); ?>
</body>
</html>