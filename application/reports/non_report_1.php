<?php

/**
 * non_report
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
//include FunctionLib
include(APP_PATH."includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH."html/header.php");
//report id
$report_id = "SNONREPDIST";
//action page 
$actionpage = "non_report.php";
//parameters 
$parameters = "TSP";
//dist filter 
$dist_filter = $prov_filter = $stakeHolder = $lvl_type = $rptType = $sel_district = $lastUpdateText = $provinceName = $stakeholderName = '';

// If form is submitted
if(isset($_REQUEST['month_sel']) || isset($_REQUEST['lvl_type']))
{
    //get selected month 
    $sel_month = $_REQUEST['month_sel'];
    //get selected year
    $sel_year = $_REQUEST['year_sel'];
    //get selected stakeholder
    $sel_stk = $_REQUEST['stk_sel'];
    //get selected province
    $sel_prov = $_REQUEST['prov_sel'];
    //get selected district
    $sel_district = $_REQUEST['district'];
    //get selected report type
    $sel_report_type = $_REQUEST['rptType'];
    //get selected level type
    $sel_lvl_type = $_REQUEST['lvl_type'];
    //get report type
    $rptType = $_REQUEST['sector'];
        //warehouse filter
	$wh_filter = '';
        //check selected province
    if($sel_prov != 'all' && $sel_prov != 0){
        //province filter
        $prov_filter = "AND tbl_warehouse.prov_id = ".$sel_prov;
	//warehouse filter	
        $wh_filter[] = "tbl_locations.ParentID = ".$sel_prov;
	}
        //check selected stakeholder
    if($sel_stk != 'all' && $sel_stk != 0){
        //set stakeholder filter
        $stk_filter = "AND tbl_warehouse.stkid = ".$sel_stk;
	//set warehouse filter	
        $wh_filter[] = "warehouses_by_month.stakeholder_id = ".$sel_stk;
	}
        //check selected district
    if($sel_district != ''){
        //set district filter
        $dist_filter = "AND tbl_warehouse.dist_id = ".$sel_district;
        //warehouse filter
		$wh_filter[] = "warehouses_by_month.district_id = ".$sel_district;
	}
	//check sel_lvl_type 
	if ($sel_lvl_type == 'all'){
            //set level filter
		$lvl_filter = ' AND stakeholder.lvl IN (3, 4, 7)';
	//set warehouse filter
                $wh_filter[] = "warehouses_by_month.`level` IN (3, 4, 7)";
	}else if ($sel_lvl_type == 'df'){
            //set level filter
		$lvl_filter = ' AND stakeholder.lvl IN (3, 4)';
		//set warehouse filter
                $wh_filter[] = "warehouses_by_month.`level` IN (3, 4)";
	}else{
            //set level filter
		$lvl_filter = "AND stakeholder.lvl = $sel_lvl_type";
		//set warehouse filter
                $wh_filter[] = "warehouses_by_month.`level` = $sel_lvl_type";
	}
	//check report type
	if ($rptType == 'public' && $sel_stk == 'all') {
        //selected stakeholder
            $sel_stk1 = 'all';
        //set stakeholder type filter
            $stk_type_filter = " AND stakeholder.stk_type_id = 0";
    } else if ($rptType == 'private' && $sel_stk == 'all') {
       //set selected stakeholder
        $sel_stk1 = 'all';
        //set stakeholder type filter
        $stk_type_filter = " AND stakeholder.stk_type_id = 1";
    }
}
else
{
//check date	
    if ( date('d') > 20 )
	{
	//set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
	}
	else
	{
	//set date
            $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
	}
        //set selected month
	$sel_month = date('m', strtotime($date));
	//set selected year
        $sel_year = date('Y', strtotime($date));
        //set selected report type
    $sel_report_type = 'reported';
   //set selected level type
    $sel_lvl_type = '3';
    //check user_stakeholder_type
	if ($_SESSION['user_stakeholder_type'] == 0) {
            //set report type
        $rptType = 'public';
        //set stakeholder type filter 
        $stk_type_filter = " AND stakeholder.stk_type_id = 0";
        //set level stakeholder type 
        $lvl_stktype = 0;
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        //set report type
        $rptType = 'private';
        //set stakeholder type filter 
        $stk_type_filter = " AND stakeholder.stk_type_id = 1";
        //set level stakeholder type
        $lvl_stktype = 1;
    }
    //selected stakeholder
    $sel_stk = empty($_SESSION['user_stakeholder1']) ? 1 : $_SESSION['user_stakeholder1'];
    //selected province
    $sel_prov = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //province filter
    $prov_filter = ($sel_prov != 10) ? (" AND tbl_warehouse.prov_id = ".$sel_prov) : '';
    //stakeholder filter
    $stk_filter = " AND tbl_warehouse.stkid = ".$sel_stk;
    //level filter
    $lvl_filter = ' AND stakeholder.lvl = 3';
    //get report type
    $_POST['rptType'] = 'reported';
    //selected province
	$sel_prov = ($sel_prov != 10) ? $sel_prov : 'all';
}

// Make Query and Execute
$newDate = date('Y-m-16', strtotime("+1 month", strtotime(date($sel_year.'-'.$sel_month))));
//check sel_report_type
if ( isset($sel_report_type) && $sel_report_type == 'ontime' ){
//set reportingAnd 
    $reportingAnd = " AND DATE_FORMAT(tbl_wh_data.add_date, '%Y-%m-%d') <= '$newDate' ";
//set reportingAnd1     
    $reportingAnd1 = " AND DATE_FORMAT(tbl_hf_data.created_date, '%Y-%m-%d') <= '$newDate' ";
}else if ( isset($sel_report_type) && $sel_report_type == 'late' ){
    //set reportingAnd 
    $reportingAnd = " AND DATE_FORMAT(tbl_wh_data.add_date, '%Y-%m-%d') > '$newDate' ";
    //set reportingAnd1
    $reportingAnd1 = " AND DATE_FORMAT(tbl_hf_data.created_date, '%Y-%m-%d') > '$newDate' ";
}
//set reporting Date 
$reportingDate = $sel_year.'-'.str_pad($sel_month, 2, 0, STR_PAD_LEFT).'-01';

//check reporting Date 
if(strtotime($reportingDate) < strtotime('2015-10-01')){
//set reporting Date 	
    $reportingDate = '2015-10-01';
}elseif(strtotime(date('Y-m', strtotime($reportingDate))) >= strtotime(date('Y-m'))){
    //select query
    $getDateQry = "SELECT MAX(warehouses_by_month.reporting_date) AS reportingDate FROM warehouses_by_month";
    //query result
    $getDateQry = mysql_fetch_array(mysql_query($getDateQry));
    //set reporting Date 
    $reportingDate = $getDateQry['reportingDate'];
}else{
//set reporting Date 	
    $reportingDate = $reportingDate;
}
//select query
//gets
//provinceId,
//province,
//districtId,
//district,
//stakeholder Main,
//stakeholder Office,
//warehouse id,
//warehouse name,
//warehouse rank,
//add_date,
//last_update,
//ip address
$qry = "SELECT
			B.provinceId,
			B.province,
			B.districtId,
			B.district,
			B.stkMain,
			B.stkOffice,
			B.wh_id,
			B.wh_name,
			B.wh_rank,
			DATE_FORMAT(A.add_date, '%Y-%m-%d') AS add_date,
			CONCAT(DATE_FORMAT(A.last_update, '%d/%m/%Y'), ' ', TIME_FORMAT(A.last_update, '%h:%i:%s %p'))AS last_update,
			A.ip_address
		FROM
			(
				SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.dist_id,
					tbl_warehouse.prov_id,
					tbl_warehouse.stkid,
					tbl_warehouse.stkofficeid,
					tbl_warehouse.wh_rank,
					tbl_wh_data.add_date,
					tbl_wh_data.last_update,
					tbl_wh_data.ip_address
				FROM
					tbl_warehouse
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_wh_data.report_month = $sel_month
				AND tbl_wh_data.report_year = $sel_year
				AND tbl_wh_data.item_id = 'IT-001'
				$stk_type_filter
				$stk_filter
				$prov_filter
				$dist_filter
				$lvl_filter
				GROUP BY
					tbl_warehouse.wh_id
				UNION
					SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.stkofficeid,
						tbl_warehouse.wh_rank,
						tbl_hf_data.created_date AS add_date,
						tbl_hf_data.last_update,
						tbl_hf_data.ip_address
					FROM
						tbl_warehouse
					INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
					INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						MONTH(tbl_hf_data.reporting_date) = $sel_month
					AND YEAR(tbl_hf_data.reporting_date) = $sel_year
					AND tbl_hf_data.item_id = 1
					$stk_type_filter
					$stk_filter
					$prov_filter
					$dist_filter
					$lvl_filter
					GROUP BY
						tbl_warehouse.wh_id
			) A
		RIGHT JOIN (
			SELECT
				DISTINCT
				tbl_warehouse.wh_id,
				tbl_warehouse.wh_name,
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_warehouse.stkofficeid,
				tbl_warehouse.wh_rank,
				MainStk.stkorder,
				MainStk.stkname AS stkMain,
				stakeholder.stkname AS stkOffice,
				District.PkLocID AS districtId,
				District.LocName AS district,
				Province.PkLocID AS provinceId,
				Province.LocName AS province
			FROM
				tbl_warehouse
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN stakeholder AS MainStk ON tbl_warehouse.stkid = MainStk.stkid
			INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
			INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
			WHERE
				tbl_warehouse.wh_id NOT IN (
					SELECT
						warehouse_status_history.warehouse_id
					FROM
						warehouse_status_history
					INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
					WHERE
						warehouse_status_history.reporting_month = '$reportingDate'
					AND warehouse_status_history.`status` = 0
					$stk_type_filter
					$stk_filter
				)
				$stk_type_filter
				$stk_filter
				$prov_filter
				$dist_filter
				$lvl_filter
		) B ON A.wh_id = B.wh_id
		AND A.prov_id = B.prov_id
		AND A.dist_id = B.dist_id
		AND A.stkid = B.stkid
		AND A.stkofficeid = B.stkofficeid
		ORDER BY
			B.provinceId ASC,
			B.district ASC,
			B.stkorder ASC,
			IF(A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
			A.wh_rank,
			A.wh_name ASC";
//query result
$qryRes = mysql_query($qry);
//count
$count = 1;
//data
$data = array();
//total warehouse
$totalWH = 0;
//on time
$ontime = 0;
//late
$late = 0;
//reported All 
$reportedAll = 0;
//non Reported 
$nonReported = 0;
//query result
while ( $row = mysql_fetch_assoc($qryRes) )
{
//total warehouse	
    $totalWH++;
    // Reported Count (Ontime and Late)
	if(!empty($row['add_date'])){ 
		$data['reported'][] = $row;
		$reportedAll++;
	}
        echo "Add date: ".$row['add_date']."<br>";
        echo "New date: ".$newDate."<br>";
	// Ontime Reported
	if(!empty($row['add_date']) && $row['add_date'] < $newDate){
		$data['ontime'][] = $row;
		$ontime++;
	}
        // Late Reported
        else if(!empty($row['add_date']) && $row['add_date'] >= $newDate){
		$data['late'][] = $row;
		$late++;
	}
	
	if(empty($row['add_date'])){ // Non-Reported Count
		$data['non-reported'][] = $row;
		$nonReported++;
	}
}

// Create XML for the Grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
if ( $sel_report_type == 'reported' ){
	$dataArr = $data['reported'];
        //report title
	$report_title = "All Reported Stores/Facilities Report for";
	if($row['add_date'] < $newDate){
		$color = 'color:#009900;';
		$rpt_status = 'On-time';
	}else{
		$color = 'color:#FF0000;';
		$rpt_status = 'Late';
	}
} else if ( $sel_report_type == 'ontime' ){
	$dataArr = $data['ontime'];
	//report title
        $report_title = "On-time Reported Stores/Facilities Report for";
	//color 
        $color = 'color:#009900;';
    $rpt_status = 'Late';
} else if ( $sel_report_type == 'late' ){
	$dataArr = $data['late'];
	//report title
        $report_title = "Late Reported Stores/Facilities Report for";
	//color 
        $color = 'color:#FF0000;';
    $rpt_status = 'Late';
} else if ( $sel_report_type == 'non-reported' ){
	$dataArr = $data['non-reported'];
	//report title
        $report_title = "Non-reported Stores/Facilities Report for";
	//color 
        $color = 'color:#FF0000;';
    $rpt_status = 'Non-reported';
}

$num = count($dataArr);

foreach( $dataArr as $row )
{
//stakeholder name	
    $stakeholderName = $row['stkMain'];
    //province Name 
	$provinceName = $row['province'];
	
	if ( $sel_report_type == 'reported' ){
		if($row['add_date'] < $newDate){
                    //color
                    $color = 'color:#009900;';
			$rpt_status = 'On-time';
		}else{
		//color	
                    $color = 'color:#FF0000;';
			$rpt_status = 'Late';
		}
	}else if ( $sel_report_type == 'ontime' ){
	//color	
            $color = 'color:#009900;';
		$rpt_status = 'On-time';
	}else if ( $sel_report_type == 'late' ){
	//color	
            $color = 'color:#FF0000;';
		$rpt_status = 'Late';
	}else if ( $sel_report_type == 'non-reported' ){
	//color	
            $color = 'color:#FF0000;';
		$rpt_status = 'Non-reported';
	}
	
	$xmlstore .= "<row>";
	$xmlstore .= "<cell>". $count++ ."</cell>";
	//province
        $xmlstore .= "<cell><![CDATA[".$row['province']."]]></cell>";
	//district
        $xmlstore .= "<cell><![CDATA[".$row['district']."]]></cell>";
	//stakeholder Main
        $xmlstore .= "<cell><![CDATA[".$row['stkMain']."]]></cell>";
	//stakeholder Office
        $xmlstore .= "<cell><![CDATA[".$row['stkOffice']."]]></cell>";
    if ($sel_report_type != 'non-reported')
    {
        $tempVar = "\"$row[wh_id]&month=$sel_month&year=$sel_year\"";
        $xmlstore .="<cell><![CDATA[<a href=javascript:showData($tempVar)>".$row['wh_name']."</a>]]>^_self</cell>";
    }
    else
    {
        //warehouse name
        $xmlstore .="<cell><![CDATA[".$row['wh_name']."]]></cell>";
    }
	//last_update
    $xmlstore .= "<cell><![CDATA[".$row['last_update']."]]></cell>";
//ip_address
    $xmlstore .= "<cell><![CDATA[".$row['ip_address']."]]></cell>";
//status
    $xmlstore .="<cell style=\"$color\"><![CDATA[".$rpt_status."]]></cell>";

    $xmlstore .= "</row>";
}
$xmlstore .= "</rows>";
//province Name
$provinceName = ($sel_prov == 'all') ? 'All' : $provinceName;
//stakeholder name
$stakeholderName = ($sel_stk1 == 'all') ? 'All' : $stakeholderName;
//check sel level type
if($sel_lvl_type == 3){
    //set store type
	$storeType = 'District Stores';
}elseif($sel_lvl_type == 4){
//set store type
    $storeType = 'Field Stores';
}elseif($sel_lvl_type == 7){
//set store type
    $storeType = 'Health Facilities';
}elseif($sel_lvl_type == 'all'){
//set store type
    $storeType = 'Stores/Facilities';
}elseif($sel_lvl_type == 'df'){
//set store type
    $storeType = 'District and Field Stores';
}
?>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
<!-- BEGIN HEADER -->
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
                	<table width="100%">
                        <tr>
                            <td><?php 
                            //include reportheader
                            include(APP_PATH."includes/report/reportheader.php");?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                	<div class="col-md-4">
                    	<h4>Total <?php echo $storeType;?>: <?php echo $totalWH;?></h4>
                    </div>
                    <?php
					$nonRptPer = number_format(($nonReported / $totalWH) * 100, 2);
					$rptAllPer = number_format(($reportedAll / $totalWH) * 100, 2);
					$onTimePer = number_format(($ontime / $totalWH) * 100, 2);
					$latePer = number_format(($late / $totalWH) * 100, 2);
					if ( isset($sel_report_type) && $sel_report_type == 'non-reported' )
					{
					?>
                    <div class="col-md-4">
                    	<h4>Non Reported <?php echo $storeType;?>: <?php echo $nonReported;?></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Non Reporting Rate: <?php echo ($nonRptPer > 100) ? 100 : $nonRptPer;?>%</h4>
                    </div>
					<?php
					}
					if ( isset($sel_report_type) && $sel_report_type == 'reported' )
					{
					?>
                    <div class="col-md-4">
                    	<h4>Reported <?php echo $storeType;?>: <?php echo $reportedAll;?></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Reporting Rate: <?php echo ($rptAllPer > 100) ? 100 : $rptAllPer;?>%</h4>
                    </div>
					<?php
					}
					if ( isset($sel_report_type) && $sel_report_type == 'ontime' )
					{
					?>
                    <div class="col-md-5">
                    	<h4>On-time Reported <?php echo $storeType;?>: <?php echo $ontime;?></h4>
                    </div>
                    <div class="col-md-3">
                        <h4>On-time Reporting Rate: <?php echo ($onTimePer > 100) ? 100 : $onTimePer;?>%</h4>
                    </div>
					<?php
					}
					if ( isset($sel_report_type) && $sel_report_type == 'late' )
					{
					?>
                    <div class="col-md-4">
                    	<h4>Late Reported <?php echo $storeType;?>: <?php echo $late;?></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Late Reporting Rate: <?php echo ($latePer > 100) ? 100 : $latePer;?>%</h4>
                    </div>
					<?php
					}
					?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    
                    <?php 
					if($num > 0)
					{
					?>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
							<td align="right" style="padding-right:5px;">
								<img style="cursor:pointer;" src="<?php echo PUBLIC_URL;?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
								<img style="cursor:pointer;" src="<?php echo PUBLIC_URL;?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
							</td>
                        </tr>
                        <tr>
                            <td><div id="mygrid_container" style="width:100%; height:470px;"></div></td>
                        </tr>
                    </table>
                    <?php 
					}
					else
                    {
                        echo "No record found.";
                    }
					?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include PUBLIC_PATH."/html/footer.php";?>
<?php include PUBLIC_PATH."/html/reports_includes.php";?>
<script>
	$(function(){
		$('#districts_td_id').hide();
		
		$('#sector').change(function(e) {
			var val = $('#sector').val();
			getStakeholder(val, '');
		});
		getStakeholder('<?php echo $rptType;?>', '<?php echo $sel_stk;?>');
	})
	function getStakeholder(val, stk)
	{
		$.ajax({
			url: 'ajax_stk.php',
			data: {type:val, stk: stk},
			type: 'POST',
			success: function(data){
				$('#stk_sel').html(data)
			}
		})
	}
	$(function(){
		showDistricts('<?php echo $sel_district;?>');
		$('#prov_sel').change(function(e) {
			showDistricts('');
		});
	})
	function showDistricts(dId)
	{
		var provinceId = $('#prov_sel').val();
		$.ajax({
			url: 'ajax_calls.php',
			type: 'POST',
			data: {provinceId: provinceId, validate: 'no', dId: dId, stkId: $('#stk_sel').val()},
			success: function(data){
				$('#districtsCol').html(data);
			}
		});
	}
</script> 
<script>
    var mygrid;
    function doInitGrid(){
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
	mygrid.setImagePath("<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center;'><?php echo "$report_title Stakeholder = $stakeholderName And Province/Region = $provinceName (".date('F',mktime(0,0,0,$sel_month)).' '.$sel_year.")";?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
        mygrid.attachHeader("S. No., Province/Region, District, Stakeholder, Warehouse/Store Type, Warehouse/Store Name, Last Updated, IP Address, Status");
        mygrid.attachHeader(",#select_filter,#select_filter,#select_filter,#select_filter,,,,");
        mygrid.attachFooter("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'>Late reporting means reported after 15th of the reporting month</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
	mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText;?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
        mygrid.setInitWidths("50,120,110,90,150,*,160,100,80");
        mygrid.setColAlign("center,left,left,left,left,left,left,center");
        mygrid.setColSorting("int,str,str,str,str,str,str,str,str");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
	mygrid.enableMultiline(true);
        mygrid.enableRowsHover(true,'onMouseOver');   // `onMouseOver` is the css class name.
        mygrid.setSkin("light");
        mygrid.init();
	mygrid.clearAll();
	mygrid.loadXMLString('<?php echo $xmlstore;?>');
    }

    function functionCall(ip)
    {
        window.open('ip_info.php?ip='+ip, '_blank', 'scrollbars=1,width=650,height=600');
    }

    function showData(p)
    {
        window.open('wh_info.php?whId='+p, '_blank', 'scrollbars=1,width=900,height=500');
    }
</script>
</body>
<!-- END BODY -->
</html>