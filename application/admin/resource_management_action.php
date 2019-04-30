<?php

/**
 * Resource Management Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including file
include("../includes/classes/AllClasses.php");
//Getting pk_id
$pk_id = mysql_real_escape_string(trim($_REQUEST['pk_id']));
//Getting resource_type_id
$resource_type_id = mysql_real_escape_string(trim($_REQUEST['resource_type_id']));
//Getting resource_name
$resource_name = mysql_real_escape_string(trim($_REQUEST['resource_name']));
//Getting description
$description = mysql_real_escape_string(trim($_REQUEST['description']));
//Getting page_title
$page_title = mysql_real_escape_string(trim($_REQUEST['page_title']));
//Getting parent_id
$parent_id = mysql_real_escape_string(trim($_REQUEST['parent_id']));
//Getting icon_class
$icon_class = mysql_real_escape_string(trim($_REQUEST['icon_class']));


$insQry = '';
if (!empty($description)) {
    //description
    $insQry .= ",resources.description = '" . $description . "' ";
} else {
    $insQry .= ",resources.description = NULL ";
}
if (!empty($page_title)) {
    //page_title
    $insQry .= ",resources.page_title = '" . $page_title . "' ";
} else {
    $insQry .= ",resources.page_title = NULL ";
}
if (!empty($parent_id)) {
    //parent_id
    $insQry .= ",resources.parent_id = '" . $parent_id . "' ";
}
if (!empty($icon_class)) {
    //icon_class
    $insQry .= ",resources.icon_class = '" . $icon_class . "' ";
} else {
    $insQry .= ",resources.icon_class = NULL ";
}

$status = 1;
$created_by = $modified_by = 1;
//Getting hdnToDo
$strDo = $_REQUEST['hdnToDo'];
/**
 * Add
 */
if ($strDo == "Add") {
    //Query for adding resource
    $qry = "INSERT INTO resources
		SET
			resources.resource_name = '" . $resource_name . "',
			resources.resource_type_id = '" . $resource_type_id . "',
			resources.created_by = '" . $created_by . "',
			resources.created_date = NOW(),
			resources.modified_by = '" . $modified_by . "',
			resources.modified_date = NOW()
			$insQry ";
    //Query result
    mysql_query($qry);

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * Query for editing resource
 */
if ($strDo == "Edit") {
    $qry = "UPDATE resources
			SET
				resources.resource_name = '" . $resource_name . "',
				resources.resource_type_id = '" . $resource_type_id . "',
				resources.modified_by = '" . $modified_by . "',
				resources.modified_date = NOW()
				$insQry 
			WHERE
				resources.pk_id = $pk_id";
    //Query result
    mysql_query($qry);
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
//Redirecting to resource_management
header("location: resource_management.php");
exit;
