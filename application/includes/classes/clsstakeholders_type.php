<?php

/**
 * clsstakeholder_type
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsStk_Type {
    //stk_type_id
    var $m_stk_type_id;
    //stk_type_descr
    var $m_stk_type_descr;

    /**
     * AddStk_type
     * @return int
     */
    function AddStk_type() {
        $strSql = "INSERT INTO  stakeholder_type (stk_type_descr) VALUES('" . $this->$m_stk_type_descr . "')";
        //result
        $rsSql = mysql_query($strSql) or die("Error Addstk_type");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * EditStk_type
     * @return boolean
     */
    function EditStk_type() {
        $strSql = "UPDATE stakeholder_type SET stk_type_descr='" . $this->$m_stk_type_descr . "' WHERE stk_type_id=" . $this->m_stk_type_id;
        //result
        $rsSql = mysql_query($strSql) or die("Error Editstk_type");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteStk_type
     * @return boolean
     */
    function DeleteStk_type() {
        $strSql = "DELETE FROM  stakeholder_type WHERE stk_type_id=" . $this->m_stk_type_id;
        //result
        $rsSql = mysql_query($strSql) or die("Error Deletestk_type");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllstk_types
     * @return boolean
     */
    function GetAllstk_types() {
        $strSql = "SELECT stk_type_id,stk_type_descr FROM  stakeholder_type";
        //result
        $rsSql = mysql_query($strSql) or die("Error GetAllstk_types");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetStk_type_descrById
     * @return boolean
     */
    function GetStk_type_descrById() {
        $strSql = "SELECT stk_type_id,stk_type_descr FROM  stakeholder_type WHERE stk_type_id=" . $this->m_stk_type_id;
        //result
        $rsSql = mysql_query($strSql) or die("Error Getstk_typeById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>