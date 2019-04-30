<?php
/**
 * mnch_data_entry
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Getting user_stakeholder1
$stakeholder = $_SESSION['user_stakeholder1'];
//Getting user_province1
$province_id = $_SESSION['user_province1'];
//Getting user_district
$district_id = $_SESSION['user_district'];
//Setting stakeholder
$objwharehouse_user->m_stk_id = $stakeholder;
//Setting province_id
$objwharehouse_user->m_prov_id = $province_id;
//Getting rpt_date
$reportingMonth = $_GET['rpt_date'].'-01';
//***************************
//Check if CMWs
//***************************
//query
//gets
//wh_id
//wh_name
$qry = "SELECT
			tbl_warehouse.wh_id,
			CONCAT(tbl_warehouse.dhis_code, ' - ', tbl_warehouse.wh_name) AS wh_name
		FROM
			tbl_warehouse
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		WHERE
			tbl_warehouse.dist_id = $district_id
		AND tbl_warehouse.stkid = 73
		AND stakeholder.lvl = 7
		AND tbl_warehouse.wh_id NOT IN (
			SELECT
				warehouse_status_history.warehouse_id
			FROM
				warehouse_status_history
			INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
			WHERE
				warehouse_status_history.reporting_month = '$reportingMonth'
			AND tbl_warehouse.dist_id = $district_id
			AND tbl_warehouse.stkid = 73
			AND warehouse_status_history.`status` = 0
		)";
//Query result
$hfResult = mysql_query($qry);
//Total Facilities
$totalFacilities = mysql_num_rows($hfResult);
//Setting userid
$objwharehouse_user->m_npkId = $userid;
//Get whuser By Idc
$result = $objwharehouse_user->GetwhuserByIdc();
//num
$num = mysql_num_rows($result);
//Checking if im_opem
if($result != FALSE && $num>0 && $_SESSION['is_allowed_im'] == 0)
{
    //load Last 3 Months
    $load3Months = 'loadLast3Months.php';
?>
    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">District/Field Stores</div>
            <div class="tools">
                <a class="collapse" href="javascript:;"></a>
            </div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="8%">Sr. No.</th>
                        <th>Store Name</th>
                        <?php 
                        //check totalFacilities
                        if($totalFacilities == 0){?>
                        <th width="8%">Sr. No.</th>
                        <th width="42%">Store Name</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                <?php
                $counter = 1;
                //dataEntryUrl
				$dataEntryUrl = '';
                                //fetch data from result
                while($row = mysql_fetch_array($result))
                {
                    //wh_id
                    $wh_Id = $row['wh_id'];
                    //data entry url
                    $dataEntryUrl = 'data_entry.php';
                    //check level and totalFacilities
                    if($row['lvl'] <= 3 || ($row['lvl'] == 4 && $totalFacilities == 0))
                    {
                        if ($counter % 2 != 0)
                        {
                            if ( $counter > 1 )
                            {
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td class=\"center\">".$counter++."</td>";
                            echo "<td><span class='wh_name' onClick=\"showReports('$wh_Id', '$load3Months', '$dataEntryUrl')\">" . $row['wh_name'] . "</span>";
                            echo "<div class=\"whDiv\" id=\"$wh_Id\" style=\"display:none;\"></div>";
                            echo "</td>";
                        }
                        else if ($counter % 2 == 0)
                        {
                            echo "<td class=\"center\">".$counter++."</td>";
                            echo "<td><span class='wh_name' onClick=\"showReports('$wh_Id', '$load3Months', '$dataEntryURL')\">" . $row['wh_name'] . "</span>";
                            echo "<div class=\"whDiv\" id=\"$wh_Id\" style=\"display:none;\"></div>";
                            echo "</td>";
                        }
                    
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
//Checking  hfResult and totalFacilities
if($hfResult!=FALSE && $totalFacilities > 0)
{
?>
    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">CMW List</div>
            <div class="tools">
                <a class="collapse" href="javascript:;"></a>
            </div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-hover">
            	<thead>
                    <tr>
                        <th width="8%">Sr. No.</th>
                        <th width="42%">CMW Name</th>
                        <th width="8%">Sr. No.</th>
                        <th width="42%">CMW Name</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $counter = 1;
                //fetch data from hfResult
                while($row = mysql_fetch_array($hfResult))
                {
                    //wh id
                    $wh_Id = $row['wh_id'];
                    //*****************************
                    // Check if data exists
                    //*****************************
                    //query 
                    //gets count
                    $qry = "SELECT
                                COUNT(tbl_hf_data.pk_id) AS num
                            FROM
                                tbl_hf_data
                            WHERE
                                tbl_hf_data.reporting_date = '$reportingMonth'
                            AND tbl_hf_data.warehouse_id = $wh_Id";
                    //query result
                    $qryRes = mysql_fetch_array(mysql_query($qry));
                    if($qryRes['num'] > 0){
                        $url = "data_entry_hf.php?Do=" . urlencode("Z" . ($wh_Id + 77000) . '|' . $rpt_date . '-01|0');
                    }else{
                        $url = "data_entry_hf.php?Do=" . urlencode("Z" . ($wh_Id + 77000) . '|' . $rpt_date . '-01|1');
                    }
                ?>
                    <?php
                    if ($counter % 2 != 0)
                    {
                        if ( $counter > 1 )
                        {
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td class=\"center\">".$counter++."</td>";
                        echo "<td><span class='wh_name'>" . " <a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\"> ". $row['wh_name'] ."</a> </span>";
                        echo "<div class=\"whDiv\" id=\"$wh_Id\" style=\"display:none;\"></div>";
                        echo "</td>";
                    }
                    else if ($counter % 2 == 0)
                    {
                        echo "<td class=\"center\">".$counter++."</td>";
                        echo "<td><span class='wh_name'>" . " <a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\"> ". $row['wh_name'] ."</a> </span>";
                        echo "<div class=\"whDiv\" id=\"$wh_Id\" style=\"display:none;\"></div>";
                        echo "</td>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}