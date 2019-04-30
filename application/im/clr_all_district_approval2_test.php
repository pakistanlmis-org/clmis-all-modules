<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
include('../dashboard/calculate_usaid_stock.php');
//include header
include(PUBLIC_PATH . "html/header.php");
//check id
$current_date = date('Y-m-d');

$q1 = " SELECT
        stakeholder.stkid,
        stakeholder.stkname
        FROM
        stakeholder
        WHERE
        stakeholder.stkid ='".$_SESSION['user_stakeholder1']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_stakeholder_name = $row['stkname'];

$q1 = "select * from tbl_locations where PkLocID = '".$_SESSION['user_province1']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_province_name = $row['LocName'];

$objuserstk->m_npkId = $_SESSION['user_id'];
$requisition_stk = $objuserstk->GetStkByUserId();

$qry_f = "SELECT
                funding_stk_prov.funding_source_id
            FROM
                funding_stk_prov
            WHERE
                funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." AND
                funding_stk_prov.province_id = ".$_SESSION['user_province1']."
                 ";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}

//echo '<pre>';print_r($_SESSION);exit; 

$integ_stk_arr = array();
foreach($_SESSION['sub_stakeholders'] as $sub_id => $sub_name)
{
    $integ_stk_arr[$sub_id] = $sub_id;
}

$qry = "SELECT
                stock_batch.item_id,
                itminfo_tab.qty_carton
        FROM
                stock_batch
        INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id

        GROUP BY
                stock_batch.item_id,
                itminfo_tab.qty_carton
        ORDER BY
                stock_batch.item_id,
                itminfo_tab.qty_carton ";
    //query result
//echo $qry;exit;
    $res1=mysql_query($qry);
    $qty_cartons = array();
    while($row = mysql_fetch_assoc($res1))
    {
        $qty_cartons[$row['item_id']][]  = $row['qty_carton'];
    }
//echo '<pre>';print_r($integ_stk_arr);exit;   
$qry2 = " SELECT
                requisition_module_flow.action_id,
                requisition_module_flow.can_submit_to,
                requisition_module_flow.is_active,
                requisition_module_flow.prov_id,
                requisition_module_flow.stk_id
            FROM
                requisition_module_flow
            WHERE
                requisition_module_flow.action_id = 1 AND
                requisition_module_flow.can_submit_to = 3 AND
                requisition_module_flow.is_active = 1 AND
                requisition_module_flow.prov_id = ".$_SESSION['user_province1']." AND
                requisition_module_flow.stk_id = ".$_SESSION['user_stakeholder1']." ";
$qryRes2 = mysql_query($qry2);
$num2 = mysql_num_rows($qryRes2);

if($num2>0)
{
    $approve_dist_reqs_is_active = TRUE;
}
else
{
    $approve_dist_reqs_is_active = FALSE;
}

//checking req flow
$qry2 = " SELECT
                requisition_module_flow.action_id,
                requisition_module_flow.can_submit_to,
                requisition_module_flow.is_active,
                requisition_module_flow.prov_id,
                requisition_module_flow.stk_id
            FROM
                requisition_module_flow
            WHERE
                requisition_module_flow.action_id = 1 AND
                requisition_module_flow.can_submit_to = 9 AND
                requisition_module_flow.is_active = 1 AND
                requisition_module_flow.prov_id = ".$_SESSION['user_province1']."  ";
$qryRes2 = mysql_query($qry2);
$num2 = mysql_num_rows($qryRes2);
$app_arr=array();
if($num2>0)
{
    $approve_prov_reqs_is_active = TRUE;
    while($row = mysql_fetch_assoc($qryRes2))
    {
        $app_arr[$row['stk_id']] = $row['stk_id'];
    }
}
else
{
    $approve_prov_reqs_is_active = FALSE;
}
//print_r($app_arr);exit;
$month=$year='';
if(!empty($_REQUEST['month']))      $month = $_REQUEST['month'];
if(!empty($_REQUEST['year']))       $year = $_REQUEST['year'];

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
                    stakeholder_item.stkid = 73
                        AND itm_category = 1
            ORDER BY
                    itminfo_tab.method_rank,itminfo_tab.itm_id ASC
";
$res= mysql_query($qry_itm);
$itm_name_id=$product=$itemIds=$itm_name_id =array();
//print_r($_SESSION);
while($row= mysql_fetch_assoc($res))
{
    //$itm_name_id[$row['itm_name']] = $row['itm_id'];
    $product[$row['method_type']][] = $row['itm_name'];
    $names[$row['itm_id']] = $row['itm_name'];
    $prod_sequence[]=$row['itm_id'];
    $itm_name_id[$row['itm_name']] = $row['itm_id'];
}


$to_mon = (sprintf("%02d", $month)).'-'.$year;
$from_mon = date('M-Y', strtotime('01-'.$to_mon.'-2 months'));
$to_mon1 = date('M-Y',strtotime('01-'.$to_mon));


$and1 = array();
$and1[] = " tbl_warehouse.wh_id = 123 ";

$qry = "SELECT
            tbl_warehouse.dist_id,
            tbl_warehouse.prov_id,
            tbl_warehouse.stkid,
            tbl_warehouse.wh_name,
            tbl_warehouse.wh_id
        FROM
            tbl_warehouse
        WHERE  " . implode(' AND ',$and1);
    //query result
//echo $qry;exit;
    $res1=mysql_query($qry);
    $wh_array = array();
    while($row = mysql_fetch_assoc($res1))
    {
        $wh_array[$row['wh_id']]  = $row['wh_name'];
    }


if(!isset($_SESSION['stakeholder_details']) && isset($_SESSION['user_stakeholder1']))
{
    $qry2 = "  SELECT
                    *
                    FROM
                    stakeholder
                    WHERE
                    stakeholder.stkid = ".$_SESSION['user_stakeholder1']."  ";

    $qryRes3 = mysql_query($qry2);
    $num3 = mysql_num_rows($qryRes3);
    if($num3>0)
    {
            while($row=mysql_fetch_assoc($qryRes3))
            {
                $_SESSION['stakeholder_details']=$row;
            }
    }
        
}    

    $signature_row_html= '<div id="hd_tbl"><table id="headerTable" width="100%" >
                                            
                                            <tr>
                                                <td width="15%">&nbsp;</td>
                                                <td  class="sb1NormalFont">Created By</td>
                                                <td style="width:20%">&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td  class="sb1NormalFont">Approved By</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            
                                            
                                         
                                        </table></div>';
