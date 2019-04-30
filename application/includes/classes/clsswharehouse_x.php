<?php

/**
 * clswarehouse
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clswarehouse_x {
//npkid
    var $m_npkId;
    //warehouse name
    var $m_wh_name;
    //district id
    var $m_dist_id;
    //level
    var $m_lvl;
    //province id
    var $m_prov_id;
    //stakeholder id
    var $m_stkid;
    //stakeholder office id
    var $m_stkofficeid;
    //hf typ id
    var $hf_type_id;
    //reporting_start_month
    var $reporting_start_month;
    //editable_data_entry_months
    var $editable_data_entry_months;
    //is_lock_data_entry
    var $is_lock_data_entry;
    //warehouse rank
    var $wh_rank;
    //hf code
    var $hf_code;
    //is active
    var $is_active;
    //Show IM
    var $is_allowed_im;
    //district
    var $district = array('');

    /**
     * 
     * Addwarehouse
     * @return int
     * 
     */
    function Addwarehouse() {
		if ($this->m_stkid == '') {
            $this->m_stkid = NULL;
        }
        if ($this->m_stkofficeid == '') {
            $this->m_stkofficeid = NULL;
        }
        if ($this->m_prov_id == '') {
            $this->m_prov_id = NULL;
        }
        if ($this->m_dist_id == '') {
            $this->m_dist_id = NULL;
        }
        if ($this->m_wh_name == '') {
            $this->m_wh_name = NULL;
        }
        if ($this->reporting_start_month == '') {
            $this->reporting_start_month = NULL;
        }
        if ($this->editable_data_entry_months == '') {
            $this->editable_data_entry_months = NULL;
        }
        if ($this->is_lock_data_entry == '') {
            $this->is_lock_data_entry = NULL;
        }
        if ($this->wh_rank == '' || $this->wh_rank == 0) {
            $this->wh_rank = NULL;
        }
        if ($this->hf_code == '') {
            $this->hf_code = NULL;
        }
        if ($this->is_allowed_im == '') {
            $this->is_allowed_im = NULL;
        }
        $strSql = "INSERT INTO tbl_warehouse (stkid,stkofficeid,prov_id,dist_id,wh_name,locid, hf_type_id, reporting_start_month, editable_data_entry_months, is_lock_data_entry, wh_rank, dhis_code, is_active, is_allowed_im) VALUES(" . $this->m_stkid . "," . $this->m_stkofficeid . "," . $this->m_prov_id . "," . $this->m_dist_id . ",'" . $this->m_wh_name . "'," . $this->m_dist_id . ",'" . $this->hf_type_id . "','" . $this->reporting_start_month . "','" . $this->editable_data_entry_months . "','" . $this->is_lock_data_entry . "','" . $this->wh_rank . "','" . $this->hf_code . "','" . $this->is_active . "','" . $this->is_allowed_im . "')";

        $rsSql = mysql_query($strSql) or die("Error Addwarehouse");

        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * 
     * Editwarehouse
     * @return boolean
     * 
     */
    function Editwarehouse() {
        $strSql = "UPDATE tbl_warehouse SET wh_id=" . $this->m_npkId;

        $stkid = ",stkid='" . $this->m_stkid . "'";
        if ($this->m_stkid != '') {
            $strSql .=$stkid;
        }

        $stkofficeid = ",stkofficeid=" . $this->m_stkofficeid;
        if ($this->m_stkofficeid != '') {
            $strSql .=$stkofficeid;
        }

        $prov_id = ",prov_id=" . $this->m_prov_id;
        if ($this->m_prov_id != '') {
            $strSql .=$prov_id;
        }

        $dist_id = ",dist_id='" . $this->m_dist_id . "'";
        if ($this->m_dist_id != '') {
            $strSql .=$dist_id;
        }

        $wh_name = ",wh_name='" . $this->m_wh_name . "'";
        if ($this->m_wh_name != '') {
            $strSql .=$wh_name;
        }

        $hf_type_id = ",hf_type_id='" . $this->hf_type_id . "'";
        if ($this->hf_type_id != '') {
            $strSql .=$hf_type_id;
        }

        $reporting_start_month = ",reporting_start_month='" . $this->reporting_start_month . "'";
        if ($this->reporting_start_month != '') {
            $strSql .=$reporting_start_month;
        }

        $editable_data_entry_months = ",editable_data_entry_months='" . $this->editable_data_entry_months . "'";
        if ($this->editable_data_entry_months != '') {
            $strSql .=$editable_data_entry_months;
        }

        $is_lock_data_entry = ",is_lock_data_entry='" . $this->is_lock_data_entry . "'";
        if ($this->is_lock_data_entry != '') {
            $strSql .=$is_lock_data_entry;
        }

        $wh_rank = ",wh_rank='" . $this->wh_rank . "'";
        if ($this->wh_rank != '' && $this->wh_rank != 0) {
            $strSql .=$wh_rank;
        } else {
            $strSql .= ',wh_rank=NULL';
        }

        $hf_code = ",dhis_code='" . $this->hf_code . "'";
        if ($this->hf_code != '') {
            $strSql .=$hf_code;
        } else {
            $strSql .= ',dhis_code=NULL';
        }

        $is_active = ",is_active='" . $this->is_active . "'";
        if ($this->is_active != '') {
            $strSql .=$is_active;
        } else {
            $strSql .= ',is_active=NULL';
        }

        $is_allowed_im = ",is_allowed_im='" . $this->is_allowed_im . "'";
        if ($this->is_allowed_im != '') {
            $strSql .=$is_allowed_im;
        } else {
            $strSql .= ',is_allowed_im=NULL';
        }
        $strSql .=" WHERE wh_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error Editwarehouse");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Deletewarehouse
     * @return boolean
     * 
     */
    function Deletewarehouse() {

        $strSql = "DELETE FROM tbl_warehouse WHERE wh_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error Deletewarehousehhhh");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * allowIM
     * @return boolean
     * 
     */
    function allowIM() {
        $getStatusQry = "select is_allowed_im from tbl_warehouse where wh_id=" . $this->m_npkId;
        $resQry = mysql_query($getStatusQry);
        $rowImAllowed = mysql_fetch_assoc($resQry);
        $allowed = '';
        if ($rowImAllowed['is_allowed_im'] == 1) {
            $allowed = 0;
        } else {
            $allowed = 1;
        }
        $strSql = "Update tbl_warehouse set is_allowed_im=$allowed WHERE wh_id=" . $this->m_npkId;
        ;
        $rsSql = mysql_query($strSql) or die("Error allow_im_update");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllWarehouses
     * @return boolean
     * 
     */
    function GetAllWarehouses() {
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province,
                    office.stkname as officeName
                   FROM
                    	tbl_warehouse
				Left Join stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
				Left Join stakeholder as office ON tbl_warehouse.stkofficeid = office.stkid
				Left Join tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
				Left Join tbl_locations AS province ON district.ParentID = province.PkLocID
				Order by province,district,officeName";
        $rsSql = mysql_query($strSql) or die("Error GetAllwarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllUCCenters
     * @param type $id
     * @return boolean
     * 
     */
    function GetAllUCCenters($id) {
        $strSql = "SELECT
            DISTINCT tbl_warehouse.wh_id,
            tbl_warehouse.wh_name
            FROM
            tbl_warehouse
            WHERE
            tbl_warehouse.locid = $id";
        $rsSql = mysql_query($strSql) or die("Error GetAllUCCenters");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetUserWarehouses
     * @return boolean
     * 
     */
    function GetUserWarehouses() {
        $strSql = "SELECT
				tbl_warehouse.wh_name,
				tbl_warehouse.wh_id
			FROM
				tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
			WHERE
				stakeholder.stk_type_id = 2
			AND tbl_warehouse.is_active = 1
			ORDER BY
				stakeholder.stkorder ASC";

        $rsSql = mysql_query($strSql) or die("Error GetUserWarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetUserIssueToWH
     * @param type $wh_id
     * @return boolean
     * 
     */
    function GetUserIssueToWH($wh_id) {
        $strSql = "SELECT
			CONCAT(tbl_warehouse.wh_name, '(', stakeholder.stkname, ')') AS wh_name,
			tbl_warehouse.wh_id
		FROM
		tbl_warehouse
		INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		WHERE
		tbl_stock_master.WHIDFrom = $wh_id
		GROUP BY tbl_warehouse.wh_name";
        $rsSql = mysql_query($strSql) or die("Error GetUserIssueToWH");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * DelGetUserIssueToWH
     * @param type $wh_id
     * @return boolean
     * 
     */
    function DelGetUserIssueToWH($wh_id) {
        $strSql = "SELECT
					tbl_warehouse.wh_name,
					tbl_warehouse.wh_id
				FROM
					tbl_warehouse
				INNER JOIN log_tbl_stock_master ON log_tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
				WHERE
					log_tbl_stock_master.TranTypeID = 2
				GROUP BY
					tbl_warehouse.wh_name
				ORDER BY
					tbl_warehouse.wh_name";
        $rsSql = mysql_query($strSql) or die("Error GetUserIssueToWH");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetUserReceiveFromWH
     * @param type $wh_id
     * @return boolean
     * 
     */
    function GetUserReceiveFromWH($wh_id) {
        $strSql = "SELECT
			tbl_warehouse.wh_name,
			tbl_warehouse.wh_id
		FROM
		tbl_warehouse
		INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		WHERE
		tbl_stock_master.WHIDTo = $wh_id
		AND tbl_stock_master.TranTypeID = 1
		GROUP BY tbl_warehouse.wh_name
		ORDER BY
		stakeholder.stkorder ASC";

        $rsSql = mysql_query($strSql) or die("Error GetUserReceiveFromWH");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * DelGetUserReceiveFromWH
     * @param type $wh_id
     * @return boolean
     * 
     * 
     * 
     */
    function DelGetUserReceiveFromWH($wh_id) {
        $strSql = "SELECT
					tbl_warehouse.wh_name,
					tbl_warehouse.wh_id
				FROM
					tbl_warehouse
				INNER JOIN log_tbl_stock_master ON log_tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
				WHERE
					log_tbl_stock_master.TranTypeID = 1
				GROUP BY
					tbl_warehouse.wh_name
				ORDER BY
					tbl_warehouse.wh_name";

        $rsSql = mysql_query($strSql) or die("Error GetUserReceiveFromWH");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getFifthLvlWH
     * @param type $wh_id
     * @return boolean
     * 
     * 
     * 
     */
    function getFifthLvlWH($wh_id) {
        $strSql = "SELECT
			tbl_warehouse.wh_name,
			tbl_warehouse.wh_id
			FROM
			tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
			stakeholder.lvl = 5 AND
			tbl_warehouse.wh_id = $wh_id";
        $rsSql = mysql_query($strSql) or die("Error getFifthLvlWH");
        if (!empty($rsSql) && mysql_num_rows($rsSql) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetLevelDownWarehouses
     * @return boolean
     * 
     * 
     * 
     */
    function GetLevelDownWarehouses() {
        //select query
        //gets
        //warehouse name
        //warehouse id
        $strSql = "SELECT
			tbl_warehouse.wh_name,
			tbl_warehouse.wh_id
			FROM
			tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
			INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
			INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
			WHERE
			stakeholder.IsSupplier = 1";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetWarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehousesByProvStk
     * @param type $user_prov
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehousesByProvStk($user_prov, $user_stk) {
        $stk = implode(",", $user_stk);
        $prov = implode(",", $user_prov);
//select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office type id
        //stakeholder name
        $strSql = "SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.dist_id,
					tbl_warehouse.prov_id,
					tbl_warehouse.stkid,
					tbl_warehouse.wh_type_id,
					tbl_warehouse.locid,
					tbl_warehouse.stkofficeid,
					stakeholder.stkname
					FROM
					tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
					INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
					WHERE tbl_warehouse.stkid IN (" . $stk . ") AND tbl_warehouse.prov_id IN (" . $prov . ")
					ORDER BY tbl_locations.LocName,tbl_warehouse.stkid,tbl_warehouse.stkofficeid";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllwarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetFederalWarehouses
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetFederalWarehouses($user_stk) {
//select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //stakeholder office type id
        $strSql = "SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND stakeholder.lvl = 1
					AND tbl_warehouse.wh_id != " . $_SESSION['user_warehouse'] . "
					ORDER BY
						tbl_warehouse.stkid ASC,
						tbl_warehouse.stkofficeid ASC";
        //query results
        $rsSql = mysql_query($strSql) or die("Error GetFederalWarehouses: " . mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetProvincialWarehouses
     * @param type $user_prov
     * @param type $user_stk
     * @param type $mainstk
     * @return boolean
     * 
     * 
     * 
     */
    function GetProvincialWarehouses($user_prov, $user_stk, $mainstk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //stakeholder office type id
        //stakeholder name
        $strSql = " SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.dist_id,
					tbl_warehouse.prov_id,
					tbl_warehouse.stkid,
					tbl_warehouse.locid,
					tbl_warehouse.stkofficeid,
					stakeholder.stkname
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_warehouse.prov_id = $user_prov
				AND stakeholder.lvl = 2
				AND stakeholder.MainStakeholder = $mainstk
				AND tbl_warehouse.wh_id != " . $_SESSION['user_warehouse'] . "
				ORDER BY
					tbl_warehouse.wh_name ASC";
       //query result
        $rsSql = mysql_query($strSql) or die("Error GetProvincialWarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetDivsionalWarehousesofProvince
     * @param type $user_prov
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetDivsionalWarehousesofProvince($user_prov, $user_stk) {
//select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //stakeholder office type id
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND tbl_warehouse.prov_id = $user_prov
					AND tbl_warehouse.wh_id != " . $_SESSION['user_warehouse'] . "
					AND stakeholder.lvl = 3
					ORDER BY
						tbl_warehouse.wh_name ASC";

        $rsSql = mysql_query($strSql) or die("Error GetDivsionalWarehousesofProvince");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetDistrictWarehousesofProvince
     * @param type $user_prov
     * @param type $user_stk
     * @param type $mainstk
     * @return boolean
     * 
     * 
     * 
     */
    function GetDistrictWarehousesofProvince($user_prov, $user_stk, $mainstk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //stakeholder office type id
        //stakeholder name
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid,
						stakeholder.stkname
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.prov_id = $user_prov
					AND stakeholder.lvl = 3
					AND stakeholder.MainStakeholder = $mainstk
					AND tbl_warehouse.wh_id != " . $_SESSION['user_warehouse'] . "
					ORDER BY
						tbl_warehouse.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die($strSql);

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetFieldWarehousesofProvince
     * @param type $user_prov
     * @param type $user_stk
     * @param type $mainstk
     * @return boolean
     * 
     * 
     * 
     */
    function GetFieldWarehousesofProvince($user_prov, $user_stk, $mainstk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //stakeholder office type id
        //stakeholder name
        $strSql = " SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.dist_id,
					tbl_warehouse.prov_id,
					tbl_warehouse.stkid,
					tbl_warehouse.locid,
					tbl_warehouse.stkofficeid,
					stakeholder.stkname
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_warehouse.prov_id = $user_prov
				AND stakeholder.lvl = 4
				AND stakeholder.MainStakeholder = $mainstk
				ORDER BY
					tbl_warehouse.wh_name ASC";
         //query result
        $rsSql = mysql_query($strSql) or die($strSql);

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetHealthFacilityWarehousesofDistrict
     * @param type $distId
     * @param type $mainstk
     * @return boolean
     * 
     * 
     * 
     */
    function GetHealthFacilityWarehousesofDistrict($distId, $mainstk) {
        //select query
        //gets
        //Health Facility Warehouses of District
        $strSql = "SELECT
					*
				FROM
					(
						SELECT
							tbl_warehouse.wh_id,
							tbl_warehouse.wh_name,
							stakeholder.lvl,
							tbl_hf_type_rank.hf_type_rank,
							tbl_warehouse.wh_rank
						FROM
							wh_user
						INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
						INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
						LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
						AND tbl_warehouse.prov_id = tbl_hf_type_rank.province_id
						AND tbl_warehouse.stkid = tbl_hf_type_rank.stakeholder_id
						WHERE
							tbl_warehouse.dist_id = " . $distId . "
						AND tbl_warehouse.stkid = " . $mainstk . "
						AND stakeholder.lvl = 7
					) A
				GROUP BY
					A.wh_id
				ORDER BY
					IF (A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
					A.wh_rank,
					IF (A.hf_type_rank = '' OR A.hf_type_rank IS NULL, 1, 0),
					A.hf_type_rank ASC,
					A.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die($strSql);

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetLevel8WarehousesofDistrict
     * @param type $distId
     * @param type $mainstk
     * @return boolean
     * 
     * 
     * 
     */
    function GetLevel8WarehousesofDistrict($distId, $mainstk) {
//select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office id
        //stakeholder name
        $strSql = " SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.dist_id,
					tbl_warehouse.prov_id,
					tbl_warehouse.stkid,
					tbl_warehouse.locid,
					tbl_warehouse.stkofficeid,
					stakeholder.stkname
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_warehouse.dist_id = $distId
				AND stakeholder.lvl = 8
				AND stakeholder.MainStakeholder = $mainstk
				ORDER BY
					tbl_warehouse.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die($strSql);

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetDistrictWarehousesofProvinceforShiftData
     * @param type $user_prov
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetDistrictWarehousesofProvinceforShiftData($user_prov, $user_stk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office id
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND tbl_warehouse.prov_id = $user_prov
					AND stakeholder.lvl = 4 AND tbl_warehouse.wh_id IN (SELECT DISTINCT
wh2.wh_id
FROM
tbl_stock_master
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_warehouse AS wh2 ON tbl_stock_master.WHIDTo = wh2.wh_id
INNER JOIN stakeholder AS stk2 ON wh2.stkofficeid = stk2.stkid
WHERE
stakeholder.stk_type_id = 1 AND
stk2.lvl > 1)
					ORDER BY
						tbl_warehouse.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProvincialWarehouses");

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehousesofDivision
     * @param type $user_div
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehousesofDivision($user_div, $user_stk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office id
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND tbl_warehouse.locid = $user_div
					AND stakeholder.lvl = 3
					ORDER BY
						tbl_warehouse.stkid ASC,
						tbl_warehouse.stkofficeid ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProvincialWarehouses");

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetTehsilWarehousesofDistrict
     * @param type $user_dist
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetTehsilWarehousesofDistrict($user_dist, $user_stk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office id
        
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND tbl_warehouse.dist_id = $user_dist
					AND stakeholder.lvl = 4
					ORDER BY
						tbl_warehouse.stkid ASC,
						tbl_warehouse.stkofficeid ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProvincialWarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetUCWarehousesofDistrict
     * @param type $user_dist
     * @param type $user_stk
     * @return boolean
     * 
     * 
     * 
     */
    function GetUCWarehousesofDistrict($user_dist, $user_stk) {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //district id
        //province id
        //stakeholder id
        //location id
        //stakeholder office id
        $strSql = " SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.dist_id,
						tbl_warehouse.prov_id,
						tbl_warehouse.stkid,
						tbl_warehouse.locid,
						tbl_warehouse.stkofficeid
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.stkid = $user_stk
					AND tbl_warehouse.dist_id = $user_dist
					AND stakeholder.lvl = 6
					ORDER BY
						tbl_warehouse.stkid ASC,
						tbl_warehouse.stkofficeid ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetProvincialWarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehousesByLevel
     * @param type $from
     * @param type $to
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehousesByLevel($from, $to) {
        //select query
        //gets
        //warehouse  name
        //warehouse id
        $strSql = "SELECT
			tbl_warehouse.wh_name,
			tbl_warehouse.wh_id
			FROM
				tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
				INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
				INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
			WHERE
			stakeholder.IsSupplier = 1 AND
			stakeholder.lvl >= " . $from . " AND
			stakeholder.lvl <= " . $to;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetWarehousesByLevel");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehouseLevelById
     * @param type $id
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehouseLevelById($id) {
        //select query
        //gets
        //level
        $strSql = "SELECT
			stakeholder.lvl
			FROM
			tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
			tbl_warehouse.wh_id = $id";
        //query results
        $rsSql = mysql_query($strSql) or die("Error GetWarehouseLevelById");
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->lvl;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllwarehouseprovince
     * @return boolean
     * 
     * 
     * 
     */
    function GetAllwarehouseprovince() {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //stakeholder name
        //stakeholder id
        //distrcit id
        //distrcit
        //province id
        //province
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province,
                    office.stkname as officeName
                    FROM
                    tbl_warehouse
                    Left Join stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                    Left Join stakeholder as office ON tbl_warehouse.stkofficeid = office.stkid
                    Left Join tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
                    Left Join tbl_locations AS province ON district.ParentID = province.PkLocID
                    where province.PkLocID=" . $this->provid;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllwarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehouseById
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehouseById() {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //stakeholder office type id
        //hf type id
        //reporting_start_month
        //editable_data_entry_months
        //is_lock_data_entry
        //warehouse rank
        //dhis code
        //is active
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    tbl_warehouse.hf_type_id,
					tbl_warehouse.reporting_start_month,
					tbl_warehouse.editable_data_entry_months,
					tbl_warehouse.is_lock_data_entry,
					tbl_warehouse.wh_rank,
					tbl_warehouse.dhis_code,
					tbl_warehouse.is_active,
					tbl_warehouse.is_allowed_im,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province
				FROM
					tbl_warehouse
				Left Join stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
				Left Join tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
				Left Join tbl_locations AS province ON district.ParentID = province.PkLocID
				WHERE
					wh_id=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetwarehouseById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehouseNameById
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehouseNameById() {
        //select query
        $strSql = "SELECT
					IF(ISNULL(tbl_warehouse.dhis_code), tbl_warehouse.wh_name, CONCAT(tbl_warehouse.dhis_code, ' - ', tbl_warehouse.wh_name)) AS wh_name
				FROM
					tbl_warehouse
				WHERE
					wh_id = " . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("GetWarehouseNameById");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $whName = $row['wh_name'];
            }
            return $whName;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehouseTypeNameById
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehouseTypeNameById() {
         //select query
        //gets
        //Warehouse Type Name By Id
        $strSql = "SELECT
						tbl_hf_type.hf_type
					FROM
						tbl_hf_type
					WHERE
						tbl_hf_type.pk_id = " . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("GetWarehouseNameById");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $whName = $row['hf_type'];
            }
            return $whName;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStkIDByWHId
     * @return boolean
     * 
     * 
     * 
     */
    function GetStkIDByWHId() {
         //select query
        //gets
        //stakeholder id by warehouse id
        $strSql = "SELECT tbl_warehouse.Stkid from tbl_warehouse
					 WHERE wh_id=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("GetStkIDByWHId");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $whName = $row['Stkid'];
            }
            return $whName;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStkIDByWHTypeId
     * @return boolean
     * 
     * 
     * 
     */
    function GetStkIDByWHTypeId() {
         //select query
        //gets
        //stk id
        $strSql = "SELECT tbl_warehouse.Stkid from tbl_warehouse
					 WHERE hf_type_id=" . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("GetStkIDByWHId");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $whName = $row['Stkid'];
            }
            return $whName;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWHByWHId
     * @param type $wh_id
     * @return boolean
     * 
     * 
     * 
     */
    function GetWHByWHId($wh_id) {
         //select query
        //gets
        //warehouse by id
        $strSql = "SELECT wh_name from tbl_warehouse
					 WHERE wh_id=" . $wh_id;
        //query result
        $rsSql = mysql_query($strSql) or die("GetWarehouseNameById");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $wh_name = $row['wh_name'];
            }
            return $wh_name;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * IsDivWHofProvWH
     * @param type $div_id
     * @param type $prov_id
     * @return int
     * 
     * 
     * 
     */
    function IsDivWHofProvWH($div_id, $prov_id) {
         //select query
        //gets
        //warehouse id
        $strSql = " SELECT
						tbl_warehouse.wh_id
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						prov_id = (
							SELECT
								prov_id
							FROM
								tbl_warehouse
							WHERE
								wh_id = $div_id
						)
					AND wh_id = $prov_id
					AND stakeholder.lvl = 2";
       //query result
        $rsSql = mysql_query($strSql) or die("Error: IsDivWHofProvWH -> clsswharehouse.php");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 
     * IsSubWHofDistWH
     * @param type $SubWH_id
     * @param type $Dist_id
     * @return int
     * 
     * 
     * 
     */
    function IsSubWHofDistWH($SubWH_id, $Dist_id) {
        //select query
        //gets
        //warehouse id
        $strSql = " SELECT
						tbl_warehouse.wh_id
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						prov_id = (
							SELECT
								prov_id
							FROM
								tbl_warehouse
							WHERE
								wh_id = $SubWH_id
						)
					AND wh_id = $Dist_id
					AND stakeholder.lvl = 4";
        //query result
        $rsSql = mysql_query($strSql) or die("Error: IsSubWHofDistWH -> clsswharehouse.php");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 
     * GetWarehouseBylocByStakeholderOffice
     * @return boolean
     * 
     * 
     * 
     */
    function GetWarehouseBylocByStakeholderOffice() {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //stakeholder office id
        //stakeholder name
        //stakeholder id
        //district id
        //district
        //province id
        //province
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province
                    FROM
                    tbl_warehouse
                    Left Join stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                    Left Join tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
                    Left Join tbl_locations AS province ON district.ParentID = province.PkLocID WHERE tbl_warehouse.stkofficeid=" . $this->m_stkofficeid . " and tbl_warehouse.locid=" . $this->m_dist_id;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetWarehouseBylocByStakeholderOfficee");


        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWarehouseBylocByStakeholder
     * @return boolean
     * 
     */
    function GetWarehouseBylocByStakeholder() {
        $district = $this->m_dist_id;
        $lvl = ($this->m_lvl != 'all') ? " AND stakeholder.lvl = $this->m_lvl" : '';
        //select query 
        //gets
        //warehouse id
        //warehouse name
        //stakeholder name
        //stakeholder office id
        //stakeholder id
        //district id
        //district
        //province
        //province id
        $strSql = "SELECT
						tbl_warehouse.wh_id,
						CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
						tbl_warehouse.stkofficeid,
						stakeholder.stkname,
						tbl_warehouse.stkid,
						district.PkLocID AS dist_id,
						district.LocName AS district,
						province.PkLocID AS prov_id,
						province.LocName AS province
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
					INNER JOIN tbl_locations AS province ON tbl_warehouse.prov_id = province.PkLocID
					WHERE
						tbl_warehouse.stkid = " . $this->m_stkid . "
					AND tbl_warehouse.dist_id IN ($district)
					$lvl";
        
        $rsSql = mysql_query($strSql) or die("Error GetWarehouseBylocByStakeholder");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllWarehouses1
     * @return boolean
     * 
     */
    function GetAllWarehouses1() {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //stakeholder office id
        //stakeholder name
        //stakeholder id
        //district id
        //district
        //province id
        //province
        //office name
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province,
                    office.stkname as officeName
                    FROM
                    tbl_warehouse
                    Left Join stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                    Left Join stakeholder as office ON tbl_warehouse.stkofficeid = office.stkid
                    Left Join tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
                    Left Join tbl_locations AS province ON district.ParentID = province.PkLocID
                    WHERE tbl_warehouse.stkid='" . $this->m_stkid . "' AND province.PkLocID='" . $this->m_provid . "'
                    Order by province,district,officeName
                    ";
//query result
        $rsSql = mysql_query($strSql) or die("Error GetAllwarehouses");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllWarehouses0
     * @return boolean
     * 
     */
    function GetAllWarehouses0() {
        //select query
        //gets
        //warehouse id
        //warehouse name
        //stakeholder office id
        //stakeholder name
        //district id
        //district
        //province id
        //province
        $strSql = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.stkofficeid,
                    stakeholder.stkname,
                    tbl_warehouse.stkid,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province,
                    office.stkname AS officeName
            FROM
                    tbl_warehouse
            LEFT JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
            LEFT JOIN stakeholder AS office ON tbl_warehouse.stkofficeid = office.stkid
            LEFT JOIN tbl_locations AS district ON tbl_warehouse.locid = district.PkLocID
            LEFT JOIN tbl_locations AS province ON district.ParentID = province.PkLocID
            ORDER BY
                    province,
                    district,
                    officeName";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllwarehouses0");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWHTypes
     * @return boolean
     * 
     */
    function GetWHTypes() {
        //select query
        //gets
        //pk id
        //hf type
        $strSql = "SELECT
						tbl_hf_type.pk_id,
						tbl_hf_type.hf_type
					FROM
						tbl_hf_type
					ORDER BY
						tbl_hf_type.hf_rank ASC";
//query result
        $rsSql = mysql_query($strSql) or die("Error GetWHTypes");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
