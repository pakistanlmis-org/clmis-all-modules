<?php
//echo '<pre>';
//print_r($_REQUEST);
//exit;
/**
 * loadLast3Months
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
$flag1 = FALSE;

$user_stakeholder_type = (!empty($_SESSION['user_stakeholder_type'])?$_SESSION['user_stakeholder_type']:'0');
//check user_id
if (isset($_SESSION['user_id'])) {
        //get user_id
	$userid = $_SESSION['user_id'];
        //set user_id
	$objwharehouse_user->m_npkId = $userid;
        //get user province
	$province = $_SESSION['user_province'];
        //get user stakeholder
	$mainStk = $_SESSION['user_stakeholder'];
}

if (isset($_REQUEST['wharehouse_id'])) {
    //get wharehouse_id
	$wh_Id = $_REQUEST['wharehouse_id'];
        //select query
	 $qry = "SELECT
				tbl_warehouse.stkid,
				tbl_warehouse.prov_id,
				tbl_warehouse.is_lock_data_entry,
				CONCAT(DATE_FORMAT(MAX(tbl_wh_data.last_update),'%d/%m/%Y'),' ',TIME_FORMAT(MAX(tbl_wh_data.last_update),'%r')) AS last_update
			FROM
				tbl_warehouse
			LEFT JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			WHERE
				tbl_warehouse.wh_id = $wh_Id
			ORDER BY
				tbl_wh_data.w_id DESC
			LIMIT 1";
	//query result
        $qryRes = mysql_fetch_array(mysql_query($qry));
	$mainStk = $qryRes['stkid'];
	$lastUpdate = (!empty($qryRes['last_update'])) ? $qryRes['last_update'] : 'Not yet reported';
	
	if ($qryRes['prov_id'] == 2 && $mainStk == 1 && !isset($_SESSION['enable_entry']))
	{
		if($qryRes['is_lock_data_entry'] == 1)
		{
			echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
			echo '<span class="help-block" style="color:#E04545">Data entry date for this facility has passed. Please contact administrator to enter data.</span>';
			exit;
		}
	}
} else {
    //error message
	die('No warehouses selected.');
}
//check dataEntryURL
if (isset($_REQUEST['dataEntryURL'])) {
    //get dataEntryURL
	$dataEntryURL = $_REQUEST['dataEntryURL'];
}
/**
 * checkDraft
 * 
 * @param type $draftMonth
 * @param type $draftYear
 * @param type $wh_Id
 * @return string
 */
function checkDraft($draftMonth, $draftYear, $wh_Id)
{
    // See if this month data exists in drafts
    $qry = "SELECT
				COUNT(tbl_wh_data_draft.w_id) AS num
			FROM
				tbl_wh_data_draft
			WHERE
				tbl_wh_data_draft.report_month = $draftMonth
			AND tbl_wh_data_draft.report_year = $draftYear
			AND tbl_wh_data_draft.wh_id = $wh_Id";
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    if ($qryRes['num']>0)
    {
        $draft = ' (Draft)';
    }
    else
    {
        $draft = '';
    }
    return $draft;
}

// Last Update
$lastUpQry = "SELECT
				CONCAT(DATE_FORMAT(tbl_wh_data.last_update,'%d/%m/%Y'),' ',TIME_FORMAT(tbl_wh_data.last_update,'%r')) AS last_update
			FROM
				tbl_wh_data
			WHERE
				tbl_wh_data.wh_id = $wh_Id
			ORDER BY
				tbl_wh_data.w_id DESC
			LIMIT 1";
//query result
$lastUpQryRes = mysql_fetch_array(mysql_query($lastUpQry));
$lastUpdate = (!empty($lastUpQryRes['last_update'])) ? $lastUpQryRes['last_update'] : 'Not yet reported';

$objReports->wh_id = $wh_Id;
$objReports->province_id = $province;
$objReports->stk = $mainStk;
$LastReportDate = $objReports->GetLastReportDate();

