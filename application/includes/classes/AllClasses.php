<?php
/**
 * AllClasses
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//require Configuration.inc file
require_once("Configuration.inc.php");
//time zone
date_default_timezone_set('Asia/Karachi');
//check user id
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    echo "<script>window.location='../../index.php?strMsg=Please+login'</script>";
    exit;
}
require_once("database.php");
require_once("functions.php");
require_once("clsConfiguration.php");
require_once("clsDatabaseManager.php");
require_once("db.php");
require_once("clsStakeholders.php");
require_once("clsManageUser.php");
require_once("clsstakeholders_type.php");
require_once("clsswharehouse.php");
require_once("clsLocations.php");
require_once("clsManageitem.php");
require_once("clsDistriclevel.php");
require_once("clsItemGroup.php");
require_once("clsItemofGroup.php");
require_once("clsIstakeholder_item.php");
require_once("clswarehouse_user.php");
require_once("clsReports.php");
require_once("clsManageContent.php");
require_once("clsManageLocations.php");
require_once("clsProductType.php");
require_once("clsProductCategory.php");
require_once("clsManageStatus.php");
require_once("clsUserProvinces.php");
require_once("clsUserStackholders.php");
require_once("clsWarehouseData.php");

require_once("clsStockMaster.php");
require_once("clsStockDetail.php");
require_once("clsStockBatch.php");
require_once("clsTransTypes.php");
require_once("clsItemUnits.php");
require_once("clsFiscalYear.php");
require_once("clsvvmTypes.php");
require_once("clsStakeholderType.php");
require_once("clsHealthFacilityType.php");
require_once("clsShipments.php");
require_once("clsComments.php");
require_once("clsAlerts.php");
//creatting object of clsstakeholder
$objstk = new clsstakeholder();
//creatting object of clsStk_Type
$objstkType = new clsStk_Type();
//creatting object of clsUser
$objuser = new clsUser();
//creatting object of clswarehouse
$objwarehouse = new clswarehouse();
//creatting object of clslocations
$objloc = new clslocations();
//creatting object of clstbl_dist_levels
$objlvl = new clstbl_dist_levels();
//creatting object of clsManageItem
$objManageItem = new clsManageItem();
//creatting object of clsItemGroup
$ItemGroup = new clsItemGroup();
//creatting object of clsItemOfGroup
$ItemOfGroup = new clsItemOfGroup();
//creatting object of clsstakeholderitem
$objstakeholderitem = new clsstakeholderitem();
//creatting object of clsIwh_user
$objwharehouse_user = new clsIwh_user();
//creatting object of ClsReports
$objReports = new ClsReports();
//creatting object of ClsContent
$objContent = new ClsContent();
//creatting object of clsManagelocations
$objManageLocations = new clsManagelocations();
//creatting object of clsItemType
$objitemtype = new clsItemType();
//creatting object of 
$objitemcategory = new clsItemCategory();
//creatting object of clsItemCategory
$objitemstatus = new clsItemStatus();
//creatting object of clsUserProvinces
$objuserprov = new clsUserProvinces();
//creatting object of clsUserStackholders
$objuserstk = new clsUserStackholders();
//creatting object of clsWarehouseData
$objWhData = new clsWarehouseData();
//creatting object of MySQLDatabase
$database = new MySQLDatabase();
//creatting object of clsStockMaster
$objStockMaster = new clsStockMaster();
//creatting object of clsStockDetail
$objStockDetail = new clsStockDetail();
//creatting object of clsStockBatch
$objStockBatch = new clsStockBatch();
//creatting object of clsTransTypes
$objTransType = new clsTransTypes();
//creatting object of clsItemUnits
$objItemUnits = new clsItemUnits();
//creatting object of clsvvmTypes
$objvvmType = new clsvvmTypes();
//creatting object of clsFiscalYear
$objFiscalYear = new clsFiscalYear();
//creatting object of clsStakeholderType
$StakeholderType = new clsStakeholderType();
//creatting object of clsHealthFacilityType
$HealthFacilityType = new clsHealthFacilityType();
//creatting object of clsHealthFacilityType
$objshipments = new clsShipments();
$objcomments = new clsComments();
$objAlerts= new clsAlerts();
