<?php

/**
 * Assign Resources Ajax
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

//Getting ctype
$ctype = mysql_real_escape_string(trim($_POST['ctype']));
//Getting role_id
$role_id = mysql_real_escape_string(trim($_POST['role_id']));
?>
<style>
    ul, li{list-style:none;}
</style>
<?php

/**
 * sub
 * 
 * @param type $items
 * @param type $id
 * @param type $assignedArr
 */
function sub($items, $id, $assignedArr) {
    $checked="";
    echo "<ul>";
    foreach ($items as $item) {
        if(!empty($assignedArr['resource']))
        $checked = (in_array($item['pk_id'], $assignedArr['resource'])) ? 'checked="checked"' : '';
        //Checking parent_id
        if ($item['parent_id'] == $id) {
            echo "<li>";
            echo "<input $checked type=\"checkbox\" name=\"resources[]\" value=\"" . $item['pk_id'] . "\" />&nbsp;";
            echo "<label>";
            echo "<select name=\"rank_" . $item['pk_id'] . "\" class=\"form-control input-sm\" style=\"display:inline; width:90px\">";
            echo "<option value=\"\">Rank</option>";
            for ($i = 1; $i <= 20; $i++) {
                $selected = (in_array($item['pk_id'], $assignedArr['resource']) && $assignedArr[$item['pk_id']] == $i) ? 'selected="selected"' : '';
                echo "<option value=\"" . $i . "\" $selected>" . $i . "</option>";
            }
            echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "" . $item['resource_name'] . "</label>";
            //Calling function sub
            sub($items, $item['pk_id'], $assignedArr);
            echo "</li>";
        }
    }
    echo "</ul>";
}

//Checking ctype
switch ($ctype) {
    case 1:
        // Query to get assigned resources to the selected roles
        // gets 
        //resource_id,
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
        $assignedArr =array();
        //Getting results
        while ($row = mysql_fetch_array($qryRes)) {
            $assignedArr['resource'][] = $row['resource_id'];
            $assignedArr[$row['resource_id']] = $row['rank'];
        }
        //echo '<pre>';        print_r($assignedArr);        exit;
        //gets
        //pk_id
        //resource_name
        //parent_resource
        //parent_id
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
				WHERE
					resources.resource_type_id in (1,2)
				ORDER BY
					resources.resource_type_id ASC,
					resources.pk_id ASC,
					resources.parent_id ASC";
        //Query result
        $qryRes = mysql_query($qry);
        //Getting results
        while ($row = mysql_fetch_assoc($qryRes)) {
            $items[] = $row;
        }
        $checked="";
        echo "<ul>";
        foreach ($items as $item) {
            if(!empty($assignedArr['resource']))
            $checked = (in_array($item['pk_id'], $assignedArr['resource'])) ? 'checked="checked"' : '';
            //Ceecking parent_id
            if (is_null($item['parent_id'])) {
                echo "<li>";
                echo "<input $checked type=\"checkbox\" name=\"resources[]\" value=\"" . $item['pk_id'] . "\" />&nbsp;";
                echo "<label>";
                echo "<select name=\"rank_" . $item['pk_id'] . "\" class=\"form-control input-sm\" style=\"display:inline; width:90px\">";
                echo "<option value=\"\">Rank</option>";
                //Populate rank combo
                for ($i = 1; $i <= 20; $i++) {
                    $selected = (in_array($item['pk_id'], $assignedArr['resource']) && $assignedArr[$item['pk_id']] == $i) ? 'selected="selected"' : '';
                    echo "<option value=\"" . $i . "\" $selected>" . $i . "</option>";
                }
                echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
                echo "" . $item['resource_name'] . "</label>";
                $id = $item['pk_id'];
                //Calling function sub
                sub($items, $id, $assignedArr);
                echo "</li>";
            }
        }
        echo "</ul>";
        break;
    case 2:
        //Delete query
        $qry = "DELETE FROM role_resources
			WHERE
				role_resources.role_id = " . $role_id . " ";
        mysql_query($qry);
        mysql_query("ALTER TABLE role_resources AUTO_INCREMENT=1 ENGINE=innoDB");
        //Insert query
        foreach ($_POST['resources'] as $resource) {
            $qry = "INSERT INTO role_resources
				SET
					role_resources.resource_id = " . $resource . ",
					role_resources.rank = '" . mysql_real_escape_string($_POST['rank_' . $resource]) . "',
					role_resources.role_id = " . $role_id . " ";
            mysql_query($qry);
        }
        //unset session variable text
        $_SESSION['err']['text'] = 'Resources has been successfully assigned.';
        //unset session variable type
        $_SESSION['err']['type'] = 'success';
        break;
}