if ($LastReportDate != "")
{
	$LRD_dt = new DateTime($LastReportDate);
        //Get Pending Report Month
	$NewReportDate = $objReports->GetPendingReportMonth();
	
	if ($NewReportDate != "") 
	{
		$NRD_dt = new DateTime($NewReportDate);
		echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
		$do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
		
		// Show last three months for which date is entered
		$allMonths = '';
                //Get Last 3 Months
		$last3Months = $objReports->GetLast3Months();
		for ($i = 0; $i < sizeof($last3Months); $i++)
		{
			$L3M_dt = new DateTime($last3Months[$i]);
			$draftYear = $L3M_dt->format('Y');
			$draftMonth = $L3M_dt->format('m');
			//check draft
			$draft = checkDraft($draftMonth, $draftYear, $wh_Id);
			$do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');
			
                        $date_temp = $L3M_dt->format('Y-m-') . '01';
                        if( (!isset($_REQUEST['request_from']) || $_REQUEST['request_from']!='admin') && !empty($_SESSION['is_allowed_im'])  && $_SESSION['is_allowed_im']=='1' && $date_temp >= $_SESSION['im_start_month'])
                        {
                            //dont show months after enabling of IM module
                            $allMonths[] = '<span class="help-block">IM is enabled at this warehouse , from '.date('M-Y',strtotime($_SESSION['im_start_month'])).'. Therefore data entry screen will be locked from '.date('M-Y',strtotime($_SESSION['im_start_month'])).' and onwards. </span>';
                        }
                        else{
                            if($user_stakeholder_type=='1'){
                                $url = "data_entry_district_store_ngo.php?Do=" . $do3Months;
                            }
                            else{
                                $url = "data_entry.php?Do=" . $do3Months;
                            }
                            $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs red\">" . $L3M_dt->format('M-Y') . "$draft" . " <i class=\"fa fa-edit\"></i></a>";
                        }
                        
                }
		//draft year
		$draftYear = $NRD_dt->format('Y');
		//draft month
                $draftMonth = $NRD_dt->format('m');
		//check draft
                $draft = checkDraft($draftMonth, $draftYear, $wh_Id);
		//url
                if($user_stakeholder_type=='1'){
                    $url = "data_entry_district_store_ngo.php?Do=" . $do;
                }
                else{
                    $url = "data_entry.php?Do=" . $do;
                }
                
                $date_temp2 = $NRD_dt->format('Y-m-') . '01';
                if( (!isset($_REQUEST['request_from']) || $_REQUEST['request_from']!='admin') && !empty($_SESSION['is_allowed_im'])  && $_SESSION['is_allowed_im']=='1' && $date_temp2 >= $_SESSION['im_start_month'])
                {
                   echo '<span class="help-block">IM Module is Enabled at this warehouse , from '.date('M-Y',strtotime($_SESSION['im_start_month'])).'. Therefore data entry screen will be locked from '.date('M-Y',strtotime($_SESSION['im_start_month'])).' and onwards. </span>';

                }
                else
                { 
                    echo " <a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs green\"> Add " . $NRD_dt->format('M-y') . " Report$draft <i class=\"fa fa-plus\"></i></a> ";

                }
                echo (!empty($allMonths)) ? implode(' ', $allMonths) : '';
		//flag
                $flag1 = TRUE;
	}
	else
	{
		echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
		$allMonths = '';
		$last3Months = $objReports->GetLast3Months();
		for ($i = 0; $i < sizeof($last3Months); $i++)
		{
			//L3M_dt
                    $L3M_dt = new DateTime($last3Months[$i]);
			//draftYear
                        $draftYear = $L3M_dt->format('Y');
			//draftMonth
                        $draftMonth = $L3M_dt->format('m');
			//draft
                        $draft = checkDraft($draftMonth, $draftYear, $wh_Id);
			//do3Months
                        $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');
			//url
                        
                        $date_temp = $L3M_dt->format('Y-m-') . '01';
                        if((!isset($_REQUEST['request_from']) || $_REQUEST['request_from']!='admin') && !empty($_SESSION['is_allowed_im'])  && $_SESSION['is_allowed_im']=='1' && $date_temp >= $_SESSION['im_start_month'])
                        {
                            //dont show months after enabling of IM module
                            $allMonths[] = '<span class="help-block">IM is Enabled at this warehouse , from '.date('M-Y',strtotime($_SESSION['im_start_month'])).'. Therefore data entry screen will be locked from '.date('M-Y',strtotime($_SESSION['im_start_month'])).' and onwards. </span>';
                       
                        }
                        else{
                            if($user_stakeholder_type=='1'){
                                $url = "data_entry_district_store_ngo.php?Do=" . $do3Months;
                            }
                            else{
                                $url = "data_entry.php?Do=" . $do3Months;
                            }

                            $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs red\">" . $L3M_dt->format('M-Y') . "$draft <i class=\"fa fa-edit\"></i></a>";
                            }
                        }
		echo (!empty($allMonths)) ? implode(' ', $allMonths) : '';
	}
}
if ($flag1 != TRUE)
{
    //Get This Month Report Date
	$NRD_dt = new DateTime($objReports->GetThisMonthReportDate());
	if (substr($LastReportDate, 0, 7) != $NRD_dt->format('Y-m'))
	{
		if (substr($LastReportDate, 0, 7) < $NRD_dt->format('Y-m'))
		{
			echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
                        //draftYear
			$draftYear = $NRD_dt->format('Y');
			//draftMonth
                        $draftMonth = $NRD_dt->format('m');
			//draft
                        $draft = checkDraft($draftMonth, $draftYear, $wh_Id);
			//do
                        $do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
			//url 
                        if($user_stakeholder_type=='1'){
                            $url = "data_entry_district_store_ngo.php?Do=" . $do;
                        }
                        else{
                            $url = "data_entry.php?Do=" . $do;
                        }
                        
                        $date_temp2 = $NRD_dt->format('Y-m-') . '01';
                        if(!empty($_SESSION['is_allowed_im'])  && $_SESSION['is_allowed_im']=='1' && $date_temp2 < $_SESSION['im_start_month'])
                        {
                            echo "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs green\"> Add " . $NRD_dt->format('M-y') . " Report$draft <i class=\"fa fa-plus\"></i></a>";
                        }
		}
	}
}
?>