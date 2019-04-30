<?php

/**
 * Assign Resources Ajax Copy
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
//Getting ctype
$ctype = mysql_real_escape_string(trim($_POST['ctype']));
//Getting role_id
$role_id = mysql_real_escape_string(trim($_POST['role_id']));
//Checking ctype
switch ($ctype) {
    case 1:
        // Query to get assigned resources to the selected roles
        //gets
        //resource_id
        //rank
        $qry = "SELECT
					role_resources.resource_id,
					role_resources.rank
				FROM
					role_resources
				WHERE
					role_resources.role_id = $role_id";
        //Query result 
        $qryRes = mysql_query($qry);
        //Gettinf results
        while ($row = mysql_fetch_array($qryRes)) {
            $assignedArr['resource'][] = $row['resource_id'];
            $assignedArr[$row['resource_id']] = $row['rank'];
        }

        $where = ($role_id == 2) ? ' WHERE resources.pk_id < 8' : ' WHERE resources.pk_id > 7';
        //Select query
        //gets
        //resources.pk_id
        //resource_name
        //parent_resource
        //resource_type_id
        $qry = "SELECT
					resources.pk_id,
					IF(ISNULL(resources.page_title), resources.resource_name, resources.page_title) AS resource_name,
					parent.resource_name AS parent_resource,
					resources.parent_id,
					resources.resource_type_id
				FROM
					resources
				LEFT JOIN resources AS parent ON resources.parent_id = parent.pk_id
				$where
				ORDER BY
					resources.resource_type_id ASC,
					resources.pk_id ASC,
					resources.parent_id ASC";
        //Query result
        $qryRes = mysql_query($qry);
        //Getting results
        while ($row = mysql_fetch_array($qryRes)) {
            $checked = (in_array($row['pk_id'], $assignedArr['resource'])) ? 'checked="checked"' : '';
            //Checking parent_id
            if (is_null($row['parent_id'])) {
                echo "<br /><br /><h4>";
                echo "<input $checked type=\"checkbox\" style=\"display:none;\" id=\"parent_" . $row['pk_id'] . "\" name=\"resources[]\" value=\"" . $row['pk_id'] . "\" />&nbsp;";
                echo "<select name=\"rank_" . $row['pk_id'] . "\" class=\"form-control input-sm\" style=\"display:inline; width:90px\">";
                echo "<option value=\"\">Rank</option>";
                for ($i = 1; $i <= 20; $i++) {
                    $selected = (in_array($row['pk_id'], $assignedArr['resource']) && $assignedArr[$row['pk_id']] == $i) ? 'selected="selected"' : '';
                    echo "<option value=\"" . $i . "\" $selected>" . $i . "</option>";
                }
                echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "" . $row['resource_name'] . "</h4>";
            } else {
                echo "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "<input $checked type=\"checkbox\" class=\"parent_" . $row['parent_id'] . "\" name=\"resources[]\" value=\"" . $row['pk_id'] . "\" onClick=\"checkParent(this.className)\" />&nbsp;";
                echo "<label>";
                echo "<select name=\"rank_" . $row['pk_id'] . "\" class=\"form-control input-sm\" style=\"display:inline; width:90px\">";
                echo "<option value=\"\">Rank</option>";
                for ($i = 1; $i <= 20; $i++) {
                    $selected = (in_array($row['pk_id'], $assignedArr['resource']) && $assignedArr[$row['pk_id']] == $i) ? 'selected="selected"' : '';
                    echo "<option value=\"" . $i . "\" $selected>" . $i . "</option>";
                }
                echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "" . $row['resource_name'] . "</label>";
            }
        }
        break;
    case 2:
        //Delete query
        $qry = "DELETE FROM role_resources
			WHERE
				role_resources.role_id = " . $role_id . " ";
        mysql_query($qry);
        mysql_query("ALTER TABLE role_resources AUTO_INCREMENT=1 ENGINE=innoDB");

        foreach ($_POST['resources'] as $resource) {
            //Insert query
            $qry = "INSERT INTO role_resources
				SET
					role_resources.resource_id = " . $resource . ",
					role_resources.rank = '" . mysql_real_escape_string($_POST['rank_' . $resource]) . "',
					role_resources.role_id = " . $role_id . " ";
            mysql_query($qry);
        }
        //Displaying messages
        $_SESSION['err']['text'] = 'Resources has been successfully assigned.';
        $_SESSION['err']['type'] = 'success';
        break;
}