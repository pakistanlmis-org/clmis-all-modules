<?php

/**
 * clssdistrict
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

class clsdistric {
    //warehouse rc id
    var $m_whrec_id;
    //warehouse name
    var $m_wh_name;
    //warehouse address
    var $m_wh_address;
    // contact person
    var $m_contact_person;
    //contact email
    var $m_contemail;
    //province
    var $m_province;
    //counter
    var $m_counter;
    //district id
    var $m_dist_id;

    /**
     * 
     * Adddistric
     * @return int
     * 
     */
    function Adddistric() {
        //insert query
        $strSql = "INSERT INTO  tbl_districts (whrec_id,wh_name,wh_address,contact_person,contemail,province,counter,dist_id) VALUES('" . $this->m_whrec_id . "','" . $this->m_wh_name . "','" . $this->m_wh_address . "','" . $this->m_contact_person . "','" . $this->m_contemail . "'," . $this->m_province . "," . $this->m_counter . "," . $this->m_dist_id . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Adddistric");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Editdistric
     * @return boolean
     */
    function Editdistric() {
        //update query
        $strSql = "UPDATE tbl_districts SET whrec_id=" . $this->m_whrec_id . ",wh_name=" . $this->m_wh_name . ",wh_address=" . $this->m_wh_address . ",contact_person=" . $this->m_contact_person . ",contemail='" . $this->m_contemail . "',province='" . $this->m_province . "',counter='" . $this->m_counter . "' WHERE dist_id=" . $this->m_dist_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error Editdistric");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Deletedistric
     * @return boolean
     */
    function Deletedistric() {
        //delete query
        $strSql = "DELETE FROM `lmisdbn`.`tbl_districts` WHERE `tbl_districts`.`dist_id`=" . $this->m_dist_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error Deletedistric" . $this->m_dist_id);
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAlldistric
     * @return boolean
     */
    function GetAlldistric() {
        //select query
        $strSql = "SELECT
						tbl_districts.whrec_id,
						tbl_districts.wh_name,
						tbl_districts.wh_address,
						tbl_districts.contact_person,
						tbl_districts.contemail,
						province.prov_id,
						province.prov_title,
						tbl_districts.counter,
						tbl_districts.dist_id
						FROM
						tbl_districts
						Inner Join province ON tbl_districts.province = province.prov_id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAlldistric");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetdistricById
     * @return boolean
     */
    function GetdistricById() {
        //select query
        $strSql = "SELECT
						tbl_districts.whrec_id,
						tbl_districts.wh_name,
						tbl_districts.wh_address,
						tbl_districts.contact_person,
						tbl_districts.contemail,
						province.prov_id,
						province.prov_title,
						tbl_districts.counter,
						tbl_districts.dist_id
						FROM
						tbl_districts
						Inner Join province ON tbl_districts.province = province.prov_id
						WHERE dist_id=" . $this->m_dist_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetdistricById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetdistricByProvince
     * @return boolean
     */
    function GetdistricByProvince() {
        //select query
        $strSql = "SELECT
						tbl_districts.whrec_id,
						tbl_districts.wh_name,
						tbl_districts.wh_address,
						tbl_districts.contact_person,
						tbl_districts.contemail,
						province.prov_id,
						province.prov_title,
						tbl_districts.counter,
						tbl_districts.dist_id
						FROM
						tbl_districts
						Inner Join province ON tbl_districts.province = province.prov_id
						WHERE tbl_districts.province=" . $this->m_province;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetdistricById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
