<?php
/**
 * satelite_wh
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
//Including header file
include(PUBLIC_PATH."html/header.php");
//Checking user_id
if (isset($_SESSION['user_id']))
{
    //Getting user_id
    $userid=$_SESSION['user_id'];
    //Setting user id
    $objwharehouse_user->m_npkId=$userid;
}
else
{
    //Display message
    echo "user not login or timeout";
	exit;
}
?>
<style>
.wh_name {
	cursor: pointer;
	color: #428bca !important;
}
.btn-sm,  .btn-xs {
	margin-bottom: 4px;
}
</style>
<script>
    function openPopUp(pageURL)
    {
		var w = screen.width - 100;
		var h = screen.height - 100;
		var left = (screen.width/2)-(w/2);
		var top = 0;
		return window.open(pageURL, 'Data Entry', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    }
</script>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
<!-- BEGIN HEADER -->
<div class="page-container">
    <?php 
    //Including top file
    include PUBLIC_PATH."html/top.php";
    //Including top_im
    include PUBLIC_PATH."html/top_im.php";?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box green ">
                        <div class="portlet-title">
                            <div class="caption">Satellite Camps Data Entry</div>
                            <div class="tools"> <a class="collapse" href="javascript:;"></a> </div>
                        </div>
                        <div class="portlet-body">
                            <?php
                                                        //Gets
                                                        //stkid
                                                        //prov_id
							$qry = "SELECT
										tbl_warehouse.stkid,
										tbl_warehouse.prov_id
									FROM
										tbl_warehouse
									INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
									WHERE
										tbl_warehouse.wh_id =".$_SESSION['user_warehouse'];
                                                        //Query result
							$check = mysql_fetch_array(mysql_query($qry));
							$stakeholder = $check['stkid'];
							$province_id = $check['prov_id'];
							
							// Health Facilities
							$_SESSION['LIMIT'] = 2;
							$counter = 1;
							$objwharehouse_user->m_stk_id=$stakeholder;
							$objwharehouse_user->m_prov_id=$province_id;
							//Get wh user HF Satellite By Idc
							$result1=$objwharehouse_user->GetwhuserHFSatelliteByIdc();
							if($result1!=FALSE && mysql_num_rows($result1)>0)
							{
							?>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="8%">Sr. No.</th>
                                        <th width="42%">Satellite Camp</th>
                                        <th width="8%">Sr. No.</th>
                                        <th width="42%">Satellite Camp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;
                                    while($row = mysql_fetch_array($result1))
                                    {
                                        $wh_Id = $row['wh_id'];
                                        $dataEntryURL = 'data_entry_hf_satellite.php';
									   	$load3Months = 'loadLast3MonthsHFSatellite.php';
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
                                            echo "<td><span class='wh_name' onClick=\"showReports('$wh_Id', '$load3Months', '$dataEntryURL')\">" . $row['wh_name'] . "</span>";
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
                                    ?>
                                </tbody>
                            </table>
                            <?php
							}
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
//Including footer file
include PUBLIC_PATH."/html/footer.php";?>
<script>
function showReports(wharehouse_id, load3Months, dataEntryURL)
{
	if($('div#'+wharehouse_id).is(':visible'))
	{
		$('div#'+wharehouse_id).hide();
		return false;
	}
	else
	{
		if(wharehouse_id)
		{
			$('.whDiv').hide();
			$.ajax({
				url: load3Months,
				data: {wharehouse_id: wharehouse_id, dataEntryURL: dataEntryURL},
				type: 'post',
				success: function(data){
					$('div#'+wharehouse_id).show().html(data);
				}
			})
		}
	}
}
</script> 
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>