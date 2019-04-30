<?php

//report id 
$report_id = "SNASUM";
//report title 
$report_title = "National Report for";
//action page 
$actionpage = "";
//parameters 
$parameters = "TS";
//parameters width
$parameter_width = "40%";
//if submitted
if (isset($_REQUEST['go'])) {

    if (isset($_REQUEST['month_sel']) && !empty($_REQUEST['month_sel'])) {
        $sel_month = $_REQUEST['month_sel'];
    }

    if (isset($_REQUEST['year_sel']) && !empty($_REQUEST['year_sel'])) {
        $sel_year = $_REQUEST['year_sel'];
    }

    if (isset($_REQUEST['stk_sel']) && !empty($_REQUEST['stk_sel'])) {
        $sel_stk = $_REQUEST['stk_sel'];
    }

    $sector = $_REQUEST['sector'];
    $rptType = $sector;
    if ($sector == 'Public' || $sector == 'public') {
        $lvl_stktype = 0;
    } else {
        $lvl_stktype = 1;
    }
} else {

    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    $sel_month = date('m', strtotime($date));
    $sel_year = date('Y', strtotime($date));
    if ($_SESSION['user_stakeholder_type'] == 0) {
        $sector = 'public';
        $rptType = 'public';
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        $sector = 'private';
        $rptType = 'private';
    } else {
        $sector = 'public';
        $rptType = 'public';
    }
    $sel_stk = 0;
    if ($sector == 'Public' || $sector == 'public') {
        $lvl_stktype = 0;
    } else {
        $lvl_stktype = 1;
    }
}

if ($rptType == 'all') {
    $stk = 0;
    //set filter
    $filter = '';
} else if ($rptType == 'public') {
    $stk = 0;
    //set filter
    $filter = ' AND stakeholder.stk_type_id = 0';
} else if ($rptType == 'private') {
    $stk = 0;
    //set filter
    $filter = ' AND stakeholder.stk_type_id = 1';
}

$reportingDate = $sel_year . '-' . $sel_month . '-01';
//select query
$qry = "SELECT
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_name,
                SUM(summary_national.consumption) AS consumption,
                SUM(summary_national.avg_consumption) AS avg_consumption,
                SUM(summary_national.soh_national_lvl) AS SOH,
                (SUM(summary_national.soh_national_lvl) / SUM(summary_national.avg_consumption)) AS MOS,
                (SUM(summary_national.consumption) * itminfo_tab.extra) AS CYP
        FROM
                summary_national
        INNER JOIN itminfo_tab ON summary_national.item_id = itminfo_tab.itmrec_id
        INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
        WHERE
                summary_national.reporting_date = '$reportingDate'
        AND itminfo_tab.itm_category = 1
        $filter
        GROUP BY
                itminfo_tab.itmrec_id
        ORDER BY
                itminfo_tab.frmindex ASC";
//query result
//  echo $qry;exit;
$qryRes = mysql_query($qry);
//num of result
$num = mysql_num_rows(mysql_query($qry));
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    //item name
    $xmlstore .= "<cell>" . $row['itm_name'] . "</cell>";
    //consumption
    $xmlstore .= "<cell>" . number_format($row['consumption']) . "</cell>";
    //average consumption
    $xmlstore .= "<cell>" . number_format($row['avg_consumption']) . "</cell>";
    //SOH
    $xmlstore .= "<cell>" . number_format($row['SOH']) . "</cell>";
    //MOS
    $xmlstore .= "<cell>" . number_format($row['MOS'], 1) . "</cell>";

    $m_q="SELECT getMosColor('" . $row['MOS'] . "', '" . $row['itmrec_id'] . "', '" . $stk . "', 1)";
    $rs_mos = mysql_query($m_q);
    $bgcolor = mysql_result($rs_mos, 0, 0);

    $xmlstore .= "<cell><![CDATA[<div style=\"width:10px; height:12px; background-color:$bgcolor;\"></div>]]></cell>";
    $xmlstore .= "<cell>" . number_format($row['CYP']) . "</cell>";
    $xmlstore .= "</row>";
}
$xmlstore .= "</rows>";
//end xml
?>

<div class="" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%">
                            
                            <?php
                            //check num
                            if ($num > 0) {
                                ?>
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:320px;"></div></td>
                                </tr>
                                <?php
                            } else {
                                echo "<tr><td>No record found</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
              
    </div>

    <!-- END FOOTER -->
   
    <script>
        $(function() {
            $('#sector').change(function(e) {
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');
        })
    </script> 
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "National Report - " . ucwords($rptType) . " Sector" . ' Stakeholders (' . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?> </div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,Product, Consumption, AMC, Stock On Hand, <div style='text-align: center;'>Month of Stock</sdiv>,#cspan, CYP");
            mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,160,160,160,60,40,80");
            mygrid.setColAlign("center,left,right,right,right,center,center,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script> 
    <!-- END JAVASCRIPTS -->
</div>