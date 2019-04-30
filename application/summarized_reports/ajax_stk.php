<?php

/**
 * reporting_rate
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//login
Login();
//including db file
include(APP_PATH . "includes/classes/db.php");

if(isset($_POST['type']))
{
	//stk filter
	$stkFilter = '';
	//get stk type
	$stkType = $_POST['type'];
	
	
        $stkFilter = ' stakeholder.stk_type_id IN (0, 1)';
	
	//select query
	//gtes
	//stk id
	//stk name
	$querystk = "SELECT DISTINCT
						stakeholder.stkid,
						stakeholder.stkname,
						stakeholder.stk_type_id
                                                
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
					INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
					WHERE
						$stkFilter
					AND tbl_warehouse.is_active = 1
					ORDER BY
						stakeholder.stk_type_id ASC,
						stakeholder.stkorder ASC";
	echo '<option value="all">All</option>';
	$rsstk = mysql_query($querystk) or die();
	while ($rowstk = mysql_fetch_array($rsstk)) {
		if ($_POST['stk'] == $rowstk['stkid']) {
			$sel = "selected='selected'";
		} else {
			$sel = "";
		}
                $disp_1='';
                if ($stkType == 'private' || $stkType == '1') {
                    if($rowstk['stk_type_id'] == 0)
                        $disp_1 = ' display:none; ';
                } else if ($stkType == 'public' || $stkType == '0') {
                    if($rowstk['stk_type_id'] == 1)
                        $disp_1 = ' display:none; ';
                }
		?>
<option value="<?php echo $rowstk['stkid']; ?>" stk-type="<?=$rowstk['stk_type_id']?>" <?php echo $sel; ?> style="<?=$disp_1?>"><?php echo $rowstk['stkname']; ?></option>
		<?php
	}
}

if(isset($_REQUEST['showProvinces']) && $_REQUEST['showProvinces'] == 1)
{
	$sel_stk = $_REQUEST['stakeholder'];
	$sel_prov = $_REQUEST['provinceId'];
	$hfProvOnly = isset($_REQUEST['hfProvOnly']) ? $_REQUEST['hfProvOnly'] : '';
	$showAllOpt = isset($_REQUEST['showAllOpt']) ? $_REQUEST['showAllOpt'] : 1;
	
	$filter = (!empty($hfProvOnly)) ? " AND stakeholder.lvl = 7" : "";
	$filter .= ($sel_stk != 'all' && $sel_stk != '') ? " AND tbl_warehouse.stkid = $sel_stk" : '';
	if($showAllOpt != 0)
	{
	?>
    <option value="all">All</option>
	<?php
	}
    //select query
    //gets
    //province id
    //province title
	$queryprov = "SELECT DISTINCT
					tbl_locations.PkLocID,
					tbl_locations.LocName
				FROM
					tbl_locations
				INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_locations.ParentID IS NOT NULL
				AND tbl_locations.LocLvl = 2
				AND tbl_warehouse.is_active = 1
				$filter
				ORDER BY
					tbl_locations.PkLocID";
    //query result
    $rsprov = mysql_query($queryprov) or die();
    while ($rowprov = mysql_fetch_array($rsprov)) {
        
        if ($sel_prov == $rowprov['PkLocID']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowprov['LocName']; ?></option>
        <?php
    }
}