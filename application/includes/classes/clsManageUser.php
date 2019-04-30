<?php

/**
 * clsManageUser
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsUser {

    
    var $m_npkId;
    //npkid
    var $m_stkid;
    //distrcit id
    var $m_dist_id;
    //province id
    var $m_prov_id;
    //location id
    var $m_locid;
    //user login id
    var $m_usrlogin_id;
    //sysuser pwd
    var $m_sysusr_pwd;
    //full name
    var $full_name;
    //email id
    var $email_id;
    //phone number
    var $phone_no;
    //fax number
    var $fax_no;
    //address
    var $address;
    //sysuser photo
    var $sysusr_photo;
    //sysuer department
    var $sysusr_dept;
    //sysuer designation
    var $sysusr_deg;
    //sysuer type
    var $sysusr_type;
    //warehouse rc id
    var $m_whrec_id;
    //sysuser photo
    var $m_sysusr_photo;
    //User Level
    var $m_user_level;

    /**
     * AddUser() 
     * @return int
     */
    function AddUser() {
       
        try {
            if ($this->m_whrec_id == '') {
                $this->m_whrec_id = 'NULL';
            }
            if ($this->m_stkid == '') {
                $this->m_stkid = 0;
            }
            if ($this->m_dist_id == '') {
                $this->m_dist_id = 'NULL';
            }
            if ($this->m_prov_id == '') {
                $this->m_prov_id = '0';
            }
            if ($this->m_usrlogin_id == '') {
                $this->m_usrlogin_id = 'NULL';
            }
            if ($this->m_full_name == '') {
                $this->m_full_name = 'NULL';
            }
            if ($this->m_email_id == '') {
                $this->m_email_id = '';
            }
            if ($this->m_phone_no == '') {
                $this->m_phone_no = '';
            }
            if ($this->m_sysusr_photo == '') {
                $this->m_sysusr_photo = '';
            }
            if ($this->m_sysusr_dept == '') {
                $this->m_sysusr_dept = 'NULL';
            }
            if ($this->m_sysusr_deg == '') {
                $this->m_sysusr_deg = 'NULL';
            }
            if ($this->m_sysusr_type == '') {
                $this->m_sysusr_type = 'NULL';
            }

            $this->m_sysusr_pwd = md5($this->m_sysusr_pwd);
            $hash = md5(strtolower($this->m_usrlogin_id) . '' . $this->m_sysusr_pwd);

            $strSql = "INSERT INTO sysuser_tab(user_level,acopen_dt,UserID,stkid,province,usrlogin_id,
		sysusr_name,sysusr_pwd,sysusr_status,sysusr_email,sysusr_ph,sysusr_cell,sysusr_addr,sysusr_dept,sysusr_deg,sysusr_type,sysusr_photo,auth)
		VALUES('".(!empty($this->m_user_level)?$this->m_user_level:'')."',NOW()," . ($this->GetMaxUserId() + 1) . "," . $this->m_stkid . "," . $this->m_prov_id . ",'" . $this->m_usrlogin_id . "','" . $this->m_full_name . "','" . $this->m_sysusr_pwd . "','Active','" . $this->m_email_id . "','" . $this->m_phone_no . "','" . $this->m_fax_no . "','" . $this->m_address . "','" . $this->m_sysusr_dept . "','" . $this->m_sysusr_deg . "','" . $this->m_sysusr_type . "','" . $this->m_sysusr_photo . "','" . $hash . "')";
           //echo $strSql;
           //exit;
            //query result
            $rsSql = mysql_query($strSql) or die("Error AddUser");
            if (mysql_insert_id() > 0) {
                return mysql_insert_id();
            } else {
                return 0;
            }
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * EditUser
     * @return boolean
     */
    function EditUser() {
        //adding additional options

        $strSql = "UPDATE sysuser_tab SET UserID=" . $this->m_npkId;

        $stkid = ",stkid='" . $this->m_stkid . "'";
        if ($this->m_stkid != '') {
            $strSql .=$stkid;
        }

        $prov_id = ",province=" . $this->m_prov_id;
        if ($this->m_prov_id != '') {
            $strSql .=$prov_id;
        }

        $usrlogin_id = ",usrlogin_id='" . $this->m_usrlogin_id . "'";
        if ($this->m_usrlogin_id != '') {
            $strSql .=$usrlogin_id;
        }

        $sysusr_pwd = ",sysusr_pwd='" . $this->m_sysusr_pwd . "'";
        if ($this->m_sysusr_pwd != '') {
            $strSql .=$sysusr_pwd;
        }

        $full_name = ",sysusr_name='" . $this->m_full_name . "'";
        if ($this->m_full_name != '') {
            $strSql .=$full_name;
        }

        $phone_no = ",sysusr_ph='" . $this->m_phone_no . "'";
        if ($this->m_phone_no != '') {
            $strSql .=$phone_no;
        }

        $email_id = ",sysusr_email='" . $this->m_email_id . "'";
        if ($this->m_email_id != '') {
            $strSql .=$email_id;
        }

        $fax_no = ",sysusr_cell='" . $this->m_fax_no . "'";
        {
            $strSql .=$fax_no;
        }

        $address = ",sysusr_addr='" . $this->m_address . "'";
        {
            $strSql .=$address;
        }

        $sysusr_dept = ",sysusr_dept='" . $this->m_sysusr_dept . "'";
        if ($this->m_sysusr_dept != '') {
            $strSql .=$sysusr_dept;
        }

        $sysusr_deg = ",sysusr_deg='" . $this->m_sysusr_deg . "'";
        if ($this->m_sysusr_deg != '') {
            $strSql .=$sysusr_deg;
        }

        $sysusr_type = ",sysusr_type='" . $this->m_sysusr_type . "'";
        if ($this->m_sysusr_type != '') {
            $strSql .=$sysusr_type;
        }

        $m_sysusr_photo = ",sysusr_photo='" . $this->m_sysusr_photo . "'";
        if ($this->m_sysusr_photo != '') {
            $strSql .=$m_sysusr_photo;
        }
        
        $sysuser_level = ",user_level = '" . $this->m_user_level . "'";
        if ($this->m_user_level != '') {
            $strSql .= $sysuser_level;
        }

        if ($this->m_usrlogin_id != '' || $this->m_sysusr_pwd != '') {
            $hash = md5(strtolower($this->m_usrlogin_id) . '' . $this->m_sysusr_pwd);
            $strSql .= ",auth='" . $hash . "'";
        }

        $strSql .=" WHERE UserID=" . $this->m_npkId;
        //echo $strSql;exit;
        //query result
        $rsSql = mysql_query($strSql) or die("Error 'EditUser' Query: $strSql");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteUser
     * @return boolean
     */
    function DeleteUser() {
        $strSql = "DELETE FROM  sysuser_tab WHERE UserID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteUser");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllUser
     * GetAllUser
     */
    function GetAllUser() {
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.stkid, stakeholder.stkname, GROUP_CONCAT(DISTINCT District.LocName) AS Districts, Province.LocName AS Provinces, wh_user.sysusrrec_id, wh_user.wh_id, GROUP_CONCAT(DISTINCT tbl_warehouse.wh_name) AS wh_name, tbl_warehouse.wh_id, sysuser_tab.usrlogin_id
            FROM sysuser_tab
            LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
            LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
            LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
            INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
            INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllSubAdminUser
     * @return boolean
     * 
     */
    function GetAllSubAdminUser() {
        $strSql = "SELECT * from sysuser_tab WHERE sysusr_type = 'UT-006'";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllUserprov2
     * @return boolean
     */
    function GetAllUserprov2() {
        $strSql = "SELECT sysuser_tab.UserID,sysuser_tab.sysusr_type, sysuser_tab.stkid, stakeholder.stkname, GROUP_CONCAT(DISTINCT District.LocName) AS Districts, Province.LocName AS Provinces, wh_user.sysusrrec_id, wh_user.wh_id, GROUP_CONCAT(DISTINCT tbl_warehouse.wh_name) AS wh_name, tbl_warehouse.wh_id, sysuser_tab.usrlogin_id
            FROM sysuser_tab
            LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
            LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
            LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
            INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
            INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id where Province.PkLocID='" . $this->provid . "' AND sysuser_tab.sysusr_type='UT-002'  GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllUserprov
     * @return boolean
     */
    function GetAllUserprov() {
        //select query
        //gets
        //user id
        //stakeholder id
        //stakeholder name
        //districts
        //provinces
        //warehouse rec id
        //warehouse name
        //warehouse id
        //user login id
        $strSql = "SELECT
						sysuser_tab.UserID,
						sysuser_tab.stkid,
						stakeholder.stkname,
						District.LocName AS Districts,
						Province.LocName AS Provinces,
						tbl_warehouse.wh_id AS whrec_id,
						tbl_warehouse.wh_name,
						tbl_warehouse.wh_id,
						sysuser_tab.usrlogin_id
					FROM
						sysuser_tab
					LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
					INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
					INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetUserByprovtitle
     * @return boolean
     */
    function GetUserByprovtitle() {
        //select query
        //gets
        //User ID,
        //stakeholder id,
        //stakeholder name,
        //warehouse rec id,
        //warehouse name,
        //district id,
        //district,
        //province id,
        //province,
        //warehouse id,
        //usrlogin id
        $strSql = "SELECT
                    sysuser_tab.UserID,
                    sysuser_tab.stkid,
                    stakeholder.stkname,
                    sysuser_tab.whrec_id,
                    tbl_warehouse.wh_name,
                    district.PkLocID AS dist_id,
                    district.LocName AS district,
                    province.PkLocID AS prov_id,
                    province.LocName AS province,
                    tbl_warehouse.wh_id,
                    sysuser_tab.usrlogin_id
                    FROM
                    sysuser_tab
                    Left Join stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                    Left Join tbl_warehouse ON sysuser_tab.whrec_id = tbl_warehouse.wh_id
                    Left Join tbl_locations AS district ON sysuser_tab.locid = district.PkLocID
                    Left Join tbl_locations AS province ON district.ParentID = province.PkLocID
                    ORDER BY province.PkLocID";
        //query result
        $rsSql = mysql_query($strSql) or die("Error UserByprovtitle");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetUserByid
     * @return boolean
     */
    function GetUserByid() {
        //select query
        //gets
        //User ID,
        //stakeholder id,
        //stakeholder name,
        //whrec_id,
        //warehouse name,
        //district id,
        //district,
        //province id,
        //province,
        //warehouse id,
        //usrlogin id
        $strSql = "SELECT
						sysuser_tab.UserID,
						sysuser_tab.stkid,
						stakeholder.stkname,
						tbl_warehouse.wh_id AS whrec_id,
						tbl_warehouse.wh_name,
						district.PkLocID AS dist_id,
						district.LocName AS district,
						province.PkLocID AS prov_id,
						province.LocName AS province,
						tbl_warehouse.wh_id,
						sysuser_tab.usrlogin_id
					FROM
						sysuser_tab
					LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
					INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
					INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id
                    WHERE
						sysuser_tab.usrlogin_id='" . $this->m_usrlogin_id . "'";
        //query result
        ///echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetUserByid");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

        /**
     * GetUserByid
     * @return boolean
     */
    function GetUserByUsernameOnly() {
        //select query
        //gets
        //User ID,
        //stakeholder id,
        //stakeholder name,
        //whrec_id,
        //warehouse name,
        //district id,
        //district,
        //province id,
        //province,
        //warehouse id,
        //usrlogin id
        $strSql = "SELECT
                            sysuser_tab.*
                    FROM
                            sysuser_tab
                    WHERE
                            sysuser_tab.usrlogin_id='" . $this->m_usrlogin_id . "'";
        //query result
        ///echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetUserByid");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetUserByUserID
     * @return boolean
     */
    function GetUserByUserID() {
        //select query
        //gets
        //User ID,
        //sysusr pwd,
        //stakeholder id,
        //sysusr name,
        //sysusr email,
        //sysusr phone,
        //sysusr cell,
        //sysusr address,
        //sysusr department,
        //sysusr_designation,
        //sysusr type,
        //sysusrrec id,
        //warehiuse ids,
        //stakeholder name,
        //warehouse name,
        //district id,
        //district,
        //province id,
        //province,
        //warehouse id,
        //usrlogin id
        $strSql = "SELECT
                        sysuser_tab.UserID,
                        sysuser_tab.sysusr_type,
                        sysuser_tab.sysusr_pwd,
                        sysuser_tab.stkid,
                        sysuser_tab.sysusr_name,
                        sysuser_tab.sysusr_email,
                        sysuser_tab.sysusr_ph,
                        sysuser_tab.sysusr_cell,
                        sysuser_tab.sysusr_addr,
                        sysuser_tab.sysusr_dept,
                        sysuser_tab.sysusr_deg,
                        sysuser_tab.sysusr_type,
                        wh_user.sysusrrec_id,
                        GROUP_CONCAT(DISTINCT wh_user.wh_id) AS wh_ids,
                        stakeholder.stkname,
                        GROUP_CONCAT(DISTINCT tbl_warehouse.wh_name) AS wh_name,
                        GROUP_CONCAT(DISTINCT district.PkLocID) AS dist_id,
                        GROUP_CONCAT(DISTINCT district.LocName) AS district,
                        province.PkLocID AS prov_id,
                        province.LocName AS province,
                        tbl_warehouse.wh_id,
                        sysuser_tab.usrlogin_id,
                        sysuser_tab.user_level,
                        sysuser_tab.sysusr_status
                    FROM
                        sysuser_tab
                        Left Join stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                        LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id 
                        LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                        Left Join tbl_locations AS district ON district.PkLocID=tbl_warehouse.locid
                        Left Join tbl_locations AS province ON province.PkLocID=sysuser_tab.province
                    WHERE sysuser_tab.UserID='" . $this->m_npkId . "'";
//echo  $strSql;exit;        
//query result
        $rsSql = mysql_query($strSql) or die("Error GetUserByUserID");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAdminByAdminID
     * @return boolean
     */
    function GetAdminByAdminID() {
        //select query 
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.sysusr_type, sysuser_tab.sysusr_name,sysuser_tab.sysusr_name,sysuser_tab.sysusr_pwd,sysuser_tab.sysusr_ph,sysuser_tab.sysusr_cell, sysuser_tab.stkid,stakeholder.stkname,sysuser_tab.province, sysuser_tab.usrlogin_id,sysuser_tab.sysusr_email,sysuser_tab.sysusr_addr
                FROM sysuser_tab
                LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                where sysuser_tab.UserID='" . $this->m_npkId . "' AND (sysuser_tab.sysusr_type='UT-001' OR sysuser_tab.sysusr_type='5' ) GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die("Error GetAdminByAdminID");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetMaxUserId
     * @return int
     */
    function GetMaxUserId() {
        //select query
        $strSql = "SELECT IFNULL(max(UserID),0) as MaxID from sysuser_tab";
        //query result
        $rsSql = mysql_query($strSql) or die("GetMaxUserId");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $MaxID = $row['MaxID'];
            }
            return $MaxID;
        } else {
            return 0;
        }
    }

    /**
     * GetAllUser1
     * @return boolean
     */
    function GetAllUser1() {
        //select query
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.sysusr_type, sysuser_tab.stkid, stakeholder.stkname, GROUP_CONCAT(DISTINCT District.LocName) AS Districts, Province.LocName AS Provinces, wh_user.sysusrrec_id, wh_user.wh_id, GROUP_CONCAT(DISTINCT tbl_warehouse.wh_name) AS wh_name, tbl_warehouse.wh_id, sysuser_tab.usrlogin_id
                FROM sysuser_tab
                LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
                INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id where sysuser_tab.sysusr_type='UT-002' AND Province.PkLocID='" . $this->m_provid . "' AND sysuser_tab.stkid='" . $this->m_stkid . "' GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllUser2
     * @return boolean
     */
    function GetAllUser2() {

        $stk = implode(",", $user_stk);
        $prov = implode(",", $user_prov);
//select query
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.sysusr_type, sysuser_tab.stkid, stakeholder.stkname, GROUP_CONCAT(DISTINCT District.LocName) AS Districts, Province.LocName AS Provinces, wh_user.sysusrrec_id, wh_user.wh_id, GROUP_CONCAT(DISTINCT tbl_warehouse.wh_name) AS wh_name, tbl_warehouse.wh_id, sysuser_tab.usrlogin_id
                FROM sysuser_tab
                LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                INNER JOIN tbl_locations AS District ON District.PkLocID = tbl_warehouse.locid
                INNER JOIN tbl_locations AS Province ON Province.PkLocID = tbl_warehouse.prov_id where sysuser_tab.sysusr_type='UT-002' AND Province.PkLocID IN('" . $prov . "') AND sysuser_tab.stkid IN ('" . $stk . "') GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllUserAdministrator
     * @return boolean
     */
    function GetAllUserAdministrator() {
        //select query
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.sysusr_type, sysuser_tab.sysusr_name,sysuser_tab.sysusr_name, sysuser_tab.stkid,stakeholder.stkname,sysuser_tab.province, Province.LocName AS Provinces, sysuser_tab.usrlogin_id,sysuser_tab.sysusr_email
                FROM sysuser_tab
                LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                LEFT JOIN tbl_locations AS Province ON Province.PkLocID = sysuser_tab.province 
                where sysuser_tab.sysusr_type='UT-001' GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllprovAdministrator
     * @return boolean
     */
    function GetAllprovAdministrator() {
        $strSql = "SELECT sysuser_tab.UserID, sysuser_tab.sysusr_type, sysuser_tab.sysusr_name,sysuser_tab.sysusr_name, sysuser_tab.stkid,stakeholder.stkname,sysuser_tab.province, Province.LocName AS Provinces, sysuser_tab.usrlogin_id,sysuser_tab.sysusr_email
            FROM sysuser_tab
            LEFT JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
            LEFT JOIN tbl_locations AS Province ON Province.PkLocID = sysuser_tab.province 
            where (sysuser_tab.sysusr_type='UT-001' AND Province.PkLocID='" . $this->provid . "' GROUP BY sysuser_tab.UserID";
//query result
        $rsSql = mysql_query($strSql) or die(mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetSuperAdminbyID
     * @return boolean
     */
    function GetSuperAdminbyID() {
        $m_npkId = $this->m_npkId;
        $strSql = "Select usrlogin_id,sysusr_name,sysusr_email,sysusr_ph,sysusr_cell,sysusr_addr from sysuser_tab where UserID='" . $m_npkId . "'";
        //query result
        $rsSql = mysql_query($strSql) or die(mysql_error());

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
