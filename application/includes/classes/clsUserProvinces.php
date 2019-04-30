<?php

/**
 * clsUserProvinces
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsUserProvinces {

//npkId
    var $m_npkId;
    //nuserId
    var $m_nuserId;
    //nprovId
    var $m_nprovId;

    /**
     * GetProvByUserId
     * @return boolean
     */
    function GetProvByUserId() {
        $array = array();
        $strSql = "SELECT * FROM user_prov WHERE user_id = " . $this->m_npkId;
        //Query result
        $rsSql = mysql_query($strSql) or die("Error GetProvByUserId");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $array[] = $row['prov_id'];
            }
            return $array;
        } else {
            return FALSE;
        }
    }

    /**
     * GetProvInfoByUserId
     * @return boolean
     */
    function GetProvInfoByUserId() {
        $array = array();
        $strSql = "SELECT * FROM user_prov WHERE user_id = " . $this->m_npkId;
        //Query result
        $rsSql = mysql_query($strSql) or die("Error GetProvByUserId");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $array[] = array(
                    'id' => $row['prov_id'],
                    'name' => $row['prov_id']
                );
            }
            return $array;
        } else {
            return FALSE;
        }
    }

    /**
     * insert
     * @return boolean
     */
    function insert() {

        $strSql = "INSERT INTO  user_prov (user_id, prov_id) VALUES (" . $this->m_nuserId . ", " . $this->m_nprovId . ")";
        //Query result
        $rsSql = mysql_query($strSql) or die("Error insert");
        if ($rsSql) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * delete
     * @return boolean
     */
    function delete() {
        $strSql = "DELETE FROM user_prov WHERE user_id = " . $this->m_nuserId;
        //Query result
        $rsSql = mysql_query($strSql) or die("Error delete");
        if ($rsSql) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
