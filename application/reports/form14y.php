<?php
ini_set('max_execution_time', 300);
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$rptId = 'form14y';
$total_out_dist = 0;
$t=0;
$t_u=0;
$t2=0;
$t_u2=0;
$total_dist_array = array();
$total_out_type = 0;
$total_type_array = array();
$usr_col_array=array();
$usr_row_array=array();
$usr_col_array_type=array(); 
$c_type_array=array();
$c_dist_array=array();
$cyp_col_dist=array();
if (isset($_POST['submit'])) {
    //selected month
    //$selMonth = mysql_real_escape_string($_POST['month_sel']);
    //selected year
    //$selYear = mysql_real_escape_string($_POST['year_sel']);
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);

    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    //get reporting date
    //$reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    $reportingDate = "BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";

    $objstk->m_npkId = $stakeholder;
    $rsSql = $objstk->GetStakeholdersById();
    $stk_data = mysql_fetch_assoc($rsSql);
    //echo '<pre>';print_r($stk_data);exit;
    if ($fromDate != $toDate) {
        //reporting period							
        $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
    } else {
        //reporting period	
        $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
    }
    //reporting Date 
    //$reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    //select query
    // Get 
    // Province name
//    $qry = "SELECT
//            prov.LocName AS prov_name,
//            dist.PkLocID AS dist_id,
//            dist.LocName AS dist_name
//            FROM
//            tbl_locations AS prov
//            INNER JOIN tbl_locations AS dist ON dist.ParentID = prov.PkLocID
//            WHERE
//            prov.PkLocID = $selProv ORDER BY
//dist_name ASC
// ";
    
    $qry = "SELECT DISTINCT
	prov.LocName AS prov_name,
	dist.PkLocID AS dist_id,
	dist.LocName AS dist_name
FROM
	tbl_locations AS prov
