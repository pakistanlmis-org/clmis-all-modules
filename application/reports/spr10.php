<?php
/**
 * spr10
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
ini_set('max_execution_time', 300);
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$rptId = 'spr10';
//if submitted
$and_dist = "";
$and_hf="";
$dist_id = '';
$itm_totals=array();
if (isset($_POST['submit'])) {
    //from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);

    $hf_id = mysql_real_escape_string($_POST['hf_type_sel']);
    $hfTypeId = $hf_id;
    if($hf_id!=0){
        $and_hf="  tbl_warehouse.hf_type_id=$hf_id
                AND";
    }
    else if($hf_id==0)
    {
        $and_hf='';
        
    }
    $districtId = mysql_real_escape_string($_POST['district']);
    if (empty($districtId) || $districtId == null) {
        $and_dist = "tbl_warehouse.prov_id = $selProv AND";
        $and_dist2 = "  tbl_locations.PkLocID = $selProv ";
    } else {
        $and_dist = "tbl_warehouse.dist_id = $districtId AND";
        $and_dist2 = "  tbl_locations.PkLocID = $districtId ";
    }
    //district id
    //select query
    // Get district name
    $qry = "SELECT
				tbl_locations.LocName
            FROM
				tbl_locations
            WHERE " . $and_dist2;

    $row = mysql_fetch_array(mysql_query($qry));

    $distrctName = $row['LocName'];
    //file name 
    $fileName = 'SPR10_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
    $dist_id = $districtId;
}



if (!isset($selProv) && $_SESSION['user_province1'] < 10)
    $selProv = $_SESSION['user_province1'];

if (!isset($selProv))
    $selProv = 1;
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
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
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Contraceptive Performance</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
<?php include('sub_dist_form.php'); ?>
                            </div>
                        </div>

                    </div>
                </div>
<?php
//if submitted
if (isset($_POST['submit'])) {
    //select query
    //gets
    //warehouse id,
    //warehouse name,
    //item name,
    //issue balance,
    //performed,
    //reffered,
    //item category,
    //sales,
    //CYP
    $qry = "SELECT
A.hf_type,
	A.wh_id,
	A.wh_name,
	A.itm_name,
        A.LocName,
	A.itm_id,
	A.issue_balance,
	A.performed,
	A.reffered,
	A.itm_category,
	(
		REPgetItemPrice ('$toDate-01', 1, $selProv, A.itm_id) * A.issue_balance
	) AS sales,

IF (
	A.itm_category = 2,
	(A.CYPFactor * A.performed),
	(
		A.CYPFactor * A.issue_balance
	)
) AS CYP
FROM
	(
		SELECT 
			tbl_hf_type.hf_type AS hf_type,
			tbl_warehouse.wh_id,
			tbl_warehouse.wh_name,
                        tbl_locations.LocName,
			SUM(tbl_hf_data.issue_balance) AS issue_balance,
			itminfo_tab.extra AS CYPFactor_old,
			provincial_cyp_factors.cyp_factor AS CYPFactor,
			itminfo_tab.itm_id,
			itminfo_tab.itm_name,
			itminfo_tab.itm_category,
			SUM(
				tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp
			) AS performed,

		IF (
			tbl_warehouse.hf_type_id IN (4, 5),
			(

				IF (
					tbl_hf_data_reffered_by.hf_type_id IN (4, 5),
					SUM(
						tbl_hf_data_reffered_by.ref_surgeries
					),
					0
				)
			),
			SUM(tbl_hf_data.issue_balance)
		) AS reffered
		FROM
			tbl_warehouse
	INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
		INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
		INNER JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID		
AND tbl_hf_data.item_id = provincial_cyp_factors.item_id
		AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id
		LEFT JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
		WHERE $and_dist $and_hf
		tbl_warehouse.stkid = 1
		AND DATE_FORMAT(
			tbl_hf_data.reporting_date,
			'%Y-%m'
		) BETWEEN '$fromDate'
		AND '$toDate'
                
		GROUP BY
			tbl_warehouse.wh_id,
			tbl_hf_data.item_id
		ORDER BY
                tbl_locations.LocName,
		tbl_warehouse.wh_name ASC,
		itminfo_tab.frmindex ASC
	) A";
    //query results
//    print_r($qry);exit;
    $qryRes = mysql_query($qry);
    //fetch results
    if (mysql_num_rows($qryRes) > 0) {
        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                        <?php
                        $whId = '';
                        $itemId = '';
                        $items = $whName =$whName2 =$district= array();
                        $c=0;
                      
                        //fetch results
                        while ($row = mysql_fetch_array($qryRes)) {
                            if (!in_array($row['itm_name'], $items) && $row['itm_category'] != 2) {
                                $items[$row['itm_id']] = $row['itm_name'];
                            }
                            //check warehouse name
                            //if (!in_array($row['wh_name'], $whName)) {
                            //    $whName[$row['wh_id']] = $row['wh_name'];
                            //}
                            $whName[$row['wh_id']] = $row['wh_name'];
                            $district[$row['wh_id']]=$row['LocName'];
                            $data[$row['wh_id']]['CYP'][] = round($row['CYP']);
                            $data[$row['wh_id']]['sales'][] = round($row['sales']);
                            $total['CYP'][] = round($row['CYP']);
                            $total['sales'][] = round($row['sales']);
                            //check  item category
                            if ($row['itm_category'] == 2) {
                                $data[$row['wh_id']]['cs_done'][] = $row['performed'];
                                $data[$row['wh_id']]['cs_reffer'][] = $row['reffered'];
                                $total['cs_done'][] = $row['performed'];
                                $total['cs_reffer'][] = $row['reffered'];
                            } else {
                                $data[$row['wh_id']][$row['itm_id']] = $row['issue_balance'];
                                @$total[$row['itm_name']][] = $row['issue_balance'];
                            }
                            $c++;
                        }
                       // echo $c;
                        //echo '<pre>';print_r($whName2);print_r($whName);exit;
                        ?>
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            Individual Outlet wise Contraceptive Performance <br>
        <?php
        if ($fromDate != $toDate) {
            //set reporting period
            $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
        } else {
            //set reporting period	
            $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
        }
        ?>
                                            <?php echo $reportingPeriod .(!empty($districtId)?', District ':', Province ').'' . $distrctName.' '.$hf_type_name?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">SPR-10</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 10px;">
                                        <table id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No</th>
                                                    <th rowspan="2" style="width:150px !important;">District</th>
                                                           <th rowspan="2" style="width:150px !important;">Name of the Outlet</th>

 <?php
        //fetch items
        foreach ($items as $itm_id => $name) {
            echo "<th>$name</th>";
        }
        ?>
                                                    <th colspan="2">Surgery Cases</th>
                                                    <th rowspan="2">CYP</th>
                                                    <th rowspan="2">Sales (Rs)</th>
                                                    <th rowspan="2">Remarks</th>
                                                </tr>
                                                <tr>
        <?php
        foreach ($items as $itm_id => $name) {
            echo "<th>(Achivement)</th>";
        }
        ?>
                                                    <th>Reffered</th>
                                                    <th>Performed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        <?php
        $counter = 1;
        foreach ($whName as $id => $name) {
            ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $district[$id]; ?></td>
                                                        <td><?php echo $name; ?></td>
                                                    <?php
                                                    //fetch items
                                                    foreach ($items as $itm_id => $methodName) {
                                                        echo "<td class=\"right\">" . number_format(@$data[$id][$itm_id]) . "</td>";
                                                        @$itm_totals[$itm_id] += $data[$id][$itm_id];
                                                    }
                                                    echo "<td class=\"right\">" . number_format(array_sum($data[$id]['cs_reffer'])) . "</td>";
                                                    echo "<td class=\"right\">" . number_format(array_sum($data[$id]['cs_done'])) . "</td>";
                                                    ?>
                                                        <td class="right" style="width:90px;"><?php echo number_format(array_sum($data[$id]['CYP'])); ?></td>
                                                        <td class="right"><?php echo number_format(array_sum($data[$id]['sales'])); ?></td>
                                                        <?php
                                                            @$itm_totals['CYP']     += array_sum($data[$id]['CYP']);
                                                            @$itm_totals['sales']   += array_sum($data[$id]['sales']);
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
            <?php
        }
        ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="3">Total</th>
        <?php
        //fetch items
        foreach ($items as $itm_id => $methodName) {
            //echo "<th class=\"right\">" . number_format(array_sum($total[$methodName])) . "</th>";
            echo "<th class=\"right\">" .number_format(@$itm_totals[$itm_id]) . "</th>";
        }
        echo "<th class=\"right\">" . number_format(array_sum($total['cs_reffer'])) . "</th>";
        echo "<th class=\"right\">" . number_format(array_sum($total['cs_done'])) . "</th>";
        echo "<th class=\"right\">" . number_format(@$itm_totals['CYP']) . "</th>";
        echo "<th class=\"right\">" . number_format(@$itm_totals['sales']) . "</th>";
        ?>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="note note-info">
                                * Showing data of reported SDPs.
                           </div>   
                        </div>
                    </div>
                    </div>
        <?php
    } else {
        echo "No record found";
    }
}
// Unset varibles
unset($data, $issue, $items, $whName);
?>
        </div>
    </div>
</div>
            <?php
            //include footer
            include PUBLIC_PATH . "/html/footer.php";
            //include combos
            include ('combos.php');
            ?>

</body>
<script>

<?php
//if (!isset($_POST['submit']) && isset($selProv)){
//    echo 'showDistricts();';
//}

?>
showDistricts();
$('#prov_sel').change(function(e) {
        

        $('#district').html('<option value="">Select</option>');
        showDistricts();
        
        console.log('Pro changed');
        setTimeout(hide_it, 300);
        setTimeout(hide_it, 1000);
        
        function hide_it(){
            console.log('After timeout');
            $("#hf_type_sel").children("option[value='']").wrap('<span/>');
            $("#hf_type_sel").children("option[value='0']").wrap('<span/>');
        }

});
<?php
if (empty($districtId) || $districtId == null) {
?>
     $("#hf_type_sel").children("option[value='']").wrap('<span/>');
     $("#hf_type_sel").children("option[value='0']").wrap('<span/>');
//    $("#hf_type_sel").children("option[value='']").hide();
//    $("#hf_type_sel").children("option[value='0']").hide();
    $("#hf_type_sel").attr('required',true);
<?php
}
else
{
  ?>
    $("#hf_type_sel span").children("option[value='']").unwrap();
    //$("#hf_type_sel span").children("option[value='0']").unwrap();

//    $("#hf_type_sel").children("option[value='']").show();
//    $("#hf_type_sel").children("option[value='0']").show();
    $("#hf_type_sel").attr('required',false);
<?php  
}
?>


    $(function () {

$(document).on('change','#district',function() {
        var dist = $(this).val();
        console.log('val:'+dist);
        if(dist == ''){
            $("#hf_type_sel").children("option[value='']").wrap('<span/>');
            $("#hf_type_sel").children("option[value='0']").wrap('<span/>');
            
//            $("#hf_type_sel").children("option[value='']").hide();
//            $("#hf_type_sel").children("option[value='0']").hide();
            $("#hf_type_sel").attr('required',true);
        }
        else{
            $("#hf_type_sel span").children("option[value='']").unwrap();
            //$("#hf_type_sel span").children("option[value='0']").unwrap();
            
            //$("#hf_type_sel").children("option[value='0']").show();
            $("#hf_type_sel").attr('required',false);
            
        }
    });
      })
</script>
</html>