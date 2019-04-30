<?php

/**
 * Manage Stakeholders Items Action
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

//Initializing variables
$nstkId = 0;
$stkname = "";
$stkgroupid = 0;
$strNewGroupName = "";
$stktype = 0;
$prov_id = 0;

//Getting form data
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //getting hdnToDo
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['ItemID']) && !empty($_REQUEST['ItemID'])) {
    //getting Item Id
    $ItemID = $_REQUEST['ItemID'];
    //Delete stakeholder item
    $objstakeholderitem->m_stk_id = $nstkId;
    $objstakeholderitem->Deletestakeholderitem();

    foreach ($ItemID as $arec) {
        $objstakeholderitem->m_stk_item = $arec;
        $objstakeholderitem->m_stkid = $nstkId;
        //Add  stakeholder item1
        $objstakeholderitem->Addstakeholderitem1();
    }
}
//redirecting to ManageStakeholdersItems
header("location:ManageStakeholdersItems.php");
exit;
?>