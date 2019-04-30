<?php
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

$month=$year='';
if(!empty($_REQUEST['month']))      $month = $_REQUEST['month'];
if(!empty($_REQUEST['year']))       $year = $_REQUEST['year'];

$last_date  = date("Y-m-t", strtotime($year."-".$month."-01"));   
//echo $last_date;exit;
$months_list[] = $year."-".$month."-01";
$to_mon = (sprintf("%02d", $month)).'-'.$year;
$from_mon = date('M-Y', strtotime('01-'.$to_mon.'-2 months'));
$to_mon1 = date('M-Y',strtotime('01-'.$to_mon));

$integ_stk_arr = array();
foreach($_SESSION['sub_stakeholders'] as $sub_id => $sub_name)
{
    $integ_stk_arr[$sub_id] = $sub_id;
}


$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            funding_stk_prov.province_id = $province";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}


?>
    <script>
     function printContents() {
            var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
            var printingContents = document.getElementById("printing").innerHTML;

            var docprint = window.open("", "", printing);

            docprint.document.open();
            docprint.document.write('<html><head><title style="font:16px">Provincial Distribution Plan</title>');

            docprint.document.write('</head><body onLoad="self.print()"><center>');
            docprint.document.write(printingContents);
            
            docprint.document.write('</center>');

            docprint.document.write('</body></html>');
            docprint.document.close();
            docprint.focus();   
        }
    </script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
<?php
$qry = "
   SELECT
        clr_distribution_plans.pk_id as plan_id,
        clr_distribution_plans.plan_number,
        clr_distribution_plans.plan_status,
        clr_distribution_plans.`month`,
        clr_distribution_plans.`year`,
        clr_distribution_plans_stk.stk_id,
        clr_master.pk_id,
        clr_master.requisition_num,
        clr_master.approval_status,
        itminfo_tab.itm_name,
        clr_details.itm_id,
        sum(clr_details.qty_req_prov) as qty,
        dist.LocName,
        pro.LocName as province_name,
        tbl_warehouse.dist_id
    FROM
    clr_distribution_plans
    INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
    INNER JOIN clr_master ON clr_master.distribution_plan_id = clr_distribution_plans.pk_id
    INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
    INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
    INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
    INNER JOIN tbl_locations dist ON tbl_warehouse.dist_id = dist.PkLocID
    INNER JOIN tbl_locations pro ON clr_distribution_plans.prov_id = pro.PkLocID
    WHERE

        clr_distribution_plans_stk.stk_id IN (".implode(',',$integ_stk_arr).") AND
        clr_distribution_plans.`year` = $year AND
        clr_distribution_plans.`month` = $month AND
        clr_distribution_plans.plan_status = 'Submitted' AND
        clr_distribution_plans.prov_id = ".$_SESSION['user_province1']."
    GROUP BY
        clr_distribution_plans_stk.stk_id,
        tbl_warehouse.dist_id,
        clr_details.itm_id
    order BY
        clr_distribution_plans_stk.stk_id,
        tbl_warehouse.dist_id,
        clr_details.itm_id
        
    ";
    //echo $qry;exit;
    $res = mysql_query($qry);
    $plan_arr=$stk_arr=$disp_data=$district_arr=$total_data=array();

    
    while($row = mysql_fetch_assoc($res))
    {
        //print_r($row);
        $disp_data[$row['dist_id']][$row['stk_id']][$row['itm_id']]=$row['qty'];
        $district_arr[$row['dist_id']]=$row['LocName'];
        $stk_arr[$row['stk_id']]=$row['LocName'];
        $plan_arr[$row['stk_id']][$row['plan_id']]=$row['plan_number'];
        $province_name= $row['province_name'];
        
        if(!isset($total_data[$row['stk_id']][$row['itm_id']])) 
            $total_data[$row['stk_id']][$row['itm_id']]=0;
        
        $total_data[$row['stk_id']][$row['itm_id']]+=$row['qty'];
    }
    
//echo '<pre>';print_r($disp_data);exit;
    
