<?php

function get_user_basic_info($user_id){
    $strSql = "SELECT
                sysuser_tab.sysusrrec_id,
                sysuser_tab.sysusr_type,
                sysuser_tab.user_level,
                sysuser_tab.usrlogin_id,
                sysuser_tab.sysusr_name,
                sysuser_tab.sysusr_ph,
                sysuser_tab.sysusr_cell,
                sysuser_tab.sysusr_email,
                sysuser_tab.UserID
            FROM
                sysuser_tab
            WHERE
                sysuser_tab.UserID='" . $user_id . "'";
    $rsSql = mysql_query($strSql) or die("Error get_user_basic_info");
    if (mysql_num_rows($rsSql) > 0) {
        $data = mysql_fetch_assoc($rsSql);
        return $data;
    } else {
        return FALSE;
    }
}

function get_user_detail_info($user_id){
    $strSql = "SELECT
                        sysuser_tab.UserID,
                        sysuser_tab.stkid,
                        stakeholder.stkname,
                        tbl_warehouse.wh_id AS whrec_id,
                        tbl_warehouse.wh_name,
                        district.PkLocID AS dist_id,
                        district.LocName AS district,
                        province.PkLocID AS prov_id,
                        province.LocName AS province,
                        tbl_warehouse.wh_id,
                        sysuser_tab.usrlogin_id
                FROM
                        sysuser_tab
                LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
                INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id
            WHERE
                sysuser_tab.UserID='" . $user_id . "'";
    //query result
    ///echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error GetUserByid");
    if (mysql_num_rows($rsSql) > 0) {
        return $rsSql;
    } else {
        return FALSE;
    }
}


function get_wh_basic_info($wh_id){
    $strSql = "SELECT
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                tbl_warehouse.wh_type_id,
                tbl_warehouse.dist_id,
                tbl_warehouse.prov_id,
                tbl_warehouse.stkid,
                tbl_warehouse.hf_type_id
                FROM
                tbl_warehouse
                WHERE 
                tbl_warehouse.wh_id = '" . $wh_id . "'";
    $rsSql = mysql_query($strSql) or die("Error get_wh_basic_info");
    if (mysql_num_rows($rsSql) > 0) {
        $data = mysql_fetch_assoc($rsSql);
        return $data;
    } else {
        return FALSE;
    }
}