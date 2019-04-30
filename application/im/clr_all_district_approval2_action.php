<?php

//echo '<pre>';print_r($_REQUEST);exit;    
//include AllClasses
include("../includes/classes/AllClasses.php");

$objuserstk->m_npkId = $_SESSION['user_id'];
$requisition_stk = $objuserstk->GetStkByUserId();
//echo '<pre>';print_r($requisition_stk);exit;

$stk_for_req = implode($requisition_stk,',');

$month=$year='';
if(!empty($_REQUEST['month']))    $month = sprintf("%02d", $_REQUEST['month']);
if(!empty($_REQUEST['year']))       $year = $_REQUEST['year'];



if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Approve')
{
   
        if(!empty($_REQUEST['check']))
        {
            //creating a dist plan in table
            $qry2 = "SELECT
                       count(distinct clr_distribution_plans.pk_id) as count_of_plans
                    FROM
                       clr_distribution_plans
                       INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                    WHERE
                       clr_distribution_plans.prov_id = '".$_SESSION['user_province1']."' AND
                           
                       clr_distribution_plans.`month` = '".$month."' AND
                       clr_distribution_plans.`year` = '".$_REQUEST['year']."'
           ";
            //echo $qry2;exit;
           $res = mysql_query($qry2);
           $row = mysql_fetch_assoc($res);
           $temp_num = $row['count_of_plans'];
           $temp_num++;
           $num2 = sprintf("%03d", $temp_num);

           //plan number = DP 02 01 06 17 01
           // DP proID StkID Mon Yr Integer
           $pro = sprintf("%02d", $_SESSION['user_province1']);
           //$stk = sprintf("%02d", $_SESSION['user_stakeholder1']);
           $mon = sprintf("%02d", $_REQUEST['month']);
           
           if(!empty($_REQUEST['warehouse_sel']))
           {
                $wh_clause = " submitted_to ='".$_REQUEST['warehouse_sel']."' , ";           
                $wh_clause2 = " , requisition_to ='".$_REQUEST['warehouse_sel']."' ";           
           }
           else
           {
               $wh_clause = " ";
               $wh_clause2 = " ";
           }
          
           $plan_num = 'DP'.$pro.$mon.$_REQUEST['year'].$num2;

             $qry = " INSERT INTO clr_distribution_plans
                       SET
                       plan_number='".$plan_num."',
                       prov_id = '".$_SESSION['user_province1']."',
                       year = '".$_REQUEST['year']."',
                       month ='".$mon."',
                       ".$wh_clause."    
                       `plan_status` = 'Submitted' ";
             //echo $qry;exit;
           mysql_query($qry);
           $new_plan_id  = mysql_insert_id();
           //end of creating a dist plan in db
           $multi_stks=array();
           
            foreach($_REQUEST['check'] as $pk_id => $val)
            {
             //updating each req with new plan id   
                if($val == 'on')
                {
                    $qry = " SELECT stk_id From clr_master where pk_id ='".$pk_id."'  ";
                      //echo $qry;exit;
                    $res = mysql_query($qry);
                    $row = mysql_fetch_assoc($res);
                    $multi_stks[$row['stk_id']]=$row['stk_id'];
                    
                     $qry = "UPDATE clr_master SET approval_status='Approved' , distribution_plan_id='".$new_plan_id."' ".$wh_clause2."  WHERE pk_id ='".$pk_id."' ";
                     //echo $qry;exit;
                     mysql_query($qry);
                     
                     $qry2 = "UPDATE clr_details SET   approve_qty = qty_req_prov  , approval_status='Approved' WHERE  pk_master_id ='".$pk_id."' ";
                     mysql_query($qry2);
                }
            }
            
            if(!empty($multi_stks))
            {
                 foreach( $multi_stks as $k=>$v)
                 {
                       $qry = " INSERT INTO clr_distribution_plans_stk
                                   SET
                                   plan_id='".$new_plan_id."',
                                   stk_id = '".$v."'  ";
                       mysql_query($qry);
                 }
            }
            
            
        }
        redirect('clr_all_district_approval2.php?month='.$month.'&year='.$year.'&msg=saved');  
}