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
           if(!empty($_REQUEST['multi_stk_id']))
           {
               
                $multi_stks = explode(',',$_REQUEST['multi_stk_id']);
                
                foreach( $multi_stks as $k=>$v)
                {
                      $qry = " INSERT INTO clr_distribution_plans_stk
                                  SET
                                  plan_id='".$new_plan_id."',
                                  stk_id = '".$v."'  ";
                      mysql_query($qry);
                }
           }
           
          
            foreach($_REQUEST['check'] as $pk_id => $val)
            {
             //updating each req with new plan id   
                if($val == 'on')
                {
                     $qry = "UPDATE clr_master SET approval_status='Approved' , distribution_plan_id='".$new_plan_id."' ".$wh_clause2."  WHERE pk_id ='".$pk_id."' ";
                     //echo $qry;exit;
                     mysql_query($qry);
                     
                     $qry2 = "UPDATE clr_details SET   approve_qty = qty_req_prov  , approval_status='Approved' WHERE  pk_master_id ='".$pk_id."' ";
                     mysql_query($qry2);
                }
            }
            
            //fetch this DP info..
            $qry_dp = "SELECT
                            GROUP_CONCAT(stakeholder.stkname) as stk_name,
                            clr_distribution_plans.pk_id,
                            clr_distribution_plans.plan_number,
                            clr_distribution_plans.prov_id,
                            clr_distribution_plans.plan_status,
                            clr_distribution_plans.created_on,
                            clr_distribution_plans.`month`,
                            clr_distribution_plans.`year`,
                            tbl_locations.LocName as prov_name
                        FROM
                            clr_distribution_plans
                        INNER JOIN clr_distribution_plans_stk ON clr_distribution_plans.pk_id = clr_distribution_plans_stk.plan_id
                        INNER JOIN stakeholder ON clr_distribution_plans_stk.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON clr_distribution_plans.prov_id = tbl_locations.PkLocID
                        WHERE
                            clr_distribution_plans.pk_id = '".$new_plan_id."' ";
            $res_rs = mysql_query($qry_dp);
            $dp_arr = mysql_fetch_assoc($res_rs);
            $to_mon     = date('M-Y', strtotime(($dp_arr['year'].'-'.$dp_arr['month'].'-01')));
            $from_mon   = date('M-Y', strtotime('01-'.$to_mon.'-2 months'));
            
            //sending email
            if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk')
            {
                //this email only for testing...
                $to      = 'jakram@ghsc-psm.org';
            }
            else{
                //live system
                 $to      = 'dir.cws@gmail.com';

            }
            $cc      = 'muhahmed@ghsc-psm.org';
            $subject = 'Distribution Plan Submitted';
            $msg     = 'Dear WMSUSER,<br/>';
            $msg     .= '<br/>New Provincial Distribution Plan has been created by '.$dp_arr['prov_name'] .' in cLMIS';
            $msg     .= '<br/><br/>Province :'.$dp_arr['prov_name'];
            $msg     .= '<br/>Stakeholder :'.$dp_arr['stk_name'];
            $msg     .= '<br/>From :'.$from_mon;
            $msg     .= '<br/>To :'.$to_mon;
            $msg     .= '<br/><br/><br/><br/>';
            $msg     .= '<i>PS: This is an auto generated email('.$_SERVER['SERVER_NAME'].').</i>';
            
            $page_name = 'distribution_plan_created';
            $sent_by_user = null;
            $sent_to_user = null;
            $wh_id        = null;
            include_once "../dashboard/include_functions_email.php";
            func_shoot_email($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
            //end sending email
            
            
            
        }
        redirect('clr_all_district_approval.php?month='.$month.'&year='.$year.'&msg=saved');  
}