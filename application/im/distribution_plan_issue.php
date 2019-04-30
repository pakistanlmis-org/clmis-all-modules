<?php
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

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
                    stakeholder_item.stkid = ".$_SESSION['user_stakeholder1']."
                        AND itm_category = 1
            ORDER BY
                    itminfo_tab.method_rank,itminfo_tab.itm_id ASC
";
$res= mysql_query($qry_itm);
$itm_name_id=$product=$itemIds =array();
//print_r($_SESSION);
while($row= mysql_fetch_assoc($res))
{
    //$itm_name_id[$row['itm_name']] = $row['itm_id'];
    $product[$row['method_type']][] = $row['itm_name'];
    $names[$row['itm_id']] = $row['itm_name'];
    $prod_sequence[]=$row['itm_id'];
}
$to_mon = (sprintf("%02d", $month)).'-'.$year;
$from_mon = date('M-Y', strtotime('01-'.$to_mon.'-2 months'));
$to_mon1 = date('M-Y',strtotime('01-'.$to_mon));

?>
    <script>
     function printContents() {
            var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
            var printingContents = document.getElementById("printing").innerHTML;

            var docprint = window.open("", "", printing);

            docprint.document.open();
            docprint.document.write('<html><head><title>Distribution Plan</title>');

            docprint.document.write('</head><body onLoad="self.print();"><center>');
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
        clr_master.pk_id,
        clr_master.requisition_num,
        clr_master.requisition_to,
        clr_master.wh_id,
        clr_master.stk_id,
        clr_master.fk_stock_id,
        clr_master.date_from,
        clr_master.date_to,
        clr_master.requested_by,
        clr_master.requested_on,
        clr_master.approval_status,
        clr_master.distribution_plan_id,
        clr_distribution_plans.plan_number,
        clr_distribution_plans.`month`,
        clr_distribution_plans.`year`,
        clr_distribution_plans.prov_id,
        clr_distribution_plans.plan_status,
        tbl_warehouse.wh_name,
        clr_details.itm_id,
        clr_details.approve_qty as req,
        clr_details.qty_req_prov,
        stakeholder.stkname,
        tbl_locations.LocName
        FROM
        clr_master
        INNER JOIN clr_distribution_plans ON clr_master.distribution_plan_id = clr_distribution_plans.pk_id
        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
        INNER JOIN stakeholder ON clr_master.stk_id=stakeholder.stkid
        INNER JOIN tbl_locations ON clr_distribution_plans.prov_id = tbl_locations.PkLocID
        WHERE
        clr_master.distribution_plan_id = ".$_REQUEST['plan_id']."
    ";
    //echo $qry;exit;
    $res = mysql_query($qry);
    $stk_names=$disp_data=$total_data=array();

    while($row = mysql_fetch_assoc($res))
    {
        $stk_name = $row['stkname'];
        $province_name = $row['LocName'];
        
        $to_mon = (sprintf("%02d", $row['month'])).'-'.$row['year'];
        $from_mon = date('M-Y', strtotime('01-'.$to_mon.' -2 months'));
        $to_mon1 = date('M-Y',strtotime('01-'.$to_mon));
                                                    
        $disp_data[$row['pk_id']][$row['itm_id']] = $row['req'];
        $disp_data[$row['pk_id']]['wh_name'] = $row['wh_name'];
        $disp_data[$row['pk_id']]['stkname'] = $row['stkname'];
        $disp_data[$row['pk_id']]['approval_status'] = $row['approval_status'];
        $disp_data[$row['pk_id']]['requisition_num'] = $row['pk_id'];
        $disp_data[$row['pk_id']]['wh_id'] = $row['wh_id'];
        $stk_names[$row['stkname']] =$row['stkname'];

        if(!empty($total_data[$row['itm_id']]))
            $total_data[$row['itm_id']] += $row['req'];
        else
            $total_data[$row['itm_id']] = $row['req'];
    }
    //echo '<pre>';print_r($disp_data);exit;
?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head" >
                                <h3 class="heading ">Central Distribution Plan -  ( Plan Number : <?php echo $_REQUEST['plan_num']?>)</h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <h4 class="heading center">Stock issuance against distribution plan of <?php echo $province_name.'-'.implode(',',$stk_names) ;?></h4>
                                    <h5 class="heading center"><?php echo ''.$from_mon.' to '.$to_mon1.'';?></h5>
                                <?php
                                        $fileName='Distribution_plan';
                                        //include sub_dist_reports
                                        include('sub_export_options.php');
                                    ?>
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
                                        
                                        <div class="col-md-12" id="get_data_set">
                                                    <table width="100%" id="myTable" cellspacing="0" align="center" class=" table  table-bordered table-condensed" >

                                                        <thead>
                                                            <tr id="row_head" class="info" >
                                                                <td rowspan="" style="text-align:center;" width="40">S. No.</td>
                                                                <td rowspan="" id="desc" width="250">Requisition From</td>
                                                                <?php
                                                                    foreach ($prod_sequence as $itm)
                                                                    {
                                                                        echo '<td>'.$names[$itm].'</td>';
                                                                    }
                                                                ?>                                                  
                                                                <td rowspan="" style="width:80px;">Action</td>
                                                                <td rowspan="" style="width:80px;">Status</td>
                                                                <td rowspan="" style="width:80px;">Issued Vouchers</td>
                                                            </tr>
                                                            <tr  class="info">
                                                               
                                                            </tr>
                                                        </thead>

                                                        <tbody>    
                                                    <?php
                                                
                                                $c=0;
                                                $count_of_prov_approved=0;
                                                if(!empty($disp_data))
                                                {
                                                    foreach($disp_data as $pk_id => $data)
                                                    {
                                                        $c++;
                                                        
                                                        $cls= "";
                                                        if($data['approval_status']=='Issued') $cls= " success ";
                                                        if($data['approval_status']=='Issue in Process') $cls= " warning ";
                                                        
                                                        $html= '<tr class="'.$cls.'" id="row_'.$c.'">';
                                                       
                                                        
                                                        $html.= ' <td style="text-align:center">'.$c.'</td>';
                                                        $html.= ' <td>'.$data['wh_name'].' - '.$data['stkname'].'</td>';
                                                        
                                                         
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            if(isset($data[$itm]) && $data[$itm]>=0)
                                                                $html.= ' <td style="text-align:right">'.number_format($data[$itm]).'</td>';
                                                            else
                                                                $html.= ' <td class="danger"></td>';
                                                           
                                                        }
                                                        $html.= ' <td style="text-align:center">';
                                                        $html.= '<a href="clr_view.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'">View</a>';
                                                        
                                                        if($data['approval_status']=='Approved' || $data['approval_status']=='Issue in Process')
                                                        $html.= ' | <a href="issue.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'&rq='.$data['requisition_num'].'&ref_page=distribution_plan_issue&plan_id='.$_REQUEST['plan_id'].'&plan_num='.$_REQUEST['plan_num'].'">Issue</a>';
                                                        
                                                        $html.= ' </td>';
                                                        $html.= ' <td style="text-align:center">'.str_replace('_',' ',$data['approval_status']).'</td>';
                                                        
                                                         $qry_v = "SELECT DISTINCT               tbl_stock_master.TranNo,
                                                                                                tbl_stock_master.PKStockId,
                                                                                                tbl_stock_detail.IsReceived
                                                                                        FROM
                                                                                                clr_details
                                                                                        INNER JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
                                                                                        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                                                        WHERE
                                                                                                tbl_stock_master.TranTypeID = 2 AND
                                                                                                clr_details.pk_master_id = " . $pk_id . "
                                                                                        ORDER BY
                                                                                                tbl_stock_master.PkStockID ASC";
                                                        
                                                        $getStockIssues = mysql_query($qry_v) or die("Err GetStockIssueId");

                                                        $issueVoucher = '';
                                                        //chech if record exists
                                                        if (mysql_num_rows($getStockIssues) > 0) {
                                                            
                                                            //fetch results
                                                            while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
                                                                $issueVoucher .= "<div> ";
                                                                $issueVoucher .= " <a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $resStockIssues['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $resStockIssues['TranNo'] . "</a>";
                                                                $issueVoucher .= "</div> ";    

                                                            }
                                                        }
                                                        
                                                        $html.= ' <td style="text-align:center">'.(!empty($issueVoucher)?$issueVoucher:'').'</td>';

                                                        $html.= ' </tr>';
                                                        
                                                        echo $html;
                                                    }
                                                }
                                                
                                                ?>
                                                <tr style="text-align:right"  class="info hide_rows">
                                                    <td colspan="2">Total Quantity</td>
                                                    <?php
                                                        $html='';
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            $html .= ' <td style="text-align:right">'.number_format(!empty($total_data[$itm])?$total_data[$itm]:'0').'</td>';
                                                        }
                                                        echo $html;
                                                    ?>
                                                    <td colspan="3" rowspan="7" align="left">
                                                        <div>
                                                            <a onClick="printContents()" value="Print" class="btn btn-warning" >Print</a>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                
                                                </tbody>
                                            </table>
                                            
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