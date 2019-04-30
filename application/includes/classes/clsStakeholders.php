<?php

/**
 * clsStakeholders
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsStakeholder {

    //npkid
    var $m_npkId;
    //stakeholder name
    var $m_stkname;
    //stakeholder order
    var $m_stkorder;
    //parent id
    var $m_ParentID;
    //stakeholder type id
    var $m_stk_type_id;
    //level
    var $m_lvl;
    //main stakeholder
    var $m_MainStakeholder;

    /**
     * 
     * AddStakeholder
     * @return int
     * 
     * 
     */
    function AddStakeholder() {
        //check stakeholder name
        if ($this->m_stkname == '') {
            $this->m_stkname = 'NULL';
        }
        //check stakeholder order
        if ($this->m_stkorder == '') {
            $this->m_stkorder = 'NULL';
        }
        //check parent id
        if ($this->m_ParentID == '') {
            $this->m_ParentID = 'NULL';
        }
        //check stakeholder type id
        if ($this->m_stk_type_id == '') {
            $this->m_stk_type_id = 0;
        }
        //check level
        if ($this->m_lvl == '') {
            $this->m_lvl = 1;
        }
        //main stakeholder
        if ($this->m_MainStakeholder == '') {
            $this->m_MainStakeholder = 'NULL';
        }

        //insert query
        //inserts
        //stkname,
        //stkorder,
        //ParentID,
        //stk_type_id,
        //lvl,
        //MainStakeholder
        $strSql = "INSERT INTO  stakeholder (stkname,stkorder,ParentID,stk_type_id,lvl,MainStakeholder) VALUES('" . $this->m_stkname . "'," . $this->m_stkorder . "," . $this->m_ParentID . "," . $this->m_stk_type_id . "," . $this->m_lvl . "," . $this->m_MainStakeholder . ")";

        $rsSql = mysql_query($strSql) or die("Error AddStakeholder");

        $id = mysql_insert_id();
        if ($id > 0) {
            if ($this->m_MainStakeholder == 'NULL') {
                $strSql1 = "Update stakeholder SET MainStakeholder=" . $id . " where stkid=" . $id;
                $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
            }
            return $id;
        } else {
            return 0;
        }
    }

    /**
     * 
     * EditStakeholder
     * @return boolean
     * 
     * 
     */
    function EditStakeholder() {
        $strSql = "UPDATE stakeholder SET stkid=" . $this->m_npkId;
        $stkname = ",stkname='" . $this->m_stkname . "'";
        if ($this->m_stkname != '') {
            $strSql .=$stkname;
        }
        //stakeholder order
        $stkorder = ",stkorder=" . $this->m_stkorder;
        if ($this->m_stkorder != '') {
            $strSql .=$stkorder;
        }
        //parent id
        $ParentID = ",ParentID=" . $this->m_ParentID;
        if ($this->m_ParentID != '') {
            $strSql .=$ParentID;
        }

        if (isset($this->m_stk_type_id) && !empty($this->m_stk_type_id)) {
            $stk_type_id = ",stk_type_id=" . $this->m_stk_type_id;
            $strSql .=$stk_type_id;
        }

        $lvl = ",lvl=" . $this->m_lvl;
        if ($this->m_lvl != '') {
            $strSql .=$lvl;
        }

        $MainStakeholder = ",MainStakeholder=" . $this->m_MainStakeholder;
        if ($this->m_MainStakeholder != '') {
            $strSql .=$MainStakeholder;
        }

        $strSql .=" WHERE stkid=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error EditStakeholder");
        if (mysql_affected_rows()) { {
                return $rsSql;
            }
        } else { {
                return FALSE;
            }
        }
    }

    /**
     * 
     * DeleteStakeholder
     * @return boolean
     * 
     * 
     */
    function DeleteStakeholder() {
        $strSql = "DELETE FROM  stakeholder WHERE stkid=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error DeleteStakeholder");
        if (mysql_affected_rows()) { {
                return TRUE;
            }
        } else { {
                return FALSE;
            }
        }
    }

    /**
     *
     * DeleteComments
     * @return boolean
     *
     */
    function DeleteComments(){
        $strSql = "DELETE FROM dashboard_comments WHERE pk_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error DeleteComments");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function DeleteManufacturer() {
        $strSql = "DELETE FROM stakeholder_item WHERE stk_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error DeleteManufacturer");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllStakeholders
     * @return boolean
     * 
     * 
     */
    function GetAllStakeholders() {
        //select query
        //gets
        //Stakeholder id
        //province id
         $qry = "SELECT
					user_stk.stk_id
				FROM
					user_stk
				WHERE
					user_stk.user_id = " . $_SESSION['user_id'];
        //query result
        $qryRes = mysql_query($qry);
        $arr = array();
        while ($row = mysql_fetch_array($qryRes)) {
            if (!in_array($row['stk_id'], $arr)) {
                $arr[] = $row['stk_id'];
            }
        }
        $and = (!empty($arr)) ? " AND stakeholder.stkid  IN (" . implode(',', $arr) . ") " : '';
        //select query
        //gets
        //stakeholder id,
        //stakeholder name,
        //stakeholder order,
        //stakeholder_type_descr,
        //Parent,
        //ParentID,
        //stakeholder_type_id,
        //level,
        //level_name,
        //MainStakeholder
        $strSql = "SELECT
						stakeholder.stkid,
						stakeholder.stkname,
						stakeholder.stkorder,
						stakeholder_type.stk_type_descr,
						IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
						stakeholder.ParentID,
						stakeholder.stk_type_id,
						stakeholder.lvl,
						tbl_dist_levels.lvl_name,
						stakeholder.MainStakeholder
					FROM
						stakeholder
					LEFT Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
					INNER Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
					LEFT Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
					WHERE stakeholder.ParentID is null
					AND stakeholder.stk_type_id IN (0, 1)
					$and
					ORDER BY
						stakeholder.stkorder ASC";
        //query result
        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetAllStakeholdersttttttttt");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStakeholdersByUserId
     * @param type $stkIds
     * @return boolean
     * 
     * 
     */
    function GetStakeholdersByUserId($stkIds) {
        //select query 
        //gets
        //stakeholder.stkid,
        //stakeholder name,
        //stakeholder order,
        //stakeholde type descr,
        //Parent,
        //ParentID,
        //stakeholder type id,
        //level,
        //level name,
        //MainStakeholder
        $strSql = "SELECT
						stakeholder.stkid,
						stakeholder.stkname,
						stakeholder.stkorder,
						stakeholder_type.stk_type_descr,
						IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
						stakeholder.ParentID,
						stakeholder.stk_type_id,
						stakeholder.lvl,
						tbl_dist_levels.lvl_name,
						stakeholder.MainStakeholder
						FROM
						stakeholder
						Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
						Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
						Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
						where stakeholder.ParentID is null AND stakeholder.stkid IN (" . $stkIds . ")
						ORDER BY stakeholder.stkid";
        //query result
//        echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetAllStakeholdersttttttttt");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStakeholdersLowersLevels
     * @return boolean
     * 
     * 
     */
    function GetStakeholdersLowersLevels() {
        if ($this->m_npkId != '') {
            $whereclause = "WHERE stakeholder.stkid=" . $this->m_npkId;
        } else {
            $whereclause = '';
        }

        $strSql = "SELECT lvl_id,lvl_name from tbl_dist_levels where tbl_dist_levels.lvl_id > (
					SELECT ifnull(max(lvl),0) as toplvl FROM stakeholder " . $whereclause . ")";
        $rsSql = mysql_query($strSql) or die("Error GetStakeholdersLowersLevels");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllLevels
     * @return boolean
     * 
     * 
     */
    function GetAllLevels() {
        $strSql = "SELECT lvl_id,lvl_name from tbl_dist_levels";
        $rsSql = mysql_query($strSql) or die("Error GetAllLevels");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllStakeholdersOfficeTypes
     * @return boolean
     * 
     * 
     * 
     */
    function GetAllStakeholdersOfficeTypes() {
        //select query
        //gets
        //stakeholder.stkid,
					//stakeholder name,
					//stakeholder order,
					//stakeholder type descr,
					//Parent,
					//ParentID,
					//stakeholder type id,
					//level,
					//level name,
					//MainStakeholder
        $strSql = "SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					stakeholder.MainStakeholder
					FROM
					stakeholder
					Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
					Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
					Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
					ORDER BY stakeholder.stkid";
        $rsSql = mysql_query($strSql) or die("Error GetAllStakeholdersOfficeTypes");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllStakeholdersAtParentlevel
     * @return boolean
     * 
     * 
     * 
     */
    function GetAllStakeholdersAtParentlevel() {
        $lvl = $this->m_lvl;
        if ($lvl > 1 && $lvl != 7) {
            $lvl = $lvl - 1;
        } else if ($lvl == 7) {
            $lvl = 4;
        }


        $strSql = "SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					stakeholder.MainStakeholder
					FROM
					stakeholder
					Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
					Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
					Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
					where stakeholder.lvl=" . $lvl . " and stakeholder.MainStakeholder =" . $this->m_MainStakeholder . " ORDER BY stakeholder.stkid";
        $rsSql = mysql_query($strSql) or die("Error GetAllStakeholdersAtlevel");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllStakeholderslddddd
     * @return boolean
     * 
     * 
     * 
     */
    function GetAllStakeholderslddddd() {
        $strSql = "SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					tbl_dist_levels.lvl_id
					FROM
										stakeholder
										Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
										Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
										Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
										where lvl_id !=3";
        $rsSql = mysql_query($strSql) or die("Error GetAllStakeholderslvll");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStakeholdersById
     * @return boolean
     * 
     * 
     * 
     */
    function GetStakeholdersById() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder type descr
        //parent
        //stakeholder type id
        //level
        //level name
        //main stakeholder
        $strSql = "
		SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					stakeholder.MainStakeholder
					FROM
					stakeholder
					Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
					Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
					Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
					WHERE stakeholder.stkid=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetStakeholderById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    function GetManufacturersById() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder type descr
        //parent
        //stakeholder type id
        //level
        //level name
        //main stakeholder
        $strSql = "
		SELECT
	itminfo_tab.itm_id,
	itminfo_tab.itm_name,
	stakeholder.stkname,
	stakeholder_item.brand_name,
	stakeholder_item.net_capacity,
	stakeholder_item.quantity_per_pack,
	stakeholder_item.carton_per_pallet,
	stakeholder_item.stk_id,
	stakeholder_item.pack_length,
    stakeholder_item.pack_width,
    stakeholder_item.pack_height,
    stakeholder_item.gross_capacity,
    stakeholder_item.gtin
FROM
	stakeholder_item
INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	stakeholder.stk_type_id = 3
AND stakeholder_item.stk_id = " . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetManufacturersById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStakeholdersFamilyById
     * @return boolean
     * 
     * 
     * 
     */
    function GetStakeholdersFamilyById() {
        //select query
        //stakeholder id
        //stakeholder name
        //stakeholder order
        //parent
        //stakeholder type id
        //level
        //level name
        $strSql = "SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name
				FROM
					stakeholder
				Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
				Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
				Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
				WHERE stakeholder.MainStakeholder=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetStakeholdersFamilyById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStakeholdersFamilynatandpro
     * @return boolean
     * 
     * 
     * 
     */
    function GetStakeholdersFamilynatandpro() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder order
        //stakeholder typ description
        //parent
        //parent id
        //stakeholder type id
        //level
        //level name
        //level id
        $strSql = "SELECT
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					tbl_dist_levels.lvl_id
					FROM
										stakeholder
										Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
										Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
										Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
										WHERE tbl_dist_levels.lvl_id=" . $this->m_npkId . "OR tbl_dist_levels.lvl_id=" . $this->m_npkId;

        $rsSql = mysql_query($strSql) or die("Error GetStakeholdersFamilynatandpro");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetRanks
     * @return boolean
     * 
     * 
     */
    function GetRanks() {
        //select query
        $strSql = "SELECT DISTINCT stkorder FROM stakeholder order by stkorder DESC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetRanks");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * get_stakeholder_name
     * @return type
     * 
     * 
     */
    function get_stakeholder_name() {
        $stkname = '';
        //select query
        $strSql = "SELECT stkname FROM stakeholder where stkid=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die($strSql . mysql_error());
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $RowEditStk = mysql_fetch_object($rsSql);
            $stkname = $RowEditStk->stkname;
        }
        return $stkname;
    }

    /**
     * 
     * GetMaxRank
     * @return type
     * 
     * 
     */
    function GetMaxRank() {
        $MaxRank = 0;
        //select query
        $strSql = "SELECT Max(stkorder) as MaxOrder FROM stakeholder ";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetMaxRank");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $RowEditStk = mysql_fetch_object($rsSql);
            $MaxRank = (int) ($RowEditStk->MaxOrder + 1);
        }
        return $MaxRank;
    }

    /**
     * 
     * GetMaxGroupID
     * @return type
     * 
     * 
     */
    function GetMaxGroupID() {
        $MaxRank = 0;
        //select query
        $strSql = "SELECT Max(stkgroupid) as MaxOrder FROM stakeholder ";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetMaxGroupID");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $RowEditStk = mysql_fetch_object($rsSql);
            $MaxRank = (int) ($RowEditStk->MaxOrder);
        }
        return $MaxRank;
    }

    /**
     * 
     * GetAllManufacturersUpdate
     * @param type $item_pack_size_id
     * @return boolean
     * 
     * 
     */
    function GetAllManufacturersUpdate($item_pack_size_id) {
        //select query
        //gets
        //stakeholder id,
        //stakeholder name,
        //stakeholder order,
        //stakeholder type descr,
        // Parent,
        //ParentID,
        //stakeholder type id,
        //level,
        //level_name,
        //Main Stakeholder
        $strSql = "SELECT
						stakeholder.stkid,
						stakeholder.stkname,
						stakeholder.stkorder,
						stakeholder_type.stk_type_descr,
						IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
						stakeholder.ParentID,
						stakeholder.stk_type_id,
						stakeholder.lvl,
						tbl_dist_levels.lvl_name,
						stakeholder.MainStakeholder
						FROM
						stakeholder
						Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
						Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
                                                Inner Join stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
						Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
						where stakeholder.ParentID is null
                                                and stakeholder.stk_type_id='2'
                                                and stakeholder_item.stk_item = " . $item_pack_size_id . "
						ORDER BY stakeholder.stkid";
        //query results
        $stSql = mysql_query($strSql) or die("Error GetAllStakeholdersttttttttt");

        if (mysql_num_rows($stSql) > 0) {
            return $stSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllManufacturers
     * @param type $item_pack_size_id
     * @return type
     * 
     * 
     */
    function GetAllManufacturers($item_pack_size_id = null) {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        //stakeholder typ description
        //parent
        //parent id
        //stakeholder type id
        //level
        //level name
        //main stakeholder 
        //
        $strSql = "SELECT
						stakeholder.stkid,
						stakeholder.stkname,
						stakeholder.stkorder,
						stakeholder_type.stk_type_descr,
						IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
						stakeholder.ParentID,
						stakeholder.stk_type_id,
						stakeholder.lvl,
						tbl_dist_levels.lvl_name,
						stakeholder.MainStakeholder
						FROM
						stakeholder
						Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
						Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
                                                Inner Join stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
						Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
						where stakeholder.ParentID is null
                                                and stakeholder.stk_type_id='3'
                                             GROUP BY
						stakeholder_item.stk_item
						ORDER BY stakeholder.stkid";

        $result_set = mysql_query($strSql) or die("Error GetAllStakeholdersttttttttt");

        $object_array = array();
        while ($row = mysql_fetch_object($result_set)) {
            $object_array[] = $row;
        }
        return $object_array;
    }

    /**
     * 
     * GetAllMainStakeholders
     * @return boolean
     * 
     * 
     */
    function GetAllMainStakeholders() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        $strSql = "SELECT
						stakeholder.stkid,
						stakeholder.stkname
					FROM
						stakeholder
					WHERE
						stakeholder.ParentID IS NULL
					AND stakeholder.stk_type_id IN (0, 1)
					ORDER BY
						stakeholder.stkorder ASC";

        $strSql = mysql_query($strSql) or die("Error GetAllMainStakeholders");

        if (mysql_num_rows($strSql) > 0) {
            return $strSql;
        } else {
            return FALSE;
        }
    }
	
    /**
     * 
     * GetAllMainStakeholders whome stock is issued
     * @return boolean
     * 
     * 
     */
    function GetAllMainTransStakeholders() {
        //select query
        //gets
        //stakeholder id
        //stakeholder name
        $strSql = "SELECT DISTINCT
						stakeholder.stkid,
						stakeholder.stkname
					FROM
						tbl_stock_master
					INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
					INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
					ORDER BY
						stakeholder.stkorder ASC";

        $strSql = mysql_query($strSql) or die("Error GetAllMainTransStakeholders");

        if (mysql_num_rows($strSql) > 0) {
            return $strSql;
        } else {
            return FALSE;
        }
    }

}

?>
