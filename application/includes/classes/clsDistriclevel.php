<?php

/**
 * clsDistrictlevel
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clstbl_dist_levels {
    //npkId
    var $m_npkId;
    //lvl_name
    var $m_lvl_name;
    //lvl_desc
    var $m_lvl_desc;

    /**
     * Addtbl_dist_levels
     * 
     * @return int
     */
    function Addtbl_dist_levels() {
        $strSql = "INSERT INTO  tbl_dist_levels (lvl_name,lvl_desc) VALUES('" . $this->m_lvl_name . "','" . $this->m_lvl_desc . "')";
        $rsSql = mysql_query($strSql) or die("Error Addtbl_dist_levels");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Edittbl_dist_levels
     * 
     * @return boolean
     */
    function Edittbl_dist_levels() {
        $strSql = "UPDATE tbl_dist_levels SET lvl_name='" . $this->m_lvl_name . "',lvl_desc='" . $this->m_lvl_desc . "' WHERE lvl_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error Edittbl_dist_levels");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Deletetbl_dist_levels
     * 
     * @return boolean
     */
    function Deletetbl_dist_levels() {
        $strSql = "DELETE FROM  tbl_dist_levels WHERE lvl_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error Deletetbl_dist_levels");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAlllevels
     * 
     * @return boolean
     */
    function GetAlllevels() {
        $strSql = "SELECT lvl_id,lvl_name,lvl_desc FROM  tbl_dist_levels";
        $rsSql = mysql_query($strSql) or die("Error GetAlllevels");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAlllevelsById
     * 
     * @return boolean
     */
    function GetAlllevelsById() {
        $strSql = "SELECT lvl_id,lvl_name,lvl_desc FROM  tbl_dist_levels WHERE lvl_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetAlllevelsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetLowerLevels
     * 
     * @return boolean
     */
    function GetLowerLevels() {
        $strSql = "SELECT lvl_id,lvl_name FROM  tbl_dist_levels WHERE lvl_id >=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetLevelsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
