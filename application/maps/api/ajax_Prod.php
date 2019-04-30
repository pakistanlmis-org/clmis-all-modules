<?php
/**
 * ajax_Prod
 * @package maps/api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH . "includes/classes/db.php");

//stk
$stk = $_REQUEST["stk"];
//type
$type = $_REQUEST["type"];

if ($stk == 'all') {
    //query 
    //gets
    //itmrec_id
    //itm_name
    //itm_type
    $query = "SELECT
                 itminfo_tab.itmrec_id,
                 itminfo_tab.itm_name,
                 itminfo_tab.itm_type
                        FROM
                                itminfo_tab
                        WHERE
                                itminfo_tab.itmrec_id NOT IN ('IT-010', 'IT-014', 'IT-012','IT-031','IT-032')       
                        ORDER BY
                        itminfo_tab.frmindex ASC";
} else {
    //query 
    //gets
    //itmrec_id
    //itm_name
    //itm_type
    $query = "SELECT
                 itminfo_tab.itmrec_id,
                 itminfo_tab.itm_name,
                 itminfo_tab.itm_type
                        FROM
                                itminfo_tab
                INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                        WHERE
                                itminfo_tab.itmrec_id NOT IN ('IT-010', 'IT-014', 'IT-012','IT-031','IT-032')
                               
                        AND stakeholder_item.stkid = $stk         
                        ORDER BY
                        itminfo_tab.frmindex ASC";
}


$rsstk = mysql_query($query);
$selected = 'IT-001';
if ($type == "CoupleYearProtection" || $type == "CYPNormalizedByPopulation") {
    echo '<option value="all">All</option>';
}
while ($result = mysql_fetch_array($rsstk)) {
    if ($selected == $result['itmrec_id']) {
        $sel = "selected='selected'";
    } else {
        $sel = "";
    }
    ?>
    <option value="<?php echo $result['itmrec_id']; ?>" <?php echo $sel; ?> ><?php echo $result['itm_name']; ?></option>
    <?php
}
?>