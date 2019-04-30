<?php

/**
 * clsManageLocations
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsManagelocations {

    var $PkLocID;

    /**
     * Get All Locations Type
     * 
     * @return boolean
     */
    function GetAllLocationsType() {

        $strSql = "SELECT LoctypeID,LoctypeName,TypeLvl FROM tbl_locationtype WHERE TypeLvl=" . $this->TypeLvl;
        $rsSql = mysql_query($strSql) or die("Error GetAllLocationstype");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>