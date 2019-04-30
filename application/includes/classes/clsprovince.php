<?php

/**
 * clsprovince
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsprovince {
    //prov_id
    var $m_prov_id;
    //prov_title
    var $m_prov_title;
    //regionstatus
    var $m_regionstatus;
/**
 * Addprovince
 * @return int
 */
    function Addprovince() {
        $strSql = "INSERT INTO  province(prov_title,regionstatus) VALUES('" . $this->$m_prov_title . "'," . $this->$m_regionstatus . ")";

        $rsSql = mysql_query($strSql) or die("Error province");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }
/**
 * Editprovince
 * @return boolean
 */
    function Editprovince() {
        $strSql = "UPDATE province SET prov_title='" . $this->$m_prov_title . "',regionstatus=" . $this->$m_regionstatus . " WHERE prov_id=" . $this->m_prov_id;
        $rsSql = mysql_query($strSql) or die("Error province");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * Deleteprovince
 * @return boolean
 */
    function Deleteprovince() {
        $strSql = "DELETE FROM  province WHERE prov_id=" . $this->m_prov_id;
        $rsSql = mysql_query($strSql) or die("Error Deleteprovince");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllprovince
 * @return boolean
 */
    function GetAllprovince() {
        $strSql = "SELECT prov_id,prov_title,regionstatus FROM  province";
        $rsSql = mysql_query($strSql) or die("Error GetAllprovince");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetProvinceById
 * @return boolean
 */
    function GetProvinceById() {
        $strSql = "SELECT prov_id,prov_title,regionstatus FROM  province WHERE prov_id=" . $this->m_prov_id;
        $rsSql = mysql_query($strSql) or die("Error GetProvinceById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>