<?php
/**
 * plot_graph
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include  graph_function
include("graph_function.php");
//include FusionChart
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
$_POST = $_REQUEST;
// Print indicator definition
$indicator = $indicatorsDefArr[$_POST['indicator']];
echo "<div style=\"margin-bottom:20px;\" class=\"note note-success\">$indicator</div>";

// When Form is submitted
// Comparison Graph
if (isset($_POST['type']) && $_POST['type'] == 'comparison') {
    //sector
    $sector = $_POST['sector'];
    //stakeholder 
    $stakeholder = $_POST['stakeholder'];
    //compareOption 
    $compareOption = $_POST['compare_option'];
    //indicator 
    $indicator = $_POST['indicator'];
    //province 
    $province = $_POST['province'];
    //district 
    $district = $_POST['district'];
    if ($_POST['graph_type'] == 1) {
        $graphType = 'MSColumn2D.swf';
    } else if ($_POST['graph_type'] == 2) {
        $graphType = 'MSLine.swf';
    } else if ($_POST['graph_type'] == 3) {
        $graphType = 'MSSpline.swf';
    }
    //timeInterval
    $timeInterval = $_POST['time_interval'];
    //dates
    $dates = getInterval($timeInterval);
    //start date
    $startDate = substr($dates[0], 4);
    //end date
    $endDate = substr($dates[1], 4);
    //year select
    $yearSel = $_POST['year'];
    //stakeholder Filter
    $stakeholderFilter = $stakeholderFilter2 = $provinceFilter = $provinceFilter2 = $provinceAll = $provinceJoin = $districtFilter = $districtFilter2 = $districtAll = '';
    //stakeholder Caption
    $stakeholderCaption = $locationCaption = '';

    if ($sector == 'all') {
        $sectorText = 'All Sectors';
    } else if ($sector == 0) {
        $sectorText = 'Public Sector';
    } else if ($sector == 1) {
        $sectorText = 'Private Sector';
    }

    // Table and Column names based on the Compare Options
    if (in_array($compareOption, array(1, 4))) {
        //tableName
        $tableName = 'summary_national';
        //SOHStoreCol
        $SOHStoreCol = "$tableName.soh_national_store";
        //SOHLevelCol
        $SOHLevelCol = "$tableName.soh_national_lvl";
    } else if (in_array($compareOption, array(2, 5, 8))) {
        //tableName
        $tableName = 'summary_province';
        //SOHStoreCol
        $SOHStoreCol = "$tableName.soh_province_store";
        //SOHLevelCol
        $SOHLevelCol = "$tableName.soh_province_lvl";
    } else if (in_array($compareOption, array(3, 6, 9))) {
        //tableName
        $tableName = 'summary_district';
        //SOHStoreCol
        $SOHStoreCol = "$tableName.soh_district_store";
        //SOHLevelCol
        $SOHLevelCol = "$tableName.soh_district_lvl";
    }

    // Apply Filters based on the compare options
    // Sector Filter
    if ($sector != 'all') {
        $sectorFilter = "AND stakeholder.stk_type_id = $sector";
    }

    if (!in_array($compareOption, array(4, 5, 6))) {
        // Stakeholder Filter
        if (!empty($stakeholder) && $stakeholder != 'all') {
            $stakeholderFilter = " AND $tableName.stakeholder_id = " . $stakeholder;
            $stakeholderFilter2 = "  AND tbl_warehouse.stkid = " . $stakeholder;
            $stakeholderCaption = $stakeholderArr[$stakeholder];
        } else {
            $stakeholderCaption = "All Stakeholders";
        }
    }

    if (!in_array($compareOption, array(8, 9))) {
        // Province Filter
        if (in_array($compareOption, array(2, 5))) {
            if (!empty($province) && $province != 'all') {
                //provinceFilter
                $provinceFilter = " AND $tableName.province_id = " . $province;
                $provinceFilter2 = " AND tbl_warehouse.prov_id = " . $province;
                //locationCaption 
                $locationCaption = $locationArr[$province];
            } else {
                //locationCaption 	
                $locationCaption = "All Provinces";
                //provinceJoin	
                $provinceJoin = " INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID";
                //provinceall
                $provinceAll = " AND tbl_locations.ParentID IS NOT NULL";
            }
        }

        // District Filter
        if (in_array($compareOption, array(3, 6))) {
            $districtAll = " AND summary_district.province_id != 10";
            if (!empty($district)) {
                $districtFilter = " AND $tableName.district_id = " . $district;
                $districtFilter2 = " AND tbl_warehouse.dist_id = " . $district;
                $locationCaption = $locationArr[$district];
            } else {
                $locationCaption = "All Districts";
            }
        }
    }

    // Extract data and Pltot Graph
    foreach ($_POST['product_multi'] as $product) {
        //chart id
        $chart_id = "chart" . $product;
        //caption
        $caption = $productsArr['ProductName'][$product];
        //sub caption
        $subCaption = $indicatorsArr[$indicator] . ' &#8594; ' . $compareOptionArr[$compareOption];
        if (!empty($locationCaption)) {
            $subCaption .= ' &#8594; ' . $locationCaption;
        }if (!empty($sectorText)) {
            $subCaption .= ' &#8594; ' . $sectorText;
        }if (!empty($stakeholderCaption)) {
            $subCaption .= ' &#8594; ' . $stakeholderCaption;
        }if (!empty($yearSel) && !in_array($compareOption, array(1, 2, 3))) {
            $subCaption .= ' &#8594; ' . $yearSel;
        }

//download File Name 
        $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d h:i:s');
        if ($indicator == 1) {
            $yAxisName = 'Couples';
        } else if (in_array($indicator, array(2, 3, 7, 8, 9))) {
            $yAxisName = 'Units';
        } else if (in_array($indicator, array(4, 5, 6))) {
            $yAxisName = 'Months';
        }
//xml
        $xmlstore = '';
        $xmlstore .= startXML($caption, $subCaption, $downloadFileName, $yAxisName, $xAxisName);
        $xmlstore .= "<categories>";
        $xmlstore .= drawMonthCategories($timeInterval);
        $xmlstore .= "</categories>";

        if (in_array($compareOption, array(1, 2, 3))) {
            foreach ($_POST['year_multi'] as $year) {
                $xmlstore .= "<dataset seriesName='$year'>";
                //select query
                //gets
                //itm_id
                //itm_name
                //consumption
                //CYP
                //AVG_consumption
                //SOH_level
                //SOH_store
                //SOH_field
                //
                if ($product == '31' || $product == '32') {
                    $qry = "

                            SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                tbl_hf_data.reporting_date,
                                Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS consumption,
                                itminfo_tab.extra AS CYPFactor,
                                '0','0','0','0'
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            WHERE
                                tbl_hf_data.reporting_date BETWEEN '" . $year . $startDate . "' AND '" . $year . $endDate . "' 
                                AND tbl_hf_data.item_id = $product
                                $sectorFilter
                                $stakeholderFilter2
                                $provinceFilter2
                                $districtFilter2
                            GROUP BY
                                tbl_hf_data.reporting_date
                            ORDER BY
                                tbl_hf_data.reporting_date
                        ";
                    //echo $qry;exit;
                } else {
                    $qry = "SELECT
                                    itminfo_tab.itm_id,
                                    itminfo_tab.itm_name,
                                    SUM(IFNULL($tableName.consumption, 0)) AS consumption,
                                    SUM(IFNULL($tableName.consumption, 0)) * itminfo_tab.extra AS CYP,
                                    SUM(IFNULL($tableName.avg_consumption, 0)) AS AVG_consumption,
                                    SUM(IFNULL($SOHLevelCol, 0)) AS SOH_level,
                                    SUM(IFNULL($SOHStoreCol, 0)) AS SOH_store,
                                    SUM(IFNULL($SOHLevelCol, 0)) - SUM(IFNULL($SOHStoreCol, 0)) AS SOH_field
                            FROM
                                    $tableName
                            $provinceJoin
                            INNER JOIN stakeholder ON $tableName.stakeholder_id = stakeholder.stkid
                            INNER JOIN itminfo_tab ON $tableName.item_id = itminfo_tab.itmrec_id
                            WHERE
                                    $tableName.item_id = '" . $productsArr['ProductId'][$product] . "'
                            AND $tableName.reporting_date BETWEEN '" . $year . $startDate . "' AND '" . $year . $endDate . "'
                            $sectorFilter
                            $stakeholderFilter
                            $provinceFilter
                            $provinceAll
                            $districtFilter
                            $districtAll
                            GROUP BY
                                    $tableName.reporting_date
                            ORDER BY
                                    $tableName.reporting_date";
                }
                //query result
                $qryRes = mysql_query($qry);
                //fetch query result
                while ($row = mysql_fetch_array($qryRes)) {
                    $val = getIndicatorValue($row, $indicator);
                    $xmlstore .= "<set value='" . $val . "' />";
                }
                $xmlstore .= "</dataset>";
            }
        } else if (in_array($compareOption, array(4, 5, 6))) {
            foreach ($_POST['stakeholder_multi'] as $stk) {
                $xmlstore .= "<dataset seriesName='" . $stakeholderArr[$stk] . "'>";
                //select query
                //gets
                //itm_id
                //itm_name
                //consumption
                //CYP
                //AVG_consumption
                //SOH_level
                //SOH_store
                //SOH_field
                //
                if ($product == '31' || $product == '32') {
                    $qry = "

                            SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                tbl_hf_data.reporting_date,
                                Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS consumption,
                                itminfo_tab.extra AS CYPFactor,
                                '0','0','0','0'
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            WHERE
                                tbl_hf_data.reporting_date BETWEEN '" . $yearSel . $startDate . "' AND '" . $yearSel . $endDate . "' 
                                AND tbl_hf_data.item_id = $product
                                AND tbl_warehouse.stkid=" . $stk . "
                                $sectorFilter
                                
                                $provinceFilter2
                                $districtFilter2
                            GROUP BY
                                tbl_hf_data.reporting_date
                            ORDER BY
                                tbl_hf_data.reporting_date
                        ";
                    //echo $qry;
                } else {
                    $qry = "SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                SUM(IFNULL($tableName.consumption, 0)) AS consumption,
                                SUM(IFNULL($tableName.consumption, 0)) * itminfo_tab.extra AS CYP,
                                SUM(IFNULL($tableName.avg_consumption, 0)) AS AVG_consumption,
                                SUM(IFNULL($SOHStoreCol, 0)) AS SOH_store,
                                SUM(IFNULL($SOHLevelCol, 0)) AS SOH_level,
                                SUM(IFNULL($SOHLevelCol, 0)) - SUM(IFNULL($SOHStoreCol, 0)) AS SOH_field
                        FROM
                                $tableName
                        $provinceJoin
                        INNER JOIN stakeholder ON $tableName.stakeholder_id = stakeholder.stkid
                        INNER JOIN itminfo_tab ON $tableName.item_id = itminfo_tab.itmrec_id
                        WHERE
                                $tableName.item_id = '" . $productsArr['ProductId'][$product] . "'
                        AND $tableName.reporting_date BETWEEN '" . $yearSel . $startDate . "' AND '" . $yearSel . $endDate . "'
                        AND $tableName.stakeholder_id = " . $stk . "
                        $sectorFilter
                        $stakeholderFilter
                        $provinceFilter
                        $provinceAll
                        $districtFilter
                        $districtAll
                        GROUP BY
                                $tableName.reporting_date
                        ORDER BY
                                $tableName.reporting_date";
                }
                //query result
                $qryRes = mysql_query($qry);
                //fetch result
                while ($row = mysql_fetch_array($qryRes)) {
                    $val = getIndicatorValue($row, $indicator);
                    $xmlstore .= "<set value='" . $val . "' />";
                }
                $xmlstore .= "</dataset>";
            }
        } else if (in_array($compareOption, array(8, 9))) {
            if ($compareOption == 8) {
                $locations = $_POST['province_multi'];
                $locationFilter = " AND summary_province.province_id ";
                $locationFilter2 = " AND tbl_warehouse.prov_id ";
            } else if ($compareOption == 9) {
                $locations = $_POST['district_multi'];
                $locationFilter = " AND summary_district.district_id ";
                $locationFilter2 = " AND tbl_warehouse.dist_id ";
            }
            foreach ($locations as $location) {
                $xmlstore .= "<dataset seriesName='" . $locationArr[$location] . "'>";
                //select query
                //gets
                //itm_id
                //itm_name
                //consumption
                //CYP
                //AVG_consumption
                //SOH_level
                //SOH_store
                //SOH_field
                if ($product == '31' || $product == '32') {
                    $qry = "

                            SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                tbl_hf_data.reporting_date,
                                Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS consumption,
                                itminfo_tab.extra AS CYPFactor,
                                '0','0','0','0'
                            FROM
                                    tbl_warehouse
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            WHERE
                                tbl_hf_data.reporting_date BETWEEN '" . $yearSel . $startDate . "' AND '" . $yearSel . $endDate . "' 
                                AND tbl_hf_data.item_id = $product
                                $locationFilter2 = $location
                                $sectorFilter
                                $stakeholderFilter2
                            GROUP BY
                                tbl_hf_data.reporting_date
                            ORDER BY
                                tbl_hf_data.reporting_date
                        ";
                    //echo $qry;
                } else {
                    $qry = "SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                SUM(IFNULL($tableName.consumption, 0)) AS consumption,
                                SUM(IFNULL($tableName.consumption, 0)) * itminfo_tab.extra AS CYP,
                                SUM(IFNULL($tableName.avg_consumption, 0)) AS AVG_consumption,
                                SUM(IFNULL($SOHStoreCol, 0)) AS SOH_store,
                                SUM(IFNULL($SOHLevelCol, 0)) AS SOH_level,
                                SUM(IFNULL($SOHLevelCol, 0)) - SUM(IFNULL($SOHStoreCol, 0)) AS SOH_field
                        FROM
                                $tableName
                        INNER JOIN stakeholder ON $tableName.stakeholder_id = stakeholder.stkid
                        INNER JOIN itminfo_tab ON $tableName.item_id = itminfo_tab.itmrec_id
                        WHERE
                                $tableName.item_id = '" . $productsArr['ProductId'][$product] . "'
                        AND $tableName.reporting_date BETWEEN '" . $yearSel . $startDate . "' AND '" . $yearSel . $endDate . "'
                        $locationFilter = $location
                        $sectorFilter
                        $stakeholderFilter
                        GROUP BY
                                $tableName.reporting_date
                        ORDER BY
                                $tableName.reporting_date";
                }
                //query result
                $qryRes = mysql_query($qry);
                //fetch result
                while ($row = mysql_fetch_array($qryRes)) {
                    $val = getIndicatorValue($row, $indicator);
                    $xmlstore .= "<set value='" . $val . "' />";
                }
                $xmlstore .= "</dataset>";
            }
        }

        $xmlstore .= endXML();
        ?>
        <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
        <?php
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/" . $graphType, "", $xmlstore, $chart_id, '100%', 500, false, false);
    }
}
// Simple Graph
else if (isset($_POST['type']) && $_POST['type'] == 'simple') {
    //sector 
    $sector = $_POST['sector'];
    //stakeholder 
    $stakeholder = $_POST['stakeholder'];
    //compareOption 
    $compareOption = $_POST['compare_option'];
    //indicator 
    $indicator = $_POST['indicator'];
    //province 
    $province = $_POST['province'];
    //district 
    $district = $_POST['district'];
    //timeInterval 
    $timeInterval = $_POST['time_interval'];
    //dates 
    $dates = getInterval($timeInterval);
    //startDate 
    $startDate = substr($dates[0], 4);
    //endDate 
    $endDate = substr($dates[1], 4);
    //year
    $year = $_POST['year'];

    if ($year == 'all') {
        $yfrom = 2010;
        $yto = date("Y");
    } else {
        $yfrom = $year;
        $yto = $year;
    }

    //check grapg type
    if ($_POST['graph_type'] == 1) {
        $graphType = 'Column2D.swf';
    } else if ($_POST['graph_type'] == 2) {
        $graphType = 'Line.swf';
    } else if ($_POST['graph_type'] == 3) {
        $graphType = 'StackedColumn2D.swf';
    } else if ($_POST['graph_type'] == 4) {
        $graphType = 'Spline.swf';
    }

    if ($sector == 'all') {
        $sectorText = 'All Sectors';
    } else if ($sector == 0) {
        $sectorText = 'Public Sector';
    } else if ($sector == 1) {
        $sectorText = 'Private Sector';
    }
//stakeholderFilter 
    $stakeholderFilter = $provinceFilter = $districtFilter = '';
    //stakeholderCaption 
    $stakeholderCaption = $locationCaption = '';

    // Table and Column names based on the Compare Options
    if ($compareOption == 7) {
        //table name
        $tableName = 'summary_national';
        //SOHStoreCol 
        $SOHStoreCol = "$tableName.soh_national_store";
        //SOHLevelCol 
        $SOHLevelCol = "$tableName.soh_national_lvl";
    } else if ($compareOption == 8) {
        //tableName 
        $tableName = 'summary_province';
        //SOHStoreCol 
        $SOHStoreCol = "$tableName.soh_province_store";
        //SOHLevelCol 
        $SOHLevelCol = "$tableName.soh_province_lvl";
    } else if ($compareOption == 9) {
        //tableName 
        $tableName = 'summary_district';
        //SOHStoreCol 
        $SOHStoreCol = "$tableName.soh_district_store";
        //SOHLevelCol 
        $SOHLevelCol = "$tableName.soh_district_lvl";
    }

    // Apply Filters based on the selected options
    // Sector Filter
    if ($sector != 'all') {
        $sectorFilter = "AND stakeholder.stk_type_id = $sector";
    }
    // Stakeholder Filter
    if (!empty($stakeholder) && $stakeholder != 'all') {
        //stakeholderFilter
        $stakeholderFilter = " AND $tableName.stakeholder_id = " . $stakeholder;
        $stakeholderFilter2 = " AND tbl_warehouse.stkid = " . $stakeholder;
        //stakeholderCaption
        $stakeholderCaption = $stakeholderArr[$stakeholder];
    } else {
        $stakeholderCaption = "All Stakeholders";
    }

    // Province Filter
    if (!empty($province) && $compareOption == 8) {
        //provinceFilter
        $provinceFilter = " AND $tableName.province_id = " . $province;
        $provinceFilter2 = " AND tbl_warehouse.prov_id= " . $province;
        //locationCaption
        $locationCaption = $locationArr[$province];
    }
    // District Filter
    $districtAll = " AND summary_district.province_id != 10";
    if (!empty($district) && $compareOption == 9) {
        //districtFilter
        $districtFilter = " AND $tableName.district_id = " . $district;
        $districtFilter2 = " AND tbl_warehouse.dist_id = " . $district;
        //locationCaption
        $locationCaption = $locationArr[$district];
    }

    // Extract data and Pltot Graph
    if ($indicator != 1) {
        foreach ($_POST['product_multi'] as $product) {
            //echo $product ; continue;
            //chart id
            $chart_id = "chart" . $product;
            //caption 
            $caption = $productsArr['ProductName'][$product];
            //subCaption 
            $subCaption = $indicatorsArr[$indicator] . ' &#8594; ' . $compareOptionArr[$compareOption];
            if (!empty($locationCaption)) {
                //subCaption
                $subCaption .= ' &#8594; ' . $locationCaption;
            }if (!empty($sectorText)) {
                //subCaption
                $subCaption .= ' &#8594; ' . $sectorText;
            }if (!empty($stakeholderCaption)) {
                //subCaption
                $subCaption .= ' &#8594; ' . $stakeholderCaption;
            }
            if (!empty($year)) {
                //sub caption
                if ($yfrom != $yto) {
                    $subCaption .= ' &#8594; ' . $yfrom . "-" . $yto;
                } else {
                    $subCaption .= ' &#8594; ' . $yfrom;
                }
            }
            //downloadFileName 
            $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d h:i:s');
            if ($indicator == 1) {
                $yAxisName = 'Couples';
            } else if (in_array($indicator, array(2, 3, 7, 8, 9))) {
                $yAxisName = 'Units';
            } else if (in_array($indicator, array(4, 5, 6))) {
                $yAxisName = 'Months';
            }
            $xAxisName = 'Months';
//xml
            $xmlstore = '';
            $xmlstore .= startXML($caption, $subCaption, $downloadFileName, $yAxisName, $xAxisName);
            //select query
            //gets
            //item id
            //item name
            //reporting_date
            //consumption
            //CYP
            //AVG_consumption
            //SOH_level
            //SOH_store
            //SOH_field

            if ($year == 'all') {
                $group = "GROUP BY MONTH($tableName.reporting_date)";
            } else {
                $group = "GROUP BY $tableName.reporting_date";
            }

            if ($product == '31' || $product == '32') {
                $qry = "
                    
                        SELECT
                            itminfo_tab.itm_id,
                            itminfo_tab.itm_name,
                            tbl_hf_data.reporting_date,
                            Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS consumption,
                            itminfo_tab.extra AS CYPFactor,
                            '0','0','0','0'
                        FROM
                                tbl_warehouse
                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                        WHERE
                            tbl_hf_data.reporting_date BETWEEN '" . $yfrom . $startDate . "' AND '" . $yto . $endDate . "' 
AND MONTH (tbl_hf_data.reporting_date) <> 0                            
AND tbl_hf_data.item_id = $product
                            $sectorFilter
                            $stakeholderFilter2
                            $provinceFilter2
                            $districtFilter2
                        $group
                        ORDER BY
                            tbl_hf_data.reporting_date
                    ";
            } else {
                $qry = "
                        SELECT
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_name,
                                $tableName.reporting_date,
                                SUM(IFNULL($tableName.consumption, 0)) AS consumption,
                                SUM(IFNULL($tableName.consumption, 0)) * itminfo_tab.extra AS CYP,
                                SUM(IFNULL($tableName.avg_consumption, 0)) AS AVG_consumption,
                                SUM(IFNULL($SOHLevelCol, 0)) AS SOH_level,
                                SUM(IFNULL($SOHStoreCol, 0)) AS SOH_store,
                                SUM(IFNULL($SOHLevelCol, 0)) - SUM(IFNULL($SOHStoreCol, 0)) AS SOH_field
                        FROM
                                $tableName
                        $provinceJoin
                        INNER JOIN stakeholder ON $tableName.stakeholder_id = stakeholder.stkid
                        INNER JOIN itminfo_tab ON $tableName.item_id = itminfo_tab.itmrec_id
                        WHERE
                                $tableName.item_id = '" . $productsArr['ProductId'][$product] . "'
                        AND $tableName.reporting_date BETWEEN '" . $yfrom . $startDate . "' AND '" . $yto . $endDate . "'
                        AND MONTH ($tableName.reporting_date) <> 0
                        $sectorFilter
                        $stakeholderFilter
                        $provinceFilter
                        $districtFilter
                        $group
                        ORDER BY
                                $tableName.reporting_date
                         
                ";
            }
            //query result
            //echo $qry;
            $qryRes = mysql_query($qry);
            //fetch query result
            while ($row = mysql_fetch_array($qryRes)) {
                $val = getIndicatorValue($row, $indicator);
                $xmlstore .= "<set label='" . date('M', strtotime($row['reporting_date'])) . "' value='" . $val . "' />";
            }
            $xmlstore .= endXML();
            ?>
            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
            <?php
            //fusion chart
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/" . $graphType, "", $xmlstore, $chart_id, '100%', 500, false, false);
        }
    } else {
        //caption 
        $caption = "Couple Year Protection";
        //sub caption
        $subCaption = $indicatorsArr[$indicator] . ' &#8594; ' . $compareOptionArr[$compareOption];
        if (!empty($locationCaption)) {
            //sub caption
            $subCaption .= ' &#8594; ' . $locationCaption;
        }if (!empty($stakeholderCaption)) {
            //sub caption
            $subCaption .= ' &#8594; ' . $stakeholderCaption;
        }
        if (!empty($year)) {
            //sub caption
            if ($yfrom != $yto) {
                $subCaption .= ' &#8594; ' . $yfrom . "-" . $yto;
            } else {
                $subCaption .= ' &#8594; ' . $yfrom;
            }
        }
//downloadFileName 
        $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d h:i:s');
        if ($indicator == 1) {
            $yAxisName = 'Couples';
        } else if (in_array($indicator, array(2, 3, 7, 8, 9))) {
            $yAxisName = 'Units';
        } else if (in_array($indicator, array(4, 5, 6))) {
            $yAxisName = 'Months';
        }
        $xAxisName = 'Months';
//xml
        $xmlstore = '';
        $xmlstore .= startXML($caption, $subCaption, $downloadFileName, $yAxisName, $xAxisName);
        $xmlstore .= "<categories>";
        $xmlstore .= drawMonthCategories($timeInterval);
        $xmlstore .= "</categories>";

        if ($year == 'all') {
            $group = "GROUP BY MONTH($tableName.reporting_date)";
        } else {
            $group = "GROUP BY $tableName.reporting_date";
        }


        foreach ($_POST['product_multi'] as $product) {
            $chart_id = "chart" . $product;
            $xmlstore .= "<dataset seriesName='" . $productsArr['ProductName'][$product] . "'>";
            //select query
            //gets
            //item id
            //item name
            //reporting_date
            //consumption
            //CYP
            //AVG_consumption
            //SOH_level
            //SOH_store
            //SOH_field
//            if ($sector == 'all') {
//                $item1 = "CONCAT(
//		itminfo_tab.itm_name,
//		' ',
//	IF (
//		stakeholder.stk_type_id = 0,
//		'Public',
//		'Private'
//	)
//	) itm_name";
//                
//                $group .= ",stakeholder.stk_type_id,summary_national.item_id";
//            } else {
//                $item1 = "itminfo_tab.itm_name";
//            }

            $qry = "SELECT
						itminfo_tab.itm_id,
						itminfo_tab.itm_name,
						$tableName.reporting_date,
						SUM(IFNULL($tableName.consumption, 0)) AS consumption,
						SUM(IFNULL($tableName.consumption, 0)) * itminfo_tab.extra AS CYP,
						SUM(IFNULL($tableName.avg_consumption, 0)) AS AVG_consumption,
						SUM(IFNULL($SOHLevelCol, 0)) AS SOH_level,
						SUM(IFNULL($SOHStoreCol, 0)) AS SOH_store,
						SUM(IFNULL($SOHLevelCol, 0)) - SUM(IFNULL($SOHStoreCol, 0)) AS SOH_field
					FROM
						$tableName
					$provinceJoin
					INNER JOIN stakeholder ON $tableName.stakeholder_id = stakeholder.stkid
					INNER JOIN itminfo_tab ON $tableName.item_id = itminfo_tab.itmrec_id
					WHERE
						$tableName.item_id = '" . $productsArr['ProductId'][$product] . "'
					AND $tableName.reporting_date BETWEEN '" . $yfrom . $startDate . "' AND '" . $yto . $endDate . "'
                                        AND MONTH ($tableName.reporting_date) <> 0
					$sectorFilter
					$stakeholderFilter
					$provinceFilter
					$districtFilter
					$group
					ORDER BY
						$tableName.reporting_date";
            //query result
            //echo $qry;
            $qryRes = mysql_query($qry);
            //fetch result
            while ($row = mysql_fetch_array($qryRes)) {
                $val = getIndicatorValue($row, $indicator);
                $xmlstore .= "<set value='" . $val . "' />";
            }
            $xmlstore .= "</dataset>";
        }
        $xmlstore .= endXML();
        //end xml
        ?>
        <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
        <?php
        //include chart
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/" . $graphType, "", $xmlstore, $chart_id, '100%', 500, false, false);
    }
}