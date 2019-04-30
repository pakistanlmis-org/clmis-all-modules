<?php

/**
 * clsItemofGroup
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsItemOfGroup {

    //npkId
    var $m_npkId;
    //pk Items of Groups ID
    var $m_pkItemsofGroupsID;
    //item id
    var $m_ItemID;
    //group id
    var $m_GroupID;
    //group
    var $n_group;
    //no
    var $no;

    /**
     * AddItemOfGroup
     * @return boolean
     */
    function AddItemOfGroup() {
        if ($this->m_ItemID == '') {
            $this->m_ItemID = 0;
        }
        if ($this->m_GroupID == '') {
            $this->m_GroupID = 0;
        }
        //add query
        $strSql = "INSERT INTO itemsofgroups(ItemID,GroupID) VALUES(" . $this->m_ItemID . "," . $this->m_GroupID . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemOfGroup1");
        $n_group--;

        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * AddItemOfGroup1
     * @return boolean
     */
    function AddItemOfGroup1() {
        if ($this->m_ItemID == '') {
            $this->m_ItemID = 0;
        }
        if ($this->m_GroupID == '') {
            $this->m_GroupID = 0;
        }

        $n_group = count($this->m_GroupID);

        $no = 0;

        while ($n_group != 0) {
            //add query
            $strSql = "INSERT INTO itemsofgroups(ItemID,GroupID) VALUES(" . $this->m_ItemID . "," . $this->m_GroupID[$no] . ")";
            //query result
            $rsSql = mysql_query($strSql) or die("Error AddItemOfGroup1");
            $n_group--;
            $no++;
        }
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * EditItemGroup
     * @return boolean
     */
    function EditItemGroup() {
        //delete query
        $strSql = "DELETE FROM  itemsofgroups WHERE ItemID=" . $this->m_ItemID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemOfGroup1");

        if ($this->m_ItemID == '') {
            $this->m_ItemID = 0;
        }
        if ($this->m_GroupID == '') {
            $this->m_GroupID = 0;
        }

        $n_group = count($this->m_GroupID);

        $no = 0;

        while ($n_group != 0) {
            //insert query
            $strSql = "INSERT INTO itemsofgroups(ItemID,GroupID) VALUES(" . $this->m_ItemID . "," . $this->m_GroupID[$no] . ")";
            //query result
            $rsSql = mysql_query($strSql) or die("Error AddItemOfGroup1");
            $n_group--;
            $no++;
        }
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * EditItemOfGroup
     * @return boolean
     */
    function EditItemOfGroup() {
        //update query
        $strSql = "UPDATE itemsofgroups SET pkItemsofGroupsID=" . $this->m_npkId;
        $ItemID = ",ItemID='" . $this->m_ItemID . "'";
        if ($this->m_ItemID != '') {
            $strSql .=$ItemID;
        }

        $GroupID = ",GroupID='" . $this->m_GroupID . "'";
        if ($this->m_GroupID != '') {
            $strSql .=$GroupID;
        }

        $strSql .=" WHERE pkItemsofGroupsID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemOfGroup");
        $rsSql = mysql_query($strSql) or die("Error EditItemOfGroup");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteItemGroup
     */
    function DeleteItemGroup() {
        //delete query
        $strSql = "DELETE FROM  itemsofgroups WHERE ItemID=" . $this->m_ItemID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemOfGroup");
    }

    /**
     * DeleteItemOfGroup
     * @return boolean
     */
    function DeleteItemOfGroup() {
        //delete query
        $strSql = "DELETE FROM  itemsofgroups WHERE GroupID=" . $this->m_GroupID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemOfGroup");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteGroup
     * @return boolean
     */
    function DeleteGroup() {
        //delete query
        $strSql = "DELETE FROM  itemgroups WHERE PKItemGroupID=" . $this->m_GroupID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemOfGroup1");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllItemOfGroup
     * @return boolean
     */
    function GetAllItemOfGroup() {
        //select query
        //gets
        //pk item group id
        //item grou name
        //item id
        //item name
        $strSql = "SELECT
				itemgroups.PKItemGroupID,
				itemgroups.ItemGroupName,
				itemsofgroups.ItemID,
				itminfo_tab.itm_name
				FROM
				itemgroups
				Left Join itemsofgroups ON itemgroups.PKItemGroupID = itemsofgroups.GroupID
				Left Join itminfo_tab ON itemsofgroups.ItemID = itminfo_tab.itm_id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllItemOfGroup");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemOfGroupById
     * @return boolean
     */
    function GetItemOfGroupById() {
        //select query
        //gets
        //pk item group id 
        //item group name
        //item id
        //item name
        $strSql = "
				SELECT
				itemgroups.PKItemGroupID,
				itemgroups.ItemGroupName,
				itemsofgroups.ItemID,
				itminfo_tab.itm_name
				FROM
				itemgroups
				Left Join itemsofgroups ON itemgroups.PKItemGroupID = itemsofgroups.GroupID
				Left Join itminfo_tab ON itemsofgroups.ItemID = itminfo_tab.itm_id
				Where itemgroups.PKItemGroupID=" . $this->m_npkId;

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemOfGroupById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemsOfGroupById
     * @return boolean
     */
    function GetItemsOfGroupById() {
        //select query
        //gets
        //item name
        $strSql = "
				SELECT
				itemsofgroups.ItemID,itminfo_tab.itm_name
				FROM
				itemsofgroups 
				left Join itminfo_tab ON itemsofgroups.ItemID = itminfo_tab.itm_id
				Where itemsofgroups.GroupID IN (" . $this->m_npkId .")";

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemsOfGroupById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemsofGroupinCSV
     * @return type
     * 
     */
    function GetItemsofGroupinCSV() {
        $csv = "";
        $objMIs = $this->GetItemsOfGroupById();
        if ($objMIs != FALSE && mysql_num_rows($objMIs) > 0) {
            while ($Rowranks = mysql_fetch_object($objMIs)) {
                $csv.=$Rowranks->itm_name . ",";
            }
        }
        if (strlen($csv) > 0) {
            $csv = substr($csv, 0, strlen($csv) - 1);
        }
        return $csv;
    }

}

?>
