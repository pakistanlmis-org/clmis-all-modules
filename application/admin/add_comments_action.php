<?php

/**
 * add_action_manufacturer
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
$strDo = "Add";
$nstkId = 0;
$autorun = false;

if (!empty($_REQUEST['add_comments'])) {
    //Getting new_manufacturer
    $new_comments = $_REQUEST['add_comments'];
    $hdncommentId = $_REQUEST['hdncommentId'];
    //Getting itm_id
    $dashboard_id = $_REQUEST['dashboard_id'];
    $dashlet_id = $_REQUEST['dashlet_id'];
    $stakeholder_id = $_REQUEST['stakeholder_id'];
    $location_id = $_REQUEST['location_id'];
    $mon_year = $_REQUEST['month_year'];
    $comments = trim(preg_replace('/\s\s+/', '<br/>', $_REQUEST['comments']));

    $objcomments->dashlet_id = $dashlet_id;
    $objcomments->dashboard_id = $dashboard_id;
    $objcomments->stakeholder_id = $stakeholder_id;
    $objcomments->location_id = $location_id;
    $objcomments->comments = $comments;
    $objcomments->month_year = $mon_year.'-01';

    if (!empty($hdncommentId)) {
        $objcomments->pk_id = $hdncommentId;
        $stkItemId = $objcomments->save();

        $_SESSION['err']['text'] = 'Data has been successfully updated.';

        $_SESSION['err']['type'] = 'success';

    } else {

        $stkItemId = $objcomments->save();
        $_SESSION['err']['text'] = 'Data has been successfully added.';

        $_SESSION['err']['type'] = 'success';
    }
}

redirect("ManageDashboardComments.php");
exit;
?>