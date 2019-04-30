<?php

/**
 * clsManageitem
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsManageItem {

    //npkid
    var $m_npkId;
    //npkid
    var $m_itm_id;
    //item id
    var $m_itm_name;
    //item name
    var $m_generic_name;
    //item type
    var $m_itm_type;
    //item unit
    var $m_itm_unit;
    //item category
    var $m_itm_category;
    //carton quantity
    var $m_qty_carton;
    //filed color
    var $m_field_color;
    //item des
    var $m_itm_des;
    //item status
    var $m_itm_status;
    //frm index
    var $m_frmindex;
    //extra
    var $m_extra;
    //stakeholder id
    var $m_stkid;
    //item
    var $m_item;
    //string
    var $string;
    //number
    var $number;

    /**
     * AddManageItem
     * @return type
     */
    function AddManageItem() {
        //check generic name
        if ($this->m_generic_name == '') {
            $this->m_generic_name = 'NULL';
        }
        if ($this->m_method_type == '') {
            $this->m_method_type = 'NULL';
        }
        //check item name
        if ($this->m_itm_name == '') {
            $this->m_itm_name = 'NULL';
        }
        //check item type
        if ($this->m_itm_type == '') {
            $this->m_itm_type = 'NULL';
        }
        //check item category
        if ($this->m_itm_category == '') {
            $this->m_itm_category = 'NULL';
        }
        //check qty carton
        if ($this->m_qty_carton == '') {
            $this->m_qty_carton = 0;
        }
        //check item status
        if ($this->m_itm_status == '') {
            $this->m_itm_status = 'NULL';
        }
        //check frm index
        if ($this->m_frmindex == '') {
            $this->m_frmindex = 0;
        }
        //insert query      
        $strSql = "INSERT INTO itminfo_tab (itm_name,generic_name,method_type,itm_type,item_unit_id,itm_category,qty_carton,itm_des,itm_status,frmindex) VALUES ('" . $this->m_itm_name . "','" . $this->m_generic_name . "','" . $this->m_method_type . "','" . $this->m_itm_type . "','" . $this->m_itm_unit . "','" . $this->m_itm_category . "'," . $this->m_qty_carton . ",'" . $this->m_itm_des . "','" . $this->m_itm_status . "','" . $this->m_frmindex . "')";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddManageItem");
        $number = mysql_insert_id();

        $string = 'IT-';
        $string.=str_pad($number, 3, '0', STR_PAD_LEFT);

        $strSQL1 = "update itminfo_tab set itmrec_id='" . $string . "' where itm_id='" . $number . "'";
        mysql_query($strSQL1);
        if ($number != 0) {
            return $number;
        }
    }

    /**
     * EditManageItem
     * @return boolean
     */
    function EditManageItem() {
        //update query
        $strSql = "UPDATE itminfo_tab SET itm_id=" . $this->m_npkId;

        $itm_name = ",itm_name='" . $this->m_itm_name . "'";
        //check item name
        if ($this->m_itm_name != '') {
            //set item name
            $strSql .=$itm_name;
        }
        //check generic name
        $m_generic_name = ",generic_name='" . $this->m_generic_name . "'";
        if ($this->m_generic_name != '') {
            //set generic name
            $strSql .=$m_generic_name;
        }

        $m_method_type = ",method_type='" . $this->m_method_type . "'";
        if ($this->m_method_type != '') {
            //set generic name
            $strSql .=$m_method_type;
        }

        $itm_type = ",itm_type='" . $this->m_itm_type . "'";
        //check item type
        if ($this->m_itm_type != '') {
            //set item type
            $strSql .=$itm_type;
        }

        $itm_unit = ",item_unit_id='" . $this->m_itm_unit . "'";
        //check item unit
        if ($this->m_itm_unit != '') {
            //set item unit
            $strSql .=$itm_unit;
        }

        $itm_category = ",itm_category='" . $this->m_itm_category . "'";
        //check item category
        if ($this->m_itm_category != '') {
            //set item category
            $strSql .=$itm_category;
        }

        $qty_carton = ",qty_carton=" . $this->m_qty_carton;
        //check qty carton
        if ($this->m_qty_carton != '') {
            //set qty carton
            $strSql .=$qty_carton;
        }

        $field_color = ",field_color='" . $this->m_field_color . "'";
        //check field color
        if ($this->m_field_color != '') {
           //set field color
            $strSql .=$field_color;
        }

        $itm_des = ",itm_des='" . $this->m_itm_des . "'";
        $strSql .=$itm_des;

        $itm_status = ",itm_status='" . $this->m_itm_status . "'";
//check item status
        if ($this->m_itm_status != '') {
        //set item status
            $strSql .=$itm_status;
        }

        $frmindex = ",frmindex=" . $this->m_frmindex;
        if ($this->m_frmindex != '') {
            $strSql .=$frmindex;
        }

        $extra = ",extra='" . $this->m_extra . "'";
        if ($this->m_extra != '') {
            $strSql .=$extra;
        }

        $strSql .=" WHERE itm_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error EditManageItem");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteManageItem
     * @return boolean
     */
    function DeleteManageItem() {
        //delete query
        $strSql = "DELETE FROM  itminfo_tab WHERE itm_id=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteManageItem");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllManageItem
     * @return boolean
     */
    function GetAllManageItem($category = '1,4,5') {
        //select query
        //gets
        //item rec id,
        //item id,
        //item name,
        //generic name,
        //item type,
        //item category,
        //qty carton,
        //field color,
        //item des,
        //item status,
        //frm index,
        //extra
        $strSql = "SELECT DISTINCT
						itminfo_tab.itmrec_id,
						itminfo_tab.itm_id,
						itminfo_tab.itm_name,
						itminfo_tab.generic_name,
						itminfo_tab.itm_type,
						itminfo_tab.itm_category,
						itminfo_tab.qty_carton,
						itminfo_tab.field_color,
						itminfo_tab.itm_des,
						itminfo_tab.itm_status,
						itminfo_tab.frmindex,
						itminfo_tab.extra,
						itminfo_tab.item_unit_id
					FROM
						itminfo_tab
					INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
					WHERE
                                                stakeholder_item.stkid = " . $_SESSION['user_stakeholder1'] . "
						AND itminfo_tab.itm_category IN ($category)
					ORDER BY
                                                itminfo_tab.itm_category,
						itminfo_tab.frmindex";
        //query result
//        echo $strSql;
        $rsSql = mysql_query($strSql) or die("Error GetAllManageItem");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetManageItemById
     * @return boolean
     */
    function GetManageItemById() {
        
        $strSql = "SELECT
					itminfo_tab.itm_id,
					itminfo_tab.itm_name,
					itminfo_tab.generic_name,
					itminfo_tab.itm_type,
					itminfo_tab.item_unit_id,
					itminfo_tab.itm_category,
					itminfo_tab.qty_carton,
					itminfo_tab.itm_des,
					itminfo_tab.itm_status,
					itminfo_tab.frmindex,
					itminfo_tab.extra,
					stakeholder_item.stkid,
					stakeholder_item.stk_item,
					itemsofgroups.ItemID,
					itemsofgroups.GroupID
				FROM
					itminfo_tab
				LEFT JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				LEFT JOIN itemsofgroups ON itemsofgroups.ItemID = itminfo_tab.itm_id 
				WHERE itminfo_tab.itm_id=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetManageItemById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllWHProduct
     * @return boolean
     */
    function GetAllWHProduct() {
        $strSql = "SELECT DISTINCT
		itminfo_tab.itm_id,
		itminfo_tab.itm_name
		FROM
		itminfo_tab
		INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
		INNER JOIN stock_batch ON stock_batch.item_id = itminfo_tab.itm_id
		WHERE
		stock_batch.wh_id = " . $_SESSION['user_warehouse'] . " 
		AND stock_batch.status = 'Running' AND stock_batch.Qty > 0
		ORDER BY
		itminfo_tab.frmindex ASC";
        
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllWHProduct");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                $array[] = array(
                    'id' => $row->itm_id,
                    'name' => $row->itm_name
                );
            }
            return $array;
        } else {
            return false;
        }
    }

    /**
     * GetAllProduct
     * @return boolean
     */
    function GetAllProduct() {
        $strSql = "SELECT
		itminfo_tab.itm_id,
		itminfo_tab.itm_name
		FROM
		itminfo_tab
		INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
		WHERE
		stakeholder_item.stkid = 1
		ORDER BY
		itminfo_tab.frmindex ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllProduct");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                $array[] = array(
                    'id' => $row->itm_id,
                    'name' => $row->itm_name
                );
            }
            return $array;
        } else {
            return false;
        }
    }
    
    
    /**
     * GetAllProduct_of_stk
     * @return boolean
     */
    function GetAllProduct_of_stk($stk = 1) {
        $strSql = "SELECT
		itminfo_tab.itm_id,
		itminfo_tab.itm_name
		FROM
		itminfo_tab
		INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
		WHERE
		stakeholder_item.stkid = $stk
		ORDER BY
		itminfo_tab.frmindex ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllProduct");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return false;
        }
    }
    /**
     * GetProductName
     * @param type $item_id
     * @return boolean
     */
    function GetProductName($item_id) {
        $strSql = "SELECT
        itminfo_tab.itm_name
        FROM
        itminfo_tab
        WHERE
        itminfo_tab.itm_id = $item_id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProductCat");
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->itm_name;
        } else {
            return false;
        }
    }

    /**
     * GetProductCat
     * @param type $item_id
     * @return boolean
     */
    function GetProductCat($item_id) {
        $strSql = "SELECT
						tbl_product_category.ItemCategoryName
					FROM
						itminfo_tab
					INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
					WHERE
						itminfo_tab.itm_id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProductCat");
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->ItemCategoryName;
        } else {
            return false;
        }
    }

    /**
     * GetProductDoses
     * @param type $product
     * @return boolean
     */
    function GetProductDoses($product) {
        $strSql = "SELECT
					itminfo_tab.itm_type
				FROM
					itminfo_tab
				WHERE
					itminfo_tab.itm_id = " . $product;
//query result
        $rsSql = mysql_query($strSql) or die("Error GetManageItemById");

        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->itm_type;
        } else {
            return false;
        }
    }

}

?>
