<?php
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
$itm_name_id=$product=$itemIds =array();
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
clr_master.attachment_name,
tbl_warehouse.wh_name,
clr_details.itm_id,
clr_details.qty_req_dist_lvl1 AS req,
clr_details.qty_req_prov,
stakeholder.stkname,
itminfo_tab.itm_name,
itminfo_tab.method_rank,
itminfo_tab.method_type,
tbl_warehouse.prov_id,
tbl_locations.LocName as prov_name
FROM
clr_master
INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
WHERE
	clr_master.approval_status in ('Hard_Copy','Hard_Copy_Issued')
ORDER BY
tbl_warehouse.prov_id,
tbl_warehouse.stkid,
tbl_warehouse.wh_name ASC,clr_master.pk_id desc,
itminfo_tab.method_rank ASC

    ";
//    echo $qry;exit;
    $res = mysql_query($qry);
    $stk_names=$disp_data=$total_data=array();

    while($row = mysql_fetch_assoc($res))
    {
        //$product[$row['method_type']][] = $row['itm_name'];
        if(!isset($names[$row['itm_id']]))
        $names[$row['itm_id']] = $row['itm_name'];
        //$prod_sequence[]=$row['itm_id'];
        
        $stk_name = $row['stkname'];
        $province_name = '';
        
        $to_mon = $row['date_to'];
        $from_mon = $row['date_from'];
        $to_mon1 = date('M-Y',strtotime('01-'.$to_mon));
                                                    
        $disp_data[$row['pk_id']][$row['itm_id']] = $row['req'];
        $disp_data[$row['pk_id']]['wh_name'] = $row['wh_name'];
        $disp_data[$row['pk_id']]['stkname'] = $row['stkname'];
        $disp_data[$row['pk_id']]['approval_status'] = $row['approval_status'];
        $disp_data[$row['pk_id']]['attachment_name'] = $row['attachment_name'];
        $disp_data[$row['pk_id']]['requisition_num'] = $row['pk_id'];
        $disp_data[$row['pk_id']]['wh_id'] = $row['wh_id'];
        $disp_data[$row['pk_id']]['prov_id'] = $row['prov_id'];
        $disp_data[$row['pk_id']]['stk_id'] = $row['stk_id'];
        $disp_data[$row['pk_id']]['prov_name'] = $row['prov_name'];
        $disp_data[$row['pk_id']]['date_to'] = $row['date_to'];
        $disp_data[$row['pk_id']]['date_from'] = $row['date_from'];
        $stk_names[$row['stkname']] =$row['stkname'];

        if(!empty($total_data[$row['itm_id']]))
            $total_data[$row['itm_id']] += $row['req'];
        else
            $total_data[$row['itm_id']] = $row['req'];
    }
