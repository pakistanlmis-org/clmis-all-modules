<?php
/**
 * Get From Ajax
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
//Getting id
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
//Getting id2
$id2 = isset($_REQUEST['id2']) ? $_REQUEST['id2'] : '';
//Getting ctype
$ctype = $_REQUEST['ctype'];
//Initializing variables
//$PkLocID
//wh_id
//stkid
//StkparentID 
//lvl_id 
$PkLocID = '';
$wh_id = '';
$stkid = '';
$StkparentID = '';
$lvl_id = '';

if (is_numeric($id) == true || is_numeric($id2) == true || !empty($_REQUEST['stkid']) || !empty($_REQUEST['dashid']) ) {
//Checking ctype
    if (isset($_REQUEST['ctype']) && !empty($_REQUEST['ctype'])) {
        //Checking ctype
        switch ($ctype) {
            //*****************************************
            // Get stakeholders' offices list  
            //*****************************************
            case 1:
                //Getting id
                $objstk->m_npkId = $_REQUEST['id'];
                //Get Stakeholders Family By Id
                $rsStakeholders = $objstk->GetStakeholdersFamilyById();
                //Checking rsStakeholders
                if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
                    print "<option value=''>Select</option>";
                    //Getting results
                    while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                        print "<option value='" . $RowGroups->stkid . "' " . ($RowGroups->stkid == $_SESSION['user_stakeholder_office'] ? 'selected=selected' : "") . " >" . $RowGroups->stkname;
                        print "</option>";
                    }
                }
                break;
            //*****************************************
            // Get province from stakeholder 
            //*****************************************
            case 2:
                $PkLocID = 0;
                //Getting id
                $objstk->m_npkId = $_REQUEST['id'];
                //Get Stakeholders By Id
                $rsStakeholders = $objstk->GetStakeholdersById();
                //Checking rsStakeholders
                if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
                    //Getting results
                    while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                        $lvl = $RowGroups->lvl;
                    }
                }
                //Setting level
                $objloc->LocLvl = $lvl;
                //Get All Locations
                $rsprov = $objloc->GetAllLocations();
                //Checking rsprov
                if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                    print "<option value=''>Select</option>";
                    //Getting results
                    while ($RowProv = mysql_fetch_object($rsprov)) {
                        print "<option value='" . $RowProv->PkLocID . ($RowProv->PkLocID == $PkLocID ? 'selected=selected' : "") . "' >" . $RowProv->LocName;
                        print "</option>";
                    }
                }
                break;
            //*****************************************
            // Get districts from province 
            //*****************************************
            case 3:
                $objloc->ParentID = $id;
                //Get All Locations from Parent
                $rsprov = $objloc->GetAllLocationsfromParent();

                //Checking rsprov
                if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                    //Getting results
                    while ($RowProv = mysql_fetch_object($rsprov)) {
                        ?>

                        <input type="checkbox" name="select4[]" id="select4" value='<?php echo $RowProv->PkLocID; ?>' <?php
                        if (!empty($_SESSION['distArr']) && in_array($RowProv->PkLocID, $_SESSION['distArr'])) {
                            echo "checked=checked";
                        }
                        ?> onclick="showwarehouse()"> <?php echo $RowProv->LocName; ?></br>
                               <?php
                           }
                       }
                       break;
                   //*****************************************
                   // Get sub-stakeholders parent at level 
                   //*****************************************
                   case 4:
                       $objstk->m_lvl = $_REQUEST['id'];
                       $objstk->m_MainStakeholder = $_REQUEST['id2'];
                       //Get All Stakeholders At Parent level
                       $rsStakeholders = $objstk->GetAllStakeholdersAtParentlevel();
                       if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
                           print "<option value=''>Select</option>";
                           while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                               print "<option value='" . $RowGroups->stkid . "' " . ($RowGroups->stkid == $_SESSION['parent_id'] ? 'selected=selected' : "") . " >" . $RowGroups->stkname;
                               print "</option>";
                           }
                       }
                       break;
                   //*****************************************
                   // Get districts from province 
                   //*****************************************
                   case 5:

                       $objwarehouse->m_dist_id = $id;
                       $objwarehouse->m_stkofficeid = $id2;
                       //Get Warehouse By loc By Stakeholder Office
                       $rsprov = $objwarehouse->GetWarehouseBylocByStakeholderOffice();

                       if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {

                           print "<option value=''  size='5' multiple='multiple'>Select....</option>";
                           while ($RowProv = mysql_fetch_object($rsprov)) {
                               print "<option value='" . $RowProv->wh_id . ($RowProv->wh_id == $wh_id ? 'selected=selected' : "") . "' >" . $RowProv->wh_name;
                               print "</option>";
                           }
                       }

                       break;
                   //*****************************************
                   // Get districts from province 
                   //*****************************************
                   case 6:
                       $id = implode(',', $_REQUEST['id']);
                       $objwarehouse->m_dist_id = (!empty($id)) ? $id : '0';
                       $lvl = $_REQUEST['lvl'];
                       $objwarehouse->m_lvl = $lvl;
                       $objwarehouse->m_stkid = $id2;
                       //Get Warehouse By loc By Stakeholder
                       $rsprov = $objwarehouse->GetWarehouseBylocByStakeholder();
                       if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                           while ($RowProv = mysql_fetch_object($rsprov)) {
                               ?>
                        <input type="checkbox" name="warehouses[]" id="warehouses" value='<?php echo $RowProv->wh_id ?>' <?php
                               if (!empty($_SESSION['whArr']) && in_array($RowProv->wh_id, $_SESSION['whArr'])) {
                                   echo "checked=checked";
                               }
                               ?>> <?php echo $RowProv->wh_name; ?></br>
                               <?php
                           }
                       }
                       break;
                   //*****************************************
                   // Get province from stakeholder
                   //*****************************************
                   case 7:
                       $PkLocID = 0;
                       $objstk->m_npkId = $_REQUEST['id'];
                       //Get Stakeholders By Id
                       $rsStakeholders = $objstk->GetStakeholdersById();
                       if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
                           while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                               $lvl = $RowGroups->lvl;
                           }
                       }
                       $objlvl->m_npkId = $lvl;
                       //Get Lower Levels
                       $rsprov = $objlvl->GetLowerLevels();
                       if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                           echo '<option value="">Select</option>';
                           while ($RowProv = mysql_fetch_object($rsprov)) {
                               if ($RowProv->lvl_id != 1) {
                                   print "<option value='" . $RowProv->lvl_id . "' " . ($RowProv->lvl_id == $_SESSION['level_id'] ? 'selected=selected' : "") . " >" . $RowProv->lvl_name;
                                   print "</option>";
                               }
                           }
                       }
                       break;
                   //*****************************************
                   // Get districts from province 
                   //*****************************************
                   case 8:
                       $objloc->ParentID = $id;
                       //Get All Locations from Parent
                       $rsprov = $objloc->GetAllLocationsfromParent();
                       if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                           print "<option value=''>Select</option>";
                           while ($RowProv = mysql_fetch_object($rsprov)) {
                               ?>

                        <?php
                        print "<option value='" . $RowProv->PkLocID . "' " . ($RowProv->PkLocID == $_SESSION['dist_id'] ? 'selected=selected' : "") . " >" . $RowProv->LocName;
                        print "</option>";
                    }
                }
                break;
            //*****************************************
            // Get districts from province 
            //*****************************************
            case 9:
                //id contains the location level
                $objManageLocations->TypeLvl = $id;
                //Get All Locations Type
                $rsprov = $objManageLocations->GetAllLocationsType();
                if ($rsprov != FALSE && mysql_num_rows($rsprov) > 0) {
                    print "<option value=''>Select</option>";
                    while ($RowProv = mysql_fetch_object($rsprov)) {
                        ?>
                        <?php
                        print "<option value='" . $RowProv->LoctypeID . "' " . ($RowProv->LoctypeID == $_SESSION['loc_type'] ? 'selected=selected' : "") . " >" . $RowProv->LoctypeName;
                        print "</option>";
                    }
                }
                break;

            case 10:
                //Gets 
                //rank
                $qry = "SELECT
						MAX(tbl_warehouse.wh_rank) + 1 AS rank
					FROM
						tbl_warehouse
					WHERE
						tbl_warehouse.dist_id = $id
					AND tbl_warehouse.stkid = $id2";
                $qryRes = mysql_fetch_row(mysql_query($qry));
                echo (!empty($qryRes[0])) ? round($qryRes[0]) : '';
                break;

            case 11:
                //Getting stkid
                $stkid = $_REQUEST['stkid'];
                //Grts
                //tbl_hf_type.pk_id
                //hf_type
                $qry = "SELECT
						tbl_hf_type.pk_id,
						tbl_hf_type.hf_type
					FROM
						tbl_hf_type
					WHERE
						tbl_hf_type.stakeholder_id = $stkid
					ORDER BY
						tbl_hf_type.hf_rank ASC";
                //Query result
                $qryRes = mysql_query($qry);
                //Getting result
                echo "<option value=\"\">Select</option>";
                while ($row = mysql_fetch_array($qryRes)) {
                    echo "<option value=\"" . $row['pk_id'] . "\">" . $row['hf_type'] . "</option>";
                }
                break;

            case 12:
                //Getting stkid
                $stkid = $_REQUEST['stkid'];
                //Gets
                //stakeholder.lvl
                $qry = "SELECT
						stakeholder.lvl
					FROM
						stakeholder
					WHERE
						stakeholder.stkid = $stkid";
                //Query result
                $qryRes = mysql_fetch_row(mysql_query($qry));
                echo $qryRes[0];
                break;

            case 13:
                //Getting stkid
                $dashid = $_REQUEST['dashid'];
                //Grts
                //tbl_hf_type.pk_id
                //hf_type
                $qry = "SELECT
	resources.pk_id,
	resources.page_title
FROM
	resources
WHERE
	resources.resource_type_id = 3
AND resources.parent_id = $dashid
ORDER BY
	resources.page_title ASC";
                //Query result
                $qryRes = mysql_query($qry);
                //Getting result
                echo "<option value=\"\">Select</option>";
                while ($row = mysql_fetch_array($qryRes)) {
                    echo "<option value=\"" . $row['pk_id'] . "\">" . $row['page_title'] . "</option>";
                }
                break;
        }
    }
}
?>
