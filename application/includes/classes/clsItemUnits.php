<?php

/**
 * clsItemUnits
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsItemUnits {

    //npkId
    public $m_npkId;
    //unit_type
    public $m_unit_type;

    /**
     * AddItemUnit
     * 
     * @return type
     */
    function AddItemUnit() {
        if ($this->m_unit_type == '') {
            $this->m_unit_type = 'NULL';
        }
        //add query
        $strSql = "INSERT INTO tbl_itemunits (UnitType) VALUES ('" . $this->m_unit_type . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemUnit");
        $number = mysql_insert_id();

        if ($number != 0) {
            return $number;
        }
    }

    /**
     * EditItemUnit
     * 
     * @return boolean
     */
    function EditItemUnit() {
        //edit query
        $strSql = "UPDATE tbl_itemunits SET UnitType='" . $this->m_unit_type . "' ";
        $strSql .=" WHERE pkUnitID=" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error EditItemUnit");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteItemUnit
     * 
     * @return boolean
     */
    function DeleteItemUnit() {
        //delete query
        $strSql = "DELETE FROM  tbl_itemunits WHERE pkUnitID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemUnit");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllItemUnits
     * 
     * @return boolean
     */
    function GetAllItemUnits() {
        $strSql = "SELECT * FROM	tbl_itemunits";
        //query result
        $rsSql = mysql_query($strSql) or die("Error: GetAllItemUnits");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemUnitById
     * 
     * @return boolean
     */
    function GetItemUnitById() {
        $strSql = "SELECT
			tbl_itemunits.UnitType
			FROM
			tbl_itemunits
			WHERE tbl_itemunits.pkUnitID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error: GetItemUnitById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetUnitByItemId
     * 
     * @param type $item_id
     * @return boolean
     */
    function GetUnitByItemId($item_id) {
        $strSql = "SELECT
			tbl_itemunits.pkUnitID,
			tbl_itemunits.UnitType
			FROM
			tbl_itemunits
			INNER JOIN itminfo_tab ON itminfo_tab.itm_type = tbl_itemunits.UnitType
			WHERE
			itminfo_tab.itm_id = " . $item_id . "";
        //query result
        $rsSql = mysql_query($strSql) or die("Error: GetUnitByItemId");
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return array(
                'id' => $row->pkUnitID,
                'type' => $row->UnitType
            );
        } else {
            return FALSE;
        }
    }

}

?>