INNER JOIN tbl_locations AS dist ON dist.ParentID = prov.PkLocID
INNER JOIN tbl_warehouse ON tbl_warehouse.dist_id = dist.PkLocID
INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	prov.PkLocID = $selProv
AND stakeholder.lvl = 3
AND tbl_warehouse.stkid = $stakeholder
ORDER BY
	dist_name ASC";
    $distName = array();
    $res = mysql_query($qry);
    while($row = mysql_fetch_assoc($res))
    {
        $provinceName = $row['prov_name'];
        $distName[$row['dist_id']] = $row['dist_name'];
    }
    
    $qry = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_category,
            itminfo_tab.method_type,
            itminfo_tab.method_rank,
            itminfo_tab.user_factor
            FROM
            itminfo_tab
            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
            WHERE
            itminfo_tab.itm_category in (1,2) AND
            itminfo_tab.method_type IS NOT NULL AND
            stakeholder_item.stkid = $stakeholder
            ORDER BY
            itminfo_tab.method_rank ASC

 ";
    $product = $items = $p_name_id =  array();
    $res = mysql_query($qry);
    while($row = mysql_fetch_assoc($res))
    {
        $product[$row['method_type']][] = $row['itm_name'];
        $items[$row['itm_id']] = $row['itm_name'];
        $p_name_id[$row['itm_name']] = $row['itm_id'];
        $usr_row_array[$row['itm_id']]=$row['user_factor'];
    }
    
    $fileName = 'Form14_' . $provinceName . '_for_' . str_replace(" ", "", str_replace("'", "", str_replace("-", "", $reportingDate)));
}
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
                        <h3 class="page-title row-br-b-wp">Provincial Summary of Contraceptive Performance Delivery Services by Category of Service Outlets</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //include sub_dist_form
                                include('sub_dist_form.php');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                //if submitted
                if (isset($_POST['submit'])) {
                    
                    
                    
                    
                    

                    $qry = " SELECT
                                    tbl_hf_type_data.district_id,
                                    tbl_hf_type_data.facility_type_id,
                                    tbl_hf_type_data.item_id,
                                    tbl_hf_type_data.reporting_date,
                                    Sum(
                                            tbl_hf_type_data.issue_balance
                                    ) AS issuance,
                                    itminfo_tab.itm_name,
                            itminfo_tab.user_factor as u_factor,
                                    itminfo_tab.method_type,
                                    tbl_hf_type.hf_type,
                                    tbl_locations.LocName AS dist_name
                            FROM
                                    tbl_hf_type_data
                            INNER JOIN tbl_locations ON tbl_hf_type_data.district_id = tbl_locations.PkLocID
                            INNER JOIN itminfo_tab ON tbl_hf_type_data.item_id = itminfo_tab.itm_id
                            INNER JOIN tbl_hf_type ON tbl_hf_type_data.facility_type_id = tbl_hf_type.pk_id

                            WHERE
                                    tbl_locations.ParentID =$selProv
                            AND DATE_FORMAT(tbl_hf_type_data.reporting_date,'%Y-%m') $reportingDate
                            AND itminfo_tab.itm_category = 1
                            AND method_type IS NOT NULL
                            AND tbl_hf_type.stakeholder_id = $stakeholder
                            GROUP BY
                                    tbl_hf_type_data.district_id,
                                    tbl_hf_type_data.facility_type_id,
                                    tbl_hf_type_data.item_id,
                                    tbl_hf_type_data.reporting_date
                            ORDER BY
                                    dist_name ASC,
                                    tbl_hf_type.hf_rank ASC,
                                    itminfo_tab.method_rank ASC
                        ";
                   // print_r($qry);
//                    echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    $query_test = "SELECT
                            Count(distinct tbl_warehouse.wh_id) AS total_outlets,
                            tbl_warehouse.dist_id,
                            tbl_warehouse.hf_type_id
                            from tbl_warehouse

                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid

                            WHERE
                            tbl_warehouse.prov_id = $selProv AND
                            tbl_warehouse.stkid = $stakeholder AND
                            tbl_warehouse.reporting_start_month <= '$fromDate" . "-01'" . " AND
                            stakeholder.lvl = 7
                            GROUP BY
                            tbl_warehouse.dist_id,
                            tbl_warehouse.hf_type_id,
                            tbl_warehouse.prov_id,
                            tbl_warehouse.stkid
                            ";
                                                $qryResTest = mysql_query($query_test);
                                                $query  = "SELECT
                            provincial_cyp_factors.cyp_factor as c_factor,
                            provincial_cyp_factors.item_id
                            FROM
                            provincial_cyp_factors
                            WHERE
                            provincial_cyp_factors.stakeholder_id = $stakeholder AND
                            provincial_cyp_factors.province_id = $selProv 

";
                    $result_cyp  = mysql_query($query );
                     // print_r($query);exit;
                    if (mysql_num_rows(mysql_query($qry)) > 0 && mysql_num_rows(mysql_query($query_test)) > 0&& mysql_num_rows(mysql_query($query)) > 0) {
                        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <?php
                            //set items				
                            $hfType = $type_wise_arr = $dist_wise_arr = $dist_name = array();
                            //get results
                            while ($row = mysql_fetch_array($qryRes)) {
                                if (!in_array($row['itm_name'], $items)) {
                                    //$items[$row['item_id']] = $row['itm_name'];
                                    //$product[$row['method_type']][] = $row['itm_name'];
                                }
                                $hfType[$row['facility_type_id']] = $row['hf_type'];
                                //$p_name_id[$row['itm_name']] = $row['item_id'];

                                @$type_wise_arr[$row['facility_type_id']][$row['item_id']] += $row['issuance'];
                                @$dist_wise_arr[$row['district_id']][$row['item_id']] += $row['issuance'];
                                //$val=$row['u_factor']*$row['item_id'];
                                //@$usr_row_array[$row['item_id']]=$row['u_factor'];
                               
                                //@$usr_col_array[$row['item_id']]=  $row['u_factor'];
                               
                            }
                            
                            
                            
                            $qry = " SELECT
                                            tbl_hf_data.item_id,
                                            tbl_warehouse.hf_type_id,
                                            Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS performance,
                                            itminfo_tab.user_factor AS userFactor,
                                            itminfo_tab.extra AS CYPFactor,
                                            itminfo_tab.itm_category,
                                            itminfo_tab.itm_name,
                                            tbl_warehouse.dist_id
                                    FROM
                                            tbl_warehouse
                                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                    INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                    WHERE
                                            tbl_warehouse.prov_id = $selProv
                                            AND tbl_warehouse.stkid = $stakeholder
                                            AND DATE_FORMAT(tbl_hf_data.reporting_date,'%Y-%m') $reportingDate
                                    GROUP BY
                                            tbl_warehouse.dist_id,
                                            tbl_warehouse.hf_type_id,
                                            tbl_hf_data.item_id

                                ";
        //                    echo $qry;exit;
                            $qryRes = mysql_query($qry);
                            while ($row = mysql_fetch_array($qryRes)) {
                                @$type_wise_arr[$row['hf_type_id']][$row['item_id']] += $row['performance'];
                                @$dist_wise_arr[$row['dist_id']][$row['item_id']] += $row['performance'];
                            }

                            
                            
                            
                            
                             //print_r( $usr_row_array_type);
                            while ($row_test = mysql_fetch_array($qryResTest)) {


                                @$outlets_type_wise[$row_test['hf_type_id']] += $row_test['total_outlets'];
                                @$outlets_district_wise[$row_test['dist_id']] += $row_test['total_outlets'];
                            }
                             while ($row_cyp = mysql_fetch_array($result_cyp)) {


                                @$c_type_array[$row_cyp['item_id']] += $row_cyp['c_factor'];
                               // @$c_dist_array[$row_cyp['item_id']] += $row_cyp['c_factor'];
                            }
               
                            //echo '<pre>';print_r($product);print_r($hfType);print_r($p_name_id);exit;
                           //  echo '<pre>';print_r(@$c_type_array); 
                            ?>
                            <table width="100%">
                                <tr>
                                    <td style="padding-top: 10px;" align="center">
                                        <h4 class="center bold">
                                            Monthly Performance Report(Outlet wise)  <?php echo $reportingPeriod; ?><br>
                                            Inrespect of <?= $stk_data['stkname'] ?> <?php echo $provinceName ?>
                                        </h4>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No.</th>
                                                    <th rowspan="2" width="13%">Name of Service Outlet</th>
                                                    <th rowspan="2" width="7%">No. of Outlets</th>
                                                    <?php
                                                    //get product 
                                                    foreach ($product as $proType => $proNames) {
                                                        //check product type								
                                                        if ($proType == 'Condoms') {
                                                            echo "<th colspan=" . sizeof($proNames) . ">$proType</th>";
                                                        } else {
                                                            echo "<th colspan=" . (sizeof($proNames) + 1) . ">$proType</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th rowspan="2">CYP</th>
                                                    <th rowspan="2">Users</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $var = '';
                                                    $count = 1;
                                                    //get product 
                                                    foreach ($product as $proType => $proNames) {
                                                        //get product name
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($items)) . "%'>$name</th>";
                                                        }
                                                        //check province type								
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th width='100'>Total</th>";
                                                        }
                                                        $var = $proType;
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody> 
                                                <?php
                                                $counter = 1;
                                                //hf Type
                                                foreach ($hfType as $id => $hfName) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $hfName; ?></td>
                                                        <td class="center"><?php
                                                            echo $outlets_type_wise[$id];
                                                            $total_out_type += $outlets_type_wise[$id];
                                                            ?></td>
                                                        <?php
                                                        $var = '';
                                                        $count = 1;
                                                        //get product
                                                        foreach ($product as $proType => $proNames) {
                                                            //set method Type Total 	
                                                            $methodTypeTotal = 0;
                                                            foreach ($proNames as $methodName) {
                                                                $prod_id = $p_name_id[$methodName];
                                                                if (!empty($type_wise_arr[$id][$prod_id]))
                                                                    $this_cons = $type_wise_arr[$id][$prod_id];
                                                                else
                                                                    $this_cons = 0;
                                                                $methodTypeTotal = $methodTypeTotal + $this_cons;
                                                                
                                                                @$total_type_array[$prod_id] += $this_cons;
                                                                 
                                                               
                                                                echo "<td class=\"right\">" . number_format($this_cons) . "</td>";
                                                                @$usr_col_array[$prod_id]=$this_cons*$usr_row_array[$prod_id];
                                                                @$c_dist_array[$prod_id]= $this_cons*$c_type_array[$prod_id];
                                                                $t+= $c_dist_array[$prod_id];
                                                                 $t_u+= $usr_col_array[$prod_id];
                                                            }
                                                            //check pro type
                                                            if ($proType != $var && $count > 1) {
                                                                //show metho dType Total	
                                                                echo "<td class=\"right\">" . number_format($methodTypeTotal) . "</td>";
                                                            }
                                                            $var = $proType;
                                                            $count++;
                                                        }
                                                        echo "<th class=\"right\">";
                                                        print_r(number_format(array_sum(@$c_dist_array)));
                                                        echo '</th>';
                                                         echo "<th class=\"right\">";
                                                        print_r(number_format(array_sum($usr_col_array)));
                                                        echo '</th>';
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                               // print_r($t);
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="2">Total</th>

                                                    <th class="center"><?php
                                                        //print_r($outlets_district_wise );
                                                        $sum = 0;
                                                        $count = 1;

                                                        echo $total_out_type;
                                                        ?>
                                                    </th>
                                                    <?php
                                                    $sum_pro = 0;

                                                    foreach ($product as $proType => $proNames) {
                                                        //set method Type Total
                                                        $methodTypeTotal = 0;
                                                        //get product names
                                                        foreach ($proNames as $methodName) {
                                                            //set method Type Total
                                                            $prod_id = $p_name_id[$methodName];
                                                            $methodTypeTotal = $methodTypeTotal + $total_type_array[$prod_id];
                                                            echo "<th class=\"right\">" . number_format($total_type_array[$prod_id]) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            //show method Type Total
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        $var = $proType;
                                                        $count++;
                                                    }
                                                    echo "<th class=\"right\">" .  number_format($t) . "</th>";
                                                    echo "<th class=\"right\">" . number_format($t_u) . "</th>";
                                                    ?>
                                                    </td>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">CYP</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    //get product
                                                    foreach ($product as $proType => $proNames) {
                                                        //set method Type Total
                                                        $methodTypeTotal = 0;
                                                        //get product name
                                                        foreach ($proNames as $methodName) {
                                                            //set method name
                                                             $prod_id = $p_name_id[$methodName];
                                                            $methodTypeTotal = $methodTypeTotal +  ($total_type_array[$prod_id]*@$c_type_array[$prod_id]);
                                                            //show method Type 
                                                            echo "<th class=\"right\">" . number_format( ($total_type_array[$prod_id]*@$c_type_array[$prod_id])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        $var = $proType;
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">Users</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    //get product
                                                    foreach ($product as $proType => $proNames) {
                                                        //method Type Total
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            //set method Type Total
                                                            $prod_id = $p_name_id[$methodName];
                                                            $methodTypeTotal = $methodTypeTotal +  ($total_type_array[$prod_id]*$usr_row_array [$prod_id]) ;
                                                            echo "<th class=\"right\">" . number_format(  $total_type_array[$prod_id]*$usr_row_array [$prod_id]  ) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <h5 style="margin-top:20px;"  class="center bold">
                                            Monthly Performance Report(District wise) <?php echo $reportingPeriod; ?><br>
                                            Inrespect of <?= $stk_data['stkname'] ?> <?php echo $provinceName ?>
                                        </h5>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No.</th>
                                                    <th rowspan="2" width="13%">District</th>
                                                    <th rowspan="2" width="7%">No. of Outlets</th>
                                                    <?php
                                                    //get product
                                                    foreach ($product as $proType => $proNames) {
                                                        if ($proType == 'Condoms') {
                                                            //show product name
                                                            echo "<th colspan=" . sizeof($proNames) . ">$proType</th>";
                                                        } else {
                                                            echo "<th colspan=" . (sizeof($proNames) + 1) . ">$proType</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th rowspan="2">CYP</th>
                                                    <th rowspan="2">Users</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count	
                                                    $count = 1;
                                                    //get product
                                                    foreach ($product as $proType => $proNames) {
                                                        //get product names
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($items)) . "%'>$name</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th width='100'>Total</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $counter = 1;
                                                foreach ($distName as $id => $name) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $name; ?></td>
                                                        <td class="center"><?php
                                                            print_r($outlets_district_wise[$id]);
                                                            $total_out_dist += $outlets_district_wise[$id];
                                                            ?></td>
                                                        <?php
                                                        $var = '';
                                                        $count = 1;
                                                        //get product
                                                        
                                                        foreach ($product as $proType => $proNames) {
                                                            //method Type Total 	
                                                            $methodTypeTotal = 0;
                                                            foreach ($proNames as $methodName) {
                                                                $prod_id = $p_name_id[$methodName];

                                                                if (!empty($dist_wise_arr[$id][$prod_id])) {
                                                                    $this_cons = $dist_wise_arr[$id][$prod_id];
                                                                    
                                                                } else
                                                                    $this_cons = 0;
                                                                
                                                                @$total_dist_array[$prod_id] += $this_cons;
                                                                @$usr_col_array_type[$prod_id]=$this_cons*$usr_row_array[$prod_id];
                                                                @$cyp_col_dist[$prod_id]= $this_cons*$c_type_array[$prod_id];
                                                                $t2+= $cyp_col_dist[$prod_id];
                                                                $t_u2+= $usr_col_array_type[$prod_id];
                                                                $methodTypeTotal = $methodTypeTotal + $this_cons;


                                                                //show method Type Total 	
                                                                echo "<td class=\"right\">" . number_format($this_cons) . "</td>";
                                                            }
                                                            if ($proType != $var && $count > 1) {
                                                                //show method Type Total 
                                                                echo "<td class=\"right\">" . number_format($methodTypeTotal) . "</td>";
                                                            }

                                                            $var = $proType;
                                                            $count++;
                                                        }

                                                        //show cyp
                                                       echo "<th class=\"right\">";
                                                       //if($id == '105')print_r(@$cyp_col_dist);
                                                        echo number_format(array_sum(@$cyp_col_dist));
                                                        echo '</th>';
                                                        //show users
                                                        echo "<th class=\"right\">";
                                                        print_r(number_format(array_sum($usr_col_array_type)));
                                                        echo '</th>';
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                                // print_r($usr_col_array_type);
                                                ?>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="2">Total</th>

                                                    <th class="center"><?php
                                                        //print_r($outlets_district_wise );
                                                        $sum = 0;
                                                        $count = 1;

                                                        echo $total_out_dist;
                                                        ?>
                                                    </th>
                                                    <?php
                                                    $sum_pro = 0;

                                                    foreach ($product as $proType => $proNames) {
                                                        //set method Type Total
                                                        $methodTypeTotal = 0;
                                                        //get product names
                                                        foreach ($proNames as $methodName) {
                                                            //set method Type Total
                                                            $prod_id = $p_name_id[$methodName];
                                                            $methodTypeTotal = $methodTypeTotal + @$total_dist_array[$prod_id];
                                                            echo "<th class=\"right\">" . number_format(@$total_dist_array[$prod_id]) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            //show method Type Total
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        $var = $proType;
                                                        $count++;
                                                    }
                                                     echo "<th class=\"right\">" .  number_format($t2) . "</th>";
                                                    echo "<th class=\"right\">" . number_format($t_u2) . "</th>";
                                                    ?>
                                                    </td>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">CYP</th>
                                                        <?php
                                                        //var
                                                        $var = '';
                                                        //count
                                                        $count = 1;
                                                        //get product
                                                        foreach ($product as $proType => $proNames) {
                                                            //set method Type Total
                                                            $methodTypeTotal = 0;
                                                            //get product name
                                                            foreach ($proNames as $methodName) {
                                                                //set method name
                                                                 $prod_id = $p_name_id[$methodName];
                                                                $methodTypeTotal = $methodTypeTotal + (@$total_dist_array[$prod_id]*@$c_type_array[$prod_id]);
                                                                //show method Type 
                                                                echo "<th class=\"right\">" . number_format(@$total_dist_array[$prod_id]*@$c_type_array[$prod_id]) . "</th>";
                                                            }
                                                            if ($proType != $var && $count > 1) {
                                                                echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                            }
                                                            $var = $proType;
                                                            $count++;
                                                        }
                                                        ?>
                                                </tr>
                                                 <tr>
                                                    <th class="right" colspan="3">Users</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    //get product 
                                                    foreach ($product as $proType => $proNames) {
                                                        //method Type Total
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            //set method Type Total
                                                             $prod_id = $p_name_id[$methodName];
                                                            $methodTypeTotal = $methodTypeTotal + (@$total_dist_array [$prod_id]*$usr_row_array[$prod_id]);
                                                            echo "<th class=\"right\">" . number_format(@$total_dist_array [$prod_id]*$usr_row_array[$prod_id]) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    } 
                                                    //print_r($total_type_array);
                                                    ?>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="note note-info">
                                        * No. of Outlets = Count of total SDPs
                                   </div>   
                                </div>
                            </div>
                            <?php
                        }
                        else
                        {
                            echo 'No record found';
                            
                        }
                    }
// Unset varibles
                    unset($data, $issue, $totalUsers, $totalCYP, $items, $distName, $totalOutlets, $product);
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
        <script>
            $(function () {
                $('#stakeholder').change(function (e) {
                    $('#itm_id, #prov_sel').html('<option value="">Select</option>');

                    showProvinces('');
                });
            })
            function showProvinces(pid) {
                var stk = $('#stakeholder').val();
                if (typeof stk !== 'undefined')
                {
                    $.ajax({
                        url: 'ajax_stk.php',
                        type: 'POST',
                        data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                        success: function (data) {
                            $('#prov_sel').html(data);
                        }
                    })
                }
            }
        </script>
</body>
</html>