?>
    <script>
     function printContents3() {
            var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
            var printingContents = document.getElementById("export").innerHTML;

            var docprint = window.open("", "", printingContents);

            docprint.document.open();
            //docprint.document.write('<html><head><h3 style="align:center">Provincial Approvals & Distribution Plan ( Pending Requisitions )</h3>');

            docprint.document.write('</head><body onLoad="self.print();self.close();"><center>');
            docprint.document.write(printingContents);
            
            docprint.document.write('</center>');

            docprint.document.write('</body></html>');
            docprint.document.close();
            docprint.focus();   
        }
  function printContents2() {
      
            var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
            var printingContents = document.getElementById("c_table").innerHTML;

            var docprint = window.open("", "", printingContents);

            docprint.document.open();
            docprint.document.write('<html><head><h3 style="align:center"><p style="text-align:center;margin-left:35px;margin-bottom: 20px;font-size: 15px !important;"><b>Provincial Approval & Distribution Plan - (Temp)</b></b></p></h3>');

            docprint.document.write('<p style="text-align:center;margin-left:35px;font-size: 12px !important;"><b><?=(isset($_SESSION['stakeholder_details']['report_title3'])?$_SESSION['stakeholder_details']['report_title3']:'')?> - <?php echo (!empty($user_province_name)?$user_province_name.'':'')?> </b></b></p>');
            docprint.document.write('<p style="text-align:center;margin-left:35px;font-size: 12px !important;"><b><?php echo $from_mon.' - '.$to_mon1;?> </b></b></p>');

            
            docprint.document.write('<style>table#dp_table tr td, table#myTable tr th {font-size: 11px;padding-left: 5px;text-align: left;border: 1px solid #999;} table#dp_table {margin-top: 20px;border-collapse: collapse;border-spacing: 0;}  @media only print { .doNotPrintCls { display: none !important; } } </style>');
            docprint.document.write('</head><body onLoad="self.print();self.close();"><center>');
            
            docprint.document.write('<table id="dp_table" border="border: 1px solid #999;" >');
            docprint.document.write(printingContents);
            
            docprint.document.write('</table>');
            var sign1 = $('#hd_tbl').html();
            docprint.document.write(sign1);
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

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Provincial Approval & Distribution Plan  </h3>
                            </div>
                            <div class="widget-body">
                                <div id="export" style="clear:both;margin-top:20px;">
                                     <div class="row">
                                                <div class="col-md-12">
                                                    
                                                    <p style="color: #000000; font-size: 25px;text-align:center">
                                                            
                                                            <p style="text-align:center;margin-left:35px;margin-bottom: 20px;font-size: 15px !important;"><b>Provincial Approval & Distribution Plan</b></b></p>
                                                            
                                                            <p style="text-align:center;margin-left:35px;font-size: 12px !important;"><b><?=(isset($_SESSION['stakeholder_details']['report_title3'])?$_SESSION['stakeholder_details']['report_title3']:'')?> - <?php echo (!empty($user_province_name)?$user_province_name.'':'')?> </b></b></p>
                                                            <p style="text-align:center;margin-left:35px;font-size: 12px !important;"><b><?php echo $from_mon.' to '.$to_mon1?></b></b></p>
                                                    <hr style="margin:3px 10px;" />
                                                </div>
                                            </div>
                                    <div style="margin-left:0px !important; width:100% !important;">
                                        <form id="approval_form" action="clr_all_district_approval2_action.php">
                                            
                                            <div class="row doNotPrintCls">
                                                <div class="col-md-2">
                                                    Submit to:
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control form-control-sm" name="warehouse_sel" id="warehouse_sel">
                                                            <?php
                                                            foreach($wh_array as $wh_id => $wh_name)
                                                            {
                                                                echo '<option value="'.$wh_id.'">'.$wh_name.'</option>';
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            </div>
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
                                                line-height: 30px;
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
                                                    display:none !important;
                                                }
                                                .doNotPrintCls {
                                                    display:none !important;
                                                }
                                            }
                                        </style>
                                       
                                        <div style="clear:both;"></div>
                                        
                                        <?php
                                                //currently only taking values of cwh with id 123
                                                $month = (sprintf("%02d", $month)); 
                                                $soh_date = $year.'-'.$month.'-01';
                                                
                                                $toDate = date('d/m/Y');
                                                //from date	
                                                $fromDate = date('d/m/Y', strtotime("-3 month", strtotime(dateToDbFormat($toDate))));
                                                $avg_con_Date = date('d/m/Y', strtotime("-12 month", strtotime(dateToDbFormat($toDate))));
                                                //start date
                                                $startDate = $startDate = date('Y-m-d', strtotime("-3 month", strtotime(dateToDbFormat($fromDate))));
                                                //end date
                                                $endDate = dateToDbFormat($fromDate);
                                                $avg_con_Date1 = dateToDbFormat($avg_con_Date);
                                                $mosDate = dateToDbFormat($toDate);
                                                $whId =123;
                                                
                                                //query for national level
                                                   $qry_soh = "
                                                        SELECT
                                                                itminfo_tab.itm_id,
                                                                SUM(tbl_stock_detail.Qty) AS soh,
                                                                (
                                                                        SELECT
                                                                                SUM(summary_national.consumption)/12
                                                                        FROM
                                                                                summary_national
                                                                        INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
                                                                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                        WHERE
                                                                                summary_national.reporting_date Between '$avg_con_Date1' AND '" . dateToDbFormat($toDate) . "'
                                                                        AND summary_national.item_id = itminfo_tab.itmrec_id
                                                                        AND stakeholder.lvl = 1
                                                                        AND stakeholder.stkid IN ( ".implode(',',$integ_stk_arr).")
                                                                        AND tbl_warehouse.wh_id = $whId
                                                                        GROUP BY
                                                                                summary_national.stakeholder_id
                                                                ) AS amc
                                                        FROM
                                                                itminfo_tab
                                                        INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                                        INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                                        INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                                                        INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
                                                        WHERE
                                                                DATE_FORMAT(
                                                                        tbl_stock_master.TranDate,
                                                                        '%Y-%m-%d'
                                                                ) <= '$mosDate'
                                                        AND (
                                                                tbl_stock_master.WHIDFrom = $whId
                                                                OR tbl_stock_master.WHIDTo = $whId
                                                        )
                                                        AND stock_batch.funding_source in  (".implode(',',$funding_stks).") 
                                                        AND tbl_stock_detail.temp = 0
                                                        GROUP BY
                                                                itminfo_tab.itm_id
                                                        ORDER BY
                                                                itminfo_tab.frmindex ASC   
                                                  
                                                ";
                                                //echo $qry_soh;exit;
                                                $res_soh = mysql_query($qry_soh);
                                                //national level
                                                $soh_arr_c =  $amc_arr_c = $mos_arr_c = array();
                                                while($row = mysql_fetch_assoc($res_soh))
                                                {
                                                    $soh_arr_c[$row['itm_id']] = $row['soh'];
                                                    //$amc_arr_c[$row['itm_id']] = $row['amc'];
                                                    if(!empty($row['soh']) && $row['soh'] > 0 && !empty($row['amc']) && $row['amc'] > 0)
                                                        $mos_arr_c[$row['itm_id']] = $row['soh'] / $row['amc'];
                                                    else
                                                        $mos_arr_c[$row['itm_id']] = 0;
                                                }
                                                //echo '<pre>';print_r($soh_arr_c);print_r($curr_stock);exit;
                                                
                                                //calculating the amc of last available month of the stakeholders data.
                                                // amc query
                                                 $qry_soh = "
                                                          SELECT
                                                                    summary_province.province_id,
                                                                    itminfo_tab.itm_id,
                                                                    SUM(
                                                                            summary_province.avg_consumption
                                                                    ) AS avg_consumption
                                                            FROM
                                                                    summary_province
                                                            INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                                                            INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
                                                            INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                                                            WHERE
                                                                    summary_province.reporting_date = (select min(max_month) from (
                                                                                                        SELECT
                                                                                                                max(
                                                                                                                        summary_province.reporting_date
                                                                                                                ) AS max_month
                                                                                                        FROM
                                                                                                                summary_province
                                                                                                        INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                                                                                                        INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
                                                                                                        INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                                                                                                        WHERE
                                                                                                                summary_province.stakeholder_id in (".implode(',',$integ_stk_arr).")
                                                                                                        AND tbl_locations.ParentID IS NOT NULL
                                                                                        GROUP BY
                                                                                                summary_province.stakeholder_id) as d 
                                                                                                )
                                                            AND summary_province.stakeholder_id in (".implode(',',$integ_stk_arr).")
                                                            AND tbl_locations.ParentID IS NOT NULL
                                                            AND tbl_locations.PkLocID = ".$_SESSION['user_province1']."
                                                            GROUP BY
                                                                    summary_province.province_id,
                                                                    summary_province.item_id
                                                  
                                                ";
                                                //echo $qry_soh;exit;
                                                $res_soh = mysql_query($qry_soh);
                                                //national level
                                                $amc_arr_c   = array();
                                                while($row = mysql_fetch_assoc($res_soh))
                                                {
                                                    $amc_arr_c[$row['itm_id']] = $row['avg_consumption'] / count($integ_stk_arr);
                                                    //$amc_arr_c2[$row['itm_id']] = $row['avg_consumption'];
                                                }
                                                //end of amc
                                               //echo '<pre>OPENING';print_r($amc_arr_c);print_r($amc_arr_c2);exit;
                                        
                                                //calculating the quota for stk according to fp2020 dashboard
