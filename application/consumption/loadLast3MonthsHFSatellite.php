<?php
/**
 * loadLast3MonthsHFSatelite
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
$flag1 = FALSE;
//Checking user_id
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    //getting user_province
    $province = $_SESSION['user_province'];
    //Getting user_stakeholder
    $mainStk = $_SESSION['user_stakeholder'];
}
//Checking wharehouse_id
if (isset($_REQUEST['wharehouse_id'])) {
    //Getting wharehouse_id
    $wh_Id = $_REQUEST['wharehouse_id'];
} else {
    $wh_Id = $wh_Id;
}
//Checking dataEntryURL
if (isset($_REQUEST['dataEntryURL'])) {
    //Getting dataEntryURL
	$dataEntryURL = $_REQUEST['dataEntryURL'];
}

$objReports->wh_id = $wh_Id;
$objReports->province_id = $province;
//Get Last Report Date HF Satellite
$LastReportDate = $objReports->GetLastReportDateHFSatellite();
//***************************
// Last Update
//***************************
$lastUpQry = "SELECT
				CONCAT(DATE_FORMAT(tbl_hf_satellite_data.last_update,'%d/%m/%Y'),' ',TIME_FORMAT(tbl_hf_satellite_data.last_update,'%r')) AS last_update
			FROM
				tbl_hf_satellite_data
			WHERE
				tbl_hf_satellite_data.warehouse_id = $wh_Id
			LIMIT 1";
//Query result
$lastUpQryRes = mysql_fetch_array(mysql_query($lastUpQry));
$lastUpdate = (!empty($lastUpQryRes['last_update'])) ? $lastUpQryRes['last_update'] : 'Not yet reported';
//Checking LastReportDate
if ($LastReportDate != "") {    
    $LRD_dt = new DateTime($LastReportDate);
    //Get Pending Report Month HF Satellite
    $NewReportDate = $objReports->GetPendingReportMonthHFSatellite();
    //Checking new report date 
    if ($NewReportDate != "") {
		
        $NRD_dt = new DateTime($NewReportDate);
		
       	echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
        $do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
        ?>
        <td>
            <?php
            // Show last three months for which date is entered
            $allMonths = '';
            //Get Last 3 Months HF Satellite
            $last3Months = $objReports->GetLast3MonthsHFSatellite();
            for ($i = 0; $i < sizeof($last3Months); $i++) {
                $L3M_dt = new DateTime($last3Months[$i]);
                $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');
                $url = $dataEntryURL . "?Do=" . $do3Months;
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
        echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
        ?>
        <td>
            <?php
            //***************************************************
            // Show last three months for which data is entered
            //***************************************************
            $allMonths = '';
            //Get Last 3 Months HF Satellite
            $last3Months = $objReports->GetLast3MonthsHFSatellite();
            for ($i = 0; $i < sizeof($last3Months); $i++) {
                $L3M_dt = new DateTime($last3Months[$i]);
                $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $L3M_dt->format('Y-m-') . '01|0');

                $url = $dataEntryURL . "?Do=" . $do3Months;
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
        	echo '<span class="help-block">Last Update: '.$lastUpdate.'</span>';
            $do = urlencode("Z" . ($wh_Id + 77000) . '|' . $NRD_dt->format('Y-m-') . '01|1');
            $url = $dataEntryURL . "?Do=" . $do;
            echo "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs green\"> Add " . $NRD_dt->format('M-y') . " Report <i class=\"fa fa-plus\"></i></a>";
        }
    }
    echo "</tr>";
}
?>