if(!isset($_SESSION['stk_items']))
{    
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
                        stakeholder_item.stkid in (".implode(',',$integ_stk_arr).")
                AND itm_category = 1
                ORDER BY
                stakeholder_item.stkid,
                        itminfo_tab.method_rank,
                        itminfo_tab.itm_id ASC
                ";
    $res= mysql_query($qry_itm);
    $products=array();
    //print_r($_SESSION);
    while($row= mysql_fetch_assoc($res))
    {
        $products[$row['stkid']][$row['itm_id']] = $row['itm_name'];
    }
    $_SESSION['stk_items']=$products;
}
$products  = $_SESSION['stk_items'];

//    echo '<pre>';
//    print_r($integ_stk_arr);
//    print_r($products);
//    print_r($disp_data);
//    exit;
?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head" >
                                <h3 class="heading ">Approve Distribution Plans </h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <h4 class="heading center"><?php echo '<b>'.$province_name.'</b> - '.implode(',',$_SESSION['sub_stakeholders']) ;?></h4>
                                    <h5 class="heading center"><?php echo ''.$from_mon.' to '.$to_mon1.'';?></h5>
                                
                                    <div style="margin-left:0px !important; width:100% !important;">
                                        <form id="approval_form" action="clr_all_district_approval_action.php">
                                        <style>
                                            body {
                                                margin: 0px !important;
                                                font-family: Arial, Helvetica, sans-serif;
                                            }

                                            table#myTable {
                                                margin-top: 20px;
                                                border-collapse: collapse;
                                                border-spacing: 0;
                                            }

                                            table#myTable tr td, table#myTable tr th {
                                                font-size: 11px;
                                                padding-left: 5px;
                                                text-align: left;
                                                border: 1px solid #999;
                                            }

                                            table#myTable tr td.TAR {
                                                text-align: right;
                                                padding: 5px;
                                                width: 50px !important;
                                            }

                                            .sb1NormalFont {
                                                color: #444444;
                                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                                font-size: 11px;
                                                font-weight: bold;
                                                text-decoration: none;
                                            }

                                            p {
                                                margin-bottom: 5px;
                                                font-size: 11px !important;
                                                line-height: 1 !important;
                                                padding: 0 !important;
                                            }

                                            table#headerTable tr td {
                                                font-size: 11px;
                                            }

                                            /* Print styles */
                                            @media only print {
                                                table#myTable tr td, table#myTable tr th {
                                                    font-size: 8px;
                                                    padding-left: 2 !important;
                                                    text-align: left;
                                                    border: 1px solid #999;
                                                }

                                                #doNotPrint {
                                                    display: none !important;
                                                }
                                            }
                                        </style>
                                       
                                        <div style="clear:both;"></div>
                                        
                                        <?php
                                                //currently only taking values of cwh with id 123
                                                if($month<10) $month='0'.$month; 
                                                $soh_date = $year.'-'.$month.'-01';
                                                 
                                                //query for provincial level
                                                
                                                if( mysql_num_rows($res) > 0)
                                                {
                                                    ?>
                                        <div id="get_data_set">
                                                    <table width="100%" id="myTable" cellspacing="0" align="center" class=" table table-bordered table-condensed" >

                                                        <thead>
                                                            <tr id="row_head" class="info" >
                                                                <td rowspan="2" style="text-align:center;" width="">S. No.</td>
                                                                <td rowspan="2" id="desc" width="">Requisition From</td>
                                                                <?php
                                                                    foreach ($products as $stk => $stk_data)
                                                                    {
                                                                        $count = count($stk_data);
                                                                        echo '<td style="text-align:center" colspan="'.($count+1).'">'.$_SESSION['sub_stakeholders'][$stk].'</td>';
                                                                    }
                                                                ?>                                                  
                                                                <td rowspan="" style="width:80px;">Action</td>
                                                            </tr>
                                                            <tr  class="info">
                                                               <?php
                                                                    foreach ($products as $stk => $stk_data)
                                                                    {
                                                                        foreach ($stk_data as $itm => $itm_name)
                                                                        {
                                                                            echo '<td  style="text-align:center">'.$itm_name.'</td>';
                                                                        }
                                                                        echo '<td > </td>';
                                                                    }
                                                                ?>   
                                                            </tr>
                                                        </thead>

                                                        <tbody>    
                                                    <?php
                                                
                                                $c=0;
                                                $count_of_prov_approved=0;
                                                if(!empty($disp_data))
                                                {
                                                    foreach($disp_data as $dist_id => $dist_data)
                                                    {
                                                        $c++;
                                                        $html= '<tr id="row_'.$c.'">';
                                                        $html.= ' <td style="text-align:center">'.$c.'</td>';
                                                        $html.= ' <td style="text-align:center">'.$district_arr[$dist_id].'</td>';
                                                        
                                                        foreach ($products as $stk => $stk_data)
                                                        {
                                                            foreach ($stk_data as $itm => $itm_name)
                                                            {
                                                                $qty=0;
                                                                if(isset($dist_data[$stk][$itm]))
                                                                    $qty=$dist_data[$stk][$itm];
                                                                $html.=  '<td style="text-align:center">'.$qty.'</td>';
                                                            }
                                                            
                                                            if($c==1)
                                                            {
                                                                $d_c = count($district_arr);
                                                                $html .= '<td rowspan="'.$d_c.'">';
                                                                if(!empty($plan_arr[$stk]))
                                                                {
                                                                    foreach($plan_arr[$stk] as $plan_id => $plan_number)
                                                                    {
                                                                        $html .= '<a href="distribution_plan_approvals.php?plan_id='.$plan_id.'&do=approve">Approve('.$plan_number.')</a></br>';
                                                                    }
                                                                }
                                                                else
                                                                    $html .= '<a href="">-</a>';
                                                                $html .= '</td>';
                                                            }
                                                        }
                                                                
                                                        $html.= ' <td style="text-align:center"></td>';
                                                        $html.= ' </tr>';
                                                        echo $html;
                                                    }
                                                }
                                                
                                                //total row
                                                $html = '';
                                                $html = '<tr class="info">';
                                                $html.= ' <td colspan="2" style="text-align:center"><b>Total</b></td>';

                                                foreach ($products as $stk => $stk_data)
                                                {
                                                    foreach ($stk_data as $itm => $itm_name)
                                                    {
                                                        $qty=0;
                                                        if(isset($total_data[$stk][$itm]))
                                                            $qty=$total_data[$stk][$itm];
                                                        $html.=  '<td style="text-align:center"><b>'.$qty.'</b></td>';
                                                    }
                                                    $html.= '<td rowspan=" "></td>';
                                                }

                                                $html.= ' <td style="text-align:center"></td>';
                                                $html.= ' </tr>';
                                                echo $html;
                                                
                                                
                                                //SOH calculation start
                                                

    $and = '';

    if(!empty($_SESSION['user_province1']))
    {
        $and .= " AND national_stock.prov_id =". $_SESSION['user_province1'] ." ";
    }
    if(!empty($last_date))
    {
        $and .= " AND national_stock.tr_date < '$last_date'  ";
    }

//calculating the share of federal stock from national_stock table 

$pipeline_arr =array();
$provincial_soh =array();

        //issuance of the federal stock
        $qry = "
            SELECT
                tbl_locations.LocName,
                stakeholder.stkname,
                itminfo_tab.itm_name,
                itminfo_tab.itmrec_id,
                Sum(national_stock.quantity) as qty,
                tbl_locations.LocType
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE
                 
                /*AND stakeholder.stk_type_id = 0*/
                AND national_stock.prov_id = $province 
                AND national_stock.tr_date <= '$last_date'
            GROUP BY
                tbl_locations.LocName,

                itminfo_tab.itm_name
            ORDER BY
                national_stock.prov_id,
                national_stock.stk_id,
                national_stock.item_id
        ";
//        echo $qry;
//        exit;
        $qryRes = mysql_query($qry);
        
        while($row = mysql_fetch_assoc($qryRes))
        {
                    
            if(empty($closing_bal[$row['itmrec_id']][$v]))
                   $closing_bal[$row['itmrec_id']][$v]=0;
        
            
            //this is federal share only , at every month
            $closing_bal[$row['itmrec_id']][$v]=$row['qty'];
            //$q_data[$row['itmrec_id']]['share_of_stock']=$closing_bal[$row['itmrec_id']][$v];
        }
    
    
        //now fetching the provincial share...
        $qry_5 = "SELECT
                             itminfo_tab.itm_name,
                             itminfo_tab.qty_carton,
                             SUM(tbl_stock_detail.Qty)  AS vials,
                             tbl_itemunits.UnitType,
                             itminfo_tab.itmrec_id,
                             stock_batch.funding_source,
                             tbl_warehouse.wh_name as funding_source_name
                     FROM
                             stock_batch
                     INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                     INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                     INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                     INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                     INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                     WHERE

                             DATE_FORMAT(
                                     tbl_stock_master.TranDate,
                                     '%Y-%m-%d'
                             ) <= '".$last_date."'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )
                         AND stock_batch.funding_source in  (".implode(',',$funding_stks).") 
                     GROUP BY
                             itminfo_tab.itm_id,
                             stock_batch.funding_source
                     ORDER BY
                             itminfo_tab.frmindex
             ";
            //echo $qry_5;exit;
            $res_5 = mysql_query($qry_5);
            
            while($row_5 = mysql_fetch_assoc($res_5))
            {
                //this is the prov stock
                if(empty($provincial_soh[$row_5['itmrec_id']][$v]['soh'])) $provincial_soh[$row_5['itmrec_id']][$v]['soh']=0;
                
                $provincial_soh[$row_5['itmrec_id']][$v]['soh']+=$row_5['vials'];

               // if(!empty($row_5['vials']) && !empty($q_data[$row_5['itmrec_id']]['share_of_stock']))
                //    $q_data[$row_5['itmrec_id']]['share_of_stock']+=$row_5['vials'];

               // if(!empty($row_5['vials']))
               // $q_data[$row_5['itmrec_id']]['stock_of_funding_sources'][$row_5['funding_source_name']] = $row_5['vials'];
            }    

                                                //SOH calculation end
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                //SOH Row
                                                $html = '';
                                                $html = '<tr id="">';
                                                $html.= ' <td colspan="2" style="text-align:center"><b>CWH SOH</b></td>';

                                                foreach ($products as $stk => $stk_data)
                                                {
                                                    foreach ($stk_data as $itm => $itm_name)
                                                    {
                                                        $qty=0;
                                                        $html.=  '<td style="text-align:center"><b>'.$qty.'</b></td>';
                                                    }
                                                    $html.= '<td rowspan=" "></td>';
                                                }

                                                $html.= ' <td style="text-align:center"></td>';
                                                $html.= ' </tr>';
                                                echo $html;
                                                
                                                //Remaining Qty Row
                                                $html = '';
                                                $html = '<tr id="">';
                                                $html.= ' <td colspan="2" style="text-align:center"><b>Remaining Qty</b></td>';

                                                foreach ($products as $stk => $stk_data)
                                                {
                                                    foreach ($stk_data as $itm => $itm_name)
                                                    {
                                                        $qty=0;
                                                        $html.=  '<td style="text-align:center"><b>'.$qty.'</b></td>';
                                                    }
                                                    $html.= '<td rowspan=" "></td>';
                                                }

                                                $html.= ' <td style="text-align:center"></td>';
                                                $html.= ' </tr>';
                                                echo $html;
                                                
                                                //MOS Row
                                                $html = '';
                                                $html = '<tr id="">';
                                                $html.= ' <td colspan="2" style="text-align:center"><b>CWH MOS</b></td>';

                                                foreach ($products as $stk => $stk_data)
                                                {
                                                    foreach ($stk_data as $itm => $itm_name)
                                                    {
                                                        $qty=0;
                                                        $html.=  '<td style="text-align:center"><b>'.$qty.'</b></td>';
                                                    }
                                                    $html.= '<td rowspan=" "></td>';
                                                }

                                                $html.= ' <td style="text-align:center"></td>';
                                                $html.= ' </tr>';
                                                echo $html;
                                                
                                                ?>
                                                
                                                
                                                
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                   
                                                    <label class="control-label hide">
                                                        <span class="note note-danger">Products which were not included in requisition , are marked Red </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                                <?php
                                                }//end of if num rows
                                                else
                                                {
                                                    echo 'No requisitions found in this plan';
                                                }
                                                ?>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <script></script>
    
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>