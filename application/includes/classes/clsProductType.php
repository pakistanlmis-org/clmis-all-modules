<?php

/**
 * clsProductType
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//class clsItemType
class clsItemType {
    //npkId
    var $m_npkId;
    //PKItemTypeID
    var $m_PKItemTypeID;
    //ItemTypeName
    var $m_ItemTypeName;
/**
 * AddItemType
 * @return int
 */
    function AddItemType() {
        if ($this->m_ItemTypeName == '') {
            $this->m_ItemTypeName = 'NULL';
        }
        //add query 
        $strSql = "INSERT INTO  tbl_product_type(ItemTypeName) VALUES('" . $this->m_ItemTypeName . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemType");
        if (mysql_insert_id() > 0) { {
                return mysql_insert_id();
            }
        } else { {
                return 0;
            }
        }
    }
/**
 * EditItemType
 * @return boolean
 */
    function EditItemType() {
        //edit query 
        $strSql = "UPDATE tbl_product_type SET PKItemTypeID=" . $this->m_npkId;
        $ItemTypeName = ",ItemTypeName='" . $this->m_ItemTypeName . "'";
        if ($this->m_ItemTypeName != '') {
            $strSql .=$ItemTypeName;
        }

        $strSql .=" WHERE PKItemTypeID=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error EditItemType");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * DeleteItemType
 * @return boolean
 */
    function DeleteItemType() {
        //delete query 
        $strSql = "DELETE FROM tbl_product_type WHERE PKItemTypeID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemType");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllItemType
 * @return boolean
 */
    function GetAllItemType() {
        //select query
        $strSql = "SELECT
					tbl_product_type.PKItemTypeID,
					tbl_product_type.ItemTypeName
					FROM
					tbl_product_type";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllManageItem");

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetItemTypeById
 * @return boolean
 */
    function GetItemTypeById() {
        $strSql = "
				SELECT
					tbl_product_type.PKItemTypeID,
					tbl_product_type.ItemTypeName
					FROM
					tbl_product_type
					WHERE tbl_product_type.PKItemTypeID=" . $this->m_npkId;

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemTypeById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
