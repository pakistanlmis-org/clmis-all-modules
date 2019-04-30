<?php

/**
 * clsUserStakeholders
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsUserStackholders {

//npkId
    var $m_npkId;
    //nuserId
    var $m_nuserId;
    //nstkId
    var $m_nstkId;

    /**
     * GetStkByUserId
     * @return boolean
     */
    function GetStkByUserId() {
         $strSql = "SELECT * FROM user_stk WHERE user_id = " . $this->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error GetStkByUserId");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_array($rsSql)) {
                $array[] = $row['stk_id'];
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
        $strSql = "INSERT INTO  user_stk (user_id, stk_id) VALUES (" . $this->m_nuserId . ", " . $this->m_nstkId . ")";
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
        $strSql = "DELETE FROM user_stk WHERE user_id = " . $this->m_nuserId;
        $rsSql = mysql_query($strSql) or die("Error delete");
        if ($rsSql) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
