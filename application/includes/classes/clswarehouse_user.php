<?php

/**
 * clswarehouse_user
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsIwh_user {

    //npkid
    var $m_npkId;
    //warehouse user id
    var $m_wh_user_id;
    //sys user id
    var $m_sysusrrec_id;
    //warehouse id
    var $m_wh_id;
    //stakeholder id
    var $m_stk_id;
    //province id
    var $m_prov_id;
    var $level;

    /**
     * 
     * Addwh_user
     * @return boolean
     * 
     * 
     * 
     */
    function Addwh_user() {
        //check sysusrrec id
        if ($this->m_sysusrrec_id == '') {
            $this->m_sysusrrec_id = 0;
        }
        //check warehouse id
        if ($this->m_wh_id == '') {
            $this->m_wh_id = 0;
        }
        //insert query
        //inserts
        //sys user rec
        //warehouse id
        $strSql = "INSERT INTO wh_user(sysusrrec_id,wh_id) VALUES(" . $this->m_sysusrrec_id . "," . $this->m_wh_id . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Addwh_user");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Editwh_user
     * @return boolean
     * 
     * 
     * 
     */
    function Editwh_user() {
//update query
        $strSql = "UPDATE wh_user SET wh_user_id=" . $this->m_npkId;
        $sysusrrec_id = ",sysusrrec_id='" . $this->m_sysusrrec_id . "'";
        if ($this->m_sysusrrec_id != '') {
            $strSql .= $sysusrrec_id;
        }

        $wh_id = ",wh_id='" . $this->m_wh_id . "'";
        if ($this->m_wh_id != '') {
            $strSql .= $wh_id;
        }

        $strSql .= " WHERE wh_user_id=" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error wh_user");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Deletewh_user
     * @return boolean
     * 
     * 
     * 
     */
    function Deletewh_user() {
        if (!empty($this->m_npkId)) {
            //delete query
            $strSql = "DELETE FROM  wh_user WHERE wh_user_id=" . $this->m_npkId;
//query result
            //echo '>a>'.$strSql;exit;
            $rsSql = mysql_query($strSql) or die("Error Deletewh_user 1");
            if (mysql_affected_rows()) {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Deletewh_userbyuserid
     * @return boolean
     * 
     * 
     * 
     */
    function Deletewh_userbyuserid() {
        //delete query
        $strSql = "DELETE FROM  wh_user WHERE sysusrrec_id=" . $this->m_sysusrrec_id;
        //query result
        //echo $strSql;exit;
        
            //echo '>b>'.$strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error Deletewh_user byuserid");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetAllwh_user
     * @return boolean
     * 
     * 
     * 
     */
    function GetAllwh_user() {
        //select query
        //gets
        //wh_user.sysusrrec_id,
        //sysusr_name,
        //warehouse id,
        //warehouse name,
        //UserID,
        //warehouse id,
        //usrlogin id,
        //stakeholder id,
        //stakeholder name
        $strSql = "SELECT
					wh_user.sysusrrec_id,
					sysuser_tab.sysusr_name,
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					sysuser_tab.UserID,
					wh_user.wh_id,
					sysuser_tab.usrlogin_id,
					stakeholder.stkid,
					stakeholder.stkname
				FROM
					sysuser_tab
				Left Join wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
				Left Join tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
				Left Join stakeholder ON sysuser_tab.stkid = stakeholder.stkid";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllwh_user");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Getwh_userById
     * @return boolean
     * 
     * 
     * 
     */
    function Getwh_userById() {
        //select query
        //gets
        //sysusrrec_id,
        //sysusr_name,
        //warehouse id,
        //warehouse name,
        //UserID,
        //warehouse id,
        //user login_id,
        //stakeholder id,
        //stakeholder name
        $strSql = "
				SELECT
					wh_user.sysusrrec_id,
					sysuser_tab.sysusr_name,
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					sysuser_tab.UserID,
					wh_user.wh_id,
					sysuser_tab.usrlogin_id,
					stakeholder.stkid,
					stakeholder.stkname
				FROM
					sysuser_tab
				Left Join wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
				Left Join tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
				Left Join stakeholder ON sysuser_tab.stkid = stakeholder.stkid
				WHERE wh_user.wh_user_id=" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error Getwh_userByIdhhhhhhh");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetwhuserByIdc
     * @return boolean
     * 
     * 
     * 
     */
    function GetwhuserByIdc() {
        //select query
        //gets
        //whuser By Id
        $strSql = "SELECT
						*
					FROM
						(
							SELECT
								wh_user.sysusrrec_id,
								sysuser_tab.sysusr_name,
								tbl_warehouse.wh_id,
								tbl_warehouse.wh_name,
								sysuser_tab.usrlogin_id,
								stakeholder.lvl
							FROM
								sysuser_tab
							INNER JOIN wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
							INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
							INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
							WHERE
								wh_user.sysusrrec_id = " . $this->m_npkId . "
							AND stakeholder.lvl <= 4 
						) A
					GROUP BY
						A.wh_id
					ORDER BY
						A.lvl ASC,
						A.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
        //fetch results
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetwhuserHFByIdc
     * @return boolean
     * 
     * 
     * 
     */
    function GetwhuserHFByIdc() {
        //select query
        //gets
        //warehouse user HF By Id
        $strSql = "SELECT
						*
					FROM
						(
							SELECT
								wh_user.sysusrrec_id,
								sysuser_tab.sysusr_name,
								tbl_warehouse.wh_id,
								tbl_warehouse.wh_name,
								sysuser_tab.usrlogin_id,
								stakeholder.lvl,
								tbl_hf_type_rank.hf_type_rank,
                                tbl_warehouse.wh_rank          
							FROM
								sysuser_tab
							INNER JOIN wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
							INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
							INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
							INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
							WHERE
								wh_user.sysusrrec_id = " . $this->m_npkId . "
							AND stakeholder.lvl > 4
							AND tbl_hf_type_rank.stakeholder_id = " . $this->m_stk_id . "
							AND tbl_hf_type_rank.province_id = " . $this->m_prov_id . "
							AND tbl_warehouse.is_active = 1
						) A
					GROUP BY
						A.wh_id
					ORDER BY
                        IF(A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
						A.wh_rank,
						A.hf_type_rank ASC,
						A.wh_name ASC";
        //query result
        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
        //fetch results
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetwhuserHFSatelliteByIdc
     * @return boolean
     * 
     * 
     * 
     */
    function GetwhuserHFSatelliteByIdc() {
        //select query
        //gets
        //warehouse user HF Satellite By Id
        $strSql = "SELECT
						*
					FROM
						(
							SELECT
								wh_user.sysusrrec_id,
								sysuser_tab.sysusr_name,
								tbl_warehouse.wh_id,
								CONCAT('Satellite Camp - ', tbl_warehouse.wh_name) AS wh_name,
								sysuser_tab.usrlogin_id,
								stakeholder.lvl,
								tbl_hf_type_rank.hf_type_rank,
                                tbl_warehouse.wh_rank          
							FROM
								sysuser_tab
							INNER JOIN wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
							INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
							INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
							INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
							WHERE
								wh_user.sysusrrec_id = " . $this->m_npkId . "
							AND stakeholder.lvl > 4
							AND tbl_hf_type_rank.stakeholder_id = " . $this->m_stk_id . "
							AND tbl_hf_type_rank.province_id = " . $this->m_prov_id . "
							AND tbl_warehouse.hf_type_id IN (1, 2)
						) A
					GROUP BY
						A.wh_id
					ORDER BY
                        IF(A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
						A.wh_rank,
						A.hf_type_rank ASC,
						A.wh_name ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
        //fetch results
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetwhuserHFTypeByIdc
     * @return boolean
     * 
     * 
     * 
     */
    function GetwhuserHFTypeByIdc() {
        //select query
        //gets
        //warehouse user HF Type By Id
        $strSql = "SELECT
						*
					FROM
						(
							SELECT
								wh_user.sysusrrec_id,
								sysuser_tab.sysusr_name,
								sysuser_tab.usrlogin_id,
								CONCAT(DATE_FORMAT(tbl_hf_type_data.last_update,'%d/%m/%Y'),' ',TIME_FORMAT(tbl_hf_type_data.last_update,'%r')) AS last_update,
								tbl_hf_type.pk_id,
								tbl_hf_type.hf_type,
								tbl_hf_type_rank.hf_type_rank
							FROM
								sysuser_tab
							INNER JOIN wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
							INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
							LEFT JOIN tbl_hf_type_data ON tbl_warehouse.hf_type_id = tbl_hf_type_data.facility_type_id 
							AND tbl_warehouse.dist_id = tbl_hf_type_data.district_id
							LEFT JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
							INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
							WHERE
								wh_user.sysusrrec_id = " . $this->m_npkId . "
							AND tbl_hf_type.hf_type IS NOT NULL
							AND tbl_warehouse.dist_id = " . $_SESSION['dist_id'] . "
							AND tbl_hf_type_rank.stakeholder_id = " . $this->m_stk_id . "
							AND tbl_hf_type_rank.province_id = " . $this->m_prov_id . "
							GROUP BY
								tbl_hf_type.pk_id
						) A
					GROUP BY
						A.pk_id
					ORDER BY
						A.hf_type_rank ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * Getwh_userinCSV
     * @return string
     * 
     * 
     * 
     */
    function Getwh_userinCSV() {
        $csv = "";
        //get warehouse user By Id
        $objwharehouse_user = $this->Getwh_userById();
        if ($objwharehouse_user != FALSE && mysql_num_rows($objwharehouse_user) > 0) {
            while ($Rowranks = mysql_fetch_object($objwharehouse_user)) {
                $csv .= $Rowranks->wh_name . ",";
            }
        }

        return $csv;
    }

    /**
     * 
     * wh_userdelete
     * @return boolean
     * 
     * 
     * 
     */
    function wh_userdelete() {

        $level = $this->level;
        if ($level == 'all') {
            $strSql = "DELETE FROM wh_user WHERE sysusrrec_id=" . $this->m_npkId;
        } else {
            $strSql = "DELETE FROM wh_user WHERE wh_user_id IN (SELECT
			 A.wh_user_id
		FROM
			(SELECT
	DISTINCT wu.wh_user_id
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN wh_user wu ON wu.wh_id = tbl_warehouse.wh_id
WHERE
	wu.sysusrrec_id = " . $this->m_npkId . " AND stakeholder.lvl = " . $this->level . ")A)";
        }
        //delete query
        //query result
        
            //echo '>c>'.$strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error Deletewh_user 3");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetProvinceIdByIdc
     * @return type
     * 
     * 
     * 
     */
    function GetProvinceIdByIdc() {
        //select query
        //gets
        //province id
        if ($this->m_npkId == "8904") {
            $province_id = array("prov_id" => 2);
            return $province_id;
        }
        $strSql = "SELECT
						tbl_warehouse.prov_id
					FROM
						wh_user
					INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
					WHERE
						wh_user.sysusrrec_id = " . $this->m_npkId . "
					LIMIT 1";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
        if (mysql_num_rows($rsSql) > 0) {
            //fetch results
            return mysql_fetch_array($rsSql);
        } else {
            if (!empty($this->m_wh_id)) {
                $strSql = "SELECT
								tbl_warehouse.prov_id
							FROM
								tbl_warehouse
							WHERE
								tbl_warehouse.wh_id = " . $this->m_wh_id;
                //query result
                $rsSql = mysql_query($strSql) or die("Error Getwh_userById");
                //fetch results
                return mysql_fetch_array($rsSql);
            }
        }
    }

}

?>
