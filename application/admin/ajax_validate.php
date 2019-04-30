<?php

/**
 * ajax validate
 * used for validating ajax data
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
//Validate 
//usrlogin_id
if (isset($_REQUEST['usrlogin_id'])) {
    //Getting usrlogin_id
    $val = mysql_real_escape_string($_REQUEST['usrlogin_id']);
    //Query for usrlogin_id
    $qry = "SELECT
				COUNT(sysuser_tab.usrlogin_id) AS num
			FROM
				sysuser_tab
			WHERE
				sysuser_tab.usrlogin_id = '$val'";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//login
if (isset($_REQUEST['login'])) {
    //Getting login
    $val = mysql_real_escape_string($_REQUEST['login']);
    //Query login
    $qry = "SELECT
				COUNT(sysuser_tab.usrlogin_id) AS num
			FROM
				sysuser_tab
			WHERE
				sysuser_tab.usrlogin_id = '$val'";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//txtStkName
if (isset($_REQUEST['txtStkName'])) {
    //Getting txtStkName
    $val = mysql_real_escape_string($_REQUEST['txtStkName']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND stakeholder.stkid != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for txtStkName
    $qry = "SELECT 
				COUNT(stakeholder.stkid) AS num
			FROM
				stakeholder
			WHERE
				stakeholder.stkname = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//txtStktype
if (isset($_REQUEST['txtStktype'])) {
    //Getting txtStktype
    $val = mysql_real_escape_string($_REQUEST['txtStktype']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND stakeholder.stkid != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for txtStktype
    $qry = "SELECT
				COUNT(stakeholder.stkid) AS num
			FROM
				stakeholder
			WHERE
				stakeholder.stkname = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//txtStkName1
if (isset($_REQUEST['txtStkName1'])) {
    //Getting txtStkName1
    $val = mysql_real_escape_string($_REQUEST['txtStkName1']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND itminfo_tab.itm_id != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for txtStkName1
    $qry = "SELECT
				COUNT(itminfo_tab.itm_id) AS num
			FROM
				itminfo_tab
			WHERE
				itminfo_tab.itm_name = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//ItemGroupName
if (isset($_REQUEST['ItemGroupName'])) {
    //Getting ItemGroupName
    $val = mysql_real_escape_string($_REQUEST['ItemGroupName']);
    $where = '';
    //Checking pk_id
    if (isset($_SESSION['pk_id'])) {
        $where = " AND itemgroups.PKItemGroupID != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for ItemGroupName
    $qry = "SELECT
				COUNT(itemgroups.PKItemGroupID) AS num
			FROM
				itemgroups
			WHERE
				itemgroups.ItemGroupName = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate productcategory
if (isset($_REQUEST['productcategory'])) {
    //Getting productcategory
    $val = mysql_real_escape_string($_REQUEST['productcategory']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND tbl_product_category.PKItemCategoryID != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for productcategory
    $qry = "SELECT
				COUNT(tbl_product_category.PKItemCategoryID) AS num
			FROM
				tbl_product_category
			WHERE
				tbl_product_category.ItemCategoryName = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//productstatus
if (isset($_REQUEST['productstatus'])) {
    //Getting productstatus
    $val = mysql_real_escape_string($_REQUEST['productstatus']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND tbl_product_status.PKItemStatusID != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for productstatus
    $qry = "SELECT
				COUNT(tbl_product_status.PKItemStatusID) AS num
			FROM
				tbl_product_status
			WHERE
				tbl_product_status.ItemStatusName = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//producttype
if (isset($_REQUEST['producttype'])) {
    //Getting producttype
    $val = mysql_real_escape_string($_REQUEST['producttype']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND tbl_itemunits.pkUnitID != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for producttype
    $qry = "SELECT
				COUNT(tbl_itemunits.pkUnitID) AS num
			FROM
				tbl_itemunits
			WHERE
				tbl_itemunits.UnitType = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//loc_name
if (isset($_REQUEST['loc_name'])) {
    //Getting loc_name
    $val = mysql_real_escape_string($_REQUEST['loc_name']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND tbl_locations.PkLocID != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for loc_name
    $qry = "SELECT
				COUNT(tbl_locations.PkLocID) AS num
			FROM
				tbl_locations
			WHERE
				tbl_locations.LocName = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//StakeholderTypeName
if (isset($_REQUEST['StakeholderTypeName'])) {
    //Getting StakeholderTypeName
    $val = mysql_real_escape_string($_REQUEST['StakeholderTypeName']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND stakeholder_type.stk_type_id != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for StakeholderTypeName
    $qry = "SELECT
				COUNT(stakeholder_type.stk_type_id) AS num
			FROM
				stakeholder_type
			WHERE
				stakeholder_type.stk_type_descr = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}
//Validate
//HealthFacilityTypeName
if (isset($_REQUEST['HealthFacilityTypeName'])) {
    //Getting HealthFacilityTypeName
    $val = mysql_real_escape_string($_REQUEST['HealthFacilityTypeName']);
    $where = '';
    if (isset($_SESSION['pk_id'])) {
        //Checking pk_id
        $where = " AND tbl_hf_type.pk_id != '" . $_SESSION['pk_id'] . "'";
    }
    //Query for HealthFacilityTypeName
    $qry = "SELECT
		COUNT(tbl_hf_type.pk_id) AS num
		FROM
				tbl_hf_type
			WHERE
				tbl_hf_type.hf_type = '$val'
			$where ";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //Checking result count
    if ($qryRes['num'] == 1) {
        $isValid = 'false';
    } else {
        $isValid = 'true';
    }
    echo $isValid;
}