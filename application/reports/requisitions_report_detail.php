<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

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

$month=$year='';
if(!empty($_REQUEST['month']))      $month = $_REQUEST['month'];
if(!empty($_REQUEST['year']))       $year = $_REQUEST['year'];
if(!empty($_REQUEST['wh_id']))      $wh_id = $_REQUEST['wh_id'];
if(!empty($_REQUEST['stk_id']))     $stk_id = $_REQUEST['stk_id'];




$q1 = " SELECT
	*
	FROM
	stakeholder
	WHERE
	stkid = '".$stk_id."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_stakeholder_name = $row['stkname'];

$q1 = "SELECT
        tbl_warehouse.wh_id,
        tbl_warehouse.wh_name
        FROM
        tbl_warehouse
        WHERE
        tbl_warehouse.wh_id =  '".$wh_id."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$wh_name = $row['wh_name'];




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
            docprint.document.write('<html><head><h3 style="align:center">Provincial Approvals & Distribution Plan ( Pending Requisitions )</h3>');

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

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Requisition Details </h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <h4 class="center"><?php echo $from_mon.' to '.$to_mon1?></h4>
                                    <h5 class="center"><?php echo (!empty($wh_name)?$wh_name.' - ':'').(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?></h5>
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
                                                
                                                
                                                //echo '<pre>';print_r($soh_arr_c);print_r($curr_stock);exit;
                                        
                                                if($approve_dist_reqs_is_active) $in_list=" ";
                                                else $in_list=" ,'Pending' ";
                                                
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
                                                            
                                                            tbl_locations.LocLvl = 3 AND
                                                            
                                                            MONTH(clr_master.date_to) = '".$month."'AND
                                                            YEAR(clr_master.date_to) = '".$year."'
                                                                
                                                            AND clr_master.wh_id = ".$wh_id."
                                                        ORDER BY
                                                            tbl_locations.PkLocID ASC,
                                                            clr_details.itm_id
                                                        ";
                                                //echo $qry;
                                                $res = mysql_query($qry);
                                       
                                               
                                                
                                                
                                                $disp_data=$total_data=array();
                                                if( mysql_num_rows($res) > 0)
                                                {
                                                    ?>
                                        <div id="get_data_set">
                                                    <table width="100%" id="myTable" cellspacing="0" align="center" class=" table  table-bordered table-condensed" >


                                                        <thead>
                                                            <tr id="row_head" class="info row_copy" data-id="head1">
                                                               <td rowspan="" style="text-align:center;" width="40">S. No.</td>
                                                                <td rowspan="" id="desc" width="150">District - Stakeholder</td>
                                                                <?php
                                                                    foreach ($prod_sequence as $itm)
                                                                    {
                                                                        echo '<td  style="min-width:50px" >'.$names[$itm].'</td>';
                                                                    }
                                                                ?>                                               
                                                                
                                                                <td rowspan="" style="">Action</td>
                                                                <td rowspan="" style="width:80px;">Status</td>
                                                                <td rowspan="" style="width:80px;">Remarks</td>
                                                            </tr>
                                                            <tr  class="info">
                                                               
                                                                
                                                            </tr>
                                                        </thead>


                                                        <tbody>    
                                                    <?php
                                                    
                                                //echo '<pre>';
                                                $stk  = $stk_name = array();
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    //print_r($row);
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
                                                if(!empty($disp_data))
                                                {
                                                    foreach($disp_data as $pk_id => $data)
                                                    {
                                                        
                                                        $c++;
                                                        $html= '<tr id="row_'.$c.'" class="req_rows row_copy" data-id="'.$c.'">';
                                                        
                                                        
                                                        $html.= ' <td style="text-align:center">'.$c.'</td>';
                                                        $html.= ' <td>'.$data['district'].' - '.$data['stkname'].'</td>';
                                                        
                                                         
                                                        foreach ($prod_sequence as $itm)
                                                        {
                                                            
                                                            //   $html.= ' <td style="text-align:right">'.number_format($data[$itm]).'</td>';
                                                            if(isset($data[$itm]) && $data[$itm]>=0)
                                                                $html.= ' <td style="text-align:right">'.number_format($data[$itm]).'</td>';
                                                            else
                                                                $html.= ' <td class="danger"></td>';
                                                        }
                                                        $html.= ' <td style="text-align:center">';
                                                        $html.= '<a href="../im/clr_view.php?id='.$pk_id.'&wh_id='.$data['wh_id'].'">View</a>';
                                                        
                                                       
                                                        $html.= ' </td>';
                                                        $html.= ' <td style="text-align:center">'.str_replace('_',' ',$data['approval_status']).'</td>';
                                                        $html.= ' <td style="text-align:center"></td>';

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
                                                    <td colspan="2" rowspan="7" align="left">
                                                        <div id ="doNotPrint">
                                                            <input type="hidden" name="form_action" id="form_action" value="" >
                                                            <input type="hidden" name="multi_stk_id" id="multi_stk_id" value="<?php echo implode($stk,',')?>" >
                                                            <input type="hidden" name="multi_stk_name" id="multi_stk_name" value="<?php echo implode($stk_name,',')?>" >
                                                            <a onClick="printContents()" value="Print" class="btn btn-warning" >Print</a>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                
                                                    
                                                
                                                
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label class="control-label">
                                                        <span class="note note-info">* Central Warehouse can round off quantities as per available packing</span>
                                                    </label>

                                                </div>
                                            </div>
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
                
                
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    

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
       
        
        $('html, body').animate({ scrollTop: $('#'+div_id).offset().top }, 'slow');
    
    }
    </script>
    
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>