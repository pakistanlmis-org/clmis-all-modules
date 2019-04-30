<?php

//echo '<pre>';print_r($_REQUEST);exit;    
//include AllClasses
include("../includes/classes/AllClasses.php");

$month=$year='';
if(!empty($_REQUEST['month']))    $month = sprintf("%02d", $_REQUEST['month']);
if(!empty($_REQUEST['year']))       $year = $_REQUEST['year'];

if(!empty($_REQUEST['plan_id']))    $pk_id = $_REQUEST['plan_id'];
{        

    //first check if NO issuance occured against the distribution plan
    $qry_one = " SELECT DISTINCT
                        clr_distribution_plans.pk_id,
                        clr_distribution_plans.plan_number,
                        (
                                SELECT
                                        count(*)
                                FROM
                                        clr_master
                                WHERE
                                        clr_master.distribution_plan_id = clr_distribution_plans.pk_id
                                AND clr_master.approval_status IN (
                                        'Issued',
                                        'Issue in Process',
                                        'Hard_Copy_Issued'
                                )
                        ) AS issued
                FROM
                        clr_distribution_plans
                INNER JOIN clr_master ON clr_distribution_plans.pk_id = clr_master.distribution_plan_id
                INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                WHERE
                clr_distribution_plans.pk_id = '".$pk_id."' ";
    $rs_one = mysql_query($qry_one);
    $row_one = mysql_fetch_array($rs_one);
    if(empty($row_one['issued']) || $row_one['issued']<=0)
    {
    
        $qry = "UPDATE clr_master SET approval_status='Prov_Approved' , distribution_plan_id=NULL    WHERE distribution_plan_id ='".$pk_id."' ";
        mysql_query($qry);

        $qry2 = "UPDATE clr_distribution_plans SET plan_status = 'Deleted'  WHERE  pk_id ='".$pk_id."' ";
        mysql_query($qry2);

    }
}
$url = $_REQUEST['redirect'];
redirect($url.'.php?month='.$month.'&year='.$year.'&msg=saved');  