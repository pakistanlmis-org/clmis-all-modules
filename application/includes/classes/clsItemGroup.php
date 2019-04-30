<?php

/**
 * clsItemGroup
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsItemGroup {

    var $m_npkId;
    var $m_PKItemGroupID;
    var $m_ItemGroupName;

    /**
     * AddItemGroup
     * 
     * @return int
     */
    function AddItemGroup() {
        if ($this->m_ItemGroupName == '') {
            $this->m_ItemGroupName = 'NULL';
        }
        //add query
        $strSql = "INSERT INTO  itemgroups(ItemGroupName) VALUES('" . $this->m_ItemGroupName . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemGroup");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * EditItemGroup
     * 
     * @return boolean
     */
    function EditItemGroup() {
        //edit query
        $strSql = "UPDATE itemgroups SET PKItemGroupID=" . $this->m_npkId;
        $ItemGroupName = ",ItemGroupName='" . $this->m_ItemGroupName . "'";
        if ($this->m_ItemGroupName != '') {
            $strSql .=$ItemGroupName;
        }
        //query result
        $strSql .=" WHERE PKItemGroupID=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error EditItemGroup");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteItemGroup
     * 
     * @return boolean
     */
    function DeleteItemGroup() {
        //delete query
        $strSql = "DELETE FROM  itemgroups WHERE PKItemGroupID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemGroup");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllItemGroup
     * 
     * @return boolean
     */
    function GetAllItemGroup() {
        //all item query
        $strSql = "
				SELECT
					itemgroups.PKItemGroupID,
					itemgroups.ItemGroupName
					FROM
					itemgroups";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllManageItem");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemGroupById
     * 
     * @return boolean
     */
    function GetItemGroupById() {
        //item group by id query
        $strSql = "
				SELECT
					itemgroups.PKItemGroupID,
					itemgroups.ItemGroupName
					FROM
					itemgroups
					WHERE itemgroups.PKItemGroupID=" . $this->m_npkId;

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemGroupById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
