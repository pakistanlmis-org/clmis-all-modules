<?php

/**
 * Manage Stakeholders Office Types Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../includes/classes/AllClasses.php");

$hdnstktypeId = 0;
$ParentID = 0;
$stkname = "";
$lstlvl = 0;
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //getting hdnToDo
    $strDo = $_REQUEST['hdnToDo'];
}
if (isset($_REQUEST['ID']) && !empty($_REQUEST['ID'])) {
    //getting ID
    $ID = $_REQUEST['ID'];
}
if (isset($_REQUEST['txtStktype']) && !empty($_REQUEST['txtStktype'])) {
    //stakeholder type
    $stkname = $_REQUEST['txtStktype'];
}
if (isset($_REQUEST['lstlvl']) && !empty($_REQUEST['lstlvl'])) {
    //getting lstlvl
    $lstlvl = $_REQUEST['lstlvl'];
    $objstk->m_lvl = $lstlvl;
}

if (isset($_REQUEST['lststkholdersParent']) && !empty($_REQUEST['lststkholdersParent'])) {
    //getting lststkholdersParent
    $ParentID = $_REQUEST['lststkholdersParent'];
    $objstk->m_ParentID = $ParentID;
}

if (isset($_REQUEST['Stakeholders']) && !empty($_REQUEST['Stakeholders'])) {
    //getting Stakeholders
    $MainStakeholder = $_REQUEST['Stakeholders'];
    $objstk->m_MainStakeholder = $MainStakeholder;
}
$objstk->m_stkname = $stkname;

$getStkType = mysql_fetch_array(mysql_query("SELECT
												stakeholder.stk_type_id
											FROM
												stakeholder
											WHERE
												stakeholder.stkid =" . $_POST['Stakeholders']));
$objstk->m_stk_type_id = $getStkType['stk_type_id'];


/**
 * 
 * Edit Stakeholder
 * 
 */
if ($strDo == "Edit") {
    $objstk->m_npkId = $ID;
    $objstk->EditStakeholder();
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * 
 * Add Stakeholder
 * 
 */
if ($strDo == "Add") {
    //max value
    $objstk->m_stkorder = $objstk->GetMaxRank() + 1; 
    //AddStakeholder
    $objstk->AddStakeholder();
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
//redirecting to ManageStakeholdersOfficeTypes
header("location:ManageStakeholdersOfficeTypes.php");
exit;
?>