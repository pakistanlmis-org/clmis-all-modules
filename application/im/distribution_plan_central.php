<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

if(!empty($_REQUEST['year_sel']))
    $selYear = $_REQUEST['year_sel'];
else
    $selYear = date('Y'); 
//creating temp array for months of year
$mon_year_arr=array();
for($i=1;$i<=12;$i++)
{
    $mon_year_arr[$i]=$i.'-'.$selYear;
}
//echo '<pre>';print_r($_SESSION);exit;
$q1 = "select * from stakeholder where stkid = '".$_SESSION['user_stakeholder1']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_stakeholder_name = $row['stkname'];

?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Central Distribution Plans - <?php echo (!empty($user_stakeholder_name)?$user_stakeholder_name:''); ?> - All Provinces</h3>
                            </div>
                            <div class="widget-body">
                                <a href="new_clr_open.php" target="_blank" class="btn btn-green green btn-sm doNotPrintCls" >Add Manually Recieved Requisition</a>
                                                    
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div style="margin-left:0px !important; width:100% !important;">
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
                                        <table width="100%" id="myTable"  class="requisitions table   table-bordered table-condensed" cellspacing="0" align="center">
                                            
                                            
                                            <thead>
                                                <tr>
                                                    <td style="text-align:center;" >S. No.</td>
                                                    <td style="text-align:center;" >Plan Number</td>
                                                    <td style="text-align:center;" >Created Date</td>
                                                    <td style="text-align:center;" >Stakeholder</td>
                                                    <td style="text-align:center;" >Province</td>
                                                    <td style="text-align:center;" >No of Requisitions</td>
                                                    <td style="text-align:center;" >Duration From</td>
                                                    <td style="text-align:center;" >Duration To</td>
                                                    <td style="text-align:center;" >Status</td>
                                                    <td style="text-align:center;" >Action</td>
                                                    <td style="text-align:center;" >Remarks</td>
                                                </tr>
                                            </thead>
                                            
                                            
                                            <tbody>
                                                <?php
                                                
                                                  
                                                  $qry = "SELECT
                                                                clr_distribution_plans.pk_id,
                                                                clr_distribution_plans.plan_number,
                                                                clr_distribution_plans.prov_id,
                                                                clr_distribution_plans.plan_status,
                                                                clr_distribution_plans.created_on,
                                                                
                                                                clr_distribution_plans.`month`,
                                                                clr_distribution_plans.`year`,
                                                                count(distinct clr_master.pk_id) as total_reqs,
                                                                sum(CASE WHEN clr_master.approval_status= 'Approved' THEN 1 else 0 END) as approved,
                                                                sum(CASE WHEN clr_master.approval_status= 'Issued' THEN 1 else 0 END) as issued,
                                                                sum(CASE WHEN clr_master.approval_status= 'Issue in Process' THEN 1 else 0 END) as iip,
                                                                GROUP_CONCAT( distinct stakeholder.stkname) as stkname,
                                                                
                                                                tbl_locations.LocName,
                                                                (SELECT
                                                                                requisition_module_flow.is_active
                                                                        FROM
                                                                                requisition_module_flow
                                                                        WHERE
                                                                                requisition_module_flow.action_id = 5
                                                                        AND requisition_module_flow.can_submit_to = 6
                                                                        AND requisition_module_flow.prov_id = clr_distribution_plans.prov_id
                                                                        AND requisition_module_flow.stk_id = clr_distribution_plans_stk.stk_id
                                                                        limit 1) as dp_approval_is_active
                                                            FROM
                                                                clr_distribution_plans
                                                                INNER JOIN clr_master ON clr_master.distribution_plan_id = clr_distribution_plans.pk_id
                                                                INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                                                                INNER JOIN stakeholder ON clr_distribution_plans_stk.stk_id = stakeholder.stkid
                                                                INNER JOIN tbl_locations ON clr_distribution_plans.prov_id = tbl_locations.PkLocID
                                                            
                                                            WHERE
                                                                    clr_master.requisition_to = ".$_SESSION['user_warehouse']."     
                                                                   
                                                            GROUP BY
                                                                clr_distribution_plans.pk_id
                                                            ORDER BY 
                                                                clr_distribution_plans.created_on DESC
                                                        ";
                                                //echo $qry;exit;
                                                $res = mysql_query($qry);
                                                $dist_data=$total_data=array();
                                                $c=1;
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    if(!empty($row['dp_approval_is_active'] && $row['dp_approval_is_active'] == '1')) 
                                                        $dp_approval_is_active =TRUE;
                                                    else
                                                        $dp_approval_is_active =FALSE;
                                                    
                                                    
                                                    $to_mon = (sprintf("%02d", $row['month'])).'-'.$row['year'];
                                                    $from_mon = date('M-Y', strtotime('01-'.$to_mon.' -2 months'));
                                                    $to_mon1 = date('M-Y',strtotime('01-'.$to_mon));
                                                    $cls='';
                                                    $status=str_replace('_',' ',$row['plan_status']);
                                                    if($row['total_reqs'] == $row['issued']){
                                                        $status='Issued';
                                                        $cls = " success ";
                                                    }
                                                    else if($row['issued'] > 0 || $row['iip'] > 0){
                                                        $status='Partial Issued';
                                                        $cls = " warning ";
                                                    }
                                                        
                                                    echo '<tr class="'.$cls.'">';
                                                    echo '<td class="center">'.$c++.'</td>';
                                                    echo '<td class="center">'.$row['plan_number'].'</td>';
                                                    echo '<td class="center">'.date('Y-M-d',strtotime($row['created_on'])).'</td>';
                                                    echo '<td class="center">'.$row['stkname'].'</td>';
                                                    echo '<td class="center">'.$row['LocName'].'</td>';
                                                    echo '<td class="center">'.$row['total_reqs'].'</td>';
                                                    echo '<td class="center">'.$from_mon.'</td>';
                                                    echo '<td class="center">'.$to_mon1.'</td>';
                                                    
                                                    if($dp_approval_is_active && $row['plan_status']=='Submitted'){
                                                        echo '<td class="center">Approval Pending</td>';
                                                        echo '<td class="center"> </td>';
                                                    }
                                                    else{
                                                        echo '<td class="center">'.$status.'</td>';
                                                        echo '<td class="center"><a href="distribution_plan_issue.php?plan_id='.$row['pk_id'].'&plan_num='.$row['plan_number'].'">Open</a></td>';
                                                    }
                                                    echo '<td class="center"> </td>';
                                                    echo '</tr>';
                                                }
                                                 
                                                 //manual requisitions ...  
                                                  $qry = "SELECT
                                                        count(clr_master.pk_id) as cnt
                                                        FROM
                                                        clr_master
                                                        WHERE
                                                        clr_master.approval_status in ('Hard_Copy','Hard_Copy_Issued')

                                                        ";
                                                //echo $qry;exit;
                                                $res = mysql_query($qry);
                                                $dist_data=$total_data=array();
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    echo '<tr>';
                                                    echo '<td class="center">'.$c++.'</td>';
                                                    echo '<td class="center" colspan="4">Manual Requisitions</td>';
                                                    echo '<td style="display: none;"></td>';
                                                    echo '<td style="display: none;"></td>';
                                                    echo '<td style="display: none;"></td>';
                                                    echo '<td class="center">'.$row['cnt'].'</td>';
                                                    
                                                    echo '<td class="center" colspan="3"></td>';
                                                    echo '<td style="display: none;"></td>';
                                                    echo '<td style="display: none;"></td>';
                                                    echo '<td class="center"><a href="list_manual_requisitions.php">Open</a></td>';

                                                    echo '<td class="center"></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                
                                                
                                            </tbody>
                                        </table>
                                       
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
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>