//                                                $qry = "SELECT
//                                                                tbl_locations.LocName,
//                                                                stakeholder.stkname,
//                                                                itminfo_tab.itm_name,
//                                                                itminfo_tab.itm_id,
//                                                                Sum(national_stock.quantity) as qty,
//                                                                tbl_locations.LocType
//                                                            FROM
//                                                                national_stock
//                                                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
//                                                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
//                                                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
//                                                            WHERE
//
//                                                                national_stock.prov_id = ".$_SESSION['user_province1']."
//                                                                /*AND stakeholder.stk_type_id = 0*/
//                                                                AND national_stock.tr_date <= '".$current_date."'  
//                                                            GROUP BY
//                                                                tbl_locations.LocName,
//
//                                                                itminfo_tab.itm_name
//                                                            ORDER BY
//                                                                national_stock.prov_id,
//                                                                national_stock.stk_id,
//                                                                national_stock.item_id
//                                                    ";
//                                                //query result
//                                                //echo $qry;exit;
//                                                $qryRes = mysql_query($qry);
//                                                $closing_bal = array();
//                                                
//                                                if($_SESSION['user_stakeholder1'] == '1')
//                                                {
//                                                    while ($row = mysql_fetch_assoc($qryRes)) {
//                                                        $closing_bal[$row['itm_id']] = ($row['qty']>0)?$row['qty']:'0';
//                                                    }
//                                                }
                                                
                                                //echo '<pre>OLD';print_r($closing_bal);
                                                //usaid supported stock , according to new formula
                                                $closing_bal_named_array = calculate_usaid_stock($current_date,$_SESSION['user_province1']);
    
                                                $closing_bal=array();
                                                foreach($closing_bal_named_array as $p_name=>$p_arr){
                                                    foreach($p_arr as $itm_name=>$val){
                                                        @$closing_bal[$itm_name_id[$itm_name]] = (($val>0)?$val:0);
                                                    }
                                                }
                                                //end usaid stock
                                                
                                                
                                                //echo '<pre>NEW';print_r($closing_bal);print_r($closing_bal_named_array);print_r($itm_name_id);
                                                //exit;
                                                
                                                
                                                
                                                
                                                $reserved_qry = "SELECT
                                                                    sum(clr_details.qty_req_prov) as reserved,
                                                                    clr_details.itm_id
                                                                    FROM
                                                                    clr_master
                                                                    INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                                                                    INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                                                                    WHERE
                                                                    clr_master.approval_status in ('Prov_Approved','Approved') AND
                                                                    tbl_warehouse.prov_id = ".$_SESSION['user_province1']." AND
                                                                    tbl_warehouse.stkid in ( ".implode(',',$integ_stk_arr)." )
                                                                    GROUP BY
                                                                    clr_details.itm_id";
                                                $reserved_res = mysql_query($reserved_qry);
                                                $reserved_arr=array();
                                                while($row = mysql_fetch_assoc($reserved_res))
                                                {
                                                    $reserved_arr[$row['itm_id']] = $row['reserved'];
                                                }
                                                
                                                
                                                
                                                    $qry = "SELECT
                                                            tbl_locations.LocName,
                                                            clr_details.itm_id,
                                                            clr_details.qty_req_dist_lvl2 AS req1,
                                                            (CASE   WHEN clr_master.approval_status = 'Pending' THEN clr_details.qty_req_dist_lvl1
                                                                    WHEN clr_master.approval_status= 'Dist_Approved' THEN clr_details.qty_req_dist_lvl2
                                                                    ELSE clr_details.qty_req_prov 
                                                             END) as req,
                                                            clr_master.pk_id,
                                                            clr_master.wh_id,
                                                            clr_master.approval_status,
                                                            clr_master.requisition_num,
                                                            clr_master.stk_id,
                                                            stakeholder.stkname
                                                        FROM
                                                            tbl_locations
                                                            INNER JOIN tbl_warehouse ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                            INNER JOIN clr_master ON clr_master.wh_id = tbl_warehouse.wh_id
                                                            INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                                                            INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
                                                        WHERE
                                                            tbl_locations.ParentID = ".$_SESSION['user_province1']." AND
                                                            tbl_locations.LocLvl = 3 AND
                                                            clr_master.approval_status NOT in ('Issued', 'Issue in Process','Denied','Approved') AND
                                                            MONTH(clr_master.date_to) = '".$month."'AND
                                                            YEAR(clr_master.date_to) = '".$year."'
                                                             AND clr_master.stk_id in  (".implode($integ_stk_arr,",").")
                                                        ORDER BY
                                                            stakeholder.stkname,
                                                            tbl_locations.LocName ,
                                                            clr_details.itm_id
                                                        ";
                                                //echo $qry;
                                                $res = mysql_query($qry);
                                       
                                               
                                                
                                                
                                                $disp_data=$total_data=array();
                                                if( mysql_num_rows($res) > 0)
                                                {
                                                    ?>
                                        <?php
                                        $fileName='Distribution_plan';
                                        //include sub_dist_reports
                                        include('sub_export_options.php');
                                    ?>
                                        <div id="get_data_set" class="col-md-12">
                                                    <table width="100%" id="myTable" cellspacing="0" align="center" class=" table  table-bordered table-condensed" >


                                                        <thead>
                                                            <tr id="row_head" class="info row_copy" data-id="head1">
                                                                <td class="doNotPrintCls" rowspan="" style="text-align:center;" width="25"><input id="check_all" name="check_all" type="checkbox"></td>
                                                                <td rowspan="" style="text-align:center;" width="25">Sr. No.</td>
                                                                <td rowspan="" id="desc" width="150">District - Stakeholder</td>
                                                                <?php
                                                                    foreach ($prod_sequence as $itm)
                                                                    {
                                                                        echo '<td  style="min-width:50px">';
                                                                        echo '<a style="color:black !important;"  data-toggle="popover" title="Carton Sizes Available" data-trigger="hover" data-placement="left" data-html="true" ';
                                                                        $size='';
                                                                        if(!empty($qty_cartons[$itm]))
                                                                        {
                                                                            foreach($qty_cartons[$itm] as $k=>$s){
                                                                               $size .= ($k+1).':  '.$s.' Per Carton'; 
                                                                            }
                                                                        }
                                                                        echo 'data-content="'.$size.'">';
                                                                        echo ''.$names[$itm].'';
                                                                        echo '</a>';
                                                                        echo '<input style="direction: rtl;height:20px;font-size:11px;" class="size size_'.$itm.'" data-itm="'.$itm.'" size="5">';
                                                                        echo '</td>';
                                                                    }
                                                                ?>                                                  
                                                                <td class="doNotPrintCls" rowspan="" style="width:230px;">Action</td>
                                                                <td rowspan="" style="width:120px;">Status</td>
                                                            </tr>
                                                            <tr  class="info">
                                                               
                                                                
                                                            </tr>
                                                        </thead>


                                                        <tbody>    
                                                    <?php
                                                    
                                                //echo '<pre>'; print_r($app_arr);
                                                $stk  = $stk_name = array();
												$r_count = 0;
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    $in_list = array();
                                                    $in_list[]='Prov_Approved';
                                                    $in_list[]='Prov_Saved';
                                                    //echo $row['stk_id'];
                                                    if(in_array($row['stk_id'], $app_arr))
                                                    {
                                                            $in_list[]= "RS_Approved";
                                                    }
                                                    else
                                                    {
                                                         if($approve_dist_reqs_is_active) 
                                                                $in_list[]= "Dist_Approved";
                                                            else
                                                            {
                                                                $in_list[]= "Pending";
                                                                $in_list[]= "Dist_Approved";
                                                                $in_list[]= "RS_Approved";
                                                                $in_list[]= "RS_Saved";
                                                            }
                                                    }
                                                    
                                                    //echo '<pre>'; print_r($in_list);
                                                    //echo 'Dist:'.$approve_dist_reqs_is_active.',prov:'.$approve_prov_reqs_is_active;
                                                    
                                                    if(!in_array($row['approval_status'], $in_list))
                                                    {
                                                        continue;
                                                    }
                                                    $r_count++;
                                                    
                                                    $stk[$row['stk_id']]        = $row['stk_id'];
                                                    $stk_name[$row['stkname']] = $row['stkname'];
                                                    $disp_data[$row['pk_id']][$row['itm_id']] = $row['req'];
                                                    $disp_data[$row['pk_id']]['district'] = $row['LocName'];
                                                    $disp_data[$row['pk_id']]['stkname'] = $row['stkname'];
                                                    $disp_data[$row['pk_id']]['approval_status'] = $row['approval_status'];
                                                    $disp_data[$row['pk_id']]['requisition_num'] = $row['requisition_num'];
                                                    $disp_data[$row['pk_id']]['wh_id'] = $row['wh_id'];
                                                    
                                                    if(!empty($total_data[$row['itm_id']]))
                                                        $total_data[$row['itm_id']] += $row['req'];
                                                    else
                                                        $total_data[$row['itm_id']] = $row['req'];
                                                    //print_r($row);
                                                }
                                               //echo '<pre>';print_r($disp_data);exit;
                                                $c=0;
                                                $count_of_prov_approved=0;
                                                $previous_stk= '';
                                                if(!empty($disp_data))
                                                {
                                                    foreach($disp_data as $pk_id => $data)
                                                    {
                                                        
                                                        $c++;
                                                        $html='';
                                                        if($data['stkname'] != $previous_stk)
                                                        {
                                                            $span= count($prod_sequence) + 6;
                                                            $html.= '<tr>';
                                                            $html.= '<td colspan="'.$span.'">'.$data['stkname'].'</td>';
                                                            $html.= '</tr>';
                                                        }
                                                        $previous_stk=$data['stkname'];
                                                        $html .= '<tr id="row_'.$c.'" class="req_rows row_copy" data-id="'.$c.'">';
                                                        if($data['approval_status']=='Prov_Approved')
                                                        {
                                                            $html.= ' <td class="doNotPrintCls"><input name="check['.$pk_id.']" data-id="'.$c.'"  class="mycheckbox " type="checkbox"></td>';
                                                            $count_of_prov_approved++;
                                                        }
                                                        else 
                                                        {
                                                            $html.= ' <td class="doNotPrintCls"><input  id="chk_'.$pk_id.'" name="check['.$pk_id.']" data-id="'.$c.'"  class="mycheckbox_disabled " style="display:none;" type="checkbox"></td>';
                                                       
                                                        }
                                                        
                                                        $html.= ' <td style="text-align:center">'.$c.'</td>';
                                                        $html.= ' <td>'.$data['district'].' - '.$data['stkname'].'</td>';
                                                        
                                                         
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                              $cls3=$qty_disp='';
                                                            if(isset($data[$itm]) && $data[$itm]>=0)
                                                            {
                                                                 $qty_disp = $data[$itm];
                                                            }
                                                            if(!array_key_exists($itm,$data))$cls3='danger';
                                                            
                                                            $style='';
                                                            if($data['approval_status']=='Prov_Approved' || $data['approval_status']=='Approved')
                                                            {
                                                                //$style=' display:none;';
                                                            }
                                                            
                                                            //$html.= ' <td class="'.$cls3.'" style="text-align:right">'.$qty_disp.'</td>';
                                                            $html.= '<td  id="td_'.$pk_id.'_'.$itm.'" class="'.$cls3.' value_td  value_td_'.$itm.'" style="text-align:right">';
                                                            $html.= '<input class="value_input value_input_'.$itm.' pkid_'.$pk_id.'" id="input_'.$pk_id.'_'.$itm.'" name="input_'.$pk_id.'_'.$itm.'" value="'.$qty_disp.'" data-pkid="'.$pk_id.'" data-itm="'.$itm.'" data-original="'.$qty_disp.'" size="6"  style="direction: rtl; height:20px;font-size:11px;'.$style.'">';
                                                            $html.= '<br/><span class="value_span value_span_'.$itm.' font-blue" id="span_'.$pk_id.'_'.$itm.'" >'.(!empty($qty_disp)?number_format($qty_disp):'&nbsp;').'</span>';
                                                            $html.= '</td>';
                                                        }
                                                         $html.= ' <td style="text-align:center" class="doNotPrintCls">';
                                                        $html.= '<a class="btn btn-xs grey" href="clr_view.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'">View</a>';
                                                        
                                                        if($approve_dist_reqs_is_active)
                                                        {
                                                             if($data['approval_status']=='Dist_Approved' || $data['approval_status']=='Prov_Saved' )
                                                             {
                                                                 $html.= '  <a  class="btn btn-xs yellow-gold cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Saved" data-name="'.$data['district'].' - '.$data['stkname'].'">Save</a>';
                                                                 $html.= '  <a  class="btn btn-xs green cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Approved" data-name="'.$data['district'].' - '.$data['stkname'].'">Approve</a>';
                                                             }
                                                             elseif($data['approval_status']=='Prov_Approved')
                                                             {
                                                                 $html.= '  <a  class="btn btn-xs yellow-gold cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Saved" data-name="'.$data['district'].' - '.$data['stkname'].'">Save</a>';
                                                                 $html.= '  <a  class="btn btn-xs green cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Approved" data-name="'.$data['district'].' - '.$data['stkname'].'" style="display:none;">Approve</a>';
                                                             }
                                                        
                                                        }
                                                        else
                                                        {
                                                             if($data['approval_status']=='Pending' || $data['approval_status']=='Dist_Approved' || $data['approval_status']=='Prov_Saved' || $data['approval_status']=='RS_Approved' )
                                                             {
                                                                 $html.= '  <a  class="btn btn-xs yellow-gold cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Saved" data-name="'.$data['district'].' - '.$data['stkname'].'">Save</a>';
                                                                 $html.= '  <a  class="btn btn-xs green cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Approved" data-name="'.$data['district'].' - '.$data['stkname'].'">Approve</a>';
                                                             }
                                                             elseif($data['approval_status']=='Prov_Approved')
                                                             {
                                                                  $html.= '  <a  class="btn btn-xs yellow-gold cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Saved" data-name="'.$data['district'].' - '.$data['stkname'].'">Save</a>';
                                                                 $html.= '  <a  class="btn btn-xs green cls_approve_req" data-id="'.$pk_id.'" data-act="Prov_Approved" data-name="'.$data['district'].' - '.$data['stkname'].'" style="display:none;">Approve</a>';
                                                             }
                                                                 
                                                        }
                                                       
                                                        $html.= ' </td>';
                                                        
                                                        $cls = '';
                                                        if($data['approval_status'] == 'RS_Approved') $st='Approved by '.$data['stkname'];
                                                        elseif($data['approval_status'] == 'RS_Saved') $st='Saved by '.$data['stkname'];
                                                        elseif($data['approval_status'] == 'Prov_Approved'){
                                                            $st='Approved by '.(isset($_SESSION['user_stakeholder_name'])?$_SESSION['user_stakeholder_name']:'Procurement Stakeholder');
                                                            $cls = "success";
                                                            
                                                        }
                                                        elseif($data['approval_status'] == 'Prov_Saved'){
                                                            $st='Saved by '.(isset($_SESSION['user_stakeholder_name'])?$_SESSION['user_stakeholder_name']:'Procurement Stakeholder');
                                                            $cls = "warning";
                                                        }
                                                        else{
                                                            $st = $data['approval_status'];
                                                            
                                                        }
                                                        
                                                        
                                                        $html.= ' <td style="text-align:center" class="" id="status_of_'.$pk_id.'">'.str_replace('_',' ',$st).'</td>';

                                                        $html.= ' </tr>';
                                                        
                                                        echo $html;
                                                    }
                                                }
                                                
                                                
                                                ?>
                                                
                                               
                                                <?php
												if(isset($r_count) && $r_count>0)
												{
												?>
                                                
                                                <tr style="text-align:right"  class="info hide_rows">
                                                    <td colspan="3">(A) Total Quantity</td>
                                                    <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            $provincial = (!empty($soh_arr_c[$itm])?$soh_arr_c[$itm]:'0');
                                                            $national_q = (isset($closing_bal[$itm])?$closing_bal[$itm]:'0');
                                                            $soh_val = ($provincial) +($national_q);
                                                            
                                                            $t_req = (!empty($total_data[$itm])?$total_data[$itm]:'0');
                                                            $html .= ' <td style="text-align:right">';
                                                            $html.= ' <input class="value_total_req value_total_req_'.$itm.'" id="total_req_'.$itm.'" name="total_req_'.$itm.'" value="'.$t_req.'" data-original="'.$t_req.'" data-prod="'.$names[$itm].'"  data-soh="'.$soh_val.'"  size="6"  style="direction: rtl;">';
                                                            $html .=  '<br/>'.number_format($t_req);
                                                            $html .= '</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                    <td colspan="3" rowspan="7" align="left">
                                                        <div id ="doNotPrint">
                                                            <input type="hidden" name="form_action" id="form_action" value="" >
                                                            <input type="hidden" name="multi_stk_id" id="multi_stk_id" value="<?php echo implode($stk,',')?>" >
                                                            <input type="hidden" name="multi_stk_name" id="multi_stk_name" value="<?php echo implode($stk_name,',')?>" >
                                                            <button style="display:none" id="submit" name="submit" type="submit" value="Approve">a</button>
                                                            <a id="save_plan_btn" class="btn btn-success action_btns hide doNotPrintCls">Save Plan</a>
                                                            <a id="approve_plan_btn" class="btn btn-success action_btns doNotPrintCls">Submit As Plan</a>
                                                            <a onClick="printContents3()" value="Print" class="btn btn-warning" >Print</a>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                                <tr class="hide_rows">
                                                    <td colspan="3">(B) CWH SOH (<?=(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?>)</td>
                                                     <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            $provincial = (!empty($soh_arr_c[$itm])?$soh_arr_c[$itm]:'0');
                                                            $national_q = (isset($closing_bal[$itm])?$closing_bal[$itm]:'0');
                                                            $soh_val = ($provincial) +($national_q);
                                                            $html.= ' <td style="text-align:right;color:black !important"><a style="color:black !important;"  data-toggle="popover" title="Stock Breakdown" data-trigger="hover" data-placement="left" data-html="true" data-content="Provincial Stock : '.number_format($provincial).' <br/>National Quota: '.number_format($national_q).'">'.number_format($soh_val).'</a></td>';
                                                            //$html.= ' <td style="text-align:right">'.number_format($soh_val).'='.$soh_arr_c[$itm].'+'.$closing_bal[$itm].'</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                </tr>
                                                <tr class="hide_rows">
                                                    <td colspan="3">(C) Reserved Quantity (<?=(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?>)</td>
                                                     <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                           $res_val = (!empty($reserved_arr[$itm])?$reserved_arr[$itm]:'0');
                                                            $html.= ' <td style="text-align:right"><a style="color: black;" onclick="window.open(\'reservation_detail2.php?id='.$itm.'\', \'_blank\', \'scrollbars=1,width=800,height=600\');">'.number_format($res_val).'</a></td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                </tr>
                                                
                                                <tr class="hide_rows">
                                                    <td colspan="3">(D) Remaining Quantity (<?=(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?>) (B-A-C)</td>
                                                    <?php
                                                    
                                                    $html='';
                                                    foreach ($prod_sequence as $itm)
                                                        {
                                                            $b = (!empty($soh_arr_c[$itm])?$soh_arr_c[$itm]:0);
                                                            $a = (!empty($total_data[$itm])?$total_data[$itm]:0);
                                                            $c = (!empty($reserved_arr[$itm])?$reserved_arr[$itm]:0);
                                                            $d = $b-$a-$c;
                                                            $font='';
                                                            if($c<0) $font=' color:red ';
                                                            $html.= ' <td style="text-align:right;'.$font.'">'.max(number_format($d),0).'</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                </tr>
                                                
                                                    
                                                <tr class="hide_rows">
                                                    <td colspan="3">(E) CWH MOS (<?=(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?>)</td>
                                                    <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            $soh_val = (!empty($soh_arr_c[$itm])?$soh_arr_c[$itm]:'0') +(isset($closing_bal[$itm])?$closing_bal[$itm]:'0');
                                                            $amc = (!empty($amc_arr_c[$itm])?$amc_arr_c[$itm]:'0');
                                                            $mos  = ((!empty($amc) && $amc>0)?$soh_val/$amc:'0');
                                                            
                                                            $html.= ' <td style="text-align:right">'.number_format($mos,1).'</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                </tr>
                                                <tr class="hide_rows">
                                                    <td colspan="3">(F) AMC (<?php echo (!empty($user_province_name)?$user_province_name.' - ':'').(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?>)</td>
                                                    <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            $soh = (!empty($soh_arr_c[$itm])?$soh_arr_c[$itm]:'0') +(isset($closing_bal[$itm])?$closing_bal[$itm]:'0');
                                                            
                                                            if(!empty($amc_arr_c[$itm]) && $amc_arr_c[$itm] > 0)
                                                                $amc = $amc_arr_c[$itm];
                                                            else
                                                                $amc = 0;
                                                            $html.= ' <td style="text-align:right">'.number_format($amc).'</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                </tr>
                                                
                                                <?php
												}
												?>
                                                </tbody>
                                            </table>
                                        </div>
										<?php
												if(isset($r_count) && $r_count>0)
												{
												?>
                                            <div class="row doNotPrintCls col-md-12">
                                                <div class="col-md-12 form-group">
                                                    <label class="control-label">
                                                        <span class="note note-info">* Central Warehouse can round off quantities as per available packing</span>
                                                    </label>

                                                </div>
                                            </div>
											<div class="col-md-12">
												<?=$signature_row_html;?>
											</div>
										
                                                <?php
												}
												else
												{
												?>
												<div class="row doNotPrintCls col-md-12">
													<div class="col-md-12 form-group">
														<label class="control-label">
															<span class="note note-danger">Requisition/s are waiting for approval from Reporting Stakeholders.</span>
															<span class="note note-danger">Those Requisitions will only be available to you after their approval.</span>
														</label>

													</div>
												</div>
												<?php
												}
												?>
                                        </div>
                                                <?php
                                                }//end of if num rows
                                                else
                                                {
                                                    echo 'No pending requisitions for this Time Period';
                                                }
                                                ?>
                                            
                                        
                                        
                                            <input type="hidden" name="month" value="<?php echo $month?>"/>
                                            <input type="hidden" name="year"    value="<?php echo $year?>"/>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Already Submitted Distribution Plans - <?php echo $from_mon.' to '.$to_mon1?></h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" id="" cellspacing="0" align="center" class=" table table-striped table-bordered table-condensed" >
                                    

                                    <?php
                                     $qry = "SELECT
                                                    distinct clr_distribution_plans.pk_id,
                                                    clr_distribution_plans.plan_number,
                                                    clr_distribution_plans.prov_id,
                                                    clr_distribution_plans.plan_status,
                                                    clr_distribution_plans.created_on,
                                                    
                                                    clr_distribution_plans.`month`,
                                                    clr_distribution_plans.`year`,
                                                    (select count(*) from clr_master where clr_master.distribution_plan_id = clr_distribution_plans.pk_id) as total_req
                                                    FROM
                                                    clr_distribution_plans
                                                    INNER JOIN clr_master ON clr_distribution_plans.pk_id = clr_master.distribution_plan_id
                                                    INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                                                    WHERE
                                                    clr_distribution_plans.prov_id =  ".$_SESSION['user_province1']." AND
                                                    clr_distribution_plans_stk.stk_id  in (".implode($integ_stk_arr,",").")  AND
                                                    clr_distribution_plans.`year` =  ".$year." AND
                                                    clr_distribution_plans.`month` =  ".sprintf("%02d", $month)."
                                            ";
                                                
                                    $res = mysql_query($qry);

                                    $disp_data=$total_data=array();
                                    
                                    if($res && mysql_num_rows($res) > 0)
                                    {
                                        echo '<thead>
                                                <tr>
                                                    <th>Sr No</th>
                                                    <th>Plan Number</th>
                                                    <th>No of Requisitions</th>
                                                    <th>Plan Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>';
                                        $c=1;
                                        while($row = mysql_fetch_assoc($res))
                                        {
                                            echo '<tr>';
                                            echo '<td>'.$c++.'</td>';
                                            echo '<td>'.$row['plan_number'].'</td>';
                                            echo '<td>'.$row['total_req'].'</td>';
                                            echo '<td>'.str_replace('_', ' ', $row['plan_status']).'</td>';
                                            echo '<td><a href="distribution_plan_view.php?plan_id='.$row['pk_id'].'&plan_num='.$row['plan_number'].'">View</a></td>';
                                            echo '</tr>';
                                        }
                                    }
                                    else
                                    {
                                        echo 'No Distribution Plan submitted for this time duration.';
                                    }
                                    ?>
                                </table>
                            </div>
                                
                        </div>
                        
                    </div>
                                
                 </div>
                
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    

                                            
    
    <div id="dp_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-full">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Submit Distribution Plan for <?php echo $from_mon.' to '.$to_mon1?> </h4>
            </div>
              <div id="modal_body" class="modal-body">
                Processing...
            </div>
            <div class="modal-footer">
                
                <a id="confirm_approval_btn" class="btn btn-success action_btns">Confirm to Submit As Distribution Plan</a>
                <a onClick="printContents2()" value="Print" class="btn btn-warning" >Print</a>
            
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
          </div>

        </div>
      </div>
    
    <script>
        <?php
        if($count_of_prov_approved == 0)
        {
        ?>
            $('#check_all').hide();
        <?php
        }
        ?>
        $('#check_all').click(function(){
            if($(this).is(':checked'))
            {
                $('.mycheckbox').prop('checked',true);
            }
            else
            {
                $('.mycheckbox').prop('checked',false);
            }
        });
        
        $('.action_btns').click(function() {
            var id = $(this).attr('id');
            $('#form_action').val(id);
            
            checked = $(".mycheckbox:checked").length;

            if(!checked) {
              alert("You must select at least one requisition to approve.");
              return false;
            }
           
            var tbl = $('#get_data_set').html();
            var tbl ='<table id="c_table" width="100%" cellspacing="0" align="center" class=" table table-bordered table-condensed" >';
            $(".row_copy").each(function(e){
                var d = $( this ).attr('data-id');
                console.log('>>'+d);
                var id=$(this).attr('id');
                if(id=='row_head')  
                    var a= '<tr class="" id="row_'+d+'">'+$(this).html()+'</tr>';
                else
                    var a= '<tr class="req_rows hide_rows" id="row_'+d+'">'+$(this).html()+'</tr>';
                tbl += a;
            });
            tbl += '</table>';
            $('#modal_body').html(tbl);
            $('#modal_body').find('#c_table').find('.hide_rows').hide();
            $('#modal_body').find('#c_table').find('input:checkbox').hide();
            
            $(".mycheckbox:checked").each(function(e){
                var d = $( this ).attr('data-id');
                 console.log('show:'+d);
                $('#modal_body').find('#c_table').find('#row_'+d).show();
            });
            
            $('#dp_modal').modal('show');
            //$("#submit").trigger('click');
            
        });
        
        $('#confirm_approval_btn').click(function() {
           
            $("#submit").trigger('click');
            
        });
        
  
        $('.size').keyup(function() {
            var itm = $(this).data('itm');
            var v = $(this).val();
            if(v=='undefined' || v=='0' || v=='' || isNaN(v))
            {    
                v=1;
                $(this).val('');
            }
            else
                $(this).val(v);
            var total_req =0;
            $( ".value_input_"+itm ).each(function( index ) {
                    //console.log( index );
                    var qty = $(this).data('original');
                    console.log('v: '+v);
                    
                    var new_qty = parseFloat(qty) / parseFloat(v);
                    //console.log('a: '+new_qty);
                    
                    var new_qty2 = Math.ceil(new_qty);
                    //console.log('b : '+new_qty2);
                    
                    var new_qty3 = parseFloat(new_qty2) * parseFloat(v);
                    //console.log('c : '+new_qty3);
                    
                    //$(this).closest('.value_input').val(new_qty3);
                   var ii = $(this).closest('.value_input').attr('id');
                   if(new_qty3=='undefined' || new_qty3=='0' || new_qty3=='' || isNaN(new_qty3))
                        new_qty3=0;
                    
                   var disp_1 = $('#'+ii).css('display');
                    
                   $('#'+ii).attr('value',new_qty3);
                   $('#'+ii).val(new_qty3);
                    console.log(ii+' : '+new_qty3+' , display :: '+disp_1);
                    total_req+=new_qty3;
            });
                $('#total_req_'+itm).val(total_req);
                var total_orig = $('#total_req_'+itm).data('soh');
                var prod = $('#total_req_'+itm).data('prod');
                if(total_req > total_orig)
                {
                    toastr.warning('Total requested quantity of '+prod+' has exceeded than the stock available.');
                }
        });
        
        
        $('.value_input').keyup(function() {
            var itm = $(this).data('itm');
            //console.log('itm:'+itm)
            var total_req=0;
            $( ".value_input_"+itm ).each(function( index ) {
                var v = $(this).val();
                
            //console.log('t:'+total_req +','+(v))
            if(isNaN(total_req))total_req=0;
            if(isNaN(v))v=0;
                total_req= parseInt(total_req) + parseInt(v);
            });
            
            $('#total_req_'+itm).val(total_req);
            var total_orig = $('#total_req_'+itm).data('soh');
            var prod = $('#total_req_'+itm).data('prod');
            if(total_req > total_orig)
            {
                toastr.warning('Total requested quantity of '+prod+' has exceeded than the stock available.');
            }
            var tv = $(this).val();
            $(this).attr('value',tv);
        });
        $('.cls_approve_req').click(function() {
            var id = $(this).data('id');
            var act = $(this).data('act');
            var r_name = $(this).data('name');
            var datastr= 'id='+id+'&act='+act;
            $.this = $(this);
            //console.log('ID :'+id);
            $('.pkid_'+id).each(function(){
                var i = $(this).data('itm');
                var val = $(this).val();
                datastr+='&itm_'+i+'='+val;
                //console.log('ID :'+id +',itm:'+i +',val:'+val);
                
                if(act=='Prov_Approved'){
                    //$('#span_'+id+'_'+i).html(val);
                }
            });
            
                //console.log('ID :'+id +',datastr:'+datastr);
            $.ajax({
                    url: 'provincial_approve_requisition.php',
                    type: 'GET',
                    data: datastr,
                    dataType : 'json',
                    success: function(data) {
                       if(data.updated == 'yes'){
                           if(act=='Prov_Saved'){
                                toastr.success('Requisition values for '+r_name+' saved. New Status : Provincial '+data.status);
                               $.this.parents('tr').removeClass('success').addClass('warning');
                               $.this.parents('tr').find('.mycheckbox').hide().removeClass('mycheckbox').addClass('mycheckbox_disabled');
                               //$.this.parents('tr').find('.value_input').show();
                               $('#status_of_'+id).html('Prov '+data.status);
                               $('.cls_approve_req[data-id='+id+'][data-act=Prov_Approved]').show();
                               
                           }
                           if(act=='Prov_Approved'){
                                toastr.success('Requisition values for '+r_name+' saved. New Status : Provincial '+data.status);
                               $.this.parents('tr').removeClass('warning').addClass('success');
                               $.this.parents('tr').find('.mycheckbox_disabled').show().removeClass('mycheckbox_disabled').addClass('mycheckbox');
                               //$.this.parents('tr').find('.value_input').hide();
                               $('#status_of_'+id).html('Prov '+data.status);
                               $('.cls_approve_req[data-id='+id+'][data-act=Prov_Approved]').hide();
                           }
                           
                           
                       }
                       else
                       {
                           toastr.error('We could not save the changes for '+r_name+' at the moment.');
                       }
                    }
                })
        });
        
        
    </script>
    
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>