<?php

/**
 * clsStakeholderType
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsStakeholderType {

    //npkId
    var $m_npkId;
    //StakeholderTypeID
    var $m_StakeholderTypeID;
    //StakeholderTypeDescription
    var $m_StakeholderTypeDescription;

    /**
     * AddStakeholderType
     * @return int
     */
    function AddStakeholderType() {
        if ($this->m_StakeholderTypeDescription == '') {
            $this->m_StakeholderTypeDescription = 'NULL';
        }
        //add query
        $strSql = "INSERT INTO stakeholder_type (stk_type_id, stk_type_descr)SELECT MAX(stakeholder_type.stk_type_id) + 1 AS counter, '$this->m_StakeholderTypeDescription' FROM stakeholder_type";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddStakeholderType");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * EditStakeholderType
     * @return boolean
     */
    function EditStakeholderType() {
        //edit query
        $strSql = "UPDATE stakeholder_type SET stk_type_id=" . $this->m_npkId;
        $StakeholderTypeName = ",stk_type_descr='" . $this->m_StakeholderTypeDescription . "'";
        if ($this->m_StakeholderTypeDescription != '') {
            $strSql .=$StakeholderTypeName;
        }

        $strSql .=" WHERE stk_type_id=" . $this->m_npkId;

        $rsSql = mysql_query($strSql) or die("Error EditStakeholderType");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteStakeholderType
     * @return boolean
     */
    function DeleteStakeholderType() {
        //delete query
        $strSql = "DELETE FROM  stakeholder_type WHERE stk_type_id=" . $this->m_npkId;

        $rsSql = mysql_query($strSql) or die("Error StakeholderType");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllItStakeholderType
     * @return boolean
     */
    function GetAllItStakeholderType() {

        $strSql = " SELECT
                        stakeholder_type.stk_type_id,
                        stakeholder_type.stk_type_descr
                    FROM
                        stakeholder_type";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllItStakeholderType");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetStakeholderTypeById
     * @return boolean
     */
    function GetStakeholderTypeById() {

        $strSql = " SELECT
                        stakeholder_type.stk_type_id,
                        stakeholder_type.stk_type_descr
                    FROM
                        stakeholder_type
                    WHERE
                        stakeholder_type.stk_type_id =" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error GetStakeholderTypeById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
