<?php

//Including required file
include("../../includes/classes/AllClasses.php");

$province_id = $_POST['prov_id'];
$stakeholder_id = $_POST['stk_id'];
$user_id = $_SESSION['user_id'];

$selected = $_POST['selected_options'];

foreach ($selected as $ftype_id => $products) {
    foreach ($products as $product_id => $wh) {
        foreach ($wh as $wh_id => $value) {
            $qry = "SELECT pk_id, value FROM alerts_mapping WHERE stakeholder_id = $stakeholder_id AND province_id = $province_id AND "
                    . " hf_type_id = $ftype_id AND product_id = $product_id AND alert_type = 1 AND warehouse_id = $wh_id";
            $res = mysql_query($qry);
            if (mysql_num_rows($res) > 0) {
                $resset = mysql_fetch_assoc($res);
                $pk_id = $resset['pk_id'];
                $old_value = $resset['value'];

                if ($old_value != $value) {
                    $qryupdate = "UPDATE alerts_mapping SET value = '$value' WHERE pk_id = $pk_id";
                    mysql_query($qryupdate);
                }
            } else {
                mysql_query("INSERT INTO alerts_mapping (
            stakeholder_id,
            province_id,
            hf_type_id,
            product_id,
            alert_type,
            `value`,
            warehouse_id,
            created_by) VALUES ($stakeholder_id, $province_id, $ftype_id, $product_id, 1, '$value', $wh_id, $user_id)
            ");
            }
        }
    }
}

mysql_query("INSERT INTO alerts_mapping (
            stakeholder_id,
            province_id,
            hf_type_id,
            product_id,
            alert_type,
            `value`,
            created_by) VALUES ($stakeholder_id, $province_id, $ftype_id, $product_id, 1, 'Available (at selected HFs)', $user_id)
            ");

$_SESSION['err']['text'] = 'Data has been successfully updated.';
$_SESSION['err']['type'] = 'success';

echo "<script>var theform = window.opener.document.forms[0].submit();
window.close();</script>";

//header("location: stockout.php?stk-prov-filter=submit&stakeholder=$stakeholder_id&province=$province_id");