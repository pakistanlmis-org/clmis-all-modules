<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'approve' )
{
    $qry2 = " UPDATE clr_distribution_plans SET plan_status = 'Plan_Approved' WHERE pk_id = ".$_REQUEST['plan_id']."   ";
    $qryRes3 = mysql_query($qry2);
    
    redirect('distribution_plan_approvals.php');  
}



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



if(!isset($_SESSION['integrated_stakeholder']))
{
        $qry2 = "  SELECT
                        integrated_stakeholders.main_stk_id,
                        integrated_stakeholders.sub_stk_id,
                        stakeholder.stkname
                    FROM
                    integrated_stakeholders
                    INNER JOIN stakeholder ON integrated_stakeholders.sub_stk_id = stakeholder.stkid
                    WHERE
                    integrated_stakeholders.province_id = ".$_SESSION['user_province1']." AND
                    integrated_stakeholders.main_stk_id = ".$_SESSION['user_stakeholder1']."  ";

        $qryRes3 = mysql_query($qry2);
        $num3 = mysql_num_rows($qryRes3);
        if($num3>0)
        {
                while($row=mysql_fetch_assoc($qryRes3))
                {
                    $_SESSION['integrated_stakeholder']='yes';
                    $_SESSION['sub_stakeholders'][$row['sub_stk_id']]=$row['stkname'];
                }
        }
        else
        {
                $_SESSION['integrated_stakeholder']='no';
        }
}

$integ_stk_arr = array();
foreach($_SESSION['sub_stakeholders'] as $sub_id => $sub_name)
{
    $integ_stk_arr[$sub_id] = $sub_id;
}
//echo '<pre>';print_r($_SESSION);exit;
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

                <!-- BEGIN PAGE HEADER-->
                <div class="row hide">
                    <form name="frm" id="frm" action="" method="get">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter By</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3" >
                                                <div class="control-group ">
                                                    <label class="control-label">Year</label>
                                                    <div class="controls">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm" required>
                                                            <?php
                                                            if (empty($selYear)) $selYear= date('Y'); 
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 right">
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" value="Search" class="btn btn-primary" />
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                            
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Central Distribution Plans - <?=(implode(',',$_SESSION['sub_stakeholders']))?> </h3>
                            </div>
                            <div class="widget-body">
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
                                        <table width="100%" id="myTable"  class="requisitions table table-striped table-bordered table-condensed" cellspacing="0" align="center">
                                            
                                            
                                            <thead>
                                                <tr>
                                                    <td style="text-align:center;" >S. No.</td>
                                                    <td style="text-align:center;" >Plan Number</td>
                                                    <td style="text-align:center;" >Stakeholder</td>
                                                    <td style="text-align:center;" >Province</td>
                                                    <td style="text-align:center;" >No of Requisitions</td>
                                                    <td style="text-align:center;" >Duration From</td>
                                                    <td style="text-align:center;" >Duration To</td>
                                                    <td style="text-align:center;" >Status</td>
                                                    <td style="text-align:center;" >Action</td>
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
                                                            clr_distribution_plans_stk.stk_id,
                                                            stakeholder.stkname,
                                                            tbl_locations.LocName,
                                                            count(clr_master.pk_id) as num_of_reqs,
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
                                                            clr_distribution_plans_stk.stk_id in (".implode(',',$integ_stk_arr).")   AND
                                                            clr_distribution_plans.prov_id = ".$_SESSION['user_province1']."             
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
                                                    
                                                    $status=$row['plan_status'];
                                                    echo '<tr>';
                                                    echo '<td class="center">'.$c++.'</td>';
                                                    echo '<td class="center">'.$row['plan_number'].'</td>';
                                                    echo '<td class="center">'.$row['stkname'].'</td>';
                                                    echo '<td class="center">'.$row['LocName'].'</td>';
                                                    echo '<td class="center">'.$row['num_of_reqs'].'</td>';
                                                    echo '<td class="center">'.$from_mon.'</td>';
                                                    echo '<td class="center">'.$to_mon1.'</td>';
                                                    echo '<td class="center">'.str_replace('_', ' ', $status).'</td>';
                                                    echo '<td class="center">';
                                                    echo '<a href="distribution_plan_view.php?plan_id='.$row['pk_id'].'&plan_num='.$row['plan_number'].'">View</a>';
                                                    
                                                    if ($dp_approval_is_active && $status == 'Submitted')
                                                    echo ' | <a href="distribution_plan_approvals.php?plan_id='.$row['pk_id'].'&do=approve">Approve</a>';
                                                    
                                                    echo '</td>';
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