//    echo '<pre>';print_r($disp_data);exit;
?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head" >
                                <h3 class="heading ">Manually Submitted Requisitions</h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div class="col-md-9">
                                        <h4 class="heading center">Stock issuance against manually submitted requisitions</h4>
                                     </div>
                                    <div class="col-md-3">
                                        <a href="new_clr_open.php?redirect_to=list_manual_requisitions" target="_blank" class="btn btn-green green btn-sm doNotPrintCls" >Add Manually Recieved Requisition</a>
                                     </div>
                                    
                                    <h5 class="heading center"> </h5>
                                
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
                                                
                                                if( mysql_num_rows($res) > 0)
                                                {
                                                    ?>
                                        <div id="get_data_set">
                                                    <table width="100%" id="myTable" cellspacing="0" align="center" class=" table  table-bordered table-condensed" >

                                                        <thead>
                                                            <tr id="row_head" class="info" >
                                                                <td rowspan="" style="text-align:center;" width="40">S. No.</td>
                                                                <td rowspan="" id="desc" width="200">Requisition From</td>
                                                                <td rowspan="" id="desc" width="150">From - To</td>
                                                                <?php
                                                                $col_cnt = 7;
                                                                    foreach ($names as $itmid => $itm_name)
                                                                    {
                                                                        echo '<td>'.$itm_name.'</td>';
                                                                        $col_cnt++;
                                                                    }
                                                                ?>                                                  
                                                                <td rowspan="" style="width:80px;">Action</td>
                                                                <td rowspan="" style="width:140px;">Status</td>
                                                                <td rowspan="" style="width:80px;">Attachments</td>
                                                                <td rowspan="" style="width:80px;">Issued Vouchers</td>
                                                            </tr>
                                                            <tr  class="info">
                                                               
                                                            </tr>
                                                        </thead>

                                                        <tbody>    
                                                    <?php
                                                
                                                $c=0;
                                                $count_of_prov_approved=0;
                                                $last_prov ='';
                                                if(!empty($disp_data))
                                                {
                                                    foreach($disp_data as $pk_id => $data)
                                                    {
                                                        $html= '';
                                                        $c++;
                                                        
                                                        $cls= "";
                                                        if($data['approval_status']=='Hard_Copy_Issued'){ $cls= " success "; }
                                                        
                                                        if($last_prov != $data['prov_id']){
                                                            $html .= '<tr class="info" ><td colspan="'.$col_cnt.'">Province: '.$data['prov_name'].'</td></tr>';
                                                        }
                                                        $last_prov = $data['prov_id'];
                                                        $html.= '<tr class="'.$cls.'" id="row_'.$c.'">';
                                                       
                                                        
                                                        $html.= ' <td style="text-align:center">'.$c.'</td>';
                                                        
                                                        $clr_array = array();
                                                        $clr_array['1'] = 'green';
                                                        $clr_array['2'] = 'purple-plum';
                                                        $clr_array['7'] = 'grey-cascade';
                                                        $clr_array['9'] = 'red-pink';
                                                        $clr_array['73'] = 'yellow';
                                                        $span_class= ' class="btn btn-xs bg-'.(!empty($clr_array[$data['stk_id']])?$clr_array[$data['stk_id']]:'yellow').'  " ';
                                                        $html.= ' <td>'.$data['wh_name'].' - <a '.$span_class.'>'.$data['stkname'].'</a></td>';
                                                        $html.= ' <td>'.date('M Y',strtotime($data['date_from'])).' - '.date('M Y',strtotime($data['date_to'])).'</td>';
                                                        
                                                         
                                                        foreach ($names as $itmid => $itm_name)
                                                        {
                                                            if(isset($data[$itmid]) && $data[$itmid]>=0)
                                                                $html.= ' <td style="text-align:right">'.number_format($data[$itmid]).'</td>';
                                                            else
                                                                $html.= ' <td class="danger"></td>';
                                                           
                                                        }
                                                        $html.= ' <td style="text-align:center">';
                                                        $html.= '<a href="clr_view_manual.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'">View</a>';
                                                        
                                                        if($data['approval_status']=='Hard_Copy' || $data['approval_status']=='Approved' || $data['approval_status']=='Issue in Process')
                                                        $html.= ' | <a href="issue_manual_req.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'&rq='.$data['requisition_num'].'&ref_page=list_manual_requisitions">Issue</a>';
                                                        
                                                        $html.= ' </td>';
                                                        if($data['approval_status']=='Hard_Copy')
                                                            $html.= ' <td style="text-align:center">Manually Submitted </td>';
                                                        elseif($data['approval_status']=='Hard_Copy_Issued')
                                                            $html.= ' <td style="text-align:center">Issued (Manual)</td>';
                                                        else
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
                                                        
                                                        $html.= ' <td style="text-align:center">'.(!empty($data['attachment_name'])?'<a target="_blank" href="../../user_uploads/requisitions_attachments/'.$data['attachment_name'].'">'.$data['attachment_name'].'</a>':'').'</td>';
                                                        $html.= ' <td style="text-align:center">'.(!empty($issueVoucher)?$issueVoucher:'').'</td>';

                                                        $html.= ' </tr>';
                                                        
                                                        echo $html;
                                                    }
                                                }
                                                
                                                ?>
                                                <tr style="text-align:right"  class="info hide_rows">
                                                    <td colspan="3">Total Quantity</td>
                                                    <?php
                                                        $html='';
                                                        foreach ($names as $itmid => $itm_name)
                                                        {
                                                            $html .= ' <td style="text-align:right">'.number_format(!empty($total_data[$itmid])?$total_data[$itmid]:'0').'</td>';
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