<?php
//echo '<pre>';
//print_r($_REQUEST);
//exit;
/**
 * loadLast3MonthsHF
 * @package consumption
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");

$flag1 = FALSE;

if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    $province = $_SESSION['user_province'];
    $mainStk = $_SESSION['user_stakeholder'];
    $district_id = $_SESSION['user_district'];
}
//check  wharehouse_id
if (isset($_REQUEST['wharehouse_id'])) {
    //get wharehouse_id
    $wh_Id = $_REQUEST['wharehouse_id'];
//query gets
    //stkid
    //prov_id
    //is_lock_data_entry
    //last_update
    $qry = "SELECT
				tbl_warehouse.stkid,
				tbl_warehouse.prov_id,
				tbl_warehouse.is_lock_data_entry,
				CONCAT(DATE_FORMAT(MAX(tbl_hf_data.last_update),'%d/%m/%Y'),' ',TIME_FORMAT(MAX(tbl_hf_data.last_update),'%r')) AS last_update
			FROM
				tbl_warehouse
			LEFT JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
			WHERE
				tbl_warehouse.wh_id = $wh_Id
			ORDER BY
				tbl_hf_data.pk_id DESC
			LIMIT 1";
    //result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    $mainStk = $qryRes['stkid'];
    //lastUpdate
    $lastUpdate = (!empty($qryRes['last_update'])) ? $qryRes['last_update'] : 'Not yet reported';
    //check prov_id
    
    //following condition removed on 3 aug 2017 , for lock/unlock data entry
    //if ($qryRes['prov_id'] == 2 && $mainStk == 1) {
        //check is_lock_data_entry
        if( $_SESSION['user_role'] != '2' &&  $_SESSION['user_role'] !='26' )
        {
            if ($qryRes['is_lock_data_entry'] == 1) {
                echo '<span class="help-block">Last Update: ' . $lastUpdate . '</span>';
                echo '<span class="help-block" style="color:#E04545">Data entry date for this facility has passed. Please contact administrator to enter data.</span>';
                exit;
            }
        }
    //}
} else {
    die('No warehouses selected.');
}
//check dataEntryURL
if (isset($_REQUEST['dataEntryURL'])) {
    //get dataEntryURL
    $dataEntryURL = $_REQUEST['dataEntryURL'];
}

$objReports->wh_id = $wh_Id;
$objReports->stk = $mainStk;
$objReports->province_id = $province;
$objReports->district_id = $district_id;
//Get Last Report Date HF
$LastReportDate = $objReports->GetLastReportDateHF();


if ($LastReportDate != "") {
    $LRD_dt = new DateTime($LastReportDate);
    //Get Pending Report Month HF
    $NewReportDate = $objReports->GetPendingReportMonthHF();

    //Check for PWD KP
    if ($NewReportDate == "" && $qryRes['prov_id'] == 3 && $mainStk == 1) {
        $cur_day = date("d");
        if($cur_day >= 25){
            if($LRD_dt->format("m") != date("m") && $LRD_dt->format("y") != date("y")){
                $NewReportDate = date("Y-m-d");
            }            
        }
    }

    if ($NewReportDate != "") {
        $NRD_dt = new DateTime($NewReportDate);
        echo '<span class="help-block">Last Update: ' . $lastUpdate . '</span>';
        $do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
        ?>
        <td>
            <?php
            // Show last three months for which date is entered
            $allMonths = '';
            //Get Last 3 Months HF
            $last3Months = $objReports->GetLast3MonthsHF();
            for ($i = 0; $i < sizeof($last3Months); $i++) {
                $L3M_dt = new DateTime($last3Months[$i]);
                $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');
                //url
                $url = $dataEntryURL . "?Do=" . $do3Months;
                //all months
                //echo "Data Entry is Lock";
                $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs red\">" . $L3M_dt->format('M-Y') . " <i class=\"fa fa-edit\"></i></a>";
            }
            $url = $dataEntryURL . "?Do=" . $do;
            echo " <a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs green\"> Add " . $NRD_dt->format('M-y') . " Report <i class=\"fa fa-plus\"></i></a> ";
            echo (!empty($allMonths)) ? implode(' ', $allMonths) : '';
            $flag1 = TRUE;
            ?>

        </td>
        <?php
    } else {
        echo '<span class="help-block">Last Update: ' . $lastUpdate . '</span>';
        ?>
        <td>
            <?php
            // Show last three months for which date is entered
            $allMonths = '';
            //Get Last 3 Months HF
            $last3Months = $objReports->GetLast3MonthsHF();
            for ($i = 0; $i < sizeof($last3Months); $i++) {
                $L3M_dt = new DateTime($last3Months[$i]);
                $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');
                //url
                $url = $dataEntryURL . "?Do=" . $do3Months;
                //all month
              
                    $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs red\">" . $L3M_dt->format('M-Y') . " <i class=\"fa fa-edit\"></i></a>";
                
            }
            echo (!empty($allMonths)) ? implode(' ', $allMonths) : '';
            ?>

        </td>
        <?php
    }
}
if ($flag1 != TRUE) {
    //Get This Month Report Date
    $NRD_dt = new DateTime($objReports->GetThisMonthReportDate());
    if (substr($LastReportDate, 0, 7) != $NRD_dt->format('Y-m')) {
        if (substr($LastReportDate, 0, 7) < $NRD_dt->format('Y-m')) {
            echo '<span class="help-block">Last Update: ' . $lastUpdate . '</span>';
            $do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
            $url = $dataEntryURL . "?Do=" . $do;
            echo "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs green\"> Add " . $NRD_dt->format('M-y') . " Report <i class=\"fa fa-plus\"></i></a>";
        }
    }
}
?>