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


$plan_status = (isset($_REQUEST['plan_status'])?$_REQUEST['plan_status']:'');

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

if(!empty($_REQUEST['year_sel']))
    $selYear = $_REQUEST['year_sel'];
else
    $selYear = date('Y'); 

//creating temp array for months of year
$mon_year_arr=$to_arr=$to_arr2=array();
for($i=1;$i<=12;$i++)
{
    $mon_year_arr[$i]=date('M-Y',strtotime($selYear.'-'.$i.'-01'));
    $a=strtotime($selYear.'-'.$i.'-01');
    $to_arr[$i]=date('M-Y',strtotime('+2 month',$a));
    $temp=date('m',strtotime('+2 month',$a));
    $to_arr2[$i]=(int)$temp;
}
//echo '<pre>';print_r($to_arr2);exit;

$q1 = "select * from tbl_locations where PkLocID = '".$_SESSION['user_province1']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_province_name = $row['LocName'];

$q1 = "select stkid,stkname from stakeholder ";
$res1 = mysql_query($q1);
$stk_all_arr = array();

while($row = mysql_fetch_assoc($res1))
{
    $stk_all_arr[$row['stkid']] = $row['stkname'];
}


$objuserstk->m_npkId = $_SESSION['user_id'];
$requisition_stk = $objuserstk->GetStkByUserId();
//echo '<pre>';print_r($requisition_stk);exit;


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
                <div class="row">
                    <form name="frm" id="frm" action="" method="get">
                        <div class="col-md-12">
                            <div class="widget  " data-toggle="collapse-widget">
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
                                            
                                            <div class="col-md-3 hide" >
                                                <div class="control-group ">
                                                    <label class="control-label">Plan Status</label>
                                                    <div class="controls">
                                                        <select name="plan_status" id="plan_status" class="form-control input-sm" >
                                                            <option <?=(($plan_status=='all')?' selected ':'')?> value="all">All</option>
                                                            <option <?=(($plan_status=='Submitted')?' selected ':'')?> value="Submitted">Submitted</option>
                                                            <option <?=(($plan_status=='Issue in Progress')?' selected ':'')?> value="Issue in Progress">Issue in Progress</option>
                                                            <option <?=(($plan_status=='Issued')?' selected ':'')?> value="Issued">Issued</option>
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
                                <h3 class="heading">Submitted Distribution Plans - <?php echo (!empty($user_province_name)?$user_province_name.' - ':'').((implode(',',$_SESSION['sub_stakeholders'])))?></h3>
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
                                                    <td style="text-align:center;" >Duration From</td>
                                                    <td style="text-align:center;" >Duration To</td>
                                                    <td style="text-align:center;" >Distribution Plans Submitted</td>
                                                    <td style="text-align:center;" >Status</td>
                                                </tr>
                                            </thead>
                                            
                                            
                                            <tbody>
                                                <?php
                                                
                                                    $qry = " SELECT
                                                                clr_distribution_plans.`year`,
                                                                clr_distribution_plans.`month`,
                                                                count( DISTINCT clr_distribution_plans.pk_id) as total_plans
                                                            FROM
                                                                clr_distribution_plans
                                                            INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                                                            WHERE
                                                                clr_distribution_plans_stk.stk_id IN (".implode(',',$integ_stk_arr).") AND
                                                                clr_distribution_plans.`year` = $selYear AND
                                                                    
                                                                clr_distribution_plans.plan_status = 'Submitted'
                                                            GROUP BY
                                                                clr_distribution_plans.`year`,
                                                                clr_distribution_plans.`month`
                                                            ";
                                                //echo $qry;exit;
                                                $res = mysql_query($qry);
                                                
                                                
                                                $dist_data=$total_data=array();
                                                //echo '<pre>';
                                                $c=1;
                                                
                                                $plans_arr=array();
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    $plans_arr[(int)$row['month']]=$row['total_plans'];
                                                }
                                                //echo '<pre>';print_r($plans_arr);print_r($mon_year_arr);exit;
                                                foreach($mon_year_arr as $k => $month)
                                                {
                                                    $to_mon  = date('m',strtotime($to_arr[$k]));
                                                    $to_year = date('Y',strtotime($to_arr[$k]));
                                                    
                                                    //print_r($row);
                                                    echo '<tr>';
                                                    echo '<td class="center">'.$c++.'</td>';
                                                    echo '<td class="center">'.$month.'</td>';
                                                    echo '<td class="center">'.$to_arr[$k].'</td>';
                                                    echo '<td class="center">'.((isset($plans_arr[$to_arr2[$k]])?$plans_arr[$to_arr2[$k]]:'')).'</td>';
                                                    echo '<td class="center"><a href="distribution_plans_integrated_view.php?year='.$to_year.'&month='.$to_mon.'">View</a></td>';
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