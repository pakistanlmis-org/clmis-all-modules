<?php

/**
 * ClsLevels
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsLevels {
    
    var $m_lvl_id;
    var $m_lvl_name;

    /**
     * Add Levels
     * 
     * @return int
     */
    function AddLevels() {
        //add query
        $strSql = "INSERT INTO  tbl_dist_levels (lvl_name) VALUES('" . $this->$m_lvl_name . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddLevels");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * Edit Levels
     * 
     * @return boolean
     */
    function EditLevels() {
        //edit query
        $strSql = "UPDATE tbl_dist_levels SET lvl_name='" . $this->$m_lvl_name . "' WHERE lvl_id=" . $this->m_lvl_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error EditLevels");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Delete Levels
     * 
     * @return boolean
     */
    function DeleteLevels() {
        //delet query
        $strSql = "DELETE FROM  tbl_dist_levels WHERE lvl_id=" . $this->m_lvl_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteLevels");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get All Levelss
     * 
     * @return boolean
     */
    function GetAllLevelss() {
        $strSql = "SELECT lvl_id,lvl_name FROM  tbl_dist_levels";
        $rsSql = mysql_query($strSql) or die("Error GetAllLevelss");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Get Level By Id
     * 
     * @return boolean
     */
    function GetLevelById() {
        $strSql = "SELECT lvl_id,lvl_name FROM  tbl_dist_levels WHERE lvl_id=" . $this->m_lvl_id;
        $rsSql = mysql_query($strSql) or die("Error GetLevelsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Get Lower Levels
     * 
     * @return boolean
     */
    function GetLowerLevels() {
        $strSql = "SELECT lvl_id,lvl_name FROM  tbl_dist_levels WHERE lvl_id >" . $this->m_lvl_id;
        $rsSql = mysql_query($strSql) or die("Error GetLevelsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>