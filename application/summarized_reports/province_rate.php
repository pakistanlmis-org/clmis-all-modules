<?php
$report_id = "PROVINCERRREPORT";
//check date
if (date('d') > 10) {
    //set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
//set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//selected month
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
//month
$month = isset($_REQUEST['ending_month']) ? $_REQUEST['ending_month'] : $sel_month;
//year
$year = isset($_REQUEST['year_sel']) ? $_REQUEST['year_sel'] : $sel_year;
//select query
//pk location id
//location name
//PWDpct_D
//PWDpct_F
//PWDTotal
//LHWpct_D
//LHWpct_F
//LHWTotal
//DOHpct_D
//DOHpct_F
//DOHTotal
$query = "SELECT
			tbl_locations.PkLocID,
			tbl_locations.LocName,
			COALESCE(ROUND((PWDRpt_D/PWD_D) * 100, 2), NULL, 0) AS PWDpct_D,
			COALESCE(ROUND((PWDRpt_F/PWD_F) * 100, 2), NULL, 0) AS PWDpct_F,
			COALESCE(ROUND((PWDRpt_D/PWD_D * 100 + PWDRpt_F/PWD_F * 100)/2, 2), NULL, 0) AS PWDTotal,
			COALESCE(ROUND((LHWRpt_D/LHW_D) * 100, 2), NULL, 0) AS LHWpct_D,
			COALESCE(ROUND((LHWRpt_F/LHW_F) * 100, 2), NULL, 0) AS LHWpct_F,
			COALESCE(ROUND((LHWRpt_D/LHW_D * 100 + LHWRpt_F/LHW_F * 100)/2, 2), NULL, 0) AS LHWTotal,
			COALESCE(ROUND((DOHRpt_D/DOH_D) * 100, 2), NULL, 0) AS DOHpct_D,
			COALESCE(ROUND((DOHRpt_F/DOH_F) * 100, 2), NULL, 0) AS DOHpct_F,
			COALESCE(ROUND((DOHRpt_D/DOH_D * 100 + DOHRpt_F/DOH_F * 100)/2, 2), NULL, 0) AS DOHTotal
		FROM (
			SELECT
				tbl_warehouse.prov_id,
				Sum(IF(tbl_warehouse.stkid = 1 AND stakeholder.lvl = 3, 1, 0)) AS PWD_D,
				Sum(IF(tbl_warehouse.stkid = 2 AND stakeholder.lvl = 3, 1, 0)) AS LHW_D,
				Sum(IF(tbl_warehouse.stkid = 7 AND stakeholder.lvl = 3, 1, 0)) AS DOH_D,
				Sum(IF(tbl_warehouse.stkid = 1 AND stakeholder.lvl = 4, 1, 0)) AS PWD_F,
				Sum(IF(tbl_warehouse.stkid = 2 AND stakeholder.lvl = 4, 1, 0)) AS LHW_F,
				Sum(IF(tbl_warehouse.stkid = 7 AND stakeholder.lvl = 4, 1, 0)) AS DOH_F
			FROM
				stakeholder
			INNER JOIN tbl_warehouse ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			WHERE
				stakeholder.lvl IN(3, 4)
			GROUP BY
				tbl_warehouse.prov_id
			) A
			JOIN
			(SELECT
				tbl_warehouse.prov_id,
				Sum(IF(tbl_warehouse.stkid = 1 AND stakeholder.lvl = 3, 1, 0)) AS PWDRpt_D,
				Sum(IF(tbl_warehouse.stkid = 2 AND stakeholder.lvl = 3, 1, 0)) AS LHWRpt_D,
				Sum(IF(tbl_warehouse.stkid = 7 AND stakeholder.lvl = 3, 1, 0)) AS DOHRpt_D,
				Sum(IF(tbl_warehouse.stkid = 1 AND stakeholder.lvl = 4, 1, 0)) AS PWDRpt_F,
				Sum(IF(tbl_warehouse.stkid = 2 AND stakeholder.lvl = 4, 1, 0)) AS LHWRpt_F,
				Sum(IF(tbl_warehouse.stkid = 7 AND stakeholder.lvl = 4, 1, 0)) AS DOHRpt_F
			FROM
				stakeholder
			INNER JOIN tbl_warehouse ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			WHERE
				stakeholder.lvl IN(3, 4) AND
				tbl_wh_data.report_month = $month AND
				tbl_wh_data.report_year = $year AND
				tbl_wh_data.item_id = 'IT-001'
			GROUP BY
				tbl_warehouse.prov_id
			) B
			ON A.prov_id = B.prov_id
			RIGHT JOIN tbl_locations ON tbl_locations.PkLocID = A.prov_id
			WHERE tbl_locations.LocLvl = 2
				AND ParentID IS NOT NULL";
//query result
$rs_query = mysql_query($query);
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
//counter
$counter = 1;
//fetch results
while ($rsPro = mysql_fetch_array($rs_query)) {

    $xmlstore .= "<row>";
    //counter
    $xmlstore .= "<cell>" . $counter . "</cell>";
    //
    $xmlstore .= "<cell>$rsPro[LocName]</cell>";
    //PWDpct_D
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[PWDpct_D]</cell>";
    //PWDpct_F
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[PWDpct_F]</cell>";
    //PWDTotal
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[PWDTotal]</cell>";
    //LHWpct_D
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[LHWpct_D]</cell>";
    //LHWpct_F
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[LHWpct_F]</cell>";
    //LHWTotal
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[LHWTotal]</cell>";
    //DOHpct_D
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[DOHpct_D]</cell>";
    //DOHpct_F
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[DOHpct_F]</cell>";
    //DOHTotal
    $xmlstore .= "<cell style=\"text-align:right;\">$rsPro[DOHTotal]</cell>";
    //
    $xmlstore .= "</row>";
    $counter++;
}

$xmlstore .= "</rows>";
?>
</head>
<!-- END HEAD -->

<div onLoad="doInitGrid()">
   
                
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                            <tr>
                                <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                            </tr>
                            <tr>
                                <td><div id="mygrid_container" style="width:100%; height:330px;"></div></td>
                            </tr>
                        </table>
                    </div>
                </div>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Provincial - Reporting Rate " . ' (' . date('F', mktime(0, 0, 0, $month)) . ' ' . $year . ")"; ?> </div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("<div title='Serial Number'>Sr. No.</div>,<div title='Province Name'>Province</div>,<div title='PWD' style='text-align:center;'>PWD</div>,#cspan,#cspan,<div title='LHW' style='text-align:center;'>LHW</div>,#cspan,#cspan,<div title='Department of Health' style='text-align:center;'>DOH</div>,#cspan,#cspan");
            mygrid.setColAlign("center,left,center,center,center,center,center,center,center,center,center");
            mygrid.attachHeader("#rspan,#rspan,District,Field,Total,District,Field,Total,District,Field,Total");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,90,90,90,90,90,90,90,90,90");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
</div>