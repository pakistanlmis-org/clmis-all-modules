<?php
/**
 * ajax
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//get ctype
$cat_type = isset($_POST['cat']) ? $_POST['cat'] : '';
$where_clause = '';
if ($cat_type == 1) {
    $where_clause = "WHERE itminfo_tab.itm_category in (1,2)";
} else if ($cat_type == 5) {
    $where_clause = "WHERE itminfo_tab.itm_category =  $cat_type ";
} else {
    $where_clause = "";
}

$qry = "SELECT DISTINCT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_category
            FROM
                    itminfo_tab
            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item 
            $where_clause
            ORDER BY
                itm_category,
                itminfo_tab.frmindex ASC
";
//print_r($qry);
$res = mysql_query($qry);



echo '<label class="checkbox">';
echo "<input type=\"checkbox\" name=\"product_multi_check\" id=\"product_multi_check\" value=\"all\" />All</label>";

while ($row = mysql_fetch_array($res)) {

    if ($row['itm_id'] == '31' || $row['itm_id'] == '32')
        $row['itm_name'] = 'CS Cases - ' . $row['itm_name'];
    echo '<label class="checkbox">';
    echo "<input type=\"checkbox\" name=\"product_multi[]\" id=\"product_multi\" value=\"" . $row['itm_id'] . "\" /> " . $row['itm_name'];
    echo "</label>";
}
//print_r($qry)
?>
<script>
    $(function () {

        $("#product_multi_check").change(function () {

            var a = $(this).prop('checked');
            if (a) {
                $('input:checkbox').prop('checked', true);
                //$("input[id=product_multi]").checked("true");
            } else {
                $('input:checkbox').prop('checked', false);
            }
        });
    });



</script>