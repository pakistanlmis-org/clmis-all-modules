<?php	
/**
 * loadLast3MonthsHFType
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("includes/AllClasses.php");
$flag1=FALSE;
//Setting wh_id
$objReports->wh_id=$wh_type_id;
//Get Last Report Date HF Type
$LastReportDate=$objReports->GetLastReportDateHFType();
//Check LastReportDate
if ($LastReportDate!="")
{
	$LRD_dt = new DateTime($LastReportDate);
        //Get Pending Report Month HF Type
	$NewReportDate=$objReports->GetPendingReportMonthHFType();
        //Check New Report Date
	if ($NewReportDate!="")
	{
                //New Report Date
		$NRD_dt = new DateTime($NewReportDate);
		echo "<tr>";
		echo  "<td class=\"sb1NormalFont\">".$row['hf_type']."</td>";
		echo  "<td>".$row['last_update']."</td>";
                //encoding url
		$do=urlencode("Z".($wh_type_id+77000).'|'.$NRD_dt->format('Y-m-').'01|1');
		?>
			<td>
				<?php 
                                //***************************************************
				// Show last three months for which date is entered
				//***************************************************
                                $allMonths1 = '';
                                //Get Last 3 Months HF Type
				$last3Months = $objReports->GetLast3MonthsHFType();
				for ( $i=0; $i<sizeof($last3Months); $i++ )
				{   
                                        //last 3 Months
					$L3M_dt = new DateTime($last3Months[$i]);
                                        //encoding url
					$do3Months=urlencode("Z".($wh_type_id+77000).'|'.$L3M_dt->format('Y-m-').'01|0');
					$url = "data_entry_hf_type.php?Do=".$do3Months;
					$allMonths1 .= "<a href=\"#\" onclick=\"openPopUp('$url')\">".$L3M_dt->format('M-Y')."</a>".", ";
				}
				$url = "data_entry_hf_type.php?Do=".$do;
				echo " <a href=\"#\" onclick=\"openPopUp('$url')\" style=\"color: blue\"> Add ".$NRD_dt->format('M-y')." Report</a> ";
				echo substr($allMonths1, 0, -2);
				$flag1=TRUE;
				?>
				
			</td>
		<?php
		echo "</tr>";
	}
	else
	{
		echo "<tr>";
		echo  "<td class=\"sb1NormalFont\">".$row['hf_type']."</td>";
		echo  "<td>".$row['last_update']."</td>";
		?>
		<td>
			<?php 
                        //**************************************************
			// Show last three months for which date is entered
			//**************************************************
                        $allMonths = '';
                        //Get Last 3 Months HF Type
			$last3Months = $objReports->GetLast3MonthsHFType();
			for ( $i=0; $i<sizeof($last3Months); $i++ )
			{
				$L3M_dt = new DateTime($last3Months[$i]);
                                //Encoding url
				$do3Months=urlencode("Z".($wh_type_id+77000).'|'.$L3M_dt->format('Y-m-').'01|0');
				
				$url = "data_entry_hf_type.php?Do=".$do3Months;
				$allMonths .=  "<a href=\"#\" onclick=\"openPopUp('$url')\">".$L3M_dt->format('M-Y')."</a>".", ";
			}
			echo substr($allMonths, 0, -2);
			?>
			
		</td>
	<?php
		
	}
}
if ($flag1!=TRUE)
{
        //Get This Month Report Date
	$NRD_dt = new DateTime($objReports->GetThisMonthReportDate());
	if (substr($LastReportDate,0,7)!=$NRD_dt->format('Y-m'))
	{
		if (substr($LastReportDate,0,7) < $NRD_dt->format('Y-m'))
		{
			echo  "<td class=\"sb1NormalFont\">".$row['hf_type']."</td>";
			echo  "<td>".$row['last_update']."</td>";
			$do=urlencode("Z".($wh_type_id+77000).'|'.$NRD_dt->format('Y-m-').'01|1');
			$url = "data_entry_hf_type.php?Do=".$do;
			echo  "<td><a href=\"#\" onclick=\"openPopUp('$url')\" style=\"color: blue\"> Add ".$NRD_dt->format('M-y')." Report </a></td>";
		}
	}
	echo "</tr>";
}
?>