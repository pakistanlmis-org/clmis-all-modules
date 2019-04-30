<?php

/**
 * clsIstakeholder_item
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsstakeholderitem {

    //npkId
    var $m_npkId;
    //stk_id
    var $m_stk_id;
    //stakeholder id
    var $m_stkid;
    //stakeholder item
    var $m_stk_item;
    //type
    var $m_type;
    //item id
    var $m_itm_id;
    //item name
    var $m_itm_name;
    //stakeholder name
    var $m_stkname;
    //stakeholder_n
    var $stk_n;
    //no
    var $no;
    //brand name
    var $brand_name;
    //quantity_per_pack
    var $quantity_per_pack;
    var $carton_per_pallet;
    //gtin
    var $gtin;
    //rank
    var $rank;
    //gross capacity
    var $gross_capacity;
    //net capacity
    var $net_capacity;
    //pack length
    var $pack_length;
    //pack width
    var $pack_width;
    //pack height
    var $pack_height;

    /**
     * Add stakeholder item
     * @return boolean
     */
    function Addstakeholderitem() {
        if ($this->m_stkid == '') {
            $this->m_stkid = 0;
        }
        if ($this->m_stk_item == '') {
            $this->m_stk_item = 0;
        }
        $stk_n = count($this->m_stkid);
        $no = 0;
        while ($stk_n != 0) {
            //echo '<pre>';print_r($this);exit;
            //add query
            $strSql = "INSERT INTO stakeholder_item(stkid,stk_item) VALUES(" . $this->m_stkid[$no] . "," . $this->m_stk_item . ")";
            //query result
            //echo $strSql;exit;
            $rsSql = mysql_query($strSql) or die("Error Addstakeholderitem");
            $no++;
            $stk_n--;
        }
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Addstakeholderitem1
     * @return boolean
     */
    function Addstakeholderitem1() {
        //add query
        $strSql = "                                             INSERT INTO stakeholder_item ";
	$strSql .= "                                            SET  ";
	$strSql .= "                                            stkid = '" . $this->m_stkid . "' ";
	$strSql .= "                                            ,stk_item = '" . $this->m_stk_item . "' ";
	$strSql .= "                                            ,brand_name = '" . $this->brand_name . "'  ";
	if(!empty($this->quantity_per_pack)) $strSql .= "	,quantity_per_pack = '" . $this->quantity_per_pack . "' ";
	if(!empty($this->carton_per_pallet)) $strSql .= "	,carton_per_pallet = '" . $this->carton_per_pallet . "' ";
	if(!empty($this->gtin)) $strSql .= "			,gtin = '" . $this->gtin . "' ";
	if(!empty($this->gross_capacity)) $strSql .= "		,gross_capacity = '" . $this->gross_capacity . "' ";
	if(!empty($this->net_capacity)) $strSql .= "		,net_capacity = '" . $this->net_capacity . "' ";
	if(!empty($this->pack_length)) $strSql .= "		,pack_length = '" . $this->pack_length . "' ";
	if(!empty($this->pack_width)) $strSql .= "		,pack_width = '" . $this->pack_width . "' ";
	if(!empty($this->pack_height)) $strSql .= "		,pack_height = '" . $this->pack_height . "' ";
        //query result
        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error Addstakeholderitem");
        if (mysql_affected_rows()) {
            return mysql_insert_id();
        } else {
            return FALSE;
        }
    }

    /**
     * Addstakeholderitem2
     * @return boolean
     */
    function Addstakeholderitem2() {
        //add query
        $strSql = "INSERT INTO stakeholder_item
				SET
					stkid = '" . $this->m_stkid . "',
					stk_item = '" . $this->m_stk_item . "',
					brand_name = '" . $this->brand_name . "',
					carton_per_pallet = '" . $this->carton_per_pallet . "',
					quantity_per_pack = '" . $this->quantity_per_pack . "',
					gtin = '" . $this->gtin . "',
					gross_capacity = '" . $this->gross_capacity . "',
					net_capacity = '" . $this->net_capacity . "',
					pack_length = '" . $this->pack_length . "',
					pack_width = '" . $this->pack_width . "',
					pack_height = '" . $this->pack_height . "' ";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Addstakeholderitem");
        if (mysql_affected_rows()) {
            return mysql_insert_id();
        } else {
            return FALSE;
        }
    }

    /**
 * Updatestakeholderitem2
 * @return boolean
 */
    function UpdateStakeholderItem() {
        //add query
        $strSql = "Update stakeholder_item
				SET
					stkid = '" . $this->m_stkid . "',
					stk_item = '" . $this->m_stk_item . "',
					brand_name = '" . $this->brand_name . "',
					carton_per_pallet = '" . $this->carton_per_pallet . "',
					quantity_per_pack = '" . $this->quantity_per_pack . "',
					gtin = '" . $this->gtin . "',
					gross_capacity = '" . $this->gross_capacity . "',
					net_capacity = '" . $this->net_capacity . "',
					pack_length = '" . $this->pack_length . "',
					pack_width = '" . $this->pack_width . "',
					pack_height = '" . $this->pack_height . "' 
					WHERE stk_id = $this->m_npkId";
        //query result
        $rsSql = mysql_query($strSql) or die("Error UpdateStakeholderItem");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    

    /**
     * Editstkholderitem
     * @return boolean
     */
    function Editstkholderitem() {
        //edit query
        $strSql = "DELETE FROM  stakeholder_item WHERE stk_item=" . $this->m_stk_item;
        $rsSql = mysql_query($strSql) or die("Error Deletestakeholderitem");
        if ($this->m_stkid == '') {
            $this->m_stkid = 0;
        }
        if ($this->m_stk_item == '') {
            $this->m_stk_item = 0;
        }

        $stk_n = count($this->m_stkid);
        $no = 0;
        while ($stk_n != 0) {
            $strSql = "INSERT INTO stakeholder_item(stkid,stk_item) VALUES(" . $this->m_stkid[$no] . "," . $this->m_stk_item . ")";
            //query result
            $rsSql = mysql_query($strSql) or die("Error Addstakeholderitem");
            $no++;
            $stk_n--;
        }
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Editstakeholderitem
     * 
     * @return boolean
     */
    function Editstakeholderitem() {
        //edit query
        $strSql = "UPDATE stakeholder_item SET stk_id=" . $this->m_npkId;
        $stkid = ",stkid='" . $this->m_stkid . "'";
        if ($this->m_stkid != '') {
            $strSql .=$stkid;
        }
        $stk_item = ",stk_item='" . $this->m_stk_item . "'";

        if ($this->m_stk_item != '') {
            $strSql .=$stk_item;
        }
        $type = ",type='" . $this->m_type . "'";
        if ($this->m_type != '') {
            $strSql .=$type;
        }

        $strSql .=" WHERE stk_id=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error stakeholderitem");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * Deletestakeholderitem
     * @return boolean
     */
    function Deletestakeholderitem() {
        $strSql = "DELETE FROM  stakeholder_item WHERE stkid=" . $this->m_stk_id;
//query result
        $rsSql = mysql_query($strSql) or die("Error Deletestakeholderitem");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Deletestkholderitem
     */
    function Deletestkholderitem() {
        $strSql = "DELETE FROM  stakeholder_item WHERE stk_item=" . $this->m_stk_item;
        $rsSql = mysql_query($strSql) or die("Error Deletestakeholderitem");
    }

    /**
     * GetAllstakeholderitem
     * @return boolean
     */
    function GetAllstakeholderitem() {
//select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder item
        //item name
        //item type
        $strSql = "SELECT
				stakeholder_item.stk_id,
				stakeholder_item.stkid,
				stakeholder.stkname,
				stakeholder_item.stk_item,
				itminfo_tab.itm_name,
				stakeholder_item.type
				FROM
				stakeholder_item
				Left Join stakeholder ON stakeholder.stkid = stakeholder_item.stkid
				Left Join itminfo_tab ON itminfo_tab.itm_id = stakeholder_item.stk_item";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllstakeholderitem");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllstakeholder
     * @return boolean
     */
    function GetAllstakeholder() {
        $strSql = "SELECT DISTINCT stakeholder_item.stkid,stakeholder.stkname
				FROM
				stakeholder_item
				inner Join stakeholder ON stakeholder.stkid = stakeholder_item.stkid
				WHERE stakeholder.ParentID is null ORDER BY stakeholder.stkname DESC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllstakeholderitem");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetstakeholderitemById
     * @return boolean
     */
    function GetstakeholderitemById() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder item
        //item name
        //item type
        $strSql = "SELECT
						stakeholder_item.stk_id,
						stakeholder_item.stkid,
						stakeholder.stkname,
						stakeholder_item.stk_item,
						itminfo_tab.itm_name,
						stakeholder_item.type
						FROM
						stakeholder_item
						Left Join stakeholder ON stakeholder.stkid = stakeholder_item.stkid
						Left Join itminfo_tab ON itminfo_tab.itm_id = stakeholder_item.stk_item
						WHERE stakeholder_item.stkid=" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error GetstakeholderitemById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetstakeholderitemById
     * @return boolean
     */
    
    function get_manufacturer_by_id($manufacturer_id) {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder item
        //item name
        //item type
        $strSql = "SELECT
                        stakeholder.stkname,
                        stakeholder_item.stk_item,
                        itminfo_tab.itm_name,
                        stakeholder_item.type,
                        stakeholder_item.brand_name
                        FROM
                        stakeholder_item
                        Left Join stakeholder ON stakeholder.stkid = stakeholder_item.stkid
                        Left Join itminfo_tab ON itminfo_tab.itm_id = stakeholder_item.stk_item
                        WHERE stakeholder_item.stk_id=" . $manufacturer_id;
//query result
       //echo $strSql;
        $rsSql = mysql_query($strSql) or die("Error GetstakeholderitemById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    
    /**
     * GetstakeholderItemsById
     * @return boolean
     */
    function GetstakeholderItemsById() {
        //select query
        //stakeholder id
        //item name
        //item id
        $strSql = "
				SELECT
					stakeholder_item.stk_id,
					itminfo_tab.itm_name,
					itminfo_tab.itm_id
				FROM
					stakeholder_item
					Left Join itminfo_tab ON itminfo_tab.itm_id = stakeholder_item.stk_item
				Where itminfo_tab.itm_category = 1 AND stakeholder_item.stkid=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetstakeholderItemsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetIstakeholderinCSV
     * @return type
     */
    function GetIstakeholderinCSV() {
        $csv = "";
        //Get stakeholder Items By Id
        $objMIs = $this->GetstakeholderItemsById();
        if ($objMIs != FALSE && mysql_num_rows($objMIs) > 0) {
            while ($Rowranks = mysql_fetch_object($objMIs)) {
                if (strlen($Rowranks->itm_name) > 0) {
                    $csv.=$Rowranks->itm_name . ",";
                }
            }
        }
        if (strlen($csv) > 0) {
            $csv = substr($csv, 0, strlen($csv) - 1);
        }
        return $csv;
    }

}

?>
