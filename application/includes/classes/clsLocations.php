<?php
/**
 * clsLocations
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clslocations {

    var $PkLocID;
    var $LocName;
    var $LocLvl;
    var $ParentID;
    var $LocType;
/**
 * Addlocations
 * @return int
 */
    function Addlocations() {
        if ($this->LocName == '') {
            $this->LocName = 'NULL';
        }
        if ($this->ParentID == '') {
            $this->ParentID = 0;
        }
        if ($this->LocType == '') {
            $this->LocType = 0;
        }
        if ($this->LocLvl == '') {
            $this->LocLvl = 1;
        }
        //insert query
        //inserts
        //location name
        //location level
        //location type
        //parent id
        $strSql = "INSERT INTO  tbl_locations (LocName,LocLvl,ParentID,LocType) VALUES('" . $this->LocName . "'," . $this->LocLvl . "," . $this->ParentID . "," . $this->LocType . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error Addlocations");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }
/**
 * Editlocations
 * @return boolean
 */
    function Editlocations() {

        $strSql = "UPDATE tbl_locations SET PkLocID=" . $this->PkLocID;

        $LocName = ",LocName='" . $this->LocName . "'";
        if ($this->LocName != '') {
            $strSql .=$LocName;
        }

        $LocLvl = ",LocLvl=" . $this->LocLvl;
        if ($this->LocLvl != '') {
            $strSql .=$LocLvl;
        }

        $ParentID = ",ParentID=" . $this->ParentID;
        if ($this->ParentID != '') {
            $strSql .=$ParentID;
        }

        $LocType = ",LocType=" . $this->LocType;
        $strSql .=$LocType;

        $strSql .=" WHERE PkLocID=" . $this->PkLocID;
//query result
        $rsSql = mysql_query($strSql) or die("Error Editlocations");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * DeleteLocation
 * @return boolean
 */
    function DeleteLocation() {
        $strSql = "DELETE FROM  tbl_locations WHERE PkLocID=" . $this->PkLocID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error Delete location");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllLocations
 * @return boolean
 */
    function GetAllLocations() {
        $qry = "SELECT
					user_stk.stk_id,
					user_prov.prov_id
				FROM
					user_stk
				JOIN user_prov ON user_stk.user_id = user_prov.user_id
				WHERE
					user_stk.user_id = " . $_SESSION['user_id'];
        //query result
        //echo $qry;exit;
        $qryRes = mysql_query($qry);
        $arr = array();
        while ($row = mysql_fetch_array($qryRes)) {
            if (!in_array($row['prov_id'], $arr)) {
                $arr[] = $row['prov_id'];
            }
        }
        $and = (!empty($arr)) ? " AND tbl_locations.PkLocID IN (" . implode(',', $arr) . ") " : '';
        $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations WHERE LocLvl=" . $this->LocLvl . " $and ";
        
        if($this->show_national !='yes')
        $strSql .= " AND tbl_locations.ParentID IS NOT NULL";

        if($this->office >= 3 && !empty($_SESSION['user_province1']) && !empty($_SESSION['user_level']) && $_SESSION['user_level']>=3)
        {
            $strSql .= " AND tbl_locations.PkLocID = ".$_SESSION['user_province1']." ";
        }
//query result
//        echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllLocations of Level 2
 * @return boolean
 */
    function GetAllLocationsL2() {
        $strSql = "SELECT
						PkLocID,
						LocName,
						LocLvl,
						ParentID,
						LocType
					FROM
						tbl_locations
					WHERE
						LocLvl = 2";
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetLocationsById
 * @param type $provIds
 * @return boolean
 */
    function GetLocationsById($provIds) {
        $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations WHERE LocLvl=" . $this->LocLvl . " AND PkLocID IN (" . $provIds . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllLocations1
 * @return boolean
 */
    function GetAllLocations1() {
        $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllLocationsfromParent
 * @return boolean
 */
    function GetAllLocationsfromParent() {
        if ($this->ParentID != '10') {
            $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations WHERE LocLvl> 2 and ParentID=" . $this->ParentID . " Order by LocName";
        } else {
            $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations WHERE LocLvl> 2 Order by LocName";
        }
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllLocationsWithType
 * @return boolean
 */
    function GetAllLocationsWithType() {
        $strSql = "SELECT PkLocID,LocName,LocLvl,ParentID,LocType FROM tbl_locations WHERE LocLvl=" . $this->LocLvl . " and LocType=" . $this->LocType;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllLocations");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetLocationById
 * @return boolean
 */
    function GetLocationById() {
        //select query
        //gets
        //pk location id
        //location name
        //location level
        //location type
        //parent id
        //province
        $strSql = "SELECT
					tbl_locations.PkLocID,
					tbl_locations.LocName,
					tbl_locations.LocLvl,
					tbl_locations.ParentID,
					tbl_locations.LocType,
					(CASE WHEN Province.LocType=2 THEN tbl_locations.ParentID else Province.ParentID  END) AS Province
				FROM
					tbl_locations
				INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
				WHERE
					tbl_locations.LocLvl IN (3, 4)
			AND tbl_locations.PkLocID = " . $this->PkLocID;
        //query result
       //echo $strSql;
        $rsSql = mysql_query($strSql) or die("Error GetLocationById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * get_location_name
 * @return type
 */
    function get_location_name() {
        $LocName = '';
        $strSql = "SELECT LocName FROM tbl_locations WHERE PkLocID=" . $this->PkLocID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error get_location_name");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $RowLoc = mysql_fetch_object($rsSql);
            $LocName = $RowLoc->LocName;
        }
        return $LocName;
    }
/**
 * GetLocationsByLevel
 * @param type $parent
 * @param type $lvl
 * @return boolean
 */
    function GetLocationsByLevel($parent, $lvl) {
        //select query
        //gets
        //pk location id
        //location name
        //location type
        //parent id
        $strSql = 'SELECT
				tbl_locations.PkLocID,
				tbl_locations.LocName,
				tbl_locations.LocLvl,
				tbl_locations.ParentID,
				tbl_locations.LocType
				FROM
				tbl_locations
				WHERE tbl_locations.LocLvl = ' . $lvl . ' AND tbl_locations.ParentID = ' . $parent . ' ORDER BY tbl_locations.LocName';
//query result
        $rsSql = mysql_query($strSql) or die("Error GetLocationsByLevel");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllProvinces
 * @return boolean
 */
    function GetAllProvinces() {
        $strSql = 'SELECT
					tbl_locations.PkLocID,
					tbl_locations.LocName
				FROM
					tbl_locations
				WHERE
					tbl_locations.LocLvl = 2
				AND tbl_locations.ParentID IS NOT NULL';
//query result
        $rsSql = mysql_query($strSql) or die("Error GetAllProvinces");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>