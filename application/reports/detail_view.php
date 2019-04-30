<?php

/**
 * detail_view
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
$report_id = "SDISTRICTREPORT";
//report title
$report_title = "District Report";

//get param
if (isset($_REQUEST['param']))
{
	$var = explode('|', ($_REQUEST['param']));
	//ind
        $ind = $var[0];
	//year
        $year = $var[1];
	//month
        $month = $var[2];
	//stakeholder
        $stk = $var[3];
	//province
        $prov = $var[4];
	//item
        $itm = $var[5];
	//item name
        $itmName = $var[6];
	//sector
        $sector = !empty($var[7]) ? $var[7] : '';
	//extra
        $extra = !empty($var[8]) ? $var[8] : '';
	
	if ($prov != 'all')
	{
		$provClause = " AND tbl_warehouse.prov_id = '".$prov."' ";
                //select query
		// Get Province name
		$getProv = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $prov"));
		//province 
                $province = $getProv['LocName'];
	}
	else
	{
	//province clause
            $provClause = '';
		//province 
            $province = 'All';
	}
	
	if ($stk != 'all')
	{
		$stkClause = " AND tbl_warehouse.stkid = '".$stk."' ";
                //select query
		// Get stakeholder name
		$getStk = mysql_fetch_array(mysql_query("SELECT
													stakeholder.stkname
												FROM
													stakeholder
												WHERE
													stakeholder.stkid = $stk"));
                 //stakeholder
		$stakeholder = $getStk['stkname'];
	}
	else
	{
		if ($sector == 'private')
		{
                    //stk clause
			$stkClause = ' AND stakeholder.stk_type_id = 1';
                         //stakeholder
			$stakeholder = 'All (Private)';
		}
		else if ($sector == 'public')
		{
		//stk clause	
                    $stkClause = ' AND stakeholder.stk_type_id = 0';
                     //stakeholder
			$stakeholder = 'All (Public)';
		}
		else
		{
                    //stk clause
                    $stkClause = '';
                    //stakeholder
                    $stakeholder = 'All';
		}
	}
	
	if($ind == 1)
	{
            //Consumption
		$indicator = 'Consumption';
                //select query
                //gets
                //total
                //location name
		$qry = "SELECT
					SUM(tbl_wh_data.wh_issue_up) AS total,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					tbl_wh_data.item_id = '".$itm."'
				AND tbl_wh_data.report_month = '".$month."'
				AND tbl_wh_data.report_year = '".$year."'
				AND stakeholder.lvl = 4
				".$provClause."
				".$stkClause."
				GROUP BY
					tbl_warehouse.dist_id
				ORDER BY
					tbl_locations.LocName";
	}
	
	if($ind == 2)
	{
            //Stock on Hand
		$indicator = 'Stock on Hand';
                //select query
                //gets
                //total
                //location name
		$qry = "SELECT
					SUM(tbl_wh_data.wh_cbl_a) AS total,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					tbl_wh_data.item_id = '".$itm."'
				AND tbl_wh_data.report_month = '".$month."'
				AND tbl_wh_data.report_year = '".$year."'
				AND stakeholder.lvl >= 2
				".$provClause."
				".$stkClause."
				GROUP BY
					tbl_warehouse.dist_id
				ORDER BY
					tbl_locations.LocName";
	}
	
	if($ind == 3)
	{
            //CYP
		$indicator = 'CYP';
                //select query
                //gets
                //total
                //location name
		$qry = "SELECT
					SUM(tbl_wh_data.wh_issue_up) AS total,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					tbl_wh_data.item_id = '".$itm."'
				AND tbl_wh_data.report_month = '".$month."'
				AND tbl_wh_data.report_year = '".$year."'
				AND stakeholder.lvl = 4
				".$provClause."
				".$stkClause."
				GROUP BY
					tbl_warehouse.dist_id
				ORDER BY
					tbl_locations.LocName";
	}
	
	if($ind == 4)
	{
            //Received(District)
		$indicator = 'Received(District)';
                //select query
                //gets
                //total
                //location name
		$qry = "SELECT
					SUM(tbl_wh_data.wh_received) AS total,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					tbl_wh_data.item_id = '".$itm."'
				AND tbl_wh_data.report_month = '".$month."'
				AND tbl_wh_data.report_year = '".$year."'
				AND stakeholder.lvl = 3
				".$provClause."
				".$stkClause."
				GROUP BY
					tbl_warehouse.dist_id
				ORDER BY
					tbl_locations.LocName";
	}
	
	if($ind == 5)
	{
            //Received(Field)
		$indicator = 'Received(Field)';
                //select query
                //gets
                //total
                //location name
		$qry = "SELECT
					SUM(tbl_wh_data.wh_received) AS total,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					tbl_wh_data.item_id = '".$itm."'
				AND tbl_wh_data.report_month = '".$month."'
				AND tbl_wh_data.report_year = '".$year."'
				AND stakeholder.lvl = 4
				".$provClause."
				".$stkClause."
				GROUP BY
					tbl_warehouse.dist_id
				ORDER BY
					tbl_locations.LocName";
	}
	//query result
	$qryRes = mysql_query($qry);
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
<!-- BEGIN HEADER -->
<div class="page-container">
    <!-- Content -->
    <div id="content" style="margin-left:0;">
        <table width="100%">
            <tr>
                <td align="right" style="padding-right:5px;">
                    <img style="cursor:pointer;" src="<?php echo PUBLIC_URL;?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                    <img style="cursor:pointer;" src="<?php echo PUBLIC_URL;?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                </td>
            </tr>
            <tr>
                <td><div id="mygrid_container" style="width:100%; height:450px; background-color:white;"></div></td>
            </tr>
        </table>
        <?php
        //counter
    $counter = 1;
    //total
    $total = 0;
    // Create XML
    $xmlstore="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .="<rows>";
    //fetch result
    while ($row = mysql_fetch_array($qryRes))
    {
        //value
        $val = ($ind == 3) ? ($row['total'] * $extra) : $row['total'];
        
        $total += $val;
        //xml
        $xmlstore .= "<row>";
        //increment counter
        $xmlstore .= "<cell>".$counter++."</cell>";
        //location name
        $xmlstore .= "<cell><![CDATA[".$row['LocName']."]]></cell>";
        $xmlstore .= "<cell>".(($ind == 3) ? number_format($val, 3) : number_format($val))."</cell>";
        $xmlstore .= "</row>";
    }
    //xml row
    $xmlstore .= "<row>";
    $xmlstore .= "<cell></cell>";
    $xmlstore .= "<cell style=\"text-align:right\">Total</cell>";
    //total
    $xmlstore .= "<cell>".number_format($total)."</cell>";
    $xmlstore .= "</row>";
    $xmlstore .="</rows>";
    ?>
    </div>
</div>
<?php 
//include reports_includes
include PUBLIC_PATH."/html/reports_includes.php";?>
<script>
	var mygrid;
	function doInitGrid(){
		mygrid = new dhtmlXGridObject('mygrid_container');
		mygrid.selMultiRows = true;
		mygrid.setImagePath("<?php echo PUBLIC_URL;?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
		mygrid.setHeader("<div style='text-align:center;'>District wise <?php echo $indicator;?> for Province '<?php echo $province;?>' Stakeholder '<?php echo $stakeholder;?>' Product '<?php echo $itmName;?>' (<?php echo date('M Y', strtotime($year.'-'.$month));?>),#cspan,#cspan");
		mygrid.attachHeader("Sr. No.,District, <?php echo $indicator;?>");
		mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A');?></div>,#cspan,#cspan");
		mygrid.setInitWidths("60,*,150");
		mygrid.setColAlign("center,left,right");
		mygrid.setColTypes("ro,ro,ro");
		mygrid.enableRowsHover(true,'onMouseOver');
		mygrid.setSkin("light");
		mygrid.init();
		mygrid.clearAll();
		mygrid.loadXMLString('<?php echo $xmlstore;?>');
	}
</script>
</body>
<!-- END BODY -->
</html>