<?php

/**
 * clsManageStatus
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsItemStatus {
    //npkId
    var $m_npkId;
    //PKItemStatusID
    var $m_PKItemStatusID;
    //ItemStatusName
    var $m_ItemStatusName;
/**
 * AddItemStatus
 * @return int
 */
    function AddItemStatus() {
        if ($this->m_ItemStatusName == '') {
            $this->m_ItemStatusName = 'NULL';
        }
        //add query
        $strSql = "INSERT INTO  tbl_product_status(ItemStatusName) VALUES('" . $this->m_ItemStatusName . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemStatus");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }
/**
 * EditItemStatus
 * @return boolean
 */
    function EditItemStatus() {
        //edit query
        $strSql = "UPDATE tbl_product_status SET PKItemStatusID=" . $this->m_npkId;

        $ItemStatusName = ",ItemStatusName='" . $this->m_ItemStatusName . "'";
        if ($this->m_ItemStatusName != '') {
            $strSql .=$ItemStatusName;
        }

        $strSql .=" WHERE PKItemStatusID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error EditItemStatus");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * DeleteItemStatus
 * @return boolean
 */
    function DeleteItemStatus() {
        //delete query
        $strSql = "DELETE FROM tbl_product_status WHERE PKItemStatusID=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error DeleteItemStatus");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllItemStatus
 * @return boolean
 */
    function GetAllItemStatus() {
        //select query
        $strSql = "SELECT
					tbl_product_status.PKItemStatusID,
					tbl_product_status.ItemStatusName
					FROM
					tbl_product_status";

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllManageItem");

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetItemStatusById
 * @return boolean
 */
    function GetItemStatusById() {
        //select query
        $strSql = "
				SELECT
					tbl_product_status.PKItemStatusID,
					tbl_product_status.ItemStatusName
					FROM
					tbl_product_status
					WHERE tbl_product_status.PKItemStatusID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemStatusById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
