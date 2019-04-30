<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id

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
if($_SESSION['integrated_stakeholder'] == 'yes')
{
    //redirect('distribution_plan_approvals.php');  
    redirect('distribution_plan2.php');  
}

if(!empty($_REQUEST['year_sel']))
    $selYear = $_REQUEST['year_sel'];
else
    $selYear = date('Y'); 

$district='';
if(!empty($_REQUEST['district']))
    $district = $_REQUEST['district'];

//creating temp array for months of year
$mon_year_arr=array();
for($i=1;$i<=12;$i++)
{
    $mon_year_arr[$i]=$i.'-'.$selYear;
}
//echo '<pre>';print_r($_SESSION);exit;
$q1 = " SELECT
	sysuser_tab.usrlogin_id,
	sysuser_tab.sysusr_name,
	
	getUserStakeholders(sysuser_tab.UserID) AS stakeholders,
	
	sysuser_tab.UserID
	FROM
	sysuser_tab
	WHERE
	sysuser_tab.UserID = '".$_SESSION['user_id']."' ";
$res1 = mysql_query($q1);
$row = mysql_fetch_array($res1);
$user_stakeholder_name = $row['stakeholders'];

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
//$requisition_stk = $objuserstk->GetStkByUserId();
$requisition_stk= array();
$requisition_stk[$_SESSION['user_stakeholder1']] = $_SESSION['user_stakeholder1'];
//echo '<pre>';print_r($requisition_stk);exit;


//checking requisition flow
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
                requisition_module_flow.prov_id = ".$_SESSION['user_province1']." AND
                requisition_module_flow.stk_id = ".$_SESSION['user_stakeholder1']." ";
$qryRes2 = mysql_query($qry2);
$num2 = mysql_num_rows($qryRes2);

if($num2>0)
{
    $approve_prov_reqs_is_active = TRUE;
}
else
{
    $approve_prov_reqs_is_active = FALSE;
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
                                                            for ($j = date('Y')+1; $j >= 2010; $j--) {
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
                                <h3 class="heading">All Requisitions - <?php echo (!empty($user_province_name)?$user_province_name.' - ':'').(!empty($user_stakeholder_name)?$user_stakeholder_name.'':'')?></h3>
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
                                                    <td style="text-align:center;" >Total Requisitions</td>
                                                    <td style="text-align:center;" >Action</td>
                                                </tr>
                                            </thead>
                                            
                                            
                                            <tbody>
                                                <?php
                                                
                                                    $qry = "SELECT
                                                            clr_master.date_to,
                                                            clr_master.date_from,
                                                            YEAR(clr_master.date_to) as year,
                                                            MONTH(clr_master.date_to) as month,
                                                            count(DISTINCT tbl_warehouse.dist_id) as dist,
                                                            count(*) as total,

                                                            sum(CASE WHEN clr_master.approval_status= 'Pending' THEN 1 else 0 END) as pending,
                                                            sum(CASE WHEN clr_master.approval_status= 'Prov_Saved' THEN 1 else 0 END) as prov_saved,
                                                            sum(CASE WHEN clr_master.approval_status= 'Prov_Approved' THEN 1 else 0 END) as prov_approved,
                                                            sum(CASE WHEN clr_master.approval_status= 'Dist_Approved' THEN 1 else 0 END) as dist_approved,
                                                            sum(CASE WHEN clr_master.approval_status= 'Approved' THEN 1 else 0 END) as approved,
                                                            sum(CASE WHEN clr_master.approval_status= 'Issued' THEN 1 else 0 END) as issued,
                                                            sum(CASE WHEN clr_master.approval_status= 'Issue in Process' THEN 1 else 0 END) as iip
                                                            FROM
                                                            clr_master

                                                            INNER JOIN tbl_warehouse ON clr_master.wh_id=tbl_warehouse.wh_id
                                                            WHERE
                                                                tbl_warehouse.prov_id= '".$_SESSION['user_province1']."'
                                                                AND clr_master.stk_id in  (".implode(',',$requisition_stk).")
                                                                AND YEAR (clr_master.date_to) = '".$selYear."'
                                                                AND (SELECT Count(clr_details.pk_id)  from clr_details WHERE clr_details.pk_master_id = clr_master.pk_id ) >0
                                                            GROUP BY 
                                                                YEAR (clr_master.date_to),
                                                                MONTH(clr_master.date_to)
                                                            ORDER BY 
                                                                clr_master.date_to DESC
                                                        ";
                                                //echo $qry;
                                                $res = mysql_query($qry);
                                                
                                                
                                                $dist_data=$total_data=array();
                                                //echo '<pre>';
                                                $c=1;
                                                
                                                $row1=array();
                                                while($row = mysql_fetch_assoc($res))
                                                {
                                                    
                                                    $status='No Requisitions';
                                                    
                                                    //if($approve_dist_reqs_is_active)
                                                    //    $req = $row['dist_approved']+$row['prov_saved']+$row['prov_approved'];
                                                    //else
                                                    //    $req = $row['pending']+$row['dist_approved']+$row['prov_saved']+$row['prov_approved'];
                                                    
                                                    $req = $row['total'];
                                                    if(!empty($req) && $req > 0)
                                                    {
                                                        $status='View';

                                                        $to_mon = (sprintf("%02d", $row['month'])).'-'.$row['year'];
                                                        $from_mon = date('M-Y', strtotime('01-'.$to_mon.'-2 months'));
                                                        $to_mon1 = date('M-Y',strtotime('01-'.$to_mon));

                                                        //print_r($row);
                                                        echo '<tr>';
                                                        echo '<td class="center">'.$c++.'</td>';
                                                        echo '<td class="center">'.$from_mon.'</td>';
                                                        echo '<td class="center">'.$to_mon1.'</td>';
                                                        echo '<td class="center">'.(!empty($req)?$req:'0').'</td>';
                                                        echo '<td class="center"><a href="clr_all_district_approval3.php?month='.$row['month'].'&year='.$row['year'].'">'.$status.'</a></td>';
                                                        echo '</tr>';
                                                    }
                                                    
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